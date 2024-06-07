<?php
include("session.php");
$idPaginaInterna = 'DT0265';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
}

$codigoUnico=Utilidades::generateCode("ABO");
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
                                <div class="page-title"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="abonos.php" onClick="deseaRegresar(this)"><?=$frases[413][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                            <div class="panel">
                                <header class="panel-heading panel-heading-purple"><?=$frases[56][$datosUsuarioActual['uss_idioma']];?> <?=$frases[413][$datosUsuarioActual['uss_idioma']];?></header>
                                <div class="panel-body">
									<form name="formularioGuardar" action="abonos-guardar.php" method="post" enctype="multipart/form-data">
										<input type="hidden" value="<?=$codigoUnico?>" name="codigoUnico" id="idAbono">

										<div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[424][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2" id="select_cliente" name="cliente" onchange="mostrarTipoTransaccion()" required <?=$disabledPermiso;?>>
                                                </select>
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#select_cliente').select2({
                                                placeholder: 'Seleccione el usuario...',
                                                theme: "bootstrap",
                                                multiple: false,
                                                    ajax: {
                                                        type: 'GET',
                                                        url: '../compartido/ajax-listar-usuarios.php',
                                                        processResults: function(data) {
                                                            var radios = document.getElementsByName('tipoTransaccion');
                                                            
                                                            for (var i = 0; i < radios.length; i++) {
                                                                if (radios[i].checked) {
                                                                    radios[i].checked = false;
                                                                }
                                                            }
                                                            $('#mostrarFacturas').empty().hide().html('').show(1);
                                                            document.getElementById("divFacturas").style.display="none";
                                                            document.getElementById("divCuentasContables").style.display="none";
                                                            document.getElementById("divTipoTransaccion").style.display="none";
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
                                            <label class="col-sm-2 control-label"><?=$frases[414][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-3">
                                                <select class="form-control select2" id="metodoPago" name="metodoPago" required <?=$disabledPermiso;?>>
                                                    <option value="" >Seleccione una opción</option>
													<option value="EFECTIVO" >Efectivo</option>
													<option value="CHEQUE" >Cheque</option>
													<option value="T_DEBITO" >T. Débito</option>
													<option value="T_CREDITO" >T. Crédito</option>
													<option value="TRANSFERENCIA" >Transferencia</option>
													<option value="OTROS" >Otras Formas de pago</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label"><?=$frases[345][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <input type="file" name="comprobante" class="form-control" <?=$disabledPermiso;?>>
                                            </div>
										</div>

                                        <div id="divTipoTransaccion" style="display: none;">
                                            <div class="panel">
                                                <header class="panel-heading panel-heading-blue"> Tipo de Transacción</header>
                                                <div class="panel-body" style="text-align: center;">
                                                    <span style="font-size: 17px;">Ajustar este ingreso a una <b>factura de venta</b> existente en el sistema?</span><br>
                                                    Recuerda que puedes registrar un ingreso sin necesidad de que este asociado a una factura de venta<br>
                                                
                                                    <div class="form-group row" style="align-items: center; justify-content: center;">
                                                        <div class="col-sm-2">
                                                            <input type="radio" name="tipoTransaccion" id="opt1" value="<?=INVOICE?>" onClick="tipoAbono(1)"> SÍ
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="radio" name="tipoTransaccion" id="opt2" value="<?=ACCOUNT?>" onChange="tipoAbono(2)"> NO
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divFacturas" style="display: none;">
                                                <header class="panel-heading panel-heading-blue"> Facturas Pendientes</header>
                                                <div class="panel-body">

                                                    <div class="table-scrollable">
                                                        <table class="display" style="width:100%;" id="tablaItems">
                                                            <thead>
                                                                <tr>
                                                                    <th>Cod</th>
                                                                    <th><?=$frases[107][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th><?=$frases[417][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th><?=$frases[418][$datosUsuarioActual['uss_idioma']];?></th>
                                                                    <th>Valor recibido</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="mostrarFacturas"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel" id="divCuentasContables" style="display: none;">
                                                <header class="panel-heading panel-heading-blue"> A qué cuentas contables pertenece este ingreso?</header>
                                                <div class="panel-body">

                                                    <div class="table-scrollable">
                                                        <table class="display" style="width:100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Concepto</th>
                                                                    <th>Valor</th>
                                                                    <th>Cant.</th>
                                                                    <th>Descripción</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td id="idConcepto"></td>
                                                                    <td>
                                                                        <div style="padding: 0px;">
                                                                            <select class="form-control  select2" style="width: 100%;" id="concepto" onchange="guardarNuevoConcepto(this)" <?=$disabledPermiso;?>>
                                                                                <option value="">Seleccione una opción</option>
                                                                                <option value="OTROS_INGRESOS" >Otros Ingresos</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" id="precioNuevo" data-precio="0" onchange="actualizarSubtotalConceptos('idNuevo')" value="0" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" id="cantidadNuevo" data-cantidad="1" onchange="actualizarSubtotalConceptos('idNuevo')" value="1" style="width: 50px;" disabled>
                                                                    </td>
                                                                    <td>
                                                                        <textarea  id="descripNueva" cols="30" rows="1" onchange="guardarDescripcionConcepto('idNuevo')" disabled></textarea>
                                                                    </td>
                                                                    <td id="subtotalNuevo">$0</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label"><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></label>
                                            <div class="col-sm-4">
                                                <textarea cols="80" id="editor1" name="obser" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label"><?=$frases[416][$datosUsuarioActual['uss_idioma']];?>
                                                <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Estas notas no se verán reflejadas en el comprobante."><i class="fa fa-question"></i></button>
                                            </label>
                                            <div class="col-sm-4">
                                                <textarea cols="80" id="editor2" name="notas" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>></textarea>
                                            </div>
                                        </div>
                                        
                                        <?php require_once("../class/componentes/botones-guardar.php");
                            				$botones = new botonesGuardar("abonos.php",Modulos::validarPermisoEdicion()); ?>
                                    </form>
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3">
                            <div class="panel">
                                <header class="panel-heading panel-heading-blue">TOTAL</header>
                                <div class="panel-body">
                                    <table style="width: 100%;" align="center">
                                        <tr>
                                            <td style="padding-right: 20px;">TOTAL:</td>
                                            <td align="left" id="totalNeto">$0</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-right: 20px;">TOTAL. ABONOS:</td>
                                            <td align="left" id="abonosNeto">$0</td>
                                        </tr>
                                        <tr style="font-size: 15px; font-weight:bold;">
                                            <td style="padding-right: 20px;">TOTAL POR COBRAR:</td>
                                            <td align="left" id="porCobrarNeto">$0</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <!-- end page container -->
        <?php require_once(ROOT_PATH."/main-app/compartido/footer.php");?>
    </div>
    <script src="../js/Movimientos.js" ></script>
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
        CKEDITOR.replace( 'editor2' );
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>