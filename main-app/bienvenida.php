<?php
// Configuración directa sin redirecciones
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");

$Plataforma = new Plataforma;

// Obtener datos del registro desde SESSION (más seguro y directo)
$data = isset($_SESSION['datosRegistroCompletado']) ? $_SESSION['datosRegistroCompletado'] : [];

// Si no hay datos en session, intentar desde GET (para compatibilidad)
if (empty($data) && isset($_GET['inf'])) {
    $data = unserialize(base64_decode($_GET['inf']));
}

// Validar que haya datos
if (empty($data)) {
    error_log("BIENVENIDA: No hay datos de registro");
    header("Location: index.php");
    exit();
}

// Log de los datos recibidos
error_log("========================================");
error_log("PÁGINA BIENVENIDA - DATOS RECIBIDOS:");
error_log("========================================");
error_log(print_r($data, true));
error_log("========================================");

// Extraer datos
$nombreUsuario = isset($data['usuario_nombre']) ? $data['usuario_nombre'] : 'Usuario';
$email = isset($data['usuario_email']) ? $data['usuario_email'] : '';
$usuario = isset($data['usuario_usuario']) ? $data['usuario_usuario'] : '';
$clave = isset($data['usuario_clave']) ? $data['usuario_clave'] : '12345678';
$institucionNombre = isset($data['institucion_nombre']) ? $data['institucion_nombre'] : 'tu institución';

// Log de datos extraídos
error_log("Datos extraídos para mostrar:");
error_log("- Nombre: " . $nombreUsuario);
error_log("- Email: " . $email);
error_log("- Usuario: " . $usuario);
error_log("- Institución: " . $institucionNombre);

