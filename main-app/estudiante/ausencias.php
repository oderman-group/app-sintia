<?php include("session.php");?>
<?php include("verificar-usuario.php");?>
<?php $idPaginaInterna = 'ES0022';?>
<?php include("../compartido/historial-acciones-guardar.php");?>
<?php include("verificar-carga.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");?>
<?php //include("verificar-pagina-bloqueada.php");?>
<?php include("../compartido/head.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");?>
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
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[106][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
											<?php
											$porcentaje = 0;
											for($i=1; $i<=$datosEstudianteActual['gra_periodos']; $i++){
												$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosEstudianteActual['mat_grado'], $i);
												
												$porcentajeGrado=25;
												if(!empty($periodosCursos['gvp_valor'])){
													$porcentajeGrado=$periodosCursos['gvp_valor'];
												}
												
												$notapp = Boletin::traerNotaBoletinCargaPeriodo($config, $i, $datosEstudianteActual['mat_id'], $cargaConsultaActual);
												$porcentaje=0;
												if(!empty($notapp['bol_nota'])){
													$porcentaje = ($notapp['bol_nota']/$config['conf_nota_hasta'])*100;
												}
												if(!empty($notapp['bol_nota']) && $notapp['bol_nota'] < $config['conf_nota_minima_aprobar']) $colorGrafico = 'danger'; else $colorGrafico = 'info';
												if($i==$periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"'; else $estiloResaltadoP = '';
											?>
												<p>
													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($i);?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$porcentajeGrado;?>%)</a>
													
													<?php
														if(!empty($notapp['bol_nota']) and $config['conf_sin_nota_numerica']!=1){

														$notaPorPeriodo=$notapp['bol_nota'];
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notapp['bol_nota']);
															$notaPorPeriodo= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
														}
													?>
														<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><b><?=$frases[62][$datosUsuarioActual['uss_idioma']];?>:</b> <?=$notaPorPeriodo;?></div>
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
								
									
									<?php include("filtro-cargas.php");?>
								
									<?php include("../compartido/publicidad-lateral.php");?>
									
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
													 $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_clases 
													 WHERE cls_id_carga='".$cargaConsultaActual."' AND cls_periodo='".$periodoConsultaActual."' AND  cls_estado=1 AND cls_disponible=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
														$contReg=1;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$ausencia = Ausencias::traerAusenciasClaseEstudiante($config, $resultado['cls_id'], $datosEstudianteActual['mat_id']);
														$totalAusencia=0;
														if(!empty($ausencia['aus_ausencias'])){
															$totalAusencia=$ausencia['aus_ausencias'];
														}
													 ?>
													<tr>
                                                        <td><?=$contReg;?></td>
														<td><?=$resultado['cls_id'];?></td>
														<td><a href="clases-ver.php?idR=<?=base64_encode($resultado['cls_id']);?>"><?=$resultado['cls_tema'];?></a></td>
														<td><?=$resultado['cls_fecha'];?></td>
														<td><?=$totalAusencia;?></td>
														<td>
															<a href="clases-ver.php?idR=<?=base64_encode($resultado['cls_id']);?>"><i class="material-icons">trending_flat</i></a>
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