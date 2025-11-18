<?php
$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}
$usuario="";
if(!empty($_GET["usuario"])){ $usuario=base64_decode($_GET["usuario"]);}
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Foros.php");

$datosConsultaBD = Foros::consultarDatosForos($conexion, $config, $idR);
?>					
					<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class="pull-left">
                                <div class="page-title"><?=$datosConsultaBD['foro_nombre'];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>

					<div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									
									
									<div class="panel">
											<header class="panel-heading panel-heading-yellow">Participantes</header>

											<div class="panel-body">
												<p>Este es el listado de los que han entrado a este foro.</p>
												<ul class="list-group list-group-unbordered">
													<?php
													$urlRecurso = 'foros-detalles.php?idR='.$_GET["idR"];
													$filtroAdicional= "AND mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
													$cursoActual=GradoServicios::consultarCurso($datosCargaActual['car_curso']);
													$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,$datosCargaActual['car_grupo']);
													
													// PRE-CARGAR TODOS LOS INGRESOS AL FORO DE TODOS LOS ESTUDIANTES
													// EN UNA SOLA CONSULTA PARA EVITAR N+1 QUERIES
													// ============================================
													$ingresosMapa = [];
													$idsUsuarios = [];
													$listaEstudiantes = [];
													
													// Guardar estudiantes en array para reutilizar
													while($est = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$listaEstudiantes[] = $est;
														$idsUsuarios[] = $est['uss_id'];
													}
													
													if (!empty($idsUsuarios)) {
														$idsUsuariosEsc = array_map(function($id) use ($conexion) {
															return "'" . mysqli_real_escape_string($conexion, $id) . "'";
														}, $idsUsuarios);
														$inUsuarios = implode(',', $idsUsuariosEsc);
														$urlRecursoEsc = mysqli_real_escape_string($conexion, $urlRecurso);
														$yearEsc = mysqli_real_escape_string($conexion, $_SESSION["bd"]);
														$institucion = (int)$config['conf_id_institucion'];
														
														$sqlIngresos = "SELECT hil_id, hil_usuario, hil_url, hil_titulo, hil_fecha
																		FROM ".$baseDatosServicios.".seguridad_historial_acciones 
																		WHERE hil_url LIKE '%".$urlRecursoEsc."%' 
																		AND hil_usuario IN ({$inUsuarios}) 
																		AND (hil_fecha LIKE '%".$yearEsc."%' OR hil_institucion=".$institucion.")
																		ORDER BY hil_fecha DESC";
														
														$consultaIngresos = mysqli_query($conexion, $sqlIngresos);
														if ($consultaIngresos) {
															while($ingreso = mysqli_fetch_array($consultaIngresos, MYSQLI_BOTH)){
																$idUsuario = $ingreso['hil_usuario'];
																// Guardar solo el primer ingreso (más reciente) por usuario
																if (!isset($ingresosMapa[$idUsuario])) {
																	$ingresosMapa[$idUsuario] = $ingreso;
																}
															}
														}
													}
													
													$contReg = 1;
													foreach($listaEstudiantes as $resultado){
														$nombreCompleto =Estudiantes::NombreCompletoDelEstudiante($resultado);
														$ingresoClase = $ingresosMapa[$resultado['uss_id']] ?? null;
														
														if($ingresoClase){
													?>
													<li class="list-group-item">
														<a href="foros-detalles.php?idR=<?=$_GET["idR"];?>&usuario=<?=base64_encode($resultado['mat_id_usuario']);?>"><?=$nombreCompleto?></a> 
														<div class="profile-desc-item pull-right"><?=$ingresoClase['hil_fecha'];?></div>
													</li>
													<?php }}?>
												</ul>
												
												<p align="center"><a href="foros-detalles.php?idR=<?=$_GET["idR"];?>">VER TODOS</a></p>

											</div>
										</div>
									
								</div>
								
								
								<div class="col-md-4 col-lg-6">
									
									<div class="card card-box">
										<div class="card-head">
											<header><?=$datosConsultaBD['foro_nombre'];?></header>
										</div>
										
										<div class="card-body " id="bar-parent1">
											<?=$datosConsultaBD['foro_descripcion'];?>
										</div>
									</div>
									
									<div class="card card-box">
										
										<div class="card-body " id="bar-parent1">
										<form class="form-horizontal" action="../compartido/foros-guardar-comentario.php" method="post">
											<input type="hidden" name="foro" value="<?=$idR;?>">
											
											<div class="form-group row">
												<div class="col-sm-12">
													<textarea name="contenido" class="form-control" rows="3" placeholder="Tu comentario" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
												</div>
											</div>
											
											<div class="form-group">
												<div class="offset-md-3 col-md-9">
													<button type="submit" class="btn btn-info">Comentar</button>
													<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual['uss_idioma']];?></button>
												</div>
											</div>
										</form>
											
										</div>
									</div>
									
									

											<?php 
											$filtro = '';
											if(is_numeric($usuario)){$filtro .= " AND com_id_estudiante='".$usuario."'";}
											
											// PRE-CARGAR TODAS LAS RESPUESTAS DE TODOS LOS COMENTARIOS
											// EN UNA SOLA CONSULTA PARA EVITAR N+1 QUERIES
											// ============================================
											$respuestasMapa = Foros::traerRespuestasForoMapa($conexion, $config, $idR);
											
											$consulta = Foros::traerComentariosForos($conexion, $config, $idR, $filtro);
											$contReg = 1;
											while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
												// Obtener respuestas del mapa pre-cargado
												$respuestasComentario = $respuestasMapa[$resultado['com_id']] ?? [];
												$numReacciones = count($respuestasComentario);
	
											?>
												<div id="PUB<?=$resultado['com_id'];?>" class="row">
													<div class="col-sm-12">
														<div class="panel">
															
															<div class="card-head">
																
																	<?php if($_SESSION["id"]==$resultado['com_id_estudiante']){
																		 $href='../compartido/foros-eliminar-comentario.php?e='.base64_encode(2).'&idCom='.base64_encode($resultado['com_id']).'&idR='.$_GET["idR"];
																		?>
																	<button id ="panel-<?=$resultado['com_id'];?>" 
																	   class = "mdl-button mdl-js-button mdl-button--icon pull-right" 
																	   data-upgraded = ",MaterialButton">
																	   <i class = "material-icons">more_vert</i>
																	</button>
																	<ul class = "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
																	   data-mdl-for="panel-<?=$resultado['com_id'];?>">
																	   <li class = "mdl-menu__item"><a href="#" onClick="sweetConfirmacion('Alerta!','Deseas eliminar este registro?','question','<?= $href ?>')"><i class="fa fa-trash"></i><?=$frases[174][$datosUsuarioActual['uss_idioma']];?></a></li>
																	</ul>
																	<?php }?>
															</div>
															
															<div class="user-panel">
																	<div class="pull-left image">
																		<img src="../files/fotos/<?=$resultado['uss_foto'];?>" class="img-circle user-img-circle" alt="User Image" height="50" width="50" />
																	</div>
																	<div class="pull-left info">
																		<p><a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=$_GET["idR"];?>&usuario=<?=base64_encode($resultado['uss_id']);?>"><?=$resultado['uss_nombre'];?></a><br><span style="font-size: 11px;"><?=$resultado['com_fecha'];?></span></p>
																	</div>
															</div>

															<div class="panel-body">
																<p><?=$resultado['com_descripcion'];?></p>	
															</div>

															<div class="card-body">
																<a class ="pull-right" onClick="mostrarDetalles(this)" id="<?=base64_encode($resultado['com_id']);?>"><?=number_format($numReacciones,0,",",".");?> respuestas</a>
															</div>
															
														</div>
														<script type="application/javascript">
															function mostrarDetalles(dato){
																var id = 'pub'+dato.id;
																document.getElementById(id).style.display = "block";
															}
															function ocultarDetalles(dato){
																var id = 'pub'+dato.name;
																document.getElementById(id).style.display = "none";
															}
														</script>
														<div class="panel" id="pub<?=base64_encode($resultado['com_id']);?>" style="display: none;">
															<header class="panel-heading panel-heading-purple">
																Respuestas (<?=number_format($numReacciones,0,",",".");?>)
																<a class="pull-right" onClick="ocultarDetalles(this)" name="<?=base64_encode($resultado['com_id']);?>">Ocultar</a>
															</header>
															<div class="panel-body">
																<form class="form-horizontal" action="../compartido/foros-guardar-respuesta.php" method="post">
																	<input type="hidden" name="idR" value="<?=$idR;?>">
																	<input type="hidden" name="comentario" value="<?=$resultado['com_id'];?>">

																	<div class="form-group row">
																		<div class="col-sm-9">
																			<input name="contenido" class="form-control" placeholder="Tu respuesta" required />
																		</div>
																		<div class="col-sm-3">
																			<button type="submit" class="btn btn-info">Responder</button>
																		</div>
																	</div>

																	<div class="form-group">
																		
																	</div>
																</form>
																
																<?php
																// Iterar sobre el array pre-cargado en lugar de hacer consulta
																foreach($respuestasComentario as $datoReacciones){
																?>
																	<p>
																		<?php if($_SESSION["id"]==$datoReacciones['fore_id_estudiante']){?>
																			<a href="#" name="../compartido/foros-eliminar-respuesta.php?idResp=<?=base64_encode($datoReacciones['fore_id']);?>&idCom=<?=base64_encode($resultado['com_id']);?>&idR=<?=$_GET["idR"];?>" onClick="deseaEliminar(this)"><i class="fa fa-times"></i></a>
																		<?php }?>
																		<a><?=$datoReacciones['uss_nombre'];?></a>: <?=$datoReacciones['fore_respuesta'];?></p>
																<?php }?>
															</div>
														</div>
														
													</div>
												</div>

											<?php
												$contReg ++;
											}
											?>
                                </div>
								
								
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=strtoupper($frases[113][$datosUsuarioActual['uss_idioma']]);?> </header>
										<div class="panel-body">
											<?php
											$registrosEnComun = Foros::traerForosDisintos($conexion, $config, $idR, $cargaConsultaActual, $periodoConsultaActual);
											while($regComun = mysqli_fetch_array($registrosEnComun, MYSQLI_BOTH)){
											?>
												<p><a href="<?=$_SERVER['PHP_SELF'];?>?idR=<?=base64_encode($regComun['foro_id']);?>"><?=$regComun['foro_nombre'];?></a></p>
											<?php }?>
										</div>
                                    </div>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
                                </div>
								
							
                            </div>
                        </div>
                    </div>