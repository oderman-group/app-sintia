



<div class="page-content">

                    <div class="page-bar">

                        <div class="page-title-breadcrumb">

                            <div class=" pull-left">

                                <div class="page-title"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?> </div>

								<?php include("../compartido/texto-manual-ayuda.php");?>

                            </div>

							

								<?php 

								//DOCENTES

								if($datosUsuarioActual[3]==2){?>

									<ol class="breadcrumb page-breadcrumb pull-right">

										<li><a class="parent-item" href="periodos-resumen.php"><?=$frases[84][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

										<li class="active"><?=$frases[6][$datosUsuarioActual['uss_idioma']];?></li>

									</ol>

								<?php }?>

							

								<?php 

								//ACUDIENTES

								if($datosUsuarioActual[3]==3){?>

									<ol class="breadcrumb page-breadcrumb pull-right">

										<li><a class="parent-item" href="estudiantes.php"><?=$frases[71][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

										<li><a class="parent-item" href="periodos-resumen.php?usrEstud=<?=$_GET["usrEstud"];?>"><?=$frases[84][$datosUsuarioActual[8]];?></a>&nbsp;<i class="fa fa-angle-right"></i></li>

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

									if($datosUsuarioActual[3]!=4){

									?>



									<div class="panel">

											<header class="panel-heading panel-heading-yellow">INFORMACIÓN DE CONSULTA</header>



											<div class="panel-body">

												<ul class="list-group list-group-unbordered">

													<li class="list-group-item">

														<b>ESTUDIANTE</b> 

														<div class="profile-desc-item pull-right"><?=strtoupper($datosEstudianteActual[3]." ".$datosEstudianteActual[4]." ".$datosEstudianteActual[5]);?></div>

													</li>

													<li class="list-group-item">

														<b>ASIGNATURA</b> 

														<div class="profile-desc-item pull-right"><?=strtoupper($datosCargaActual['mat_nombre']);?></div>

													</li>

													

													<li class="list-group-item">

														<b>PERIODO</b> 

														<div class="profile-desc-item pull-right"><?=strtoupper($_GET["periodo"]);?></div>

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

													<a href="<?=$_SERVER['PHP_SELF'];?>?carga=<?=$cargaConsultaActual;?>&periodo=<?=$i;?>&usrEstud=<?=$_GET["usrEstud"];?>" <?=$estiloResaltadoP;?>><?=strtoupper($frases[27][$datosUsuarioActual['uss_idioma']]);?> <?=$i;?> (<?=$periodosCursos['gvp_valor'];?>%)</a>

													

													<?php if($notapp[0]!="" and $config['conf_sin_nota_numerica']!=1){?>

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



							

									<?php 

									//ESTUDIANTES

									if($datosUsuarioActual[3]==4){

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

													 $filtro = '';

													 if(is_numeric($_GET["indicador"])){$filtro .= " AND act_id_tipo='".$_GET["indicador"]."'";}

													 $consulta = mysqli_query($conexion, "SELECT * FROM academico_actividades 

													 WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."'

													 AND act_registrada=1 AND act_estado=1 $filtro

													 ");

													 $contReg = 1;

													 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){

														$nota = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_calificaciones
														WHERE cal_id_actividad='".$resultado[0]."' AND cal_id_estudiante='".$datosEstudianteActual[0]."'"), MYSQLI_BOTH);

														$porNuevo = ($resultado[3] / 100);

														$acumulaValor = ($acumulaValor + $porNuevo);

														$notaMultiplicada = ($nota[3] * $porNuevo);

														$sumaNota = ($sumaNota + $notaMultiplicada);

														//COLOR DE CADA NOTA

														if($nota[3]<$config[5]) $colorNota = $config[6];

														else $colorNota = $config[7];

														$indicadorName = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_indicadores 
															INNER JOIN academico_indicadores_carga ON ipc_indicador=ind_id
															WHERE ind_id='".$resultado['act_id_tipo']."'
															"), MYSQLI_BOTH); 

													 ?>

                                                    

													<tr>

                                                        <td><?=$contReg;?></td>

														<td><?=$resultado[0];?></td>

														<td>
															<?=$resultado[1];?><br>
															<span style="font-size: 10px; color: blue;"><b>INDICADOR:</b> <?=$indicadorName['ind_nombre']." (".$indicadorName['ipc_valor']."%)";?></span>
														</td>

														<td><?=$resultado[2];?></td>

														<td><?=$resultado[3];?>%</td>

														<td style="color:<?=$colorNota;?>"><?=$nota[3];?></td>

														<td><?=$nota[4];?></td>

                                                    </tr>

													<?php 

														 $contReg++;

													  }

														//DEFINITIVAS

														$carga = $cargaConsultaActual;

														$periodo = $periodoConsultaActual;

														$estudiante = $datosEstudianteActual[0];

														include("../definitivas.php");

													  ?>

                                                </tbody>

												<?php

													if(($datosUsuarioActual[3]==3 or $datosUsuarioActual[3]==4) and $config['conf_sin_nota_numerica']==1){}else{

													?>

												<tfoot>

													<tr style="font-weight:bold;">

														<td colspan="4"><?=strtoupper($frases[107][$datosUsuarioActual['uss_idioma']]);?></td>

														<td><?=$porcentajeActual;?>%</td>

														<td style="color:<?=$colorDefinitiva;?>"><?=$definitiva;?></td>

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