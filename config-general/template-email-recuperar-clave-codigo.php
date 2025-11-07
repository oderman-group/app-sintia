<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=isset($data['asunto']) ? $data['asunto'] : 'Código de Recuperación - SINTIA'?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .email-header {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header img {
            width: 120px;
            margin-bottom: 20px;
        }
        
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        
        .email-body {
            background: #ffffff;
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .code-container {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 2px dashed #ef4444;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        
        .code-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .verification-code {
            font-size: 48px;
            font-weight: 700;
            color: #ef4444;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
        }
        
        .code-expiry {
            font-size: 14px;
            color: #ef4444;
            margin-top: 15px;
            font-weight: 600;
        }
        
        .security-alert {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        
        .security-alert h4 {
            color: #991b1b;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .security-alert p {
            font-size: 14px;
            color: #7f1d1d;
            margin: 0;
            line-height: 1.5;
        }
        
        .info-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        
        .info-box p {
            font-size: 14px;
            color: #92400e;
            margin: 0;
            line-height: 1.5;
        }
        
        .help-section {
            background: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .help-section h3 {
            font-size: 16px;
            color: #1f2937;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .help-section p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .contact-info {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .contact-button {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .contact-button:hover {
            transform: translateY(-2px);
        }
        
        .email-footer {
            background: #1f2937;
            padding: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .email-footer a {
            color: #ef4444;
            text-decoration: none;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #9ca3af;
            text-decoration: none;
            font-size: 20px;
        }
        
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 25px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            
            .email-header,
            .email-body {
                padding: 30px 20px;
            }
            
            .verification-code {
                font-size: 36px;
                letter-spacing: 5px;
            }
            
            .contact-info {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <img src="https://main.plataformasintia.com/app-sintia/sintia-color.png" alt="SINTIA Logo">
            <h1>🔐 Recuperación de Contraseña</h1>
            <p>Solicitud de restablecimiento de acceso</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                ¡Hola <?php echo isset($data['usuario_nombre']) ? $data['usuario_nombre'] : 'Usuario'; ?>!
            </div>
            
            <p class="message">
                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en <strong>SINTIA</strong>. 
                Para continuar con el proceso de recuperación, utiliza el siguiente código de verificación:
            </p>
            
            <div class="code-container">
                <div class="code-label">Tu Código de Verificación</div>
                <div class="verification-code">
                    <?php 
                    echo isset($data['codigo']) ? $data['codigo'] : '000000'; 
                    ?>
                </div>
                <div class="code-expiry">
                    ⏱️ Expira en 10 minutos
                </div>
            </div>
            
            <div class="security-alert">
                <h4>
                    <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="fill: #ef4444;">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    ¡Alerta de Seguridad!
                </h4>
                <p>
                    <strong>Si NO solicitaste este código:</strong> Tu cuenta puede estar en riesgo. 
                    Te recomendamos cambiar tu contraseña inmediatamente o contactar con nuestro equipo de soporte.
                </p>
            </div>
            
            <div class="info-box">
                <p>
                    <strong>ℹ️ Información:</strong> Este código es único, de un solo uso y expirará en 10 minutos. 
                    Solo puede ser utilizado para restablecer la contraseña de tu cuenta.
                </p>
            </div>
            
            <p class="message">
                Ingresa este código en la página de recuperación para continuar. Si el código ha expirado 
                o necesitas uno nuevo, puedes solicitar otro desde la misma página de recuperación.
            </p>
            
            <div class="divider"></div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h3>¿Necesitas ayuda?</h3>
                <p>
                    Si no reconoces esta actividad o tienes problemas para recuperar tu cuenta, 
                    nuestro equipo de soporte está disponible para asistirte.
                </p>
                <div class="contact-info">
                    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, necesito ayuda para recuperar mi cuenta en SINTIA" 
                       class="contact-button" target="_blank">
                        📱 WhatsApp
                    </a>
                    <a href="mailto:info@plataformasintia.com?subject=Ayuda - Recuperación de Cuenta" 
                       class="contact-button">
                        ✉️ Email
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>SINTIA - Plataforma Educativa</strong></p>
            <p>Transformando la educación con tecnología</p>
            
            <div class="social-links">
                <a href="https://facebook.com/plataformasintia" target="_blank">Facebook</a> | 
                <a href="https://twitter.com/plataformasintia" target="_blank">Twitter</a> | 
                <a href="https://instagram.com/platsintia" target="_blank">Instagram</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px;">
                Este correo fue enviado a <strong><?php echo isset($data['usuario_email']) ? $data['usuario_email'] : ''; ?></strong>
                <br>
                Si no solicitaste este código, puedes ignorar este mensaje de forma segura.
            </p>
            
            <p style="margin-top: 15px; font-size: 11px; color: #6b7280;">
                © <?php echo date('Y'); ?> SINTIA. Todos los derechos reservados.
                <br>
                <a href="https://sintia.co/blog/" style="color: #667eea;">Blog</a> | 
                <a href="https://plataformasintia.com/terminos" style="color: #ef4444;">Términos de Servicio</a>
            </p>
        </div>
    </div>
</body>
</html>
