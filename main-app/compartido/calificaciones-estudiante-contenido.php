<?php
	require_once(ROOT_PATH."/main-app/class/Boletin.php");
	require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
	require_once(ROOT_PATH."/main-app/class/Grados.php");
	require_once(ROOT_PATH."/main-app/class/Indicadores.php");
	require_once(ROOT_PATH."/main-app/class/Actividades.php");
	require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
	$usrEstud="";
	if(!empty($_GET["usrEstud"])){ $usrEstud=base64_decode($_GET["usrEstud"]);}
?>

<div class="page-content">

                    <div class="page-bar">

                        <div class="page-title-breadcrumb">

                            <div class=" pull-left">

                                <div class="page-title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?> </div>

								<?php include("../compartido/texto-manual-ayuda.php");?>

                            </div>

							

								<?php 

								//DOCENTES

								if($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE){?>

									<ol class="breadcrumb page-breadcrumb pull-right">

										<li><a class="parent-item" href="calificaciones.php?tab=4"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

										<li class="active"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></li>

									</ol>

								<?php }?>

							

								<?php 

								//ACUDIENTES

								if($datosUsuarioActual['uss_tipo'] == TIPO_ACUDIENTE){?>

									<ol class="breadcrumb page-breadcrumb pull-right">

										<li><a class="parent-item" href="estudiantes.php"><?=$frases[71][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

										<li><a class="parent-item" href="periodos-resumen.php?usrEstud=<?=base64_encode($usrEstud);?>"><?=$frases[84][$datosUsuarioActual['uss_idioma']];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

										<li class="active"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></li>

									</ol>

								<?php }?>

                        </div>

                    </div>

                    

                    <div class="row">

                        <div class="col-md-12">

                            <div class="row">

                                

								<div class="col-md-4 col-lg-3">

									

									<?php

									if($datosUsuarioActual['uss_tipo']!=TIPO_ESTUDIANTE){

									?>



									<div class="panel">

											<header class="panel-heading panel-heading-yellow"><?=strtoupper($frases[283][$datosUsuarioActual['uss_idioma']]);?></header>



											<div class="panel-body">

												<ul class="list-group list-group-unbordered">

													<li class="list-group-item">

														<b><?=strtoupper($frases[61][$datosUsuarioActual['uss_idioma']]);?></b> 

														<div class="profile-desc-item pull-right"><?=Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual);;?></div>

													</li>

													<li class="list-group-item">

														<b><?=strtoupper($frases[116][$datosUsuarioActual['uss_idioma']]);?></b> 

														<div class="profile-desc-item pull-right"><?=strtoupper($datosCargaActual['mat_nombre']);?></div>

													</li>

													

													<li class="list-group-item">

														<b><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?></b> 

														<div class="profile-desc-item pull-right"><?=strtoupper($periodo);?></div>

													</li>

													

												</ul>



											</div>

										</div>

									<?php }?>

									

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

												if(!empty($notapp['bol_nota'])){
													$porcentaje = ($notapp['bol_nota']/$config['conf_nota_hasta'])*100;
												}

												if(!empty($notapp['bol_nota']) and $notapp['bol_nota'] < $config['conf_nota_minima_aprobar']) $colorGrafico = 'danger'; else $colorGrafico = 'info';

												if($i==$periodoConsultaActual) $estiloResaltadoP = 'style="color: orange;"'; else $estiloResaltadoP = '';

											?>

												<p>

													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=base64_encode($cargaConsultaActual);?>&periodo=<?=base64_encode($i);?>&usrEstud=<?=base64_encode($usrEstud);?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$porcentajeGrado;?>%)</a>

													

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



							

									<?php 

									//ESTUDIANTES

									if($datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE){

										include("filtro-cargas.php");

									}

									?>

								

									<?php include("../compartido/publicidad-lateral.php");?>

									

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

                                        <div class="card-body ">

                                        <div class="table-responsive">

                                            <table class="table table-striped custom-table table-hover">

                                                <thead>

                                                    <tr>

                                                        <th>#</th>

														<th><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>

														<th><?=$frases[50][$datosUsuarioActual['uss_idioma']];?></th>

														<th><?=$frases[51][$datosUsuarioActual['uss_idioma']];?></th>

														<th><?=$frases[52][$datosUsuarioActual['uss_idioma']];?></th>

														<th><?=$frases[108][$datosUsuarioActual['uss_idioma']];?></th>

														<th><?=$frases[109][$datosUsuarioActual['uss_idioma']];?></th>

                                                    </tr>

                                                </thead>

                                                <tbody>

													<?php
													$consulta = Actividades::consultaActividadesCarga($config, $cargaConsultaActual, $periodoConsultaActual);
													if(!empty($_GET["indicador"])){
														$consulta = Actividades::consultaActividadesCargaIndicador($config, base64_decode($_GET["indicador"]), $cargaConsultaActual, $periodoConsultaActual);
													}

													 $contReg = 1;

													 $acumulaValor = 0;

													 $sumaNota = 0;

													 $porcentajeActualActividad = 0;
													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){

														$nota = Calificaciones::traerCalificacionActividadEstudiante($config, $resultado['act_id'], $datosEstudianteActual['mat_id']);

														$porNuevo = ($resultado['act_valor'] / 100);

														$acumulaValor = ($acumulaValor + $porNuevo);

														$notaMultiplicada=0;
														$nota3="";
														$nota4="";
														if(!empty($nota['cal_nota'])){
															$nota3=$nota['cal_nota'];
															$nota4=$nota['cal_observaciones'];
															$notaMultiplicada = ($nota['cal_nota'] * $porNuevo);
														}

														$sumaNota = ($sumaNota + $notaMultiplicada);
														$porcentajeActualActividad +=$resultado['act_valor'];

														//COLOR DE CADA NOTA

														if(!empty($nota['cal_nota']) && $nota['cal_nota']<$config[5]) $colorNota = $config[6];

														else $colorNota = $config[7];

														$indicadorName = Indicadores::traerIndicadoresDatosRelacion($resultado['act_id_tipo']); 

															$notaFinal=$nota3;
															if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
																$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota3);
																$notaFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
															}

													 ?>

                                                    

													<tr>

                                                        <td><?=$contReg;?></td>

														<td><?=$resultado['act_id'];?></td>

														<td>
															<?=$resultado['act_descripcion'];?><br>
															<span style="font-size: 10px; color: blue;"><b>INDICADOR:</b> <?=$indicadorName['ind_nombre']." (".$indicadorName['ipc_valor']."%)";?></span>
														</td>

														<td><?=$resultado['act_fecha'];?></td>

														<td><?=$resultado['act_valor'];?>%</td>

														<td style="color:<?=$colorNota;?>"><?=$notaFinal;?></td>

														<td><?=$nota4;?></td>

                                                    </tr>

													<?php 

														 $contReg++;

													  }

														//DEFINITIVAS

														$carga = $cargaConsultaActual;

														$periodo = $periodoConsultaActual;

														$estudiante = $datosEstudianteActual['mat_id'];

														include("../definitivas.php");

														$definitivaFinal=$definitiva;
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
															$definitivaFinal= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
														}

													  ?>

                                                </tbody>

												<?php

													if(($datosUsuarioActual['uss_tipo']==TIPO_ACUDIENTE or $datosUsuarioActual['uss_tipo']==TIPO_ESTUDIANTE) and $config['conf_sin_nota_numerica']==1){}else{

													?>

												<tfoot>

													<tr style="font-weight:bold;">

														<td colspan="4"><?=strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]);?></td>

														<td><?=$porcentajeActualActividad;?>%</td>

														<td style="color:<?=$colorDefinitiva;?>"><?=$definitivaFinal;?></td>

														<td></td>

													 </tr>

												</tfoot>

												<?php }?>

                                            </table>

                                            </div>

                                        </div>

                                    </div>

                                </div>

								

							

                            </div>

                        </div>

                    </div>

                </div>