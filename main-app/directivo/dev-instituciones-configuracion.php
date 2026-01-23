<?php
include("session.php");

$idPaginaInterna = 'DV0032';

Modulos::verificarPermisoDev();

include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

// Incluir clases necesarias
require_once(ROOT_PATH."/main-app/class/Tables/BDT_configuracion.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");

// Obtener ID de institución y año
$id = !empty($_GET['id']) ? base64_decode($_GET['id']) : $_SESSION["idInstitucion"];
$year = $_SESSION["bd"];
if (!empty($_GET['year'])) {
    $year = base64_decode($_GET['year']);
}

// Obtener años disponibles de la institución
$yearsDisponibles = [];
try {
	$sqlInstitucion = "SELECT ins_years FROM {$baseDatosServicios}.instituciones WHERE ins_id = ?";
	$consultaInstitucion = BindSQL::prepararSQL($sqlInstitucion, [$id]);
	if ($consultaInstitucion) {
		$datosInstitucion = mysqli_fetch_array($consultaInstitucion, MYSQLI_BOTH);
		if (!empty($datosInstitucion['ins_years'])) {
			$yearsArray = explode(",", $datosInstitucion['ins_years']);
			$yearStart = intval($yearsArray[0]);
			$yearEnd = intval($yearsArray[1] ?? $yearsArray[0]);
			while($yearStart <= $yearEnd) {
				$yearsDisponibles[] = $yearStart;
				$yearStart++;
			}
		}
	}
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

// Si no hay años disponibles, usar el año actual
if (empty($yearsDisponibles)) {
	$yearsDisponibles = [$year];
}

// Cargar datos de configuración
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

// Si no hay datos de configuración, inicializar array vacío para evitar errores
if (empty($datosConfiguracion)) {
	$datosConfiguracion = [
		'conf_id' => '',
		'conf_periodo' => '',
		'conf_periodos_maximos' => 4,
		'conf_max_peso_archivos' => 10,
		'ins_siglas' => ''
	];
}

// Obtener ins_years para la barra superior si no está en datosConfiguracion
if (empty($datosConfiguracion['ins_years']) && !empty($yearsDisponibles)) {
	$datosConfiguracion['ins_years'] = min($yearsDisponibles) . ',' . max($yearsDisponibles);
}

// Variables para el formulario
$configDEV = 1; // Siempre es 1 para páginas de dev
$disabledPermiso = "";

if (!Modulos::validarPermisoEdicion() && $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
	$disabledPermiso = "readonly";
}
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
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
    <?php include("../compartido/encabezado.php"); ?>

    <?php include("../compartido/panel-color.php"); ?>
    <!-- start page container -->
    <div class="page-container">
        <?php include("../compartido/menu.php"); ?>
        <!-- start page content -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <?php include_once("includes/formulario-configuracion-contenido.php"); ?>
            </div>
            <!-- end page content -->
            <?php // include("../compartido/panel-configuracion.php");
            ?>
        </div>
        <!-- end page container -->
        <?php include("../compartido/footer.php"); ?>
    </div>
    <!-- start js include path -->
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../config-general/assets/plugins/popper/popper.js"></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script src="../../config-general/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
    <!-- Common js-->
    <script src="../../config-general/assets/js/app.js"></script>
    <script src="../../config-general/assets/js/layout.js"></script>
    <script src="../../config-general/assets/js/theme-color.js"></script>
    <!-- notifications -->
    <script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
    <script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>
    <!-- Material -->
    <script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- dropzone -->
    <script src="../../config-general/assets/plugins/dropzone/dropzone.js"></script>
    <!--tags input-->
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
    <script src="../../config-general/assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
    <!--select2-->
    <script src="../../config-general/assets/plugins/select2/js/select2.js"></script>
    <script src="../../config-general/assets/js/pages/select2/select2-init.js"></script>
    <!-- end js include path -->
    <script src="../ckeditor/ckeditor.js"></script>

    <script>
        // Replace the <textarea id="editor1"> with a CKEditor 4
        // instance, using default configuration.
        CKEDITOR.replace('editor1');
        CKEDITOR.replace( 'editor2' );
    </script>
    </body>

    <!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->

    </html>