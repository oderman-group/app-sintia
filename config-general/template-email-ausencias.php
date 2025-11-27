<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

// Extraer datos del array $data
$nombreAcudiente = isset($data['nombre_acudiente']) ? $data['nombre_acudiente'] : 'Acudiente';
$nombreEstudiante = isset($data['nombre_estudiante']) ? $data['nombre_estudiante'] : 'Estudiante';
$nombreMateria = isset($data['nombre_materia']) ? $data['nombre_materia'] : 'la materia';
$numeroAusencias = isset($data['numero_ausencias']) ? $data['numero_ausencias'] : '1';
$temaClase = isset($data['tema_clase']) ? $data['tema_clase'] : 'la clase';
$fechaClase = isset($data['fecha_clase']) ? $data['fecha_clase'] : date('d/m/Y');
$nombreDocente = isset($data['nombre_docente']) ? $data['nombre_docente'] : 'el docente';
$curso = isset($data['curso']) ? $data['curso'] : '';
$grupo = isset($data['grupo']) ? $data['grupo'] : '';
$ausenciasTotales = isset($data['ausencias_totales']) ? $data['ausencias_totales'] : $numeroAusencias;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Ausencia - SINTIA</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                
                <!-- Contenedor Principal -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.15); max-width: 600px;">
                    
                    <!-- Header con Gradiente Naranja -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #fb923c 100%); padding: 40px 30px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <img src="<?=$Plataforma->logo;?>" alt="Logo" style="max-width: 120px; height: auto; margin-bottom: 20px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 80px; height: 80px; margin: 0 auto 20px; display: inline-flex; align-items: center; justify-content: center;">
                                            <span style="font-size: 40px;">📅</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            Notificación de Ausencia
                                        </h1>
                                        <p style="color: rgba(255,255,255,0.95); margin: 10px 0 0 0; font-size: 15px;">
                                            Registro de inasistencia académica
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
                                ha registrado <strong style="color: #f59e0b;"><?=$numeroAusencias;?></strong> ausencia(s) en clase.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Card de Información de la Ausencia -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        
                                        <!-- Detalles de la Ausencia -->
                                        <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 15px;">
                                            <tr>
                                                <td style="color: #78350f; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    👤 Estudiante:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; font-weight: 700; text-align: right; padding: 8px 0;">
                                                    <?=$nombreEstudiante;?>
                                                </td>
                                            </tr>
                                            <?php if(!empty($curso) && !empty($grupo)) { ?>
                                            <tr>
                                                <td style="color: #78350f; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    🎓 Curso:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$curso;?> <?=$grupo;?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <td style="color: #78350f; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    📚 Materia:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">
                                                    <?=$nombreMateria;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #78350f; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    📖 Tema de la Clase:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$temaClase;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #78350f; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    👨‍🏫 Docente:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$nombreDocente;?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #78350f; font-weight: 600; font-size: 14px; padding: 8px 0;">
                                                    📅 Fecha de la Clase:
                                                </td>
                                                <td style="color: #2d3748; font-size: 14px; text-align: right; padding: 8px 0;">
                                                    <?=$fechaClase;?>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Separador -->
                                        <div style="height: 1px; background: rgba(245, 158, 11, 0.2); margin: 15px 0;"></div>
                                        
                                        <!-- Ausencias Destacadas -->
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="padding: 20px 0;">
                                                    <div style="background: white; border-radius: 15px; padding: 20px 30px; display: inline-block; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);">
                                                        <p style="color: #78350f; font-size: 13px; margin: 0 0 8px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                                                            Ausencias en esta Clase
                                                        </p>
                                                        <p style="color: #f59e0b; font-size: 48px; font-weight: 800; margin: 0; line-height: 1;">
                                                            <?=$numeroAusencias;?>
                                                        </p>
                                                        <p style="color: #718096; font-size: 12px; margin: 8px 0 0 0;">
                                                            Total acumulado: <strong style="color: #2d3748;"><?=$ausenciasTotales;?></strong> ausencia(s)
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
                    
                    <!-- Card de Importancia -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 4px solid #ef4444; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #991b1b; margin: 0; font-size: 15px; line-height: 1.6;">
                                            <strong style="font-size: 16px;">⚠️ ¿Por qué es importante?</strong><br><br>
                                            La asistencia regular es fundamental para:
                                        </p>
                                        <ul style="color: #991b1b; margin: 10px 0 0 0; padding-left: 20px; font-size: 14px; line-height: 1.8;">
                                            <li>El adecuado proceso de aprendizaje</li>
                                            <li>El cumplimiento académico</li>
                                            <li>La continuidad en los temas</li>
                                            <li>El rendimiento general del estudiante</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Mensaje de Acción -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-left: 4px solid #10b981; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #065f46; margin: 0; font-size: 15px; line-height: 1.6;">
                                            <strong style="font-size: 16px;">💡 Recomendaciones:</strong><br><br>
                                        </p>
                                        <ul style="color: #065f46; margin: 10px 0 0 0; padding-left: 20px; font-size: 14px; line-height: 1.8;">
                                            <li>Verifica el motivo de la ausencia con tu acudido</li>
                                            <li>Si fue justificada, presenta la excusa correspondiente</li>
                                            <li>Asegúrate de que el estudiante se ponga al día</li>
                                            <li>Contacta al docente si es necesario</li>
                                            <li>Revisa el registro completo de asistencia en la plataforma</li>
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
                                            📊 Ver Registro de Asistencia
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Card de Recordatorio -->
                    <tr>
                        <td style="padding: 0 40px 35px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); border-radius: 12px; border-left: 4px solid #8b5cf6;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #5b21b6; margin: 0; font-size: 14px; line-height: 1.6;">
                                            <strong style="font-size: 15px;">📋 Recuerda:</strong><br><br>
                                            Las ausencias injustificadas pueden afectar el desempeño académico y, en algunos casos, 
                                            el cumplimiento de los requisitos de promoción. Si la ausencia fue por motivos de salud 
                                            u otra causa justificada, por favor presenta la excusa correspondiente a la coordinación 
                                            académica.
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
                                Este es un mensaje automático del sistema de asistencia.
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
                                Esta notificación se envía automáticamente cuando se registra una ausencia para un estudiante. 
                                Mantente informado del proceso académico de tu acudido a través de la plataforma SINTIA.
                                Si tienes preguntas, contacta a la institución educativa.
                            </p>
                        </td>
                    </tr>
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>

