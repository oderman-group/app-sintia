<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=isset($data['asunto']) ? $data['asunto'] : 'Comunicado de SINTIA'?></title>
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
            white-space: pre-wrap;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }
        
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            
            .email-header, .email-body, .footer {
                padding: 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <img src="https://plataformasintia.com/images/logo-white.png" alt="SINTIA Logo">
            <h1>Comunicado de SINTIA</h1>
            <p>Mensaje importante para ti</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                ¡Hola <?php echo isset($data['usuario_nombre']) ? htmlspecialchars($data['usuario_nombre']) : 'Usuario'; ?>!
            </div>
            
            <p class="message">
                <?php echo isset($data['mensaje']) ? nl2br(htmlspecialchars($data['mensaje'])) : 'No hay mensaje disponible.'; ?>
            </p>
            
            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #9ca3af; text-align: center;">
                Este es un mensaje automático de la plataforma SINTIA. Por favor, no respondas a este correo.
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>SINTIA</strong><br>
                Plataforma Educativa Integral
            </p>
            <p>
                <a href="https://plataformasintia.com">www.plataformasintia.com</a>
            </p>
            <p style="font-size: 12px; color: #9ca3af; margin-top: 20px;">
                © <?php echo date('Y'); ?> SINTIA. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>

