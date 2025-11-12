<?php
include("bd-conexion.php");

$gradosConsulta = "SELECT * FROM ".BD_ACADEMICA.".academico_grados
WHERE 
    gra_estado = 1 
AND gra_tipo='".GRADO_GRUPAL."' 
AND institucion={$config['conf_id_institucion']} 
AND year={$config["conf_agno"]}
";
$grados = $pdoI->prepare($gradosConsulta);
$grados->execute();
$num = $grados->rowCount();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Admisión | <?= $datosInfo['info_nombre']; ?></title>
    
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
            padding: 20px 0;
        }
        
        .admision-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Hero Section */
        .hero-section {
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .hero-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }
        
        .hero-icon i {
            font-size: 48px;
            color: white;
        }
        
        .hero-section h1 {
            font-size: 32px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 16px;
        }
        
        .hero-section .lead {
            font-size: 18px;
            color: #5f6368;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        
        .hero-costo {
            display: inline-block;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border: 2px solid #ffc107;
            border-radius: 16px;
            padding: 16px 32px;
            margin-top: 20px;
        }
        
        .hero-costo-label {
            font-size: 14px;
            color: #856404;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .hero-costo-valor {
            font-size: 28px;
            color: #664d03;
            font-weight: 800;
        }
        
        /* Banner */
        .banner-section {
            margin-bottom: 30px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        
        .banner-section img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Formulario Principal */
        .form-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            margin-bottom: 30px;
        }
        
        .form-section-title {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 30px;
            padding-bottom: 16px;
            border-bottom: 3px solid #f1f3f4;
        }
        
        .form-section-number {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            flex-shrink: 0;
        }
        
        .form-section-title h3 {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
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
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-control:invalid {
            border-color: #e9ecef;
        }
        
        .form-control.is-invalid {
            border-color: #d93025;
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
        
        /* Checkbox de políticas */
        .politicas-check {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #2196F3;
            border-radius: 16px;
            padding: 24px;
            margin: 30px 0;
        }
        
        .politicas-check .form-check {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .politicas-check input[type="checkbox"] {
            width: 24px;
            height: 24px;
            cursor: pointer;
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .politicas-check label {
            font-size: 15px;
            color: #1565c0;
            line-height: 1.6;
            margin: 0;
            cursor: pointer;
        }
        
        .politicas-link {
            color: #1976d2;
            text-decoration: underline;
            font-weight: 600;
        }
        
        .politicas-link:hover {
            color: #0d47a1;
        }
        
        /* Botón de envío */
        .submit-section {
            text-align: center;
            padding: 30px 0;
        }
        
        .btn-submit-admision {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 18px 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
            text-transform: none;
        }
        
        .btn-submit-admision:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
        }
        
        .btn-submit-admision i {
            font-size: 20px;
        }
        
        /* CTA de consulta */
        .cta-consulta {
            background: white;
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            margin-top: 30px;
        }
        
        .cta-consulta h3 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 16px;
        }
        
        .cta-consulta p {
            font-size: 16px;
            color: #5f6368;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        
        .btn-consultar {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 40px;
            background: white;
            color: #667eea;
            border: 3px solid #667eea;
            border-radius: 16px;
            font-size: 17px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-consultar:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
            text-decoration: none;
        }
        
        /* Progress indicator */
        .form-progress {
            display: flex;
            gap: 8px;
            margin-bottom: 32px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 16px;
        }
        
        .progress-step {
            flex: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }
        
        .progress-step.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .progress-step.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Alerts modernos */
        .alert-modern {
            border: none;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        
        .alert-modern-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .alert-info.alert-modern {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 5px solid #2196F3;
        }
        
        .alert-info.alert-modern .alert-modern-icon {
            background: #2196F3;
            color: white;
        }
        
        .alert-info.alert-modern .alert-content {
            color: #1565c0;
            font-size: 15px;
            line-height: 1.6;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 24px;
            }
            
            .hero-section h1 {
                font-size: 26px;
            }
            
            .form-card {
                padding: 24px;
            }
            
            .form-section-number {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            
            .form-section-title h3 {
                font-size: 18px;
            }
            
            .btn-submit-admision {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .hero-section {
            animation: fadeInUp 0.5s ease-out;
        }
        
        /* Validación visual */
        .form-control.is-valid {
            border-color: #27ae60;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2327ae60' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.6rem) center;
            background-size: calc(0.75em + 0.5rem) calc(0.75em + 0.5rem);
            padding-right: calc(1.5em + 1.2rem);
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
    </style>
</head>

<body>
    <div class="admision-container">
        <?php include("menu.php"); ?>
        
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>¡Bienvenido al Proceso de Admisión!</h1>
            <p class="lead">
                <?= $config['cfgi_texto_inicial']; ?>
            </p>
            <div class="hero-costo">
                <div class="hero-costo-label">Inversión del proceso</div>
                <div class="hero-costo-valor">$<?= number_format($valorInscripcion, 0, ".", "."); ?></div>
            </div>
        </div>
        
        <!-- Banner opcional -->
        <?php if($config['cfgi_mostrar_banner']==1 && !empty($config['cfgi_banner_inicial']) && file_exists('../files/imagenes-generales/'.$config['cfgi_banner_inicial'])): ?>
            <div class="banner-section">
                <img src="../files/imagenes-generales/<?= $config['cfgi_banner_inicial']; ?>" alt="Banner de admisión">
            </div>
        <?php endif; ?>
        
        <!-- Alertas -->
        <?php include("alertas.php"); ?>
        
        <!-- Formulario Principal -->
        <div class="form-card">
            <!-- Barra de progreso -->
            <div class="form-progress">
                <div class="progress-step active" id="progress-1"></div>
                <div class="progress-step" id="progress-2"></div>
                <div class="progress-step" id="progress-3"></div>
            </div>
            
            <form action="index-guardar.php" method="post" id="formAdmision">
                <input type="hidden" name="iditoken" value="<?= md5($_REQUEST['idInst']); ?>">
                <input type="hidden" name="idInst" value="<?= $_REQUEST['idInst']; ?>">
                
                <!-- Sección 1: Datos del Estudiante -->
                <div class="form-section" id="seccion-1">
                    <div class="form-section-title">
                        <div class="form-section-number">1</div>
                        <h3>Datos del Estudiante</h3>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo de documento <span class="required">*</span></label>
                                <select name="tipoDocumento" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="105">Cédula de ciudadanía</option>
                                    <option value="106">NUIP</option>
                                    <option value="107">Tarjeta de identidad</option>
                                    <option value="108">Registro civil</option>
                                    <option value="109">Cédula de extranjería</option>
                                    <option value="110">Pasaporte</option>
                                    <option value="139">PEP</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de documento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="documento" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primer apellido <span class="required">*</span></label>
                                <input type="text" class="form-control" name="apellido1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Segundo apellido</label>
                                <input type="text" class="form-control" name="apellido2">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primer nombre <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombreEstudiante" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Segundo nombre</label>
                                <input type="text" class="form-control" name="nombreEstudiante2">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grado al que aspira <span class="required">*</span></label>
                                <select name="grado" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <?php
                                    $grados->execute(); // Reejecutar query
                                    while ($datosGrado = $grados->fetch()) {
                                    ?>
                                        <option value="<?= $datosGrado['gra_id']; ?>"><?= $datosGrado['gra_nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>¿Hizo el proceso antes? <span class="required">*</span></label>
                                <select class="form-control" name="procesoAdmisionAntes" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="1">SÍ</option>
                                    <option value="2">NO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 2: Datos del Acudiente -->
                <div class="form-section mt-5" id="seccion-2">
                    <div class="form-section-title">
                        <div class="form-section-number">2</div>
                        <h3>Datos del Acudiente</h3>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo de documento <span class="required">*</span></label>
                                <select name="tipoDocumentoAcudiente" class="form-control" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="105">Cédula de ciudadanía</option>
                                    <option value="106">NUIP</option>
                                    <option value="107">Tarjeta de identidad</option>
                                    <option value="108">Registro civil</option>
                                    <option value="109">Cédula de extranjería</option>
                                    <option value="110">Pasaporte</option>
                                    <option value="139">PEP</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número de documento <span class="required">*</span></label>
                                <input type="text" class="form-control" name="documentoAcudiente" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primer apellido <span class="required">*</span></label>
                                <input type="text" class="form-control" name="apellido1Acudiente" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Segundo apellido</label>
                                <input type="text" class="form-control" name="apellido2Acudiente">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primer nombre <span class="required">*</span></label>
                                <input type="text" class="form-control" name="nombre1Acudiente" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Segundo nombre</label>
                                <input type="text" class="form-control" name="nombre2Acudiente">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Celular <span class="required">*</span></label>
                                <input type="tel" class="form-control" name="celular" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección 3: Políticas y Confirmación -->
                <div class="form-section mt-5" id="seccion-3">
                    <div class="form-section-title">
                        <div class="form-section-number">3</div>
                        <h3>Políticas y Confirmación</h3>
                    </div>
                    
                    <div class="politicas-check">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck" required>
                            <label class="form-check-label" for="gridCheck">
                                Autorizo el tratamiento de datos personales según la 
                                <?php
                                if(!empty($config['cfgi_politicas_adjunto']) && file_exists('../files/imagenes-generales/'.$config['cfgi_politicas_adjunto']) || $config['cfgi_mostrar_politicas'] == 2){
                                    switch($config['cfgi_mostrar_politicas']){
                                        case 1:
                                            $enlace='target="_blank" href="../files/imagenes-generales/'.$config['cfgi_politicas_adjunto'].'"';
                                        break;
                                        case 2:
                                            $enlace='href="javascript:void(0);" onClick="mostrarPoliticas()"';
                                        break;
                                        default:
                                            $enlace='target="_blank" href="../files/imagenes-generales/'.$config['cfgi_politicas_adjunto'].'"';
                                        break;
                                    }
                                ?>
                                    <a <?= $enlace; ?> class="politicas-link">política de tratamiento de datos</a>.
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-info alert-modern">
                        <div class="alert-modern-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>Información importante:</strong><br>
                            El formulario de inscripción tiene una inversión de <strong>$<?= number_format($valorInscripcion, 0, ".", "."); ?></strong>. 
                            Una vez enviada su solicitud, recibirá un correo electrónico con los detalles y los siguientes pasos del proceso.
                        </div>
                    </div>
                </div>
                
                <!-- Botón de envío -->
                <div class="submit-section">
                    <button type="submit" class="btn-submit-admision">
                        <i class="fas fa-paper-plane"></i>
                        <span>Enviar Solicitud de Admisión</span>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- CTA de consulta -->
        <div class="cta-consulta">
            <i class="fas fa-search" style="font-size: 48px; color: #667eea; margin-bottom: 24px;"></i>
            <h3>¿Ya realizaste tu solicitud?</h3>
            <p>Si ya completaste el proceso de registro anteriormente, puedes consultar el estado de tu solicitud haciendo clic en el botón de abajo.</p>
            <a class="btn-consultar" href="consultar-estado.php?idInst=<?= $_REQUEST['idInst']; ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Consultar Estado de Solicitud</span>
            </a>
        </div>
    </div>
    
    <!-- Modal de Políticas -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title" style="font-weight: 700;">
                        <i class="fas fa-shield-alt"></i> Política de Tratamiento de Datos
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 30px; font-size: 15px; line-height: 1.8; color: #2c3e50;">
                    <?= $config['cfgi_politicas_texto']; ?>
                </div>
                <div class="modal-footer" style="border-top: 2px solid #f1f3f4;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 10px 24px;">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h4 style="color: #667eea; font-weight: 700;">Enviando solicitud...</h4>
            <p style="color: #5f6368;">Por favor espera un momento</p>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        function mostrarPoliticas(){
            $("#exampleModal").modal("show");
        }
        
        // Validación en tiempo real y progreso
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formAdmision');
            const inputs = form.querySelectorAll('input[required], select[required]');
            
            // Actualizar progreso
            function actualizarProgreso() {
                const seccion1Inputs = document.querySelectorAll('#seccion-1 input[required], #seccion-1 select[required]');
                const seccion2Inputs = document.querySelectorAll('#seccion-2 input[required], #seccion-2 select[required]');
                const politicasCheck = document.getElementById('gridCheck');
                
                let seccion1Completa = Array.from(seccion1Inputs).every(input => input.value.trim() !== '');
                let seccion2Completa = Array.from(seccion2Inputs).every(input => input.value.trim() !== '');
                let seccion3Completa = politicasCheck.checked;
                
                document.getElementById('progress-1').classList.toggle('active', seccion1Completa);
                document.getElementById('progress-2').classList.toggle('active', seccion2Completa);
                document.getElementById('progress-3').classList.toggle('active', seccion3Completa);
            }
            
            // Agregar listeners para actualizar progreso
            inputs.forEach(input => {
                input.addEventListener('input', actualizarProgreso);
                input.addEventListener('change', actualizarProgreso);
                
                // Validación visual
                input.addEventListener('blur', function() {
                    if (this.value.trim() !== '') {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    } else if (this.hasAttribute('required')) {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });
            });
            
            document.getElementById('gridCheck').addEventListener('change', actualizarProgreso);
            
            // Mostrar loading al enviar
            form.addEventListener('submit', function(e) {
                if (form.checkValidity()) {
                    document.getElementById('loadingOverlay').style.display = 'flex';
                }
            });
            
            // Scroll suave a secciones al hacer focus
            inputs.forEach((input, index) => {
                input.addEventListener('focus', function() {
                    const section = this.closest('.form-section');
                    if (section && window.innerWidth > 768) {
                        section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            });
        });
    </script>
</body>
</html>
