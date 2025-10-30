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

                <!-- Información de la Institución -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.05);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h4 style="margin: 0; color: #667eea;">
                                    <i class="fas fa-info-circle"></i> Información de la Institución
                                </h4>
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <a href="auto-login-dev.php?user=<?=base64_encode(1);?>&idInstitucion=<?=base64_encode($datosInstitucion['ins_id']);?>&bd=<?=base64_encode($datosInstitucion['ins_bd']);?>&yearDefault=<?=base64_encode($yearActual);?>" 
                                       class="btn btn-sm" 
                                       style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;"
                                       title="Iniciar sesión automáticamente en esta institución">
                                        <i class="fas fa-sign-in-alt"></i> Autologin
                                    </a>
                                    <button 
                                       onclick="enviarCorreoBienvenida()" 
                                       class="btn btn-sm" 
                                       style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;"
                                       title="Enviar correo de bienvenida al contacto principal">
                                        <i class="fas fa-envelope"></i> Enviar Bienvenida
                                    </button>
                                    <a href="configuracion-sistema.php?year=<?=base64_encode($yearActual)?>&id=<?=base64_encode($datosInstitucion['ins_id'])?>" 
                                       class="btn btn-sm" 
                                       style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;"
                                       title="Editar configuración del sistema">
                                        <i class="fas fa-cog"></i> Config. Sistema
                                    </a>
                                    <a href="configuracion-institucion.php" 
                                       class="btn btn-sm" 
                                       style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;"
                                       title="Editar información institucional">
                                        <i class="fas fa-building"></i> Config. Institución
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>ID:</strong> <span id="infoId"><?= $datosInstitucion['ins_id']; ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>NIT:</strong> <span id="infoNit"><?= $datosInstitucion['ins_nit']; ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>BD:</strong> <span id="infoBd"><?= $datosInstitucion['ins_bd']; ?></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Estado:</strong> 
                                    <span id="infoEstado" class="badge" style="background: <?= ($datosInstitucion['ins_estado'] == 1) ? '#38ef7d' : '#f45c43'; ?>;">
                                        <?= ($datosInstitucion['ins_estado'] == 1) ? 'Activa' : 'Inactiva'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($tieneGeneralInfo && $tieneConfiguracion): ?>
                            <div style="margin-top: 15px; padding: 12px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 8px; border-left: 4px solid #10b981;">
                                <span style="color: #065f46; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i>
                                    Configuración completa para el año <?=$yearActual?>
                                </span>
                            </div>
                            <?php endif; ?>
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
</script>

<script src="../js/instituciones-modulos-v2.js"></script>

</body>
</html>


