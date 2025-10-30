<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Año Renovado Exitosamente - SINTIA</title>
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
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
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
            fill: #3b82f6;
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
            color: #3b82f6;
            font-weight: 700;
        }
        
        .year-badge {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 24px;
            font-weight: 800;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        }
        
        .info-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid rgba(59, 130, 246, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .info-title {
            text-align: center;
            color: #1e40af;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .info-title svg {
            width: 24px;
            height: 24px;
            fill: #3b82f6;
        }
        
        .info-row {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
        }
        
        .info-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
        }
        
        .info-value {
            color: #1f2937;
            font-weight: 700;
            font-size: 16px;
        }
        
        .year-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            padding: 25px;
            background: #f9fafb;
            border-radius: 15px;
        }
        
        .year-box {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px 30px;
            text-align: center;
            min-width: 120px;
        }
        
        .year-box.old {
            opacity: 0.6;
        }
        
        .year-box.new {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, white 100%);
        }
        
        .year-box-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .year-box-value {
            font-size: 28px;
            font-weight: 800;
            color: #1f2937;
        }
        
        .year-box.new .year-box-value {
            color: #3b82f6;
        }
        
        .arrow-icon {
            font-size: 30px;
            color: #3b82f6;
            font-weight: bold;
        }
        
        .access-button {
            text-align: center;
            margin: 35px 0;
        }
        
        .access-button a {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white !important;
            text-decoration: none;
            padding: 18px 50px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 30px 0;
        }
        
        .feature-item {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        
        .feature-icon svg {
            width: 28px;
            height: 28px;
            fill: white;
        }
        
        .feature-title {
            color: #1f2937;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .feature-text {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .help-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        
        .help-section h4 {
            color: #92400e;
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
            background: #3b82f6;
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
            color: #3b82f6;
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
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .year-arrow {
                flex-direction: column;
                gap: 10px;
            }
            
            .arrow-icon {
                transform: rotate(90deg);
            }
            
            .help-links {
                flex-direction: column;
            }
            
            .help-link {
                width: 100%;
                justify-content: center;
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
            <h1>¡Año Renovado!</h1>
            <p>Tu institución está lista para un nuevo período académico</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="welcome-message">
                <h2>¡Hola de nuevo! 👋</h2>
                <p>
                    <span class="institution-name"><?=isset($data['institucion_nombre']) ? htmlspecialchars($data['institucion_nombre']) : 'Tu institución'?></span> 
                    ha renovado exitosamente su año académico en SINTIA.
                </p>
                <div class="year-badge">
                    Año <?=isset($data['year_nuevo']) ? htmlspecialchars($data['year_nuevo']) : date('Y')?>
                </div>
            </div>
            
            <!-- Year Transition -->
            <div class="year-arrow">
                <div class="year-box old">
                    <div class="year-box-label">Año Anterior</div>
                    <div class="year-box-value"><?=isset($data['year_anterior']) ? htmlspecialchars($data['year_anterior']) : ''?></div>
                </div>
                <div class="arrow-icon">→</div>
                <div class="year-box new">
                    <div class="year-box-label">Año Nuevo</div>
                    <div class="year-box-value"><?=isset($data['year_nuevo']) ? htmlspecialchars($data['year_nuevo']) : date('Y')?></div>
                </div>
            </div>
            
            <!-- Info Section -->
            <div class="info-section">
                <div class="info-title">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" fill="currentColor"/>
                    </svg>
                    Detalles de la Renovación
                </div>
                
                <div class="info-row">
                    <div class="info-label">Institución:</div>
                    <div class="info-value"><?=isset($data['institucion_nombre']) ? htmlspecialchars($data['institucion_nombre']) : 'N/A'?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Nuevo Año Académico:</div>
                    <div class="info-value" style="color: #3b82f6;"><?=isset($data['year_nuevo']) ? htmlspecialchars($data['year_nuevo']) : date('Y')?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Fecha de Renovación:</div>
                    <div class="info-value"><?=date('d/m/Y H:i')?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">URL de Acceso:</div>
                    <div class="info-value" style="font-size: 14px; color: #3b82f6;">
                        <a href="<?=isset($data['url_acceso']) ? htmlspecialchars($data['url_acceso']) : REDIRECT_ROUTE?>" style="color: #3b82f6; text-decoration: none;">
                            <?=isset($data['url_acceso']) ? htmlspecialchars($data['url_acceso']) : REDIRECT_ROUTE?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Access Button -->
            <div class="access-button">
                <a href="<?=isset($data['url_acceso']) ? htmlspecialchars($data['url_acceso']) : REDIRECT_ROUTE?>" target="_blank">
                    Acceder al Nuevo Año
                </a>
            </div>
            
            <!-- Features Grid -->
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <div class="feature-title">Datos Preservados</div>
                    <div class="feature-text">Tu configuración e información histórica se mantienen intactos</div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <div class="feature-title">Módulos Activos</div>
                    <div class="feature-text">Todos tus módulos siguen configurados y listos para usar</div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <div class="feature-title">Usuarios Listos</div>
                    <div class="feature-text">Todos los usuarios pueden acceder al nuevo año</div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <div class="feature-title">Soporte Total</div>
                    <div class="feature-text">Nuestro equipo te acompaña en este nuevo período</div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="help-section">
                <h4>¿Necesitas Ayuda o Tienes Preguntas?</h4>
                <p style="color: #78350f; margin-bottom: 15px; font-size: 14px;">
                    Estamos aquí para ayudarte con el nuevo año académico
                </p>
                <div class="help-links">
                    <a href="https://api.whatsapp.com/send?phone=573006075800&text=Hola, he renovado mi año en SINTIA y necesito ayuda" 
                       class="help-link whatsapp" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="fill: white;">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                    </a>
                    <a href="mailto:info@plataformasintia.com?subject=Ayuda - Renovación Año SINTIA" 
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
                <a href="https://instagram.com/plataformasintia" target="_blank">Instagram</a> | 
                <a href="https://plataformasintia.com" target="_blank">Sitio Web</a>
            </div>
            
            <p style="margin-top: 15px; font-size: 11px; color: #6b7280;">
                © <?=date('Y')?> SINTIA. Todos los derechos reservados.
                <br>
                <a href="https://plataformasintia.com/privacidad" style="color: #3b82f6;">Política de Privacidad</a> | 
                <a href="https://plataformasintia.com/terminos" style="color: #3b82f6;">Términos de Servicio</a>
            </p>
        </div>
    </div>
</body>
</html>

