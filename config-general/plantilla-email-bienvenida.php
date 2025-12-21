<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a SINTIA</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
        
        .success-icon {
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
        
        .success-icon svg {
            width: 60px;
            height: 60px;
            fill: #10b981;
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
        
        .credentials-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .credentials-title {
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
        
        .credentials-title svg {
            width: 24px;
            height: 24px;
            fill: #667eea;
        }
        
        .credential-row {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
        }
        
        .credential-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .credential-label svg {
            width: 18px;
            height: 18px;
            fill: #9ca3af;
        }
        
        .credential-value {
            color: #1f2937;
            font-weight: 700;
            font-size: 18px;
            font-family: 'Courier New', monospace;
            background: #f9fafb;
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        .security-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .security-notice h4 {
            color: #92400e;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .security-notice p {
            color: #78350f;
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }
        
        .access-button {
            text-align: center;
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
        
        .access-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 35px 0;
        }
        
        .access-button-secondary {
            text-align: center;
        }
        
        .access-button-secondary a {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white !important;
            text-decoration: none;
            padding: 16px 45px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        
        .access-button-secondary a:hover,
        .access-button a:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }
        
        .next-steps {
            background: #f9fafb;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .next-steps h3 {
            color: #1f2937;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .step {
            display: flex;
            align-items: start;
            gap: 15px;
            margin-bottom: 18px;
            padding: 12px;
            background: white;
            border-radius: 10px;
        }
        
        .step-number {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .step-content h4 {
            color: #1f2937;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .step-content p {
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
        
        .help-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .help-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .help-link.whatsapp {
            background: #25d366;
            color: white !important;
        }
        
        .help-link.email {
            background: #667eea;
            color: white !important;
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
            
            .credentials-section {
                padding: 20px;
            }
            
            .credential-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .credential-value {
                width: 100%;
                text-align: center;
                font-size: 16px;
            }
            
            .help-links {
                flex-direction: column;
            }
            
            .help-link {
                width: 100%;
                justify-content: center;
            }
            
            .access-buttons {
                gap: 12px;
            }
            
            .access-button a,
            .access-button-secondary a {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>
            <h1>¡Cuenta Activada!</h1>
            <p>Tu registro se ha completado exitosamente</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="welcome-message">
                <h2>¡Bienvenido a SINTIA, <?=isset($data['usuario_nombre']) ? htmlspecialchars($data['usuario_nombre']) : 'Usuario'?>!</h2>
                <p>
                    Nos complace enormemente que <span class="institution-name"><?=isset($data['institucion_nombre']) ? htmlspecialchars($data['institucion_nombre']) : 'tu institución'?></span> 
                    forme parte de la familia SINTIA. Estás a punto de transformar la gestión educativa de tu institución con la plataforma más completa del mercado.
                </p>
            </div>
            
            <?php if(isset($data['uso_sintia']) && !empty($data['uso_sintia']) && $data['uso_sintia'] != 'No especificado'): ?>
            <!-- Uso de SINTIA -->
            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-left: 4px solid #0ea5e9; border-radius: 10px; padding: 20px; margin: 25px 0;">
                <h4 style="color: #0c4a6e; font-size: 16px; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg" style="fill: #0ea5e9;">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Usarás SINTIA para:
                </h4>
                <p style="color: #075985; font-size: 15px; margin: 0; font-weight: 600;">
                    <?=htmlspecialchars($data['uso_sintia'])?>
                </p>
            </div>
            <?php endif; ?>
            
            <!-- Credentials Section -->
            <div class="credentials-section">
                <div class="credentials-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" fill="currentColor"/>
                    </svg>
                    Tus Credenciales de Acceso
                </div>
                
                <div class="credential-row">
                    <div class="credential-label">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" fill="currentColor"/>
                        </svg>
                        Usuario:
                    </div>
                    <div class="credential-value"><?=isset($data['usuario_usuario']) ? htmlspecialchars($data['usuario_usuario']) : 'N/A'?></div>
                </div>
                
                <div class="credential-row">
                    <div class="credential-label">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" fill="currentColor"/>
                        </svg>
                        Contraseña:
                    </div>
                    <div class="credential-value"><?=isset($data['usuario_clave']) ? htmlspecialchars($data['usuario_clave']) : 'N/A'?></div>
                </div>
                
                <div class="credential-row">
                    <div class="credential-label">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        Email:
                    </div>
                    <div class="credential-value" style="font-size: 14px;"><?=isset($data['usuario_email']) ? htmlspecialchars($data['usuario_email']) : 'N/A'?></div>
                </div>
            </div>
            
            <!-- Security Notice -->
            <div class="security-notice">
                <h4>
                    <svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg" style="fill: #f59e0b;">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Importante - Seguridad
                </h4>
                <p>
                    Por tu seguridad, te recomendamos <strong>cambiar tu contraseña</strong> la primera vez que inicies sesión. 
                    Nunca compartas tus credenciales con nadie y mantén tu información segura.
                </p>
            </div>
            
            <!-- Access Buttons -->
            <div class="access-buttons">
                <div class="access-button">
                    <a href="<?=REDIRECT_ROUTE?>" target="_blank">
                        Acceder a Mi Cuenta Ahora
                    </a>
                </div>
                
                <div class="access-button-secondary">
                    <a href="<?=REDIRECT_ROUTE?>/guias-iniciales-sintia/guia-directivos.php" target="_blank">
                        📚 Ver Instructivo Inicial para Directivos
                    </a>
                </div>
            </div>
            
            <div class="divider"></div>
            
            <!-- Next Steps -->
            <div class="next-steps">
                <h3>📋 Tus Primeros Pasos en SINTIA</h3>
                
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Inicia Sesión</h4>
                        <p>Usa tus credenciales para acceder a tu panel de control</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Completa tu Perfil</h4>
                        <p>Actualiza la información de tu institución y personaliza tu experiencia</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Configura tus Módulos</h4>
                        <p>Ajusta los módulos según las necesidades específicas de tu institución</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>Explora y Aprende</h4>
                        <p>Descubre todas las herramientas disponibles con nuestras guías interactivas</p>
                    </div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h4>¿Necesitas Ayuda para Comenzar?</h4>
                <p style="color: #3b82f6; margin-bottom: 15px; font-size: 14px;">
                    Nuestro equipo de soporte está disponible para ayudarte
                </p>
                <div class="help-links">
                    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, acabo de crear mi cuenta en SINTIA y necesito ayuda" 
                       class="help-link whatsapp" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="fill: white;">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                    </a>
                    <a href="mailto:info@plataformasintia.com?subject=Ayuda - Nueva Cuenta SINTIA" 
                       class="help-link email">
                        <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="fill: white;">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        Email Soporte
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
