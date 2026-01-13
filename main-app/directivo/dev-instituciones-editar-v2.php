<?php
include("session.php");

$idPaginaInterna = 'DV0011';

include("../compartido/historial-acciones-guardar.php");

Modulos::verificarPermisoDev();

// Obtener ID de institución (por defecto la primera activa)
$institucionIdParam = !empty($_GET["id"]) ? base64_decode($_GET["id"]) : null;

if ($institucionIdParam) {
    try {
        $consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_id='" . $institucionIdParam . "' AND ins_enviroment='" . ENVIROMENT . "'");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $datosInstitucion = mysqli_fetch_array($consulta, MYSQLI_BOTH);
} else {
    // Si no hay ID, obtener la primera institución activa
    try {
        $consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_estado=1 AND ins_enviroment='" . ENVIROMENT . "' LIMIT 1");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $datosInstitucion = mysqli_fetch_array($consulta, MYSQLI_BOTH);
}

// Obtener todas las instituciones para el selector
try {
    $consultaInstituciones = mysqli_query($conexion, "SELECT ins_id, ins_siglas, ins_nombre, ins_estado 
        FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_enviroment='" . ENVIROMENT . "' 
        ORDER BY ins_siglas ASC");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

// Obtener todos los módulos disponibles
try {
    $consultaModulos = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".modulos WHERE mod_estado = 1 ORDER BY mod_nombre ASC");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

// Obtener módulos de la institución actual
$modulosInstitucion = [];
if (!empty($datosInstitucion['ins_id'])) {
    try {
        $consultaModulosInst = mysqli_query($conexion, "SELECT ipmod_modulo FROM " . BD_ADMIN . ".instituciones_modulos 
            WHERE ipmod_institucion = " . $datosInstitucion['ins_id']);
        while ($mod = mysqli_fetch_array($consultaModulosInst, MYSQLI_BOTH)) {
            $modulosInstitucion[] = $mod['ipmod_modulo'];
        }
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
}

// Contar totales
$totalModulos = mysqli_num_rows($consultaModulos);
$totalModulosAsignados = count($modulosInstitucion);

// VERIFICAR SI EXISTEN REGISTROS EN general_informacion Y configuracion
$tieneGeneralInfo = false;
$tieneConfiguracion = false;
$yearActual = $datosInstitucion['ins_year_default'] ?? date('Y');

// Información del contrato
$contratoActual = $datosInstitucion['ins_contrato'] ?? '';
$contratoExiste = false;
$contratoRutaRelativa = '';
$contratoTamano = '';
$contratoFechaActualizacion = '';
if (!empty($contratoActual)) {
    $contratoRutaFisica = ROOT_PATH . "/files-general/contratos/" . $contratoActual;
    if (file_exists($contratoRutaFisica)) {
        $contratoExiste = true;
        $contratoRutaRelativa = "../../files-general/contratos/" . $contratoActual;
        $bytesContrato = @filesize($contratoRutaFisica);
        if ($bytesContrato !== false) {
            if ($bytesContrato >= 1048576) {
                $contratoTamano = number_format($bytesContrato / 1048576, 2) . ' MB';
            } else {
                $contratoTamano = number_format(max($bytesContrato, 1) / 1024, 2) . ' KB';
            }
        }
        $timestampContrato = @filemtime($contratoRutaFisica);
        if ($timestampContrato !== false) {
            $contratoFechaActualizacion = date('d/m/Y H:i', $timestampContrato);
        }
    }
}

if (!empty($datosInstitucion['ins_id'])) {
    // Verificar general_informacion
    try {
        $consultaGeneralInfo = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".general_informacion 
            WHERE info_institucion = " . $datosInstitucion['ins_id'] . " AND info_year = '" . $yearActual . "'");
        $resultGeneralInfo = mysqli_fetch_array($consultaGeneralInfo, MYSQLI_BOTH);
        $tieneGeneralInfo = ($resultGeneralInfo['total'] > 0);
    } catch (Exception $e) {
        error_log("Error verificando general_informacion: " . $e->getMessage());
    }
    
    // Verificar configuracion
    try {
        $consultaConfig = mysqli_query($conexion, "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".configuracion 
            WHERE conf_id_institucion = " . $datosInstitucion['ins_id'] . " AND conf_agno = '" . $yearActual . "'");
        $resultConfig = mysqli_fetch_array($consultaConfig, MYSQLI_BOTH);
        $tieneConfiguracion = ($resultConfig['total'] > 0);
    } catch (Exception $e) {
        error_log("Error verificando configuracion: " . $e->getMessage());
    }
}

include("../compartido/head.php");
?>

<!-- Estilos adicionales para esta página -->
<link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
<link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- Font Awesome 6 para iconos modernos -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="../css/instituciones-modulos-v2.css" rel="stylesheet" type="text/css" />

<style>
/* Estilos inline para carga inmediata */
.instituciones-moderno-container {
    background: #f5f7fa;
    min-height: 100vh;
    padding-bottom: 30px;
}

.selector-institucion-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    color: white;
}

.institucion-select-wrapper {
    position: relative;
    max-width: 600px;
}

.institucion-select-wrapper select {
    width: 100%;
    padding: 18px 20px;
    border: none;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 600;
    background: white;
    color: #333;
    cursor: pointer;
    transition: all 0.3s ease;
    appearance: none;
    min-height: 60px;
    line-height: 1.5;
}

.institucion-select-wrapper::after {
    content: "\f078";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #667eea;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.stat-card .stat-number {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-card .stat-label {
    font-size: 14px;
    opacity: 0.9;
}

.modulos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.modulo-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.modulo-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.modulo-card.activo::before {
    transform: scaleX(1);
}

.modulo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.modulo-card.activo {
    border-color: #667eea;
    background: linear-gradient(135deg, #f5f7ff 0%, #f0f4ff 100%);
}

.modulo-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.modulo-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.modulo-toggle {
    position: relative;
    width: 60px;
    height: 30px;
}

.modulo-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 30px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

input:checked + .toggle-slider:before {
    transform: translateX(30px);
}

.modulo-info h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.modulo-info .modulo-id {
    font-size: 12px;
    color: #999;
}

.modulo-descripcion {
    font-size: 14px;
    color: #666;
    line-height: 1.5;
}

.buscador-modulos {
    margin-bottom: 30px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
}

.buscador-modulos input {
    width: 100%;
    padding: 15px 20px 15px 50px;
    border: 2px solid #e8ecf1;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.buscador-modulos input:focus {
    outline: none;
    border-color: #667eea;
}

.search-icon {
    position: absolute;
    left: 35px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 18px;
}

.filtros-rapidos {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.filtro-btn {
    padding: 8px 16px;
    border: 2px solid #e8ecf1;
    background: white;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
    color: #666;
}

.filtro-btn:hover {
    border-color: #667eea;
    color: #667eea;
}

.filtro-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
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
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 10px;
    color: white;
    font-weight: 500;
    z-index: 10000;
    animation: slideIn 0.3s ease;
    display: none;
}

.toast-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.toast-error {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.no-results i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.acciones-masivas {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.btn-accion-masiva {
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-activar-todos {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-desactivar-todos {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
}

.btn-accion-masiva:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Alert de Configuración Faltante */
.config-alert-card {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-left: 5px solid #f59e0b;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 5px 20px rgba(245, 158, 11, 0.2);
    animation: slideInDown 0.5s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.config-alert-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.config-alert-icon {
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #f59e0b;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.config-alert-title {
    flex: 1;
}

.config-alert-title h3 {
    margin: 0 0 5px 0;
    color: #92400e;
    font-size: 20px;
    font-weight: 700;
}

.config-alert-title p {
    margin: 0;
    color: #78350f;
    font-size: 14px;
}

.config-alert-body {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.config-missing-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
}

.config-missing-list li {
    padding: 12px;
    margin-bottom: 10px;
    background: #fef3c7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #92400e;
    font-weight: 600;
}

.config-missing-list li i {
    color: #f59e0b;
    font-size: 18px;
}

.btn-crear-registros {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border: none;
    color: white;
    padding: 14px 30px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
}

.btn-crear-registros:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
}

.success-alert {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-left: 5px solid #10b981;
    color: #065f46;
}

.success-alert .config-alert-icon {
    color: #10b981;
}

/* Estilos para Tabs */
.nav-tabs .nav-link {
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: #667eea !important;
    border-bottom-color: #667eea !important;
}

.nav-tabs .nav-link.active {
    color: #667eea !important;
    border-bottom: 3px solid #667eea !important;
    font-weight: 700 !important;
}

/* Estilos para formulario */
.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
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
            <div class="page-content instituciones-moderno-container">
                
                <!-- Header con breadcrumb -->
                <div class="page-bar">
                    <div class="page-title-breadcrumb">
                        <div class="pull-left">
                            <div class="page-title">
                                <i class="fas fa-building"></i> Gestión de Módulos por Institución
                            </div>
                        </div>
                        <ol class="breadcrumb page-breadcrumb pull-right">
                            <li><a class="parent-item" href="dev-instituciones.php">Instituciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                            <li class="active">Gestión de Módulos</li>
                        </ol>
                    </div>
                </div>

                <!-- Selector de Institución -->
                <div class="selector-institucion-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 style="margin-top: 0; margin-bottom: 20px;">
                                <i class="fas fa-school"></i> Seleccionar Institución
                            </h3>
                            <div class="institucion-select-wrapper">
                                <select id="selectorInstitucion" class="form-control">
                                    <?php while ($inst = mysqli_fetch_array($consultaInstituciones, MYSQLI_BOTH)) { ?>
                                        <option value="<?= $inst['ins_id']; ?>" 
                                            <?= ($inst['ins_id'] == $datosInstitucion['ins_id']) ? 'selected' : ''; ?>
                                            data-estado="<?= $inst['ins_estado']; ?>">
                                            <?= $inst['ins_siglas']; ?> - <?= $inst['ins_nombre']; ?> 
                                            <?= ($inst['ins_estado'] == 0) ? '(Inactiva)' : ''; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="stats-grid" style="grid-template-columns: 1fr;">
                                <div class="stat-card">
                                    <div class="stat-number" id="statModulosActivos"><?= $totalModulosAsignados; ?></div>
                                    <div class="stat-label">Módulos Activos</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number" id="statModulosDisponibles"><?= $totalModulos; ?></div>
                                    <div class="stat-label">Total Disponibles</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end;">
                            <a href="auto-login-dev.php?user=<?=base64_encode(1);?>&idInstitucion=<?=base64_encode($datosInstitucion['ins_id']);?>&bd=<?=base64_encode($datosInstitucion['ins_bd']);?>&yearDefault=<?=base64_encode($yearActual);?>" 
                               class="btn btn-sm" 
                               style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;"
                               title="Iniciar sesión automáticamente en esta institución">
                                <i class="fas fa-sign-in-alt"></i> Autologin
                            </a>
                            <button 
                               onclick="enviarCorreoBienvenida()" 
                               class="btn btn-sm" 
                               style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;"
                               title="Enviar correo de bienvenida al contacto principal">
                                <i class="fas fa-envelope"></i> Enviar Bienvenida
                            </button>
                            <a href="configuracion-sistema.php?year=<?=base64_encode($yearActual)?>&id=<?=base64_encode($datosInstitucion['ins_id'])?>" 
                               class="btn btn-sm" 
                               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;"
                               title="Editar configuración del sistema">
                                <i class="fas fa-cog"></i> Config. Sistema
                            </a>
                            <a href="configuracion-institucion.php" 
                               class="btn btn-sm" 
                               style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;"
                               title="Editar información institucional">
                                <i class="fas fa-building"></i> Config. Institución
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Alert de Registros Faltantes -->
                <?php if (!$tieneGeneralInfo || !$tieneConfiguracion): ?>
                <div class="config-alert-card" id="configAlertCard">
                    <div class="config-alert-header">
                        <div class="config-alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="config-alert-title">
                            <h3>⚠️ Configuración Incompleta Detectada</h3>
                            <p>Esta institución necesita registros de configuración para el año <?=$yearActual?></p>
                        </div>
                    </div>
                    
                    <div class="config-alert-body">
                        <p style="margin-bottom: 15px; color: #92400e; font-weight: 600;">
                            Registros faltantes para operar correctamente:
                        </p>
                        <ul class="config-missing-list">
                            <?php if (!$tieneGeneralInfo): ?>
                            <li>
                                <i class="fas fa-database"></i>
                                <span><strong>general_informacion</strong> - Información general de la institución (logo, datos legales, equipo directivo)</span>
                            </li>
                            <?php endif; ?>
                            
                            <?php if (!$tieneConfiguracion): ?>
                            <li>
                                <i class="fas fa-cog"></i>
                                <span><strong>configuracion</strong> - Configuración del sistema académico (notas, periodos, permisos)</span>
                            </li>
                            <?php endif; ?>
                        </ul>
                        
                        <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <p style="margin: 0; color: #78350f; font-size: 14px;">
                                <i class="fas fa-info-circle"></i>
                                <strong>¿Por qué es necesario?</strong> Sin estos registros, las páginas de configuración 
                                (Configuración del Sistema, Configuración de la Institución) no funcionarán correctamente y mostrarán errores.
                            </p>
                        </div>
                        
                        <button class="btn-crear-registros" onclick="crearRegistrosConfiguracion()">
                            <i class="fas fa-magic"></i>
                            Crear Registros Automáticamente
                        </button>
                        
                        <small style="display: block; margin-top: 15px; color: #78350f;">
                            <i class="fas fa-shield-alt"></i>
                            Esta acción es segura y creará los registros con valores predeterminados óptimos.
                        </small>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Sistema de Tabs -->
                <div class="card card-topline-purple" style="border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <div class="card-body" style="padding: 0;">
                        <!-- Nav Tabs -->
                        <ul class="nav nav-tabs" role="tablist" style="border-bottom: 2px solid #e8ecf1; padding: 20px 20px 0 20px; margin: 0;">
                            <li class="nav-item" style="margin-right: 10px;">
                                <a class="nav-link active" data-toggle="tab" href="#tab_modulos" role="tab" 
                                   style="padding: 12px 24px; border: none; border-bottom: 3px solid transparent; background: transparent; color: #666; font-weight: 600; transition: all 0.3s;">
                                    <i class="fas fa-puzzle-piece"></i> Módulos
                                </a>
                            </li>
                            <li class="nav-item" style="margin-right: 10px;">
                                <a class="nav-link" data-toggle="tab" href="#tab_datos" role="tab" 
                                   style="padding: 12px 24px; border: none; border-bottom: 3px solid transparent; background: transparent; color: #666; font-weight: 600; transition: all 0.3s;">
                                    <i class="fas fa-edit"></i> Datos de la Institución
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_estadisticas" role="tab" onclick="cargarEstadisticas()"
                                   style="padding: 12px 24px; border: none; border-bottom: 3px solid transparent; background: transparent; color: #666; font-weight: 600; transition: all 0.3s;">
                                    <i class="fas fa-chart-bar"></i> Estadísticas
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" style="padding: 30px;">
                            <!-- TAB 1: MÓDULOS -->
                            <div class="tab-pane fade show active" id="tab_modulos" role="tabpanel">
                                <!-- Buscador y Filtros -->
                                <div class="buscador-modulos">
                    <div style="position: relative;">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="buscarModulo" placeholder="Buscar módulos por nombre o ID..." autocomplete="off">
                    </div>
                    
                    <div class="filtros-rapidos">
                        <button class="filtro-btn active" data-filtro="todos">
                            <i class="fas fa-list"></i> Todos
                        </button>
                        <button class="filtro-btn" data-filtro="activos">
                            <i class="fas fa-check-circle"></i> Activos
                        </button>
                        <button class="filtro-btn" data-filtro="inactivos">
                            <i class="fas fa-times-circle"></i> Inactivos
                        </button>
                    </div>
                </div>

                <!-- Acciones Masivas -->
                <div class="acciones-masivas">
                    <button class="btn-accion-masiva btn-activar-todos" onclick="activarTodosModulos()">
                        <i class="fas fa-check-double"></i> Activar Todos
                    </button>
                    <button class="btn-accion-masiva btn-desactivar-todos" onclick="desactivarTodosModulos()">
                        <i class="fas fa-ban"></i> Desactivar Todos
                    </button>
                </div>

                <!-- Grid de Módulos -->
                <div class="modulos-grid" id="modulosGrid">
                    <?php
                    // Reset del puntero de la consulta
                    mysqli_data_seek($consultaModulos, 0);
                    
                    while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
                        $estaActivo = in_array($modulo['mod_id'], $modulosInstitucion);
                        $claseActivo = $estaActivo ? 'activo' : '';
                    ?>
                        <div class="modulo-card <?= $claseActivo; ?>" data-modulo-id="<?= $modulo['mod_id']; ?>" data-estado="<?= $estaActivo ? 'activo' : 'inactivo'; ?>">
                            <div class="modulo-header">
                                <div class="modulo-icon">
                                    <i class="fas fa-puzzle-piece"></i>
                                </div>
                                <label class="modulo-toggle">
                                    <input type="checkbox" 
                                        class="toggle-modulo" 
                                        data-modulo-id="<?= $modulo['mod_id']; ?>"
                                        <?= $estaActivo ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <div class="modulo-info">
                                <h4><?= $modulo['mod_nombre']; ?></h4>
                                <span class="modulo-id">ID: <?= $modulo['mod_id']; ?></span>
                            </div>
                            <div class="modulo-descripcion">
                                <?= !empty($modulo['mod_descripcion']) ? $modulo['mod_descripcion'] : 'Sin descripción disponible'; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                                <div class="no-results" id="noResults" style="display: none;">
                                    <i class="fas fa-search"></i>
                                    <h3>No se encontraron módulos</h3>
                                    <p>Intenta con otros términos de búsqueda</p>
                                </div>
                            </div>
                            <!-- FIN TAB MÓDULOS -->

                            <!-- TAB 2: DATOS DE LA INSTITUCIÓN -->
                            <div class="tab-pane fade" id="tab_datos" role="tabpanel">
                                <form id="formDatosInstitucion" enctype="multipart/form-data">
                                    <input type="hidden" name="ins_id" id="ins_id" value="<?= $datosInstitucion['ins_id']; ?>">
                                    <input type="hidden" name="ins_contrato_actual" id="ins_contrato_actual" value="<?= htmlspecialchars($contratoActual); ?>">
                                    
                                    <!-- Información Básica -->
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 15px 20px; margin-bottom: 25px;">
                                        <h4 style="color: white; margin: 0; font-weight: 600;">
                                            <i class="fas fa-info-circle"></i> Información Básica
                                        </h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-building"></i> Nombre de la Institución *</label>
                                            <input type="text" class="form-control" name="ins_nombre" id="ins_nombre" value="<?= $datosInstitucion['ins_nombre']; ?>" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label"><i class="fas fa-tag"></i> Siglas *</label>
                                            <input type="text" class="form-control" name="ins_siglas" id="ins_siglas" value="<?= $datosInstitucion['ins_siglas']; ?>" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label"><i class="fas fa-id-card"></i> NIT</label>
                                            <input type="text" class="form-control" name="ins_nit" id="ins_nit" value="<?= $datosInstitucion['ins_nit']; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-calendar"></i> Fecha de Inicio</label>
                                            <?php
                                            // Extraer solo la fecha (YYYY-MM-DD) del datetime
                                            $fechaInicio = '';
                                            if (!empty($datosInstitucion['ins_fecha_inicio'])) {
                                                $fechaInicio = date('Y-m-d', strtotime($datosInstitucion['ins_fecha_inicio']));
                                            }
                                            ?>
                                            <input type="date" class="form-control" name="ins_fecha_inicio" id="ins_fecha_inicio" value="<?= $fechaInicio; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-calendar-alt"></i> Fecha de Renovación</label>
                                            <?php
                                            // Extraer solo la fecha (YYYY-MM-DD) del datetime
                                            $fechaRenovacion = '';
                                            if (!empty($datosInstitucion['ins_fecha_renovacion'])) {
                                                $fechaRenovacion = date('Y-m-d', strtotime($datosInstitucion['ins_fecha_renovacion']));
                                            }
                                            ?>
                                            <input type="date" class="form-control" name="ins_fecha_renovacion" id="ins_fecha_renovacion" value="<?= $fechaRenovacion; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-database"></i> Base de Datos</label>
                                            <input type="text" class="form-control" name="ins_bd" id="ins_bd" value="<?= $datosInstitucion['ins_bd']; ?>" readonly style="background-color: #f5f7fa;">
                                            <small class="text-muted">Este campo no se puede modificar</small>
                                        </div>
                                    </div>

                                    <!-- Información de Contacto -->
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 15px 20px; margin: 30px 0 25px 0;">
                                        <h4 style="color: white; margin: 0; font-weight: 600;">
                                            <i class="fas fa-address-book"></i> Información de Contacto
                                        </h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-user"></i> Contacto Principal *</label>
                                            <input type="text" class="form-control" name="ins_contacto_principal" id="ins_contacto_principal" value="<?= $datosInstitucion['ins_contacto_principal']; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-briefcase"></i> Cargo del Contacto</label>
                                            <input type="text" class="form-control" name="ins_cargo_contacto" id="ins_cargo_contacto" value="<?= $datosInstitucion['ins_cargo_contacto']; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-phone"></i> Teléfono Principal</label>
                                            <input type="tel" class="form-control" name="ins_telefono_principal" id="ins_telefono_principal" value="<?= $datosInstitucion['ins_telefono_principal']; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-mobile-alt"></i> Celular de Contacto</label>
                                            <input type="tel" class="form-control" name="ins_celular_contacto" id="ins_celular_contacto" value="<?= $datosInstitucion['ins_celular_contacto']; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Ciudad</label>
                                            <input type="text" class="form-control" name="ins_ciudad" id="ins_ciudad" value="<?= $datosInstitucion['ins_ciudad']; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-envelope"></i> Email de Contacto</label>
                                            <input type="email" class="form-control" name="ins_email_contacto" id="ins_email_contacto" value="<?= $datosInstitucion['ins_email_contacto']; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-at"></i> Email Institucional</label>
                                            <input type="email" class="form-control" name="ins_email_institucion" id="ins_email_institucion" value="<?= $datosInstitucion['ins_email_institucion']; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><i class="fas fa-link"></i> URL de Acceso</label>
                                            <input type="url" class="form-control" name="ins_url_acceso" id="ins_url_acceso" value="<?= $datosInstitucion['ins_url_acceso']; ?>" placeholder="https://ejemplo.sintia.com">
                                        </div>
                                    </div>

                                    <!-- Gestión de Contrato -->
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 15px 20px; margin: 30px 0 25px 0;">
                                        <h4 style="color: white; margin: 0; font-weight: 600;">
                                            <i class="fas fa-file-contract"></i> Contrato de la Institución
                                        </h4>
                                    </div>

                                    <div class="row align-items-stretch">
                                        <div class="col-md-6 mb-3">
                                            <?php if ($contratoExiste) { ?>
                                                <div class="border rounded p-3 h-100" style="background: #f8fafc;">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h5 class="mb-1" style="font-weight: 600; color: #1f2937;">Contrato vigente</h5>
                                                            <small class="text-muted">Última actualización: <?= !empty($contratoFechaActualizacion) ? $contratoFechaActualizacion : 'N/D'; ?></small>
                                                        </div>
                                                    </div>
                                                    <p class="mb-2" style="color: #4b5563; word-break: break-all;">
                                                        <strong>Archivo:</strong> <?= htmlspecialchars($contratoActual); ?><br>
                                                        <strong>Tamaño:</strong> <?= !empty($contratoTamano) ? $contratoTamano : 'N/D'; ?>
                                                    </p>
                                                    <div class="d-flex flex-wrap">
                                                        <a href="<?= $contratoRutaRelativa; ?>" target="_blank" class="btn btn-sm btn-primary mr-2 mb-2" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none;">
                                                            <i class="fas fa-download"></i> Ver / Descargar
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-secondary mb-2" id="btnEnviarContrato" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                                                            <i class="fas fa-paper-plane"></i> Enviar por correo
                                                        </button>
                                                    </div>
                                                    <small class="d-block mt-3 text-muted">Al adjuntar un nuevo archivo se reemplazará el contrato actual.</small>
                                                </div>
                                            <?php } else { ?>
                                                <div class="alert alert-info h-100" role="alert" style="display: flex; flex-direction: column; justify-content: center;">
                                                    <i class="fas fa-info-circle mb-2"></i>
                                                    <strong>No hay un contrato cargado para esta institución.</strong>
                                                    <span>Carga un archivo en el panel derecho para agregarlo.</span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-upload"></i> Adjuntar/Actualizar contrato</label>
                                            <input type="file" class="form-control" name="ins_contrato_archivo" id="ins_contrato_archivo" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <small class="text-muted d-block mt-2">
                                                Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Tamaño máximo 10 MB.<br>
                                                El archivo se almacenará en <code>files-general/contratos</code>.
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Configuración del Plan -->
                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 15px 20px; margin: 30px 0 25px 0;">
                                        <h4 style="color: white; margin: 0; font-weight: 600;">
                                            <i class="fas fa-cog"></i> Configuración y Plan
                                        </h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-certificate"></i> Plan</label>
                                            <select class="form-control" name="ins_id_plan" id="ins_id_plan">
                                                <option value="">Sin plan</option>
                                                <?php
                                                try{
                                                    $planesQuery = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".planes_sintia WHERE plns_tipo='".PLANES."' ORDER BY plns_nombre");
                                                    while ($plan = mysqli_fetch_array($planesQuery, MYSQLI_BOTH)) {
                                                        $selected = ($plan['plns_id'] == $datosInstitucion['ins_id_plan']) ? 'selected' : '';
                                                        echo '<option value="'.$plan['plns_id'].'" '.$selected.'>'.$plan['plns_nombre'].'</option>';
                                                    }
                                                } catch (Exception $e) {
                                                    echo "<!-- Error cargando planes -->";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-calendar-check"></i> Año por Defecto</label>
                                            <input type="number" class="form-control" name="ins_year_default" id="ins_year_default" value="<?= $datosInstitucion['ins_year_default']; ?>" min="2020" max="2050">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-toggle-on"></i> Estado</label>
                                            <select class="form-control" name="ins_estado" id="ins_estado">
                                                <option value="1" <?= ($datosInstitucion['ins_estado'] == 1) ? 'selected' : ''; ?>>Activa</option>
                                                <option value="0" <?= ($datosInstitucion['ins_estado'] == 0) ? 'selected' : ''; ?>>Inactiva</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-lock"></i> Bloqueada</label>
                                            <select class="form-control" name="ins_bloqueada" id="ins_bloqueada">
                                                <option value="0" <?= ($datosInstitucion['ins_bloqueada'] == 0) ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?= ($datosInstitucion['ins_bloqueada'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-building"></i> Tipo de Cuenta</label>
                                            <?php 
                                            $esInterna = ($datosInstitucion['is_internal_oderman'] == 1 || $datosInstitucion['is_internal_oderman'] == '1');
                                            if ($esInterna) { ?>
                                                <select class="form-control" name="is_internal_oderman" id="is_internal_oderman" disabled style="background-color: #fff3cd; border-color: #ffc107;">
                                                    <option value="1" selected>Cuenta Interna Oderman</option>
                                                </select>
                                                <input type="hidden" name="is_internal_oderman" value="1">
                                                <small class="text-warning d-block mt-2">
                                                    <i class="fas fa-lock"></i> Una vez marcada como cuenta interna, no puede cambiarse por seguridad.
                                                </small>
                                            <?php } else { ?>
                                                <select class="form-control" name="is_internal_oderman" id="is_internal_oderman">
                                                    <option value="0" <?= (!$esInterna) ? 'selected' : ''; ?>>Cliente Externo</option>
                                                    <option value="1">Cuenta Interna Oderman</option>
                                                </select>
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle"></i> Las cuentas internas son para uso de Oderman y no pueden revertirse.
                                                </small>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-calendar-plus"></i> Años Disponibles</label>
                                            <input type="text" class="form-control" name="ins_years" id="ins_years" value="<?= $datosInstitucion['ins_years']; ?>" placeholder="2023,2024,2025">
                                            <small class="text-muted">Separados por comas (ej: 2023,2024,2025)</small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><i class="fas fa-bullhorn"></i> ¿Cómo se enteró de nosotros?</label>
                                            <input type="text" class="form-control" name="ins_medio_info" id="ins_medio_info" value="<?= $datosInstitucion['ins_medio_info']; ?>" placeholder="Redes sociales, recomendación, búsqueda web, etc.">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="ins_notificaciones_acudientes" id="ins_notificaciones_acudientes" value="1" <?= ($datosInstitucion['ins_notificaciones_acudientes'] == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="ins_notificaciones_acudientes">
                                                    <i class="fas fa-bell"></i> Habilitar notificaciones a acudientes
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de Deuda (si aplica) -->
                                    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; padding: 15px 20px; margin: 30px 0 25px 0;">
                                        <h4 style="color: white; margin: 0; font-weight: 600;">
                                            <i class="fas fa-dollar-sign"></i> Información de Deuda
                                        </h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-exclamation-triangle"></i> ¿Tiene Deuda?</label>
                                            <select class="form-control" name="ins_deuda" id="ins_deuda">
                                                <option value="0" <?= ($datosInstitucion['ins_deuda'] == 0) ? 'selected' : ''; ?>>No</option>
                                                <option value="1" <?= ($datosInstitucion['ins_deuda'] == 1) ? 'selected' : ''; ?>>Sí</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-money-bill-wave"></i> Valor de la Deuda</label>
                                            <input type="number" class="form-control" name="ins_valor_deuda" id="ins_valor_deuda" value="<?= $datosInstitucion['ins_valor_deuda']; ?>" step="0.01" placeholder="0.00">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label"><i class="fas fa-file-invoice-dollar"></i> Concepto de la Deuda</label>
                                            <input type="text" class="form-control" name="ins_concepto_deuda" id="ins_concepto_deuda" value="<?= $datosInstitucion['ins_concepto_deuda']; ?>" placeholder="Descripción del concepto">
                                        </div>
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div class="row mt-4">
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="btn btn-secondary" onclick="resetFormulario()">
                                                <i class="fas fa-undo"></i> Restablecer
                                            </button>
                                            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 12px 30px;">
                                                <i class="fas fa-save"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- FIN TAB DATOS -->

                            <!-- TAB 3: ESTADÍSTICAS -->
                            <div class="tab-pane fade" id="tab_estadisticas" role="tabpanel">
                                <!-- Loading -->
                                <div id="loading_estadisticas" class="text-center py-5" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-3">Cargando estadísticas...</p>
                                </div>

                                <!-- Contenedor de estadísticas -->
                                <div id="contenedor_estadisticas">
                                    <p class="text-center text-muted">Haz clic en "Cargar Estadísticas" para ver los datos</p>
                                </div>

                                <!-- Botón para recargar -->
                                <div class="text-center mt-4">
                                    <button class="btn btn-primary" onclick="cargarEstadisticas()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 12px 30px;">
                                        <i class="fas fa-sync-alt"></i> Recargar Estadísticas
                                    </button>
                                </div>
                            </div>
                            <!-- FIN TAB ESTADÍSTICAS -->
                        </div>
                    </div>
                </div>
                <!-- FIN SISTEMA DE TABS -->

            </div>
        </div>
    </div>
    
    <?php include("../compartido/footer.php"); ?>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Toast Notification -->
<div class="toast-notification" id="toastNotification"></div>

<!-- Modal Enviar Contrato -->
<div class="modal fade" id="modalEnviarContrato" tabindex="-1" role="dialog" aria-labelledby="modalEnviarContratoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="formEnviarContrato">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEnviarContratoLabel"><i class="fas fa-paper-plane"></i> Enviar contrato por correo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if ($contratoExiste) { ?>
                <div class="alert alert-light border mb-3" role="alert" style="background: #f9fafb; color: #1f2937;">
                    <i class="fas fa-file-contract"></i> Archivo a enviar: <strong><?= htmlspecialchars($contratoActual); ?></strong>
                </div>
                <?php } ?>
                <div class="form-group">
                    <label for="nombreDestinatarioContrato"><i class="fas fa-user"></i> Nombre del destinatario <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nombreDestinatarioContrato" name="nombreDestinatarioContrato" placeholder="Ej: Juan Pérez" required>
                    <small class="form-text text-muted">Este nombre aparecerá en el saludo del correo.</small>
                </div>
                <div class="form-group mb-0">
                    <label for="correoContrato"><i class="fas fa-envelope"></i> Correo destinatario <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="correoContrato" name="correoContrato" placeholder="correo@ejemplo.com" required>
                    <small class="form-text text-muted">El contrato se enviará como adjunto al correo especificado.</small>
                    <small class="text-danger d-none" id="errorCorreoContrato"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-paper-plane"></i> Enviar contrato
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts esenciales (igual que en otras páginas) -->
<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../config-general/assets/plugins/popper/popper.js"></script>
<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
<!-- bootstrap -->
<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- Common js-->
<script src="../../config-general/assets/js/app.js"></script>
<script src="../../config-general/assets/js/layout.js"></script>
<script src="../../config-general/assets/js/theme-color.js"></script>
<!-- select2 -->
<script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
<script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>

<!-- Scripts adicionales solo para esta página -->
<script>
// Configuración global
const INSTITUCION_ACTUAL = <?= $datosInstitucion['ins_id']; ?>;
const YEAR_ACTUAL = '<?= $yearActual; ?>';
const CONTRATO_DISPONIBLE = <?= $contratoExiste ? 'true' : 'false'; ?>;
const CONTRATO_NOMBRE = '<?= htmlspecialchars($contratoActual, ENT_QUOTES); ?>';

// Función para crear registros de configuración faltantes
function crearRegistrosConfiguracion() {
    if (!confirm('¿Estás seguro de crear los registros de configuración automáticamente?\n\nEsto creará:\n- general_informacion (si falta)\n- configuracion (si falta)\n\nCon valores predeterminados óptimos para el año <?=$yearActual?>.')) {
        return;
    }
    
    // Mostrar loading
    document.getElementById('loadingOverlay').classList.add('active');
    
    fetch('dev-instituciones-crear-registros-config.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            institucionId: INSTITUCION_ACTUAL,
            year: YEAR_ACTUAL
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingOverlay').classList.remove('active');
        
        if (data.success) {
            // Mostrar mensaje de éxito
            const alertCard = document.getElementById('configAlertCard');
            alertCard.classList.remove('config-alert-card');
            alertCard.classList.add('config-alert-card', 'success-alert');
            alertCard.innerHTML = `
                <div class="config-alert-header">
                    <div class="config-alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="config-alert-title">
                        <h3>✅ Registros Creados Exitosamente</h3>
                        <p>La configuración se ha completado correctamente</p>
                    </div>
                </div>
                <div class="config-alert-body">
                    <p style="color: #065f46; margin-bottom: 15px;">
                        <strong>Se crearon:</strong>
                    </p>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        ${data.creados.general_informacion ? '<li style="color: #065f46; margin-bottom: 8px;"><i class="fas fa-check"></i> general_informacion</li>' : ''}
                        ${data.creados.configuracion ? '<li style="color: #065f46; margin-bottom: 8px;"><i class="fas fa-check"></i> configuracion</li>' : ''}
                    </ul>
                    <p style="margin-top: 20px; color: #065f46;">
                        <i class="fas fa-info-circle"></i>
                        Ahora puedes acceder a las páginas de configuración sin problemas.
                    </p>
                    <button onclick="location.reload()" class="btn-crear-registros" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); margin-top: 15px;">
                        <i class="fas fa-sync"></i>
                        Recargar Página
                    </button>
                </div>
            `;
        } else {
            alert('Error al crear registros: ' + data.message);
        }
    })
    .catch(error => {
        document.getElementById('loadingOverlay').classList.remove('active');
        alert('Error de conexión: ' + error);
        console.error('Error:', error);
    });
}

// Función para enviar correo de bienvenida
function enviarCorreoBienvenida() {
    if (!confirm('¿Deseas enviar el correo de bienvenida al contacto principal de esta institución?\n\n' + 
                 'Institución ID: <?= $datosInstitucion['ins_id']; ?>\n' +
                 'Año: <?= $yearActual; ?>')) {
        return;
    }
    
    const institucionId = <?= $datosInstitucion['ins_id']; ?>;
    const year = '<?= $yearActual; ?>';
    
    // Mostrar loading usando la función existente
    mostrarLoading(true);
    
    fetch('ajax-enviar-correo-bienvenida.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            institucionId: institucionId,
            year: year
        })
    })
    .then(response => response.json())
    .then(data => {
        mostrarLoading(false);
        
        if (data.success) {
            mostrarNotificacion(data.message, 'success');
        } else {
            mostrarNotificacion(data.message || 'Error al enviar el correo', 'error');
        }
    })
    .catch(error => {
        mostrarLoading(false);
        mostrarNotificacion('Error de conexión: ' + error, 'error');
        console.error('Error:', error);
    });
}

// Función para resetear el formulario
function resetFormulario() {
    if (confirm('¿Estás seguro de que deseas restablecer el formulario a los valores originales?')) {
        document.getElementById('formDatosInstitucion').reset();
        // Recargar la página para obtener los valores originales
        location.reload();
    }
}

// Función para cargar estadísticas
function cargarEstadisticas() {
    $('#loading_estadisticas').show();
    $('#contenedor_estadisticas').html('');
    
    $.ajax({
        url: 'ajax-instituciones-estadisticas.php',
        type: 'POST',
        dataType: 'json',
        data: {
            institucion_id: INSTITUCION_ACTUAL,
            year: YEAR_ACTUAL
        },
        success: function(response) {
            $('#loading_estadisticas').hide();
            
            if (response.success) {
                renderizarEstadisticas(response.estadisticas);
            } else {
                $('#contenedor_estadisticas').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${response.message}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            $('#loading_estadisticas').hide();
            console.error('Error:', error);
            $('#contenedor_estadisticas').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Error de conexión al cargar estadísticas
                </div>
            `);
        }
    });
}

function renderizarEstadisticas(stats) {
    const html = `
        <!-- Estudiantes -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 15px 20px; margin-bottom: 25px;">
            <h4 style="color: white; margin: 0; font-weight: 600;">
                <i class="fas fa-user-graduate"></i> Estudiantes
            </h4>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.matriculados}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Matriculados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-user-check" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.asistentes}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Asistentes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
                    <i class="fas fa-user-slash" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.cancelados}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Cancelados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);">
                    <i class="fas fa-user-clock" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.en_inscripcion}</div>
                    <div style="font-size: 14px; opacity: 0.9;">En Inscripción</div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-4">
                <div style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);">
                    <i class="fas fa-user-times" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.no_matriculados}</div>
                    <div style="font-size: 14px; opacity: 0.9;">No Matriculados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
                    <i class="fas fa-trash-alt" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.eliminados}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Eliminados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                    <i class="fas fa-users" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div style="font-size: 36px; font-weight: 700;">${stats.estudiantes.total}</div>
                    <div style="font-size: 14px; opacity: 0.9;">Total Activos</div>
                </div>
            </div>
        </div>

        <!-- Usuarios -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 10px; padding: 15px 20px; margin-bottom: 25px;">
            <h4 style="color: white; margin: 0; font-weight: 600;">
                <i class="fas fa-users-cog"></i> Usuarios del Sistema
            </h4>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-3">
                <div style="background: white; border: 2px solid #10b981; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <i class="fas fa-chalkboard-teacher" style="font-size: 32px; margin-bottom: 10px; color: #10b981;"></i>
                    <div style="font-size: 36px; font-weight: 700; color: #10b981;">${stats.usuarios.docentes}</div>
                    <div style="font-size: 14px; color: #6b7280;">Docentes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: white; border: 2px solid #667eea; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <i class="fas fa-user-tie" style="font-size: 32px; margin-bottom: 10px; color: #667eea;"></i>
                    <div style="font-size: 36px; font-weight: 700; color: #667eea;">${stats.usuarios.directivos}</div>
                    <div style="font-size: 14px; color: #6b7280;">Directivos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: white; border: 2px solid #f59e0b; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <i class="fas fa-user-friends" style="font-size: 32px; margin-bottom: 10px; color: #f59e0b;"></i>
                    <div style="font-size: 36px; font-weight: 700; color: #f59e0b;">${stats.usuarios.acudientes}</div>
                    <div style="font-size: 14px; color: #6b7280;">Acudientes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: white; border: 2px solid #8b5cf6; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <i class="fas fa-user-graduate" style="font-size: 32px; margin-bottom: 10px; color: #8b5cf6;"></i>
                    <div style="font-size: 36px; font-weight: 700; color: #8b5cf6;">${stats.usuarios.estudiantes}</div>
                    <div style="font-size: 14px; color: #6b7280;">Usuarios Estudiante</div>
                </div>
            </div>
        </div>

        <!-- Datos Académicos -->
        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 10px; padding: 15px 20px; margin-bottom: 25px;">
            <h4 style="color: white; margin: 0; font-weight: 600;">
                <i class="fas fa-graduation-cap"></i> Datos Académicos
            </h4>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div style="background: white; border-left: 4px solid #667eea; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-book" style="font-size: 24px; color: white;"></i>
                        </div>
                        <div>
                            <div style="font-size: 28px; font-weight: 700; color: #667eea;">${stats.otros.cursos}</div>
                            <div style="font-size: 14px; color: #6b7280;">Cursos/Grados</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: white; border-left: 4px solid #10b981; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-layer-group" style="font-size: 24px; color: white;"></i>
                        </div>
                        <div>
                            <div style="font-size: 28px; font-weight: 700; color: #10b981;">${stats.otros.grupos}</div>
                            <div style="font-size: 14px; color: #6b7280;">Grupos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background: white; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clipboard-list" style="font-size: 24px; color: white;"></i>
                        </div>
                        <div>
                            <div style="font-size: 28px; font-weight: 700; color: #f59e0b;">${stats.otros.cargas}</div>
                            <div style="font-size: 14px; color: #6b7280;">Cargas Activas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen Total -->
        <div style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); border-radius: 12px; padding: 25px; margin-top: 30px;">
            <h5 style="margin-bottom: 20px; color: #374151;">
                <i class="fas fa-chart-pie"></i> Resumen General
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div style="padding: 15px; background: white; border-radius: 8px; margin-bottom: 10px;">
                        <strong style="color: #374151;">Total Estudiantes Activos:</strong>
                        <span style="float: right; font-size: 20px; font-weight: 700; color: #667eea;">${stats.estudiantes.total}</span>
                    </div>
                    <div style="padding: 15px; background: white; border-radius: 8px;">
                        <strong style="color: #374151;">Total Usuarios del Sistema:</strong>
                        <span style="float: right; font-size: 20px; font-weight: 700; color: #10b981;">${stats.usuarios.total}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="padding: 15px; background: white; border-radius: 8px; margin-bottom: 10px;">
                        <strong style="color: #374151;">Estudiantes Eliminados:</strong>
                        <span style="float: right; font-size: 20px; font-weight: 700; color: #ef4444;">${stats.estudiantes.eliminados}</span>
                    </div>
                    <div style="padding: 15px; background: white; border-radius: 8px;">
                        <strong style="color: #374151;">Estructura Académica:</strong>
                        <span style="float: right; font-size: 16px; color: #6b7280;">${stats.otros.cursos} cursos, ${stats.otros.grupos} grupos</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#contenedor_estadisticas').html(html);
}

// Función auxiliar para validar correo
function validarEmailContrato(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Manejo del formulario de datos de institución
$(document).ready(function() {
    $('#formDatosInstitucion').on('submit', function(e) {
        e.preventDefault();
        
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            mostrarNotificacion('Por favor completa todos los campos requeridos', 'error');
            return false;
        }
        
        const formElement = this;
        const formData = new FormData(formElement);

        if (!formData.has('ins_notificaciones_acudientes')) {
            formData.append('ins_notificaciones_acudientes', 0);
        }
        
        // Verificar si hay un archivo de contrato para subir
        const archivoContrato = document.getElementById('ins_contrato_archivo');
        const tieneArchivoNuevo = archivoContrato && archivoContrato.files && archivoContrato.files.length > 0;
        
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Guardar datos básicos primero
        $.ajax({
            url: 'dev-instituciones-guardar-datos.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Si hay un archivo de contrato nuevo, subirlo
                    if (tieneArchivoNuevo) {
                        const contratoFormData = new FormData();
                        contratoFormData.append('ins_id', INSTITUCION_ACTUAL);
                        contratoFormData.append('ins_contrato_archivo', archivoContrato.files[0]);
                        
                        $.ajax({
                            url: 'dev-instituciones-guardar-contrato.php',
                            type: 'POST',
                            data: contratoFormData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(contratoResponse) {
                                document.getElementById('loadingOverlay').style.display = 'none';
                                if (contratoResponse.success) {
                                    mostrarNotificacion('✅ Datos y contrato actualizados correctamente', 'success');
                                } else {
                                    mostrarNotificacion('✅ Datos actualizados. ⚠️ Error al subir contrato: ' + contratoResponse.message, 'warning');
                                }
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr, status, error) {
                                document.getElementById('loadingOverlay').style.display = 'none';
                                mostrarNotificacion('✅ Datos actualizados. ⚠️ Error de conexión al subir contrato', 'warning');
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        });
                    } else {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        mostrarNotificacion(response.message || 'Datos actualizados correctamente', 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    let mensajeError = response.message || 'Error al actualizar los datos';
                    
                    if (response.error_detallado) {
                        mensajeError += '\n\nDetalle: ' + response.error_detallado;
                    }
                    
                    if (response.archivo && response.linea) {
                        mensajeError += '\n\nArchivo: ' + response.archivo + ' (línea ' + response.linea + ')';
                    }
                    
                    console.error('Error del servidor:', response);
                    mostrarNotificacion(mensajeError, 'error');
                }
            },
            error: function(xhr, status, error) {
                document.getElementById('loadingOverlay').style.display = 'none';
                console.error('Error AJAX completo:', {xhr: xhr, status: status, error: error});
                console.error('Respuesta del servidor:', xhr.responseText);
                
                let mensajeError = 'Error de conexión al guardar los datos';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        mensajeError = response.message;
                    }
                } catch (e) {
                    if (xhr.responseText) {
                        mensajeError += '\n\nRespuesta del servidor: ' + xhr.responseText.substring(0, 200);
                    }
                }
                
                mensajeError += '\n\nCódigo de estado HTTP: ' + xhr.status;
                mostrarNotificacion(mensajeError, 'error');
            }
        });
    });

    $('#btnEnviarContrato').on('click', function() {
        if (!CONTRATO_DISPONIBLE) {
            mostrarNotificacion('No hay un contrato cargado para esta institución.', 'error');
            return;
        }
        $('#nombreDestinatarioContrato').val('');
        $('#correoContrato').val('');
        $('#errorCorreoContrato').addClass('d-none').text('');
        $('#modalEnviarContrato').modal('show');
    });

    $('#modalEnviarContrato').on('hidden.bs.modal', function() {
        $('#nombreDestinatarioContrato').val('');
        $('#correoContrato').val('');
        $('#errorCorreoContrato').addClass('d-none').text('');
    });

    $('#formEnviarContrato').on('submit', function(e) {
        e.preventDefault();

        if (!CONTRATO_DISPONIBLE) {
            mostrarNotificacion('No hay un contrato cargado para esta institución.', 'error');
            return;
        }

        const nombreDestinatario = ($('#nombreDestinatarioContrato').val() || '').trim();
        const correo = ($('#correoContrato').val() || '').trim();
        const errorLabel = $('#errorCorreoContrato');

        if (nombreDestinatario === '') {
            errorLabel.text('Por favor ingresa el nombre del destinatario.').removeClass('d-none');
            return;
        }

        if (correo === '') {
            errorLabel.text('Por favor ingresa un correo destinatario.').removeClass('d-none');
            return;
        }

        if (!validarEmailContrato(correo)) {
            errorLabel.text('Ingresa un correo electrónico válido.').removeClass('d-none');
            return;
        }

        errorLabel.addClass('d-none').text('');
        mostrarLoading(true);

        const formData = new FormData();
        formData.append('ins_id', INSTITUCION_ACTUAL);
        formData.append('correo_destino', correo);
        formData.append('nombre_destinatario', nombreDestinatario);

        fetch('dev-instituciones-enviar-contrato.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            mostrarLoading(false);
            if (data.success) {
                $('#modalEnviarContrato').modal('hide');
                mostrarNotificacion(data.message || 'Contrato enviado correctamente', 'success');
            } else {
                mostrarNotificacion(data.message || 'No se pudo enviar el contrato', 'error');
                if (data.detalle) {
                    console.error('Detalle envío contrato:', data.detalle);
                }
            }
        })
        .catch(error => {
            mostrarLoading(false);
            mostrarNotificacion('Error de conexión al enviar el contrato', 'error');
            console.error('Error envío contrato:', error);
        });
    });
});
</script>

<script src="../js/instituciones-modulos-v2.js"></script>

</body>
</html>


