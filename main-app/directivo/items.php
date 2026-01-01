<?php
include("session.php");
$idPaginaInterna = 'DT0258';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title">Items</div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active">Items</li>
                            </ol>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-8 col-lg-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header>Items</header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													<div class="btn-group">
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0259'])) { ?>
                                                            <a href="items-agregar.php" class="btn deepPink-bgcolor"> Agregar nuevo <i class="fa fa-plus"></i></a>
                                                        <?php } ?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[187][$datosUsuarioActual['uss_idioma']];?></th>
														<th>Tipo</th>
														<th><?=$frases[381][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0261','DT0263'])){?>
                                                            <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $consulta= Movimientos::listarItems($conexion, $config);
                                                        $contReg = 1;
                                                        while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
                                                            $price = 0;
                                                            if (!empty($resultado['price'])) {
                                                                $price = $resultado['price'];
                                                            }

                                                            // Usar item_id en lugar de id (según el esquema de BD actualizado)
                                                            $itemId = $resultado['item_id'] ?? $resultado['id'] ?? null;
                                                            $existeTransaction = 0;
                                                            if (!empty($itemId)) {
                                                                $existeTransaction = Movimientos::validarExistenciaItemsEnTransaction($conexion, $config, (string)$itemId);
                                                            }

                                                            $arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
                                                            $arrayDatos = json_encode($arrayEnviar);
                                                            $objetoEnviar = htmlentities($arrayDatos);
                                                    ?>
													<tr id="reg<?=$resultado['item_id'];?>">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['item_id'];?></td>
														<td><?=$resultado['name'];?></td>
														<td>
															<?php 
																$tipoItem = $resultado['item_type'] ?? 'D';
																if ($tipoItem == 'C') {
																	echo '<span class="label label-success">Crédito</span>';
																	// Mostrar application_time para créditos
																	$applicationTime = $resultado['application_time'] ?? 'ANTE_IMPUESTO';
																	$textoApplicationTime = ($applicationTime == 'POST_IMPUESTO') ? 'Después del Impuesto' : 'Antes del Impuesto';
																	echo '<br><small style="color: #666; font-size: 0.85em;">(' . $textoApplicationTime . ')</small>';
																} else {
																	echo '<span class="label label-info">Débito</span>';
																}
															?>
														</td>
														<td>$<?=number_format($price,0,",",".")?></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0261','DT0263'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0261'])){?>
                                                                            <li><a href="items-editar.php?id=<?=base64_encode((string)$resultado['item_id']);?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <?php } if(Modulos::validarSubRol(['DT0263']) && $existeTransaction==0){?>
                                                                            <li><a href="javascript:void(0);" title="<?=$objetoEnviar;?>" id="<?=$resultado['item_id'];?>" name="items-eliminar.php?id=<?=base64_encode((string)$resultado['item_id']);?>" onClick="deseaEliminar(this)"><?=$frases[174][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <?php }?>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        <?php }?>
                                                    </tr>
													<?php 
														 $contReg++;
													  }
													  ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="col-md-4 col-lg-3">
									<?php require_once(ROOT_PATH."/main-app/compartido/publicidad-lateral.php");?>
								</div>
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
	<!-- data tables -->
    <script src="../../config-general/assets/plugins/datatables/jquery.dataTables.min.js" ></script>
 	<script src="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js" ></script>
    <script src="../../config-general/assets/js/pages/table/table_data.js" ></script>
    <!-- Common js-->
	<script src="../../config-general/assets/js/app.js" ></script>
    <script src="../../config-general/assets/js/layout.js" ></script>
	<script src="../../config-general/assets/js/theme-color.js" ></script>
	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js" ></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
    <!-- end js include path -->
</body>

</html>