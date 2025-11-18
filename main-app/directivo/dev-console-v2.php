<?php
include("session.php");

$idPaginaInterna = 'DV0012';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

include("../compartido/head.php");

$Plataforma = new Plataforma;
?>

<!-- Estilos adicionales -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --log-error: #fee2e2;
    --log-error-text: #991b1b;
    --log-warning: #fef3c7;
    --log-warning-text: #92400e;
    --log-info: #dbeafe;
    --log-info-text: #1e40af;
    --log-success: #d1fae5;
    --log-success-text: #065f46;
}

.logs-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding-bottom: 30px;
}

.logs-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.nav-tabs {
    border-bottom: 2px solid #e8ecf1;
    margin-bottom: 0;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #666;
    font-weight: 600;
    padding: 12px 24px;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: #667eea;
    border-bottom-color: #667eea;
}

.nav-tabs .nav-link.active {
    color: #667eea;
    border-bottom-color: #667eea;
    background: transparent;
}

.filters-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.log-line {
    padding: 12px 15px;
    margin-bottom: 8px;
    border-radius: 8px;
    border-left: 4px solid #e8ecf1;
    background: white;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.6;
    transition: all 0.2s ease;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.log-line:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateX(4px);
}

.log-line.error {
    background: var(--log-error);
    border-left-color: var(--log-error-text);
    color: var(--log-error-text);
}

.log-line.warning {
    background: var(--log-warning);
    border-left-color: var(--log-warning-text);
    color: var(--log-warning-text);
}

.log-line.info {
    background: var(--log-info);
    border-left-color: var(--log-info-text);
    color: var(--log-info-text);
}

.log-timestamp {
    color: #6b7280;
    font-weight: 600;
    margin-right: 12px;
}

.log-number {
    display: inline-block;
    min-width: 60px;
    color: #9ca3af;
    font-size: 11px;
    margin-right: 8px;
}

