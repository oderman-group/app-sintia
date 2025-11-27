/**
 * Sistema Moderno de Gestión de Módulos por Institución
 * Gestión en tiempo real con AJAX
 */

// Variables globales
let institucionActual;
let modulosOriginales = [];

// Inicializar cuando el DOM esté listo
$(document).ready(function() {
    // Inicializar variable con la constante global
    if (typeof INSTITUCION_ACTUAL !== 'undefined') {
        institucionActual = INSTITUCION_ACTUAL;
    } else {
        console.error('INSTITUCION_ACTUAL no está definida');
    }
    
    inicializarComponentes();
    cargarEventos();
    guardarEstadoOriginal();
});

/**
 * Inicializar componentes
 */
function inicializarComponentes() {
    // Esperar a que Select2 esté disponible
    if (typeof $.fn.select2 !== 'undefined') {
        // Inicializar Select2 para el selector de instituciones
        $('#selectorInstitucion').select2({
            placeholder: 'Seleccione una institución',
            width: '100%',
            dropdownAutoWidth: true
        });
    } else {
        console.warn('Select2 no está disponible');
    }
}

/**
 * Guardar estado original de los módulos
 */
function guardarEstadoOriginal() {
    modulosOriginales = [];
    $('.toggle-modulo:checked').each(function() {
        modulosOriginales.push(parseInt($(this).data('modulo-id')));
    });
}

/**
 * Cargar eventos
 */
function cargarEventos() {
    // Cambio de institución
    $('#selectorInstitucion').on('change', function() {
        const institucionId = $(this).val();
        cambiarInstitucion(institucionId);
    });

    // Toggle de módulos
    $(document).on('change', '.toggle-modulo', function() {
        const moduloId = $(this).data('modulo-id');
        const isChecked = $(this).is(':checked');
        toggleModulo(moduloId, isChecked);
    });

    // Buscador
    $('#buscarModulo').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        filtrarModulos(searchTerm);
    });

    // Filtros rápidos
    $('.filtro-btn').on('click', function() {
        $('.filtro-btn').removeClass('active');
        $(this).addClass('active');
        
        const filtro = $(this).data('filtro');
        aplicarFiltro(filtro);
    });
}

/**
 * Cambiar de institución
 */
