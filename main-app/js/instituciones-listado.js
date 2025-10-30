/**
 * Sistema de Gestión de Instituciones Moderno
 * Con paginación AJAX, filtros y búsqueda en tiempo real
 */

// Variables globales
let paginaActual = 1;
let porPagina = 20;
let busquedaTimeout = null;
let estadisticasGlobales = {
    total: 0,
    activas: 0,
    inactivas: 0,
    bloqueadas: 0
};

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    cargarInstituciones();
    setupEventListeners();
    cargarEstadisticas();
});

/**
 * Configurar event listeners
 */
function setupEventListeners() {
    // Búsqueda en tiempo real
    $('#busqueda').on('keyup', function() {
        clearTimeout(busquedaTimeout);
        busquedaTimeout = setTimeout(function() {
            paginaActual = 1;
            cargarInstituciones();
        }, 500);
    });
    
    // Filtros
    $('#filtroPlan, #filtroEstado, #filtroBloqueado').on('change', function() {
        paginaActual = 1;
        cargarInstituciones();
    });
}

/**
 * Cargar instituciones con paginación
 */
function cargarInstituciones() {
    mostrarLoading(true);
    
    const filtros = {
        pagina: paginaActual,
        porPagina: porPagina,
        busqueda: $('#busqueda').val(),
        plan: $('#filtroPlan').val(),
        estado: $('#filtroEstado').val(),
        bloqueado: $('#filtroBloqueado').val()
    };
    
    $.ajax({
        url: 'ajax-instituciones-listar.php',
        type: 'POST',
        data: filtros,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderizarInstituciones(response.instituciones);
                renderizarPaginacion(response.paginacion);
            } else {
                mostrarNotificacion(response.message || 'Error al cargar instituciones', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar instituciones:', error);
            mostrarNotificacion('Error de conexión al cargar instituciones', 'error');
        },
        complete: function() {
            mostrarLoading(false);
        }
    });
}

/**
 * Renderizar instituciones en la tabla
 */
function renderizarInstituciones(instituciones) {
    const tbody = $('#tablaBody');
    const emptyState = $('#emptyState');
    
    tbody.empty();
    
    if (instituciones.length === 0) {
        $('#tablaInstituciones').hide();
        $('#paginationContainer').hide();
        emptyState.show();
        return;
    }
    
    $('#tablaInstituciones').show();
    $('#paginationContainer').show();
    emptyState.hide();
    
    instituciones.forEach(function(inst) {
        const bgColor = inst.ins_bloqueada == '1' ? '#ff572238' : '';
        const checked = inst.ins_bloqueada == '1' ? 'checked' : '';
        const estadoBadge = inst.ins_estado == '1' ? 
            '<span class="badge-status badge-activo">Activo</span>' : 
            '<span class="badge-status badge-inactivo">Inactivo</span>';
        
        const plan = inst.plns_nombre || 'Sin plan';
        
        const row = `
            <tr id="Reg${inst.ins_id}" style="background-color: ${bgColor};">
                <td><strong>#${inst.ins_id}</strong></td>
                <td>
                    <label class="switch-toggle">
                        <input type="checkbox" 
                               id="${inst.ins_id}" 
                               ${checked}
                               onchange="toggleBloqueo(this, ${inst.ins_id})">
                        <span class="switch-slider"></span>
                    </label>
                </td>
                <td><strong>${inst.ins_siglas || 'N/A'}</strong></td>
                <td>${inst.ins_nombre || 'Sin nombre'}</td>
                <td>
                    <div>${inst.ins_contacto_principal || 'N/A'}</div>
                    <small style="color: #6b7280;">${inst.ins_email_contacto || ''}</small>
                </td>
                <td>${plan}</td>
                <td>${estadoBadge}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn-actions" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                            Acciones
                        </button>
                        <ul class="dropdown-menu dropdown-menu-modern">
                            <li>
                                <a href="dev-instituciones-editar-v2.php?id=${window.btoa(inst.ins_id)}">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </li>
                            <li>
                                <a href="auto-login-dev.php?user=${window.btoa(1)}&idInstitucion=${window.btoa(inst.ins_id)}&bd=${window.btoa(inst.ins_bd)}&yearDefault=${window.btoa(inst.ins_year_default)}">
                                    <i class="fas fa-sign-in-alt"></i> Autologin
                                </a>
                            </li>
                            <li>
                                <a href="dev-instituciones-configuracion.php?id=${window.btoa(inst.ins_id)}">
                                    <i class="fas fa-cog"></i> Configuración
                                </a>
                            </li>
                            <li>
                                <a href="dev-instituciones-Informacion.php?id=${window.btoa(inst.ins_id)}">
                                    <i class="fas fa-info-circle"></i> Información
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        `;
        
        tbody.append(row);
    });
}

/**
 * Renderizar paginación
 */
