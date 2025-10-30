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
    <title>Solicitud de Admisión Recibida</title>
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
                                        <!-- Ícono de check con círculo -->
                                        <div style="width: 80px; height: 80px; background-color: #ffffff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="#27ae60"/>
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0 0 10px 0; line-height: 1.3;">
                                            ¡Solicitud Recibida!
                                        </h1>
                                        <p style="color: rgba(255, 255, 255, 0.95); font-size: 16px; margin: 0; line-height: 1.5;">
                                            Tu proceso de admisión ha comenzado exitosamente
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Contenido principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Saludo -->
                            <p style="color: #2c3e50; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                                Cordial saludo <strong style="color: #667eea;"><?= htmlspecialchars($data['usuario_nombre']); ?></strong>,
                            </p>
                            
                            <p style="color: #2c3e50; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;">
                                Su solicitud de admisión para el aspirante <strong><?= htmlspecialchars($data['solicitud_nombre']); ?></strong> 
                                fue realizada correctamente. A continuación encontrará información importante sobre su solicitud.
                            </p>
                            
                            <!-- Box de información importante -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%); border: 2px solid #ffc107; border-radius: 12px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <div style="width: 36px; height: 36px; background-color: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#ffffff"/>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td style="padding-left: 15px;">
                                                    <p style="color: #856404; font-size: 15px; font-weight: 600; margin: 0 0 8px 0;">
                                                        Información Importante
                                                    </p>
                                                    <p style="color: #856404; font-size: 14px; line-height: 1.5; margin: 0;">
                                                        Guarde estos datos en un lugar seguro. Los necesitará durante todo el proceso de admisión.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Datos de la solicitud -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 5px solid #667eea; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        
                                        <!-- Número de solicitud -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #dee2e6; padding-bottom: 15px; margin-bottom: 15px;">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="#667eea"/>
                                                    </svg>
                                                </td>
                                                <td style="padding-left: 10px;">
                                                    <p style="color: #6c757d; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">
                                                        Número de Solicitud
                                                    </p>
                                                    <p style="color: #212529; font-size: 18px; font-weight: 700; margin: 0; letter-spacing: 1px;">
                                                        #<?= str_pad($data['solicitud_id'], 6, '0', STR_PAD_LEFT); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Documento del aspirante -->
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" fill="#667eea"/>
                                                    </svg>
                                                </td>
                                                <td style="padding-left: 10px;">
                                                    <p style="color: #6c757d; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">
                                                        Documento del Aspirante
                                                    </p>
                                                    <p style="color: #212529; font-size: 18px; font-weight: 700; margin: 0;">
                                                        <?= htmlspecialchars($data['solicitud_documento']); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Siguiente paso -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px; border-left: 5px solid #2196F3; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #1565c0; font-size: 15px; font-weight: 600; margin: 0 0 10px 0;">
                                            📋 ¿Qué sigue ahora?
                                        </p>
                                        <p style="color: #1976d2; font-size: 14px; line-height: 1.6; margin: 0;">
                                            Nuestro equipo de admisiones revisará su información y se pondrá en contacto con usted 
                                            para indicarle los siguientes pasos del proceso. Puede consultar el estado de su solicitud 
                                            en cualquier momento haciendo clic en el botón de abajo.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Botón CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= REDIRECT_ROUTE; ?>/admisiones/consultar-estado.php?idInst=<?= $_REQUEST['idInst']; ?>&solicitud=<?= base64_encode($data['solicitud_id']); ?>&documento=<?= base64_encode($data['solicitud_documento']); ?>" 
                                           style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 12px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                            🔍 Consultar Estado de Solicitud
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Mensaje de despedida -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top: 1px solid #e9ecef; padding-top: 20px;">
                                <tr>
                                    <td align="center">
                                        <p style="color: #6c757d; font-size: 14px; line-height: 1.6; margin: 0;">
                                            Gracias por preferirnos. Si tiene alguna pregunta, no dude en contactarnos.<br>
                                            <strong style="color: #667eea;">¡Que tenga un excelente día!</strong>
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
                                            Este es un correo automático, por favor no responda a este mensaje.
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
