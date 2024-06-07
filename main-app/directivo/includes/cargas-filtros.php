<div class="col-md-4 col-lg-3">
									<div class="panel">
										<header class="panel-heading panel-heading-red">MENÚ <?=strtoupper($frases[12][$datosUsuarioActual['uss_idioma']]);?></header>
										<div class="panel-body">
                                        	
											<p><a href="cargas-transferir.php">Transferir cargas</a></p>
											<p><a href="cargas-estilo-notas.php">Estilo de notas</a></p>
											<p><a href="cargas-indicadores-obligatorios.php">Indicadores obligatorios</a></p>
										</div>
                                	</div>
									
									<?php
									require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
									require_once ROOT_PATH."/main-app/class/Grupos.php";
									require_once(ROOT_PATH."/main-app/class/Grados.php");
									require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
									
									$estadisticasCargas = CargaAcademica::contarCargas($config);
									?>
									
									<h4 align="center"><?=strtoupper($frases[205][$datosUsuarioActual['uss_idioma']]);?></h4>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[5][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$cursos = Grados::traerGradosInstitucion($config);
											while($curso = mysqli_fetch_array($cursos, MYSQLI_BOTH)){
												$estudiantesPorGrado = CargaAcademica::contarCargasGrado($config, $curso['gra_id']);
												$porcentajePorGrado = 0;
												if(!empty($estadisticasCargas[0])){
													$porcentajePorGrado = round(($estudiantesPorGrado[0]/$estadisticasCargas[0])*100,2);
												}
												if($curso['gra_id']==$_GET["curso"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$curso['gra_id'];?>&grupo=<?=$_GET["grupo"];?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=strtoupper($curso['gra_nombre']);?>: <b><?=$estudiantesPorGrado[0];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajePorGrado;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajePorGrado;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Grupos </header>
										<div class="panel-body">
											<?php
											$grupos = Grupos::listarGrupos();
											while($grupo = mysqli_fetch_array($grupos, MYSQLI_BOTH)){
												if($grupo['gru_id']==$_GET["grupo"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$grupo['gru_id'];?>&curso=<?=$_GET["curso"];?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=strtoupper($grupo['gru_nombre']);?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[28][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$docentes = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_tipo = ".TIPO_DOCENTE." AND uss_bloqueado=0
											ORDER BY uss_nombre");
											while($docente = mysqli_fetch_array($docentes, MYSQLI_BOTH)){
												$cargasPorDocente = CargaAcademica::contarCargasDocente($config, $docente['uss_id']);
												$porcentajePorGrado = 0;
												if(!empty($estadisticasCargas[0])){
													$porcentajePorGrado = round(($cargasPorDocente[0]/$estadisticasCargas[0])*100,2);
												}
												if($docente['uss_id']==$_GET["docente"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>&grupo=<?=$_GET["grupo"];?>&docente=<?=$docente['uss_id'];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=UsuariosPadre::nombreCompletoDelUsuario($docente);?>: <b><?=$cargasPorDocente[0];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajePorGrado;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajePorGrado;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[73][$datosUsuarioActual['uss_idioma']];?> </header>
										<div class="panel-body">
											<?php
											$docentes = Asignaturas::consultarTodasAsignaturas($conexion, $config);
											while($docente = mysqli_fetch_array($docentes, MYSQLI_BOTH)){
												$cargasPorDocente = CargaAcademica::contarCargasMaterias($config, $docente['mat_id']);
												$porcentajePorGrado = 0;
												if(!empty($estadisticasCargas[0])){
													$porcentajePorGrado = round(($cargasPorDocente[0]/$estadisticasCargas[0])*100,2);
												}
												if($docente['mat_id']==$_GET["asignatura"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
											
												<div class="work-monitor work-progress">
															<div class="states">
																<div class="info">
																	<div class="desc pull-left"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>&grupo=<?=$_GET["grupo"];?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$docente['mat_id'];?>" <?=$estiloResaltado;?>><?=strtoupper($docente['mat_nombre']);?>: <b><?=$cargasPorDocente[0];?></b></a></div>
																	<div class="percent pull-right"><?=$porcentajePorGrado;?>%</div>
																</div>

																<div class="progress progress-xs">
																	<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentajePorGrado;?>%">
																		<span class="sr-only">90% </span>
																	</div>
																</div>
															</div>
														</div>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple">Cantidades </header>
										<div class="panel-body">
											<?php
											for($i=10; $i<=100; $i=$i+10){
												if($i==$_GET["cantidad"]) $estiloResaltado = 'style="color: orange;"'; else $estiloResaltado = '';
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?grupo=<?=$_GET['grupo'];?>&curso=<?=$_GET["curso"];?>&cantidad=<?=$i;?>&docente=<?=$_GET["docente"];?>&asignatura=<?=$_GET["asignatura"];?>" <?=$estiloResaltado;?>><?=$i." cargas";?></a></p>
											<?php }?>
											<p align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?curso=<?=$_GET['curso'];?>&grupo=<?=$_GET["grupo"];?>">VER TODOS</a></p>
										</div>
                                    </div>
									
									
									
									<?php include("../compartido/publicidad-lateral.php");?>
								</div>