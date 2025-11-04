<?php
require_once("index-logica.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");
$Plataforma = new Plataforma;
$usuarioId = !empty($_REQUEST['usuarioId']) ? base64_decode($_REQUEST['usuarioId']) : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Restablecer Contraseña</title>
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
        
        /* Card de restauración */
        .restore-card {
            background: var(--sintia-primary-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            border: 1px solid rgba(65, 196, 196, 0.1);
            backdrop-filter: blur(10px);
            max-width: 600px;
            margin: 2rem auto;
        }
        
        /* Header */
        .restore-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .restore-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--sintia-success) 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .restore-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .restore-title {
            color: var(--sintia-accent);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .restore-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0;
            font-weight: 400;
            line-height: 1.6;
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
            left: 0;
            right: 0;
            height: 2px;
            background: var(--sintia-success);
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
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
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
        
        /* Input de contraseña */
        .password-input-group {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .input-group-text {
            background-color: rgba(65, 196, 196, 0.1);
            border-color: rgba(65, 196, 196, 0.2);
            color: var(--sintia-secondary);
            border-radius: 12px 0 0 12px;
            border-right: none;
        }
        
        .form-control {
            border-left: none;
            border-color: rgba(65, 196, 196, 0.2);
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .password-input-wrapper .form-control {
            border-radius: 0;
        }
        
        .password-toggle {
            border-radius: 0 12px 12px 0;
            border-left: none;
        }
        
        .form-control:focus {
            border-color: var(--sintia-secondary);
            box-shadow: 0 0 0 0.2rem rgba(65, 196, 196, 0.25);
            border-left: none;
        }
        
        .form-control.is-valid {
            border-color: var(--sintia-success);
            background-image: none;
        }
        
        .form-control.is-invalid {
            border-color: var(--sintia-error);
            background-image: none;
        }
        
        /* Feedback de validación */
        .validation-feedback {
            display: none;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            animation: slideInUp 0.3s ease-out;
        }
        
        .validation-feedback.show {
            display: block;
        }
        
        .validation-feedback.valid {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--sintia-success);
            border-left: 3px solid var(--sintia-success);
        }
        
        .validation-feedback.invalid {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--sintia-error);
            border-left: 3px solid var(--sintia-error);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Requisitos de contraseña */
        .password-requirements {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin: 1rem 0;
            border-left: 4px solid var(--sintia-secondary);
        }
        
        .password-requirements h6 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--sintia-accent);
            margin-bottom: 0.75rem;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }
        
        .requirement-item i {
            font-size: 1rem;
            color: #94a3b8;
        }
        
        .requirement-item.met i {
            color: var(--sintia-success);
        }
        
        .requirement-item.met {
            color: var(--sintia-success);
        }
        
        /* Fuerza de contraseña */
        .password-strength {
            margin-top: 0.5rem;
        }
        
        .strength-meter {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-meter-fill.weak {
            width: 33%;
            background: var(--sintia-error);
        }
        
        .strength-meter-fill.medium {
            width: 66%;
            background: var(--sintia-warning);
        }
        
        .strength-meter-fill.strong {
            width: 100%;
            background: var(--sintia-success);
        }
        
        .strength-label {
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .strength-label.weak {
            color: var(--sintia-error);
        }
        
        .strength-label.medium {
            color: var(--sintia-warning);
        }
        
        .strength-label.strong {
            color: var(--sintia-success);
        }
        
        /* Botón principal */
        .btn-restore {
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(65, 196, 196, 0.3);
            color: white;
        }
        
        .btn-restore:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 196, 196, 0.4);
            color: white;
        }
        
        .btn-restore:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-restore.loading {
            background: linear-gradient(135deg, var(--sintia-accent) 0%, #8b5cf6 100%);
        }
        
        .btn-restore.success {
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
            animation: slideInDown 0.5s ease-out;
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
        
        /* Footer */
        .restore-footer {
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
        
        /* Spinner */
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .restore-card {
                margin: 1rem;
                padding: 2rem;
            }
            
            .restore-title {
                font-size: 1.75rem;
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
                        <div class="restore-card">
                            <form method="post" id="restoreForm" action="recuperar-clave-guardar.php" class="needs-validation" novalidate>
                                
                                <?php include("../config-general/mensajes-informativos.php"); ?>
                                
                                <input type="hidden" id="usuarioId" name="usuarioId" value="<?= htmlspecialchars($usuarioId); ?>" />
                                <?php echo Csrf::campoHTML(); ?>
                                
                                <!-- Contenedor para mensajes dinámicos -->
                                <div id="dynamicMessages"></div>
                                
                                <!-- Header -->
                                <header class="restore-header">
                                    <div class="restore-icon">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <h1 class="restore-title">¡Verificación Exitosa!</h1>
                                    <p class="restore-subtitle">
                                        Ahora puedes crear tu nueva contraseña segura
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
                                    <div class="progress-step completed">
                                        <div class="progress-step-circle">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div class="progress-step-label">Verificación</div>
                                    </div>
                                    <div class="progress-step active">
                                        <div class="progress-step-circle">
                                            <i class="bi bi-key-fill"></i>
                                        </div>
                                        <div class="progress-step-label">Nueva Contraseña</div>
                                    </div>
                                </div>
                                
                                <!-- Requisitos de contraseña -->
                                <div class="password-requirements">
                                    <h6>
                                        <i class="bi bi-shield-check me-2"></i>
                                        Requisitos de seguridad:
                                    </h6>
                                    <div class="requirement-item" id="req-length">
                                        <i class="bi bi-circle"></i>
                                        <span>Entre 8 y 20 caracteres</span>
                                    </div>
                                    <div class="requirement-item" id="req-chars">
                                        <i class="bi bi-circle"></i>
                                        <span>Letras (a-z, A-Z) y números (0-9)</span>
                                    </div>
                                    <div class="requirement-item" id="req-special">
                                        <i class="bi bi-circle"></i>
                                        <span>Caracteres especiales permitidos: . $ *</span>
                                    </div>
                                </div>
                                
                                <!-- Campo Nueva Contraseña -->
                                <div class="password-input-group">
                                    <div class="input-group password-input-wrapper">
                                        <span class="input-group-text">
                                            <i class="bi bi-key"></i>
                                        </span>
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="password" 
                                            name="password" 
                                            placeholder="Nueva contraseña"
                                            autocomplete="new-password"
                                            required>
                                        <button class="btn btn-outline-secondary input-group-text password-toggle" type="button" id="togglePassword">
                                            <i class="bi bi-eye" id="icoVerNueva"></i>
                                        </button>
                                    </div>
                                    <div class="validation-feedback" id="passwordValidation"></div>
                                    
                                    <!-- Medidor de fuerza -->
                                    <div class="password-strength" id="passwordStrength" style="display: none;">
                                        <div class="strength-meter">
                                            <div class="strength-meter-fill" id="strengthMeterFill"></div>
                                        </div>
                                        <div class="strength-label text-center" id="strengthLabel"></div>
                                    </div>
                                </div>
                                
                                <!-- Campo Confirmar Contraseña -->
                                <div class="password-input-group">
                                    <div class="input-group password-input-wrapper">
                                        <span class="input-group-text">
                                            <i class="bi bi-check-circle"></i>
                                        </span>
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="confirPassword" 
                                            name="confirPassword" 
                                            placeholder="Confirmar contraseña"
                                            autocomplete="new-password"
                                            required>
                                        <button class="btn btn-outline-secondary input-group-text password-toggle" type="button" id="toggleConfirm">
                                            <i class="bi bi-eye" id="icoVerConfirm"></i>
                                        </button>
                                    </div>
                                    <div class="validation-feedback" id="confirmValidation"></div>
                                </div>
                                
                                <!-- Botón de envío -->
                                <button class="w-100 btn btn-lg btn-restore mt-4" type="submit" id="btnEnviar" disabled>
                                    <span id="btnText">Confirmar Nueva Contraseña</span>
                                    <span id="btnSpinner" class="spinner-custom ms-2" style="display: none;"></span>
                                </button>
                                
                                <!-- Footer -->
                                <footer class="restore-footer">
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
        
        <!-- Galería de fotos -->
        <div class="logo-container vertical-center photo-gallery">
            <div class="photo-slide active" style="background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2022&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            console.log('Sistema de restauración de contraseña iniciado');
            
            // Galería de fotos
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
            
            // Variables de validación
            let passwordValid = false;
            let passwordsMatch = false;
            
            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const icon = $('#icoVerNueva');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });
            
            $('#toggleConfirm').on('click', function() {
                const confirmField = $('#confirPassword');
                const icon = $('#icoVerConfirm');
                
                if (confirmField.attr('type') === 'password') {
                    confirmField.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    confirmField.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });
            
            // Validación de contraseña en tiempo real
            $('#password').on('input', function() {
                const password = $(this).val();
                validatePassword(password);
            });
            
            // Validación de confirmación en tiempo real
            $('#confirPassword').on('input', function() {
                const password = $('#password').val();
                const confirm = $(this).val();
                validatePasswordMatch(password, confirm);
            });
            
            function validatePassword(password) {
                const feedback = $('#passwordValidation');
                const input = $('#password');
                const strengthContainer = $('#passwordStrength');
                const strengthFill = $('#strengthMeterFill');
                const strengthLabel = $('#strengthLabel');
                
                // Regex para validar contraseña
                const regex = /^[A-Za-z0-9.$*]{8,20}$/;
                
                if (password.length === 0) {
                    input.removeClass('is-valid is-invalid');
                    feedback.removeClass('show');
                    strengthContainer.hide();
                    resetRequirements();
                    passwordValid = false;
                    updateSubmitButton();
                    return;
                }
                
                // Validar requisitos individuales
                const lengthValid = password.length >= 8 && password.length <= 20;
                const charsValid = /^[A-Za-z0-9.$*]+$/.test(password);
                
                // Actualizar indicadores de requisitos
                updateRequirement('req-length', lengthValid);
                updateRequirement('req-chars', charsValid);
                updateRequirement('req-special', true); // Siempre mostrar como información
                
                // Validación completa
                if (regex.test(password)) {
                    input.addClass('is-valid').removeClass('is-invalid');
                    feedback.addClass('show valid').removeClass('invalid');
                    feedback.html('<i class="bi bi-check-circle me-1"></i> Contraseña válida');
                    passwordValid = true;
                    
                    // Calcular fuerza
                    calculatePasswordStrength(password);
                    strengthContainer.show();
                } else {
                    input.addClass('is-invalid').removeClass('is-valid');
                    feedback.addClass('show invalid').removeClass('valid');
                    feedback.html('<i class="bi bi-exclamation-circle me-1"></i> La contraseña debe tener entre 8 y 20 caracteres y solo puede contener letras, números y los símbolos . $ *');
                    passwordValid = false;
                    strengthContainer.hide();
                }
                
                updateSubmitButton();
                
                // Re-validar confirmación si ya tiene valor
                const confirm = $('#confirPassword').val();
                if (confirm.length > 0) {
                    validatePasswordMatch(password, confirm);
                }
            }
            
            function validatePasswordMatch(password, confirm) {
                const feedback = $('#confirmValidation');
                const input = $('#confirPassword');
                
                if (confirm.length === 0) {
                    input.removeClass('is-valid is-invalid');
                    feedback.removeClass('show');
                    passwordsMatch = false;
                    updateSubmitButton();
                    return;
                }
                
                if (password === confirm && passwordValid) {
                    input.addClass('is-valid').removeClass('is-invalid');
                    feedback.addClass('show valid').removeClass('invalid');
                    feedback.html('<i class="bi bi-check-circle me-1"></i> Las contraseñas coinciden');
                    passwordsMatch = true;
                } else {
                    input.addClass('is-invalid').removeClass('is-valid');
                    feedback.addClass('show invalid').removeClass('valid');
                    feedback.html('<i class="bi bi-exclamation-circle me-1"></i> Las contraseñas no coinciden');
                    passwordsMatch = false;
                }
                
                updateSubmitButton();
            }
            
            function updateRequirement(id, met) {
                const element = $(`#${id}`);
                if (met) {
                    element.addClass('met');
                    element.find('i').removeClass('bi-circle').addClass('bi-check-circle-fill');
                } else {
                    element.removeClass('met');
                    element.find('i').removeClass('bi-check-circle-fill').addClass('bi-circle');
                }
            }
            
            function resetRequirements() {
                $('.requirement-item').removeClass('met');
                $('.requirement-item i').removeClass('bi-check-circle-fill').addClass('bi-circle');
            }
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                const strengthFill = $('#strengthMeterFill');
                const strengthLabel = $('#strengthLabel');
                
                // Longitud
                if (password.length >= 8) strength += 25;
                if (password.length >= 12) strength += 25;
                
                // Tiene números
                if (/[0-9]/.test(password)) strength += 25;
                
                // Tiene mayúsculas y minúsculas
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
                
                // Tiene caracteres especiales
                if (/[.$*]/.test(password)) strength += 10;
                
                // Actualizar visualización
                strengthFill.removeClass('weak medium strong');
                strengthLabel.removeClass('weak medium strong');
                
                if (strength < 50) {
                    strengthFill.addClass('weak');
                    strengthLabel.addClass('weak').text('Débil');
                } else if (strength < 75) {
                    strengthFill.addClass('medium');
                    strengthLabel.addClass('medium').text('Media');
                } else {
                    strengthFill.addClass('strong');
                    strengthLabel.addClass('strong').text('Fuerte');
                }
            }
            
            function updateSubmitButton() {
                const btn = $('#btnEnviar');
                if (passwordValid && passwordsMatch) {
                    btn.prop('disabled', false);
                } else {
                    btn.prop('disabled', true);
                }
            }
            
            // Envío del formulario
            $('#restoreForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!passwordValid || !passwordsMatch) {
                    showMessage('Por favor completa correctamente ambos campos de contraseña.', 'error');
                    return false;
                }
                
                const btn = $('#btnEnviar');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                // Cambiar estado del botón
                btn.prop('disabled', true).addClass('loading');
                btnText.text('Guardando nueva contraseña...');
                btnSpinner.show();
                
                // Enviar formulario
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'recuperar-clave-guardar.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {
                        if (response && response.success) {
                            btn.removeClass('loading').addClass('success');
                            btnText.text('¡Contraseña actualizada!');
                            btnSpinner.hide();
                            
                            showMessage('✓ Tu contraseña ha sido actualizada exitosamente. Redirigiendo al login...', 'success');
                            
                            setTimeout(() => {
                                window.location.href = 'index.php?success=SC_DT_5';
                            }, 2000);
                        } else {
                            btn.removeClass('loading').prop('disabled', false);
                            btnText.text('Confirmar Nueva Contraseña');
                            btnSpinner.hide();
                            
                            showMessage(response.message || 'Error al actualizar la contraseña. Intenta nuevamente.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Si no es JSON, asumir que el formulario se envió correctamente (método antiguo)
                        if (xhr.status === 200) {
                            btn.removeClass('loading').addClass('success');
                            btnText.text('¡Contraseña actualizada!');
                            btnSpinner.hide();
                            
                            showMessage('✓ Contraseña actualizada. Redirigiendo...', 'success');
                            
                            setTimeout(() => {
                                $(e.target)[0].submit(); // Submit normal del formulario
                            }, 1000);
                        } else {
                            btn.removeClass('loading').prop('disabled', false);
                            btnText.text('Confirmar Nueva Contraseña');
                            btnSpinner.hide();
                            
                            showMessage('Error de conexión. Intenta nuevamente.', 'error');
                        }
                    }
                });
                
                return false;
            });
            
            function showMessage(message, type) {
                const iconMap = {
                    error: 'exclamation-triangle',
                    success: 'check-circle',
                    info: 'info-circle'
                };
                
                const messageHtml = `
                    <div class="alert-dynamic ${type}" role="alert">
                        <i class="bi bi-${iconMap[type]}"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                $('#dynamicMessages').html(messageHtml);
                
                setTimeout(() => {
                    $('#dynamicMessages').fadeOut();
                }, 8000);
            }
        });
    </script>
</body>

</html>
