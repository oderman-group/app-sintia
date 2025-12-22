<?php
include("bd-conexion.php");
include("php-funciones.php");
require_once(ROOT_PATH."/main-app/class/Inscripciones.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_opciones_generales.php");

// Función helper para acceder de forma segura a $datos
function getDato($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

$id="";
if(!empty($_GET["id"])){ $id=base64_decode($_GET["id"]);}

if (md5($id) != $_GET['token']) {
    redireccionMal('respuestas-usuario.php', 4);
}

//ASPIRANTE
$aspiranteConsulta = "SELECT * FROM ".BD_ADMISIONES.".aspirantes WHERE asp_id = :id";
$aspirante = $pdoI->prepare($aspiranteConsulta);
$aspirante->bindParam(':id', $id, PDO::PARAM_INT);
$aspirante->execute();
$datosAspirante = $aspirante->fetch();

// Verificar que se encontró el aspirante
if (!$datosAspirante || empty($datosAspirante['asp_id'])) {
    redireccionMal('respuestas-usuario.php', 4);
}

$datosFecha = explode("-", $datosAspirante['asp_fecha'] ?? date('Y-m-d'));
$yearAspirante = !empty($datosFecha[0]) ? $datosFecha[0] : date("Y");
$yearConsultar = $config['conf_agno'];
if ($yearAspirante < date("Y")){
    $yearConsultar = $yearAspirante;
}

//Grados
$gradosConsulta = "SELECT * FROM ".BD_ACADEMICA.".academico_grados
WHERE gra_estado = 1 AND institucion= :idInstitucion AND year= :year";
$grados = $pdoI->prepare($gradosConsulta);
$grados->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$grados->bindParam(':year', $yearConsultar, PDO::PARAM_STR);
$grados->execute();
$num = $grados->rowCount();

//Estudiante
$estQuery = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_acudiente AND uss.institucion= :idInstitucion AND uss.year= :year
WHERE mat.mat_solicitud_inscripcion = :id AND mat.institucion= :idInstitucion AND mat.year= :year";
$est = $pdoI->prepare($estQuery);
$est->bindParam(':id', $id, PDO::PARAM_INT);
$est->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$est->bindParam(':year', $yearConsultar, PDO::PARAM_STR);
$est->execute();
$num = $est->rowCount();
$datos = $est->fetch();

// Inicializar $datos como array vacío si no se encontró nada
if (!$datos) {
    $datos = [];
}

//Documentos - Solo traer documentos si existe mat_id
$matId = !empty($datos['mat_id']) ? (string)$datos['mat_id'] : '';
if (!empty($matId)) {
    $datosDocumentos = Inscripciones::traerDocumentos($pdoI, $config, $matId, $yearConsultar);
} else {
    $datosDocumentos = [];
}

//Padre
$padreQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :id AND institucion= :idInstitucion AND year= :year";
$padre = $pdoI->prepare($padreQuery);
$padreId = !empty($datos['mat_padre']) ? $datos['mat_padre'] : '';
$padre->bindParam(':id', $padreId, PDO::PARAM_STR);
$padre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$padre->bindParam(':year', $yearConsultar, PDO::PARAM_STR);
$padre->execute();
$datosPadre = $padre->fetch();
if (!$datosPadre) {
    $datosPadre = [];
}

//Madre
$madreQuery = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id = :id AND institucion= :idInstitucion AND year= :year";
$madre = $pdoI->prepare($madreQuery);
$madreId = !empty($datos['mat_madre']) ? $datos['mat_madre'] : '';
$madre->bindParam(':id', $madreId, PDO::PARAM_STR);
$madre->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
$madre->bindParam(':year', $yearConsultar, PDO::PARAM_STR);
$madre->execute();
$datosMadre = $madre->fetch();
if (!$datosMadre) {
    $datosMadre = [];
}

$discapacidades = [
    1 => 'Ninguna',
    2 => 'Fisica',
    3 => 'Auditiva',
    4 => 'Visual',
    5 => 'Sordoceguera',
    6 => 'Intelectual/Cognitiva',
    7 => 'Psicosocial (mental)',
    8 => 'Multiple',
    9 => 'Autismo (transtorno del espectro autista - TEA)',
    10 => 'Transtornos específicos de aprendizaje o del comportamiento',
    11 => 'Sordomudo',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include(ROOT_PATH."/config-general/analytics/instituciones.php");?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Admisión | <?= $datosInfo['info_nombre']; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../sintia-icono.png" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0 40px 0;
        }
        
        .wizard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header del wizard */
        .wizard-header {
            background: white;
            border-radius: 24px 24px 0 0;
            padding: 40px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .wizard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .wizard-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #2c3e50;
            margin: 0 0 12px 0;
            text-align: center;
        }
        
        .wizard-header p {
            font-size: 15px;
            color: #5f6368;
            margin: 0;
            text-align: center;
        }
        
        /* Foto del aspirante */
        .aspirante-foto {
            position: absolute;
            top: 30px;
            right: 30px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        
        /* Stepper */
        .wizard-stepper {
            background: white;
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .step {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .step-number {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .step.completed .step-number {
            background: #27ae60;
            color: white;
        }
        
        .step-label {
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
        }
        
        .step.active .step-label {
            color: #667eea;
        }
        
        .step-divider {
            flex: 0.5;
            height: 3px;
            background: #e9ecef;
            position: relative;
        }
        
        .step-divider.completed {
            background: #27ae60;
        }
        
        /* Contenido del formulario */
        .wizard-body {
            background: white;
            padding: 40px;
            border-radius: 0 0 24px 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .step-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #f1f3f4;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .step-title-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        
        .subsection-title {
            font-size: 18px;
            font-weight: 700;
            color: #495057;
            margin: 32px 0 20px 0;
            padding-left: 16px;
            border-left: 4px solid #667eea;
        }
        
        /* Form elements */
        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-group label .required {
            color: #d93025;
            margin-left: 4px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        select.form-control {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 12px;
            padding-right: 40px;
            height: 48px;
            line-height: 1.5;
        }
        
        /* Ocultar flechas nativas en IE */
        select.form-control::-ms-expand {
            display: none;
        }
        
        /* File inputs */
        .file-upload-box {
            border: 2px dashed #dadce0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
            cursor: pointer;
        }
        
        .file-upload-box:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .file-upload-box input[type="file"] {
            display: none;
        }
        
        .file-upload-label {
            cursor: pointer;
            color: #667eea;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        
        .file-upload-label i {
            font-size: 32px;
        }
        
        .file-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            margin-top: 8px;
        }
        
        .file-link:hover {
            background: #bbdefb;
            text-decoration: none;
        }
        
        /* Alerts */
        .alert-modern {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        
        .alert-modern.alert-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1565c0;
            border-left: 5px solid #2196F3;
        }
        
        .alert-modern.alert-warning {
            background: linear-gradient(135deg, #fff9e6 0%, #ffe8a1 100%);
            color: #856404;
            border-left: 5px solid #ffc107;
        }
        
        /* Checkbox de confirmación */
        .confirmation-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #2196F3;
            border-radius: 16px;
            padding: 24px;
            margin: 32px 0;
        }
        
        .confirmation-box .form-check {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .confirmation-box input[type="checkbox"] {
            width: 24px;
            height: 24px;
            cursor: pointer;
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .confirmation-box label {
            font-size: 15px;
            color: #1565c0;
            line-height: 1.6;
            margin: 0;
            cursor: pointer;
            font-weight: 500;
        }
        
        /* Botones de navegación */
        .wizard-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 16px;
        }
        
        .btn-wizard {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-wizard-prev {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-wizard-prev:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .btn-wizard-next,
        .btn-wizard-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        
        .btn-wizard-next:hover,
        .btn-wizard-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
            color: white;
        }
        
        .btn-wizard-submit {
            background: linear-gradient(135deg, #27ae60 0%, #20c997 100%);
            box-shadow: 0 4px 16px rgba(39, 174, 96, 0.4);
        }
        
        .btn-wizard-submit:hover {
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.5);
        }
        
        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-content {
            text-align: center;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .wizard-header {
                padding: 24px;
            }
            
            .wizard-stepper {
                padding: 20px;
                flex-direction: column;
                gap: 8px;
            }
            
            .step-divider {
                display: none;
            }
            
            .wizard-body {
                padding: 24px;
            }
            
            .wizard-navigation {
                flex-direction: column;
            }
            
            .btn-wizard {
                width: 100%;
                justify-content: center;
            }
            
            .aspirante-foto {
                position: static;
                margin: 0 auto 20px;
                display: block;
            }
        }
        
        .link {
            text-decoration: underline;
            color: #1976d2;
        }
        
        .link:hover {
            color: #0d47a1;
        }
    </style>
</head>

<body>
    <div class="wizard-container">
        <?php include("menu.php"); ?>
        <?php include("alertas.php"); ?>
        
        <form action="formulario-guardar.php" method="POST" enctype="multipart/form-data" id="formularioDatosAdmision">
            <input type="hidden" name="idMatricula" value="<?= $datos['mat_id'] ?? ''; ?>">
            <input type="hidden" name="solicitud" value="<?= $id; ?>">
            <input type="hidden" name="idAcudiente" value="<?= $datos['mat_acudiente'] ?? ''; ?>">
            <input type="hidden" name="idPadre" value="<?= $datos['mat_padre'] ?? ''; ?>">
            <input type="hidden" name="idMadre" value="<?= $datos['mat_madre'] ?? ''; ?>">
            <input type="hidden" name="idInst" value="<?=$_GET['idInst'] ?? '';?>">
            <input type="hidden" name="fotoA" value="<?= $datos['mat_foto'] ?? ''; ?>">
            
            <!-- Header -->
            <div class="wizard-header">
                <?php if (!empty($datos['mat_foto'] ?? '') and file_exists('files/fotos/' . ($datos['mat_foto'] ?? ''))): ?>
                    <img src="files/fotos/<?= $datos['mat_foto'] ?? ''; ?>" class="aspirante-foto" alt="Foto del aspirante">
                <?php endif; ?>
                
                <h1><i class="fas fa-clipboard-list"></i> Formulario Completo de Admisión</h1>
                <p>Complete todos los campos requeridos para continuar con el proceso</p>
            </div>
            
            <!-- Stepper -->
            <div class="wizard-stepper">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Información Personal</div>
                </div>
                <div class="step-divider" data-divider="1"></div>
                
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Información Familiar</div>
                </div>
                <div class="step-divider" data-divider="2"></div>
                
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Documentación Aspirante</div>
                </div>
                <div class="step-divider" data-divider="3"></div>
                
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-label">Documentación Acudiente</div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="wizard-body">
                
                <!-- PASO 1: Información Personal del Aspirante -->
                <div class="step-content active" id="step-1">
                    <div class="step-title">
                        <div class="step-title-icon"><i class="fas fa-user"></i></div>
                        <span>Información Personal del Aspirante</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombres <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombre" value="<?= $datos['mat_nombres'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primer Apellido <span class="required">*</span></label>
                                <input type="text" class="form-control" name="primerApellidos" value="<?= $datos['mat_primer_apellido'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Segundo Apellido</label>
                                <input type="text" class="form-control" name="segundoApellidos" value="<?= $datos['mat_segundo_apellido'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Género <span class="required">*</span></label>
                                <select class="form-control" name="genero" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="127" <?php if (($datos['mat_genero'] ?? '') == 127) echo "selected"; ?>>Femenino</option>
                                    <option value="126" <?php if (($datos['mat_genero'] ?? '') == 126) echo "selected"; ?>>Masculino</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo de documento <span class="required">*</span></label>
                                <select class="form-control" name="tipoDoc" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="105" <?php if (($datos['mat_tipo_documento'] ?? '') == 105) echo "selected"; ?>>Cédula de ciudadanía</option>
                                    <option value="106" <?php if (($datos['mat_tipo_documento'] ?? '') == 106) echo "selected"; ?>>NUIP</option>
                                    <option value="107" <?php if (($datos['mat_tipo_documento'] ?? '') == 107) echo "selected"; ?>>Tarjeta de identidad</option>
                                    <option value="108" <?php if (($datos['mat_tipo_documento'] ?? '') == 108) echo "selected"; ?>>Registro civil o NUIP</option>
                                    <option value="109" <?php if (($datos['mat_tipo_documento'] ?? '') == 109) echo "selected"; ?>>Cédula de Extranjería</option>
                                    <option value="110" <?php if (($datos['mat_tipo_documento'] ?? '') == 110) echo "selected"; ?>>Pasaporte</option>
                                    <option value="139" <?php if (($datos['mat_tipo_documento'] ?? '') == 139) echo "selected"; ?>>PEP</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de documento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="numeroDoc" value="<?= $datos['mat_documento'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lugar de expedición <span class="required">*</span></label>
                                <select class="form-control select2" name="LugarExp" required>
                                    <option value="">Seleccione una ciudad</option>
                                    <?php
                                    $ciudadesConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
                                    INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
                                    ORDER BY ciu_nombre ASC
                                    ");
                                    while($ciudad = mysqli_fetch_array($ciudadesConsulta, MYSQLI_BOTH)){
                                    ?>
                                    <option value="<?=$ciudad['ciu_id'];?>" <?php if($ciudad['ciu_id']==($datos['mat_lugar_expedicion'] ?? '')){echo "selected";}?>><?=$ciudad['ciu_nombre'].", ".$ciudad['dep_nombre'];?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lugar de nacimiento <span class="required">*</span></label>
                                <select class="form-control select2" name="LugarNacimiento" required>
                                    <option value="">Seleccione una ciudad</option>
                                    <?php
                                    $ciudadesConsulta2 = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
                                    INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
                                    ORDER BY ciu_nombre ASC
                                    ");
                                    while($ciudad2 = mysqli_fetch_array($ciudadesConsulta2, MYSQLI_BOTH)){
                                    ?>
                                    <option value="<?=$ciudad2['ciu_id'];?>" <?php if($ciudad2['ciu_id']==($datos['mat_lugar_nacimiento'] ?? '')){echo "selected";}?>><?=$ciudad2['ciu_nombre'].", ".$ciudad2['dep_nombre'];?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha de Nacimiento <span class="required">*</span></label>
                                <input type="date" class="form-control" name="fechaNacimiento" value="<?= $datos['mat_fecha_nacimiento'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Grupo étnico <span class="required">*</span></label>
                                <select class="form-control" name="grupoEtnico" required>
                                    <option value="1" <?php if (($datos['mat_etnia'] ?? '') == 1) echo "selected"; ?>>Ninguno</option>
                                    <option value="2" <?php if (($datos['mat_etnia'] ?? '') == 2) echo "selected"; ?>>Negro, mulato, afrocolombiano</option>
                                    <option value="3" <?php if (($datos['mat_etnia'] ?? '') == 3) echo "selected"; ?>>Raizal</option>
                                    <option value="4" <?php if (($datos['mat_etnia'] ?? '') == 4) echo "selected"; ?>>Indígena</option>
                                    <option value="5" <?php if (($datos['mat_etnia'] ?? '') == 5) echo "selected"; ?>>Rom (Gitano)</option>
                                    <option value="6" <?php if (($datos['mat_etnia'] ?? '') == 6) echo "selected"; ?>>Palenquero</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Limitación o discapacidad <span class="required">*</span></label>
                                <select class="form-control" name="discapacidad" required>
                                    <?php foreach ($discapacidades as $idDisc => $discapacidad): ?>
                                        <option value="<?= $idDisc; ?>" <?php if (($datos['mat_tiene_discapacidad'] ?? '') == $idDisc) echo "selected"; ?>><?= $discapacidad; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo de situación <span class="required">*</span></label>
                                <select class="form-control" name="tipoSituacion" required>
                                    <option value="1" <?php if (($datos['mat_tipo_situacion'] ?? '') == 1) echo "selected"; ?>>Ninguna</option>
                                    <option value="2" <?php if (($datos['mat_tipo_situacion'] ?? '') == 2) echo "selected"; ?>>Desplazado</option>
                                    <option value="3" <?php if (($datos['mat_tipo_situacion'] ?? '') == 3) echo "selected"; ?>>Desmovilizado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dirección <span class="required">*</span></label>
                                <input type="text" class="form-control" name="direccion" value="<?= $datos['mat_direccion'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Barrio <span class="required">*</span></label>
                                <input type="text" class="form-control" name="barrio" value="<?= $datos['mat_barrio'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Municipio <span class="required">*</span></label>
                                <select class="form-control select2" name="municipio" required>
                                    <option value="">Seleccione una ciudad</option>
                                    <?php
                                    $ciudadesConsulta3 = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".localidad_ciudades
                                    INNER JOIN ".$baseDatosServicios.".localidad_departamentos ON dep_id=ciu_departamento
                                    ORDER BY ciu_nombre ASC
                                    ");
                                    while($ciudad3 = mysqli_fetch_array($ciudadesConsulta3, MYSQLI_BOTH)){
                                    ?>
                                    <option value="<?=$ciudad3['ciu_id'];?>" <?php if($ciudad3['ciu_id']==($datos['mat_ciudad_actual'] ?? '')){echo "selected";}?>><?=$ciudad3['ciu_nombre'].", ".$ciudad3['dep_nombre'];?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Curso al que aspira <span class="required">*</span></label>
                                <select class="form-control" name="curso" required>
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    $grados->execute();
                                    while($datosGrado = $grados->fetch()){
                                    ?>
                                        <option value="<?= $datosGrado['gra_id']; ?>" <?php if (($datos['mat_grado'] ?? '') == $datosGrado['gra_id']) echo "selected"; ?>><?= $datosGrado['gra_nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Razón por la que desea ingresar <span class="required">*</span></label>
                                <input type="text" class="form-control" name="razonPlantel" value="<?= $datos['mat_razon_ingreso_plantel'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Colegio donde cursó su último año</label>
                                <input type="text" class="form-control" name="coleAnoAnterior" value="<?= $datos['mat_institucion_procedencia'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lugar</label>
                                <input type="text" class="form-control" name="lugar" value="<?= $datos['mat_lugar_colegio_procedencia'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Motivo de retiro del colegio anterior</label>
                        <input type="text" class="form-control" name="motivo" value="<?= $datos['mat_motivo_retiro_anterior'] ?? ''; ?>">
                    </div>
                </div>
                
                <!-- PASO 2: Información Familiar -->
                <div class="step-content" id="step-2">
                    <div class="step-title">
                        <div class="step-title-icon"><i class="fas fa-users"></i></div>
                        <span>Información Familiar</span>
                    </div>
                    
                    <div class="subsection-title">Información del Padre</div>
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Nombres y Apellidos del padre <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombrePadre" value="<?= $datosPadre['uss_nombre'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Documento <span class="required">*</span></label>
                                <select class="form-control" name="tipoDocumentoPadre" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="105" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 105) echo "selected"; ?>>Cédula de ciudadanía</option>
                                    <option value="106" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 106) echo "selected"; ?>>NUIP</option>
                                    <option value="107" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 107) echo "selected"; ?>>Tarjeta de identidad</option>
                                    <option value="108" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 108) echo "selected"; ?>>Registro civil o NUIP</option>
                                    <option value="109" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 109) echo "selected"; ?>>Cédula de Extranjería</option>
                                    <option value="110" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 110) echo "selected"; ?>>Pasaporte</option>
                                    <option value="139" <?php if (($datosPadre['uss_tipo_documento'] ?? '') == 139) echo "selected"; ?>>PEP</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de Documento <span class="required">*</span></label>
                                <input type="text" class="form-control" value="<?= $datosPadre['uss_usuario'] ?? ''; ?>" name="documentoPadre" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Religión <span class="required">*</span></label>
                                <input type="text" class="form-control" name="religionPadre" value="<?= $datosPadre['uss_religion'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" name="telfonoPadre" value="<?= $datosPadre['uss_telefono'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número celular <span class="required">*</span></label>
                                <input type="text" class="form-control" name="celularPadre" value="<?= $datosPadre['uss_celular']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dirección <span class="required">*</span></label>
                                <input type="text" class="form-control" name="direccionPadre" value="<?= $datosPadre['uss_direccion']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="emailPadre" value="<?= $datosPadre['uss_email']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Ocupación <span class="required">*</span></label>
                                <input type="text" class="form-control" name="ocupacionPadre" value="<?= $datosPadre['uss_ocupacion']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado civil <span class="required">*</span></label>
                                <select class="form-control" name="estadoCivilPadre" required>
                                    <option value="0">Seleccionar...</option>
                                    <?php
                                    $opcionesGenerales = BDT_OpcionesGenerales::Select(['ogen_grupo' => 8], '*', BD_ADMIN);
                                    while ($opcionesGeneralesEstadoCivil = $opcionesGenerales->fetch()) {
                                        $selected = ($datosPadre['uss_estado_civil'] == $opcionesGeneralesEstadoCivil['ogen_id']) ? "selected" : "";
                                        echo '<option value="'. $opcionesGeneralesEstadoCivil['ogen_id']. '" '.$selected.'>'. $opcionesGeneralesEstadoCivil['ogen_nombre']. '</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="subsection-title">Información de la Madre</div>
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Nombres y Apellidos de la Madre <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombreMadre" value="<?= $datosMadre['uss_nombre']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Documento <span class="required">*</span></label>
                                <select class="form-control" name="tipoDocumentoMadre" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="105" <?php if ($datosMadre['uss_tipo_documento'] == 105) echo "selected"; ?>>Cédula de ciudadanía</option>
                                    <option value="106" <?php if ($datosMadre['uss_tipo_documento'] == 106) echo "selected"; ?>>NUIP</option>
                                    <option value="107" <?php if ($datosMadre['uss_tipo_documento'] == 107) echo "selected"; ?>>Tarjeta de identidad</option>
                                    <option value="108" <?php if ($datosMadre['uss_tipo_documento'] == 108) echo "selected"; ?>>Registro civil o NUIP</option>
                                    <option value="109" <?php if ($datosMadre['uss_tipo_documento'] == 109) echo "selected"; ?>>Cédula de Extranjería</option>
                                    <option value="110" <?php if ($datosMadre['uss_tipo_documento'] == 110) echo "selected"; ?>>Pasaporte</option>
                                    <option value="139" <?php if ($datosMadre['uss_tipo_documento'] == 139) echo "selected"; ?>>PEP</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de Documento <span class="required">*</span></label>
                                <input type="text" class="form-control" value="<?= $datosMadre['uss_usuario']; ?>" name="documentoMadre" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Religión <span class="required">*</span></label>
                                <input type="text" class="form-control" name="religionMadre" value="<?= $datosMadre['uss_religion']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" value="<?= $datosMadre['uss_telefono']; ?>" name="telfonoMadre">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número celular <span class="required">*</span></label>
                                <input type="text" class="form-control" name="celularMadre" value="<?= $datosMadre['uss_celular']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Dirección <span class="required">*</span></label>
                                <input type="text" class="form-control" name="direccionMadre" value="<?= $datosMadre['uss_direccion']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="emailMadre" value="<?= $datosMadre['uss_email']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Ocupación <span class="required">*</span></label>
                                <input type="text" class="form-control" name="ocupacionMadre" value="<?= $datosMadre['uss_ocupacion']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Estado civil <span class="required">*</span></label>
                                <select class="form-control" name="estadoCivilMadre" required>
                                    <option value="0">Seleccionar...</option>
                                    <?php
                                    $opcionesGenerales = BDT_OpcionesGenerales::Select(['ogen_grupo' => 8], '*', BD_ADMIN);
                                    while ($opcionesGeneralesEstadoCivil = $opcionesGenerales->fetch()) {
                                        $selected = ($datosMadre['uss_estado_civil'] == $opcionesGeneralesEstadoCivil['ogen_id']) ? "selected" : "";
                                        echo '<option value="'. $opcionesGeneralesEstadoCivil['ogen_id']. '" '.$selected.'>'. $opcionesGeneralesEstadoCivil['ogen_nombre']. '</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="subsection-title">Información del Acudiente <span style="color:#d93025; font-size: 14px;">(El acudiente es quien se reportará en la DIAN)</span></div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Documento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="documentoAcudiente" value="<?= $datos['uss_usuario']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombres y Apellidos Completos <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombreAcudiente" value="<?= $datos['uss_nombre']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Parentesco <span class="required">*</span></label>
                                <input type="text" class="form-control" name="parentesco" value="<?= $datos['uss_parentezco']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Religión <span class="required">*</span></label>
                                <input type="text" class="form-control" name="religionAcudiente" value="<?= $datos['uss_religion']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" name="telfonoAcudiente" value="<?= $datos['uss_telefono']; ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número celular <span class="required">*</span></label>
                                <input type="text" class="form-control" name="celularAcudiente" value="<?= $datos['uss_celular']; ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Dirección <span class="required">*</span></label>
                                <input type="text" class="form-control" name="direccionAcudiente" value="<?= $datos['uss_direccion']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="emailAcudiente" value="<?= $datos['uss_email']; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PASO 3: Documentación del Aspirante -->
                <div class="step-content" id="step-3">
                    <div class="step-title">
                        <div class="step-title-icon"><i class="fas fa-folder-open"></i></div>
                        <span>Documentación del Aspirante</span>
                    </div>
                    
                    <div class="alert-modern alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Importante:</strong> Debe cargar solo un archivo por campo. Si necesita cargar más de un archivo, comprímalos (.ZIP, .RAR). Peso máximo: 5MB por archivo.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>1. Foto <span style="color: #1976d2;">(JPG, PNG, JPEG)</span></label>
                                <div class="file-upload-box">
                                    <input type="file" id="foto" name="foto" accept=".png, .jpg, .jpeg" onChange="validarPesoArchivo(this)">
                                    <label for="foto" class="file-upload-label">
                                        <i class="fas fa-camera"></i>
                                        <span>Click para subir foto</span>
                                    </label>
                                </div>
                                <?php if (!empty($datos['mat_foto'] ?? '') and file_exists('files/fotos/' . ($datos['mat_foto'] ?? ''))): ?>
                                    <a href="files/fotos/<?= $datos['mat_foto'] ?? ''; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datos['mat_foto'] ?? ''; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>2. Paz y salvo del colegio de procedencia</label>
                                <div class="file-upload-box">
                                    <input type="file" id="pazysalvo" name="pazysalvo" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="pazysalvoA" value="<?= $datosDocumentos['matd_pazysalvo']; ?>">
                                    <label for="pazysalvo" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_pazysalvo']) and file_exists('files/otros/' . $datosDocumentos['matd_pazysalvo'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_pazysalvo']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_pazysalvo']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>3. Ficha acumulativa u observador del alumno</label>
                                <div class="file-upload-box">
                                    <input type="file" id="observador" name="observador" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="observadorA" value="<?= $datosDocumentos['matd_observador']; ?>">
                                    <label for="observador" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_observador']) and file_exists('files/otros/' . $datosDocumentos['matd_observador'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_observador']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_observador']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>4. Fotocopia de la EPS</label>
                                <div class="file-upload-box">
                                    <input type="file" id="eps" name="eps" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="epsA" value="<?= $datosDocumentos['matd_eps']; ?>">
                                    <label for="eps" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_eps']) and file_exists('files/otros/' . $datosDocumentos['matd_eps'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_eps']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_eps']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>5. Hoja de recomendación</label>
                                <div class="file-upload-box">
                                    <input type="file" id="recomendacion" name="recomendacion" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="recomendacionA" value="<?= $datosDocumentos['matd_recomendacion']; ?>">
                                    <label for="recomendacion" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_recomendacion']) and file_exists('files/otros/' . $datosDocumentos['matd_recomendacion'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_recomendacion']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_recomendacion']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>6. Vacunas</label>
                                <div class="file-upload-box">
                                    <input type="file" id="vacunas" name="vacunas" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="vacunasA" value="<?= $datosDocumentos['matd_vacunas']; ?>">
                                    <label for="vacunas" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_vacunas']) and file_exists('files/otros/' . $datosDocumentos['matd_vacunas'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_vacunas']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_vacunas']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>7. Boletines actuales</label>
                                <div class="file-upload-box">
                                    <input type="file" id="boletines" name="boletines" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="boletinesA" value="<?= $datosDocumentos['matd_boletines_actuales']; ?>">
                                    <label for="boletines" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_boletines_actuales']) and file_exists('files/otros/' . $datosDocumentos['matd_boletines_actuales'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_boletines_actuales']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_boletines_actuales']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>8. Documento de identidad (Ambas caras)</label>
                                <div class="file-upload-box">
                                    <input type="file" id="documentoIde" name="documentoIde" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="documentoIdeA" value="<?= $datosDocumentos['matd_documento_identidad']; ?>">
                                    <label for="documentoIde" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_documento_identidad']) and file_exists('files/otros/' . $datosDocumentos['matd_documento_identidad'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_documento_identidad']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_documento_identidad']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>9. Certificado</label>
                                <div class="file-upload-box">
                                    <input type="file" id="certificado" name="certificado" onChange="validarPesoArchivo(this)">
                                    <input type="hidden" name="certificadoA" value="<?= $datosDocumentos['matd_certificados']; ?>">
                                    <label for="certificado" class="file-upload-label">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Click para subir documento</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_certificados']) and file_exists('files/otros/' . $datosDocumentos['matd_certificados'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_certificados']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_certificados']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PASO 4: Documentación del Acudiente y Confirmación -->
                <div class="step-content" id="step-4">
                    <div class="step-title">
                        <div class="step-title-icon"><i class="fas fa-file-signature"></i></div>
                        <span>Documentación del Acudiente y Confirmación</span>
                    </div>
                    
                    <div class="alert-modern alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Importante:</strong> Peso máximo por archivo: 5MB. Formatos aceptados: PDF, JPG, PNG, ZIP, RAR.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>1. Certificado laboral que incluya el salario actual</label>
                                <div class="file-upload-box">
                                    <input type="file" id="cartaLaboral" name="cartaLaboral" onChange="validarPesoArchivo(this)">
                                    <label for="cartaLaboral" class="file-upload-label">
                                        <i class="fas fa-briefcase"></i>
                                        <span>Click para subir certificado laboral</span>
                                    </label>
                                </div>
                                <?php if (!empty($datosDocumentos['matd_carta_laboral']) and file_exists('files/otros/' . $datosDocumentos['matd_carta_laboral'])): ?>
                                    <a href="files/otros/<?= $datosDocumentos['matd_carta_laboral']; ?>" target="_blank" class="file-link">
                                        <i class="fas fa-check-circle"></i> <?= $datosDocumentos['matd_carta_laboral']; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="confirmation-box">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck" required>
                            <label class="form-check-label" for="gridCheck">
                                Estoy suficientemente informado del Manual de Convivencia y del Sistema Institucional de Evaluación que rigen en el <b><?php if(!empty($datosInfo['info_nombre'])) echo strtoupper($datosInfo['info_nombre']);?></b>, según aparecen en la página web y en caso de ser aceptado me comprometo a acatarlos y cumplirlos fiel y cabalmente.
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert-modern alert-info">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Recuerde:</strong> Debe tener completa toda la documentación cargada en la plataforma para que su solicitud continúe el proceso de admisión y sea agendada la respectiva entrevista y examen.
                    </div>
                    
                    <?php if(!empty($config['cfgi_frase_formulario_inscripcion_2'])): ?>
                        <div class="alert-modern alert-warning">
                            <i class="fas fa-info-circle"></i>
                            <strong><?= $config['cfgi_frase_formulario_inscripcion_2']; ?></strong>
                        </div>
                    <?php endif; ?>
                    
                    <div id="result"></div>
                </div>
                
                <!-- Navegación entre pasos -->
                <div class="wizard-navigation">
                    <button type="button" class="btn-wizard btn-wizard-prev" id="prevBtn" onclick="cambiarPaso(-1)" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                        <span>Anterior</span>
                    </button>
                    
                    <div style="flex: 1;"></div>
                    
                    <button type="button" class="btn-wizard btn-wizard-next" id="nextBtn" onclick="cambiarPaso(1)">
                        <span>Siguiente</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                    
                    <button type="submit" class="btn-wizard btn-wizard-submit" id="submitBtn" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <span>Guardar y Enviar Formulario</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h4 style="color: #667eea; font-weight: 700;">Guardando información...</h4>
            <p style="color: #5f6368;">Por favor espera un momento</p>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        
        function mostrarPaso(paso) {
            // Ocultar todos los pasos
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Mostrar paso actual
            document.getElementById('step-' + paso).classList.add('active');
            
            // Actualizar stepper
            document.querySelectorAll('.step').forEach((step, index) => {
                if (index + 1 < paso) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (index + 1 === paso) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            });
            
            // Actualizar dividers
            document.querySelectorAll('.step-divider').forEach((divider, index) => {
                if (index + 1 < paso) {
                    divider.classList.add('completed');
                } else {
                    divider.classList.remove('completed');
                }
            });
            
            // Actualizar botones
            document.getElementById('prevBtn').style.display = paso === 1 ? 'none' : 'inline-flex';
            document.getElementById('nextBtn').style.display = paso === totalSteps ? 'none' : 'inline-flex';
            document.getElementById('submitBtn').style.display = paso === totalSteps ? 'inline-flex' : 'none';
            
            // Scroll al inicio
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        function cambiarPaso(direccion) {
            const nuevoPaso = currentStep + direccion;
            
            if (nuevoPaso >= 1 && nuevoPaso <= totalSteps) {
                // Validar campos requeridos del paso actual antes de avanzar
                if (direccion > 0) {
                    const camposActuales = document.querySelectorAll('#step-' + currentStep + ' input[required], #step-' + currentStep + ' select[required]');
                    let todosValidos = true;
                    
                    camposActuales.forEach(campo => {
                        if (!campo.value.trim()) {
                            campo.classList.add('is-invalid');
                            todosValidos = false;
                        } else {
                            campo.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!todosValidos) {
                        alert('Por favor completa todos los campos obligatorios antes de continuar.');
                        return;
                    }
                }
                
                currentStep = nuevoPaso;
                mostrarPaso(currentStep);
            }
        }
        
        function validarPesoArchivo(archivoInput) {
            const maxPeso = 5 * 1024 * 1024;
            const archivo = archivoInput.files[0];
            
            if (archivo) {
                const pesoArchivoMB = ((archivo.size / 1024) / 1024).toFixed(2);
                
                if (archivo.size > maxPeso) {
                    alert(`Este archivo pesa ${pesoArchivoMB} MB. Lo ideal es que pese menos de 5 MB. Intente comprimirlo o reducir su peso.`);
                    archivoInput.value = "";
                    return false;
                }
                
                // Mostrar nombre del archivo en el label
                const label = archivoInput.parentElement.querySelector('.file-upload-label span');
                if (label) {
                    label.innerHTML = '<i class="fas fa-check-circle"></i> ' + archivo.name;
                    archivoInput.parentElement.style.borderColor = '#27ae60';
                    archivoInput.parentElement.style.background = '#e8f5e9';
                }
            }
        }
        
        // Envío asíncrono
        document.getElementById('formularioDatosAdmision').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const form = document.getElementById('formularioDatosAdmision');
            const submitBtn = document.getElementById('submitBtn');
            const resultShow = document.getElementById('result');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Enviando...</span>';
            resultShow.innerHTML = '';
            loadingOverlay.style.display = 'flex';
            
            // Crear FormData
            const formData = new FormData(form);
            
            // Enviar por fetch
            fetch('formulario-guardar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                loadingOverlay.style.display = 'none';
                resultShow.innerHTML = result;
                
                if (result.includes('Los datos fueron guardados correctamente.')) {
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>¡Enviado Correctamente!</span>';
                    
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Guardar y Enviar Formulario</span>';
                }
            })
            .catch(error => {
                console.error('Error al enviar el formulario:', error);
                loadingOverlay.style.display = 'none';
                alert('Hubo un problema al enviar el formulario. Inténtalo de nuevo. Error: ' + error);
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> <span>Guardar y Enviar Formulario</span>';
            });
        });
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            mostrarPaso(1);
            
            // Validación en tiempo real
            const inputs = document.querySelectorAll('input[required], select[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });
        });
    </script>
</body>
</html>
