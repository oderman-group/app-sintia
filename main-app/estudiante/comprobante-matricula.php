<?php include("session.php"); ?>
<?php include("verificar-usuario.php"); ?>
<?php include("verificar-sanciones.php"); ?>
<?php $idPaginaInterna = 'ES0045'; ?>
<?php include("../compartido/historial-acciones-guardar.php"); ?>
<?php include("../compartido/head.php"); ?>

<?php
// Obtener información de la institución
$infoInstitucion = [];
if(isset($conexion)) {
    try {
        $consultaInfo = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_informacion 
            WHERE info_institucion='".$config['conf_id_institucion']."' 
            AND info_year='".$_SESSION["bd"]."'");
        if(mysqli_num_rows($consultaInfo) > 0) {
            $infoInstitucion = mysqli_fetch_array($consultaInfo, MYSQLI_BOTH);
        }
    } catch (Exception $e) {
        // Silenciar error si no existe la tabla
    }
}

// Obtener nombre de la institución
$nombreInstitucion = '';
if(isset($informacion_inst) && !empty($informacion_inst['info_nombre'])) {
    $nombreInstitucion = $informacion_inst['info_nombre'];
} elseif(isset($datosUnicosInstitucion) && !empty($datosUnicosInstitucion['ins_nombre'])) {
    $nombreInstitucion = $datosUnicosInstitucion['ins_nombre'];
} elseif(!empty($infoInstitucion['info_nombre'])) {
    $nombreInstitucion = $infoInstitucion['info_nombre'];
}

// Obtener datos de contacto
$direccion = isset($informacion_inst['info_direccion']) ? $informacion_inst['info_direccion'] : (isset($infoInstitucion['info_direccion']) ? $infoInstitucion['info_direccion'] : '');
$telefono = isset($informacion_inst['info_telefono']) ? $informacion_inst['info_telefono'] : (isset($infoInstitucion['info_telefono']) ? $infoInstitucion['info_telefono'] : '');
$email = isset($informacion_inst['info_email']) ? $informacion_inst['info_email'] : (isset($infoInstitucion['info_email']) ? $infoInstitucion['info_email'] : '');
$web = isset($informacion_inst['info_web']) ? $informacion_inst['info_web'] : (isset($infoInstitucion['info_web']) ? $infoInstitucion['info_web'] : '');
$dane = isset($informacion_inst['info_dane']) ? $informacion_inst['info_dane'] : (isset($infoInstitucion['info_dane']) ? $infoInstitucion['info_dane'] : '');
$icfes = isset($informacion_inst['info_icfes']) ? $informacion_inst['info_icfes'] : (isset($infoInstitucion['info_icfes']) ? $infoInstitucion['info_icfes'] : '');
?>

<style>
    @media print {
        body { margin: 0; padding: 0; }
        .no-print { display: none !important; }
        .comprobante-container { box-shadow: none !important; margin: 0 !important; }
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px;
        min-height: 100vh;
    }
    
    .comprobante-container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    
    .comprobante-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        text-align: center;
        position: relative;
    }
    
    .comprobante-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(255,255,255,0.3);
    }
    
    .logo-container {
        margin-bottom: 20px;
    }
    
    .logo-container img {
        max-height: 80px;
        max-width: 100%;
        filter: brightness(0) invert(1);
    }
    
    .comprobante-title {
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .comprobante-subtitle {
        font-size: 18px;
        margin-top: 10px;
        opacity: 0.95;
        font-weight: 300;
    }
    
    .comprobante-body {
        padding: 40px 50px;
    }
    
    .info-section {
        margin-bottom: 35px;
    }
    
    .section-title {
        font-size: 14px;
        color: #667eea;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e0e0;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 20px;
    }
    
    .info-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #667eea;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .info-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .info-label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .info-value {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }
    
    .success-message {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 8px;
        text-align: center;
        margin: 30px 0;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .success-message i {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
        opacity: 0.9;
    }
    
    .success-message p {
        margin: 0;
        font-size: 18px;
        font-weight: 500;
    }
    
    .comprobante-footer {
        background: #f8f9fa;
        padding: 30px 50px;
        border-top: 1px solid #e0e0e0;
        text-align: center;
    }
    
    .footer-info {
        font-size: 12px;
        color: #666;
        line-height: 1.8;
        margin: 5px 0;
    }
    
    .footer-info strong {
        color: #667eea;
    }
    
    .print-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: transform 0.2s, box-shadow 0.2s;
        z-index: 1000;
    }
    
    .print-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }
    
    .print-button i {
        margin-right: 8px;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .comprobante-body {
            padding: 30px 25px;
        }
        
        .comprobante-footer {
            padding: 25px 20px;
        }
        
        .comprobante-title {
            font-size: 24px;
        }
    }
</style>

</head>
<!-- END HEAD -->

