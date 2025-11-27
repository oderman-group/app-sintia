<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

// Extraer datos del array $data
$nombreAcudiente = isset($data['nombre_acudiente']) ? $data['nombre_acudiente'] : 'Acudiente';
$nombreEstudiante = isset($data['nombre_estudiante']) ? $data['nombre_estudiante'] : 'Estudiante';
$nombreMateria = isset($data['nombre_materia']) ? $data['nombre_materia'] : 'la materia';
$notaObtenida = isset($data['nota_obtenida']) ? $data['nota_obtenida'] : 'N/A';
$notaMinima = isset($data['nota_minima']) ? $data['nota_minima'] : '3.0';
$nombreActividad = isset($data['nombre_actividad']) ? $data['nombre_actividad'] : 'la actividad';
$fechaRegistro = isset($data['fecha_registro']) ? $data['fecha_registro'] : date('d/m/Y');
$nombreDocente = isset($data['nombre_docente']) ? $data['nombre_docente'] : 'el docente';
$curso = isset($data['curso']) ? $data['curso'] : '';
$grupo = isset($data['grupo']) ? $data['grupo'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerta de Nota Baja - SINTIA</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Contenedor Principal -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); max-width: 600px;">
                    
                    <!-- Header con Gradiente Rojo/Naranja -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f93e3e 0%, #ff6b6b 100%); padding: 40px 30px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <img src="<?=$Plataforma->logo;?>" alt="Logo" style="max-width: 120px; height: auto; margin-bottom: 20px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; margin: 0 auto 20px; display: inline-flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 40px;">⚠️</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            Alerta de Desempeño Bajo
                                        </h1>
                                        <p style="color: rgba(255,255,255,0.95); margin: 10px 0 0 0; font-size: 15px;">
                                            Notificación sobre el rendimiento académico
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Saludo Personal -->
                    <tr>
                        <td style="padding: 35px 40px 25px;">
                            <h2 style="color: #2d3748; font-size: 22px; margin: 0 0 15px 0; font-weight: 600;">
                                Hola <?=strtoupper($nombreAcudiente);?> 👋
                            </h2>
                            <p style="color: #4a5568; font-size: 16px; line-height: 1.6; margin: 0;">
                                Te informamos que tu acudido <strong style="color: #2d3748;"><?=$nombreEstudiante;?></strong> 
                                ha obtenido una calificación que requiere tu atención.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Card de Información de la Nota -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%); border-left: 5px solid #f93e3e; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        
                                        <!-- Estudiante y Materia -->
                                        <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 15px;">
                                            <tr>
                                                <td style="color: #742a2a; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    👤 Estudiante:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; font-weight: 700; text-align: right; padding: 8px 0;">
                                                    <?=$nombreEstudiante;?>
                                                </td>
                                            </tr>
                                            <?php if(!empty($curso) && !empty($grupo)) { ?>
                                            <tr>
                                                <td style="color: #742a2a; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    🎓 Curso:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$curso;?> <?=$grupo;?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <td style="color: #742a2a; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    📚 Materia:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">
                                                    <?=$nombreMateria;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #742a2a; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    📝 Actividad:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$nombreActividad;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #742a2a; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    👨‍🏫 Docente:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$nombreDocente;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #742a2a; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    📅 Fecha:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$fechaRegistro;?>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Separador -->
                                        <div style="height: 1px; background: rgba(249, 62, 62, 0.2); margin: 15px 0;"></div>
                                        
                                        <!-- Nota Obtenida (Destacada) -->
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="padding: 20px 0;">
                                                    <div style="background: white; border-radius: 15px; padding: 20px 30px; display: inline-block; box-shadow: 0 4px 15px rgba(249, 62, 62, 0.2);">
                                                        <p style="color: #742a2a; font-size: 13px; margin: 0 0 8px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                                            Nota Obtenida
                                                        </p>
                                                        <p style="color: #f93e3e; font-size: 48px; font-weight: 800; margin: 0; line-height: 1;">
                                                            <?=$notaObtenida;?>
                                                        </p>
                                                        <p style="color: #718096; font-size: 12px; margin: 8px 0 0 0;">
                                                            Nota mínima para aprobar: <strong style="color: #2d3748;"><?=$notaMinima;?></strong>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Mensaje de Acción -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #78350f; margin: 0; font-size: 15px; line-height: 1.6;">
                                            <strong style="font-size: 16px;">💡 ¿Qué hacer ahora?</strong><br><br>
                                            Te recomendamos:
                                        </p>
                                        <ul style="color: #78350f; margin: 10px 0 0 0; padding-left: 20px; font-size: 14px; line-height: 1.8;">
                                            <li>Revisar el desempeño de tu acudido en la plataforma</li>
                                            <li>Dialogar con el estudiante sobre el tema</li>
                                            <li>Contactar al docente si es necesario</li>
                                            <li>Establecer un plan de mejora académica</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Botón de Acción -->
                    <tr>
                        <td style="padding: 0 40px 35px;" align="center">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                        <a href="https://plataformasintia.com/" target="_blank" style="display: inline-block; padding: 16px 40px; color: white; text-decoration: none; font-weight: 600; font-size: 16px; letter-spacing: 0.5px;">
                                            📊 Ver Calificaciones en la Plataforma
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Mensaje de Apoyo -->
                    <tr>
                        <td style="padding: 0 40px 35px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); border-radius: 12px; border-left: 4px solid #0ea5e9;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #075985; margin: 0; font-size: 14px; line-height: 1.6;">
                                            <strong style="font-size: 15px;">🤝 Recuerda:</strong><br><br>
                                            El acompañamiento familiar es fundamental para el éxito académico. 
                                            Esta notificación es una oportunidad para apoyar el proceso de aprendizaje 
                                            de tu acudido y trabajar juntos en su mejora continua.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Separador -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <div style="height: 2px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent);"></div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center;">
                            <img src="<?=$Plataforma->logo;?>" alt="Logo" style="max-width: 80px; height: auto; margin-bottom: 15px; opacity: 0.8;">
                            <p style="color: #64748b; font-size: 14px; margin: 0 0 8px 0; line-height: 1.5;">
                                Este es un mensaje automático del sistema académico.
                            </p>
                            <p style="color: #94a3b8; font-size: 13px; margin: 0 0 15px 0;">
                                Por favor no responder a este correo.
                            </p>
                            
                            <!-- Botones de Ayuda -->
                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin-top: 20px;">
                                <tr>
                                    <td style="padding: 0 8px;">
                                        <a href="https://plataformasintia.com/soporte" target="_blank" style="display: inline-block; background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                            💬 Soporte
                                        </a>
                                    </td>
                                    <td style="padding: 0 8px;">
                                        <a href="https://plataformasintia.com/ayuda" target="_blank" style="display: inline-block; background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500;">
                                            📖 Centro de Ayuda
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #94a3b8; font-size: 12px; margin: 25px 0 0 0;">
                                © <?=date('Y');?> SINTIA - Sistema Integral de Gestión Académica<br>
                                <a href="https://plataformasintia.com" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 500;">
                                    www.plataformasintia.com
                                </a>
                            </p>
                        </td>
                    </tr>
                    
                </table>
                
                <!-- Texto Legal -->
                <table width="600" cellpadding="0" cellspacing="0" style="margin-top: 20px; max-width: 600px;">
                    <tr>
                        <td align="center" style="padding: 0 20px;">
                            <p style="color: #94a3b8; font-size: 11px; line-height: 1.5; margin: 0;">
                                Esta notificación se envía automáticamente cuando un estudiante obtiene una calificación 
                                por debajo de la nota mínima de aprobación (<?=$notaMinima;?>). 
                                Si crees que esto es un error, por favor contacta a la institución.
                            </p>
                        </td>
                    </tr>
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>

