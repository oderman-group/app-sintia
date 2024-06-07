<?php
	require_once(ROOT_PATH."/main-app/class/Boletin.php");
	$usrEstud="";
	if(!empty($_GET["usrEstud"])){ $usrEstud=base64_decode($_GET["usrEstud"]);}
?>
<?php require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");?>
<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[242][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[119][$datosUsuarioActual['uss_idioma']];?> </header>
                                        <div class="panel-body">
												<p><b>P.A:</b> <?=$frases[284][$datosUsuarioActual['uss_idioma']];?></p>
										</div>
									</div>
									
									<?php
									if($datosUsuarioActual['uss_tipo']!=4){
									?>

									<div class="panel">
											<header class="panel-heading panel-heading-yellow"><?=strtoupper($frases[283][$datosUsuarioActual['uss_idioma']]);?></header>

											<div class="panel-body">
												<ul class="list-group list-group-unbordered">
													<li class="list-group-item">
														<b><?=strtoupper($frases[61][$datosUsuarioActual['uss_idioma']]);?></b> 
														<div class="profile-desc-item pull-right"><?=Estudiantes::NombreCompletoDelEstudiante($datosEstudianteActual);?></div>
													</li>
													
												</ul>

											</div>
										</div>
									<?php }?>

									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
									
								<div class="col-md-8 col-lg-9">
                                    <div class="card card-topline-purple">
                                        <div class="card-head">
                                            <header><?=$frases[242][$datosUsuarioActual['uss_idioma']];?></header>
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
                                                        <th style="text-align:center;">#</th>
														<th style="text-align:center;"><?=$frases[49][$datosUsuarioActual['uss_idioma']];?></th>
														<th><?=$frases[116][$datosUsuarioActual['uss_idioma']];?></th>
														<th style="text-align:center;">P.A</th>
														<th style="text-align:center;"><?=$frases[118][$datosUsuarioActual['uss_idioma']];?></th>
														<th style="text-align:center;"><?=$frases[285][$datosUsuarioActual['uss_idioma']];?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php
													$contReg = 1; 
													$parametros = [
														'matcur_id_matricula' => $datosEstudianteActual["mat_id"],
														'matcur_id_institucion' => $config['conf_id_institucion'],
														'matcur_years' => $_SESSION['bd']
													];
													$listaCursosMediaTecnica = MediaTecnicaServicios::listar($parametros);
													$filtroOr='';
													if ($listaCursosMediaTecnica != null) { 
														foreach ($listaCursosMediaTecnica as $dato) {
															$filtroOr=$filtroOr.'OR (car_curso='.$dato["matcur_id_curso"].' AND car_grupo='.$dato["matcur_id_grupo"].')';
														}
													}
													$cCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosEstudianteActual['mat_grado'], $datosEstudianteActual['mat_grupo'], "", $filtroOr);
													while($rCargas = mysqli_fetch_array($cCargas, MYSQLI_BOTH)){
														$rDatos = Asignaturas::consultarAsignaturaCursoUsuario($conexion, $config, $rCargas['car_curso'], $rCargas['car_materia'], $rCargas['car_docente']);
														
														//DEFINITIVAS
														$carga = $rCargas['car_id'];
														$periodo = $rCargas['car_periodo'];
														$estudiante = $datosEstudianteActual['mat_id'];
														include("../definitivas.php");
														if($definitiva<$config[5] and $definitiva!="") $colorNota = $config[6]; elseif($definitiva>=$config[5]) $colorNota = $config[7]; else {$colorNota = 'black'; $definitiva='';}
														$definitivaFinal=$definitiva;
														if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
															$estiloNotaDefinitiva = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $definitiva);
															$definitivaFinal= !empty($estiloNotaDefinitiva['notip_nombre']) ? $estiloNotaDefinitiva['notip_nombre'] : "";
														}
													?>
                                                    
													<tr>
                                                        <td style="text-align:center;"><?=$contReg;?></td>
														<td style="text-align:center;"><?=$rCargas['car_id'];?></td>
														<td><?=$rDatos[1];?></td>
														<td style="text-align:center;"><?=$rCargas['car_periodo'];?></td>
														
														<?php if($config['conf_sin_nota_numerica']!=1){?>
														<td style="text-align:center;">
															<a href="calificaciones.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>&usrEstud=<?=base64_encode($usrEstud);?>" style="color:<?=$colorNota;?>; text-decoration:underline;"><?=$definitivaFinal;?></a>
														</td>
														<?php }else{?>
														<td style="text-align:center;">
															<a href="calificaciones.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>&usrEstud=<?=base64_encode($usrEstud);?>" style="text-decoration:underline;"><?=$frases[39][$datosUsuarioActual['uss_idioma']];?></a>
														</td>
														<?php }?>
														
														<td style="text-align:center;">
															<div class="btn-group">
																	  <button type="button" class="btn btn-danger"><?=$frases[88][$datosUsuarioActual['uss_idioma']];?></button>
																	  <button type="button" class="btn btn-danger dropdown-toggle m-r-20" data-toggle="dropdown">
																		  <i class="fa fa-angle-down"></i>
																	  </button>
																	  <ul class="dropdown-menu" role="menu">
																		  <li><a href="cronograma-actividades.php?carga=<?=base64_encode($rCargas['car_id']);?>&periodo=<?=base64_encode($rCargas['car_periodo']);?>&usrEstud=<?=base64_encode($usrEstud);?>"><?=$frases[111][$datosUsuarioActual['uss_idioma']];?></a></li>
																	  </ul>
																  </div>
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