<?php
$idPaginaInterna = 'DT0074';

if (empty($_SESSION["id"])) {
    include("session.php");
    $input = json_decode(file_get_contents("php://input"), true);
    if (!empty($input)) {
        $_GET = $input;
    }
}

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
?>

<style>
    /* ========================================
       ESTILOS MEJORADOS PARA MODAL RETIRAR/RESTAURAR
       ======================================== */
    
    /* Card para información del estudiante */
    .info-estudiante-retirar {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .info-estudiante-retirar.restaurar {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .info-estudiante-retirar h5 {
        margin: 0 0 15px 0;
        font-size: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .info-estudiante-retirar h5 i {
        margin-right: 10px;
        font-size: 24px;
    }
    
    .info-estudiante-retirar .detalle-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .info-estudiante-retirar .detalle-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .info-estudiante-retirar .detalle-label {
        font-weight: 500;
        opacity: 0.9;
    }
    
    .info-estudiante-retirar .detalle-value {
        font-weight: 600;
    }
    
    /* Card para el historial */
    .historial-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .historial-card h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 16px;
    }
    
    .historial-item {
        background: white;
        border-left: 4px solid #667eea;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .historial-item:last-child {
        margin-bottom: 0;
    }
    
    .historial-item strong {
        color: #667eea;
    }
    
    /* Formulario mejorado */
    #form-<?= $idModal ?> .form-group {
        margin-bottom: 20px;
    }
    
    #form-<?= $idModal ?> label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    
    #form-<?= $idModal ?> label i {
        margin-right: 8px;
        color: #667eea;
    }
    
    #form-<?= $idModal ?> .form-control {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
    }
    
    #form-<?= $idModal ?> .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    /* Textarea específico para motivo */
    #form-<?= $idModal ?> textarea.form-control {
        resize: vertical;
        min-height: 120px;
        max-height: 300px;
        font-size: 14px;
        line-height: 1.6;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    
    #form-<?= $idModal ?> textarea.form-control:read-only {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
    
    #form-<?= $idModal ?> .form-text {
        margin-top: 8px;
        font-size: 13px;
    }
    
    #form-<?= $idModal ?> .form-text i {
        margin-right: 5px;
    }
    
    /* Alert personalizado */
    .alert-custom {
        border-radius: 8px;
        padding: 15px 20px;
        margin: 20px 0;
        border-left: 4px solid;
    }
    
    .alert-custom.warning {
        background: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }
    
    .alert-custom i {
        margin-right: 10px;
    }
    
    /* Botón submit mejorado */
    .btn-submit-custom {
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-submit-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }
    
    .btn-submit-custom.btn-danger {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    }
    
    .btn-submit-custom.btn-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .info-estudiante-retirar {
            padding: 15px;
        }
        
        .historial-card {
            padding: 15px;
        }
    }
</style>

<?php

$id = "";
if (!empty($_GET["id"])) {
    $id = base64_decode($_GET["id"]);
}

// Definir ID único para el modal
$idModal = !empty($id) ? 'retirar-estudiante-' . md5($id) : 'retirar-estudiante-default';

$e = Estudiantes::traerDatosEstudiantesretirados($conexion, $config, $id);

$nombreBoton = 'Restaurar Matrícula';
$colorBoton = 'success';
$readonly = "readonly";
$tituloFormulario = 'Restaurar Estudiante';