<body>

    <button class="print-button no-print" onclick="window.print()">
        <i class="fa fa-print"></i> Imprimir Comprobante
    </button>

    <div class="comprobante-container">
        <!-- Header -->
        <div class="comprobante-header">
            <div class="logo-container">
                <?php if(!empty($config['conf_logo'])) { ?>
                    <img src="<?=ROOT_PATH.'/'.$config['conf_logo'];?>" alt="Logo Institución">
                <?php } elseif(!empty($nombreInstitucion)) { ?>
                    <h2 style="margin:0; font-size: 28px;"><?=$nombreInstitucion;?></h2>
                <?php } else { ?>
                    <h2 style="margin:0; font-size: 28px;">Institución Educativa</h2>
                <?php } ?>
            </div>
            <h1 class="comprobante-title">Comprobante de Matrícula</h1>
            <p class="comprobante-subtitle">Año Académico <?=$_SESSION["bd"];?></p>
        </div>

        <!-- Body -->
        <div class="comprobante-body">
            <!-- Información del Estudiante -->
            <div class="info-section">
                <h3 class="section-title">
                    <i class="fa fa-user-graduate"></i> Información del Estudiante
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nombre Completo</div>
                        <div class="info-value">
                            <?= strtoupper($datosEstudianteActual["mat_primer_apellido"]." ".$datosEstudianteActual["mat_segundo_apellido"]." ".$datosEstudianteActual["mat_nombres"]); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Grado</div>
                        <div class="info-value"><?=$datosEstudianteActual["gra_nombre"];?></div>
                    </div>
                    <?php if(!empty($datosEstudianteActual["mat_grupo"])) { ?>
                    <div class="info-item">
                        <div class="info-label">Grupo</div>
                        <div class="info-value"><?=$datosEstudianteActual["mat_grupo"];?></div>
                    </div>
                    <?php } ?>
                    <?php if(!empty($datosEstudianteActual["mat_numero_matricula"])) { ?>
                    <div class="info-item">
                        <div class="info-label">Número de Matrícula</div>
                        <div class="info-value"><?=$datosEstudianteActual["mat_numero_matricula"];?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Información del Acudiente -->
            <div class="info-section">
                <h3 class="section-title">
                    <i class="fa fa-user-friends"></i> Información del Acudiente
                </h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nombre del Acudiente</div>
                        <div class="info-value"><?= strtoupper($datosEstudianteActual["uss_nombre"]." ".$datosEstudianteActual["uss_apellido1"]); ?></div>
                    </div>
                    <?php if(!empty($datosEstudianteActual["uss_direccion"])) { ?>
                    <div class="info-item">
                        <div class="info-label">Dirección</div>
                        <div class="info-value"><?=$datosEstudianteActual["uss_direccion"];?></div>
                    </div>
                    <?php } ?>
                    <?php if(!empty($datosEstudianteActual["uss_email"])) { ?>
                    <div class="info-item">
                        <div class="info-label">Correo Electrónico</div>
                        <div class="info-value"><?=$datosEstudianteActual["uss_email"];?></div>
                    </div>
                    <?php } ?>
                    <?php if(!empty($datosEstudianteActual["uss_celular"])) { ?>
                    <div class="info-item">
                        <div class="info-label">Teléfono</div>
                        <div class="info-value"><?=$datosEstudianteActual["uss_celular"];?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Mensaje de Confirmación -->
            <div class="success-message">
                <i class="fa fa-check-circle"></i>
                <p>La matrícula del estudiante se ha realizado con éxito para el año académico <?=$_SESSION["bd"];?>.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="comprobante-footer">
            <?php if(!empty($direccion)) { ?>
                <div class="footer-info">
                    <strong><i class="fa fa-map-marker-alt"></i> Dirección:</strong> <?=$direccion;?>
                </div>
            <?php } ?>
            <?php if(!empty($telefono)) { ?>
                <div class="footer-info">
                    <strong><i class="fa fa-phone"></i> Teléfono:</strong> <?=$telefono;?>
                </div>
            <?php } ?>
            <?php if(!empty($email)) { ?>
                <div class="footer-info">
                    <strong><i class="fa fa-envelope"></i> Email:</strong> <?=$email;?>
                </div>
            <?php } ?>
            <?php if(!empty($web)) { ?>
                <div class="footer-info">
                    <strong><i class="fa fa-globe"></i> Web:</strong> <a href="<?=$web;?>" target="_blank"><?=$web;?></a>
                </div>
            <?php } ?>
            <?php if(!empty($dane)) { ?>
                <div class="footer-info">
                    <strong>Código DANE:</strong> <?=$dane;?>
                </div>
            <?php } ?>
            <?php if(!empty($icfes)) { ?>
                <div class="footer-info">
                    <strong>Código ICFES:</strong> <?=$icfes;?>
                </div>
            <?php } ?>
            <div class="footer-info" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                <small style="color: #999;">
                    Este documento fue generado el <?=date('d/m/Y H:i:s');?> mediante el Sistema SINTIA
                </small>
            </div>
        </div>
    </div>

    <script>
        // Mejorar la experiencia de impresión
        window.onbeforeprint = function() {
            document.body.style.background = 'white';
        };
        window.onafterprint = function() {
            document.body.style.background = '';
        };
    </script>

</body>

</html>