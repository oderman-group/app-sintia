<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0096';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php
$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=0),
(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1),
(SELECT count(*) FROM academico_indicadores_carga 
WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1)");
$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
?>
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
                                <div class="page-title"><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
							
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<?php if($periodoConsultaActual!=$datosCargaActual['car_periodo'] and $datosCargaActual['car_permiso2']!=1){?>
										<p style="color: tomato;"> Podrás consultar la información de otros periodos diferentes al actual, pero no se podrán hacer modificaciones. </p>
									<?php }?>
									
									<?php include("info-carga-actual.php");?>
									
									<?php include("filtros-cargas.php");?>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[63][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body">
												<div class="row">
					                                        <div class="col-md-6 col-sm-6 col-6">
					                                            <?php 
																if(
																	($datosCargaActual['car_valor_indicador']==0 and $sumaIndicadores[2]<$datosCargaActual['car_maximos_indicadores'] 
																	 and $periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
																	
																	or($datosCargaActual['car_valor_indicador']==1 and $sumaIndicadores[2]<$datosCargaActual['car_maximos_indicadores'] and $periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
																)
																{
																?>
																<div class="btn-group">
					                                                <a href="indicadores-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor">
					                                                    Agregar nuevo <i class="fa fa-plus"></i>
					                                                </a>
					                                            </div>
																<?php }?>
																
																<?php if($datosCargaActual['car_valor_indicador']==1 and $porcentajeRestante<=0){?>
																	<p style="color: tomato;"> Has alcanzado el 100% de valor para los indicadores. </p>
																<?php }?>
																
																<?php if($datosCargaActual['car_maximos_indicadores']<=$sumaIndicadores[2]){?>
																	<p style="color: tomato;"> Has alcanzado el número máximo de indicadores permitidos. </p>
																<?php }?>
					                                        </div>
					                                        <div class="col-md-6 col-sm-6 col-6">
					                                            <div class="btn-group pull-right">
					                                                <a class="btn btn-primary  btn-outline dropdown-toggle" data-toggle="dropdown">Herramientas
					                                                    <i class="fa fa-angle-down"></i>
					                                                </a>
					                                                <ul class="dropdown-menu pull-right">
					                                                    <li><a href="javascript:;"><i class="fa fa-print"></i> Imprimir </a></li>
					                                                </ul>
					                                            </div>
					                                        </div>
					                                    </div>	
                                        	<div class="table-scrollable">
					                           <table class="table table-striped table-bordered table-hover table-checkable order-column valign-middle" id="example4">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[50][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[52][$datosUsuarioActual[8]];?></th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
													 INNER JOIN academico_indicadores ON ind_id=ipc_indicador
													 WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");
													 $contReg = 1; 
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														 $porcentajeActual +=$resultado['ipc_valor'];
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['ipc_id'];?></td>
														<td><?=$resultado['ind_nombre'];?></td>
														<td><?=$resultado['ipc_valor'];?>%</td>
														<td>
															<?php if($resultado['ipc_creado']==1 and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)){?>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary">Acciones</button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="indicadores-editar.php?idR=<?=$resultado['ipc_id'];?>">Editar</a></li>
																	  <li><a href="#" name="guardar.php?get=10&idR=<?=$resultado['ipc_id'];?>&idIndicador=<?=$resultado['ipc_indicador'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" onClick="deseaEliminar(this)">Eliminar</a></li>
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
												<tfoot>
													<tr style="font-weight:bold;">
														<td colspan="3"><?=strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]);?></td>
														<td><?=$porcentajeActual;?>%</td>
														<td></td>
													 </tr>
												</tfoot>   
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