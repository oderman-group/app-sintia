<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Inicio - <?=isset($data['guia_nombre']) ? htmlspecialchars($data['guia_nombre']) : 'SINTIA'?></title>
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
        
        .guide-icon {
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
        
        .guide-icon svg {
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
        
        .guide-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }
        
        .guide-section h3 {
            color: #667eea;
            font-weight: 700;
            font-size: 22px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .guide-section p {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .access-button {
            text-align: center;
            margin: 35px 0;
        }
        
        .access-button a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 18px 50px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        
        .access-button a:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }
        
        .features-section {
            background: #f9fafb;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .features-section h3 {
            color: #1f2937;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .feature-item {
            display: flex;
            align-items: start;
            gap: 15px;
            margin-bottom: 18px;
            padding: 12px;
            background: white;
            border-radius: 10px;
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            font-size: 18px;
        }
        
        .feature-content h4 {
            color: #1f2937;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .feature-content p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
            line-height: 1.4;
        }
        
        .help-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        
        .help-section h4 {
            color: #1e40af;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .help-section p {
            color: #3b82f6;
            margin-bottom: 15px;
            font-size: 14px;
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
            
            .guide-section {
                padding: 20px;
            }
            
            .access-button a {
                padding: 15px 35px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="guide-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <h1>Guía de Inicio</h1>
            <p>Tu guía completa para usar SINTIA</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="welcome-message">
                <h2>¡Hola, <?=isset($data['usuario_nombre']) ? htmlspecialchars($data['usuario_nombre']) : 'Usuario'?>!</h2>
                <p>
                    Te hemos enviado la <strong>Guía de Inicio para <?=isset($data['guia_nombre']) ? htmlspecialchars($data['guia_nombre']) : 'usuarios'?></strong> de SINTIA.
                    Esta guía paso a paso te ayudará a familiarizarte con la plataforma y aprovechar todas sus funcionalidades.
                </p>
            </div>
            
            <!-- Guide Section -->
            <div class="guide-section">
                <h3>
                    <svg viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" style="fill: #667eea;">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Accede a tu Guía Completa
                </h3>
                <p>
                    En esta guía encontrarás instrucciones detalladas, capturas de pantalla, videos tutoriales y todo lo que necesitas para comenzar a usar SINTIA de manera efectiva.
                </p>
            </div>
            
            <!-- Access Button -->
            <div class="access-button">
                <a href="<?=isset($data['guia_url']) ? htmlspecialchars($data['guia_url']) : '#'?>" target="_blank">
                    <i class="fa fa-book"></i> Ver Guía Completa
                </a>
            </div>
            
            <div class="divider"></div>
            
            <!-- Features Section -->
            <div class="features-section">
                <h3>📋 ¿Qué encontrarás en la guía?</h3>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fa fa-list-check"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Pasos Detallados</h4>
                        <p>Instrucciones claras y ordenadas para cada funcionalidad</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fa fa-images"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Capturas de Pantalla</h4>
                        <p>Imágenes que muestran exactamente dónde encontrar cada opción</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fa fa-video"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Videos Tutoriales</h4>
                        <p>Guías visuales paso a paso para procesos más complejos</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fa fa-table-list"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Tabla de Contenido</h4>
                        <p>Navegación fácil para ir directo al tema que necesitas</p>
                    </div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h4>¿Necesitas Ayuda Adicional?</h4>
                <p>
                    Si después de revisar la guía tienes preguntas o necesitas asistencia, nuestro equipo de soporte está disponible para ayudarte.
                </p>
                <p style="font-size: 13px; color: #64748b; margin-top: 10px;">
                    <strong>Email:</strong> soporte@plataformasintia.com<br>
                    <strong>Teléfono:</strong> +57 300 607 5800
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>SINTIA - Plataforma Educativa</strong></p>
            <p>Transformando la educación con tecnología</p>
            
            <p style="margin-top: 20px; font-size: 12px;">
                Este correo fue enviado a <strong><?=isset($data['usuario_email']) ? htmlspecialchars($data['usuario_email']) : ''?></strong>
            </p>
            
            <p style="margin-top: 15px; font-size: 11px; color: #6b7280;">
                © <?=date('Y')?> SINTIA. Todos los derechos reservados.
                <br>
                Si no solicitaste este correo, puedes ignorarlo de forma segura.
            </p>
        </div>
    </div>
</body>
</html>

