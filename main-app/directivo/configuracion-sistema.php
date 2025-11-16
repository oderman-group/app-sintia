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
    
    <!-- 🛩️ Cockpit Theme - Aviation Control Panel -->
    <link href="../css/cockpit-theme.css?v=<?=time()?>" rel="stylesheet" type="text/css" />
    
    <style>
        /* Ajustes finos específicos de esta página si son necesarios */
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
                    
                    <!-- 🛩️ Cockpit Control Header -->
                    <div class="config-page-header">
                        <div class="header-content">
                            <h1>
                                <i class="fa fa-dashboard"></i>
                                PANEL DE CONTROL - <?= strtoupper($frases[17][$datosUsuarioActual['uss_idioma']]); ?>
                            </h1>
                            <p>
                                <span class="led-indicator green"></span>
                                SISTEMA ACTIVO | <?= strtoupper($datosConfiguracion['ins_siglas'] ?? 'SINTIA'); ?> | AÑO <?=$year?> | CONTROLADOR: <?= strtoupper($datosUsuarioActual['uss_usuario']); ?>
                            </p>
                        </div>
                        <div class="status-badge">
                            <span class="led-indicator green"></span>
                            ONLINE
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
        <p>PROCESANDO...</p>
    </div>
    
    <!-- 🛩️ Cockpit Entry Screen - Pantalla de Entrada al Centro de Mando -->
    <div class="cockpit-entry-overlay" id="cockpitEntry">
        <div class="cockpit-entry-container">
            <!-- Scanner Lines -->
            <div class="scan-line scan-line-1"></div>
            <div class="scan-line scan-line-2"></div>
            
            <!-- Logo Radar -->
            <div class="radar-container">
                <div class="radar-circle"></div>
                <div class="radar-scan"></div>
                <div class="radar-center">
                    <i class="fa fa-dashboard"></i>
                </div>
            </div>
            
            <!-- Mensaje de Bienvenida -->
            <div class="entry-message">
                <h1 class="entry-title">
                    <span class="led-indicator green"></span>
                    ACCESO AUTORIZADO
                </h1>
                <div class="entry-subtitle">CONTROLADOR: <?= strtoupper($datosUsuarioActual['uss_usuario']); ?></div>
                
                <div class="entry-description">
                    <p>Estás a punto de acceder al <strong>CENTRO DE MANDO</strong> del sistema SINTIA.</p>
                    <p>Desde este panel de control aeronáutico podrás:</p>
                    <ul class="entry-features">
                        <li><i class="fa fa-check-circle"></i> Ajustar configuraciones generales del sistema</li>
                        <li><i class="fa fa-check-circle"></i> Personalizar comportamiento y preferencias</li>
                        <li><i class="fa fa-check-circle"></i> Configurar informes y reportes</li>
                        <li><i class="fa fa-check-circle"></i> Gestionar permisos y seguridad</li>
                        <li><i class="fa fa-check-circle"></i> Personalizar estilos y apariencia</li>
                    </ul>
                </div>
                
                <div class="entry-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    Los cambios realizados afectarán la configuración de <strong><?= $datosConfiguracion['ins_siglas'] ?? 'la institución'; ?></strong> para el año <strong><?=$year?></strong>
                </div>
                
                <button type="button" class="btn-enter-cockpit" id="btnEnterCockpit">
                    <span class="btn-led"></span>
                    <i class="fa fa-arrow-circle-right"></i>
                    INGRESAR AL PANEL DE CONTROL
                    <span class="btn-arrow">›››</span>
                </button>
                
                <div class="entry-footer">
                    <span class="system-status">
                        <span class="status-dot"></span>
                        SISTEMAS: OPERATIVOS
                    </span>
                    <span class="system-time" id="systemTime"></span>
                </div>
            </div>
        </div>
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
        
        // ========================================
        // 🛩️ COCKPIT ENTRY SCREEN CONTROLLER
        // ========================================
        
        $(document).ready(function() {
            // Actualizar reloj del sistema en tiempo real
            function updateSystemTime() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                $('#systemTime').text(hours + ':' + minutes + ':' + seconds + ' UTC-5');
            }
            
            // Actualizar cada segundo
            updateSystemTime();
            setInterval(updateSystemTime, 1000);
            
            // Manejar clic en el botón de entrada
            $('#btnEnterCockpit').on('click', function() {
                const $btn = $(this);
                const $overlay = $('#cockpitEntry');
                
                // Cambiar texto del botón
                $btn.html('<i class="fa fa-spinner fa-spin"></i> INICIANDO SISTEMAS...');
                $btn.css('pointer-events', 'none');
                
                // Simular secuencia de inicio (como pre-vuelo)
                setTimeout(function() {
                    $btn.html('<i class="fa fa-check-circle"></i> SISTEMAS VERIFICADOS');
                    $btn.css('border-color', 'var(--led-green)');
                    $btn.css('color', 'var(--led-green)');
                }, 800);
                
                setTimeout(function() {
                    $btn.html('<i class="fa fa-plane"></i> ACCEDIENDO AL PANEL...');
                }, 1600);
                
                // Ocultar overlay con animación
                setTimeout(function() {
                    $overlay.addClass('hidden');
                    
                    // Guardar en sessionStorage que ya vio la intro
                    sessionStorage.setItem('cockpitIntroShown', 'true');
                }, 2400);
            });
            
            // Verificar si ya vio la intro en esta sesión
            if (sessionStorage.getItem('cockpitIntroShown') === 'true') {
                $('#cockpitEntry').addClass('hidden');
            }
        });
    </script>
    
    <!-- 🖼️ Lightbox Moderno para Imágenes -->
    <div class="lightbox-overlay" id="lightboxOverlay">
        <div class="lightbox-close" id="lightboxClose">
            <i class="fa fa-times"></i>
        </div>
        <div class="lightbox-content">
            <img src="" alt="Vista previa" class="lightbox-image" id="lightboxImage">
        </div>
        <div class="lightbox-title" id="lightboxTitle">Vista Previa</div>
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
            $('#lightboxTitle').text(imgAlt);
            
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

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>
