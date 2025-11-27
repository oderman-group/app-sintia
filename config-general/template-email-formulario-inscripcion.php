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
    <title>Actualización de Solicitud de Admisión</title>
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
                                        <!-- Ícono de notificación con círculo -->
                                        <div style="width: 80px; height: 80px; background-color: #ffffff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z" fill="#2196F3"/>
                                            </svg>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0 0 10px 0; line-height: 1.3;">
                                            Nueva Actualización
                                        </h1>
                                        <p style="color: rgba(255, 255, 255, 0.95); font-size: 16px; margin: 0; line-height: 1.5;">
                                            Tu solicitud de admisión ha sido actualizada
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
                                Le informamos que a su solicitud de admisión se le ha agregado una nueva actualización. 
                                A continuación encontrará los detalles.
                            </p>
                            
                            <!-- Número de solicitud -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; border-left: 5px solid #667eea; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="#667eea"/>
                                                    </svg>
                                                </td>
                                                <td style="padding-left: 10px;">
                                                    <p style="color: #6c757d; font-size: 13px; margin: 0 0 4px 0; font-weight: 500;">
                                                        Solicitud N°
                                                    </p>
                                                    <p style="color: #212529; font-size: 20px; font-weight: 700; margin: 0; letter-spacing: 1px;">
                                                        #<?= str_pad($data['solicitud_id'], 6, '0', STR_PAD_LEFT); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Box de observación -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #fff9e6 0%, #ffe8a1 100%); border: 2px solid #ffc107; border-radius: 12px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="50" valign="top">
                                                    <div style="width: 44px; height: 44px; background-color: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" fill="#ffffff"/>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td style="padding-left: 15px;">
                                                    <p style="color: #856404; font-size: 15px; font-weight: 600; margin: 0 0 12px 0;">
                                                        📝 Nueva Observación
                                                    </p>
                                                    <div style="background-color: rgba(255, 255, 255, 0.7); border-radius: 8px; padding: 15px; border-left: 3px solid #ffa000;">
                                                        <div style="color: #664d03; font-size: 15px; line-height: 1.6; margin: 0;">
<?= $data['observaciones']; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Información adicional -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px; border-left: 5px solid #2196F3; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #1565c0; font-size: 15px; font-weight: 600; margin: 0 0 10px 0;">
                                            💡 ¿Qué debo hacer?
                                        </p>
                                        <p style="color: #1976d2; font-size: 14px; line-height: 1.6; margin: 0;">
                                            Le recomendamos revisar la observación detalladamente. Si tiene alguna pregunta o 
                                            necesita más información, puede consultar el estado completo de su solicitud o 
                                            contactar directamente con nuestro equipo de admisiones.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Botón CTA -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center">
                                        <?php 
                                        $idInstParam = !empty($_REQUEST['idInst']) ? $_REQUEST['idInst'] : base64_encode($data['institucion_id'] ?? '');
                                        $urlConsulta = REDIRECT_ROUTE . '/admisiones/consultar-estado.php?idInst=' . $idInstParam . '&solicitud=' . base64_encode($data['solicitud_id']) . '&documento=' . base64_encode($data['id_aspirante']);
                                        ?>
                                        <a href="<?= $urlConsulta; ?>" 
                                           style="display: inline-block; padding: 16px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 12px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                            🔍 Consultar Estado Completo
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Mensaje de despedida -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top: 1px solid #e9ecef; padding-top: 20px;">
                                <tr>
                                    <td align="center">
                                        <p style="color: #6c757d; font-size: 14px; line-height: 1.6; margin: 0;">
                                            Gracias por preferirnos. Estamos comprometidos con brindarle la mejor atención.<br>
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
                                            Si tiene preguntas, puede contactarnos directamente a través de nuestros canales oficiales.
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
