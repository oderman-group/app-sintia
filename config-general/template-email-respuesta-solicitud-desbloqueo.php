<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/General_Solicitud.php");
$Plataforma = new Plataforma;

// Determinar si fue aprobado o rechazado
$aprobado = isset($data['usuario_estado']) && $data['usuario_estado'] == Administrativo_General_Solicitud::SOLICITUD_ACEPTADA;
$headerColor = $aprobado ? '#10b981' : '#ef4444';
$headerColorEnd = $aprobado ? '#059669' : '#dc2626';
$emoji = $aprobado ? '✅' : '❌';
$titulo = $aprobado ? '¡Solicitud Aprobada!' : 'Solicitud Rechazada';
$icono = $aprobado ? '🎉' : '⚠️';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo; ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Satoshi', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background-color: #f3f4f6;">
    
    <!-- Container Principal -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Card Principal -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, <?= $headerColor; ?> 0%, <?= $headerColorEnd; ?> 100%); padding: 45px 30px; text-align: center;">
                            <div style="width: 80px; height: 80px; background-color: rgba(255,255,255,0.25); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                                <span style="font-size: 42px;"><?= $icono; ?></span>
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                                <?= $titulo; ?>
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Saludo -->
                            <p style="color: #374151; font-size: 17px; line-height: 1.6; margin: 0 0 25px;">
                                Hola <strong style="color: #6017dc;"><?= htmlspecialchars($data['usuario_nombre'] ?? 'Usuario'); ?></strong>,
                            </p>
                            
                            <?php if ($aprobado): ?>
                                <!-- SOLICITUD APROBADA -->
                                <p style="color: #059669; font-size: 18px; font-weight: 600; margin: 0 0 20px; padding: 15px; background: #d1fae5; border-radius: 8px; text-align: center;">
                                    <?= $emoji; ?> Tu solicitud de desbloqueo ha sido aprobada
                                </p>
                                
                                <p style="color: #374151; font-size: 16px; line-height: 1.7; margin: 0 0 25px;">
                                    Nos complace informarte que tu cuenta ha sido desbloqueada exitosamente. 
                                    Ya puedes acceder nuevamente a la plataforma SINTIA.
                                </p>
                                
                                <!-- Detalles de la Aprobación -->
                                <?php if (!empty($data['motivo'])): ?>
                                <div style="background: #f0fdf4; border-radius: 12px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #10b981;">
                                    <h3 style="color: #065f46; margin: 0 0 10px; font-size: 15px; font-weight: 700;">
                                        💬 Mensaje del Administrador
                                    </h3>
                                    <p style="color: #047857; font-size: 14px; line-height: 1.6; margin: 0;">
                                        <?= nl2br(htmlspecialchars($data['motivo'])); ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Botón de Acceso -->
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
                                    <tr>
                                        <td align="center">
                                            <a href="<?= REDIRECT_ROUTE; ?>" 
                                               target="_blank"
                                               style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; text-decoration: none; padding: 18px 50px; border-radius: 12px; font-weight: 700; font-size: 17px; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                                                🚀 Acceder a Mi Cuenta
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Recomendaciones -->
                                <div style="background: #eff6ff; border-radius: 12px; padding: 16px; margin-top: 25px; border-left: 4px solid #3b82f6;">
                                    <p style="color: #1e40af; font-size: 13px; line-height: 1.6; margin: 0;">
                                        <strong>💡 Recomendaciones:</strong><br>
                                        • Cambia tu contraseña si consideras que fue comprometida<br>
                                        • Revisa la actividad reciente de tu cuenta<br>
                                        • Contacta al soporte si tienes dudas
                                    </p>
                                </div>
                                
                            <?php else: ?>
                                <!-- SOLICITUD RECHAZADA -->
                                <p style="color: #dc2626; font-size: 18px; font-weight: 600; margin: 0 0 20px; padding: 15px; background: #fee2e2; border-radius: 8px; text-align: center;">
                                    <?= $emoji; ?> Tu solicitud de desbloqueo ha sido rechazada
                                </p>
                                
                                <p style="color: #374151; font-size: 16px; line-height: 1.7; margin: 0 0 25px;">
                                    Lamentablemente, tu solicitud de desbloqueo no pudo ser aprobada en este momento.
                                </p>
                                
                                <!-- Motivo del Rechazo -->
                                <?php if (!empty($data['motivo'])): ?>
                                <div style="background: #fef2f2; border-radius: 12px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #ef4444;">
                                    <h3 style="color: #991b1b; margin: 0 0 10px; font-size: 15px; font-weight: 700;">
                                        📝 Motivo del Rechazo
                                    </h3>
                                    <p style="color: #7f1d1d; font-size: 14px; line-height: 1.6; margin: 0;">
                                        <?= nl2br(htmlspecialchars($data['motivo'])); ?>
                                    </p>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Próximos Pasos -->
                                <div style="background: #f3f4f6; border-radius: 12px; padding: 20px; margin-top: 25px; border-left: 4px solid #6b7280;">
                                    <h3 style="color: #374151; margin: 0 0 12px; font-size: 15px; font-weight: 700;">
                                        🔍 ¿Qué puedes hacer?
                                    </h3>
                                    <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin: 0;">
                                        • Contacta directamente con tu institución para más información<br>
                                        • Verifica que cumples con las políticas de uso de la plataforma<br>
                                        • Puedes enviar una nueva solicitud explicando mejor tu situación
                                    </p>
                                </div>
                                
                            <?php endif; ?>
                            
                            <!-- Información de Contacto -->
                            <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-top: 30px;">
                                <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0; text-align: center;">
                                    ¿Tienes preguntas? Contáctanos en<br>
                                    <a href="mailto:soporte@plataformasintia.com" style="color: #6017dc; text-decoration: none; font-weight: 600;">
                                        soporte@plataformasintia.com
                                    </a>
                                </p>
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: #111827; padding: 30px; text-align: center;">
                            <img src="<?=$Plataforma->logo;?>" width="50" style="margin-bottom: 15px; opacity: 0.7;">
                            <p style="color: #d1d5db; font-size: 14px; line-height: 1.6; margin: 0 0 10px;">
                                <strong style="color: #ffffff;">Plataforma SINTIA</strong><br>
                                Sistema Integral de Gestión Académica
                            </p>
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                © <?= date('Y'); ?> SINTIA by ODERMAN. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>