.archivo-info {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.archivo-info-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.archivo-info-item i {
    color: #667eea;
}

.loading-overlay {
    text-align: center;
    padding: 60px 20px;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.no-logs {
    text-align: center;
    padding: 80px 20px;
    color: #9ca3af;
}

.no-logs i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.pagination-info {
    color: #6b7280;
    font-size: 14px;
}

.auto-refresh-switch {
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.stats-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}

.stats-badge.error {
    background: var(--log-error);
    color: var(--log-error-text);
}

.stats-badge.warning {
    background: var(--log-warning);
    color: var(--log-warning-text);
}

.stats-badge.info {
    background: var(--log-info);
    color: var(--log-info-text);
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
            <div class="page-content logs-container">
                
                <!-- Header -->
                <div class="logs-header">
                    <h2 style="margin: 0 0 10px 0;">
                        <i class="fas fa-terminal"></i> Consola de Logs - SINTIA
                    </h2>
                    <p style="margin: 0; opacity: 0.9;">
                        Visualizador avanzado de logs con filtros y paginación en tiempo real
                    </p>
                </div>

                <!-- Tabs -->
                <div class="card card-topline-purple" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 0;">
                        <ul class="nav nav-tabs" role="tablist" style="padding: 20px 20px 0 20px; margin: 0;">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab_local" data-archivo="errores_local">
                                    <i class="fas fa-laptop"></i> LOCAL
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_dev" data-archivo="errores_dev">
                                    <i class="fas fa-code"></i> DEV
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_prod" data-archivo="errores_prod">
                                    <i class="fas fa-server"></i> PROD
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_directivo" data-archivo="error_log_directivo">
                                    <i class="fas fa-user-tie"></i> Directivo
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_docente" data-archivo="error_log_docente">
                                    <i class="fas fa-chalkboard-teacher"></i> Docente
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_estudiante" data-archivo="error_log_estudiante">
                                    <i class="fas fa-user-graduate"></i> Estudiante
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_mail_prod" data-archivo="mail_queue_prod">
                                    <i class="fas fa-envelope"></i> Mail Queue PROD
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_mail_dev" data-archivo="mail_queue_dev">
                                    <i class="fas fa-envelope-open"></i> Mail Queue DEV
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" style="padding: 30px;">
                            <!-- Los contenidos de tabs se generan dinámicamente -->
                            <?php
                            $tabs = [
                                ['id' => 'local', 'archivo' => 'errores_local', 'active' => true],
                                ['id' => 'dev', 'archivo' => 'errores_dev', 'active' => false],
                                ['id' => 'prod', 'archivo' => 'errores_prod', 'active' => false],
                                ['id' => 'directivo', 'archivo' => 'error_log_directivo', 'active' => false],
                                ['id' => 'docente', 'archivo' => 'error_log_docente', 'active' => false],
                                ['id' => 'estudiante', 'archivo' => 'error_log_estudiante', 'active' => false],
                                ['id' => 'mail_prod', 'archivo' => 'mail_queue_prod', 'active' => false],
                                ['id' => 'mail_dev', 'archivo' => 'mail_queue_dev', 'active' => false],
                            ];
                            
                            foreach ($tabs as $tab):
                            ?>
                            <div class="tab-pane fade <?= $tab['active'] ? 'show active' : ''; ?>" 
                                 id="tab_<?= $tab['id']; ?>" 
                                 data-archivo="<?= $tab['archivo']; ?>">
                                
                                <!-- Filtros -->
                                <div class="filters-card">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label><i class="fas fa-search"></i> Buscar</label>
                                            <input type="text" class="form-control filtro-busqueda" placeholder="Buscar en logs...">
                                        </div>
                                        <div class="col-md-2">
                                            <label><i class="fas fa-filter"></i> Nivel</label>
                                            <select class="form-control filtro-nivel">
                                                <option value="todos">Todos</option>
                                                <option value="error">Errores</option>
                                                <option value="warning">Warnings</option>
                                                <option value="info">Info</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label><i class="fas fa-list"></i> Por página</label>
                                            <select class="form-control filtro-porpagina">
                                                <option value="50">50</option>
                                                <option value="100" selected>100</option>
                                                <option value="200">200</option>
                                                <option value="500">500</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button class="btn btn-primary btn-block btn-cargar-logs">
                                                <i class="fas fa-sync-alt"></i> Cargar
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <div class="auto-refresh-switch">
                                                <input type="checkbox" class="auto-refresh-checkbox" id="auto_refresh_<?= $tab['id']; ?>">
                                                <label for="auto_refresh_<?= $tab['id']; ?>" style="margin: 0;">Auto-refresh (30s)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info del archivo -->
                                <div class="archivo-info" style="display: none;">
                                    <div class="archivo-info-item">
                                        <i class="fas fa-file-alt"></i>
                                        <span class="archivo-nombre">-</span>
                                    </div>
                                    <div class="archivo-info-item">
                                        <i class="fas fa-hdd"></i>
                                        <span class="archivo-tamano">-</span>
                                    </div>
                                    <div class="archivo-info-item">
                                        <i class="fas fa-clock"></i>
                                        <span class="archivo-modificacion">-</span>
                                    </div>
                                </div>

                                <!-- Contenedor de logs -->
                                <div class="logs-content"></div>

                                <!-- Paginación -->
                                <div class="row mt-4" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="pagination-info"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <nav>
                                            <ul class="pagination justify-content-end pagination-controles"></ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php"); ?>
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

<script>
$(document).ready(function() {
    let tabActual = '.tab-pane.active';
    let intervalos = {};
    
    // Cargar logs del tab activo al inicio
    cargarLogs($(tabActual));
    
    // Al cambiar de tab
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        tabActual = $(e.target).attr('href');
        cargarLogs($(tabActual));
    });
    
    // Botón cargar logs
    $('.btn-cargar-logs').on('click', function() {
        const $tab = $(this).closest('.tab-pane');
        cargarLogs($tab, 1);
    });
    
    // Filtros
    $('.filtro-busqueda').on('keyup', function(e) {
        if (e.key === 'Enter') {
            const $tab = $(this).closest('.tab-pane');
            cargarLogs($tab, 1);
        }
    });
    
    $('.filtro-nivel, .filtro-porpagina').on('change', function() {
        const $tab = $(this).closest('.tab-pane');
        cargarLogs($tab, 1);
    });
    
    // Auto-refresh
    $('.auto-refresh-checkbox').on('change', function() {
        const $tab = $(this).closest('.tab-pane');
        const tabId = $tab.attr('id');
        
        if ($(this).is(':checked')) {
            intervalos[tabId] = setInterval(function() {
                if ($tab.hasClass('active')) {
                    cargarLogs($tab);
                }
            }, 30000); // 30 segundos
        } else {
            if (intervalos[tabId]) {
                clearInterval(intervalos[tabId]);
                delete intervalos[tabId];
            }
        }
    });
    
    // Función principal para cargar logs
    function cargarLogs($tab, pagina = 1) {
        const archivo = $tab.data('archivo');
        const busqueda = $tab.find('.filtro-busqueda').val();
        const nivel = $tab.find('.filtro-nivel').val();
        const porPagina = parseInt($tab.find('.filtro-porpagina').val());
        
        const $content = $tab.find('.logs-content');
        
        // Mostrar loading
        $content.html(`
            <div class="loading-overlay">
                <div class="loading-spinner"></div>
                <p>Cargando logs...</p>
            </div>
        `);
        
        $.ajax({
            url: 'ajax-logs-leer.php',
            type: 'POST',
            data: {
                archivo: archivo,
                pagina: pagina,
                porPagina: porPagina,
                busqueda: busqueda,
                nivel: nivel
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderizarLogs($tab, response);
                } else {
                    $content.html(`
                        <div class="no-logs">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>Error</h4>
                            <p>${response.message}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $content.html(`
                    <div class="no-logs">
                        <i class="fas fa-times-circle"></i>
                        <h4>Error de Conexión</h4>
                        <p>No se pudieron cargar los logs</p>
                    </div>
                `);
            }
        });
    }
    
    // Renderizar logs
    function renderizarLogs($tab, data) {
        const $content = $tab.find('.logs-content');
        const $archivoInfo = $tab.find('.archivo-info');
        const $paginacionRow = $tab.find('.row').last();
        
        // Actualizar info del archivo
        $archivoInfo.find('.archivo-nombre').text(data.archivo.nombre);
        $archivoInfo.find('.archivo-tamano').text(data.archivo.tamano);
        $archivoInfo.find('.archivo-modificacion').text(data.archivo.ultima_modificacion);
        $archivoInfo.show();
        
        if (data.lineas.length === 0) {
            $content.html(`
                <div class="no-logs">
                    <i class="fas fa-inbox"></i>
                    <h4>No se encontraron logs</h4>
                    <p>Intenta ajustar los filtros de búsqueda</p>
                </div>
            `);
            $paginacionRow.hide();
            return;
        }
        
        // Renderizar líneas
        let html = '';
        data.lineas.forEach(function(linea) {
            const timestamp = linea.timestamp ? `<span class="log-timestamp">[${linea.timestamp}]</span>` : '';
            html += `
                <div class="log-line ${linea.tipo}">
                    <span class="log-number">#${linea.numero}</span>
                    ${timestamp}
                    <span class="log-content">${escapeHtml(linea.contenido)}</span>
                </div>
            `;
        });
        $content.html(html);
        
        // Renderizar paginación
        renderizarPaginacion($tab, data.paginacion);
        $paginacionRow.show();
    }
    
    // Renderizar paginación
    function renderizarPaginacion($tab, paginacion) {
        const $info = $tab.find('.pagination-info');
        const $controles = $tab.find('.pagination-controles');
        
        const inicio = (paginacion.paginaActual - 1) * paginacion.porPagina + 1;
        const fin = Math.min(paginacion.paginaActual * paginacion.porPagina, paginacion.totalLineas);
        
        $info.html(`Mostrando <strong>${inicio}</strong> a <strong>${fin}</strong> de <strong>${paginacion.totalLineas}</strong> líneas`);
        
        let html = '';
        
        // Anterior
        if (paginacion.paginaActual > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.paginaActual - 1})">«</a></li>`;
        } else {
            html += `<li class="page-item disabled"><span class="page-link">«</span></li>`;
        }
        
        // Páginas
        const inicio_pag = Math.max(1, paginacion.paginaActual - 2);
        const fin_pag = Math.min(paginacion.totalPaginas, paginacion.paginaActual + 2);
        
        for (let i = inicio_pag; i <= fin_pag; i++) {
            if (i === paginacion.paginaActual) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${i})">${i}</a></li>`;
            }
        }
        
        // Siguiente
        if (paginacion.paginaActual < paginacion.totalPaginas) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="irAPagina(${paginacion.paginaActual + 1})">»</a></li>`;
        } else {
            html += `<li class="page-item disabled"><span class="page-link">»</span></li>`;
        }
        
        $controles.html(html);
    }
    
    // Ir a página
    window.irAPagina = function(pagina) {
        const $tab = $('.tab-pane.active');
        cargarLogs($tab, pagina);
        $('html, body').animate({ scrollTop: 0 }, 300);
    };
    
    // Escapar HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>

</body>
</html>

