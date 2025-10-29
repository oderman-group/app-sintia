<?php
// Configuración directa sin redirecciones

// Configuración segura de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");

// Conexión a base de datos
try {
    $conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
    $conexion = $conexionBaseDatosServicios;
    
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conexion, "utf8mb4");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Cargar clase Plataforma
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

// IMPORTANTE: Agregar timestamp para evitar caché
$versionCache = time();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Registro - Plataforma Educativa SINTIA</title>
    
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="../config-general/assets-login-2023/css/styles.css?v=<?=$versionCache?>" rel="stylesheet" />
    
    <!-- Custom styles -->
    <link href="../config-general/assets-login-2023/css/registro.css?v=<?=$versionCache?>" rel="stylesheet" />
    
    <!-- reCAPTCHA v3 (Opcional - Configurar con tu propia clave) -->
    <!-- <script src="https://www.google.com/recaptcha/api.js?render=TU_SITE_KEY_AQUI"></script> -->

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .registration-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .registration-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }
        
        .progress-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            color: white;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2rem;
        }
        
        .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 0;
        }
        
        .progress-line-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: #fff;
            transition: width 0.4s ease;
            width: 0%;
        }
        
        .step-item {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            border: 3px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }
        
        .step-item.active .step-circle {
            background: #fff;
            color: #667eea;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.4);
        }
        
        .step-item.completed .step-circle {
            background: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .step-label {
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        .step-item.active .step-label {
            opacity: 1;
            font-weight: 600;
        }
        
        .form-content {
            padding: 3rem 2rem;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-floating > label {
            color: #6b7280;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
        }
        
        .form-control:focus + .input-icon,
        .form-control.is-valid + .input-icon {
            color: #10b981;
        }
        
        .form-control.is-invalid + .input-icon {
            color: #ef4444;
        }
        
        .validation-message {
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .validation-message.show {
            display: block;
        }

        .modulo-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .modulo-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }
        
        .modulo-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .modulo-header-registro {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .modulo-icon-registro {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .modulo-info-registro h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .modulo-descripcion-registro {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.4;
            margin-top: auto;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-input {
            width: 1.5rem;
            height: 1.5rem;
            cursor: pointer;
        }
        
        .code-input-container {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .code-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .code-separator {
            display: flex;
            align-items: center;
            font-size: 24px;
            color: #9ca3af;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            border: 2px solid #9ca3af;
            color: #6b7280;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: #9ca3af;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-link {
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-link:hover {
            color: #764ba2;
            transform: translateX(3px);
        }
        
        .help-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            padding: 2rem;
            border-radius: 15px;
            margin-top: 2rem;
            text-align: center;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin: 1rem 0;
        }
        
        .alert-success-custom {
            background: #d1fae5;
            color: #065f46;
        }
        
        .alert-error-custom {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .whatsapp-float:hover {
            background-color: #128C7E;
            transform: scale(1.1);
        }
        
        .grecaptcha-badge {
            visibility: hidden;
        }
        
        .recaptcha-info {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <!-- WhatsApp Float Button -->
    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, me gustaría recibir más información de la plataforma." 
       class="whatsapp-float" target="_blank" aria-label="WhatsApp">
        <i class="fa fa-whatsapp"></i>
    </a>

    <div class="registration-wrapper">
            <div class="container">
            <div class="registration-card">
                <!-- Progress Header -->
                <div class="progress-container">
                    <div class="text-center mb-4">
                        <a href="index.php" class="d-inline-block">
                            <img src="<?=$Plataforma->logoCian;?>" width="80" alt="Logo SINTIA" style="cursor: pointer;">
                        </a>
                        <h2 class="mt-3 mb-1">¡Bienvenido a SINTIA!</h2>
                        <p class="mb-0 opacity-75">Completa tu registro en 3 simples pasos</p>
                    </div>
                    
                    <div class="progress-steps">
                        <div class="progress-line">
                            <div class="progress-line-fill" id="progressFill"></div>
                        </div>
                        
                        <div class="step-item active" data-step="1">
                            <div class="step-circle">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="step-label">Datos Personales</div>
                        </div>
                        
                        <div class="step-item" data-step="2">
                            <div class="step-circle">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="step-label">Tu Institución</div>
                        </div>
                        
                        <div class="step-item" data-step="3">
                            <div class="step-circle">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="step-label">Verificación</div>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="form-content">
                    <form id="registrationForm" method="post" action="registro-guardar.php" novalidate>
                        <!-- Token CSRF para protección contra ataques -->
                        <?php echo campoTokenCSRF(); ?>
                        
                        <input type="hidden" name="urlDefault" value="<?= !empty($_REQUEST["urlDefault"]) ? htmlspecialchars($_REQUEST["urlDefault"], ENT_QUOTES, 'UTF-8') : ""; ?>" />
                        <input type="hidden" id="idRegistro" name="idRegistro" value="<?= !empty($_REQUEST["idRegistro"]) ? htmlspecialchars($_REQUEST["idRegistro"], ENT_QUOTES, 'UTF-8') : ""; ?>" />
                        <input type="hidden" id="recaptchaToken" name="recaptchaToken" value="" />
                        
                        <!-- Step 1: Personal Data -->
                        <div class="step-content active" id="step1">
                            <h3 class="mb-4 text-center">Cuéntanos sobre ti</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               placeholder="Nombres" required 
                                               value="<?= !empty($_REQUEST["nombre"]) ? $_REQUEST["nombre"] : ""; ?>">
                                        <label for="nombre"><i class="bi bi-person me-2"></i>Nombres</label>
                                    <div class="invalid-feedback">Por favor ingrese su nombre.</div>
                                        <div class="valid-feedback">¡Se ve bien!</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                               placeholder="Apellidos" required
                                               value="<?= !empty($_REQUEST["apellidos"]) ? $_REQUEST["apellidos"] : ""; ?>">
                                        <label for="apellidos"><i class="bi bi-person me-2"></i>Apellidos</label>
                                    <div class="invalid-feedback">Por favor ingrese sus apellidos.</div>
                                        <div class="valid-feedback">¡Se ve bien!</div>
                                    </div>
                                </div>
                                </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="email@ejemplo.com" required
                                       value="<?= !empty($_REQUEST["email"]) ? $_REQUEST["email"] : ""; ?>">
                                <label for="email"><i class="bi bi-envelope me-2"></i>Correo electrónico</label>
                                <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
                                <div class="valid-feedback">¡Correo disponible!</div>
                                <small class="text-muted" style="display: none;">Verificando disponibilidad...</small>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" id="celular" name="celular" 
                                       placeholder="3001234567" required pattern="[0-9]{10}"
                                       value="<?= !empty($_REQUEST["celular"]) ? $_REQUEST["celular"] : ""; ?>">
                                <label for="celular"><i class="bi bi-phone me-2"></i>Número de celular</label>
                                <div class="invalid-feedback">Por favor ingrese un número celular válido (10 dígitos).</div>
                                <div class="valid-feedback">¡Se ve bien!</div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="button" class="btn btn-primary btn-lg" id="btnStep1Next">
                                    Continuar <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                            
                            <div class="text-center mt-4">
                                <p class="text-muted mb-2">¿Ya tienes una cuenta?</p>
                                <a href="index.php" class="btn btn-link text-decoration-none fw-bold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                                </a>
                            </div>
                        </div>

                        <!-- Step 2: Institution Data -->
                        <div class="step-content" id="step2">
                            <h3 class="mb-4 text-center">Información de tu Institución</h3>
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nombreIns" name="nombreIns" 
                                       placeholder="Nombre de la institución" required 
                                       onchange="generarSiglas(this)"
                                       value="<?= !empty($_REQUEST["nombreIns"]) ? $_REQUEST["nombreIns"] : ""; ?>">
                                <label for="nombreIns"><i class="bi bi-building me-2"></i>Nombre de la institución</label>
                                <input type="hidden" name="siglasInst" id="siglasInst">
                                <div class="invalid-feedback">Por favor ingrese el nombre de su institución.</div>
                                <div class="valid-feedback">¡Se ve bien!</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="ciudad" name="ciudad" 
                                               placeholder="Ciudad" required
                                               value="<?= !empty($_REQUEST["ciudad"]) ? $_REQUEST["ciudad"] : ""; ?>">
                                        <label for="ciudad"><i class="bi bi-geo-alt me-2"></i>Municipio/Ciudad</label>
                                        <div class="invalid-feedback">Por favor ingrese la ciudad.</div>
                                        <div class="valid-feedback">¡Se ve bien!</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="cargo" name="cargo" 
                                               placeholder="Cargo" required
                                               value="<?= !empty($_REQUEST["cargo"]) ? $_REQUEST["cargo"] : ""; ?>">
                                        <label for="cargo"><i class="bi bi-briefcase me-2"></i>Cargo que ocupa</label>
                                        <div class="invalid-feedback">Por favor ingrese su cargo.</div>
                                        <div class="valid-feedback">¡Se ve bien!</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="divider my-4"></div>
                            
                            <h4 class="mt-4 mb-3 text-center">
                                <i class="bi bi-puzzle me-2"></i>
                                ¿Qué módulos te interesan?
                            </h4>
                            <p class="text-center text-muted mb-4">
                                Selecciona los módulos que más te interesen (puedes seleccionar varios)
                            </p>
                            
                            <div id="modulosSeleccionadosInfo" class="alert alert-info mb-4" style="display: none;">
                                <i class="bi bi-check-circle me-2"></i>
                                Has seleccionado <strong><span id="modulosCounter">0</span></strong> módulo(s)
                            </div>
                            
                            <div class="row g-3" id="modulosGrid">
                                <?php
                                    try {
                                        $consultaModulos = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".modulos WHERE mod_estado=1 ORDER BY mod_nombre ASC");
                                        while ($modulo = mysqli_fetch_array($consultaModulos, MYSQLI_BOTH)) {
                                ?>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="modulo-card" data-modulo-id="<?= $modulo['mod_id']; ?>">
                                            <div class="modulo-header-registro">
                                                <div class="modulo-icon-registro">
                                                    <i class="bi bi-puzzle-fill"></i>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="modulos[]" 
                                                           value="<?= $modulo['mod_id']; ?>" 
                                                           id="modulo<?= $modulo['mod_id']; ?>">
                                                </div>
                                            </div>
                                            <div class="modulo-info-registro">
                                                <h5><?= $modulo['mod_nombre']; ?></h5>
                                                <span class="badge bg-secondary">ID: <?= $modulo['mod_id']; ?></span>
                                            </div>
                                            <div class="modulo-descripcion-registro">
                                                <?= !empty($modulo['mod_descripcion']) ? $modulo['mod_descripcion'] : 'Módulo del sistema SINTIA'; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                        }
                                    } catch (Exception $e) {
                                        echo '<div class="col-12"><div class="alert alert-warning">No se pudieron cargar los módulos</div></div>';
                                    }
                                ?>
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-outline-primary flex-fill" id="btnStep2Prev">
                                    <i class="bi bi-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary flex-fill" id="btnStep2Next">
                                    Continuar <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="index.php" class="btn btn-link btn-sm text-muted">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar registro
                                </a>
                            </div>
                        </div>

                        <!-- Step 3: Verification -->
                        <div class="step-content" id="step3">
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="bi bi-envelope-check" style="font-size: 4rem; color: #667eea;"></i>
                                </div>

                                <h3 class="mb-3">Verifica tu correo electrónico</h3>
                                <p class="text-muted mb-1">
                                    Hemos enviado un código de 6 dígitos a
                                </p>
                                <p class="fw-bold text-primary" id="emailDisplay">tu_correo@ejemplo.com</p>
                                
                                <div class="alert alert-info alert-custom mb-4">
                                    <i class="bi bi-clock me-2"></i>
                                    El código expira en <strong><span id="countdown">10:00</span></strong>
                                </div>

                                <div class="code-input-container">
                                    <input type="text" maxlength="1" class="code-input" data-index="0" />
                                    <input type="text" maxlength="1" class="code-input" data-index="1" />
                                    <input type="text" maxlength="1" class="code-input" data-index="2" />
                                    <div class="code-separator">-</div>
                                    <input type="text" maxlength="1" class="code-input" data-index="3" />
                                    <input type="text" maxlength="1" class="code-input" data-index="4" />
                                    <input type="text" maxlength="1" class="code-input" data-index="5" />
                                </div>
                                
                                <div id="verificationMessage"></div>
                                
                                <button type="button" id="btnVerificar" class="btn btn-primary btn-lg mb-3">
                                    Verificar Código
                                    <span class="loading-spinner" id="verifySpinner"></span>
                                </button>
                                
                                <div class="mt-4">
                                    <p class="text-muted small mb-2">¿No recibiste el código?</p>
                                    <button type="button" id="btnReenviar" class="btn btn-link" disabled>
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reenviar código
                                    </button>
                                    </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="button" class="btn btn-outline-primary flex-fill" id="btnStep3Prev">
                                        <i class="bi bi-arrow-left me-2"></i>Atrás
                                    </button>
                                    <a href="index.php" class="btn btn-outline-secondary flex-fill">
                                        <i class="bi bi-x-circle me-2"></i>Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                        </form>
                    
                    <!-- Help Section -->
                    <div class="help-section">
                        <h5 class="mb-3">¿Necesitas ayuda?</h5>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, necesito ayuda con el registro" 
                               class="btn btn-success" target="_blank">
                                <i class="fa fa-whatsapp me-2"></i>3006075800
                            </a>
                            <a href="mailto:info@plataformasintia.com" 
                               class="btn btn-primary">
                                <i class="bi bi-envelope me-2"></i>info@plataformasintia.com
                            </a>
                    </div>
                        
                        <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e5e7eb;">
                            <p class="mb-2 text-muted">¿Quieres conocer más sobre SINTIA?</p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <a href="https://plataformasintia.com" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="bi bi-globe me-2"></i>Sitio Web
                                </a>
                                <a href="index.php" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                                </a>
                </div>
            </div>
        </div>
                    
                    <div class="recaptcha-info">
                        Este sitio está protegido por reCAPTCHA y se aplican la 
                        <a href="https://policies.google.com/privacy" target="_blank">Política de Privacidad</a> 
                        y los <a href="https://policies.google.com/terms" target="_blank">Términos de Servicio</a> de Google.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    
    <script>
        // Función para generar siglas
        function obtenerPrimerasLetras(frase) {
            var palabras = frase.split(" ");
            var primerasLetras = "";
            for (var i = 0; i < palabras.length; i++) {
                if (palabras[i].length > 0 && palabras[i].length > 1) {
                    primerasLetras += palabras[i][0] + palabras[i][1];
                } else if (palabras[i].length > 0) {
                    primerasLetras += palabras[i][0];
                }
            }
            return primerasLetras;
        }

        function generarSiglas(datos) {
            var institucion = datos.value;
            var siglas = obtenerPrimerasLetras(institucion);
            document.getElementById("siglasInst").value = siglas.toUpperCase();
        }
        
        // Verificar que jQuery esté cargado
        if (typeof jQuery === 'undefined') {
            console.error('jQuery no está cargado');
        }
    </script>
    
    <script>
    // ==========================================
    // SCRIPT DE REGISTRO - SISTEMA COMPLETO
    // ==========================================
    
    let currentStep = 1;
    let datosRegistro = {};
    let interval = null;
    
    $(document).ready(function() {
        console.log('✅ Sistema de registro iniciado');
        
        // NAVEGACIÓN ENTRE PASOS
        $('#btnStep1Next').click(function() {
            if (validarPaso1()) {
                irAPaso(2);
            }
        });
        
        $('#btnStep2Prev').click(function() {
            irAPaso(1);
        });
        
        $('#btnStep2Next').click(function() {
            if (validarPaso2()) {
                irAPaso(3);
                // Al llegar al paso 3, crear cuenta y enviar código
                crearCuentaYEnviarCodigo();
            }
        });
        
        $('#btnStep3Prev').click(function() {
            irAPaso(2);
        });
        
        // BOTONES DEL PASO 3
        $('#btnVerificar').click(function() {
            verificarCodigoFinal();
        });
        
        $('#btnReenviar').click(function() {
            reenviarCodigoFinal();
        });
        
        // SELECCIÓN DE MÓDULOS
        $('.modulo-card').click(function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = $(this).find('input[type="checkbox"]');
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
            $(this).toggleClass('selected', $(this).find('input[type="checkbox"]').is(':checked'));
            actualizarContadorModulos();
        });
        
        // INPUTS DE CÓDIGO
        $('.code-input').on('input', function() {
            const index = $(this).data('index');
            const value = $(this).val();
            
            // Solo permitir números
            if (value && !/^\d$/.test(value)) {
                $(this).val('');
                return;
            }
            
            if (value && index < 5) {
                $('.code-input[data-index="' + (index + 1) + '"]').focus();
            }
            
            // Verificar si todos están completos
            let todosCompletos = true;
            let codigoCompleto = '';
            $('.code-input').each(function() {
                const val = $(this).val();
                if (!val) {
                    todosCompletos = false;
                    return false;
                }
                codigoCompleto += val;
            });
            
            if (todosCompletos) {
                setTimeout(() => verificarCodigoFinal(), 300);
            }
        });
        
        $('.code-input').on('keydown', function(e) {
            const index = $(this).data('index');
            if (e.key === 'Backspace' && !$(this).val() && index > 0) {
                $('.code-input[data-index="' + (index - 1) + '"]').focus();
            }
        });
        
        // Manejar pegado de código
        $('.code-input').first().on('paste', function(e) {
            e.preventDefault();
            const pastedData = e.originalEvent.clipboardData.getData('text');
            
            if (/^\d{6}$/.test(pastedData)) {
                pastedData.split('').forEach((char, index) => {
                    $('.code-input[data-index="' + index + '"]').val(char);
                });
                $('.code-input').last().focus();
                setTimeout(() => verificarCodigoFinal(), 300);
            }
        });
        
        console.log('✅ Sistema listo');
    });
    
    function irAPaso(paso) {
        // Ocultar todos los pasos
        $('.step-content').removeClass('active');
        $('.step-item').removeClass('active completed');
        
        // Mostrar paso actual
        $('#step' + paso).addClass('active');
        
        // Actualizar indicadores
        for (let i = 1; i <= 3; i++) {
            if (i < paso) {
                $('.step-item[data-step="' + i + '"]').addClass('completed');
            } else if (i === paso) {
                $('.step-item[data-step="' + i + '"]').addClass('active');
            }
        }
        
        // Actualizar barra de progreso
        const progress = ((paso - 1) / 2) * 100;
        $('#progressFill').css('width', progress + '%');
        
        currentStep = paso;
        
        // Actualizar email en paso 3
        if (paso === 3) {
            $('#emailDisplay').text($('#email').val());
        }
        
        // Scroll al inicio
        $('html, body').animate({ scrollTop: 0 }, 300);
    }
    
    function crearCuentaYEnviarCodigo() {
        console.log('Creando cuenta y enviando código...');
        
        // Recopilar todos los datos del formulario
        const formData = new FormData();
        formData.append('nombre', $('#nombre').val().trim());
        formData.append('apellidos', $('#apellidos').val().trim());
        formData.append('email', $('#email').val().trim());
        formData.append('celular', $('#celular').val().trim());
        formData.append('nombreIns', $('#nombreIns').val().trim());
        formData.append('siglasInst', $('#siglasInst').val().trim());
        formData.append('ciudad', $('#ciudad').val().trim());
        formData.append('cargo', $('#cargo').val().trim());
        
        // Agregar módulos seleccionados
        $('input[name="modulos[]"]:checked').each(function() {
            formData.append('modulos[]', $(this).val());
        });
        
        // Mostrar mensaje de carga
        mostrarMensaje('Creando tu cuenta...', 'info');
        
        $.ajax({
            url: 'registro-crear-y-enviar-codigo.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('========================================');
                console.log('RESPUESTA DE CREAR CUENTA:');
                console.log('========================================');
                console.log('Response completo:', response);
                console.log('usuarioId (uss_id):', response.usuarioId);
                console.log('institucionId:', response.institucionId);
                console.log('institucionNombre:', response.institucionNombre);
                console.log('usuarioNombre:', response.usuarioNombre);
                console.log('usuarioEmail:', response.usuarioEmail);
                console.log('datosCodigo:', response.datosCodigo);
                console.log('========================================');
                
                if (response.success) {
                    // Guardar datos del registro
                    datosRegistro = response;
                    
                    console.log('Datos guardados en datosRegistro:', datosRegistro);
                    
                    // Actualizar email display
                    $('#emailDisplay').text(response.usuarioEmail);
                    
                    // Iniciar countdown
                    startCountdown(10 * 60);
                    
                    // Habilitar botón de reenvío después de 60 segundos
                    setTimeout(() => {
                        $('#btnReenviar').prop('disabled', false);
                    }, 60000);
                    
                    // Focus en primer input
                    $('.code-input').first().focus();
                    
                    mostrarMensaje('Código enviado a ' + response.usuarioEmail, 'success');
                } else {
                    mostrarMensaje(response.message || 'Error al crear la cuenta', 'error');
                    // Volver al paso 2
                    setTimeout(() => irAPaso(2), 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión. Intenta nuevamente.', 'error');
                setTimeout(() => irAPaso(2), 3000);
            }
        });
    }
    
    function verificarCodigoFinal() {
        let codigo = '';
        $('.code-input').each(function() {
            codigo += $(this).val();
        });
        
        if (codigo.length !== 6) {
            mostrarMensaje('Ingresa el código completo de 6 dígitos', 'error');
            return;
        }
        
        $('#btnVerificar').prop('disabled', true).text('Verificando...');
        
        console.log('Enviando validación con:', {
            codigo: codigo,
            idRegistro: datosRegistro.datosCodigo.idRegistro,
            usuarioId: datosRegistro.usuarioId,  // uss_id generado desde PHP
            institucionId: datosRegistro.institucionId
        });
        
        $.ajax({
            url: 'registro-validar-codigo-final.php',
            type: 'POST',
            data: {
                code: codigo,
                idRegistro: datosRegistro.datosCodigo.idRegistro,
                usuarioId: datosRegistro.usuarioId,          // uss_id generado desde PHP
                institucionId: datosRegistro.institucionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (interval) clearInterval(interval);
                    mostrarMensaje('¡Registro completado! Redirigiendo...', 'success');
                    
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 2000);
                } else {
                    mostrarMensaje(response.message || 'Código incorrecto', 'error');
                    $('.code-input').val('').first().focus();
                    $('#btnVerificar').prop('disabled', false).text('Validar Código');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en validación:', error, xhr.responseText);
                mostrarMensaje('Error de conexión', 'error');
                $('#btnVerificar').prop('disabled', false).text('Validar Código');
            }
        });
    }
    
    function reenviarCodigoFinal() {
        if (!datosRegistro.usuarioId) {
            mostrarMensaje('Error: No hay datos de registro', 'error');
            return;
        }
        
        $('#btnReenviar').prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Enviando...');
        
        $.ajax({
            url: 'registro-reenviar-codigo.php',
            type: 'POST',
            data: {
                usuarioId: datosRegistro.usuarioId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    datosRegistro.datosCodigo = response.datosCodigo;
                    mostrarMensaje('Código reenviado exitosamente', 'success');
                    
                    // Reiniciar countdown
                    if (interval) clearInterval(interval);
                    startCountdown(10 * 60);
                    
                    $('.code-input').val('').first().focus();
                    
                    setTimeout(() => {
                        $('#btnReenviar').prop('disabled', false).html('<i class="bi bi-arrow-clockwise me-2"></i>Reenviar código');
                    }, 60000);
                } else {
                    mostrarMensaje(response.message || 'Error al reenviar', 'error');
                    $('#btnReenviar').prop('disabled', false).html('<i class="bi bi-arrow-clockwise me-2"></i>Reenviar código');
                }
            },
            error: function() {
                mostrarMensaje('Error de conexión', 'error');
                $('#btnReenviar').prop('disabled', false).html('<i class="bi bi-arrow-clockwise me-2"></i>Reenviar código');
            }
        });
    }
    
    function startCountdown(durationInSeconds) {
        let remainingTime = durationInSeconds;
        
        interval = setInterval(() => {
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;
            
            $('#countdown').text(`${minutes}:${seconds < 10 ? '0' + seconds : seconds}`);
            
            if (remainingTime === 0) {
                clearInterval(interval);
                $('#countdown').parent().removeClass('alert-info').addClass('alert-danger');
                $('#countdown').parent().html('<i class="bi bi-exclamation-triangle me-2"></i>El código ha expirado');
                $('#btnReenviar').prop('disabled', false);
            }
            
            remainingTime -= 1;
        }, 1000);
    }
    
    function mostrarMensaje(mensaje, tipo) {
        const iconos = {
            success: 'check-circle-fill',
            error: 'exclamation-circle-fill',
            info: 'info-circle-fill'
        };
        
        const clases = {
            success: 'alert-success-custom',
            error: 'alert-error-custom',
            info: 'alert-info'
        };
        
        const html = `
            <div class="alert alert-custom ${clases[tipo]} animate__animated animate__fadeInDown" role="alert">
                <i class="bi ${iconos[tipo]} me-2"></i>${mensaje}
            </div>
        `;
        
        $('#verificationMessage').html(html);
        
        setTimeout(() => {
            $('#verificationMessage .alert').addClass('animate__fadeOutUp');
            setTimeout(() => {
                $('#verificationMessage').empty();
            }, 500);
        }, 5000);
    }
    
    function validarPaso1() {
        let valido = true;
        
        // Validar nombre
        const nombre = $('#nombre').val().trim();
        if (nombre.length < 2) {
            $('#nombre').addClass('is-invalid');
            valido = false;
        } else {
            $('#nombre').removeClass('is-invalid').addClass('is-valid');
        }
        
        // Validar apellidos
        const apellidos = $('#apellidos').val().trim();
        if (apellidos.length < 2) {
            $('#apellidos').addClass('is-invalid');
            valido = false;
        } else {
            $('#apellidos').removeClass('is-invalid').addClass('is-valid');
        }
        
        // Validar email
        const email = $('#email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('#email').addClass('is-invalid');
            valido = false;
        } else {
            $('#email').removeClass('is-invalid').addClass('is-valid');
        }
        
        // Validar celular
        const celular = $('#celular').val().trim();
        if (celular.length !== 10 || !/^\d+$/.test(celular)) {
            $('#celular').addClass('is-invalid');
            valido = false;
        } else {
            $('#celular').removeClass('is-invalid').addClass('is-valid');
        }
        
        if (!valido) {
            alert('Por favor completa correctamente todos los campos.');
        }
        
        return valido;
    }
    
    function validarPaso2() {
        let valido = true;
        
        // Validar nombre institución
        const nombreIns = $('#nombreIns').val().trim();
        if (nombreIns.length < 3) {
            $('#nombreIns').addClass('is-invalid');
            valido = false;
        } else {
            $('#nombreIns').removeClass('is-invalid').addClass('is-valid');
        }
        
        // Validar ciudad
        const ciudad = $('#ciudad').val().trim();
        if (ciudad.length < 2) {
            $('#ciudad').addClass('is-invalid');
            valido = false;
        } else {
            $('#ciudad').removeClass('is-invalid').addClass('is-valid');
        }
        
        // Validar cargo
        const cargo = $('#cargo').val().trim();
        if (cargo.length < 2) {
            $('#cargo').addClass('is-invalid');
            valido = false;
        } else {
            $('#cargo').removeClass('is-invalid').addClass('is-valid');
        }
        
        // Validar módulos seleccionados
        const modulosSeleccionados = $('input[name="modulos[]"]:checked').length;
        if (modulosSeleccionados === 0) {
            alert('Por favor selecciona al menos un módulo de tu interés.');
            valido = false;
        }
        
        if (!valido && modulosSeleccionados > 0) {
            alert('Por favor completa correctamente todos los campos.');
        }
        
        return valido;
    }
    
    function actualizarContadorModulos() {
        const total = $('input[name="modulos[]"]:checked').length;
        $('#modulosCounter').text(total);
        
        if (total > 0) {
            $('#modulosSeleccionadosInfo').show();
        } else {
            $('#modulosSeleccionadosInfo').hide();
        }
    }
    </script>
</body>

</html>
