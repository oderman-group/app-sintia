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
	$disabledPermiso = "disabled";
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
$disabledCamposConfiguracion = $hayRegistroEnCalificaciones ? 'disabled' : '';
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
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> del Sistema </div>
                                <?php include("../compartido/texto-manual-ayuda.php"); ?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <?php
                                    if($idPaginaInterna == 'DV0032'){
                                        echo '<li><a class="parent-item" href="javascript:void(0);" name="dev-instituciones.php" onClick="deseaRegresar(this)">Insituciones</a>&nbsp;<i class="fa fa-angle-right"></i></li>';
                                    }
                                ?>
                                <li class="active"><?= $frases[17][$datosUsuarioActual['uss_idioma']]; ?> del Sistema </li>
                            </ol>
                        </div>
                    </div>
                    <?php //include_once("includes/formulario-configuracion-contenido.php"); ?>

                    <?php 
                    $tabs = [
                        'general' => [
                            'name' => 'General',
                            'aria-selected' => 'true',
                            'active' => 'active',
                            'show' => 'show',
                            'page-content' => 'includes/formulario-configuracion-contenido.php',
                        ], 
                        'comportamiento-sistema' => [
                            'name' => 'Comportamiento del sistema',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-comportamiento.php',
                        ],
                        'preferencias' => [
                            'name' => 'Preferencias',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-preferencias.php',
                        ],
                        'informes' => [
                            'name' => 'Informes y reportes',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-informes.php',
                        ],
                        'permisos' => [
                            'name' => 'Permisos',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-permisos.php',
                        ],
                        'estilos-apariencia' => [
                            'name' => 'Estilos y apariencia',
                            'aria-selected' => 'false',
                            'active' => '',
                            'show' => '',
                            'page-content' => 'includes/config-sistema-estilos.php',
                        ]
                    ];
                    ?>

                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <?php foreach ($tabs as $tab => $datos): ?>
                                <a class="nav-item nav-link <?=$datos['active'];?>" data-toggle="tab" href="#<?=$tab;?>" role="tab" aria-selected="<?=$datos['aria-selected'];?>"><?=$datos['name'];?></a>
                            <?php endforeach;?>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
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
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>