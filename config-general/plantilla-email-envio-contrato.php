<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envío de Contrato</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
        }
        
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 50px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .email-header::before {
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
        
        .document-icon {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .document-icon svg {
            width: 60px;
            height: 60px;
            fill: #667eea;
        }
        
        .email-header h1 {
            color: white;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 18px;
            position: relative;
            z-index: 1;
        }
        
        .email-body {
            padding: 40px 35px;
        }
        
        .welcome-message {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .welcome-message h2 {
            color: #1f2937;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .welcome-message p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .institution-name {
            color: #667eea;
            font-weight: 700;
        }
        
        .contract-info {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .contract-info-title {
            text-align: center;
            color: #667eea;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .contract-detail {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
        }
        
        .contract-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }
        
        .contract-value {
            color: #1f2937;
            font-weight: 700;
            font-size: 16px;
        }
        
        .info-notice {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .info-notice h4 {
            color: #1e40af;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .info-notice p {
            color: #1e3a8a;
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }
        
        .help-section {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        
        .help-section h4 {
            color: #1f2937;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .help-section p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        
        .email-footer {
            background: #1f2937;
            padding: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            line-height: 1.8;
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
            font-size: 14px;
        }
        
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 25px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 10px;
            }
            
            .email-header {
                padding: 35px 20px;
            }
            
            .email-header h1 {
                font-size: 26px;
            }
            
            .email-body {
                padding: 25px 20px;
            }
            
            .contract-info {
                padding: 20px;
            }
            
            .contract-detail {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="document-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <h1>📄 Contrato Adjunto</h1>
            <p>Documento institucional</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="welcome-message">
                <h2>Hola, <?=isset($data['usuario_nombre']) ? htmlspecialchars($data['usuario_nombre']) : 'Usuario'?>!</h2>
                <p>
                    Te enviamos el contrato de <span class="institution-name"><?=isset($data['institucion_nombre']) ? htmlspecialchars($data['institucion_nombre']) : 'la institución'?></span> 
                    como archivo adjunto en este correo.
                </p>
            </div>
            
            <!-- Contract Info -->
            <div class="contract-info">
                <div class="contract-info-title">
                    <svg viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" style="fill: #667eea;">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" fill="none"/>
                    </svg>
                    Información del Documento
                </div>
                
                <div class="contract-detail">
                    <div class="contract-label">Institución:</div>
                    <div class="contract-value"><?=isset($data['institucion_nombre']) ? htmlspecialchars($data['institucion_nombre']) : 'N/A'?></div>
                </div>
                
                <div class="contract-detail">
                    <div class="contract-label">Siglas:</div>
                    <div class="contract-value"><?=isset($data['institucion_siglas']) ? htmlspecialchars($data['institucion_siglas']) : 'N/A'?></div>
                </div>
                
                <div class="contract-detail">
                    <div class="contract-label">Año:</div>
                    <div class="contract-value"><?=isset($data['institucion_agno']) ? htmlspecialchars($data['institucion_agno']) : date('Y')?></div>
                </div>
            </div>
            
            <!-- Info Notice -->
            <div class="info-notice">
                <h4>📎 Archivo Adjunto</h4>
                <p>
                    El contrato se encuentra adjunto a este correo electrónico. Por favor, descárgalo y revísalo cuidadosamente. 
                    Si tienes alguna pregunta o necesitas alguna aclaración, no dudes en contactarnos.
                </p>
            </div>
            
            <div class="divider"></div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h4>¿Necesitas Ayuda?</h4>
                <p>
                    Si tienes alguna pregunta sobre el contrato o necesitas asistencia, puedes contactar directamente 
                    con nuestro equipo de soporte a través de los canales oficiales.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>SINTIA - Plataforma Educativa</strong></p>
            <p>Transformando la educación con tecnología</p>
            
            <div class="social-links">
                <a href="https://facebook.com/plataformasintia" target="_blank">Facebook</a> | 
                <a href="https://twitter.com/plataformasintia" target="_blank">Twitter</a> | 
                <a href="https://instagram.com/platsintia" target="_blank">Instagram</a> | 
                <a href="https://plataformasintia.com" target="_blank">Sitio Web</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px;">
                Este correo fue enviado a <strong><?=isset($data['usuario_email']) ? htmlspecialchars($data['usuario_email']) : ''?></strong>
            </p>
            
            <p style="margin-top: 15px; font-size: 11px; color: #6b7280;">
                © <?=date('Y')?> SINTIA. Todos los derechos reservados.
                <br>
                <a href="https://sintia.co/blog/" style="color: #667eea;">Blog</a> | 
                <a href="https://plataformasintia.com/terminos" style="color: #667eea;">Términos de Servicio</a>
            </p>
        </div>
    </div>
</body>
</html>

