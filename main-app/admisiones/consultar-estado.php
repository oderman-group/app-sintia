<?php
include("bd-conexion.php");

$solicitud = '';
$documento = '';
if (!empty($_GET["solicitud"])) {
    $solicitud = base64_decode($_GET['solicitud']);
    $documento = base64_decode($_GET['documento']);
} elseif (!empty($_POST["solicitud"])) {
    $solicitud = $_POST['solicitud'];
    $documento = $_POST['documento'];
}

// Si hay solicitud, consultar datos
$datos = null;
$num = 0;
if (!empty($solicitud)) {
    $estQuery = "SELECT * FROM aspirantes WHERE asp_id = :id AND asp_documento = :documento";
    $est = $pdo->prepare($estQuery);
    $est->bindParam(':id', $solicitud, PDO::PARAM_INT);
    $est->bindParam(':documento', $documento, PDO::PARAM_STR);
    $est->execute();
    $num = $est->rowCount();
    $datos = $est->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Estado | <?= $datosInfo['info_nombre']; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../sintia-icono.png" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .consulta-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .consulta-header {
            background: white;
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .consulta-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .consulta-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }
        
        .consulta-icon i {
            font-size: 40px;
            color: white;
        }
        
        .consulta-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 12px;
        }
        
        .consulta-header p {
            font-size: 16px;
            color: #5f6368;
            margin: 0;
        }
        
        /* Formulario de búsqueda */
        .search-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            margin-bottom: 30px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 15px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 13px;
            margin-top: 6px;
        }
        
        .btn-consultar-estado {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        
        .btn-consultar-estado:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
            color: white;
        }
        
        /* Resultado */
        .resultado-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            margin-bottom: 30px;
            animation: fadeInUp 0.7s ease-out;
        }
        
        /* Timeline de estados */
        .status-timeline {
            position: relative;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        .timeline-track {
            position: absolute;
            top: 45px;
            left: 40px;
            right: 40px;
            height: 4px;
            background: #e9ecef;
            z-index: 0;
        }
        
        .timeline-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.8s ease;
            border-radius: 4px;
        }
        
        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }
        
        .timeline-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .timeline-step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .timeline-step.active .timeline-step-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        
        .timeline-step.completed .timeline-step-circle {
            background: #27ae60;
            color: white;
        }
        
        .timeline-step-circle i {
            font-size: 20px;
        }
        
        .timeline-step-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 600;
            max-width: 100px;
            line-height: 1.3;
        }
        
        .timeline-step.active .timeline-step-label {
            color: #667eea;
            font-weight: 700;
        }
        
        /* Info cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 24px;
            border-left: 5px solid #667eea;
        }
        
        .info-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .info-value {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 700;
        }
        
        /* Badge de estado */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 24px;
        }
        
        .status-badge i {
            font-size: 18px;
        }
        
        /* Observación */
        .observacion-box {
            background: linear-gradient(135deg, #fff9e6 0%, #ffe8a1 100%);
            border: 2px solid #ffc107;
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
        }
        
        .observacion-box h5 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .observacion-box p {
            color: #664d03;
            font-size: 15px;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Botón de acción */
        .action-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        
        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
            color: white;
            text-decoration: none;
        }
        
        /* Upload comprobante */
        .upload-section {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border: 2px solid #ffc107;
            border-radius: 16px;
            padding: 32px;
            margin-top: 30px;
        }
        
        .upload-section h4 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .upload-info {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            color: #664d03;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 40px;
            border: 3px dashed #ffc107;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #856404;
        }
        
        .file-input-label:hover {
            background: rgba(255, 255, 255, 0.8);
            border-color: #ff9800;
        }
        
        .file-input-label i {
            font-size: 32px;
        }
        
        .file-selected {
            margin-top: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            color: #664d03;
            font-size: 14px;
            display: none;
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #dadce0;
            margin-bottom: 24px;
        }
        
        .empty-state h4 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 12px;
        }
        
        .empty-state p {
            color: #5f6368;
            font-size: 15px;
        }
        
        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .consulta-header {
                padding: 32px 24px;
            }
            
            .consulta-header h1 {
                font-size: 24px;
            }
            
            .search-card,
            .resultado-card {
                padding: 24px;
            }
            
            .timeline-steps {
                flex-direction: column;
                gap: 20px;
            }
            
            .timeline-track {
                display: none;
            }
            
            .timeline-step {
                flex-direction: row;
                justify-content: flex-start;
                text-align: left;
                gap: 16px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="consulta-container">
        <?php include("menu.php"); ?>
        
        <!-- Header -->
        <div class="consulta-header">
            <div class="consulta-icon">
                <i class="fas fa-search"></i>
            </div>
            <h1>Consultar Estado de Solicitud</h1>
            <p>Ingresa los datos para verificar el estado de tu proceso de admisión</p>
        </div>
        
        <!-- Alertas -->
        <?php include("alertas.php"); ?>
        
        <!-- Formulario de búsqueda -->
        <div class="search-card">
            <form action="consultar-estado.php" method="post" id="formConsulta">
                <input type="hidden" name="idInst" value="<?= $_REQUEST['idInst'] ?? ''; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número de Solicitud</label>
                            <input type="number" class="form-control" name="solicitud" autocomplete="off" required value="<?= $solicitud; ?>" placeholder="Ej: 123456">
                            <small class="form-text">Este número fue enviado a tu correo al momento del registro.</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Número de Documento del Aspirante</label>
                            <input type="text" class="form-control" name="documento" autocomplete="off" required value="<?= $documento; ?>" placeholder="Ej: 1234567890">
                            <small class="form-text">Documento de identidad del estudiante aspirante.</small>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn-consultar-estado">
                        <i class="fas fa-search"></i>
                        <span>Consultar Estado</span>
                    </button>
                </div>
            </form>
        </div>
        
        <?php if (!empty($solicitud)): ?>
            <?php if ($num > 0 && $datos): ?>
                <!-- Resultado encontrado -->
                <div class="resultado-card">
                    <!-- Badge de estado actual -->
                    <div class="text-center mb-4">
                        <div class="status-badge" style="background: <?= $fondoSolicitud[$datos['asp_estado_solicitud']]; ?>; color: white;">
                            <i class="fas fa-info-circle"></i>
                            <span><?= $estadosSolicitud[$datos['asp_estado_solicitud']]; ?></span>
                        </div>
                    </div>
                    
                    <!-- Timeline visual -->
                    <div class="status-timeline">
                        <div class="timeline-track">
                            <div class="timeline-progress" style="width: <?= $progresoSolicitud[$datos['asp_estado_solicitud']]; ?>;"></div>
                        </div>
                        
                        <div class="timeline-steps">
                            <?php 
                            $estadoActual = $datos['asp_estado_solicitud'];
                            $iconos = [
                                1 => 'fa-clipboard-check',
                                2 => 'fa-credit-card',
                                3 => 'fa-file-alt',
                                4 => 'fa-clock',
                                5 => 'fa-times-circle',
                                6 => 'fa-check-circle',
                                7 => 'fa-user-check',
                                8 => 'fa-hourglass-half',
                                9 => 'fa-graduation-cap'
                            ];
                            
                            foreach($ordenReal as $index => $clave):
                                $esActivo = $datos['asp_estado_solicitud'] == $clave;
                                $esCompletado = array_search($estadoActual, $ordenReal) > $index;
                                $clase = $esActivo ? 'active' : ($esCompletado ? 'completed' : '');
                                $icono = $iconos[$clave] ?? 'fa-circle';
                            ?>
                                <div class="timeline-step <?= $clase; ?>">
                                    <div class="timeline-step-circle">
                                        <i class="fas <?= $icono; ?>"></i>
                                    </div>
                                    <div class="timeline-step-label"><?= $estadosSolicitud[$clave]; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Información del aspirante -->
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-hashtag"></i> N° de Solicitud
                            </div>
                            <div class="info-value">#<?= str_pad($datos['asp_id'], 6, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-id-card"></i> Documento
                            </div>
                            <div class="info-value"><?= htmlspecialchars($datos['asp_documento']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user"></i> Aspirante
                            </div>
                            <div class="info-value" style="font-size: 16px;"><?= htmlspecialchars($datos['asp_nombre']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar"></i> Fecha de Solicitud
                            </div>
                            <div class="info-value" style="font-size: 16px;"><?= date('d/m/Y', strtotime($datos['asp_fecha'])); ?></div>
                        </div>
                    </div>
                    
                    <!-- Observación si existe -->
                    <?php if (!empty($datos['asp_observacion'])): ?>
                        <div class="observacion-box">
                            <h5>
                                <i class="fas fa-comment-alt"></i>
                                Observación de la Institución
                            </h5>
                            <p><?= $datos['asp_observacion']; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Acción según estado -->
                    <?php if ($datos['asp_estado_solicitud'] == 3 || $datos['asp_estado_solicitud'] == 4): ?>
                        <div class="action-box">
                            <h4 style="color: #1565c0; font-weight: 700; margin-bottom: 16px;">
                                <i class="fas fa-file-alt"></i> Siguiente Paso
                            </h4>
                            <p style="color: #1976d2; margin-bottom: 24px;">
                                Tu solicitud está lista para continuar. Completa el formulario de admisión para avanzar en el proceso.
                            </p>
                            <a class="btn-action-primary" href="formulario.php?token=<?= md5($datos['asp_id']); ?>&id=<?= base64_encode($datos['asp_id']); ?>&idInst=<?= $_REQUEST['idInst']; ?>">
                                <i class="fas fa-edit"></i>
                                <span>Completar Formulario de Admisión</span>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Upload de comprobante -->
                    <?php if ($datos['asp_estado_solicitud'] == 1 || $datos['asp_estado_solicitud'] == 2): ?>
                        <div class="upload-section">
                            <h4>
                                <i class="fas fa-file-upload"></i>
                                Adjuntar Comprobante de Pago
                            </h4>
                            
                            <div class="upload-info">
                                <strong>💰 Inversión del proceso: $<?= number_format($valorInscripcion, 0, ".", "."); ?></strong><br><br>
                                <?= $config['cfgi_texto_info_cuenta']; ?>
                            </div>
                            
                            <form action="enviar-comprobante.php" method="post" enctype="multipart/form-data" id="formComprobante">
                                <input type="hidden" name="solicitud" value="<?= $solicitud; ?>">
                                <input type="hidden" name="idInst" value="<?= $_REQUEST['idInst']; ?>">
                                
                                <div class="file-input-wrapper">
                                    <input type="file" id="comprobante" name="comprobante" accept="image/*,.pdf" required onchange="mostrarNombreArchivo(this)">
                                    <label for="comprobante" class="file-input-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <div>
                                            <div style="font-size: 16px; margin-bottom: 4px;">Selecciona o arrastra tu comprobante</div>
                                            <div style="font-size: 13px; opacity: 0.8;">PDF, JPG, PNG (Máx. 5MB)</div>
                                        </div>
                                    </label>
                                    <div class="file-selected" id="fileSelected">
                                        <i class="fas fa-file-alt"></i>
                                        <span id="fileName"></span>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn-action-primary">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Enviar Comprobante</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Botón de pagar prematrícula -->
                    <?php if ($datos['asp_estado_solicitud'] == 6 && $config['cfgi_activar_boton_pagar_prematricula'] == 1): ?>
                        <div class="action-box" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                            <h4 style="color: #155724; font-weight: 700; margin-bottom: 16px;">
                                <i class="fas fa-money-check-alt"></i> Pago de Prematrícula
                            </h4>
                            <p style="color: #1e7e34; margin-bottom: 24px;">
                                ¡Felicitaciones! Tu solicitud ha sido aprobada. Procede a realizar el pago de la prematrícula.
                            </p>
                            <a href="<?= $config['cfgi_link_boton_pagar_prematricula']; ?>" class="btn-action-primary" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);" target="_blank">
                                <i class="fas fa-credit-card"></i>
                                <span>Pagar Prematrícula</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php else: ?>
                <!-- No se encontró -->
                <div class="resultado-card">
                    <div class="empty-state">
                        <i class="fas fa-search-minus"></i>
                        <h4>No se Encontró la Solicitud</h4>
                        <p>No encontramos ninguna solicitud con los datos proporcionados.<br>Por favor verifica el número de solicitud y documento.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Link para nueva solicitud -->
        <?php if (empty($solicitud) || ($num > 0 && $datos && $datos['asp_estado_solicitud'] == 5)): ?>
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: white; font-size: 15px; margin-bottom: 16px;">
                    ¿Aún no has iniciado tu proceso?
                </p>
                <a href="admision.php?idInst=<?= $_REQUEST['idInst']; ?>" style="color: white; text-decoration: underline; font-weight: 600; font-size: 16px;">
                    <i class="fas fa-arrow-left"></i> Iniciar Nueva Solicitud
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        function mostrarNombreArchivo(input) {
            const fileSelected = document.getElementById('fileSelected');
            const fileName = document.getElementById('fileName');
            
            if (input.files && input.files[0]) {
                fileName.textContent = input.files[0].name;
                fileSelected.style.display = 'block';
            }
        }
        
        // Drag and drop para el archivo
        const fileLabel = document.querySelector('.file-input-label');
        if (fileLabel) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileLabel.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                fileLabel.addEventListener(eventName, () => {
                    fileLabel.style.borderColor = '#ff9800';
                    fileLabel.style.background = 'rgba(255, 255, 255, 0.9)';
                }, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                fileLabel.addEventListener(eventName, () => {
                    fileLabel.style.borderColor = '#ffc107';
                    fileLabel.style.background = 'rgba(255, 255, 255, 0.5)';
                }, false);
            });
            
            fileLabel.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                const input = document.getElementById('comprobante');
                
                if (files && files[0]) {
                    input.files = files;
                    mostrarNombreArchivo(input);
                }
            }
        }
    </script>
</body>
</html>