function cambiarInstitucion(institucionId) {
    mostrarLoading(true);
    
    $.ajax({
        url: 'ajax-instituciones-obtener-datos.php',
        type: 'POST',
        data: { institucion_id: institucionId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                actualizarInterfaz(response.data);
                institucionActual = institucionId;
                
                // Cambiar URL sin recargar
                const newUrl = `dev-instituciones-editar-v2.php?id=${btoa(institucionId)}`;
                window.history.pushState({path: newUrl}, '', newUrl);
                
                mostrarNotificacion('Institución cargada correctamente', 'success');
            } else {
                mostrarNotificacion(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cambiar institución:', error);
            mostrarNotificacion('Error al cargar la institución', 'error');
        },
        complete: function() {
            mostrarLoading(false);
        }
    });
}

/**
 * Actualizar interfaz con datos de la institución
 */
function actualizarInterfaz(data) {
    // Actualizar información
    $('#infoId').text(data.ins_id || '-');
    $('#infoNit').text(data.ins_nit || '-');
    $('#infoBd').text(data.ins_bd || '-');
    
    // Actualizar estado
    const estadoBadge = $('#infoEstado');
    if (data.ins_estado == 1) {
        estadoBadge.text('Activa').css('background', '#38ef7d');
    } else {
        estadoBadge.text('Inactiva').css('background', '#f45c43');
    }
    
    // Actualizar estadísticas
    $('#statModulosActivos').text(data.total_modulos || 0);
    $('#statModulosDisponibles').text(data.total_modulos_disponibles || 0);
    
    // Actualizar toggles de módulos
    $('.toggle-modulo').each(function() {
        const moduloId = parseInt($(this).data('modulo-id'));
        const estaActivo = data.modulos_asignados.includes(moduloId);
        
        $(this).prop('checked', estaActivo);
        
        // Actualizar clases de la tarjeta
        const card = $(this).closest('.modulo-card');
        if (estaActivo) {
            card.addClass('activo').attr('data-estado', 'activo');
        } else {
            card.removeClass('activo').attr('data-estado', 'inactivo');
        }
    });
    
    // Actualizar contador
    actualizarContadores();
    guardarEstadoOriginal();
}

/**
 * Toggle de módulo (activar/desactivar)
 */
function toggleModulo(moduloId, activar) {
    const accion = activar ? 'agregar' : 'remover';
    const card = $(`.modulo-card[data-modulo-id="${moduloId}"]`);
    
    // Feedback visual inmediato
    if (activar) {
        card.addClass('activo').attr('data-estado', 'activo');
    } else {
        card.removeClass('activo').attr('data-estado', 'inactivo');
    }
    
    // Enviar a servidor
    $.ajax({
        url: 'ajax-instituciones-modulos-guardar.php',
        type: 'POST',
        data: {
            institucion_id: institucionActual,
            modulo_id: moduloId,
            accion: accion
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarNotificacion(response.message, 'success');
                actualizarContadores();
                guardarEstadoOriginal();
            } else {
                // Revertir cambio visual
                if (activar) {
                    card.removeClass('activo').attr('data-estado', 'inactivo');
                    $(`.toggle-modulo[data-modulo-id="${moduloId}"]`).prop('checked', false);
                } else {
                    card.addClass('activo').attr('data-estado', 'activo');
                    $(`.toggle-modulo[data-modulo-id="${moduloId}"]`).prop('checked', true);
                }
                mostrarNotificacion(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al guardar módulo:', error);
            
            // Revertir cambio visual
            if (activar) {
                card.removeClass('activo').attr('data-estado', 'inactivo');
                $(`.toggle-modulo[data-modulo-id="${moduloId}"]`).prop('checked', false);
            } else {
                card.addClass('activo').attr('data-estado', 'activo');
                $(`.toggle-modulo[data-modulo-id="${moduloId}"]`).prop('checked', true);
            }
            
            mostrarNotificacion('Error al guardar cambios', 'error');
        }
    });
}

/**
 * Activar todos los módulos
 */
function activarTodosModulos() {
    // Verificar que institucionActual esté definida
    if (typeof institucionActual === 'undefined' || !institucionActual) {
        mostrarNotificacion('Error: No hay institución seleccionada', 'error');
        return;
    }
    
    if (!confirm('¿Está seguro de activar TODOS los módulos para esta institución?')) {
        return;
    }
    
    mostrarLoading(true);
    
    const modulosAActivar = [];
    
    // Recopilar los IDs de módulos que NO están activados
    $('.toggle-modulo:not(:checked)').each(function() {
        const moduloId = $(this).data('modulo-id');
        modulosAActivar.push(moduloId);
    });
    
    // Si no hay módulos para activar
    if (modulosAActivar.length === 0) {
        mostrarLoading(false);
        mostrarNotificacion('Todos los módulos ya están activos', 'success');
        return;
    }
    
    // UNA SOLA petición con todos los módulos
    $.ajax({
        url: 'ajax-instituciones-modulos-guardar.php',
        type: 'POST',
        data: {
            institucion_id: institucionActual,
            modulos_ids: modulosAActivar, // Array de IDs
            accion: 'agregar'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Actualizar UI: marcar todos como activos
                modulosAActivar.forEach(function(moduloId) {
                    $(`.toggle-modulo[data-modulo-id="${moduloId}"]`).prop('checked', true);
                    $(`.modulo-card[data-modulo-id="${moduloId}"]`).addClass('activo').attr('data-estado', 'activo');
                });
                
                actualizarContadores();
                guardarEstadoOriginal();
                mostrarNotificacion(response.message || 'Todos los módulos han sido activados', 'success');
            } else {
                mostrarNotificacion(response.message || 'Error al activar módulos', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al activar módulos:', error);
            mostrarNotificacion('Error al activar los módulos', 'error');
        },
        complete: function() {
            mostrarLoading(false);
        }
    });
}

/**
 * Desactivar todos los módulos
 */
function desactivarTodosModulos() {
    // Verificar que institucionActual esté definida
    if (typeof institucionActual === 'undefined' || !institucionActual) {
        mostrarNotificacion('Error: No hay institución seleccionada', 'error');
        return;
    }
    
    if (!confirm('¿Está seguro de desactivar TODOS los módulos para esta institución?')) {
        return;
    }
    
    mostrarLoading(true);
    
    const modulosADesactivar = [];
    
    // Recopilar los IDs de módulos que SÍ están activados
    $('.toggle-modulo:checked').each(function() {
        const moduloId = $(this).data('modulo-id');
        modulosADesactivar.push(moduloId);
    });
    
    // Si no hay módulos para desactivar
    if (modulosADesactivar.length === 0) {
        mostrarLoading(false);
        mostrarNotificacion('Todos los módulos ya están inactivos', 'success');
        return;
    }
    
    // UNA SOLA petición con todos los módulos
    $.ajax({
        url: 'ajax-instituciones-modulos-guardar.php',
        type: 'POST',
        data: {
            institucion_id: institucionActual,
            modulos_ids: modulosADesactivar, // Array de IDs
            accion: 'remover'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Actualizar UI: desmarcar todos
                modulosADesactivar.forEach(function(moduloId) {
                    $(`.toggle-modulo[data-modulo-id="${moduloId}"]`).prop('checked', false);
                    $(`.modulo-card[data-modulo-id="${moduloId}"]`).removeClass('activo').attr('data-estado', 'inactivo');
                });
                
                actualizarContadores();
                guardarEstadoOriginal();
                mostrarNotificacion(response.message || 'Todos los módulos han sido desactivados', 'success');
            } else {
                mostrarNotificacion(response.message || 'Error al desactivar módulos', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al desactivar módulos:', error);
            mostrarNotificacion('Error al desactivar los módulos', 'error');
        },
        complete: function() {
            mostrarLoading(false);
        }
    });
}

/**
 * Filtrar módulos por búsqueda
 */
function filtrarModulos(searchTerm) {
    let visibleCount = 0;
    
    $('.modulo-card').each(function() {
        const card = $(this);
        const nombre = card.find('.modulo-info h4').text().toLowerCase();
        const moduloId = card.data('modulo-id').toString();
        const descripcion = card.find('.modulo-descripcion').text().toLowerCase();
        
        if (nombre.includes(searchTerm) || moduloId.includes(searchTerm) || descripcion.includes(searchTerm)) {
            card.show();
            visibleCount++;
        } else {
            card.hide();
        }
    });
    
    // Mostrar mensaje si no hay resultados
    if (visibleCount === 0) {
        $('#noResults').show();
        $('#modulosGrid').hide();
    } else {
        $('#noResults').hide();
        $('#modulosGrid').show();
    }
}

/**
 * Aplicar filtro rápido
 */
function aplicarFiltro(filtro) {
    let visibleCount = 0;
    
    $('.modulo-card').each(function() {
        const card = $(this);
        const estado = card.attr('data-estado');
        
        if (filtro === 'todos') {
            card.show();
            visibleCount++;
        } else if (filtro === 'activos' && estado === 'activo') {
            card.show();
            visibleCount++;
        } else if (filtro === 'inactivos' && estado === 'inactivo') {
            card.show();
            visibleCount++;
        } else {
            card.hide();
        }
    });
    
    // Mostrar mensaje si no hay resultados
    if (visibleCount === 0) {
        $('#noResults').show();
        $('#modulosGrid').hide();
    } else {
        $('#noResults').hide();
        $('#modulosGrid').show();
    }
}

/**
 * Actualizar contadores
 */
function actualizarContadores() {
    const totalActivos = $('.toggle-modulo:checked').length;
    const totalDisponibles = $('.toggle-modulo').length;
    
    $('#statModulosActivos').text(totalActivos);
    $('#statModulosDisponibles').text(totalDisponibles);
}

/**
 * Mostrar/ocultar loading overlay
 */
function mostrarLoading(mostrar) {
    const overlay = $('#loadingOverlay');
    if (mostrar) {
        overlay.css('display', 'flex').hide().fadeIn(200);
    } else {
        overlay.fadeOut(200);
    }
}

/**
 * Mostrar notificación toast
 */
function mostrarNotificacion(mensaje, tipo = 'success') {
    const toast = $('#toastNotification');
    
    // Remover clases anteriores
    toast.removeClass('toast-success toast-error');
    
    // Agregar clase según tipo
    toast.addClass(`toast-${tipo}`);
    
    // Establecer mensaje
    toast.html(`<i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${mensaje}`);
    
    // Mostrar
    toast.fadeIn(300);
    
    // Ocultar después de 3 segundos
    setTimeout(function() {
        toast.fadeOut(300);
    }, 3000);
}

/**
 * Prevenir pérdida de datos al salir
 */
window.addEventListener('beforeunload', function(e) {
    const modulosActuales = [];
    $('.toggle-modulo:checked').each(function() {
        modulosActuales.push(parseInt($(this).data('modulo-id')));
    });
    
    // Comparar con estado original
    if (JSON.stringify(modulosOriginales.sort()) !== JSON.stringify(modulosActuales.sort())) {
        // Solo si hay cambios sin guardar (aunque guardamos automáticamente)
        // Esta función está más como precaución
    }
});


