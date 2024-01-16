<?php
include("session.php");
$idPaginaInterna = 'DT0267';
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
    $id = base64_decode($_GET['id']);
}

$resultado = Movimientos::traerDatosAbonos($conexion, $config, $id);
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
                                <div class="page-title"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[385][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="abonos.php" onClick="deseaRegresar(this)"><?=$frases[385][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[385][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?> <?=$frases[385][$datosUsuarioActual['uss_idioma']];?></header>
                                	<div class="panel-body">
									<form name="formularioGuardar" action="abonos-actualizar.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" value="<?=$id?>" name="id">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[383][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" value="<?=UsuariosPadre::nombreCompletoDelUsuario($resultado)?>" readonly>
                                            </div>

                                            <label class="col-sm-2 control-label"><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-3">
                                                <input type="datetime" class="form-control" value="<?=$resultado['registration_date']?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[380][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span>
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Puedes buscar por ID de la factura o por el nombre del usuario que realiza el abono."><i class="fa fa-question"></i></button>
                                            </label>
                                            <div class="col-sm-9">
                                                <select class="form-control select2" id="selectFactura" name="idFactura" required <?=$disabledPermiso;?>>
                                                <?php
                                                    $filtro= "AND fcu_id='".$resultado['invoiced']."'";
                                                    $consultaFactura = Movimientos::listarInvoicedSelect($conexion, $config, $filtro);
                                                    $resultadoFactura = mysqli_fetch_array($consultaFactura, MYSQLI_BOTH);

                                                    $vlrAdicional = !empty($resultadoFactura['fcu_valor']) ? $resultadoFactura['fcu_valor'] : 0;
                                                    $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $resultadoFactura['fcu_id'], $vlrAdicional);
                                                ?>
                                                <option value="<?=$resultadoFactura['fcu_id']?>" selected><?=$resultadoFactura['fcu_id']."- ".UsuariosPadre::nombreCompletoDelUsuario($resultadoFactura)." (total: ".$totalNeto.")";?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#selectFactura').select2({
                                                placeholder: 'Seleccione la factura...',
                                                theme: "bootstrap",
                                                multiple: false,
                                                    ajax: {
                                                        type: 'GET',
                                                        url: 'ajax-traer-facturas.php',
                                                        processResults: function(data) {
                                                            data = JSON.parse(data);
                                                            return {
                                                                results: $.map(data, function(item) {
                                                                    return {
                                                                        id: item.value,
                                                                        text: item.label
                                                                    }
                                                                })
                                                            };
                                                        }
                                                    }
                                                });
                                            });
                                        </script>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[52][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" name="valor" class="form-control" required <?=$disabledPermiso;?> value="<?=$resultado['payment']?>">
                                            </div>

                                            <label class="col-sm-2 control-label"><?=$frases[386][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-3">
                                                <select class="form-control select2" id="metodoPago" name="metodoPago" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="EFECTIVO" <?= $resultado['payment_method'] == "EFECTIVO" ? "selected" : ""; ?>>Efectivo</option>
                                                    <option value="CHEQUE" <?= $resultado['payment_method'] == "CHEQUE" ? "selected" : ""; ?>>Cheque</option>
                                                    <option value="T_DEBITO" <?= $resultado['payment_method'] == "T_DEBITO" ? "selected" : ""; ?>>T. Débito</option>
                                                    <option value="T_CREDITO" <?= $resultado['payment_method'] == "T_CREDITO" ? "selected" : ""; ?>>T. Crédito</option>
                                                    <option value="TRANSFERENCIA" <?= $resultado['payment_method'] == "TRANSFERENCIA" ? "selected" : ""; ?>>Transferencia</option>
                                                    <option value="OTROS" <?= $resultado['payment_method'] == "OTROS" ? "selected" : ""; ?>>Otras Formas de pago</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[345][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <?php if (!empty($resultado['voucher']) and file_exists(ROOT_PATH.'/main-app/files/comprobantes/' . $resultado['voucher'])) { ?>
                                                    <a href="<?= REDIRECT_ROUTE; ?>/files/comprobantes/<?= $resultado['voucher']; ?>" target="_blank" class="link"><?= $resultado['voucher']; ?></a>
                                                <?php } ?>
                                                <input type="file" name="comprobante" class="form-control" <?=$disabledPermiso;?>>
                                            </div>
										</div>

                                        <div class="form-group row">
                                            <label class="col-sm-12 control-label"><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-12">
                                                <textarea cols="80" id="editor1" name="obser" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>><?=$resultado['observation']?></textarea>
                                            </div>
                                        </div>
                                        
                                        <a href="javascript:void(0);" name="items.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                        <?php if(Modulos::validarPermisoEdicion()){?>
                                            <button type="submit" class="btn  btn-info">
                                                <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                                            </button>
                                        <?php }?>
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