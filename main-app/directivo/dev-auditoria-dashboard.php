<?php
include("session.php");

$idPaginaInterna = 'DV0005';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaLogger.php");

// Parámetros de filtros
$filtroNivel = isset($_GET['nivel']) ? $_GET['nivel'] : null;
$filtroAccion = isset($_GET['accion']) ? $_GET['accion'] : null;
$filtroHoras = isset($_GET['horas']) ? (int)$_GET['horas'] : 24;

// Obtener estadísticas y logs
$stats = AuditoriaLogger::obtenerEstadisticas($filtroHoras);
$logs = AuditoriaLogger::obtenerLogs(100, $filtroNivel, $filtroAccion, $filtroHoras);

include("../compartido/head.php");
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --critical-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.auditoria-dashboard {
    padding: 20px;
}

.page-header-auditoria {
    background: var(--info-gradient);
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
}

.page-header-auditoria h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header-auditoria p {
    margin: 0;
    opacity: 0.9;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card-auditoria {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    border-left: 4px solid;
}

.stat-card-auditoria.total { border-color: #3b82f6; }
.stat-card-auditoria.info { border-color: #10b981; }
.stat-card-auditoria.warning { border-color: #f59e0b; }
.stat-card-auditoria.critical { border-color: #ef4444; }

.stat-number {
    font-size: 36px;
    font-weight: 800;
    color: #1f2937;
    margin: 10px 0 5px 0;
}

.stat-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 600;
}

.filters-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.filter-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 8px;
}

.filter-group select {
    width: 100%;
    padding: 10px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

.logs-table {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    overflow-x: auto;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.table-modern thead {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.table-modern thead th {
    padding: 12px 10px;
    text-align: left;
    font-weight: 700;
    color: white;
    font-size: 12px;
    text-transform: uppercase;
    white-space: nowrap;
}

.table-modern tbody tr {
    border-bottom: 1px solid #e5e7eb;
}

.table-modern tbody tr:hover {
    background: #f9fafb;
}

.table-modern tbody td {
    padding: 12px 10px;
    vertical-align: middle;
}

.badge-nivel {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.badge-nivel.info {
    background: #d1fae5;
    color: #065f46;
}

.badge-nivel.warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-nivel.critical {
    background: #fee2e2;
    color: #991b1b;
}

.badge-accion {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    background: #f3f4f6;
    color: #1f2937;
}

.ip-text {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #6b7280;
}

.btn-ver-detalles {
    padding: 5px 12px;
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 11px;
    cursor: pointer;
    font-weight: 600;
}

.btn-ver-detalles:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
}

/* Modal */
.modal-detalles {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 10000;
    justify-content: center;
    align-items: center;
}

.modal-detalles.active {
    display: flex;
}

.modal-content-detalles {
    background: white;
    border-radius: 15px;
    padding: 30px;
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.modal-header-custom h3 {
    margin: 0;
    color: #1f2937;
    font-weight: 700;
}

.btn-close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}

.detalle-row {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.detalle-label {
    font-weight: 700;
    color: #4b5563;
}

.detalle-value {
    color: #1f2937;
}

.json-viewer {
    background: #1f2937;
    color: #10b981;
    padding: 15px;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    overflow-x: auto;
    max-height: 300px;
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
            <div class="page-content auditoria-dashboard">
                
                <!-- Header -->
                <div class="page-header-auditoria">
                    <h2>
                        <i class="fas fa-clipboard-list"></i>
                        Dashboard de Auditoría de Seguridad
                    </h2>
                    <p>Registro completo de acciones sensibles del sistema</p>
                </div>
                
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card-auditoria total">
                        <div class="stat-label">Total Acciones</div>
                        <div class="stat-number"><?= number_format($stats['total']); ?></div>
                        <small style="color: #6b7280;">últimas <?= $filtroHoras; ?> horas</small>
                    </div>
                    
                    <div class="stat-card-auditoria info">
                        <div class="stat-label">Informativas</div>
                        <div class="stat-number"><?= number_format($stats['por_nivel']['INFO']); ?></div>
                        <small style="color: #6b7280;">nivel INFO</small>
                    </div>
                    
                    <div class="stat-card-auditoria warning">
                        <div class="stat-label">Advertencias</div>
                        <div class="stat-number"><?= number_format($stats['por_nivel']['WARNING']); ?></div>
                        <small style="color: #6b7280;">nivel WARNING</small>
                    </div>
                    
                    <div class="stat-card-auditoria critical">
                        <div class="stat-label">Críticas</div>
                        <div class="stat-number"><?= number_format($stats['por_nivel']['CRITICAL']); ?></div>
                        <small style="color: #6b7280;">nivel CRITICAL</small>
                    </div>
                </div>
                
                <!-- Filtros -->
                <div class="filters-card">
                    <h4 style="margin: 0 0 20px 0; color: #1f2937; font-weight: 700;">
                        <i class="fas fa-filter"></i> Filtros
                    </h4>
                    
                    <form method="GET" action="">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label>Período</label>
                                <select name="horas" class="form-control">
                                    <option value="1" <?= $filtroHoras == 1 ? 'selected' : ''; ?>>Última hora</option>
                                    <option value="24" <?= $filtroHoras == 24 ? 'selected' : ''; ?>>Últimas 24 horas</option>
                                    <option value="168" <?= $filtroHoras == 168 ? 'selected' : ''; ?>>Última semana</option>
                                    <option value="720" <?= $filtroHoras == 720 ? 'selected' : ''; ?>>Último mes</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label>Nivel</label>
                                <select name="nivel" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="INFO" <?= $filtroNivel == 'INFO' ? 'selected' : ''; ?>>INFO</option>
                                    <option value="WARNING" <?= $filtroNivel == 'WARNING' ? 'selected' : ''; ?>>WARNING</option>
                                    <option value="CRITICAL" <?= $filtroNivel == 'CRITICAL' ? 'selected' : ''; ?>>CRITICAL</option>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label>Acción</label>
                                <select name="accion" class="form-control">
                                    <option value="">Todas</option>
                                    <option value="LOGIN" <?= $filtroAccion == 'LOGIN' ? 'selected' : ''; ?>>LOGIN</option>
                                    <option value="LOGOUT" <?= $filtroAccion == 'LOGOUT' ? 'selected' : ''; ?>>LOGOUT</option>
                                    <option value="CREAR" <?= $filtroAccion == 'CREAR' ? 'selected' : ''; ?>>CREAR</option>
                                    <option value="EDITAR" <?= $filtroAccion == 'EDITAR' ? 'selected' : ''; ?>>EDITAR</option>
                                    <option value="ELIMINAR" <?= $filtroAccion == 'ELIMINAR' ? 'selected' : ''; ?>>ELIMINAR</option>
                                    <option value="PERMISOS" <?= $filtroAccion == 'PERMISOS' ? 'selected' : ''; ?>>PERMISOS</option>
                                    <option value="CONFIGURACION" <?= $filtroAccion == 'CONFIGURACION' ? 'selected' : ''; ?>>CONFIGURACIÓN</option>
                                    <option value="EXPORTAR" <?= $filtroAccion == 'EXPORTAR' ? 'selected' : ''; ?>>EXPORTAR</option>
                                    <option value="IMPORTAR" <?= $filtroAccion == 'IMPORTAR' ? 'selected' : ''; ?>>IMPORTAR</option>
                                </select>
                            </div>
                            
                            <div class="filter-group" style="display: flex; align-items: flex-end;">
                                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tabla de Logs -->
                <div class="logs-table">
                    <h4 style="margin: 0 0 20px 0; color: #1f2937; font-weight: 700;">
                        <i class="fas fa-list"></i> Registro de Auditoría (<?= count($logs); ?> registros)
                    </h4>
                    
                    <?php if (empty($logs)): ?>
                        <div style="text-align: center; padding: 60px 20px;">
                            <i class="fas fa-inbox" style="font-size: 64px; color: #d1d5db; margin-bottom: 20px;"></i>
                            <h3 style="color: #1f2937;">No se encontraron registros</h3>
                            <p style="color: #6b7280;">Ajusta los filtros para ver más resultados</p>
                        </div>
                    <?php else: ?>
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nivel</th>
                                    <th>Acción</th>
                                    <th>Usuario</th>
                                    <th>Módulo</th>
                                    <th>Descripción</th>
                                    <th>IP</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <?= date('d/m/Y H:i', strtotime($log['aud_fecha'])); ?>
                                    </td>
                                    <td>
                                        <span class="badge-nivel <?= strtolower($log['aud_nivel']); ?>">
                                            <?= $log['aud_nivel']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-accion"><?= $log['aud_accion']; ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= $log['uss_nombre'] ?? 'N/A'; ?> <?= $log['uss_apellido1'] ?? ''; ?></strong>
                                        </div>
                                        <small style="color: #6b7280;"><?= $log['uss_usuario'] ?? $log['aud_usuario_id']; ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($log['aud_modulo']); ?></td>
                                    <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($log['aud_descripcion']); ?>
                                    </td>
                                    <td>
                                        <span class="ip-text"><?= htmlspecialchars($log['aud_ip']); ?></span>
                                    </td>
                                    <td>
                                        <button class="btn-ver-detalles" onclick="verDetalles(<?= htmlspecialchars(json_encode($log)); ?>)">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php"); ?>
</div>

<!-- Modal de Detalles -->
<div class="modal-detalles" id="modalDetalles">
    <div class="modal-content-detalles">
        <div class="modal-header-custom">
            <h3><i class="fas fa-info-circle"></i> Detalles de Auditoría</h3>
            <button class="btn-close-modal" onclick="cerrarModal()">×</button>
        </div>
        <div id="modalBody"></div>
    </div>
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
function verDetalles(log) {
    const modal = document.getElementById('modalDetalles');
    const modalBody = document.getElementById('modalBody');
    
    let datosAdicionales = '{}';
    try {
        datosAdicionales = JSON.stringify(JSON.parse(log.aud_datos_adicionales || '{}'), null, 2);
    } catch(e) {
        datosAdicionales = log.aud_datos_adicionales || '{}';
    }
    
    modalBody.innerHTML = `
        <div class="detalle-row">
            <div class="detalle-label">ID:</div>
            <div class="detalle-value">#${log.aud_id}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Fecha y Hora:</div>
            <div class="detalle-value">${log.aud_fecha}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Acción:</div>
            <div class="detalle-value"><span class="badge-accion">${log.aud_accion}</span></div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Nivel:</div>
            <div class="detalle-value"><span class="badge-nivel ${log.aud_nivel.toLowerCase()}">${log.aud_nivel}</span></div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Usuario:</div>
            <div class="detalle-value">
                ${log.uss_nombre || 'N/A'} ${log.uss_apellido1 || ''}<br>
                <small style="color: #6b7280;">${log.uss_usuario || log.aud_usuario_id}</small>
            </div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Módulo:</div>
            <div class="detalle-value">${log.aud_modulo}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Descripción:</div>
            <div class="detalle-value">${log.aud_descripcion}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">IP:</div>
            <div class="detalle-value"><span class="ip-text">${log.aud_ip}</span></div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">URL:</div>
            <div class="detalle-value" style="word-break: break-all; font-size: 12px;">${log.aud_url || 'N/A'}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Método HTTP:</div>
            <div class="detalle-value">${log.aud_metodo || 'N/A'}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">User Agent:</div>
            <div class="detalle-value" style="word-break: break-all; font-size: 11px;">${log.aud_user_agent || 'N/A'}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Institución:</div>
            <div class="detalle-value">${log.aud_institucion || 'N/A'}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Año:</div>
            <div class="detalle-value">${log.aud_year || 'N/A'}</div>
        </div>
        <div style="margin-top: 20px;">
            <div class="detalle-label" style="margin-bottom: 10px;">Datos Adicionales (JSON):</div>
            <div class="json-viewer">${datosAdicionales}</div>
        </div>
    `;
    
    modal.classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalDetalles').classList.remove('active');
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});

// Cerrar modal al hacer click fuera
document.getElementById('modalDetalles').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>

</body>
</html>

