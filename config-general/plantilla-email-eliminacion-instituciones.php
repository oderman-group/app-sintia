<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Eliminación de Instituciones</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 50px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .alert-icon {
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
        
        .alert-icon svg {
            width: 60px;
            height: 60px;
            fill: #ef4444;
        }
        
        .email-header h1 {
            color: white;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        
        .email-body {
            padding: 40px 35px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(220, 38, 38, 0.08) 100%);
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .summary-title {
            color: #dc2626;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .institution-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #ef4444;
        }
        
        .institution-name {
            color: #1f2937;
            font-weight: 700;
            font-size: 16px;
        }
        
        .institution-details {
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 25px 0;
        }
        
        .info-item {
            background: #f9fafb;
            padding: 15px;
            border-radius: 10px;
            border-left: 3px solid #667eea;
        }
        
        .info-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #1f2937;
            font-size: 16px;
            font-weight: 700;
        }
        
        .email-footer {
            background: #1f2937;
            padding: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
            line-height: 1.8;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 10px;
            }
            
            .email-header {
                padding: 35px 20px;
            }
            
            .email-body {
                padding: 25px 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="alert-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1>🗑️ Eliminación de Instituciones</h1>
            <p>Notificación del Sistema SINTIA</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: #1f2937; font-size: 22px; margin-bottom: 15px;">
                    Alerta de Eliminación de Instituciones
                </h2>
                <p style="color: #6b7280; font-size: 15px; line-height: 1.6;">
                    Se ha realizado una eliminación de datos institucionales en la plataforma SINTIA.
                </p>
            </div>
            
            <!-- Información General -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">📅 Fecha</div>
                    <div class="info-value"><?= isset($data['fecha_eliminacion']) ? htmlspecialchars($data['fecha_eliminacion']) : date('Y-m-d H:i:s') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">👤 Responsable</div>
                    <div class="info-value"><?= isset($data['responsable']) ? htmlspecialchars($data['responsable']) : 'N/A' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">🏢 Instituciones Eliminadas</div>
                    <div class="info-value"><?= isset($data['total_eliminadas']) ? htmlspecialchars($data['total_eliminadas']) : '0' ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">📎 Respaldos Adjuntos</div>
                    <div class="info-value"><?= isset($data['total_eliminadas']) ? htmlspecialchars($data['total_eliminadas']) : '0' ?></div>
                </div>
            </div>
            
            <!-- Lista de Instituciones Eliminadas -->
            <div class="summary-card">
                <div class="summary-title">
                    <svg viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" style="fill: #dc2626;">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke="currentColor" stroke-width="2" fill="none"/>
                    </svg>
                    Instituciones Eliminadas
                </div>
                
                <?php 
                if (isset($data['instituciones_eliminadas']) && is_array($data['instituciones_eliminadas'])) {
                    foreach ($data['instituciones_eliminadas'] as $inst) {
                        $nombre = htmlspecialchars($inst['nombre'] ?? 'N/A');
                        $id = htmlspecialchars($inst['id'] ?? 'N/A');
                        $bd = htmlspecialchars($inst['bd'] ?? 'N/A');
                        
                        echo "<div class='institution-item'>";
                        echo "<div class='institution-name'>$nombre</div>";
                        echo "<div class='institution-details'>";
                        echo "ID: $id | Base de datos: $bd";
                        
                        if (isset($inst['datos_eliminados']) && is_array($inst['datos_eliminados'])) {
                            $totalRegistros = array_sum($inst['datos_eliminados']);
                            echo " | <strong>$totalRegistros</strong> registros eliminados";
                        }
                        
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='institution-item'>";
                    echo "<div class='institution-details'>No se recibió información de instituciones</div>";
                    echo "</div>";
                }
                ?>
            </div>
            
            <!-- Información Importante -->
            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #f59e0b; border-radius: 10px; padding: 20px; margin: 25px 0;">
                <h4 style="color: #92400e; font-size: 16px; font-weight: 700; margin-bottom: 10px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg" style="fill: #f59e0b; display: inline-block; vertical-align: middle; margin-right: 8px;">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Archivos de Respaldo
                </h4>
                <p style="color: #78350f; font-size: 14px; margin: 0; line-height: 1.5;">
                    Se han creado archivos .txt de respaldo con toda la información de las instituciones eliminadas. 
                    Estos archivos están adjuntos a este correo y contienen un registro detallado de todos los datos 
                    que existían antes de la eliminación.
                </p>
            </div>
            
            <div style="background: #f9fafb; border-radius: 15px; padding: 25px; text-align: center; margin: 30px 0;">
                <h4 style="color: #1f2937; font-size: 16px; font-weight: 700; margin-bottom: 10px;">
                    ℹ️ Información Importante
                </h4>
                <p style="color: #6b7280; font-size: 14px; margin: 0;">
                    Este correo es una notificación automática del sistema. Los archivos de respaldo 
                    se han adjuntado para su registro y archivo. Revisa los datos eliminados y conserva 
                    estos respaldos de forma segura.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>SINTIA - Plataforma Educativa</strong></p>
            <p>Sistema de notificaciones automáticas</p>
            
            <p style="margin-top: 20px; font-size: 12px;">
                Este correo fue enviado a <strong><?= isset($data['usuario_email']) ? htmlspecialchars($data['usuario_email']) : '' ?></strong>
            </p>
            
            <p style="margin-top: 15px; font-size: 11px; color: #6b7280;">
                © <?= date('Y') ?> SINTIA - ODERMAN GROUP. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>

