<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0025';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
<?php

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
$consultaValores=mysqli_query($conexion, "SELECT
(SELECT sum(act_valor) FROM academico_actividades 
WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1),
(SELECT count(*) FROM academico_actividades 
WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1)");
$valores = mysqli_fetch_array($consultaValores, MYSQLI_BOTH);
$porcentajeRestante = 100 - $valores[0];
?>
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
                                <div class="page-title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></div>
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
                                            <header><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></header>
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
											if(
												($datosCargaActual['car_configuracion']==0 and $valores[1]<$datosCargaActual['car_maximas_calificaciones'] 
												 and $periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
																	
												or($datosCargaActual['car_configuracion']==1 and $valores[1]<$datosCargaActual['car_maximas_calificaciones'] and $periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
												)
											{
											?>
											
													<div class="btn-group">
														<a href="calificaciones-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
													
													
											<?php
											}
											?>
													
											<?php if($datosCargaActual['car_configuracion']==1 and $porcentajeRestante<=0){?>
												<p style="color: tomato;"> Has alcanzado el 100% de valor para las calificaciones. </p>
											<?php }?>
														
											<?php if($datosCargaActual['car_maximas_calificaciones']<=$valores[1]){?>
												<p style="color: tomato;"> Has alcanzado el número máximo de calificaciones permitidas. </p>
											<?php }?>
											
												</div>
											</div>
											
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[52][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[68][$datosUsuarioActual['uss_idioma']];?></th>
														<th>#EC/#ET</th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													try{
														$consulta = mysqli_query($conexion, "SELECT * FROM academico_actividades
														INNER JOIN academico_indicadores ON ind_id=act_id_tipo
														WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1
														");
													} catch (Exception $e) {
														include("../compartido/error-catch-to-report.php");
													}
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$bg = '';
														try{
															$consultaNumerosEstudiantes=mysqli_query($conexion, "SELECT
															(SELECT count(*) FROM academico_calificaciones 
															INNER JOIN academico_matriculas ON mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 AND mat_id=cal_id_estudiante
															WHERE cal_id_actividad='".$resultado[0]."'),
															(SELECT count(*) FROM academico_matriculas 
															WHERE mat_grado='".$datosCargaActual[2]."' AND mat_grupo='".$datosCargaActual[3]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido)");
														} catch (Exception $e) {
															include("../compartido/error-catch-to-report.php");
														}
														$numerosEstudiantes = mysqli_fetch_array($consultaNumerosEstudiantes, MYSQLI_BOTH);
														if($numerosEstudiantes[0]<$numerosEstudiantes[1]) $bg = '#FCC';
														 
														 $porcentajeActual +=$resultado['act_valor'];
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['act_id'];?></td>
														<td><?=$resultado['act_descripcion'];?></td>
														<td><?=$resultado['act_fecha'];?></td>
														<td><?=$resultado['act_valor'];?></td>
														<td style="font-size: 10px;"><?=$resultado['ind_nombre'];?></td>
														<td style="background-color:<?=$bg;?>"><a href="../compartido/reporte-calificaciones.php?idActividad=<?=$resultado['act_id'];?>&grado=<?=$datosCargaActual[2];?>&grupo=<?=$datosCargaActual[3];?>" target="_blank" style="text-decoration: underline;"><?=$numerosEstudiantes[0];?>/<?=$numerosEstudiantes[1];?></a></td>
														<td>
															<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary">Acciones</button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="calificaciones-registrar.php?idR=<?=$resultado['act_id'];?>">Calificar</a></li>
																	  <li><a href="calificaciones-editar.php?idR=<?=$resultado['act_id'];?>">Editar</a></li>
																	  <li><a href="javascript:void(0);" name="guardar.php?get=12&idR=<?=$resultado['act_id'];?>&idIndicador=<?=$resultado['act_id_tipo'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" onClick="deseaEliminar(this)">Eliminar</a></li>
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
														<td colspan="4"><?=strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]);?></td>
														<td><?=$porcentajeActual;?>%</td>
														<td colspan="3"></td>
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
    <script src="../../config-general/assets/plugins/jquery/jquery.min.js" ></script>
    <script src="../../config-general/assets/plugins/popper/popper.js" ></script>
    <script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js" ></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- bootstrap -->
    <script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" ></script>
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