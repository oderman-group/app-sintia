<?php include("session.php"); ?>
<?php $idPaginaInterna = 'DT0127'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php");

// Validar que la institución tenga el módulo de comunicados activo
if (!Modulos::verificarModulosDeInstitucion(Modulos::MODULO_COMUNICADOS)) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/BindSQL.php");

// Obtener estadísticas
global $config, $baseDatosServicios;
$institucion = $config['conf_id_institucion'];
$year = $_SESSION["bd"] ?? date('Y');

// Estadísticas generales
$sqlStats = "SELECT 
    com_canal,
    com_estado,
    COUNT(*) as total
FROM " . $baseDatosServicios . ".comunicaciones_enviadas
WHERE com_institucion = ? AND com_year = ?
GROUP BY com_canal, com_estado";

$stmtStats = BindSQL::prepararSQL($sqlStats, [$institucion, $year]);
$stats = [];
while ($row = mysqli_fetch_assoc($stmtStats)) {
    $canal = $row['com_canal'];
    $estado = $row['com_estado'];
    if (!isset($stats[$canal])) {
        $stats[$canal] = ['ENVIADO' => 0, 'ERROR' => 0, 'PENDIENTE' => 0];
    }
    $stats[$canal][$estado] = (int)$row['total'];
}

// Totales
$totalSMS = ($stats['SMS']['ENVIADO'] ?? 0) + ($stats['SMS']['ERROR'] ?? 0) + ($stats['SMS']['PENDIENTE'] ?? 0);
$totalWhatsApp = ($stats['WHATSAPP']['ENVIADO'] ?? 0) + ($stats['WHATSAPP']['ERROR'] ?? 0) + ($stats['WHATSAPP']['PENDIENTE'] ?? 0);
$totalEmail = ($stats['EMAIL']['ENVIADO'] ?? 0) + ($stats['EMAIL']['ERROR'] ?? 0) + ($stats['EMAIL']['PENDIENTE'] ?? 0);

$exitososSMS = $stats['SMS']['ENVIADO'] ?? 0;
$erroresSMS = $stats['SMS']['ERROR'] ?? 0;
$exitososWhatsApp = $stats['WHATSAPP']['ENVIADO'] ?? 0;
$erroresWhatsApp = $stats['WHATSAPP']['ERROR'] ?? 0;
$exitososEmail = $stats['EMAIL']['ENVIADO'] ?? 0;
$erroresEmail = $stats['EMAIL']['ERROR'] ?? 0;

$totalExitosos = $exitososSMS + $exitososWhatsApp + $exitososEmail;
$totalErrores = $erroresSMS + $erroresWhatsApp + $erroresEmail;
$totalGeneral = $totalSMS + $totalWhatsApp + $totalEmail;

?>
<style>
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-card.success {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stat-card.info {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .stat-card.danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 10px 0;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .stat-icon {
        font-size: 2rem;
        opacity: 0.8;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title">
                    <i class="fa fa-paper-plane"></i> Envío de Mensajes
                </h1>
                <p class="page-subtitle">Historial y estadísticas de comunicados enviados</p>
            </div>
        </div>

        <!-- Cards de Estadísticas -->
        <div class="row mb-4">
            <!-- Total General -->
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total de Envíos</div>
                            <div class="stat-number"><?= number_format($totalGeneral); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa fa-envelope-open"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exitosos -->
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Enviados Exitosamente</div>
                            <div class="stat-number"><?= number_format($totalExitosos); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Errores -->
            <div class="col-md-3">
                <div class="stat-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Con Errores</div>
                            <div class="stat-number"><?= number_format($totalErrores); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasa de Éxito -->
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Tasa de Éxito</div>
                            <div class="stat-number">
                                <?= $totalGeneral > 0 ? number_format(($totalExitosos / $totalGeneral) * 100, 1) : 0; ?>%
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa fa-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards por Canal -->
        <div class="row mb-4">
            <!-- SMS -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fa fa-comment"></i> SMS</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-success"><?= number_format($exitososSMS); ?></h3>
                                <small>Exitosos</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-danger"><?= number_format($erroresSMS); ?></h3>
                                <small>Errores</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <strong>Total: <?= number_format($totalSMS); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fa fa-whatsapp"></i> WhatsApp</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-success"><?= number_format($exitososWhatsApp); ?></h3>
                                <small>Exitosos</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-danger"><?= number_format($erroresWhatsApp); ?></h3>
                                <small>Errores</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <strong>Total: <?= number_format($totalWhatsApp); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fa fa-envelope"></i> Email</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-success"><?= number_format($exitososEmail); ?></h3>
                                <small>Exitosos</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-danger"><?= number_format($erroresEmail); ?></h3>
                                <small>Errores</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <strong>Total: <?= number_format($totalEmail); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Historial -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fa fa-list"></i> Historial de Comunicados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaComunicados" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Destinatario</th>
                                        <th>Canal</th>
                                        <th>Mensaje</th>
                                        <th>Estado</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM " . $baseDatosServicios . ".comunicaciones_enviadas
                                    WHERE com_institucion = ? AND com_year = ?
                                    ORDER BY com_fecha_envio DESC
                                    LIMIT 100";
                                    
                                    $stmt = BindSQL::prepararSQL($sql, [$institucion, $year]);
                                    
                                    while ($row = mysqli_fetch_assoc($stmt)) {
                                        $estadoClass = $row['com_estado'] === 'ENVIADO' ? 'success' : ($row['com_estado'] === 'ERROR' ? 'danger' : 'warning');
                                        $estadoIcon = $row['com_estado'] === 'ENVIADO' ? 'check-circle' : ($row['com_estado'] === 'ERROR' ? 'exclamation-circle' : 'clock');
                                        $canalIcon = $row['com_canal'] === 'EMAIL' ? 'envelope' : ($row['com_canal'] === 'SMS' ? 'comment' : 'whatsapp');
                                        $canalColor = $row['com_canal'] === 'EMAIL' ? 'primary' : ($row['com_canal'] === 'SMS' ? 'info' : 'success');
                                        
                                        $mensajePreview = mb_substr($row['com_mensaje'], 0, 50) . (mb_strlen($row['com_mensaje']) > 50 ? '...' : '');
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($row['com_fecha_envio'])); ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['com_usuario_nombre']); ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($row['com_destinatario']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $canalColor; ?>">
                                                <i class="fa fa-<?= $canalIcon; ?>"></i> <?= $row['com_canal']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span title="<?= htmlspecialchars($row['com_mensaje']); ?>">
                                                <?= htmlspecialchars($mensajePreview); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $estadoClass; ?>">
                                                <i class="fa fa-<?= $estadoIcon; ?>"></i> <?= $row['com_estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['com_error'])): ?>
                                                <small class="text-danger" title="<?= htmlspecialchars($row['com_error']); ?>">
                                                    <?= htmlspecialchars(mb_substr($row['com_error'], 0, 50)); ?>...
                                                    <?php if (!empty($row['com_codigo_error'])): ?>
                                                        <br><strong>Código: <?= htmlspecialchars($row['com_codigo_error']); ?></strong>
                                                    <?php endif; ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#tablaComunicados').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 25
    });
});
</script>

<?php include("../compartido/footer.php"); ?>