function renderizarPaginacion(paginacion) {
    const { paginaActual: paginaActualData, porPagina, totalRegistros, totalPaginas } = paginacion;
    
    // Información
    const inicio = ((paginaActualData - 1) * porPagina) + 1;
    const fin = Math.min(paginaActualData * porPagina, totalRegistros);
    
    $('#paginationInfo').html(
        `Mostrando <strong>${inicio}-${fin}</strong> de <strong>${totalRegistros}</strong> instituciones`
    );
    
    // Botones
    const buttonsContainer = $('#paginationButtons');
    buttonsContainer.empty();
    
    // Botón anterior
    const btnPrev = $('<button>')
        .addClass('pagination-btn')
        .html('<i class="fas fa-chevron-left"></i> Anterior')
        .prop('disabled', paginaActualData === 1)
        .on('click', cambiarPagina.bind(null, paginaActualData - 1));
    
    buttonsContainer.append(btnPrev);
    
    // Botones de páginas
    let startPage = Math.max(1, paginaActualData - 2);
    let endPage = Math.min(totalPaginas, paginaActualData + 2);
    
    // Primera página
    if (startPage > 1) {
        const btn = $('<button>')
            .addClass('pagination-btn')
            .text('1')
            .on('click', cambiarPagina.bind(null, 1));
        buttonsContainer.append(btn);
        
        if (startPage > 2) {
            buttonsContainer.append($('<span>').text('...').css({'padding': '0 10px', 'color': '#6b7280'}));
        }
    }
    
    // Páginas visibles
    for (let i = startPage; i <= endPage; i++) {
        const pagina = i;
        const btn = $('<button>')
            .addClass('pagination-btn')
            .text(pagina)
            .toggleClass('active', pagina === paginaActualData)
            .on('click', cambiarPagina.bind(null, pagina));
        buttonsContainer.append(btn);
    }
    
    // Última página
    if (endPage < totalPaginas) {
        if (endPage < totalPaginas - 1) {
            buttonsContainer.append($('<span>').text('...').css({'padding': '0 10px', 'color': '#6b7280'}));
        }
        
        const btn = $('<button>')
            .addClass('pagination-btn')
            .text(totalPaginas)
            .on('click', cambiarPagina.bind(null, totalPaginas));
        buttonsContainer.append(btn);
    }
    
    // Botón siguiente
    const btnNext = $('<button>')
        .addClass('pagination-btn')
        .html('Siguiente <i class="fas fa-chevron-right"></i>')
        .prop('disabled', paginaActualData === totalPaginas)
        .on('click', cambiarPagina.bind(null, paginaActualData + 1));
    
    buttonsContainer.append(btnNext);
}

/**
 * Cambiar página
 */
function cambiarPagina(nuevaPagina) {
    paginaActual = nuevaPagina;
    cargarInstituciones();
}

/**
 * Cargar estadísticas
 */
function cargarEstadisticas() {
    $.ajax({
        url: 'ajax-instituciones-estadisticas.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                estadisticasGlobales = response.estadisticas;
                actualizarEstadisticas();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
        }
    });
}

/**
 * Actualizar estadísticas en la UI
 */
function actualizarEstadisticas() {
    $('#statTotal').text(estadisticasGlobales.total);
    $('#statActivas').text(estadisticasGlobales.activas);
    $('#statInactivas').text(estadisticasGlobales.inactivas);
    $('#statBloqueadas').text(estadisticasGlobales.bloqueadas);
}

/**
 * Toggle bloqueo de institución
 */
function toggleBloqueo(checkbox, institucionId) {
    const bloqueado = checkbox.checked ? 1 : 0;
    
    $.ajax({
        url: 'ajax-guardar.php',
        type: 'POST',
        data: {
            idR: institucionId,
            valor: bloqueado,
            operacion: 3
        },
        success: function(data) {
            // Cambiar color de fondo
            const row = $('#Reg' + institucionId);
            if (bloqueado === 1) {
                row.css('background-color', '#ff572238');
                estadisticasGlobales.bloqueadas++;
            } else {
                row.css('background-color', 'white');
                estadisticasGlobales.bloqueadas = Math.max(0, estadisticasGlobales.bloqueadas - 1);
            }
            
            actualizarEstadisticas();
            mostrarNotificacion('Estado actualizado correctamente', 'success');
        },
        error: function(xhr, status, error) {
            console.error('Error al actualizar:', error);
            checkbox.checked = !checkbox.checked; // Revertir
            mostrarNotificacion('Error al actualizar el estado', 'error');
        }
    });
}

/**
 * Resetear filtros
 */
function resetearFiltros() {
    $('#busqueda').val('');
    $('#filtroPlan').val('todos');
    $('#filtroEstado').val('todos');
    $('#filtroBloqueado').val('todos');
    paginaActual = 1;
    cargarInstituciones();
}

/**
 * Mostrar loading
 */
function mostrarLoading(mostrar) {
    const overlay = $('#loadingOverlay');
    if (mostrar) {
        overlay.addClass('active');
    } else {
        overlay.removeClass('active');
    }
}

/**
 * Mostrar notificación
 */
function mostrarNotificacion(mensaje, tipo = 'success') {
    const toast = $('#toastNotification');
    
    toast.removeClass('toast-success toast-error');
    toast.addClass(`toast-${tipo}`);
    
    const icono = tipo === 'success' ? 'check-circle' : 'exclamation-circle';
    toast.html(`<i class="fas fa-${icono}"></i> ${mensaje}`);
    
    toast.fadeIn(300);
    
    setTimeout(function() {
        toast.fadeOut(300);
    }, 3000);
}

