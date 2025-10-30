<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Recibida - <?= $datosInfo['info_nombre']; ?></title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 15s infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }
        
        .confirmation-container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            width: 100%;
        }
        
        .confirmation-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
        
        .success-icon {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-icon i {
            font-size: 48px;
            color: #27ae60;
            animation: checkmark 0.5s ease-out 0.5s both;
        }
        
        @keyframes checkmark {
            from {
                transform: scale(0) rotate(-45deg);
            }
            to {
                transform: scale(1) rotate(0deg);
            }
        }
        
        .header-section h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
        }
        
        .header-section p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 16px;
            position: relative;
        }
        
        .content-section {
            padding: 40px 30px;
        }
        
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .info-row:first-child {
            padding-top: 0;
        }
        
        .info-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-label i {
            color: #667eea;
            width: 20px;
            text-align: center;
        }
        
        .info-value {
            color: #212529;
            font-size: 15px;
            font-weight: 600;
        }
        
        .message-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border: 2px solid #ffc107;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        
        .message-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            background: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .message-content h3 {
            color: #856404;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .message-content p {
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            flex: 1;
            min-width: 200px;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .countdown {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .countdown-number {
            font-weight: 700;
            color: #667eea;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .footer-note i {
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .confirmation-card {
                border-radius: 16px;
            }
            
            .header-section {
                padding: 30px 20px;
            }
            
            .header-section h1 {
                font-size: 24px;
            }
            
            .content-section {
                padding: 30px 20px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                min-width: 100%;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Partículas de fondo -->
    <div class="particles" id="particles"></div>
    
    <div class="confirmation-container">
        <div class="confirmation-card">
            <!-- Header -->
            <div class="header-section">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1>¡Solicitud Recibida Exitosamente!</h1>
                <p>Tu solicitud de admisión ha sido registrada correctamente</p>
            </div>
            
            <!-- Content -->
            <div class="content-section">
                <!-- Información de la solicitud -->
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-hashtag"></i>
                            Número de Solicitud
                        </span>
                        <span class="info-value">#<?= str_pad($newId, 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-user"></i>
                            Estudiante
                        </span>
                        <span class="info-value"><?= htmlspecialchars($nombreCompleto); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-id-card"></i>
                            Documento
                        </span>
                        <span class="info-value"><?= htmlspecialchars($_POST['documento']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-envelope"></i>
                            Email de Contacto
                        </span>
                        <span class="info-value"><?= htmlspecialchars($_POST['email']); ?></span>
                    </div>
                </div>
                
                <!-- Mensaje importante -->
                <div class="message-box">
                    <div class="message-icon">
                        <i class="fas fa-info"></i>
                    </div>
                    <div class="message-content">
                        <h3>¿Qué sigue ahora?</h3>
                        <p>
                            Hemos enviado un correo electrónico a <strong><?= htmlspecialchars($_POST['email']); ?></strong> 
                            con los detalles de tu solicitud. En breve, nuestro equipo de admisiones revisará tu información 
                            y se pondrá en contacto contigo para los siguientes pasos del proceso.
                        </p>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="actions">
                    <a href="<?= $urlConsulta; ?>" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Consultar Estado de Solicitud
                    </a>
                    <a href="index.php?idInst=<?= $_REQUEST['idInst']; ?>" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Volver al Inicio
                    </a>
                </div>
                
                <!-- Countdown -->
                <div class="countdown">
                    <i class="fas fa-clock"></i>
                    Serás redirigido automáticamente en <span class="countdown-number" id="countdown">10</span> segundos...
                </div>
                
                <!-- Footer note -->
                <div class="footer-note">
                    <i class="fas fa-shield-alt"></i>
                    Tu información está segura y protegida.<br>
                    Si no recibiste el correo, revisa tu carpeta de spam o correo no deseado.
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Generar partículas de fondo
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        
        // Countdown y redirección automática
        let timeLeft = 10;
        const countdownElement = document.getElementById('countdown');
        const redirectUrl = '<?= $urlConsulta; ?>';
        
        const countdownInterval = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                window.location.href = redirectUrl;
            }
        }, 1000);
        
        // Inicializar partículas al cargar
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
        });
    </script>
</body>
</html>

