<?php include("session.php");?>
<?php $idPaginaInterna = 'DT0053';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");?>
<?php include("../compartido/head.php");?>
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
                                <div class="page-title"><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-3 col-lg-3">
									
									<?php if($periodoConsultaActual!=$datosCargaActual['car_periodo'] and $datosCargaActual['car_permiso2']!=1){?>
										<p style="color: tomato;"> Podrás consultar la información de otros periodos diferentes al actual, pero no se podrán hacer modificaciones. </p>
									<?php }?>
									
									<?php include("info-carga-actual.php");?>
									
									<?php include("filtros-cargas.php");?>
									
								</div>
									
								<div class="col-md-6 col-lg-6">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></header>
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
												($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and ($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1)) 
																	
												or($periodoConsultaActual<=$datosCargaActual['gra_periodos'] and $porcentajeRestante>0)
												)
											{
											?>
											
													<div class="btn-group">
														<a href="clases-agregar.php?carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" id="addRow" class="btn deepPink-bgcolor">
															Agregar nuevo <i class="fa fa-plus"></i>
														</a>
													</div>
													
													
											<?php
											}
											?>
													
											
												</div>
											</div>
											
											
											
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[68][$datosUsuarioActual['uss_idioma']];?></th>
														<th>#EC/#ET</th>
														<th><?=$frases[54][$datosUsuarioActual[8]];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_clases
													 WHERE cls_id_carga='".$cargaConsultaActual."' AND cls_periodo='".$periodoConsultaActual."' AND cls_estado=1");
													 $contReg = 1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$bg = '';
														$consultaNumerosEstudiantes=mysqli_query($conexion, "SELECT
														(SELECT count(*) FROM academico_ausencias 
														INNER JOIN academico_matriculas ON mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 AND mat_id=aus_id_estudiante
														WHERE aus_id_clase='".$resultado[0]."'),
														(SELECT count(*) FROM academico_matriculas 
														WHERE mat_grado='".$datosCargaActual[2]."' AND mat_grupo='".$datosCargaActual[3]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido)");
														$numerosEstudiantes = mysqli_fetch_array($consultaNumerosEstudiantes, MYSQLI_BOTH);
														if($numerosEstudiantes[0]<$numerosEstudiantes[1]) $bg = '#FCC';
														 
													 ?>
                                                    
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['cls_id'];?></td>
														<td><?=$resultado['cls_tema'];?></td>
														<td><?=$resultado['cls_fecha'];?></td>
														<td style="background-color:<?=$bg;?>"><?=$numerosEstudiantes[0];?>/<?=$numerosEstudiantes[1];?></td>
														<td>
															<?php if($periodoConsultaActual==$datosCargaActual['car_periodo'] or $datosCargaActual['car_permiso2']==1){?>
																<div class="btn-group">
																  <button type="button" class="btn btn-primary">Acciones</button>
																  <button type="button" class="btn btn-primary dropdown-toggle m-r-20" data-toggle="dropdown">
																	  <i class="fa fa-angle-down"></i>
																  </button>
																  <ul class="dropdown-menu" role="menu">
																	  <li><a href="clases-registrar.php?idR=<?=$resultado['cls_id'];?>">Inasistencias</a></li>
																	  <li><a href="clases-editar.php?idR=<?=$resultado['cls_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>">Editar</a></li>
																	  <li><a href="#" name="guardar.php?get=11&idR=<?=$resultado['cls_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" onClick="deseaEliminar(this)">Eliminar</a></li>
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
								
								<div class="col-md-3 col-lg-3">
									<div class="panel">
										<header class="panel-heading panel-heading-purple">PLAN DE CLASES</header>
										
										<div class="panel-body">
											<p>Puedes reemplazar el plan actual si ya tienes uno montado.</p>
											<form action="guardar.php" method="post" enctype="multipart/form-data">
												<input type="hidden" name="id" value="16">
												<div class="form-group row">
													<div class="col-sm-12">
														<input type="file" name="file" class="form-control">
													</div>
												</div>
												<input type="submit" class="btn btn-primary" value="Guardar cambios">
											</form>
											<?php
											$consultapClase=mysqli_query($conexion, "SELECT * FROM academico_pclase 
											WHERE pc_id_carga='".$cargaConsultaActual."' AND pc_periodo='".$periodoConsultaActual."'");
											$pclase = mysqli_fetch_array($consultapClase, MYSQLI_BOTH);
											if($pclase['pc_plan']!=""){
											?>
											<hr>
											<a href="../files/pclase/<?=$pclase['pc_plan'];?>" target="_blank"><i class="fa fa-download"></i> <?=$pclase['pc_plan'];?></a>
											<?php }?>
										</div>
									</div>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
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