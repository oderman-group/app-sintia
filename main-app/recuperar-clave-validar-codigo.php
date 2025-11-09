<?php
require_once("index-logica.php");
$Plataforma = new Plataforma;

$datosUsuario = array();

if (!empty($_REQUEST['datosUsuario'])) {
    $datosUsuarioEncode = base64_decode($_REQUEST['datosUsuario']);
    
    // Limpiar y normalizar la codificación UTF-8
    $datosUsuarioEncode = mb_convert_encoding($datosUsuarioEncode, 'UTF-8', 'UTF-8');
    $datosUsuarioEncode = preg_replace('/[^\x20-\x7E\x80-\xFF]/', '', $datosUsuarioEncode); // Remover caracteres inválidos
    
    // Intentar JSON primero (más común en async)
    $datosUsuario = json_decode($datosUsuarioEncode, true);
    
    // Si JSON falla, intentar sin caracteres especiales
    if ($datosUsuario === null || !is_array($datosUsuario)) {
        // Intentar corregir problemas de codificación comunes
        $datosUsuarioEncode = str_replace(['"C�digo"', 'C�digo'], ['"Codigo"', 'Codigo'], $datosUsuarioEncode);
        $datosUsuario = json_decode($datosUsuarioEncode, true);
    }
    
    // Si JSON falla, intentar unserialize (para compatibilidad con código antiguo)
    if ($datosUsuario === null || !is_array($datosUsuario)) {
        $datosUsuario = @unserialize(base64_decode($_REQUEST['datosUsuario']));
    }
    
    // Si ambos fallan, dejar como array vacío
    if (!is_array($datosUsuario)) {
        $datosUsuario = array();
        error_log("ERROR: No se pudo decodificar datosUsuario. JSON error: " . json_last_error_msg());
    }
    
    // DEBUG: Log para verificar datos recibidos (comentar en producción)
    error_log("=== DATOS RECIBIDOS EN VALIDAR CÓDIGO ===");
    error_log("JSON decode result: " . (is_array($datosUsuario) ? 'SUCCESS' : 'FAILED'));
    error_log("Decoded data: " . print_r($datosUsuario, true));
}

// DEBUG: Mostrar en pantalla si no hay datos (solo desarrollo)
if (empty($datosUsuario)) {
    echo "<div style='background: #fee; padding: 20px; margin: 20px; border: 2px solid red;'>";
    echo "<h3>ERROR: No se pudieron decodificar los datos del usuario</h3>";
    echo "<p>datosUsuario está vacío después de intentar deserializar.</p>";
    if (!empty($_POST['datosUsuario'])) {
        $decoded = base64_decode($_POST['datosUsuario']);
        echo "<pre>Base64 recibido: " . htmlspecialchars(substr($_POST['datosUsuario'], 0, 100)) . "...</pre>";
        echo "<pre>Decoded (primeros 200 chars): " . htmlspecialchars(substr($decoded, 0, 200)) . "</pre>";
    }
    echo "<pre>GET: " . print_r($_GET, true) . "</pre>";
    echo "<pre>POST keys: " . print_r(array_keys($_POST), true) . "</pre>";
    echo "</div>";
}

$numeroCelular = !empty($datosUsuario['telefono']) ? preg_replace('/[()\s-]/', '', $datosUsuario['telefono']) : '';
$ultimos4Digitos = !empty($numeroCelular) ? substr($numeroCelular, -4) : '****';
$usuarioEmail = !empty($datosUsuario['usuarioEmail']) ? $datosUsuario['usuarioEmail'] : (!empty($datosUsuario['usuario_email']) ? $datosUsuario['usuario_email'] : 'tu correo');
$tieneCelular = !empty($numeroCelular) && strlen($numeroCelular) >= 7; // Validar que tenga al menos 7 dígitos

// Buscar idRegistro en todas las variantes posibles
$idRegistro = '';
if (!empty($datosUsuario['datosCodigo']['idRegistro'])) {
    $idRegistro = $datosUsuario['datosCodigo']['idRegistro'];
} elseif (!empty($datosUsuario['datos_codigo']['idRegistro'])) {
    $idRegistro = $datosUsuario['datos_codigo']['idRegistro'];
} elseif (!empty($datosUsuario['code']['idRegistro'])) {
    $idRegistro = $datosUsuario['code']['idRegistro'];
}

// Buscar usuarioId
$usuarioId = !empty($datosUsuario['idNuevo']) ? $datosUsuario['idNuevo'] : (!empty($datosUsuario['id_nuevo']) ? $datosUsuario['id_nuevo'] : (!empty($datosUsuario['usuarioId']) ? $datosUsuario['usuarioId'] : ''));

