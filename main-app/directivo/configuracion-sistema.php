<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0057';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_configuracion.php");
require_once ROOT_PATH.'/main-app/class/App/Academico/Calificacion.php';
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

$year = $_SESSION["bd"];
if (!empty($_GET['year'])) {
    $year = base64_decode($_GET['year']);
}

$id = $_SESSION["idInstitucion"];
if (!empty($_GET['id'])) {
    $id = base64_decode($_GET['id']);
}

try {
	$sqlConfig = "SELECT configuracion.*, ins_siglas, ins_years 
		FROM {$baseDatosServicios}.configuracion 
		INNER JOIN {$baseDatosServicios}.instituciones ON ins_id = conf_id_institucion
		WHERE conf_id_institucion = ? AND conf_agno = ?";

	$consultaConfiguracion = BindSQL::prepararSQL($sqlConfig, [$id, $year]);
	$datosConfiguracion = $consultaConfiguracion ? mysqli_fetch_array($consultaConfiguracion, MYSQLI_BOTH) : [];
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
	$datosConfiguracion = [];
}

$disabledPermiso = "";

if (!Modulos::validarPermisoEdicion() && $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
	$disabledPermiso = "readonly";
}

$configDEV   = 0;
$institucion = '';

if ($idPaginaInterna == 'DV0032')
{ 
    $configDEV =1; $institucion = "de <b>".$datosConfiguracion['ins_siglas']."</b> (". $year .")"; 
}

