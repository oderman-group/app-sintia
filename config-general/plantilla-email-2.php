<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

$contenidoMsj = $data['contenido_msj'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($asunto); ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f4f6f9;">
    
    <!-- Contenedor principal -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Card principal -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header con gradiente -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <!-- Ícono de mensaje -->
                                        <div style="width: 80px; height: 80px; background-color: #ffffff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="#667eea"/>
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="color: #ffffff; font-size: 26px; font-weight: 700; margin: 0 0 10px 0; line-height: 1.3;">
                                            Nuevo Mensaje en SINTIA
                                        </h1>
                                        <p style="color: rgba(255, 255, 255, 0.95); font-size: 16px; margin: 0; line-height: 1.5;">
                                            <?= htmlspecialchars($asunto); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Contenido principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Mensaje -->
                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 5px solid #667eea; padding: 25px; margin-bottom: 30px;">
                                <div style="color: #2c3e50; font-size: 15px; line-height: 1.7;">
                                    <?= $contenidoMsj; ?>
                                </div>
                            </div>
                            
                            <!-- Botón CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= REDIRECT_ROUTE; ?>/main-app/<?= $data['tipo_usuario'] ?? 'directivo'; ?>/mensajes.php" 
                                           style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 12px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                            📬 Ver en la Plataforma
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Nota informativa -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px; border-left: 5px solid #2196F3; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #1565c0; font-size: 14px; line-height: 1.6; margin: 0;">
                                            💡 <strong>Nota:</strong> Este mensaje fue enviado a través de la mensajería interna de SINTIA. 
                                            Puedes responder directamente desde la plataforma.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <img src="<?= $Plataforma->logo; ?>" alt="Logo" width="60" style="margin-bottom: 15px;">
                                        <p style="color: #6c757d; font-size: 13px; line-height: 1.6; margin: 0 0 10px 0;">
                                            Este es un correo automático enviado desde la plataforma SINTIA.
                                        </p>
                                        <p style="color: #6c757d; font-size: 13px; margin: 0 0 15px 0;">
                                            Para responder, ingresa a la plataforma.
                                        </p>
                                        <p style="color: #6c757d; font-size: 13px; margin: 0 0 15px 0;">
                                            <strong style="color: #667eea;">🛡️ Tu información está segura y protegida</strong>
                                        </p>
                                        <a href="https://plataformasintia.com/" style="color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">
                                            www.plataformasintia.com
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
