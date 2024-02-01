<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0128';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("../compartido/head.php");?>
<?php
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
try{
    $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_id='".base64_decode($_GET['id'])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}
$resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

$abonos = Movimientos::calcularTotalAbonado($conexion, $config, $resultado['fcu_id']);

// $abonos = number_format($abonos, 0, ",", ".");

$disabledPermiso = "";
if(!Modulos::validarPermisoEdicion() || $resultado['fcu_anulado']==1 || $resultado['fcu_status']==COBRADA || $abonos>0){
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
                                <div class="page-title">Editar Movimientos</div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							<ol class="breadcrumb page-breadcrumb pull-right">
                                <li><a class="parent-item" href="javascript:void(0);" name="movimientos.php" onClick="deseaRegresar(this)"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>
                                <li class="active">Editar Movimientos</li>
                            </ol>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12">
                                <?php 
                                    include("../../config-general/mensajes-informativos.php");
                                    include("includes/barra-superior-movimientos-financieros-editar.php");
                                ?>
								<div class="panel">
									<header class="panel-heading panel-heading-purple"><?=$frases[95][$datosUsuarioActual['uss_idioma']];?> </header>
                                	<div class="panel-body">

                                   
									<form name="formularioGuardar" action="movimientos-actualizar.php" method="post">
										<input type="hidden" value="<?=$resultado['fcu_id'];?>" name="idU" id="idTransaction">
										<input type="hidden" value="<?=TIPO_FACTURA;?>" name="typeTransaction" id="typeTransaction">
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Usuario</label>
                                            <div class="col-sm-4">
                                                <?php
                                                try{
                                                    $datosConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_GENERAL.".usuarios uss
                                                    INNER JOIN ".$baseDatosServicios.".general_perfiles ON pes_id=uss_tipo
                                                    WHERE uss_id='".$resultado['fcu_usuario']."' AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}");
                                                } catch (Exception $e) {
                                                    include("../compartido/error-catch-to-report.php");
                                                }
                                                ?>
                                                <select class="form-control  select2" id="select_usuario" name="usuario" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
                                                    <?php
                                                    while($resultadosDatos = mysqli_fetch_array($datosConsulta, MYSQLI_BOTH)){
                                                    ?>
                                                        <option value="<?=$resultadosDatos['uss_id'];?>" <?php if($resultado['fcu_usuario']==$resultadosDatos['uss_id']){ echo "selected";}?>><?=UsuariosPadre::nombreCompletoDelUsuario($resultadosDatos)." (".$resultadosDatos['pes_nombre'].")";?></option>
                                                    <?php }?>
                                                </select>
                                            </div>

                                            <label class="col-sm-2 control-label">Fecha</label>
                                            <div class="col-sm-4">
                                                <input type="date" name="fecha" class="form-control" autocomplete="off" required value="<?=$resultado['fcu_fecha'];?>" <?=$disabledPermiso;?>>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                        <label class="col-sm-2 control-label">Descripción general</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="detalle" class="form-control" autocomplete="off" value="<?=$resultado['fcu_detalle'];?>" required <?=$disabledPermiso;?>>
                                            </div>

                                            <label class="col-sm-2 control-label">Valor adicional</label>
                                            <div class="col-sm-4">
                                                <input type="number" min="0" id="vlrAdicional" name="valor" class="form-control" autocomplete="off" value="<?=$resultado['fcu_valor'];?>" required <?=$disabledPermiso;?> data-vlr-adicional-anterior="<?=$resultado['fcu_valor'];?>" onchange="cambiarAdiconal(this)">
                                            </div>
										</div>

                                        <div class="form-group row">
                                        <label class="col-sm-2 control-label">Tipo de movimiento</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="tipo" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($resultado['fcu_tipo']==1){ echo "selected";}?>>Ingreso</option>
													<option value="2" <?php if($resultado['fcu_tipo']==2){ echo "selected";}?>>Egreso</option>
													<option value="3" <?php if($resultado['fcu_tipo']==3){ echo "selected";}?>>Cobro (CPC)</option>
													<option value="4" <?php if($resultado['fcu_tipo']==4){ echo "selected";}?>>Deuda (CPP)</option>
                                                </select>
                                            </div>

                                            <label class="col-sm-2 control-label">Forma de pago</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="forma" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="1" <?php if($resultado['fcu_forma_pago']==1){ echo "selected";}?>>Efectivo</option>
													<option value="2" <?php if($resultado['fcu_forma_pago']==2){ echo "selected";}?>>Cheque</option>
													<option value="3" <?php if($resultado['fcu_forma_pago']==3){ echo "selected";}?>>T. Débito</option>
													<option value="4" <?php if($resultado['fcu_forma_pago']==4){ echo "selected";}?>>T. Crédito</option>
													<option value="5" <?php if($resultado['fcu_forma_pago']==5){ echo "selected";}?>>Transferencia</option>
													<option value="6" <?php if($resultado['fcu_forma_pago']==6){ echo "selected";}?>>No aplica</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            
                                            <label class="col-sm-2 control-label">Cerrado?</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="cerrado" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="0" <?php if($resultado['fcu_cerrado']==0){ echo "selected";}?>>Abierto</option>
													<option value="1" <?php if($resultado['fcu_cerrado']==1){ echo "selected";}?>>Cerrado</option>
                                                </select>
                                            </div>
                                            
                                            <label class="col-sm-2 control-label">Anulado</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="anulado" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="0" <?php if($resultado['fcu_anulado']==0){ echo "selected";}?>>No</option>
													<option value="1" <?php if($resultado['fcu_anulado']==1){ echo "selected";}?>>Si</option>
                                                </select>
                                            </div>
                                        </div>
										
										<div class="form-group row">
                                            <label class="col-sm-2 control-label">Estado</label>
                                            <div class="col-sm-4">
                                                <select class="form-control  select2" name="estado" required <?=$disabledPermiso;?>>
                                                    <option value="">Seleccione una opción</option>
													<option value="<?=POR_COBRAR?>" <?php if($resultado['fcu_status']==POR_COBRAR){ echo "selected";}?>>Por Cobrar</option>
													<option value="<?=COBRADA?>" <?php if($resultado['fcu_status']==COBRADA){ echo "selected";}?>>Cobrada</option>
                                                </select>
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
                                                    <table class="display" style="width:100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Item</th>
                                                                <th>Precio</th>
                                                                <th>Descripción</th>
                                                                <th>Cant.</th>
                                                                <th>Total</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="mostrarItems">
                                                            <?php
                                                                $idTransaction = base64_decode($_GET['id']);
                                                                
                                                                $itemsConsulta = Movimientos::listarItemsTransaction($conexion, $config, $idTransaction);

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
                                                                        <textarea  id="descrip<?=$fila['idtx'];?>" cols="30" rows="1" onchange="guardarDescripcion('<?=$fila['idtx'];?>')" <?=$disabledPermiso;?>><?=$fila['description']?></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" title="cantity" min="0" id="cantidadItems<?=$fila['idtx'];?>" data-cantidad="<?=$fila['cantity'];?>" onchange="actualizarSubtotal('<?=$fila['idtx'];?>')" value="<?=$fila['cantity'];?>" style="width: 50px;" <?=$disabledPermiso;?>>
                                                                    </td>
                                                                    <td id="subtotal<?=$fila['idtx'];?>" data-subtotal-anterior="<?=$fila['subtotal'];?>">$<?=number_format($fila['subtotal'], 0, ",", ".")?></td>
                                                                    <td>
                                                                        <?php if(Modulos::validarPermisoEdicion() && $resultado['fcu_anulado']==0 && $resultado['fcu_status']==POR_COBRAR && $abonos==0){?>
                                                                            <a href="#" title="<?=$objetoEnviar;?>" id="<?=$fila['idtx'];?>" name="movimientos-items-eliminar.php?idR=<?=$fila['idtx'];?>" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" onClick="deseaEliminarNuevoItem(this)">X</a>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            <?php 
                                                                    $subtotal += $fila['subtotal'];
                                                                    }
                                                                }
                                                                if(empty($resultado['fcu_valor'])){ $resultado['fcu_valor']=0; }
                                                                $total= ($subtotal+$resultado['fcu_valor'])-$abonos;
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
                                                                    <textarea  id="descripNueva" cols="30" rows="1" onchange="guardarDescripcion('idNuevo')" disabled></textarea>
                                                                </td>
                                                                <td><input type="number" min="0" id="cantidadItemNuevo" data-cantidad="1" onchange="actualizarSubtotal('idNuevo')" value="1" style="width: 50px;" disabled></td>
                                                                <td id="subtotalNuevo" data-subtotal-anterior="0">$0</td>
                                                                <td id="eliminarNuevo"></td>
                                                            </tr>
                                                            <?php if(Modulos::validarPermisoEdicion() && $resultado['fcu_anulado']==0 && $resultado['fcu_status']==POR_COBRAR && $abonos==0){?>
                                                                <tr>
                                                                    <td colspan="6">
                                                                        <button type="button" title="Agregar nueva línea para item" style="padding: 4px 4px; margin: 5px;" class="btn btn-sm" data-toggle="tooltip" onclick="nuevoItem()" data-placement="right" ><i class="fa fa-plus"></i> Agregar línea</button>
                                                                    </td>
                                                                </tr>
                                                            <?php }?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td align="right" colspan="5" style="padding-right: 20px;">SUBTOTAL:</td>
                                                                <td align="left" id="subtotal" data-subtotal="<?=$subtotal;?>" data-subtotal-anterior-sub="<?=$subtotal;?>"><?="$".number_format($subtotal, 0, ",", ".");?></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right" colspan="5" style="padding-right: 20px;">VLR. ADICIONAL:</td>
                                                                <td align="left" id="valorAdicional" data-valor-adicional="<?=$resultado['fcu_valor'];?>"><?="$".number_format($resultado['fcu_valor'], 0, ",", ".");?></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right" colspan="5" style="padding-right: 20px;">ABONADO:</td>
                                                                <td align="left"><?="$".number_format($abonos, 0, ",", ".");?></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr style="font-size: 15px; font-weight:bold;">
                                                                <td align="right" colspan="5" style="padding-right: 20px;">TOTAL NETO:</td>
                                                                <td align="left" id="totalNeto" data-total-neto="<?=$total;?>" data-total-neto-anterior="<?=$total;?>"><?="$".number_format($total, 0, ",", ".");?></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group row">
                                            <label class="col-sm-12 control-label">Observaciones</label>
                                            <div class="col-sm-12">
                                                <textarea cols="80" id="editor1" name="obs" class="form-control" rows="8" placeholder="Escribe tu mensaje" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" <?=$disabledPermiso;?>><?=$resultado['fcu_observaciones'];?></textarea>
                                            </div>
                                        </div>
										
                                        <div class="text-right">
                                            <a href="javascript:void(0);" name="movimientos.php" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
                                            <?php if(Modulos::validarPermisoEdicion() && $resultado['fcu_anulado']==0 && $resultado['fcu_status']==POR_COBRAR && $abonos==0){?>
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