<?php include("session.php");?>
<?php $idPaginaInterna = 'AC0016'; ?>
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
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[106][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<?php
											$porcentaje = 0;
											for($i=1; $i<=$datosEstudianteActual['gra_periodos']; $i++){
												$periodosCursos = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_grados_periodos
												WHERE gvp_grado='".$datosEstudianteActual['mat_grado']."' AND gvp_periodo='".$i."'
												"), MYSQLI_BOTH);
												
												$notapp = mysqli_fetch_array(mysqli_query($conexion, "SELECT bol_nota FROM academico_boletin 
												WHERE bol_estudiante='".$datosEstudianteActual['mat_id']."' AND bol_carga='".$cargaConsultaActual."' AND bol_periodo='".$i."'"), MYSQLI_BOTH);
												$porcentaje = ($notapp[0]/$config['conf_nota_hasta'])*100;
												if($notapp[0] < $config['conf_nota_minima_aprobar']) $colorGrafico = 'danger'; else $colorGrafico = 'info';
												if($i==$periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"'; else $estiloResaltadoP = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=$cargaConsultaActual;?>&periodo=<?=$i;?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$periodosCursos['gvp_valor'];?>%)</a>
													
													<?php if($notapp[0]!=""){?>
														<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><b><?=$frases[62][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$notapp[0];?></div>
																	<div class="percent pull-right"><?=$porcentaje;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-<?=$colorGrafico;?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentaje;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
													<?php }?>
											
												</p><hr>
											<?php }?>
										
										</div>
									</div>
								
							
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$cCargas = mysqli_query($conexion, "SELECT * FROM academico_cargas 
											INNER JOIN academico_materias ON mat_id=car_materia
											WHERE car_curso='".$datosEstudianteActual[6]."' AND car_grupo='".$datosEstudianteActual[7]."'");
											$nCargas = mysqli_num_rows($cCargas);
											while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
												if($rCargas['car_id']==$cargaConsultaActual) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=$rCargas['car_id'];?>&periodo=<?=$periodoConsultaActual;?>" <?=$estiloResaltado;?>><?=strtoupper($rCargas['mat_nombre']);?></a></p>
											<?php }?>
										</div>
                                    </div>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[7][$datosUsuarioActual['uss_idioma']];?></header>
                                            <div class="tools">
                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
			                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
			                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                            </div>
                                        </div>
                                        <div class="card-body ">
                                        <div class="table-responsive">
                                            <table class="table table-striped custom-table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[30][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[103][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_clases 
													 WHERE cls_id_carga='".$cargaConsultaActual."' AND cls_periodo='".$periodoConsultaActual."' AND cls_registrada=1 AND cls_estado=1");
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$ausencia = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_ausencias 
														WHERE aus_id_clase='".$resultado[0]."' AND aus_id_estudiante='".$datosEstudianteActual[0]."'"), MYSQLI_BOTH);
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado[0];?></td>
														<td><?=$resultado[1];?></td>
														<td><?=$resultado[2];?></td>
														<td><?=$ausencia[3];?></td>
														<td>
															<a href="clases-ver.php?idClase=<?=$resultado[0];?>"><i class="material-icons">trending_flat</i></a>
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