<?php
require_once("index-logica.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$Plataforma = new Plataforma;

$idUsuario = !empty($_GET['idU']) ? base64_decode($_GET['idU']) : '';
$institucion = !empty($_GET['inst']) ? base64_decode($_GET['inst']) : '';

// Obtener datos del usuario y motivo del bloqueo
$datosUsuario = null;
$motivoBloqueo = null;
if (!empty($idUsuario) && !empty($institucion)) {
    try {
        $conexionPDO = Conexion::newConnection('PDO');
        
        // Obtener datos del usuario
        $sql = "SELECT uss_id, uss_usuario, uss_nombre, uss_apellido1, uss_apellido2, uss_email 
                FROM " . BD_GENERAL . ".usuarios 
                WHERE uss_id = ? AND institucion = ? AND year = ?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $idUsuario, PDO::PARAM_STR);
        $stmt->bindParam(2, $institucion, PDO::PARAM_INT);
        $yearActual = date('Y');
        $stmt->bindParam(3, $yearActual, PDO::PARAM_STR);
        $stmt->execute();
        $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener el motivo del bloqueo (registro más reciente)
        $sqlMotivo = "SELECT usblo_motivo, usblo_fecha_creacion 
                      FROM " . BD_ADMIN . ".usuarios_bloqueados 
                      WHERE usblo_id_usuario = ? AND usblo_institucion = ? AND usblo_year = ? 
                      ORDER BY usblo_fecha_creacion DESC LIMIT 1";
        $stmtMotivo = $conexionPDO->prepare($sqlMotivo);
        $stmtMotivo->bindParam(1, $idUsuario, PDO::PARAM_STR);
        $stmtMotivo->bindParam(2, $institucion, PDO::PARAM_INT);
        $stmtMotivo->bindParam(3, $yearActual, PDO::PARAM_STR);
        $stmtMotivo->execute();
        $resultadoMotivo = $stmtMotivo->fetch(PDO::FETCH_ASSOC);
        
        if ($resultadoMotivo) {
            $motivoBloqueo = $resultadoMotivo['usblo_motivo'];
        }
        
    } catch (Exception $e) {
        error_log("Error al obtener datos del usuario bloqueado: " . $e->getMessage());
        $datosUsuario = null;
        $motivoBloqueo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Solicitar Desbloqueo</title>
    <!-- Google fonts-->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@1,900,700,500,301,701,300,501,401,901,400&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Estilos personalizados -->
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
        
        body, html, * {
            font-family: var(--sintia-font-family) !important;
        }
        
        .login-container {
            background: var(--sintia-primary-bg);
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .vertical-center {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-start;
            overflow-y: auto;
            overflow-x: hidden;
            padding-top: 3rem;
        }
        
        /* Card de desbloqueo */
        .unlock-card {
            background: var(--sintia-primary-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            border: 1px solid rgba(239, 68, 68, 0.1);
            backdrop-filter: blur(10px);
            max-width: 600px;
            margin: 2rem auto;
        }
        
        /* Header */
        .unlock-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .unlock-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--sintia-error) 0%, #dc2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 12px 30px rgba(239, 68, 68, 0.4);
            }
        }
        
        .unlock-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .unlock-title {
            color: var(--sintia-error);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .unlock-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0;
            font-weight: 400;
            line-height: 1.6;
        }
        
        /* User Info Box */
        .user-info-box {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--sintia-accent);
        }
        
        .user-info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .user-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .user-info-item i {
            color: var(--sintia-accent);
            font-size: 1.1rem;
            width: 30px;
            text-align: center;
        }
        
        .user-info-label {
            font-weight: 600;
            color: #6b7280;
            margin-right: 0.5rem;
            min-width: 70px;
        }
        
        .user-info-value {
            color: var(--sintia-text-dark);
            font-weight: 500;
        }
        
        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--sintia-warning);
            display: flex;
            gap: 1rem;
        }
        
        .info-box-icon {
            color: var(--sintia-warning);
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .info-box-content {
            flex: 1;
        }
        
        .info-box-title {
            font-weight: 700;
            color: #92400e;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .info-box-text {
            color: #78350f;
            font-size: 0.875rem;
            margin: 0;
            line-height: 1.5;
        }
        
        /* Form Controls */
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--sintia-accent);
            font-size: 0.95rem;
        }
        
        .form-label i {
            margin-right: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            resize: vertical;
            min-height: 120px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--sintia-accent);
            box-shadow: 0 0 0 4px rgba(96, 23, 220, 0.1);
        }
        
        .form-control.is-invalid {
            border-color: var(--sintia-error);
        }
        
        .form-control.is-valid {
            border-color: var(--sintia-success);
        }
        
        .invalid-feedback {
            display: none;
            color: var(--sintia-error);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .form-control.is-invalid ~ .invalid-feedback {
            display: block;
        }
        
        .char-counter {
            text-align: right;
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        
        /* Button */
        .btn-unlock {
            width: 100%;
            background: linear-gradient(135deg, var(--sintia-accent) 0%, #4f46e5 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(96, 23, 220, 0.3);
            margin-top: 1rem;
        }
        
        .btn-unlock:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(96, 23, 220, 0.4);
        }
        
        .btn-unlock:active {
            transform: translateY(0);
        }
        
        .btn-unlock:disabled {
            background: #d1d5db;
            box-shadow: none;
            cursor: not-allowed;
        }
        
        .btn-spinner {
            display: none;
        }
        
        .btn-unlock.loading .btn-spinner {
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        /* Footer Links */
        .footer-links {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .footer-links a {
            color: var(--sintia-accent);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .footer-links a:hover {
            gap: 0.75rem;
            color: #4f46e5;
        }
        
        /* Copyright */
        .copyright {
            text-align: center;
            margin-top: 2rem;
            color: #9ca3af;
            font-size: 0.875rem;
        }
        
        /* Photo Gallery (lado derecho) */
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
            background: linear-gradient(45deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.15));
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .unlock-card {
                margin: 1rem;
                padding: 2rem;
            }
            
            .unlock-title {
                font-size: 1.75rem;
            }
            
            .photo-gallery {
                display: none;
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
                        <div class="unlock-card">
                            <form method="post" id="unlockForm" action="solicitud-desbloqueo-guardar.php" novalidate>
                                
                                <?php include("../config-general/mensajes-informativos.php"); ?>
                                
                                <input type="hidden" name="usuario" value="<?= htmlspecialchars($idUsuario); ?>">
                                <input type="hidden" name="inst" value="<?= htmlspecialchars($institucion); ?>">
                                <?php echo Csrf::campoHTML(); ?>
                                
                                <!-- Header -->
                                <header class="unlock-header">
                                    <div class="unlock-icon">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>
                                    <h1 class="unlock-title">Cuenta Bloqueada</h1>
                                    <p class="unlock-subtitle">
                                        Tu cuenta ha sido bloqueada. Completa este formulario para solicitar el desbloqueo.
                                    </p>
                                </header>
                                
                                <!-- Información del usuario -->
                                <?php if (!empty($datosUsuario)): ?>
                                <div class="user-info-box">
                                    <div class="user-info-item">
                                        <i class="bi bi-person-fill"></i>
                                        <span class="user-info-label">Usuario:</span>
                                        <span class="user-info-value"><?= htmlspecialchars($datosUsuario['uss_usuario'] ?? 'N/A'); ?></span>
                                    </div>
                                    <div class="user-info-item">
                                        <i class="bi bi-person-badge"></i>
                                        <span class="user-info-label">Nombre:</span>
                                        <span class="user-info-value"><?= htmlspecialchars(($datosUsuario['uss_nombre'] ?? '') . ' ' . ($datosUsuario['uss_apellido1'] ?? '')); ?></span>
                                    </div>
                                    <div class="user-info-item">
                                        <i class="bi bi-envelope-fill"></i>
                                        <span class="user-info-label">Email:</span>
                                        <span class="user-info-value"><?= htmlspecialchars($datosUsuario['uss_email'] ?? 'N/A'); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Motivo del Bloqueo -->
                                <?php if (!empty($motivoBloqueo)): ?>
                                <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; border-left: 4px solid var(--sintia-error);">
                                    <div style="display: flex; gap: 1rem;">
                                        <div style="color: var(--sintia-error); font-size: 1.5rem; flex-shrink: 0;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-weight: 700; color: #991b1b; margin-bottom: 0.5rem; font-size: 0.95rem;">
                                                🔒 Motivo del Bloqueo
                                            </div>
                                            <div style="color: #7f1d1d; font-size: 0.875rem; line-height: 1.6;">
                                                <?= nl2br(htmlspecialchars($motivoBloqueo)); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Info Box -->
                                <div class="info-box">
                                    <div class="info-box-icon">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </div>
                                    <div class="info-box-content">
                                        <div class="info-box-title">¿Qué sucederá después?</div>
                                        <div class="info-box-text">
                                            Tu solicitud será enviada automáticamente a los directivos de tu institución. 
                                            Recibirás una respuesta por correo electrónico en un plazo de 24-48 horas.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Campo de mensaje -->
                                <div class="form-floating">
                                    <label class="form-label" for="contenido">
                                        <i class="bi bi-chat-dots-fill"></i> Motivo de tu solicitud
                                    </label>
                                    <textarea 
                                        name="contenido" 
                                        class="form-control" 
                                        id="contenido" 
                                        placeholder="Describe brevemente por qué necesitas que tu cuenta sea desbloqueada..."
                                        maxlength="500"
                                        required
                                    ></textarea>
                                    <div class="char-counter">
                                        <span id="charCount">0</span> / 500 caracteres
                                    </div>
                                    <div class="invalid-feedback">
                                        Por favor, describe el motivo de tu solicitud (mínimo 10 caracteres).
                                    </div>
                                </div>
                                
                                <!-- Botón de envío -->
                                <button type="submit" class="btn-unlock" id="btnEnviar">
                                    <span class="spinner-border spinner-border-sm btn-spinner" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-send-fill"></i>
                                    <span id="btnText">Enviar Solicitud</span>
                                </button>
                                
                                <!-- Footer Links -->
                                <div class="footer-links">
                                    <a href="index.php">
                                        <i class="bi bi-arrow-left-circle-fill"></i>
                                        Volver al Login
                                    </a>
                                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdiugXhzAj0Ysmt2gthO07tbvjxTA7CHcZqgzBpkefZC6T2qg/viewform" target="_blank">
                                        <i class="bi bi-question-circle-fill"></i>
                                        ¿Necesitas ayuda?
                                    </a>
                                </div>
                                
                            </form>
                            
                            <!-- Copyright -->
                            <div class="copyright">
                                © <?= date('Y'); ?> Plataforma SINTIA by <strong style="color: var(--sintia-accent);">ODERMAN</strong>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Galería de Fotos (lado derecho) -->
        <div class="logo-container vertical-center photo-gallery">
            <!-- Fotos relacionadas con seguridad/bloqueo -->
            <div class="photo-slide active" style="background-image: url('https://images.unsplash.com/photo-1614064641938-3bbee52942c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1551836022-d5d88e9218df?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Rotación de fotos de fondo
        let currentSlide = 0;
        const slides = $('.photo-slide');
        const totalSlides = slides.length;
        
        function showNextSlide() {
            slides.removeClass('active');
            currentSlide = (currentSlide + 1) % totalSlides;
            slides.eq(currentSlide).addClass('active');
        }
        
        if (totalSlides > 0) {
            setInterval(showNextSlide, 5000);
        }
        
        // Contador de caracteres
        const textarea = document.getElementById('contenido');
        const charCount = document.getElementById('charCount');
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            // Cambiar color según proximidad al límite
            if (count > 450) {
                charCount.style.color = 'var(--sintia-error)';
                charCount.style.fontWeight = '700';
            } else if (count > 400) {
                charCount.style.color = 'var(--sintia-warning)';
                charCount.style.fontWeight = '600';
            } else {
                charCount.style.color = '#6b7280';
                charCount.style.fontWeight = '400';
            }
        });
        
        // Manejo del formulario
        const form = document.getElementById('unlockForm');
        const btnEnviar = document.getElementById('btnEnviar');
        const btnText = document.getElementById('btnText');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validación
            const contenido = textarea.value.trim();
            
            if (contenido.length < 10) {
                textarea.classList.add('is-invalid');
                textarea.classList.remove('is-valid');
                textarea.focus();
                
                // Shake animation
                form.classList.add('animate__animated', 'animate__headShake');
                setTimeout(() => {
                    form.classList.remove('animate__animated', 'animate__headShake');
                }, 500);
                
                return false;
            }
            
            textarea.classList.remove('is-invalid');
            textarea.classList.add('is-valid');
            
            // Cambiar estado del botón
            btnEnviar.disabled = true;
            btnEnviar.classList.add('loading');
            btnText.textContent = 'Enviando solicitud...';
            
            // Enviar formulario
            setTimeout(() => {
                form.submit();
            }, 500);
        });
        
        // Validación en tiempo real
        textarea.addEventListener('input', function() {
            if (this.value.trim().length >= 10) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.trim().length > 0) {
                this.classList.remove('is-valid');
            }
        });
        
        // Efecto de focus mejorado
        textarea.addEventListener('focus', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        textarea.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    </script>
</body>

</html>
