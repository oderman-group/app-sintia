
<div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                
								<div class="col-md-4 col-lg-3">
									
									<div class="panel">
										<header class="panel-heading panel-heading-blue"><?=$frases[8][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
											<form action="<?=$_SERVER['PHP_SELF'];?>" method="get">
											<div class="form-group row">
												<div class="col-sm-8" data-hint="Aquí podrás buscar noticias específicas, dentro de todas las publicadas, usando palabras que claves que se encuentren en su titulo, descripción, etc.">
													<input type="text" name="busqueda" class="form-control" value="<?php if(isset($_GET['busqueda'])) echo $_GET["busqueda"];?>" placeholder="<?=$frases[235][$datosUsuarioActual[8]];?>...">
												</div>
												<div class="col-sm-4">
													<input type="submit" class="btn btn-primary" value="<?=$frases[8][$datosUsuarioActual[8]];?>">
												</div>
											</div>
											</form>
											<?php if(isset($_GET["busqueda"])){?><div align="center"><a href="<?=$_SERVER['PHP_SELF'];?>"><?=$frases[230][$datosUsuarioActual['uss_idioma']];?></a></div><?php }?>
										</div>
									</div>
									
									<div class="panel">
										<header class="panel-heading panel-heading-purple"><?=$frases[132][$datosUsuarioActual['uss_idioma']];?></header>
                                        <div class="panel-body">
											<p data-hint="Agrega una nueva publicación que tenga más contenido (Imagen, video, etc.)."><a href="noticias-agregar.php"><i class="fa fa-plus-circle"></i> <?=$frases[134][$datosUsuarioActual[8]];?></a></p>
											<p data-hint="Se mostrarán todas tus publicaciones que estén ocultas."><a href="../compartido/guardar.php?get=7&e=1"><i class="fa fa-eye"></i> <?=$frases[135][$datosUsuarioActual[8]];?></a></p>
											<p data-hint="Se ocultarán todas tus publicaciones que estén siendo mostradas."><a href="../compartido/guardar.php?get=7&e=0"><i class="fa fa-eye-slash"></i> <?=$frases[136][$datosUsuarioActual[8]];?></a></p>
											<p data-hint="Se eliminarán todas tus publicaciones realizadas."><a href="../compartido/guardar.php?get=7&e=2" onClick="if(!confirm('Deseas eliminar todas tus publicaciones?')){return false;}"><i class="fa fa-trash"></i> <?=$frases[137][$datosUsuarioActual[8]];?></a></p>
										</div>
									</div>
									
									<?php if($datosUsuarioActual[3]==2 or $datosUsuarioActual[3]==5){?>
										<?php include("../compartido/progreso-docentes.php");?>
									<?php }?>
									
									<?php include("../compartido/modulo-frases-lateral.php");?>
									
									<?php include("../compartido/publicidad-lateral.php");?>
									
								</div>
								
								
								<div class="col-md-4 col-lg-6">
									<div class="card card-box">
										<div class="card-head">
											<header><?=$frases[168][$datosUsuarioActual[8]];?></header>
										</div>
										
										<div class="card-body " id="bar-parent1">
											<form class="form-horizontal" action="../compartido/guardar.php" method="post">
												<input type="hidden" name="id" value="1">
												<div class="form-group row">
													<div class="col-sm-12" data-hint="Realiza una publicación rápida, con solo texto.">
														<textarea name="contenido" class="form-control" rows="3" placeholder="<?=$frases[169][$datosUsuarioActual[8]];?>" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
													</div>
												</div>

												<div class="form-group">
													<div class="offset-md-3 col-md-9">
														<button type="submit" class="btn btn-info"><?=$frases[170][$datosUsuarioActual[8]];?></button>
														<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual[8]];?></button>
													</div>
												</div>
											</form>
										</div>
										<div class="card-footer" align="center">
											<a href="noticias-agregar.php" class="btn btn-danger">CREA UNA PUBLICACIÓN MÁS COMPLETA</a>
										</div>
									</div>
									
									<?php include("../compartido/encuestas.php");?>
									
									<?php
									$arrayEnviar = array("tipo"=>4, "descripcionTipo"=>"Para ocultar fila del registro.");
									$arrayDatos = json_encode($arrayEnviar);
									$objetoEnviar = htmlentities($arrayDatos);
									?>
									

											<?php 
											$filtro = '';
											if(isset($_GET["busqueda"]) and $_GET["busqueda"]!=""){$filtro .= " AND (not_titulo LIKE '%".$_GET["busqueda"]."%') OR (not_descripcion LIKE '%".$_GET["busqueda"]."%') OR (not_keywords LIKE '%".$_GET["busqueda"]."%')";}
											if(isset($_GET["usuario"]) and is_numeric($_GET["usuario"])){$filtro .= " AND not_usuario='".$_GET["usuario"]."'";}
									
											$consulta = mysqli_query($conexion, "SELECT * FROM social_noticias
											INNER JOIN usuarios ON uss_id=not_usuario
											WHERE (not_estado=1 or (not_estado=0 and not_usuario='".$_SESSION["id"]."')) 
											AND (not_para LIKE '%".$datosUsuarioActual[3]."%' OR not_usuario='".$_SESSION["id"]."')
											$filtro
											ORDER BY not_id DESC
											");
											$not = 1;
											$contReg = 1;
											while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
												$colorFondo = 'style="background: #FFF;"';
												if($resultado[5]==0){$colorFondo = 'style="background: #999; opacity:0.7;"';}
												
												$consultaReacciones = mysqli_query($conexion, "SELECT * FROM social_noticias_reacciones
												INNER JOIN usuarios ON uss_id=npr_usuario
												WHERE npr_noticia='".$resultado[0]."'
												ORDER BY npr_id DESC
												");
												$numReacciones = mysqli_num_rows($consultaReacciones);
												$usrReacciones = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM social_noticias_reacciones 
												WHERE npr_noticia='".$resultado[0]."' AND npr_usuario='".$_SESSION["id"]."'"), MYSQLI_BOTH);
												
												if($datosUsuarioActual[3]==4){
													include("verificar-usuario.php");
													$noticiasCursos = mysqli_query($conexion, "SELECT * FROM social_noticias_cursos WHERE notpc_noticia='".$resultado[0]."'");
													$notCursoNum = mysqli_num_rows($noticiasCursos);
													if($notCursoNum>0){
														$noticiaPermitida=0;
														while($notCursosInfo = mysqli_fetch_array($noticiasCursos, MYSQLI_BOTH)){
															if($notCursosInfo['notpc_curso']==$datosEstudianteActual['mat_grado']) {$noticiaPermitida=1;}
														}
														if($noticiaPermitida==0) continue;
													}
												}
												
												
												
												$fotoUsr = $usuariosClase->verificarFoto($resultado['uss_foto']);
												
												
											?>
												<div id="PUB<?=$resultado['not_id'];?>" class="row">
													<div class="col-sm-12">
														<div class="panel" <?=$colorFondo;?>>
															
															<div class="card-head">
																<header><?=$resultado['not_titulo'];?></header>
																
																	<?php if($_SESSION["id"]==$resultado['not_usuario'] or $datosUsuarioActual[3]==5){?>
																	<button id ="panel-<?=$resultado['not_id'];?>" 
																	   class = "mdl-button mdl-js-button mdl-button--icon pull-right" 
																	   data-upgraded = ",MaterialButton">
																	   <i class = "material-icons">more_vert</i>
																	</button>
																	<ul class = "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
																	   data-mdl-for="panel-<?=$resultado['not_id'];?>">
																		<li class = "mdl-menu__item"><a href="noticias-editar.php?idR=<?=$resultado['not_id'];?>"><i class="fa fa-pencil-square-o"></i><?=$frases[165][$datosUsuarioActual[8]];?></a></li>
																	   <li class = "mdl-menu__item"><a href="../compartido/guardar.php?get=6&e=1&idR=<?=$resultado['not_id'];?>"><i class="fa fa-eye"></i><?=$frases[172][$datosUsuarioActual[8]];?></a></li>
																	   <li class = "mdl-menu__item"><a href="../compartido/guardar.php?get=6&e=0&idR=<?=$resultado['not_id'];?>"><i class="fa fa-eye-slash"></i><?=$frases[173][$datosUsuarioActual[8]];?></a></li>
																	   
																		<li class = "mdl-menu__item"><a href="#" title="<?=$objetoEnviar;?>" id="<?=$resultado['not_id'];?>" name="../compartido/guardar.php?get=6&e=2&idR=<?=$resultado['not_id'];?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i><?=$frases[174][$datosUsuarioActual[8]];?></a></li>
																	</ul>
																	<?php }?>
															</div>
															
															<div class="user-panel">
																	<div class="pull-left image">
																		<img src="<?=$fotoUsr;?>" class="img-circle user-img-circle" alt="User Image" height="50" width="50" />
																	</div>
																	<div class="pull-left info">
																		<p><a href="<?=$_SERVER['PHP_SELF'];?>?usuario=<?=$resultado['uss_id'];?>"><?=$resultado['uss_nombre'];?></a><br><span style="font-size: 11px;"><?=$resultado['not_fecha'];?></span></p>
																	</div>
																</div>

															<div class="panel-body">
																
																<p><?=$resultado['not_descripcion'];?></p>
																
																<?php if($resultado[7]!="" and file_exists('../files/publicaciones/'.$resultado[7])){?>
																	<div class="item"><a href="<?=$resultado['not_url_imagen'];?>" target="_blank"><img src="../files/publicaciones/<?=$resultado[7];?>" alt="<?=$resultado['not_titulo'];?>"></a></div>
																	<p>&nbsp;</p>
																<?php }?>
																
																<?php if($resultado['not_video']!=""){?>
																	<div><iframe width="450" height="400" src="https://www.youtube.com/embed/<?=$resultado['not_video'];?>?rel=0&amp;" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe></div>
																<?php }?>
																
																<?php if($resultado['not_archivo']!="" and file_exists('../files/publicaciones/'.$resultado['not_archivo'])){?>
																	<div align="right"><a href="../files/publicaciones/<?=$resultado['not_archivo'];?>" target="_blank"><i class="fa fa-download"></i> Descargar Archivo</a></div>
																<?php }?>
																
															</div>
															
															<div class="card-body">
																	<?php
																	 $rName = array("","Me gusta","Me encanta","Me divierte","Me entristece");
																	 $rIcons = array("","fa-thumbs-o-up","fa-heart","fa-smile-o","fa-frown-o");
																	 if(isset($usrReacciones['npr_reaccion']) AND $usrReacciones['npr_reaccion']!=""){$reaccionP = $usrReacciones['npr_reaccion'];}
																	 else{$reaccionP = 1;}
																	?>
																	<a id="panel-<?=$resultado['not_id'];?>1" class = "pull-left"><i class="fa <?=$rIcons[$reaccionP];?>"></i> <?=$rName[$reaccionP];?></a>
																	
																
																	<ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect"
																	   data-mdl-for="panel-<?=$resultado['not_id'];?>1">
																	   <?php
																	  $i=1;
																	  while($i<=4){
																		  if($i==$usrReacciones['npr_reaccion']){$estilos1='style="background:#6d84b4;"'; $estilos2='style="color:#FFF;"';}else{$estilos1=''; $estilos2='';}
																		?>	
																	   		<li class = "mdl-menu__item"><a href="../compartido/guardar.php?get=8&r=<?=$i;?>&idR=<?=$resultado['not_id'];?>&postname=<?=$resultado['not_titulo'];?>&usrname=<?=$datosUsuarioActual['uss_nombre'];?>&postowner=<?=$resultado['not_usuario'];?>"><i class="fa <?=$rIcons[$i];?>"></i><?=$rName[$i];?></a></li>
																		<?php $i++;}?>
																	</ul>
																	<?php if($numReacciones>0){?>
																		<a class ="pull-right" onClick="mostrarDetalles(this)" id="<?=$resultado['not_id'];?>"><?=number_format($numReacciones,0,",",".");?> reacciones</a>
																	<?php }?>
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
														<div class="panel" id="pub<?=$resultado['not_id'];?>" style="display: none;">
															<header class="panel-heading panel-heading-purple">
																Reacciones (<?=number_format($numReacciones,0,",",".");?>)
																<a class="pull-right" onClick="ocultarDetalles(this)" name="<?=$resultado['not_id'];?>">Ocultar</a>
															</header>
															<div class="panel-body">
																<?php
																while($datoReacciones = mysqli_fetch_array($consultaReacciones, MYSQLI_BOTH)){
																?>
																	<p><a><?=$datoReacciones['uss_nombre'];?></a> (<?=$rName[$datoReacciones['npr_reaccion']];?>)<br>
																		<span style="font-size: 10px; color: darkgray;"><?=$datoReacciones['npr_fecha'];?></span></p>
																<?php }?>
															</div>
														</div>
														
													</div>
												</div>
												
												<!-- SECCIÓN PUBLICITARIA -->
												<?php 
												if($not==3){
													$inicioPublicidad = ($contReg / $not) - 1;
													$publicidadNoticias = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".publicidad_ubicacion
													INNER JOIN ".$baseDatosServicios.".publicidad ON pub_id=pubxub_id_publicidad AND pub_estado=1
													WHERE pubxub_ubicacion=5 AND pubxub_id_institucion='".$config['conf_id_institucion']."'
													LIMIT $inicioPublicidad, 1
													"), MYSQLI_BOTH);
												?>
													<?php if(isset($publicidadNoticias['pubxub_id']) AND $publicidadNoticias['pubxub_id']!=""){
														mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".publicidad_estadisticas(pest_publicidad, pest_institucion, pest_usuario, pest_pagina, pest_ubicacion, pest_fecha, pest_ip, pest_accion)
														VALUES('".$publicidadNoticias['pub_id']."', '".$config['conf_id_institucion']."', '".$_SESSION["id"]."', '".$idPaginaInterna."', 5, now(), '".$_SERVER["REMOTE_ADDR"]."', 1)");
														
													?>
														<div align="center" style="padding-top: 5px; padding-bottom: 10px;">
															<span style="color: blue; font-size: 10px;">Promociado</span>
															<?php if($publicidadNoticias['pub_titulo']!=""){?><h4><?=$publicidadNoticias['pub_titulo'];?></h4><?php }?>
															<?php if($publicidadNoticias['pub_descripcion']!=""){?><p><?=$publicidadNoticias['pub_descripcion'];?></p><?php }?>
															<?php if($publicidadNoticias['pub_imagen']!=""){?>
																<div class="item"><a href="../compartido/guardar.php?get=14&idPag=<?=$idPaginaInterna;?>&idPub=<?=$publicidadNoticias['pub_id'];?>&idUb=5&url=<?=$publicidadNoticias['pub_url'];?>" target="_blank"><img src="http://plataformasintia.com/files-general/publicidad/<?=$publicidadNoticias['pub_imagen'];?>"></a></div>
																<p>&nbsp;</p>
															<?php }?>
															<?php if($publicidadNoticias['pub_video']!=""){?><p>
																<iframe width="450" height="415" src="https://www.youtube.com/embed/<?=$publicidadNoticias['pub_video'];?>?rel=0&amp;mute=<?=$publicidadNoticias['pub_mute'];?>&start=<?=$publicidadNoticias['pub_start'];?>&end=<?=$publicidadNoticias['pub_end'];?>&autoplay=<?=$publicidadNoticias['pub_autoplay'];?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen volume="0"></iframe></p>
															<?php }?>
														</div>
													<?php }?>
									
												<?php $not=0;}?>
												<!-- SECCIÓN PUBLICITARIA -->

											<?php
												$not++;
												$contReg ++;
											}
											?>
                                </div>
								
								
								<div class="col-md-4 col-lg-3">
									
									<div class="panel" data-hint="Se muestran las personas que están de cumpleaños en este día." id="../compartido/cumplimentados.php" title="cumplimentados" onClick="axiosAjax(this)">
										<header class="panel-heading panel-heading-red"><?php echo $frases[215][$datosUsuarioActual['uss_idioma']];?></header>
										
                                        <div id="RESP_cumplimentados" class="panel-body"></div>
									</div>		
											
									
                                </div>
								
							
                            </div>
                        </div>
                    </div>