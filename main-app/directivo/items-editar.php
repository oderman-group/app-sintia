<?php
include("session.php");
$idPaginaInterna = 'DT0261';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$id = '';
if (!empty($_GET['id'])) {
    $idDecoded = base64_decode($_GET['id'], true);
    if ($idDecoded === false || empty($idDecoded)) {
        $idDecoded = base64_decode($_GET['id']);
    }
    $id = $idDecoded;
}

if (empty($id)) {
    echo '<script type="text/javascript">alert("ID de item no proporcionado."); window.location.href="items.php";</script>';
    exit();
}

// Usar nombre de variable único para evitar conflictos con archivos incluidos
$datosItem = Movimientos::traerDatosItems($conexion, $config, $id);

// Validar que se encontró el item
$itemEncontrado = !empty($datosItem) && is_array($datosItem) && !empty($datosItem['item_id']);
if (!$itemEncontrado) {
    // Debug: verificar qué ID se está buscando
    error_log("Item no encontrado. ID recibido: {$id}, ID decodificado de: " . ($_GET['id'] ?? 'N/A'));
    // Inicializar con valores por defecto para evitar warnings
    $datosItem = [
        'item_id' => $id,
        'name' => '',
        'price' => '0',
        'tax' => '0',
        'description' => '',
        'item_type' => 'D'
    ];
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
<?php require_once(ROOT_PATH."/main-app/compartido/body.php");?>
    <div class="page-wrapper">
        <?php require_once(ROOT_PATH."/main-app/compartido/encabezado.php");?>
		
        <?php require_once(ROOT_PATH."/main-app/compartido/panel-color.php");?>
        <!-- start page container -->
        <div class="page-container">
 			<?php require_once(ROOT_PATH."/main-app/compartido/menu.php");?>
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> Items</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="items.php" onClick="deseaRegresar(this)">Items</a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> Items</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                                <?php if (!$itemEncontrado) { ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> <strong>Advertencia:</strong> No se pudo cargar la información del item con ID: <?=htmlspecialchars($id)?>. Verifique que el item exista en la base de datos.
                                </div>
                                <?php } ?>
								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> Items</header>
                                	<div class="panel-body">
									<form name="formularioGuardar" action="items-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$id?>" name="id">

                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label"><?=$frases[187][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="nombre" class="form-control" value="<?=htmlspecialchars($datosItem['name'] ?? '')?>" required <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label"><?=$frases[381][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" name="precio" class="form-control" value="<?=htmlspecialchars($datosItem['price'] ?? '0')?>" required <?=$disabledPermiso;?>>
                                            </div>

                                            <label class="col-sm-1 control-label"><?=$frases[382][$datosUsuarioActual['uss_idioma']];?>:</label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" name="iva" class="form-control" value="<?=htmlspecialchars($datosItem['tax'] ?? '0')?>" <?=$disabledPermiso;?>>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-1 control-label">Tipo <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select name="item_type" id="item_type" class="form-control" required <?=$disabledPermiso;?> onchange="toggleApplicationTime()">
                                                    <option value="D" <?=($datosItem['item_type'] ?? 'D') == 'D' ? 'selected' : ''?>>Débito (Cargo)</option>
                                                    <option value="C" <?=($datosItem['item_type'] ?? 'D') == 'C' ? 'selected' : ''?>>Crédito (Descuento)</option>
                                                </select>
                                            </div>
                                            <?php
                                            $itemType = $datosItem['item_type'] ?? 'D';
                                            $applicationTime = $datosItem['application_time'] ?? ($itemType == 'C' ? 'ANTE_IMPUESTO' : null);
                                            $showApplicationTime = ($itemType == 'C');
                                            ?>
                                            <label class="col-sm-1 control-label" id="label_application_time" style="display: <?=$showApplicationTime ? '' : 'none';?>;">Aplicación <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4" id="div_application_time" style="display: <?=$showApplicationTime ? '' : 'none';?>;">
                                                <select name="application_time" id="application_time" class="form-control" <?=$disabledPermiso;?>>
                                                    <option value="ANTE_IMPUESTO" <?=($applicationTime == 'ANTE_IMPUESTO') ? 'selected' : ''?>>Antes del Impuesto</option>
                                                    <option value="POST_IMPUESTO" <?=($applicationTime == 'POST_IMPUESTO') ? 'selected' : ''?>>Después del Impuesto</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-12 control-label"><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-12">
                                                <textarea cols="80" id="editor1" name="descrip" class="form-control" rows="8" placeholder="Escribe aqui la descripción para este item" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>><?=htmlspecialchars($datosItem['description'] ?? '')?></textarea>
                                            </div>
                                        </div>
                                        
                                       
                                        <?php $botones = new botonesGuardar("items.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- end page container -->
        <?php require_once(ROOT_PATH."/main-app/compartido/footer.php");?>
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
        CKEDITOR.replace( 'editor1' );
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>