$predicado = [
    'institucion'   => $id,
    'year'          => $year
];
$hayRegistroEnCalificaciones = Academico_Calificacion::contarRegistrosEnCalificaciones($predicado) > 0 ? true : false;
$disabledCamposConfiguracion = $hayRegistroEnCalificaciones ? 'readonly' : '';
?>

	<!--bootstrap -->
    <link href="../../config-general/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link href="../../config-general/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Theme Styles -->
    <link href="../../config-general/assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
	<!-- dropzone -->
    <link href="../../config-general/assets/plugins/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <!--tagsinput-->
    <link href="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
    <!--select2-->
    <link href="../../config-general/assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
    <link href="../../config-general/assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        /* Estilos profesionales para configuración del sistema */
        .config-page-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border-radius: 8px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .config-page-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .config-page-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
            color: #ecf0f1;
        }

        .nav-tabs-modern {
            border-bottom: 2px solid #e0e6ed;
            margin-bottom: 25px;
        }

        .nav-tabs-modern .nav-link {
            color: #555;
            font-weight: 500;
            padding: 12px 20px;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tabs-modern .nav-link:hover {
            color: #2c3e50;
            border-bottom-color: #bdc3c7;
            background: #f8f9fa;
        }

        .nav-tabs-modern .nav-link.active {
            color: #2c3e50;
            border-bottom-color: #2c3e50;
            background: transparent;
            font-weight: 600;
        }

        .tab-content-modern {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2c3e50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-overlay p {
            color: white;
            font-size: 16px;
            font-weight: 500;
        }

        /* Lightbox - Oculto por defecto */
        .lightbox-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            animation: fadeIn 0.3s ease-out;
            backdrop-filter: blur(10px);
        }

        .lightbox-overlay.active {
            display: flex;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            color: white;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 100000;
        }

        .lightbox-close:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: rotate(90deg);
        }

        .lightbox-content {
            position: relative;
            max-width: 95vw;
            max-height: 95vh;
            animation: zoomIn 0.3s ease-out;
        }

        .lightbox-image {
            max-width: 100%;
            max-height: 95vh;
            width: auto;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .lightbox-title {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 20px;
            z-index: 100000;
            display: none;
        }

        .lightbox-overlay.active .lightbox-title:not(:empty) {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Popover preview large */
        .popover-preview-large {
            max-width: 600px !important;
            width: 600px;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            background: #fff;
        }

        .popover-preview-large .popover-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
        }

        .popover-preview-large .popover-body {
            padding: 20px;
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Imagen de preview */
        .preview-image-large {
            width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            cursor: zoom-in;
        }

        .preview-image-large:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        @media (max-width: 768px) {
            .popover-preview-large {
                max-width: 95vw !important;
                width: 95vw;
            }
            
            .popover-preview-large .popover-body {
                padding: 15px;
                max-height: 70vh;
            }
        }
    </style>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php");?>
    <div class="page-wrapper">
        <?php include("../compartido/encabezado.php");?>
		
        <?php include("../compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php include("../compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    
                    <!-- Header Profesional -->
                    <div class="config-page-header">
                        <h1>
                            <i class="fa fa-cog"></i>
                            <?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?>
                        </h1>
                        <p>
                            <?= strtoupper($datosConfiguracion['ins_siglas'] ?? 'SINTIA'); ?> | Año <?=$year?>
                        </p>
                    </div>

                    <?php 
                    $tabs = [
                        'general' => [
                            'name' => 'General',
                            'icon' => 'fa-home',
                            'aria-selected' => 'true',
                            'active' => 'active',
                            'show' => 'show',
                            'page-content' => 'includes/formulario-configuracion-contenido.php',
                        ], 
                        'comportamiento-sistema' => [
                            'name' => 'Comportamiento',
                            'icon' => 'fa-sliders',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-comportamiento.php',
                        ],
                        'preferencias' => [
                            'name' => 'Preferencias',
                            'icon' => 'fa-heart',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-preferencias.php',
                        ],
                        'informes' => [
                            'name' => 'Informes',
                            'icon' => 'fa-file-text',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-informes.php',
                        ],
                        'permisos' => [
                            'name' => 'Permisos',
                            'icon' => 'fa-shield',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-permisos.php',
                        ],
                        'estilos-apariencia' => [
                            'name' => 'Estilos',
                            'icon' => 'fa-paint-brush',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-estilos.php',
                        ]
                    ];
                    ?>

                    <!-- Modern Tabs -->
                    <nav>
                        <div class="nav nav-tabs nav-tabs-modern" id="nav-tab" role="tablist">
                            <?php foreach ($tabs as $tab => $datos): ?>
                                <a class="nav-item nav-link <?=$datos['active'];?>" data-toggle="tab" href="#<?=$tab;?>" role="tab" aria-selected="<?=$datos['aria-selected'];?>">
                                    <i class="fa <?=$datos['icon'];?>"></i>
                                    <?=$datos['name'];?>
                                </a>
                            <?php endforeach;?>
                        </div>
                    </nav>

                    <!-- Tab Content -->
                    <div class="tab-content tab-content-modern" id="nav-tabContent">
                        <?php foreach ($tabs as $tab => $datos): ?>
                            <div class="tab-pane fade <?=$datos['show'];?> <?=$datos['active'];?>" id="<?=$tab;?>" role="tabpanel">
                                <?php include_once($datos['page-content']);?>
                            </div>
                        <?php endforeach;?>
                    </div>

                </div>
                <!-- end page content -->
             <?php // include("../compartido/panel-configuracion.php");?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php");?>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <p>PROCESANDO...</p>
    </div>
    
    
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"  charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js"  charset="UTF-8"></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>	
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js" ></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js" ></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js" ></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js" ></script>
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>

    <script>
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace( 'editor1' );
        CKEDITOR.replace( 'editor2' );
        
        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        
        // Show loading overlay on form submit
        $('form').on('submit', function() {
            $('#loadingOverlay').addClass('active');
        });
        
        // Smooth scroll to top when changing tabs
        $('.nav-tabs-modern .nav-link').on('click', function() {
            $('html, body').animate({
                scrollTop: $('.config-page-header').offset().top - 100
            }, 500);
        });
    </script>
    
    <!-- Lightbox para Imágenes -->
    <div class="lightbox-overlay" id="lightboxOverlay">
        <div class="lightbox-close" id="lightboxClose">
            <i class="fa fa-times"></i>
        </div>
        <div class="lightbox-content">
            <img src="" alt="" class="lightbox-image" id="lightboxImage">
        </div>
        <div class="lightbox-title" id="lightboxTitle"></div>
    </div>
    
    <script>
    // Sistema de Lightbox para Imágenes
    $(document).ready(function() {
        // Click en cualquier imagen con clase preview-image-large
        $(document).on('click', '.preview-image-large', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const imgSrc = $(this).attr('src');
            const imgAlt = $(this).closest('.popover-content').find('label').text() || 'Vista Previa';
            
            // Configurar lightbox
            $('#lightboxImage').attr('src', imgSrc);
            if (imgAlt && imgAlt.trim() !== '') {
                $('#lightboxTitle').text(imgAlt).show();
            } else {
                $('#lightboxTitle').text('').hide();
            }
            
            // Mostrar lightbox con animación
            $('#lightboxOverlay').addClass('active');
            
            // Prevenir scroll del body
            $('body').css('overflow', 'hidden');
        });
        
        // Cerrar lightbox al hacer click en X
        $('#lightboxClose').on('click', function() {
            cerrarLightbox();
        });
        
        // Cerrar lightbox al hacer click en el fondo oscuro
        $('#lightboxOverlay').on('click', function(e) {
            if (e.target === this) {
                cerrarLightbox();
            }
        });
        
        // Cerrar lightbox con tecla ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#lightboxOverlay').hasClass('active')) {
                cerrarLightbox();
            }
        });
        
        function cerrarLightbox() {
            $('#lightboxOverlay').removeClass('active');
            $('body').css('overflow', 'auto');
        }
    });
    </script>
</body>

</html>
