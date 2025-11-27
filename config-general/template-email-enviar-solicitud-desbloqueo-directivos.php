<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Desbloqueo</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Satoshi', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background-color: #f3f4f6;">
    
    <!-- Container Principal -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Card Principal -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                    
                    <!-- Header con degradado naranja/rojo -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); padding: 40px 30px; text-align: center;">
                            <div style="width: 70px; height: 70px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <span style="font-size: 36px;">⚠️</span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                Nueva Solicitud de Desbloqueo
                            </h1>
                            <p style="color: rgba(255,255,255,0.95); margin: 10px 0 0; font-size: 15px; font-weight: 400;">
                                Un usuario requiere tu atención
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Contenido Principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Saludo -->
                            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 25px;">
                                Hola <strong style="color: #6017dc;"><?= strtoupper($data['usuario_nombre'] ?? 'Directivo'); ?></strong>,
                            </p>
                            
                            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0 0 30px;">
                                Has recibido una nueva solicitud de desbloqueo de cuenta. A continuación encontrarás los detalles:
                            </p>
                            
                            <!-- Información del Usuario -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); border-radius: 12px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #f59e0b;">
                                <tr>
                                    <td>
                                        <h3 style="color: #6017dc; margin: 0 0 15px; font-size: 16px; font-weight: 700;">
                                            📋 Información del Solicitante
                                        </h3>
                                        
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #6b7280; font-size: 14px;">👤 Nombre:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right;">
                                                    <span style="color: #111827; font-size: 14px; font-weight: 600;">
                                                        <?= htmlspecialchars($data['nombre_solicitante'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #6b7280; font-size: 14px;">🔑 Usuario:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right;">
                                                    <span style="color: #111827; font-size: 14px; font-weight: 600;">
                                                        <?= htmlspecialchars($data['usuario_solicitante'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #6b7280; font-size: 14px;">📧 Email:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right;">
                                                    <span style="color: #111827; font-size: 14px; font-weight: 600;">
                                                        <?= htmlspecialchars($data['email_solicitante'] ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <strong style="color: #6b7280; font-size: 14px;">📅 Fecha:</strong>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right;">
                                                    <span style="color: #111827; font-size: 14px; font-weight: 600;">
                                                        <?= date('d/m/Y H:i'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Mensaje del Usuario -->
                            <div style="background: #fef3c7; border-radius: 12px; padding: 20px; margin-bottom: 30px; border-left: 4px solid #f59e0b;">
                                <h3 style="color: #92400e; margin: 0 0 12px; font-size: 15px; font-weight: 700;">
                                    💬 Mensaje del Usuario
                                </h3>
                                <p style="color: #78350f; font-size: 14px; line-height: 1.6; margin: 0; font-style: italic;">
                                    "<?= htmlspecialchars($data['contenido_msj'] ?? 'Sin mensaje adicional'); ?>"
                                </p>
                            </div>
                            
                            <!-- Botón de Acción -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= REDIRECT_ROUTE; ?>/directivo/solicitudes-desbloqueo.php" 
                                           target="_blank"
                                           style="display: inline-block; background: linear-gradient(135deg, #6017dc 0%, #4f46e5 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 8px 20px rgba(96, 23, 220, 0.3);">
                                            🔓 Revisar Solicitud en SINTIA
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Nota Importante -->
                            <div style="background: #fef2f2; border-radius: 12px; padding: 16px; margin-top: 25px; border-left: 4px solid #ef4444;">
                                <p style="color: #991b1b; font-size: 13px; line-height: 1.5; margin: 0;">
                                    <strong>⏱️ Atención:</strong> El usuario permanecerá bloqueado hasta que un directivo apruebe o rechace esta solicitud. 
                                    Por favor, revisa y responde lo antes posible.
                                </p>
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <img src="<?=$Plataforma->logo;?>" width="50" style="margin-bottom: 15px; opacity: 0.7;">
                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0 0 10px;">
                                <strong style="color: #111827;">Plataforma SINTIA</strong><br>
                                Sistema Integral de Gestión Académica
                            </p>
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                <a href="https://plataformasintia.com" style="color: #6017dc; text-decoration: none;">www.plataformasintia.com</a> | 
                                <a href="mailto:soporte@plataformasintia.com" style="color: #6017dc; text-decoration: none;">soporte@plataformasintia.com</a>
                            </p>
                            <p style="color: #d1d5db; font-size: 11px; margin: 15px 0 0; line-height: 1.5;">
                                Este es un correo automático generado por el sistema. Por favor no respondas directamente a este mensaje.
                            </p>
                        </td>
                    </tr>
                    
                </table>
                
                <!-- Espaciado final -->
                <div style="height: 40px;"></div>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