if ($e['mat_estado_matricula'] == MATRICULADO || $e['mat_estado_matricula'] == ASISTENTE || $e['mat_estado_matricula'] == NO_MATRICULADO || $e['mat_estado_matricula'] == EN_INSCRIPCION) {
    $nombreBoton = 'Retirar y cancelar matrícula';
    $colorBoton = 'danger';
    $readonly = "";
    $tituloFormulario = 'Retirar Estudiante';
}
?>
<form action="estudiantes-retirar-actualizar.php" method="post" class="form-horizontal" enctype="multipart/form-data" id="form-<?= $idModal ?>">
    <input type="hidden" value="<?= $e['mat_id']; ?>" name="estudiante">
    <input type="hidden" value="<?= $e['mat_estado_matricula']; ?>" name="estadoMatricula">

    <!-- Card con información del estudiante -->
    <div class="info-estudiante-retirar <?= ($e['mat_estado_matricula'] == CANCELADO) ? 'restaurar' : ''; ?>">
        <h5>
            <i class="fa <?= ($e['mat_estado_matricula'] == CANCELADO) ? 'fa-undo' : 'fa-user-times'; ?>"></i>
            <?= $tituloFormulario; ?>
        </h5>
        <div class="detalle-row">
            <span class="detalle-label"><i class="fa fa-id-badge"></i> Estudiante:</span>
            <span class="detalle-value"><?= $e['mat_documento'] . " - " . Estudiantes::NombreCompletoDelEstudiante($e); ?></span>
        </div>
        <div class="detalle-row">
            <span class="detalle-label"><i class="fa fa-info-circle"></i> Estado Actual:</span>
            <span class="detalle-value"><?= $estadosMatriculasEstudiantes[$e['mat_estado_matricula']]; ?></span>
        </div>
        <?php if (!empty($e['matret_fecha'])) { ?>
        <div class="detalle-row">
            <span class="detalle-label"><i class="fa fa-calendar"></i> Última Actualización:</span>
            <span class="detalle-value"><?= $e['matret_fecha']; ?></span>
        </div>
        <div class="detalle-row">
            <span class="detalle-label"><i class="fa fa-user"></i> Último Responsable:</span>
            <span class="detalle-value"><?= $e['uss_usuario'] . " - " . UsuariosPadre::nombreCompletoDelUsuario($e); ?></span>
        </div>
        <?php } ?>
    </div>

    <?php if (empty($e['matret_fecha'])) { ?>
        <div class="alert-custom warning">
            <i class="fa fa-info-circle"></i>
            <strong>Nota:</strong> Este estudiante no tiene historial de retiros.
        </div>
    <?php } ?>

    <?php if ($e['mat_estado_matricula'] == MATRICULADO || $e['mat_estado_matricula'] == ASISTENTE || $e['mat_estado_matricula'] == NO_MATRICULADO || $e['mat_estado_matricula'] == EN_INSCRIPCION || !empty($e['matret_fecha'])) { ?>
        <div class="form-group">
            <label><i class="fa fa-comment-alt"></i> Motivo de Retiro</label>
            <textarea class="form-control" id="editor1" name="motivo" rows="6" placeholder="Describe el motivo del retiro del estudiante..." <?php echo $readonly; ?>><?= $e['matret_motivo']; ?></textarea>
            <small class="form-text text-muted">
                <i class="fa fa-info-circle"></i> Este campo es obligatorio y quedará registrado en el historial.
            </small>
        </div>
    <?php } ?>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-<?= $colorBoton; ?> btn-submit-custom" name="consultas">
            <i class="fa <?= ($e['mat_estado_matricula'] == CANCELADO) ? 'fa-undo' : 'fa-user-times'; ?>"></i>
            <?= $nombreBoton; ?>
        </button>
    </div>

</form>

<!-- Historial de retiros -->
<?php
$consultaHistorial = Estudiantes::listarDatosEstudiantesretirados($conexion, $config, $id);
$numHistorial = mysqli_num_rows($consultaHistorial);
if ($numHistorial > 0) {
?>
<div class="historial-card">
    <h6><i class="fa fa-history"></i> Historial de Retiros</h6>
    <?php
    $cont = 1;
    while ($datosHistorial = mysqli_fetch_array($consultaHistorial)) {
        $motivo = str_replace(['<p>', '</p>'], '', $datosHistorial['matret_motivo']);
    ?>
    <div class="historial-item">
        <div><strong>Retiro #<?= $cont; ?></strong></div>
        <div><i class="fa fa-calendar"></i> <strong>Fecha:</strong> <?= $datosHistorial['matret_fecha']; ?></div>
        <div><i class="fa fa-comment"></i> <strong>Motivo:</strong> <?= $motivo; ?></div>
        <div><i class="fa fa-user"></i> <strong>Responsable:</strong> <?= UsuariosPadre::nombreCompletoDelUsuario($datosHistorial); ?></div>
    </div>
    <?php
        $cont++;
    }
    ?>
</div>
<?php } ?>