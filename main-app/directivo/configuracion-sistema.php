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

$year = $_SESSION["bd"];
if (!empty($_GET['year'])) {
    $year = base64_decode($_GET['year']);
}

$id = $_SESSION["idInstitucion"];
if (!empty($_GET['id'])) {
    $id = base64_decode($_GET['id']);
}

try {
    $consultaConfiguracion = mysqli_query($conexion, "SELECT configuracion.*, ins_siglas, ins_years FROM " . $baseDatosServicios . ".configuracion 
    INNER JOIN " . $baseDatosServicios . ".instituciones ON ins_id=conf_id_institucion
    WHERE conf_id_institucion='" . $id . "' AND conf_agno='" . $year . "'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

$datosConfiguracion = mysqli_fetch_array($consultaConfiguracion, MYSQLI_BOTH);

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
    
    <!-- Custom Styles for Modern Design -->
    <style>
        /* Variables */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.15);
            --radius-md: 12px;
            --radius-lg: 16px;
        }
        
        /* Page Header */
        .config-page-header {
            background: var(--primary-gradient);
            padding: 40px 30px;
            border-radius: var(--radius-lg);
            margin-bottom: 30px;
            box-shadow: var(--shadow-lg);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .config-page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .config-page-header .header-content {
            position: relative;
            z-index: 1;
        }
        
        .config-page-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .config-page-header h1 i {
            font-size: 36px;
            opacity: 0.9;
        }
        
        .config-page-header p {
            font-size: 16px;
            opacity: 0.95;
            margin: 0;
        }
        
        /* Modern Tabs */
        .nav-tabs-modern {
            background: white;
            padding: 15px 20px 0 20px;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            box-shadow: var(--shadow-sm);
            border: none;
            margin-bottom: 0;
        }
        
        .nav-tabs-modern .nav-item {
            margin-right: 10px;
        }
        
        .nav-tabs-modern .nav-link {
            border: none;
            background: transparent;
            color: #6b7280;
            padding: 12px 24px;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-tabs-modern .nav-link i {
            font-size: 18px;
        }
        
        .nav-tabs-modern .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        
        .nav-tabs-modern .nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .nav-tabs-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: white;
            border-radius: 3px 3px 0 0;
        }
        
        /* Tab Content Container */
        .tab-content-modern {
            background: white;
            padding: 30px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            box-shadow: var(--shadow-md);
            min-height: 400px;
        }
        
        /* Modern Panel */
        .panel-modern {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }
        
        .panel-modern .panel-heading {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-bottom: 2px solid #667eea;
            padding: 20px 25px;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .panel-modern .panel-heading i {
            font-size: 22px;
            color: #667eea;
        }
        
        .panel-modern .panel-body {
            padding: 30px 25px;
        }
        
        /* Form Groups */
        .form-group-modern {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius-md);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .form-group-modern:hover {
            box-shadow: var(--shadow-sm);
            border-color: #d1d5db;
        }
        
        .form-group-modern label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group-modern label i {
            color: #667eea;
        }
        
        .form-group-modern .form-control,
        .form-group-modern .select2,
        .form-group-modern select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 15px;
            min-height: 45px;
            transition: all 0.3s ease;
        }
        
        /* Mejorar selects específicamente */
        select.form-control {
            padding: 12px 15px;
            min-height: 45px;
            font-size: 15px;
        }
        
        .form-group-modern .form-control:focus,
        .form-group-modern .select2:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Info Tooltips */
        .info-tooltip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            cursor: help;
            transition: all 0.3s ease;
        }
        
        .info-tooltip:hover {
            background: #764ba2;
            transform: scale(1.1);
        }
        
        /* Required Indicator */
        .required-indicator {
            color: #ef4444;
            font-weight: 700;
            margin-left: 3px;
        }
        
        /* Save Button Container */
        .save-button-container {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            border-top: 2px solid #e5e7eb;
            padding: 20px 25px;
            margin: 30px -25px -30px -25px;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        }
        
        /* Modern Buttons */
        .btn-modern-primary {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-modern-secondary {
            background: white;
            border: 2px solid #e5e7eb;
            color: #6b7280;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-modern-secondary:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }
        
        /* Alert Box */
        .alert-modern {
            border-radius: var(--radius-md);
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }
        
        .alert-modern i {
            font-size: 24px;
        }
        
        .alert-modern.alert-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        .alert-modern.alert-warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        
        .alert-modern.alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .config-page-header {
                padding: 30px 20px;
            }
            
            .config-page-header h1 {
                font-size: 24px;
            }
            
            .nav-tabs-modern {
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .tab-content-modern {
                padding: 20px 15px;
            }
            
            .panel-modern .panel-body {
                padding: 20px 15px;
            }
        }
        
        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
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
                    
                    <!-- Modern Page Header -->
                    <div class="config-page-header">
                        <div class="header-content">
                            <h1>
                                <i class="fa fa-cogs"></i>
                                <?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> del Sistema
                            </h1>
                            <p>
                                Personaliza y ajusta la configuración de tu institución <?=$institucion?>
                            </p>
                        </div>
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
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>
