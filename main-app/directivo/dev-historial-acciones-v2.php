<?php
include("session.php");

$idPaginaInterna = 'DV0004';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

$Plataforma = new Plataforma;

// Obtener instituciones para el filtro
try {
    $consultaInstituciones = mysqli_query($conexion, "SELECT ins_id, ins_siglas, ins_nombre 
        FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_enviroment='" . ENVIROMENT . "' 
        ORDER BY ins_siglas ASC");
} catch (Exception $e) {
    $consultaInstituciones = null;
}

// Obtener páginas únicas para filtro
try {
    $consultaPaginas = mysqli_query($conexion, "SELECT DISTINCT pagp_id, pagp_pagina 
        FROM " . $baseDatosServicios . ".paginas_publicidad 
        WHERE pagp_id IS NOT NULL
        ORDER BY pagp_pagina ASC
        LIMIT 500");
} catch (Exception $e) {
    $consultaPaginas = null;
}
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
}

.historial-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding-bottom: 30px;
}

.filters-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.btn-action {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-eliminar-seleccionados {
    background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);
    color: white;
}

.btn-eliminar-todos {
    background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
    color: white;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.btn-action:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.historial-row {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    border-left: 4px solid #e8ecf1;
    transition: all 0.2s ease;
    cursor: pointer;
}

.historial-row:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateX(4px);
    border-left-color: var(--primary-color);
}

.historial-row.selected {
    background: linear-gradient(135deg, #f0f4ff 0%, #e8ecff 100%);
    border-left-color: var(--primary-color);
}

.historial-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.historial-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    font-size: 13px;
    color: #666;
}

.historial-info-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.historial-info-item i {
    color: var(--primary-color);
    width: 16px;
}