// DEBUG: Verificar valores extraídos
error_log("Valores extraídos - idRegistro: $idRegistro, usuarioId: $usuarioId, email: $usuarioEmail");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Validar Código</title>
    <!-- Google fonts-->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@1,900,700,500,301,701,300,501,401,901,400&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Estilos personalizados con paleta SINTIA -->
    <style>
        :root {
            --sintia-primary-bg: #ffffff;
            --sintia-secondary: #41c4c4;
            --sintia-accent: #6017dc;
            --sintia-text-dark: #000000;
            --sintia-text-light: #ffffff;
            --sintia-font-family: "Satoshi", sans-serif;
            --sintia-success: #10b981;
            --sintia-error: #ef4444;
            --sintia-warning: #f59e0b;
        }
        
        /* Aplicar fuente Satoshi globalmente */
        body, html, * {
            font-family: var(--sintia-font-family) !important;
        }
        
        .login-container {
            background: var(--sintia-primary-bg);
        }
        
        /* Card de validación */
        .validation-card {
            background: var(--sintia-primary-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            border: 1px solid rgba(65, 196, 196, 0.1);
            backdrop-filter: blur(10px);
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Header */
        .validation-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .validation-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(65, 196, 196, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 8px 20px rgba(65, 196, 196, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 12px 30px rgba(65, 196, 196, 0.5);
            }
        }
        
        .validation-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .validation-title {
            color: var(--sintia-accent);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .validation-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .validation-subtitle strong {
            color: var(--sintia-accent);
        }
        
        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2.5rem;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 16.66%;
            right: 16.66%;
            height: 2px;
            background: var(--sintia-secondary);
            z-index: 0;
        }
        
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        
        .progress-step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #9ca3af;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .progress-step.completed .progress-step-circle {
            background: var(--sintia-success);
            border-color: var(--sintia-success);
            color: white;
        }
        
        .progress-step.active .progress-step-circle {
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border-color: var(--sintia-accent);
            color: white;
        }
        
        .progress-step-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }
        
        .progress-step.active .progress-step-label,
        .progress-step.completed .progress-step-label {
            color: var(--sintia-accent);
            font-weight: 600;
        }
        
        /* Code Input Container */
        .code-input-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }
        
        .code-input {
            width: 60px;
            height: 70px;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            border: 2px solid rgba(65, 196, 196, 0.3);
            border-radius: 12px;
            transition: all 0.3s ease;
            background: rgba(65, 196, 196, 0.05);
        }
        
        .code-input:focus {
            border-color: var(--sintia-secondary);
            box-shadow: 0 0 0 4px rgba(65, 196, 196, 0.2);
            outline: none;
            background: white;
            transform: scale(1.05);
        }
        
        .code-input.filled {
            border-color: var(--sintia-success);
            background: rgba(16, 185, 129, 0.1);
        }
        
        .code-input.error {
            border-color: var(--sintia-error);
            background: rgba(239, 68, 68, 0.1);
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .code-separator {
            font-size: 2rem;
            color: #9ca3af;
            font-weight: 300;
        }
        
        /* Timer */
        .timer-container {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            text-align: center;
            margin: 1.5rem 0;
            border-left: 4px solid var(--sintia-warning);
        }
        
        .timer-label {
            font-size: 0.875rem;
            color: #92400e;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .timer-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sintia-warning);
            font-variant-numeric: tabular-nums;
        }
        
        .timer-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left-color: var(--sintia-warning);
        }
        
        .timer-expired {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left-color: var(--sintia-error);
        }
        
        .timer-expired .timer-label,
        .timer-expired .timer-value {
            color: var(--sintia-error);
        }
        
        /* Botón principal */
        .btn-validate {
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(65, 196, 196, 0.3);
            color: white;
        }
        
        .btn-validate:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 196, 196, 0.4);
            color: white;
        }
        
        .btn-validate:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-validate.loading {
            background: linear-gradient(135deg, var(--sintia-accent) 0%, #8b5cf6 100%);
        }
        
        .btn-validate.success {
            background: linear-gradient(135deg, var(--sintia-success) 0%, #059669 100%);
        }
        
        /* Mensajes dinámicos */
        .alert-dynamic {
            border-radius: 12px;
            border: none;
            font-weight: 500;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-dynamic i {
            font-size: 1.25rem;
        }
        
        .alert-dynamic.success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
            border-left: 4px solid var(--sintia-success);
        }
        
        .alert-dynamic.error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border-left: 4px solid var(--sintia-error);
        }
        
        .alert-dynamic.info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border-left: 4px solid var(--sintia-secondary);
        }
        
        /* Alternative actions */
        .alternative-actions {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .alternative-actions h6 {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .action-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem;
            color: var(--sintia-secondary);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .action-link:hover {
            background: rgba(65, 196, 196, 0.1);
            color: var(--sintia-accent);
            transform: translateX(3px);
        }
        
        .action-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            color: #9ca3af;
        }
        
        .action-link.disabled:hover {
            background: transparent;
            transform: none;
        }
        
        /* Footer */
        .validation-footer {
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
        }
        
        .recovery-link {
            color: var(--sintia-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .recovery-link:hover {
            color: var(--sintia-accent);
            text-decoration: none;
            transform: translateX(-2px);
        }
        
        /* Galería de fotos */
        .photo-gallery {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        
        .photo-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 2s ease-in-out;
        }
        
        .photo-slide.active {
            opacity: 1;
        }
        
        .photo-slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(65, 196, 196, 0.1), rgba(96, 23, 220, 0.1));
        }
        
        /* Loading spinner */
        .spinner-custom {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive para tablets y móviles */
        @media (max-width: 1024px) {
            /* Ocultar galería de fotos en tablets y móviles */
            .photo-gallery {
                display: none !important;
            }
            
            /* Centrar el formulario */
            .login-container {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .vertical-center {
                justify-content: center;
                align-items: center;
            }
            
            .validation-card {
                max-width: 550px;
                margin: 1.5rem auto;
            }
        }
        
        @media (max-width: 768px) {
            .validation-card {
                margin: 1rem;
                margin-top: 80px; /* Espacio extra para mensajes */
                padding: 2rem;
            }
            
            .validation-title {
                font-size: 1.75rem;
            }
            
            .code-input {
                width: 50px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .code-input-container {
                gap: 0.25rem;
            }
            
            /* Contenedor con espacio superior */
            .vertical-center {
                padding-top: 80px;
                padding-bottom: 40px;
            }
            
            /* Mensajes fijos en la parte superior */
            .alert-dynamic {
                position: fixed !important;
                top: 20px;
                left: 10px;
                right: 10px;
                z-index: 9999;
                margin: 0;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                animation: fadeIn 0.3s ease-out !important; /* Cambiar animación */
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        }
        
        @media (max-width: 480px) {
            .validation-card {
                margin: 0.5rem;
                margin-top: 70px; /* Espacio extra para mensajes */
                padding: 1.5rem;
                border-radius: 12px;
            }
            
            .validation-title {
                font-size: 1.5rem;
            }
            
            .validation-subtitle {
                font-size: 0.875rem;
            }
            
            .code-input {
                width: 45px;
                height: 55px;
                font-size: 1.25rem;
            }
            
            .code-input-container {
                gap: 0.15rem;
            }
            
            .btn-validate {
                font-size: 1rem;
                padding: 0.75rem 1.25rem;
            }
            
            /* Mensajes más compactos */
            .alert-dynamic {
                top: 15px;
                left: 8px;
                right: 8px;
                font-size: 0.875rem;
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                        <div class="validation-card">
                            <form method="post" id="validationForm" action="recuperar-clave-guardar.php" class="needs-validation" novalidate>
                                <?php include("../config-general/mensajes-informativos.php"); ?>
                                
                                <input type="hidden" id="idRegistro" name="idRegistro" value="<?= htmlspecialchars($idRegistro); ?>" />
                                <input type="hidden" id="usuarioId" name="usuarioId" value="<?= htmlspecialchars($usuarioId); ?>" />
                                
                                <!-- Contenedor para mensajes dinámicos -->
                                <div id="dynamicMessages"></div>
                                
                                <!-- Header -->
                                <header class="validation-header">
                                    <div class="validation-icon">
                                        <i class="bi bi-envelope-check"></i>
                                    </div>
                                    <h1 class="validation-title">Revisa tu bandeja de entrada</h1>
                                    <p class="validation-subtitle">
                                        Hemos enviado un código de 6 dígitos a <strong id="emailCode"><?= htmlspecialchars($usuarioEmail); ?></strong>
                                    </p>
                                </header>
                                
                                <!-- Progress Steps -->
                                <div class="progress-steps">
                                    <div class="progress-step completed">
                                        <div class="progress-step-circle">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div class="progress-step-label">Identificación</div>
                                    </div>
                                    <div class="progress-step active">
                                        <div class="progress-step-circle">
                                            <i class="bi bi-envelope-fill"></i>
                                        </div>
                                        <div class="progress-step-label">Verificación</div>
                                    </div>
                                    <div class="progress-step">
                                        <div class="progress-step-circle">
                                            <i class="bi bi-key-fill"></i>
                                        </div>
                                        <div class="progress-step-label">Nueva Contraseña</div>
                                    </div>
                                </div>
                                
                                <!-- Timer -->
                                <div class="timer-container" id="timerContainer">
                                    <div class="timer-label">Este código expira en:</div>
                                    <div class="timer-value">
                                        <span id="contMin">10:00</span> <span id="textMin" style="font-size: 1rem;">minutos</span>
                                    </div>
                                </div>
                                
                                <!-- Code Input -->
                                <div class="code-input-container">
                                    <input type="text" maxlength="1" class="form-control code-input" autocomplete="off"/>
                                    <input type="text" maxlength="1" class="form-control code-input" autocomplete="off"/>
                                    <input type="text" maxlength="1" class="form-control code-input" autocomplete="off"/>
                                    <span class="code-separator">-</span>
                                    <input type="text" maxlength="1" class="form-control code-input" autocomplete="off"/>
                                    <input type="text" maxlength="1" class="form-control code-input" autocomplete="off"/>
                                    <input type="text" maxlength="1" class="form-control code-input" autocomplete="off"/>
                                </div>
                                
                                <!-- Botón de validación -->
                                <button type="button" id="btnValidarCodigo" class="btn btn-validate w-100 mt-4" onclick="verificarCodigo()">
                                    <span id="btnText">Validar Código</span>
                                    <span id="btnSpinner" class="spinner-custom ms-2" style="display: none;"></span>
                                </button>
                                
                                <!-- Acciones alternativas -->
                                <div class="alternative-actions">
                                    <h6>¿No recibiste el código?</h6>
                                    <div class="row g-2">
                                        <div class="<?= $tieneCelular ? 'col-md-6' : 'col-12'; ?>">
                                            <a href="javascript:void(0);" id="intNuevo" class="action-link" data-color-cambio="<?=$Plataforma->colorUno;?>">
                                                <i class="bi bi-arrow-clockwise"></i>
                                                Reenviar al correo
                                            </a>
                                        </div>
                                        <?php if ($tieneCelular): ?>
                                        <div class="col-md-6">
                                            <a href="javascript:void(0);" id="enviarCodigoSMS" class="action-link">
                                                <i class="bi bi-phone"></i>
                                                Enviar por SMS (*** *** <?= htmlspecialchars($ultimos4Digitos); ?>)
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="d-block text-center mt-2 text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Revisa también tu carpeta de spam
                                    </small>
                                </div>
                                
                                <!-- Footer -->
                                <footer class="validation-footer">
                                    <div class="row g-3">
                                        <div class="col-md-6 text-center text-md-start">
                                            <a href="index.php" class="recovery-link">
                                                <i class="bi bi-arrow-left"></i>
                                                Volver al login
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-center text-md-end">
                                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" 
                                               class="recovery-link" 
                                               target="_blank" 
                                               rel="noopener noreferrer">
                                                <i class="bi bi-headset"></i>
                                                ¿Necesitas ayuda?
                                            </a>
                                        </div>
                                    </div>
                                </footer>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Galería de fotos rotando -->
        <div class="logo-container vertical-center photo-gallery">
            <div class="photo-slide active" style="background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2022&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/recuperarClave.js?v=<?= time(); ?>"></script>
    
    <script>
        // Asegurar que la variable intento esté disponible
        if (typeof intento === 'undefined') {
            window.intento = 0;
        }
        
        // Inicializar eventos inmediatamente
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Sistema de validación de código iniciado');
            
            // Iniciar countdown
            startCountdown(10 * 60);
            
            // Configurar botones de reenvío
            const btnReenviar = document.getElementById('intNuevo');
            const btnSMS = document.getElementById('enviarCodigoSMS');
            
            // MODO TESTING: Habilitar botones inmediatamente para pruebas
            // TODO: Comentar estas líneas en producción y dejar que solo se habiliten cuando expire el timer
            if (btnReenviar) {
                btnReenviar.classList.remove('disabled');
                btnReenviar.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Click en reenviar correo');
                    intento++;
                    enviarCodigo();
                });
                console.log('Botón reenviar configurado');
            }
            
            if (btnSMS) {
                btnSMS.classList.remove('disabled');
                btnSMS.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Click en enviar SMS');
                    enviarCodigoSMS();
                });
                console.log('Botón SMS configurado');
            } else {
                console.log('Botón SMS no disponible (usuario sin celular registrado)');
            }
            
            // Galería de fotos
            let currentSlide = 0;
            const slides = document.querySelectorAll('.photo-slide');
            const totalSlides = slides.length;
            
            function showNextSlide() {
                slides.forEach(slide => slide.classList.remove('active'));
                currentSlide = (currentSlide + 1) % totalSlides;
                if (slides[currentSlide]) {
                    slides[currentSlide].classList.add('active');
                }
            }
            
            if (totalSlides > 0) {
                setInterval(showNextSlide, 5000);
            }
            
            // Focus automático en el primer input
            const firstInput = document.querySelector('.code-input');
            if (firstInput) {
                firstInput.focus();
            }
            
            // Log para debugging
            console.log('Elementos encontrados:', {
                btnReenviar: !!btnReenviar,
                btnSMS: !!btnSMS,
                inputs: document.querySelectorAll('.code-input').length,
                slides: totalSlides
            });
        });
    </script>
</body>

</html>