// Limpiar session después de obtener los datos
unset($_SESSION['datosRegistroCompletado']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../config-general/assets-login-2023/img/logo.png" type="image/x-icon">
    <title>¡Bienvenido a SINTIA!</title>
    
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            overflow-x: hidden;
        }
        
        .welcome-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }
        
        .welcome-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            position: relative;
        }
        
        .success-icon-container {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 3rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .success-icon-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: bounceIn 0.8s ease-out;
        }
        
        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-icon i {
            font-size: 4rem;
            color: #10b981;
            animation: checkmark 0.8s ease-out 0.3s both;
        }
        
        @keyframes checkmark {
            0% {
                transform: scale(0) rotate(-45deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.2) rotate(5deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }
        
        .success-icon-container h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .success-icon-container p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }
        
        .welcome-content {
            padding: 3rem 2.5rem;
        }
        
        .welcome-message {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .welcome-message h2 {
            color: #1f2937;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .welcome-message p {
            color: #6b7280;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .credentials-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
        }
        
        .credentials-title {
            text-align: center;
            color: #667eea;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .credential-item {
            background: white;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .credential-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }
        
        .credential-label {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .credential-value {
            color: #1f2937;
            font-weight: 700;
            font-size: 1.1rem;
            font-family: 'Courier New', monospace;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            background: #764ba2;
            transform: scale(1.05);
        }
        
        .copy-btn.copied {
            background: #10b981;
        }
        
        .email-notice {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            display: flex;
            gap: 1rem;
        }
        
        .email-notice i {
            font-size: 2rem;
            color: #f59e0b;
            flex-shrink: 0;
        }
        
        .email-notice-content h4 {
            color: #92400e;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .email-notice-content p {
            color: #78350f;
            margin: 0;
            line-height: 1.5;
        }
        
        .btn-access {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 1rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        
        .btn-access:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
            color: white;
        }
        
        .btn-access i {
            font-size: 1.5rem;
        }
        
        .next-steps {
            background: #f9fafb;
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .next-steps h3 {
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .step-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: white;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .step-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .step-content h4 {
            color: #1f2937;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .step-content p {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .help-section {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #e5e7eb;
            margin-top: 2rem;
        }
        
        .help-section h4 {
            color: #1f2937;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        .help-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .help-link {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .help-link.whatsapp {
            background: #25d366;
            color: white;
        }
        
        .help-link.whatsapp:hover {
            background: #128C7E;
            color: white;
            transform: translateY(-2px);
        }
        
        .help-link.email {
            background: #667eea;
            color: white;
        }
        
        .help-link.email:hover {
            background: #764ba2;
            color: white;
            transform: translateY(-2px);
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #667eea;
            position: absolute;
            animation: confetti-fall 3s linear;
        }
        
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        @media (max-width: 768px) {
            .success-icon {
                width: 100px;
                height: 100px;
            }
            
            .success-icon i {
                font-size: 3rem;
            }
            
            .success-icon-container h1 {
                font-size: 2rem;
            }
            
            .welcome-content {
                padding: 2rem 1.5rem;
            }
            
            .btn-access {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container animate__animated animate__fadeIn">
        <div class="welcome-card">
            <!-- Success Header -->
            <div class="success-icon-container">
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h1 class="animate__animated animate__fadeInUp">¡Registro Exitoso!</h1>
                <p class="animate__animated animate__fadeInUp animate__delay-1s">Tu cuenta ha sido creada correctamente</p>
            </div>
            
            <!-- Content -->
            <div class="welcome-content">
                <div class="welcome-message">
                    <h2>¡Bienvenido a SINTIA, <?=htmlspecialchars($nombreUsuario)?>!</h2>
                    <p>
                        Nos complace que <strong><?=htmlspecialchars($institucionNombre)?></strong> forme parte 
                        de la familia SINTIA. Estás a punto de transformar la gestión educativa de tu institución.
                    </p>
                </div>
                
                <!-- Email Notice -->
                <div class="email-notice animate__animated animate__fadeInUp">
                    <i class="bi bi-envelope-check-fill"></i>
                    <div class="email-notice-content">
                        <h4>Revisa tu correo electrónico</h4>
                        <p>
                            Hemos enviado un correo a <strong><?=htmlspecialchars($email)?></strong> 
                            con información detallada sobre tu cuenta, guías de inicio rápido y recursos útiles para comenzar.
                        </p>
                    </div>
                </div>
                
                <!-- Credentials Box -->
                <div class="credentials-box animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="credentials-title">
                        <i class="bi bi-key-fill"></i>
                        Tus Credenciales de Acceso
                    </div>
                    
                    <div class="credential-item">
                        <div class="credential-label">
                            <i class="bi bi-person-badge"></i>
                            Usuario:
                        </div>
                        <div class="credential-value">
                            <span id="username"><?=htmlspecialchars($usuario)?></span>
                            <button class="copy-btn" onclick="copiarTexto('username', this)">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="credential-item">
                        <div class="credential-label">
                            <i class="bi bi-lock-fill"></i>
                            Contraseña:
                        </div>
                        <div class="credential-value">
                            <span id="password"><?=htmlspecialchars($clave)?></span>
                            <button class="copy-btn" onclick="copiarTexto('password', this)">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3 mb-0" style="border-radius: 10px;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Importante:</strong> Cambia tu contraseña en tu primer inicio de sesión por seguridad.
                    </div>
                </div>
                
                <!-- Access Button -->
                <div class="text-center my-4">
                    <a href="index.php" class="btn-access animate__animated animate__pulse animate__infinite animate__slow">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Acceder a Mi Cuenta
                    </a>
                </div>
                
                <!-- Next Steps -->
                <div class="next-steps animate__animated animate__fadeInUp animate__delay-2s">
                    <h3><i class="bi bi-list-check me-2"></i>Próximos Pasos</h3>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Inicia Sesión</h4>
                            <p>Usa las credenciales que te proporcionamos para acceder a la plataforma</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Completa tu Perfil</h4>
                            <p>Actualiza la información de tu institución y configura las preferencias</p>
                        </div>
                        </div>

                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Configura los Módulos</h4>
                            <p>Personaliza los módulos seleccionados según las necesidades de tu institución</p>
                        </div>
                        </div>

                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h4>Explora el Sistema</h4>
                            <p>Descubre todas las funcionalidades y herramientas disponibles en SINTIA</p>
                        </div>
                    </div>
                </div>
                
                <!-- Help Section -->
                <div class="help-section">
                    <h4>¿Necesitas ayuda para comenzar?</h4>
                    <div class="help-links">
                        <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, acabo de registrarme en SINTIA y necesito ayuda para comenzar" 
                           class="help-link whatsapp" target="_blank">
                            <i class="bi bi-whatsapp"></i>
                            WhatsApp
                        </a>
                        <a href="mailto:info@plataformasintia.com?subject=Ayuda - Nuevo Registro SINTIA" 
                           class="help-link email">
                            <i class="bi bi-envelope-fill"></i>
                            Email Soporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <p class="text-center mt-4 text-white">
            © <?=date('Y')?> SINTIA - Plataforma Educativa
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para copiar texto al portapapeles
        function copiarTexto(elementId, btn) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            // Copiar al portapapeles
            navigator.clipboard.writeText(text).then(() => {
                // Cambiar icono temporalmente
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2"></i>';
                btn.classList.add('copied');
                
                // Mostrar tooltip
                const tooltip = document.createElement('div');
                tooltip.textContent = '¡Copiado!';
                tooltip.style.cssText = 'position: absolute; background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.875rem; top: -40px; right: 0; animation: fadeInOut 2s ease;';
                btn.style.position = 'relative';
                btn.appendChild(tooltip);
                
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('copied');
                    tooltip.remove();
                }, 2000);
            }).catch(err => {
                console.error('Error al copiar:', err);
                alert('No se pudo copiar. Por favor copia manualmente.');
            });
        }
        
        // Crear confetti al cargar
        function createConfetti() {
            const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444'];
            const container = document.body;
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDelay = Math.random() * 0.5 + 's';
                    confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    container.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 3000);
                }, i * 30);
            }
        }
        
        // Ejecutar confetti al cargar
        window.addEventListener('load', () => {
            setTimeout(createConfetti, 500);
        });
        
        console.log('✅ Página de bienvenida cargada');
        console.log('Usuario:', '<?=$usuario?>');
    </script>
</body>
</html>