.badge-custom {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.modal-backdrop.show {
    opacity: 0.7;
}

.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

.detail-row {
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.detail-value {
    color: #6b7280;
    word-break: break-all;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.checkbox-select {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    display: none;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

</head>

<?php include("../compartido/body.php"); ?>

<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>
    <?php include("../compartido/panel-color.php"); ?>
    
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        
        <div class="page-content-wrapper">
            <div class="page-content historial-container">
                
                <!-- Header -->
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class="pull-left">
                            <div class="page-title">
                                <i class="fas fa-history"></i> Historial de Acciones
                            </div>
                            <?php include("../compartido/texto-manual-ayuda.php"); ?>
                        </div>
                    </div>
                </div>

                <?php include("../../config-general/mensajes-informativos.php"); ?>

                <!-- Barra de Filtros -->
                <div class="filters-card">
                    <h5 style="margin-bottom: 20px;">
                        <i class="fas fa-filter"></i> Filtros de Búsqueda
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label><i class="fas fa-search"></i> Buscar</label>
                            <input type="text" id="filtro_busqueda" class="form-control" placeholder="Página, URL, IP...">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label><i class="fas fa-building"></i> Institución</label>
                            <select id="filtro_institucion" class="form-control">
                                <option value="todos">Todas</option>
                                <?php
                                if ($consultaInstituciones) {
                                    while ($inst = mysqli_fetch_array($consultaInstituciones, MYSQLI_BOTH)) {
                                        echo '<option value="'.$inst['ins_id'].'">'.$inst['ins_siglas'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label><i class="fas fa-file"></i> Página</label>
                            <select id="filtro_pagina" class="form-control">
                                <option value="todos">Todas</option>
                                <?php
                                if ($consultaPaginas) {
                                    while ($pag = mysqli_fetch_array($consultaPaginas, MYSQLI_BOTH)) {
                                        echo '<option value="'.$pag['pagp_id'].'">'.$pag['pagp_pagina'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label><i class="fas fa-calendar"></i> Desde</label>
                            <input type="date" id="filtro_fecha_desde" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label><i class="fas fa-calendar"></i> Hasta</label>
                            <input type="date" id="filtro_fecha_hasta" class="form-control">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label><i class="fas fa-list"></i> Mostrar</label>
                            <select id="registros_por_pagina" class="form-control">
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" id="btn_limpiar_filtros" class="btn btn-secondary">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                            <button type="button" id="btn_aplicar_filtros" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Acciones Masivas -->
                <div class="action-buttons">
                    <label class="btn btn-secondary" style="margin: 0;">
                        <input type="checkbox" id="checkbox_seleccionar_todos" style="margin-right: 8px;">
                        Seleccionar Todos
                    </label>
                    <button class="btn-action btn-eliminar-seleccionados" id="btn_eliminar_seleccionados" disabled>
                        <i class="fas fa-trash-alt"></i> Eliminar Seleccionados (<span id="count_seleccionados">0</span>)
                    </button>
                    <button class="btn-action btn-eliminar-todos" id="btn_eliminar_todos">
                        <i class="fas fa-trash"></i> Eliminar Todos
                    </button>
                </div>

                <!-- Tarjeta Principal -->
                <div class="card card-topline-purple">
                    <div class="card-head">
                        <header>
                            Historial de Acciones
                            <span id="info_registros" class="badge badge-info ml-2">Cargando...</span>
                        </header>
                        <div class="tools">
                            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="cargarHistorial()"></a>
                            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                            <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Loading -->
                        <div id="loading_historial" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <p class="mt-3">Cargando historial...</p>
                        </div>

                        <!-- Contenedor de registros -->
                        <div id="contenedor_historial">
                            <!-- Se cargará dinámicamente -->
                        </div>

                        <!-- Paginación -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div id="info_paginacion" class="text-muted"></div>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Paginación">
                                    <ul id="paginacion_controles" class="pagination justify-content-end"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php"); ?>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Detalles del Registro #<span id="modal_id">-</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_body">
                <!-- Se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Scripts -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>

<script>
let paginaActual = 1;
let registrosPorPagina = 50;
let registrosSeleccionados = [];

$(document).ready(function() {
    cargarHistorial();
    
    // Aplicar filtros
    $('#btn_aplicar_filtros').on('click', function() {
        paginaActual = 1;
        cargarHistorial();
    });
    
    // Limpiar filtros
    $('#btn_limpiar_filtros').on('click', function() {
        $('#filtro_busqueda').val('');
        $('#filtro_institucion').val('todos');
        $('#filtro_pagina').val('todos');
        $('#filtro_fecha_desde').val('');
        $('#filtro_fecha_hasta').val('');
        $('#registros_por_pagina').val('50');
        paginaActual = 1;
        registrosPorPagina = 50;
        cargarHistorial();
    });
    
    // Cambio en registros por página
    $('#registros_por_pagina').on('change', function() {
        registrosPorPagina = parseInt($(this).val());
        paginaActual = 1;
        cargarHistorial();
    });
    
    // Seleccionar todos
    $('#checkbox_seleccionar_todos').on('change', function() {
        const checked = $(this).is(':checked');
        $('.checkbox-registro').prop('checked', checked);
        actualizarSeleccionados();
    });
    
    // Eliminar seleccionados
    $('#btn_eliminar_seleccionados').on('click', function() {
        if (registrosSeleccionados.length === 0) {
            alert('No hay registros seleccionados');
            return;
        }
        
        if (confirm(`¿Estás seguro de eliminar ${registrosSeleccionados.length} registro(s) seleccionado(s)?`)) {
            eliminarRegistros('lote', registrosSeleccionados);
        }
    });
    
    // Eliminar todos
    $('#btn_eliminar_todos').on('click', function() {
        const confirmText = prompt('Esta acción eliminará TODOS los registros del historial.\n\nPara confirmar, escribe: ELIMINAR TODO');
        
        if (confirmText === 'ELIMINAR TODO') {
            eliminarRegistros('todos', [], 'CONFIRMAR_ELIMINAR_TODO');
        } else if (confirmText !== null) {
            alert('Confirmación incorrecta. No se eliminó nada.');
        }
    });
});

function cargarHistorial() {
    $('#loading_historial').show();
    $('#contenedor_historial').html('');
    registrosSeleccionados = [];
    $('#checkbox_seleccionar_todos').prop('checked', false);
    
    $.ajax({
        url: 'ajax-historial-listar.php',
        type: 'POST',
        dataType: 'json',
        data: {
            pagina: paginaActual,
            porPagina: registrosPorPagina,
            busqueda: $('#filtro_busqueda').val(),
            institucion: $('#filtro_institucion').val(),
            pagina_id: $('#filtro_pagina').val(),
            fechaDesde: $('#filtro_fecha_desde').val(),
            fechaHasta: $('#filtro_fecha_hasta').val()
        },
        success: function(response) {
            $('#loading_historial').hide();
            
            if (response.success) {
                renderizarHistorial(response.registros, response.paginacion);
            } else {
                mostrarError(response.message);
            }
        },
        error: function(xhr, status, error) {
            $('#loading_historial').hide();
            console.error('Error:', error);
            mostrarError('Error de conexión');
        }
    });
}

function renderizarHistorial(registros, paginacion) {
    let html = '';
    
    if (registros.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5>No se encontraron registros</h5>
                <p class="text-muted">Intenta ajustar los filtros</p>
            </div>
        `;
    } else {
        registros.forEach(function(reg) {
            const tiempoCarga = parseFloat(reg.hil_tiempo_carga);
            const badgeTiempo = tiempoCarga > 5 ? 'badge-warning' : tiempoCarga > 2 ? 'badge-info' : 'badge-success';
            
            html += `
                <div class="historial-row" data-id="${reg.hil_id}">
                    <div class="historial-header">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <input type="checkbox" class="checkbox-registro checkbox-select" value="${reg.hil_id}">
                            <div>
                                <strong style="color: var(--primary-color); font-size: 16px;">
                                    <i class="fas fa-file-alt"></i> ${reg.pagp_pagina || reg.hil_titulo || 'Página no identificada'}
                                </strong>
                                <div style="font-size: 12px; color: #9ca3af; margin-top: 4px;">
                                    ID: #${reg.hil_id} | ${reg.hil_fecha}
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <span class="badge-custom ${badgeTiempo}">
                                <i class="fas fa-clock"></i> ${reg.hil_tiempo_carga}s
                            </span>
                            <button class="btn btn-sm btn-info" onclick="verDetalles(${reg.hil_id}, event)">
                                <i class="fas fa-eye"></i> Detalles
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarIndividual(${reg.hil_id}, event)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="historial-info">
                        <div class="historial-info-item">
                            <i class="fas fa-user"></i>
                            <span><strong>Usuario:</strong> ${reg.responsable}</span>
                        </div>
                        <div class="historial-info-item">
                            <i class="fas fa-building"></i>
                            <span><strong>Institución:</strong> ${reg.ins_siglas}</span>
                        </div>
                        <div class="historial-info-item">
                            <i class="fas fa-network-wired"></i>
                            <span><strong>IP:</strong> ${reg.hil_ip}</span>
                        </div>
                        <div class="historial-info-item">
                            <i class="fas fa-memory"></i>
                            <span><strong>Memoria:</strong> ${parseFloat(reg.hil_uso_memoria_mb || 0).toFixed(2)} MB</span>
                        </div>
                        ${reg.uss_autologin !== 'NO' ? `
                        <div class="historial-info-item">
                            <i class="fas fa-sign-in-alt"></i>
                            <span><strong>Autologin:</strong> ${reg.uss_autologin}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
    }
    
    $('#contenedor_historial').html(html);
    renderizarPaginacion(paginacion);
    actualizarInfoRegistros(paginacion);
    
    // Event listener para checkboxes
    $('.checkbox-registro').on('change', actualizarSeleccionados);
}

function renderizarPaginacion(paginacion) {
    let html = '';
    
    if (paginacion.paginaActual > 1) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.paginaActual - 1})">«</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">«</span></li>`;
    }
    
    let inicio = Math.max(1, paginacion.paginaActual - 2);
    let fin = Math.min(paginacion.totalPaginas, paginacion.paginaActual + 2);
    
    if (inicio > 1) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(1)">1</a></li>`;
        if (inicio > 2) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = inicio; i <= fin; i++) {
        if (i === paginacion.paginaActual) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${i})">${i}</a></li>`;
        }
    }
    
    if (fin < paginacion.totalPaginas) {
        if (fin < paginacion.totalPaginas - 1) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.totalPaginas})">${paginacion.totalPaginas}</a></li>`;
    }
    
    if (paginacion.paginaActual < paginacion.totalPaginas) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.paginaActual + 1})">»</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">»</span></li>`;
    }
    
    $('#paginacion_controles').html(html);
}

function actualizarInfoRegistros(paginacion) {
    let inicio = (paginacion.paginaActual - 1) * paginacion.porPagina + 1;
    let fin = Math.min(paginacion.paginaActual * paginacion.porPagina, paginacion.totalRegistros);
    
    $('#info_registros').text(`${paginacion.totalRegistros} registros`);
    $('#info_paginacion').html(`Mostrando <strong>${inicio}</strong> a <strong>${fin}</strong> de <strong>${paginacion.totalRegistros}</strong> registros`);
}

function irAPagina(pagina) {
    paginaActual = pagina;
    cargarHistorial();
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function actualizarSeleccionados() {
    registrosSeleccionados = [];
    $('.checkbox-registro:checked').each(function() {
        registrosSeleccionados.push(parseInt($(this).val()));
    });
    
    $('#count_seleccionados').text(registrosSeleccionados.length);
    $('#btn_eliminar_seleccionados').prop('disabled', registrosSeleccionados.length === 0);
    
    // Actualizar checkbox "seleccionar todos"
    const totalCheckboxes = $('.checkbox-registro').length;
    const totalChecked = $('.checkbox-registro:checked').length;
    $('#checkbox_seleccionar_todos').prop('checked', totalCheckboxes > 0 && totalCheckboxes === totalChecked);
}

function verDetalles(id, event) {
    if (event) event.stopPropagation();
    
    // Buscar el registro en los datos cargados
    $.ajax({
        url: 'ajax-historial-listar.php',
        type: 'POST',
        data: { pagina: paginaActual, porPagina: registrosPorPagina },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const registro = response.registros.find(r => r.hil_id == id);
                if (registro) {
                    mostrarModalDetalles(registro);
                }
            }
        }
    });
}

function mostrarModalDetalles(reg) {
    $('#modal_id').text(reg.hil_id);
    
    const html = `
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-user"></i> Usuario</div>
            <div class="detail-value">${reg.responsable} (ID: ${reg.hil_usuario})</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-calendar"></i> Fecha y Hora</div>
            <div class="detail-value">${reg.hil_fecha}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-file"></i> Página</div>
            <div class="detail-value">${reg.pagp_pagina || 'N/A'} (Código: ${reg.hil_titulo})</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-link"></i> URL</div>
            <div class="detail-value"><code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${reg.hil_url}</code></div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-building"></i> Institución</div>
            <div class="detail-value">${reg.ins_nombre} (${reg.ins_siglas}) - BD: ${reg.ins_bd}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-network-wired"></i> IP</div>
            <div class="detail-value">${reg.hil_ip}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-desktop"></i> Sistema Operativo</div>
            <div class="detail-value" style="font-size: 11px;">${reg.hil_so}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-arrow-left"></i> Página Anterior</div>
            <div class="detail-value"><code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 11px;">${reg.hil_pagina_anterior}</code></div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-clock"></i> Tiempo de Carga</div>
            <div class="detail-value">${reg.hil_tiempo_carga} segundos</div>
        </div>
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-memory"></i> Uso de Memoria</div>
            <div class="detail-value">
                Consumida: ${parseFloat(reg.hil_uso_memoria_mb || 0).toFixed(2)} MB | 
                Pico: ${parseFloat(reg.hil_pico_memoria_mb || 0).toFixed(2)} MB
            </div>
        </div>
        ${reg.uss_autologin !== 'NO' ? `
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-sign-in-alt"></i> Autologin</div>
            <div class="detail-value">${reg.uss_autologin} (ID: ${reg.hil_usuario_autologin})</div>
        </div>
        ` : ''}
        <div class="detail-row">
            <div class="detail-label"><i class="fas fa-info-circle"></i> Momento</div>
            <div class="detail-value">${reg.hil_momento}</div>
        </div>
    `;
    
    $('#modal_body').html(html);
    $('#modalDetalles').modal('show');
}

function eliminarIndividual(id, event) {
    if (event) event.stopPropagation();
    
    if (confirm('¿Estás seguro de eliminar este registro?')) {
        eliminarRegistros('individual', [id]);
    }
}

function eliminarRegistros(accion, ids, confirmacion = '') {
    $('#loadingOverlay').show();
    
    $.ajax({
        url: 'ajax-historial-eliminar.php',
        type: 'POST',
        dataType: 'json',
        data: {
            accion: accion,
            ids: ids,
            confirmacion: confirmacion,
            institucion: $('#filtro_institucion').val()
        },
        success: function(response) {
            $('#loadingOverlay').hide();
            
            if (response.success) {
                $.toast({
                    heading: 'Éxito',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#10b981',
                    icon: 'success',
                    hideAfter: 3000
                });
                
                // Recargar historial
                cargarHistorial();
            } else {
                $.toast({
                    heading: 'Error',
                    text: response.message,
                    position: 'top-right',
                    loaderBg: '#ef4444',
                    icon: 'error',
                    hideAfter: 5000
                });
            }
        },
        error: function(xhr, status, error) {
            $('#loadingOverlay').hide();
            console.error('Error:', error);
            $.toast({
                heading: 'Error',
                text: 'Error de conexión',
                position: 'top-right',
                loaderBg: '#ef4444',
                icon: 'error',
                hideAfter: 5000
            });
        }
    });
}

function mostrarError(mensaje) {
    $('#contenedor_historial').html(`
        <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
            <h5>Error</h5>
            <p class="text-muted">${mensaje}</p>
        </div>
    `);
}
</script>

</body>
</html>

