<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0278';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id = '';
if (!empty($_GET['id'])) {
    $id = base64_decode($_GET['id']);;
}

$resultado = Movimientos::traerDatosRecurrentes($conexion, $config, $id);

$datosResponsable = UsuariosPadre::sesionUsuario($resultado['responsible_user']);

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion()){
	$disabledPermiso = "disabled";
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
                                <div class="page-title">Editar <?=$frases[415][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="factura-recurrente.php" onClick="deseaRegresar(this)"><?=$frases[415][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar <?=$frases[415][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12">
                                <?php 
                                    include("../../config-general/mensajes-informativos.php");
                                ?>
								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[415][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="factura-recurrente-actualizar.php" method="post">
										<input type="hidden" value="<?=$resultado['id'];?>" name="id" id="idTransaction">
										<input type="hidden" value="<?=TIPO_RECURRING;?>" name="typeTransaction" id="typeTransaction">

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Usuario <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" id="select_usuario" name="usuario" required <?=$disabledPermiso;?>>
                                                    <?php
                                                        try{
                                                            $datosConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".usuarios uss
                                                            INNER JOIN ".BD_ADMIN.".general_perfiles ON pes_id=uss_tipo
                                                            WHERE uss_id='".$resultado['user']."' AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}");
                                                        } catch (Exception $e) {
                                                            include("../compartido/error-catch-to-report.php");
                                                        }
                                                        while($resultadosDatos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
                                                    ?>
                                                            <option value="<?=$resultadosDatos['uss_id'];?>" <?php if($resultado['user']==$resultadosDatos['uss_id']){ echo "selected";}?>><?=UsuariosPadre::nombreCompletoDelUsuario($resultadosDatos)." (".$resultadosDatos['pes_nombre'].")";?></option>
                                                    <?php }?>
                                                </select>
                                            </div>

                                            
                                            <label class="col-sm-2 control-label">Responsable</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" autocomplete="off" value="<?=UsuariosPadre::nombreCompletoDelUsuario($datosResponsable)?>" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Fecha de inicio <span style="color: red;">(*)</span> <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Fecha en la que se crea la primer factura."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-4">
                                                <input type="date" name="fechaInicio" class="form-control" autocomplete="off" required value="<?=$resultado['date_start'];?>" disabled>
                                            </div>

                                            <label class="col-sm-2 control-label">Fecha de finalización <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica el último día de la creación automática de la factura."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-4">
                                                <input type="date" name="fechaFinal" class="form-control" autocomplete="off" value="<?=$resultado['date_finish'];?>" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Frecuencia <span style="color: red;">(*)</span> <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica cada cuántos meses se generará la factura, por ejemplo si eliges 2 se creará cada 2 meses."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-4">
                                                <input type="number" min="1" name="frecuencia" class="form-control" autocomplete="off" value="1" required disabled>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label">Días de facturación <span style="color: red;">(*)</span> <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Indica que dias del mes deseas que se genere la factura."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-4">
                                                <select class="form-control select2-multiple" multiple name="dias[]" required <?=$disabledPermiso;?>>
                                                    <option value="" >Seleccione una opción</option>
                                                    <?php
                                                        $dias = explode(',',$resultado['days_in_month']);
                                                        $i = 1;
                                                        while ($i <= 31){
                                                            $selected = ($dias!="" && in_array($i, $dias)) ? "selected" : "";
                                                    ?>
                                                        <option value="<?=$i?>"  <?=$selected;?>><?=$i?></option>
                                                    <?php
                                                            $i++;
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Tipo de movimiento <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="tipo" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <option value="1" <?php if($resultado['invoice_type']==1){ echo "selected";}?>>Ingreso</option>
                                                    <option value="2" <?php if($resultado['invoice_type']==2){ echo "selected";}?>>Egreso</option>
                                                    <option value="3" <?php if($resultado['invoice_type']==3){ echo "selected";}?>>Cobro (CPC)</option>
                                                    <option value="4" <?php if($resultado['invoice_type']==4){ echo "selected";}?>>Deuda (CPP)</option>
                                                </select>
                                            </div>

                                            <label class="col-sm-2 control-label"><?=$frases[414][$datosUsuarioActual['uss_idioma']];?> <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <select class="form-control select2" id="metodoPago" name="metodoPago" required <?=$disabledPermiso;?>>
                                                    <option value="" >Seleccione una opción</option>
                                                    <option value="EFECTIVO" <?php if($resultado['payment_method'] == "EFECTIVO"){ echo "selected";}?>>Efectivo</option>
                                                    <option value="CHEQUE" <?php if($resultado['payment_method'] == "CHEQUE"){ echo "selected";}?>>Cheque</option>
                                                    <option value="T_DEBITO" <?php if($resultado['payment_method'] == "T_DEBITO"){ echo "selected";}?>>T. Débito</option>
                                                    <option value="T_CREDITO" <?php if($resultado['payment_method'] == "T_CREDITO"){ echo "selected";}?>>T. Crédito</option>
                                                    <option value="TRANSFERENCIA" <?php if($resultado['payment_method'] == "TRANSFERENCIA"){ echo "selected";}?>>Transferencia</option>
                                                    <option value="OTROS" <?php if($resultado['payment_method'] == "OTROS"){ echo "selected";}?>>Otras Formas de pago</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label">Descripción general <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <textarea name="detalle" cols="70" rows="2" required <?=$disabledPermiso;?>><?=$resultado['detail'];?></textarea>
                                            </div>

                                            <label class="col-sm-2 control-label">Valor adicional <span style="color: red;">(*)</span></label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" id="vlrAdicional" name="valor" class="form-control" autocomplete="off" value="<?=$resultado['additional_value'];?>" required <?=$disabledPermiso;?> data-vlr-adicional-anterior="0" onchange="totalizar(this)">
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                $('#select_usuario').select2({
                                                placeholder: 'Seleccione el usuario...',
                                                theme: "bootstrap",
                                                multiple: false,
                                                    ajax: {
                                                        type: 'GET',
                                                        url: '../compartido/ajax-listar-usuarios.php',
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

                                        <div class="panel">
                                            <header class="panel-heading panel-heading-blue"> Items</header>
                                            <div class="panel-body">

                                                <div class="table-scrollable">
                                                    <table class="display" style="width:100%;" id="tablaItems">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Item</th>
                                                                <th>Precio</th>
                                                                <th>Desc %</th>
                                                                <th>Impuesto</th>
                                                                <th>Descripción</th>
                                                                <th>Cant.</th>
                                                                <th>Total</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="mostrarItems">
                                                            <?php
                                                                $idTransaction = $id;
                                                                
                                                                $itemsConsulta = Movimientos::listarItemsTransaction($conexion, $config, $idTransaction, TIPO_RECURRING);

                                                                $subtotal=0;
                                                                $numItems=mysqli_num_rows($itemsConsulta);
                                                                if($numItems>0){
                                                                // Manejar el resultado según tus necesidades
                                                                    while ($fila = mysqli_fetch_array($itemsConsulta, MYSQLI_BOTH)) {
                                                                        $arrayEnviar = array("tipo"=>1, "restar"=>$fila['subtotal'], "descripcionTipo"=>"Para ocultar fila del registro.");
                                                                        $arrayDatos = json_encode($arrayEnviar);
                                                                        $objetoEnviar = htmlentities($arrayDatos);
                                                            ?>
                                                                <tr id="reg<?=$fila['idtx'];?>">
                                                                    <td><?=$fila['idtx'];?></td>
                                                                    <td><?=$fila['name'];?></td>
                                                                    <td>
                                                                        <input type="number" min="0" id="precio<?=$fila['idtx'];?>" data-precio="<?=$fila['priceTransaction'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['priceTransaction']?>" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" id="descuento<?=$fila['idtx'];?>" data-descuento-anterior="<?=$descuentoAnterior?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['discount']?>">
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-sm-12" style="padding: 0px;">
                                                                            <select class="form-control  select2" id="impuesto<?=$fila['idtx'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')">
                                                                                <option value="0" name="0">Ninguno - (0%)</option>
                                                                                <?php
                                                                                    $consulta= Movimientos::listarImpuestos($conexion, $config);
                                                                                    while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                                        $selected = $fila['tax'] == $datosConsulta['id'] ? "selected" : "";
                                                                                ?>
                                                                                <option value="<?=$datosConsulta['id']?>" data-name-impuesto="<?=$datosConsulta['type_tax']?>" data-valor-impuesto="<?=$datosConsulta['fee']?>" <?=$selected?>><?=$datosConsulta['type_tax']." - (".$datosConsulta['fee']."%)"?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <textarea  id="descrip<?=$fila['idtx'];?>" cols="30" rows="1" onchange="guardarDescripcion('<?=$fila['idtx'];?>')"><?=$fila['description']?></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" title="cantity" min="0" id="cantidadItems<?=$fila['idtx'];?>" data-cantidad="<?=$fila['cantity'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['cantity'];?>" style="width: 50px;" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td id="subtotal<?=$fila['idtx'];?>" data-subtotal-anterior="<?=$fila['subtotal'];?>">$<?=number_format($fila['subtotal'], 0, ",", ".")?></td>
                                                                    <td>
                                                                        <a href="#" title="<?=$objetoEnviar;?>" id="<?=$fila['idtx'];?>" name="movimientos-items-eliminar.php?idR=<?=$fila['idtx'];?>" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" onClick="deseaEliminarNuevoItem(this)">X</a>
                                                                    </td>
                                                                </tr>
                                                            <?php 
                                                                    }
                                                                }
                                                            ?>
                                                        </tbody>
                                                        <tbody>
                                                            <tr>
                                                                <td id="idItemNuevo"></td>
                                                                <td>
                                                                    <div class="col-sm-5" style="padding: 0px;">
                                                                        <select class="form-control  select2" id="items" onchange="guardarNuevoItem(this)" <?=$disabledPermiso;?>>
                                                                            <option value="">Seleccione una opción</option>
                                                                            <?php
                                                                                $consulta= Movimientos::listarItems($conexion, $config);
                                                                                while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                            ?>
                                                                            <option value="<?=$datosConsulta['id']?>" name="<?=$datosConsulta['price']?>"><?=$datosConsulta['name']?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="0" id="precioNuevo" data-precio="0" onchange="actualizarSubtotal('idNuevo')" value="0" disabled>
                                                                </td>
                                                                <td>
                                                                    <input type="text" id="descuentoNuevo" data-total-precio="0" data-precio-item-anterior="0" data-descuento-anterior="0" onchange="actualizarSubtotal('idNuevo')" value="0" disabled>
                                                                </td>
                                                                <td>
                                                                    <div class="col-sm-12" style="padding: 0px;">
                                                                        <select class="form-control  select2" id="impuestoNuevo" onchange="actualizarSubtotal('idNuevo')" <?=$disabledPermiso;?> disabled>
                                                                            <option value="0" name="0">Ninguno - (0%)</option>
                                                                            <?php
                                                                                $consulta= Movimientos::listarImpuestos($conexion, $config);
                                                                                while($datosConsulta = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                                            ?>
                                                                            <option value="<?=$datosConsulta['id']?>" data-name-impuesto="<?=$datosConsulta['type_tax']?>" data-valor-impuesto="<?=$datosConsulta['fee']?>"><?=$datosConsulta['type_tax']." - (".$datosConsulta['fee']."%)"?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <textarea  id="descripNueva" cols="30" rows="1" onchange="guardarDescripcion('idNuevo')" disabled></textarea>
                                                                </td>
                                                                <td><input type="number" min="0" id="cantidadItemNuevo" data-cantidad="1" onchange="actualizarSubtotal('idNuevo')" value="1" style="width: 50px;" disabled></td>
                                                                <td id="subtotalNuevo" data-subtotal-anterior="0">$0</td>
                                                                <td id="eliminarNuevo"></td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot id="tfootTotalizar">
                                                            <?php if(Modulos::validarPermisoEdicion()){?>
                                                                <tr>
                                                                    <td colspan="9">
                                                                        <button type="button" title="Agregar nueva línea para item" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" data-toggle="tooltip" onclick="nuevoItem()" data-placement="right" ><i class="fa fa-plus"></i> Agregar línea</button>
                                                                    </td>
                                                                </tr>
                                                            <?php }?>
                                                            <tr>
                                                                <td align="right" colspan="7" style="padding-right: 20px;">SUBTOTAL:</td>
                                                                <td align="left" colspan="2"id="subtotal" data-subtotal="0" data-subtotal-anterior-sub="0">$0</td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right" colspan="7" style="padding-right: 20px;">VLR. ADICIONAL:</td>
                                                                <td align="left" colspan="2"id="valorAdicional" data-valor-adicional="0">$0</td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right" colspan="7" style="padding-right: 20px;">DESCUENTO:</td>
                                                                <td align="left" colspan="2"id="valorDescuento" data-valor-descuento="0">$0</td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right" colspan="7" style="padding-right: 20px;">IMPUESTO:</td>
                                                                <td align="left" colspan="2"id="valorImpuesto">$0</td>
                                                            </tr>
                                                            <tr style="font-size: 15px; font-weight:bold;">
                                                                <td align="right" colspan="7" style="padding-right: 20px;">TOTAL NETO:</td>
                                                                <td align="left" colspan="2"id="totalNeto" data-total-neto="0" data-total-neto-anterior="0">$0</td>
                                                            </tr>
                                                        </tfoot>
                                                        <script>
                                                            $(document).ready(totalizar);
                                                        </script>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row">
                                            <label class="col-sm-12 control-label">Observaciones <button type="button" class="btn btn-sm" data-toggle="tooltip" data-placement="right" title="Observaciones adicionales que quieres que vea tu cliente en la factura."><i class="fa fa-question"></i></button></label>
                                            <div class="col-sm-12">
                                                <textarea cols="80" id="editor1" name="obs" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>><?=$resultado['observation'];?></textarea>
                                            </div>
                                        </div>
										
                                        <div class="text-right">
                                            <a href="javascript:void(0);" name="factura-recurrente.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                            <?php if(Modulos::validarPermisoEdicion()){?>
                                                <button type="submit" class="btn  btn-info">
                                                    <i class="fa fa-save" aria-hidden="true"></i> Guardar cambios 
                                                </button>
                                            <?php }?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
						
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
    </script>
</body>

<!-- Mirrored from radixtouch.in/templates/admin/smart/source/light/advance_form.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 May 2018 17:32:54 GMT -->
</html>