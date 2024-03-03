<?php include("session.php");?>
<?php $idPaginaInterna = 'DC0037';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Foros.php");?>
	<!-- data tables -->
    <link href="../../config-general/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
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
                                <div class="page-title"><?=$frases[113][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
					<?php include(ROOT_PATH."/config-general/mensajes-informativos.php"); ?>
                    <?php include("includes/barra-superior-informacion-actual.php"); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
									
								<div class="col-md-12">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[113][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
											
											<div class="row" style="margin-bottom: 10px;">
												<div class="col-sm-12">
													
											<?php
											if( CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) )
											{
											?>
											
													<div class="btn-group">
														<a href="foros-agregar.php?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
													
													
											<?php
											}
											?>
													
											
												</div>
											</div>
											
                                        <div class="table-scrollable">
                                    		<table id="example1" class="display" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[127][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[54][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$consulta = Foros::traerForos($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
													$contReg = 1; 
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
													 ?>
													<tr id="reg<?=$resultado['foro_id'];?>">
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['foro_id'];?></td>
														<td><a href="foros-detalles.php?idR=<?=base64_encode($resultado['foro_id']);?>"><?=$resultado['foro_nombre'];?></a></td>
														<td>
															<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
															
															<?php
																$arrayEnviar = array("tipo"=>1, "descripcionTipo"=>"Para ocultar fila del registro.");
																$arrayDatos = json_encode($arrayEnviar);
														 		$objetoEnviar = htmlentities($arrayDatos);
																?>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary">Acciones</button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="foros-detalles.php?idR=<?=base64_encode($resultado['foro_id']);?>">Participación</a></li>
																	  <li><a href="foros-editar.php?idR=<?=base64_encode($resultado['foro_id']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>">Editar</a></li>
																	  
																	  <li><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado['foro_id'];?>" name="foros-eliminar.php?idR=<?=base64_encode($resultado['foro_id']);?>&carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($periodoConsultaActual);?>" onClick="deseaEliminar(this)">Eliminar</a></li>
																  </ul>
															  </div>
															<?php } ?>
														</td>
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