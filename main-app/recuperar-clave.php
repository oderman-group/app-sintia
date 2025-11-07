<?php

require_once("index-logica.php");

if (isset($_POST['usuariosEncontrados'])) {
    $usuario = base64_decode($_GET['valor']);
    $listaUsuarios = unserialize($_POST['usuariosEncontrados']);
    echo '<script type="text/javascript">
    window.onload = function() {
        $("#miModalUsuarios").modal("show");
    }
    </script>';
} else {
    $usuario = '';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>Plataforma Educativa SINTIA | Recuperar Contraseña</title>
    <!-- Google fonts-->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@1,900,700,500,301,701,300,501,401,901,400&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="../config-general/assets-login-2023/css/styles.css" rel="stylesheet" />
    
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
        
        /* Fondo blanco limpio */
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
        
        /* Card de recuperación */
        .recovery-card {
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
        .recovery-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .recovery-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 20px rgba(65, 196, 196, 0.3);
        }
        
        .recovery-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .recovery-title {
            color: var(--sintia-accent);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .recovery-subtitle {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0;
            font-weight: 400;
            line-height: 1.6;
        }
        
        /* Campos de entrada mejorados */
        .input-group-text {
            background-color: rgba(65, 196, 196, 0.1);
            border-color: rgba(65, 196, 196, 0.2);
            color: var(--sintia-secondary);
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        
        .form-control {
            border-left: none;
            border-color: rgba(65, 196, 196, 0.2);
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
            border-radius: 0 12px 12px 0;
            font-size: 1rem;
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
        
        /* Validación en tiempo real */
        .validation-feedback {
            display: none;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            padding: 0.5rem 0.75rem;
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
        
        /* Botón principal mejorado */
        .btn-recovery {
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
        
        .btn-recovery:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 196, 196, 0.4);
            color: white;
        }
        
        .btn-recovery:active {
            transform: translateY(0);
        }
        
        .btn-recovery:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-recovery.loading {
            background: linear-gradient(135deg, var(--sintia-accent) 0%, #8b5cf6 100%);
        }
        
        .btn-recovery.success {
            background: linear-gradient(135deg, var(--sintia-success) 0%, #059669 100%);
        }
        
        .btn-recovery.error {
            background: linear-gradient(135deg, var(--sintia-error) 0%, #dc2626 100%);
        }
        
        /* Mensajes dinámicos */
        .alert-dynamic {
            border-radius: 12px;
            border: none;
            font-weight: 500;
            animation: slideInDown 0.5s ease-out;
            padding: 1rem 1.25rem;
        }
        
        .alert-dynamic.error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border-left: 4px solid var(--sintia-error);
        }
        
        .alert-dynamic.success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
            border-left: 4px solid var(--sintia-success);
        }
        
        .alert-dynamic.info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border-left: 4px solid var(--sintia-secondary);
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
        
        /* Animación de shake para errores */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Enlaces */
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
        
        .recovery-link i {
            font-size: 1.1rem;
        }
        
        /* Footer */
        .recovery-footer {
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
        }
        
        /* Galería de fotos en el lado derecho */
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
        
        /* Progress indicator */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e5e7eb;
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
        
        .progress-step.active .progress-step-circle {
            background: linear-gradient(135deg, var(--sintia-secondary) 0%, var(--sintia-accent) 100%);
            border-color: var(--sintia-accent);
            color: white;
        }
        
        .progress-step.completed .progress-step-circle {
            background: var(--sintia-success);
            border-color: var(--sintia-success);
            color: white;
        }
        
        .progress-step-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
        }
        
        .progress-step.active .progress-step-label {
            color: var(--sintia-accent);
            font-weight: 600;
        }
        
        /* Loading spinner personalizado */
        .spinner-custom {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
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
            
            .recovery-card {
                max-width: 550px;
                margin: 1.5rem auto;
            }
        }
        
        @media (max-width: 768px) {
            .recovery-card {
                margin: 1rem;
                padding: 2rem;
            }
            
            .recovery-title {
                font-size: 1.75rem;
            }
            
            .recovery-icon {
                width: 60px;
                height: 60px;
            }
            
            .recovery-icon i {
                font-size: 1.5rem;
            }
            
            .progress-step-label {
                font-size: 0.65rem;
            }
        }
        
        @media (max-width: 480px) {
            .recovery-card {
                margin: 0.5rem;
                padding: 1.5rem;
                border-radius: 12px;
            }
            
            .recovery-title {
                font-size: 1.5rem;
            }
            
            .recovery-subtitle {
                font-size: 0.875rem;
            }
            
            .recovery-icon {
                width: 50px;
                height: 50px;
            }
            
            .recovery-icon i {
                font-size: 1.25rem;
            }
            
            .btn-recovery {
                font-size: 1rem;
                padding: 0.75rem 1.25rem;
            }
            
            .progress-step-label {
                font-size: 0.6rem;
            }
        }
    </style>

</head>

<body>
    <div class="login-container">
        <div class="vertical-center text-center">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2" id="login">
                        <div class="recovery-card">
                            <form id="recoveryForm" method="post" action="recuperar-clave-enviar-codigo.php" class="needs-validation" novalidate>

                            <?php include '../config-general/mensajes-informativos.php'; ?>

                                <!-- Contenedor para mensajes dinámicos -->
                                <div id="dynamicMessages" class="mb-3"></div>
                                
                                <!-- Header -->
                                <header class="recovery-header">
                                    <div class="recovery-icon">
                                        <i class="bi bi-shield-lock"></i>
                                    </div>
                                    <h1 class="recovery-title">Recuperar Contraseña</h1>
                                    <p class="recovery-subtitle">
                                        Ingresa tu usuario o correo electrónico registrado y te enviaremos un código de verificación para restablecer tu contraseña de forma segura.
                                    </p>
                                </header>
                                
                                <!-- Progress Steps -->
                                <div class="progress-steps">
                                    <div class="progress-step active">
                                        <div class="progress-step-circle">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div class="progress-step-label">Identificación</div>
                                    </div>
                                    <div class="progress-step">
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
                                
                                <!-- Input de Usuario/Email -->
                                <div class="form-floating mt-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="usuarioInput" 
                                            name="Usuario"
                                            placeholder="Usuario o Email" 
                                            value="<?php echo htmlspecialchars($usuario); ?>" 
                                            required
                                            autocomplete="username">
                                    </div>
                                    <div class="validation-feedback" id="usuarioValidation"></div>
                                </div>
                                
                                <!-- Información de seguridad -->
                                <div class="alert alert-dynamic info mt-4">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Proceso seguro:</strong> El código de verificación será enviado a tu correo electrónico registrado. Revisa tu bandeja de entrada y spam.
                                </div>
                                
                                <!-- Botón de envío -->
                                <div class="login-actions mt-4">
                                    <button id="recoveryBtn" class="w-100 btn btn-lg btn-recovery" type="submit">
                                        <span id="btnText">Enviar Código de Recuperación</span>
                                        <span id="btnSpinner" class="spinner-custom ms-2" style="display: none;"></span>
                                    </button>
                            </div>

                                <!-- Footer -->
                                <footer class="recovery-footer">
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

                                    <div class="text-center mt-4">
                                        <p class="mb-2 text-muted" style="font-size: 0.9rem;">
                                            ¿No tienes una cuenta institucional?
                                        </p>
                                        <a href="registro.php" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-building me-1"></i>
                                            Registrar Institución
                                        </a>
                            </div>
                                </footer>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Galería de fotos rotando en el lado derecho -->
        <div class="logo-container vertical-center photo-gallery">
            <div class="photo-slide active" style="background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2022&q=80');"></div>
            <div class="photo-slide" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');"></div>
        </div>
    </div>

    <?php 
    if (isset($_POST['usuariosEncontrados'])) {
        include 'compartido/modal-lista-usuarios.php';
    }
     ?>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script de recuperación de contraseña -->
    <script>
        $(document).ready(function() {
            console.log('Sistema de recuperación de contraseña iniciado');
            
            // Galería de fotos rotando
            let currentSlide = 0;
            const slides = $('.photo-slide');
            const totalSlides = slides.length;
            
            function showNextSlide() {
                slides.removeClass('active');
                currentSlide = (currentSlide + 1) % totalSlides;
                slides.eq(currentSlide).addClass('active');
            }
            
            // Cambiar imagen cada 5 segundos
            if (totalSlides > 0) {
                setInterval(showNextSlide, 5000);
            }
            
            // Validación en tiempo real del campo de usuario
            let validationTimeout;
            $('#usuarioInput').on('input', function() {
                const value = $(this).val().trim();
                const input = $(this);
                const feedback = $('#usuarioValidation');
                
                // Limpiar timeout anterior
                clearTimeout(validationTimeout);
                
                // Esperar 500ms después de que el usuario deje de escribir
                validationTimeout = setTimeout(function() {
                    if (value.length === 0) {
                        input.removeClass('is-valid is-invalid');
                        feedback.removeClass('show valid invalid');
                        return;
                    }
                    
                    // Validación básica
                    const isEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                    const isUsername = value.length >= 3;
                    
                    if (isEmail || isUsername) {
                        input.addClass('is-valid').removeClass('is-invalid');
                        feedback.addClass('show valid').removeClass('invalid');
                        feedback.html('<i class="bi bi-check-circle me-1"></i> Formato válido');
                    } else {
                        input.addClass('is-invalid').removeClass('is-valid');
                        feedback.addClass('show invalid').removeClass('valid');
                        feedback.html('<i class="bi bi-exclamation-circle me-1"></i> Ingresa un usuario válido (mínimo 3 caracteres) o un email');
                    }
                }, 500);
            });
            
            // Sistema de envío asíncrono del formulario
            $('#recoveryForm').on('submit', function(e) {
                e.preventDefault();
                console.log('Formulario de recuperación enviado');
                
                // Validar formulario
                const usuarioValue = $('#usuarioInput').val().trim();
                
                if (usuarioValue.length === 0) {
                    $('#usuarioInput').addClass('is-invalid');
                    $('#usuarioValidation').addClass('show invalid').removeClass('valid');
                    $('#usuarioValidation').html('<i class="bi bi-exclamation-circle me-1"></i> Este campo es requerido');
                    showMessage('Por favor ingresa tu usuario o correo electrónico.', 'error');
                    
                    // Shake animation
                    $('#recoveryForm').addClass('shake');
                    setTimeout(() => $('#recoveryForm').removeClass('shake'), 500);
                    return false;
                }
                
                // Obtener datos del formulario
                const formData = new FormData(this);
                formData.append('async', '1'); // Indicar que es una petición asíncrona
                
                // Cambiar estado del botón
                setButtonState('loading', 'Enviando solicitud...');
                
                // Limpiar mensajes anteriores
                clearMessages();
                
                // Realizar petición AJAX
                $.ajax({
                    url: 'recuperar-clave-enviar-codigo.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    timeout: 30000,
                    
                    beforeSend: function() {
                        console.log('Enviando petición de recuperación...');
                        setButtonState('loading', 'Verificando usuario...');
                    },
                    
                    success: function(response) {
                        console.log('Respuesta recibida:', response);
                        
                        if (response.success) {
                            // Verificar si hay múltiples usuarios
                            if (response.multipleUsers && response.usuarios) {
                                // Mostrar modal de selección de usuarios
                                setButtonState('success', 'Usuarios encontrados');
                                showMessage('Se encontraron múltiples usuarios. Por favor selecciona uno.', 'info');
                                
                                // Crear y mostrar modal dinámicamente
                                createMultipleUsersModal(response.usuarios);
                                
                                // Restaurar botón
                                setTimeout(() => resetButton(), 2000);
                            } else {
                                // Código enviado exitosamente
                                setButtonState('success', '¡Código enviado!');
                                
                                // Mostrar mensaje con email
                                const emailMasked = response.usuarioEmail ? maskEmail(response.usuarioEmail) : 'tu correo';
                                showMessage(
                                    `Código de verificación enviado a ${emailMasked}. Revisa tu bandeja de entrada.`, 
                                    'success'
                                );
                                
                                // Redirigir después de un breve delay
                                setTimeout(() => {
                                    // DEBUG: Mostrar qué datos se enviarán
                                    console.log('=== DATOS QUE SE ENVIARÁN ===');
                                    console.log('Response completo:', response);
                                    console.log('datosCodigo:', response.datosCodigo);
                                    console.log('idRegistro:', response.datosCodigo ? response.datosCodigo.idRegistro : 'NO ENCONTRADO');
                                    
                                    // Verificar que los datos críticos existan
                                    if (!response.datosCodigo || !response.datosCodigo.idRegistro) {
                                        console.error('❌ ERROR: No se recibió idRegistro del servidor');
                                        console.error('Estructura recibida:', response);
                                        showMessage('Error: No se pudo obtener el código de registro. Intenta nuevamente.', 'error');
                                        resetButton();
                                        return;
                                    }
                                    
                                    const jsonData = JSON.stringify(response);
                                    const base64Data = btoa(jsonData);
                                    
                                    console.log('JSON a enviar:', jsonData);
                                    console.log('Base64:', base64Data.substring(0, 50) + '...');
                                    
                                    // Crear formulario para enviar datos del código
                                    const redirectForm = $('<form>', {
                                        method: 'POST',
                                        action: 'recuperar-clave-validar-codigo.php'
                                    }).append(
                                        $('<input>', {
                                            type: 'hidden',
                                            name: 'datosUsuario',
                                            value: base64Data
                                        })
                                    );
                                    
                                    $('body').append(redirectForm);
                                    redirectForm.submit();
                                }, 2000);
                            }
                        } else {
                            // Error en el envío
                            setButtonState('error', 'Error al enviar');
                            showMessage(response.message || 'No se pudo enviar el código. Verifica tus datos.', 'error');
                            
                            // Shake animation
                            $('#recoveryForm').addClass('shake');
                            setTimeout(() => $('#recoveryForm').removeClass('shake'), 500);
                            
                            // Restaurar botón después de 3 segundos
                            setTimeout(() => resetButton(), 3000);
                        }
                    },
                    
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', status, error);
                        
                        let errorMessage = 'Error de conexión. Verifica tu internet.';
                        
                        if (status === 'timeout') {
                            errorMessage = 'La petición tardó demasiado. Intenta nuevamente.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Error del servidor. Contacta soporte técnico.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Servicio no disponible. Intenta más tarde.';
                        }
                        
                        setButtonState('error', 'Error de conexión');
                        showMessage(errorMessage, 'error');
                        
                        // Shake animation
                        $('#recoveryForm').addClass('shake');
                        setTimeout(() => $('#recoveryForm').removeClass('shake'), 500);
                        
                        // Restaurar botón después de 3 segundos
                        setTimeout(() => resetButton(), 3000);
                    }
                });
                
                return false;
            });
            
            // Funciones auxiliares
            function setButtonState(state, text) {
                const btn = $('#recoveryBtn');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                btn.prop('disabled', true);
                btn.removeClass('loading success error');
                
                if (state === 'loading') {
                    btn.addClass('loading');
                    btnSpinner.show();
                } else if (state === 'success') {
                    btn.addClass('success');
                    btnSpinner.hide();
                } else if (state === 'error') {
                    btn.addClass('error');
                    btnSpinner.hide();
                }
                
                btnText.text(text);
            }
            
            function resetButton() {
                const btn = $('#recoveryBtn');
                const btnText = $('#btnText');
                const btnSpinner = $('#btnSpinner');
                
                btn.prop('disabled', false);
                btn.removeClass('loading success error');
                btnSpinner.hide();
                btnText.text('Enviar Código de Recuperación');
            }
            
            function showMessage(message, type) {
                const iconMap = {
                    error: 'exclamation-triangle',
                    success: 'check-circle',
                    info: 'info-circle'
                };
                
                const messageHtml = `
                    <div class="alert alert-dynamic ${type}" role="alert">
                        <i class="bi bi-${iconMap[type]} me-2"></i>
                        ${message}
                    </div>
                `;
                
                $('#dynamicMessages').html(messageHtml);
                
                // Auto-ocultar después de 8 segundos
                setTimeout(() => {
                    $('#dynamicMessages').fadeOut();
                }, 8000);
            }
            
            function clearMessages() {
                $('#dynamicMessages').empty().show();
            }
            
            function maskEmail(email) {
                const [user, domain] = email.split('@');
                if (user.length <= 3) {
                    return `${user[0]}***@${domain}`;
                }
                return `${user.substring(0, 3)}***@${domain}`;
            }
            
            function createMultipleUsersModal(usuarios) {
                // Crear modal dinámicamente
                const modalHtml = `
                    <div class="modal fade modal-usuarios-moderno" id="modalUsuariosMultiples" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
                                <div class="modal-header" style="background: linear-gradient(135deg, #41c4c4 0%, #6017dc 100%); color: white; border-radius: 20px 20px 0 0; padding: 1.5rem 2rem; border: none;">
                                    <h4 class="modal-title" style="font-weight: 700; font-size: 1.5rem; margin: 0;">
                                        <i class="bi bi-people-fill me-2"></i>
                                        Múltiples Usuarios Encontrados
                                    </h4>
                                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="filter: brightness(0) invert(1);"></button>
                                </div>
                                
                                <div class="modal-body" style="padding: 2rem;">
                                    <p class="info-text" style="color: #666; font-size: 1rem; margin-bottom: 1.5rem; text-align: center;">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Se encontraron varios usuarios. Por favor, selecciona el usuario para recuperar la contraseña.
                                    </p>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover" style="border-collapse: separate; border-spacing: 0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                                            <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                                <tr>
                                                    <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;"></th>
                                                    <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Usuario</th>
                                                    <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Email</th>
                                                    <th style="border: none; padding: 1rem; font-weight: 600; color: #495057;">Documento</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyUsuarios">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 1.5rem 2rem; background: #f8f9fa; border-radius: 0 0 20px 20px;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Cancelar
                                    </button>
                                    <button type="button" class="btn" id="btnContinuarUsuario" style="background: linear-gradient(135deg, #41c4c4 0%, #6017dc 100%); border: none; color: white; padding: 0.75rem 2rem; border-radius: 12px; font-weight: 600; box-shadow: 0 4px 15px rgba(65, 196, 196, 0.3);">
                                        <i class="bi bi-send me-2"></i>
                                        Continuar con Recuperación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Eliminar modal previo si existe
                $('#modalUsuariosMultiples').remove();
                
                // Agregar modal al body
                $('body').append(modalHtml);
                
                // Agregar filas de usuarios
                const tbody = $('#tbodyUsuarios');
                usuarios.forEach((usuario, index) => {
                    const row = `
                        <tr style="cursor: pointer; transition: all 0.3s ease;" data-usuario-id="${usuario.id_nuevo}" class="usuario-row">
                            <td style="padding: 1rem; vertical-align: middle;">
                                <input type="radio" name="usuarioSeleccionado" value="${usuario.id_nuevo}" id="usuario_${index}" style="width: 20px; height: 20px;">
                            </td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                <i class="bi bi-person me-1"></i>
                                <strong>${usuario.uss_usuario || 'N/A'}</strong>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                <i class="bi bi-envelope me-1"></i>
                                ${usuario.uss_email || 'N/A'}
                            </td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                ${usuario.uss_documento || 'N/A'}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                // Evento click en filas
                $('.usuario-row').on('click', function() {
                    const radio = $(this).find('input[type="radio"]');
                    radio.prop('checked', true);
                    $('.usuario-row').css('background-color', '');
                    $(this).css('background-color', 'rgba(65, 196, 196, 0.1)');
                });
                
                // Evento botón continuar
                $('#btnContinuarUsuario').on('click', function() {
                    const usuarioSeleccionado = $('input[name="usuarioSeleccionado"]:checked').val();
                    
                    if (!usuarioSeleccionado) {
                        showMessage('Por favor selecciona un usuario antes de continuar.', 'error');
                        return;
                    }
                    
                    // Cerrar modal
                    $('#modalUsuariosMultiples').modal('hide');
                    
                    // Mostrar loading
                    setButtonState('loading', 'Enviando código...');
                    
                    // Enviar petición para el usuario seleccionado
                    $.ajax({
                        url: 'recuperar-clave-enviar-codigo.php',
                        type: 'POST',
                        data: {
                            usuarioId: usuarioSeleccionado,
                            async: '1'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && !response.multipleUsers) {
                                setButtonState('success', '¡Código enviado!');
                                
                                const emailMasked = response.usuarioEmail ? maskEmail(response.usuarioEmail) : 'tu correo';
                                showMessage(
                                    `Código enviado a ${emailMasked}. Revisa tu bandeja de entrada.`, 
                                    'success'
                                );
                                
                                // Redirigir
                                setTimeout(() => {
                                    const redirectForm = $('<form>', {
                                        method: 'POST',
                                        action: 'recuperar-clave-validar-codigo.php'
                                    }).append(
                                        $('<input>', {
                                            type: 'hidden',
                                            name: 'datosUsuario',
                                            value: btoa(JSON.stringify(response))
                                        })
                                    );
                                    
                                    $('body').append(redirectForm);
                                    redirectForm.submit();
                                }, 2000);
                            } else {
                                setButtonState('error', 'Error');
                                showMessage(response.message || 'Error al enviar el código', 'error');
                                setTimeout(() => resetButton(), 3000);
                            }
                        },
                        error: function() {
                            setButtonState('error', 'Error de conexión');
                            showMessage('Error de conexión. Intenta nuevamente.', 'error');
                            setTimeout(() => resetButton(), 3000);
                        }
                    });
                });
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('modalUsuariosMultiples'));
                modal.show();
            }
        });
    </script>

</body>

</html>
