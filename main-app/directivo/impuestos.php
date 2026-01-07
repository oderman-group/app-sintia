<?php
include("session.php");
$idPaginaInterna = 'DT0294';
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
                                <div class="page-title"><?=$frases[425][$datosUsuarioActual['uss_idioma']];?></div>
								<?php require_once(ROOT_PATH."/main-app/compartido/texto-manual-ayuda.php");?>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li class="active"><?=$frases[425][$datosUsuarioActual['uss_idioma']];?></li>
                            </ol>
                        </div>
                    </div>
					
					<!-- Descripción de la página -->
					<div class="row mb-3">
						<div class="col-md-12">
							<p class="text-muted" style="font-size: 14px; line-height: 1.6;">
								<i class="fa fa-info-circle text-info"></i> 
								<?=__('financiero.impuestos_descripcion');?>
							</p>
						</div>
					</div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
								
								<div class="col-md-8 col-lg-12">
                                <?php require_once(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[425][$datosUsuarioActual['uss_idioma']];?></header>
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
                                                        <?php if (Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0295'])) { ?>
                                                            <a href="impuestos-agregar.php" class="btn deepPink-bgcolor"> Agregar nuevo <i class="fa fa-plus"></i></a>
                                                        <?php } ?>
													</div>
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[294][$datosUsuarioActual['uss_idioma']];?> <?=$frases[425][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[187][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[426][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0297','DT0299'])){?>
                                                            <th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                        <?php }?>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
                                                        $consulta= Movimientos::listarImpuestos($conexion, $config);
                                                        $contReg = 1;
                                                        while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){

                                                            $arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
                                                            $arrayDatos = json_encode($arrayEnviar);
                                                            $objetoEnviar = htmlentities($arrayDatos);
                                                            
                                                            // Validar si el impuesto está en uso
                                                            $impuestoEnUso = Movimientos::validarImpuestoEnUso($conexion, $config, $resultado['id']);
                                                    ?>
													<tr id="reg<?=$resultado['id'];?>">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['type_tax'];?></td>
														<td><?=$resultado['name'];?></td>
														<td><?=$resultado['fee']?>%</td>
														<td><?=$resultado['description'];?></td>
														
                                                        <?php if(Modulos::validarPermisoEdicion() && Modulos::validarSubRol(['DT0297','DT0299'])){?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary"><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></button>
                                                                    <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
                                                                        <i class="fa fa-angle-down"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
																		<?php if(Modulos::validarSubRol(['DT0297'])){?>
                                                                            <li><a href="impuestos-editar.php?id=<?=base64_encode($resultado['id']);?>"><?=$frases[165][$datosUsuarioActual['uss_idioma']];?></a></li>
                                                                        <?php } if(Modulos::validarSubRol(['DT0299']) && !$impuestoEnUso){?>
                                                                            <li><a href="javascript:void(0);" title="<?=$objetoEnviar;?>" id="<?=$resultado['id'];?>" name="impuestos-eliminar.php?id=<?=base64_encode($resultado['id']);?>" onClick="deseaEliminar(this)"><?=$frases[174][$datosUsuarioActual['uss_idioma']];?></a></li>
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