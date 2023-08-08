<?php
require_once("../class/Estudiantes.php");
$consultaDatosBD=mysqli_query($conexion, "SELECT * FROM academico_clases 
WHERE cls_id='".$_GET["idR"]."' AND cls_estado=1");
$datosConsultaBD = mysqli_fetch_array($consultaDatosBD, MYSQLI_BOTH);
?>
					<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class="pull-left">
                                <div class="page-title"><?=$datosConsultaBD['cls_tema'];?></div>
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
												<p>Este es el listado de los que han entrado a esta clase.</p>
												<ul class="list-group list-group-unbordered">
													<?php
													$urlClase = 'clases-ver.php?idR='.$_GET["idR"];
													$filtroAdicional= "AND mat_grado='".$datosCargaActual[2]."' AND mat_grupo='".$datosCargaActual[3]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
													$cursoActual=GradoServicios::consultarCurso($datosCargaActual[2]);
													$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,"",$datosCargaActual[3]);
													$contReg = 1;
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$nombreCompleto =Estudiantes::NombreCompletoDelEstudiante($resultado);
														$consultaIngresoClase=mysqli_query($conexion, "SELECT hil_id, hil_usuario, hil_url, hil_titulo, hil_fecha
														FROM ".$baseDatosServicios.".seguridad_historial_acciones 
														WHERE hil_url LIKE '%".$urlClase."%' AND hil_usuario='".$resultado['uss_id']."' AND hil_fecha LIKE '%".$_SESSION["bd"]."%'
														UNION 
														SELECT hil_id, hil_usuario, hil_url, hil_titulo, hil_fecha 
														FROM ".$baseDatosServicios.".seguridad_historial_acciones 
														WHERE hil_url LIKE '%".$urlClase."%' AND hil_usuario='".$resultado['uss_id']."' AND hil_institucion='".$config['conf_id_institucion']."' AND hil_fecha LIKE '%".$_SESSION["bd"]."%'");
														$ingresoClase = mysqli_fetch_array($consultaIngresoClase, MYSQLI_BOTH);
														
														if($ingresoClase[0]==""){continue;}
													?>
													<li class="list-group-item">
														<a href="clases-ver.php?idR=<?=$_GET["idR"];?>&usuario=<?=$resultado['mat_id_usuario'];?>"><?=$nombreCompleto?></a> 
														<div class="profile-desc-item pull-right"><?=$ingresoClase['hil_fecha'];?></div>
													</li>
													<?php }?>
												</ul>
												
												<p align="center"><a href="clases-ver.php?idR=<?=$_GET["idR"];?>">VER TODOS</a></p>

											</div>
										</div>
									
								</div>
								
								
								<div class="col-md-4 col-lg-6">
									
									<?php 
									if($datosConsultaBD['cls_meeting']!="" and $datosConsultaBD['cls_clave_docente']!="" and $datosConsultaBD['cls_clave_estudiante']!=""){
										
										if($datosUsuarioActual['uss_tipo']==2){
											$nombreSala = trim($datosCargaActual['mat_nombre'])."_".trim($datosCargaActual['gra_nombre'])."_".trim($datosCargaActual['gru_nombre']);
									?>
										
											<input id="meetingID" name="meetingID" value="<?=$datosConsultaBD['cls_meeting'];?>" type="hidden">
											<input id="moderatorPW" name="moderatorPW" type="hidden" value="<?=$datosConsultaBD['cls_clave_docente'];?>">
											<input id="attendeePW" name="attendeePW" type="hidden" value="<?=$datosConsultaBD['cls_clave_estudiante'];?>">
											<input id="meetingName" name="meetingName" type="hidden" value="<?=strtoupper($nombreSala);?>">
											<input id="username" name="username" type="hidden" value="<?=$datosUsuarioActual['uss_nombre'];?>">

											<button id="startClass" value="123" class="btn btn-success">Iniciar clase en vivo</button>
											</br>
                							<div id="notificacion" class="alert alert-success" style="width: 450px; display: none;" role="alert"></div>
										
									<?php 
										}
										if($datosUsuarioActual['uss_tipo']==4){
									?>
								
											<input id="meetingID" name="meetingID" value="<?=$datosConsultaBD['cls_meeting'];?>" type="hidden">
											<input id="attendeePW" name="attendeePW" type="hidden" value="<?=$datosConsultaBD['cls_clave_estudiante'];?>">
											<input id="username" name="username" type="hidden" value="<?=$datosUsuarioActual['uss_nombre'];?>">

											<button id="startClassStudent" value="123" class="btn btn-success">Entrar a clase en vivo</button>
											</br>
                							<div id="notificacion" class="alert alert-success" style="width: 450px; display: none;" role="alert"></div>
									
									<?php
										}
									}
									?>
									
									<div class="card card-box">
										
										<div class="card-head">
											<header><?=$datosConsultaBD['cls_tema'];?></header>
											
											<?php if($datosUsuarioActual['uss_tipo']==2){?>
												<button id ="panel-p"  class = "mdl-button mdl-js-button mdl-button--icon pull-right" data-upgraded = ",MaterialButton">
													<i class = "material-icons">more_vert</i>
												</button>
												<ul class = "mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" data-mdl-for="panel-p">
													<li class = "mdl-menu__item"><a href="clases-editar.php?idR=<?=$datosConsultaBD['cls_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>"><i class="fa fa-edit"></i>Editar</a></li>
													<li class = "mdl-menu__item"><a href="#" name="guardar.php?get=11&idR=<?=$datosConsultaBD['cls_id'];?>&carga=<?=$cargaConsultaActual;?>&periodo=<?=$periodoConsultaActual;?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i>Eliminar</a></li>
												</ul>
											<?php }?>
											
										</div>
										
										<div class="card-body">
											
											<?php if($datosConsultaBD['cls_video']!=""){?>
											<p class="iframe-container">
												<iframe width="100%" height="400" src="https://www.youtube.com/embed/<?=$datosConsultaBD['cls_video'];?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
											</p>
											<?php }?>
											
											<!-- TRANSMISIÓN EN VIVO
											<video id="vid1" class="azuremediaplayer amp-default-skin" autoplay controls width="100%" height="400" poster="poster.jpg" data-setup='{"nativeControlsForTouch": false}'>
												<source src="https://liveevent-1837f3fb-7602-sintia.preview-usso.channel.media.azure.net/8bdf3c79-67c3-4fea-8977-d7247a6dfc26/preview.ism/manifest" type="application/vnd.ms-sstr+xml" />
												<p class="amp-no-js">
													To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video
												</p>
											</video>
											-->
											
										</div>
										
										
									</div>
									
									<div class="card card-box">
										<div class="card-head">
											<header>DESCRIPCIÓN</header>
										</div>
										
										<div class="card-body">
											<p><?=$datosConsultaBD['cls_descripcion'];?></p>
											
											<?php if($datosConsultaBD['cls_archivo']!=""){
												$nombre1 = $datosConsultaBD['cls_archivo'];
												if($datosConsultaBD['cls_nombre_archivo1']!=""){$nombre1 = $datosConsultaBD['cls_nombre_archivo1'];}
											?>
												<h4 style="font-weight: bold;">Archivos adjuntos</h4>
												<p><a href="../files/clases/<?=$datosConsultaBD['cls_archivo'];?>" style="text-decoration: underline;" target="_blank"><?=$nombre1;?></a></p>
											<?php }?>
											
											<?php if($datosConsultaBD['cls_archivo2']!=""){
												$nombre2 = $datosConsultaBD['cls_archivo2'];
												if($datosConsultaBD['cls_nombre_archivo2']!=""){$nombre2 = $datosConsultaBD['cls_nombre_archivo2'];}
											?>
												<p><a href="../files/clases/<?=$datosConsultaBD['cls_archivo2'];?>" style="text-decoration: underline;" target="_blank"><?=$nombre2;?></a></p>
											<?php }?>
											
											<?php if($datosConsultaBD['cls_archivo3']!=""){
												$nombre3 = $datosConsultaBD['cls_archivo3'];
												if($datosConsultaBD['cls_nombre_archivo3']!=""){$nombre3 = $datosConsultaBD['cls_nombre_archivo3'];}
											?>
												<p><a href="../files/clases/<?=$datosConsultaBD['cls_archivo3'];?>" style="text-decoration: underline;" target="_blank"><?=$nombre3;?></a></p>
											<?php }?>
										</div>

									</div>
									
									
									<div class="card card-box">
										
										<div class="card-head">
											<header>COMENTARIOS / PREGUNTAS</header>
										</div>
										
										<div class="card-body">
										<form class="form-horizontal" action="#" method="post">
											<input type="hidden" name="id" value="14">
											<input type="hidden" name="idClase" value="<?=$_GET["idR"];?>">
											<input type="hidden" name="sesionUsuario" value="<?=$_SESSION["id"];?>">
											<input type="hidden" name="bdConsulta" value="<?=$_SESSION["inst"];?>">
											<input type="hidden" name="agnoConsulta" value="<?=$_SESSION["bd"];?>">
											
											<input type="hidden" name="envia" id="envia">
											
											<div class="form-group row">
												<div class="col-sm-12">
													<textarea id="contenido" name="contenido" class="form-control" rows="3" placeholder="Escribe aquí una pregunta o comentario para este tema..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
												</div>
											</div>
											
											<div class="form-group">
												<div class="offset-md-3 col-md-9">
													<button  id="btnEnviar" class="btn btn-info"  onclick="this.disabled=true;guardar()">Enviar</button>
													
													<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual[8]];?></button>
												</div>
											</div>
										</form>
											
										</div>
									</div>
									
											<p style="color: tomato;">Los comentarios y/o preguntas más recientes aparecen automáticamente en la parte de abajo.</p>	

											<div id="preguntas"></div>
											
															<script>
																setInterval('consultarPreguntas()',10000);

																window.onload = consultarPreguntas();	
																
																function guardar(){																
																	id="14";
																	idClase=<?=$_GET["idR"];?>;
																	sesionUsuario=<?=$_SESSION["id"];?>;
																	contenido=document.getElementById("contenido").value;
																	btn=document.getElementById("btnEnviar");
																	if(validar()){
																		datos = "id="+id
																				+"&idClase="+idClase
																				+"&sesionUsuario="+sesionUsuario
																				+"&contenido="+contenido;
																			
																		
																			$.ajax({
																			type: "POST",
																			url: "../compartido/guardar.php",
																			data: datos,
																			success: function(data){
																				document.getElementById("contenido").value="";
																				btn.disabled=false;																				
																				consultarPreguntas();
																			}
																			});
																	}else{
																		btn.disabled=false;
																	}

																};
																function validar(){
																	contenido=document.getElementById("contenido").value;
																	if( contenido == null || contenido.length == 0 || /^\s+$/.test(contenido) ) {
																		return false;
																	}else{
																		return true;
																	}
																}
																function consultarPreguntas(){

																	var claseId = <?= $_GET["idR"]; ?>;
																	var usuarioActual = <?= $datosUsuarioActual['uss_id']; ?>;
																	

																	datos = "claseId="+claseId+"&usuarioActual="+usuarioActual;
																		$.ajax({
																		type: "POST",
																		url: "../compartido/ajax-comentarios-preguntas.php",
																		data: datos,
																		success: function(data){
																			$('#preguntas').empty().hide().html(data).show(1);

																		}
																		});



																}	

																
																															
															</script>
														
									
											
                                </div>
								
								
								<div class="col-md-4 col-lg-3">
									
									<div class="panel" style="position: sticky; top:0;">
											<header class="panel-heading panel-heading-red">Clases</header>

											<div class="panel-body">
												<p>&nbsp;</p>
												<ul class="list-group list-group-unbordered">
													<?php
													$consulta = mysqli_query($conexion, "SELECT * FROM academico_clases 
													WHERE cls_id_carga='".$cargaConsultaActual."' AND cls_periodo='".$periodoConsultaActual."' AND  cls_estado=1");
													while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
														$resaltaItem = 'darkblue';
														if($resultado['cls_id']==$_GET["idR"]){$resaltaItem = 'limegreen';}
														
														$tachaItem = '';
														if($resultado['cls_disponible']=='0'){$tachaItem = 'line-through';}
														
														if($resultado['cls_disponible']=='0' and $datosUsuarioActual['uss_tipo']==4){continue;}
													?>
													<li class="list-group-item">
														<a href="clases-ver.php?idR=<?=$resultado['cls_id'];?>" style="color:<?=$resaltaItem;?>; text-decoration:<?=$tachaItem;?>;"><?=$resultado[1];?></a> 
														<div class="profile-desc-item pull-right">&nbsp;</div>
													</li>
													<?php }?>
												</ul>

											</div>
										</div>
									
                                </div>
								
							
                            </div>
                        </div>
                    </div>