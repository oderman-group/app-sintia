<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=isset($data['asunto']) ? $data['asunto'] : 'Código de Verificación - SINTIA'?></title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border: 2px dashed #667eea;
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
            color: #667eea;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
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
            <img src="https://plataformasintia.com/images/logo-white.png" alt="SINTIA Logo">
            <h1>Verifica tu correo electrónico</h1>
            <p>Estás a un paso de completar tu registro</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                ¡Hola <?php echo isset($data['usuario_nombre']) ? $data['usuario_nombre'] : 'Usuario'; ?>!
            </div>
            
            <p class="message">
                Gracias por registrarte en <strong>SINTIA</strong>, la plataforma educativa que transformará 
                la gestión de tu institución. Para completar tu registro, necesitamos verificar tu correo electrónico.
            </p>
            
            <div class="code-container">
                <div class="code-label">Tu Código de Verificación</div>
                <div class="verification-code">
                    <?php 
                    // La clase Notificacion agrega el código al array $data
                    echo isset($data['codigo']) ? $data['codigo'] : '000000'; 
                    ?>
                </div>
                <div class="code-expiry">
                    ⏱️ Expira en 10 minutos
                </div>
            </div>
            
            <div class="info-box">
                <p>
                    <strong>⚠️ Importante:</strong> Este código es único y solo puede usarse una vez. 
                    Si no solicitaste este código, puedes ignorar este correo de forma segura.
                </p>
            </div>
            
            <p class="message">
                Ingresa este código en la página de registro para continuar. Si el código ha expirado, 
                puedes solicitar uno nuevo desde la misma página.
            </p>
            
            <div class="divider"></div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h3>¿Necesitas ayuda?</h3>
                <p>
                    Si tienes algún problema con el proceso de registro o no recibiste tu código, 
                    nuestro equipo de soporte está aquí para ayudarte.
                </p>
                <div class="contact-info">
                    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, necesito ayuda con mi registro en SINTIA" 
                       class="contact-button" target="_blank">
                        📱 WhatsApp
                    </a>
                    <a href="mailto:info@plataformasintia.com" 
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
                <a href="https://instagram.com/plataformasintia" target="_blank">Instagram</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px;">
                Este correo fue enviado a <strong><?php echo isset($data['usuario_email']) ? $data['usuario_email'] : ''; ?></strong>
                <br>
                Si no solicitaste este código, puedes ignorar este mensaje.
            </p>
            
            <p style="margin-top: 15px; font-size: 11px; color: #6b7280;">
                © <?php echo date('Y'); ?> SINTIA. Todos los derechos reservados.
                <br>
                <a href="https://plataformasintia.com/privacidad" style="color: #667eea;">Política de Privacidad</a> | 
                <a href="https://plataformasintia.com/terminos" style="color: #667eea;">Términos de Servicio</a>
            </p>
        </div>
    </div>
</body>
</html>

