<?php
// Asegurar que la foto de perfil del usuario actual esté siempre definida para este módulo
if (!isset($fotoPerfilUsr)) {
    if (!class_exists('UsuariosFunciones')) {
        require_once ROOT_PATH . "/main-app/compartido/sintia-funciones.php";
    }
    $fotoPerfilUsr = UsuariosFunciones::verificarFoto($datosUsuarioActual['uss_foto'] ?? '');
}
?>
<div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"><?=$frases[100][$datosUsuarioActual['uss_idioma']];?></div>
								<?php include("../compartido/texto-manual-ayuda.php");?>
                            </div>
                        </div>
                    </div>
	
	
                   <!-- start widget --
					<div class="state-overview">
							<div class="row">
						        <div class="col-xl-3 col-md-6 col-12">
						          <div class="info-box bg-b-green">
						            <span class="info-box-icon push-bottom"><i class="material-icons">group</i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Total Estudiantes</span>
						              <span class="info-box-number">1.250</span>
						              <div class="progress">
						                <div class="progress-bar" style="width: 45%"></div>
						              </div>
						              <span class="progress-description">
						                    45% de incremento en 28 días
						                  </span>
						            </div>
						            <!-- /.info-box-content --
						          </div>
						          <!-- /.info-box --
						        </div>
						        <!-- /.col --
						        <div class="col-xl-3 col-md-6 col-12">
						          <div class="info-box bg-b-yellow">
						            <span class="info-box-icon push-bottom"><i class="material-icons">person</i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Nuevos Estudiantes</span>
						              <span class="info-box-number">155</span>
						              <div class="progress">
						                <div class="progress-bar" style="width: 40%"></div>
						              </div>
						              <span class="progress-description">
						                    40% de incremento en 28 días
						                  </span>
						            </div>
						            <!-- /.info-box-content --
						          </div>
						          <!-- /.info-box --
						        </div>
						        <!-- /.col --
						        <div class="col-xl-3 col-md-6 col-12">
						          <div class="info-box bg-b-blue">
						            <span class="info-box-icon push-bottom"><i class="material-icons">school</i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Cursos nuevos</span>
						              <span class="info-box-number">52</span>
						              <div class="progress">
						                <div class="progress-bar" style="width: 85%"></div>
						              </div>
						              <span class="progress-description">
						                    85% de incremento en 28 días
						                  </span>
						            </div>
						            <!-- /.info-box-content --
						          </div>
						          <!-- /.info-box --
						        </div>
						        <!-- /.col --
						        <div class="col-xl-3 col-md-6 col-12">
						          <div class="info-box bg-b-pink">
						            <span class="info-box-icon push-bottom"><i class="material-icons">monetization_on</i></span>
						            <div class="info-box-content">
						              <span class="info-box-text">Ingresos</span>
						              <span class="info-box-number">130.921.000</span><span>$</span>
						              <div class="progress">
						                <div class="progress-bar" style="width: 50%"></div>
						              </div>
						              <span class="progress-description">
						                    50% de incremento en 28 días
						                  </span>
						            </div>
						            <!-- /.info-box-content --
						          </div>
						          <!-- /.info-box --
						        </div>
						        <!-- /.col --
						      </div>
						</div>
					<!-- end widget -->
                     
			        <div class="row">
						
						<div class="col-sm-5">				
							
							<?php include("../compartido/encuestas.php");?>
							
							<!--
							<div class="panel" data-hint="Envíanos tu sugerencia para la plataforma. Ayudanos a mejorar.">
								<header class="panel-heading panel-heading-red">Tu opinión o sugerencia sobre la plataforma SINTIA es muy importante</header>
                                <div class="panel-body">
									<p style="text-align: justify;"><mark>Esta opción NO es para comunicarse con la Institución. Los mensajes aquí enviados llegarán solamente a los proveedores de la plataforma SINTIA.</mark></p>
									<?php if(isset($_GET["msg"]) and $_GET["msg"]==1){?>
										<p class="text-success">
											<i class="fa fa-thumbs-up"></i>
											Muchas gracias por enviar tu opinión! La tendremos en cuenta para seguir mejorando.
										</p>
									<?php }else{?>
									<form class="form-horizontal" action="../compartido/enviar-sugerencias-sintia.php" method="post">
										<input type="hidden" name="usuario" value="<?=$datosUsuarioActual['uss_nombre'];?>">
										<input type="hidden" name="tipoUsuario" value="<?=$datosUsuarioActual['uss_tipo'];?>">
										
										<input type="hidden" id="usuarioSug" value="<?=$datosUsuarioActual['uss_id'];?>">
										<input type="hidden" id="usuarioNombreSug" value="<?=$datosUsuarioActual['uss_nombre'];?>">
										<input type="hidden" id="tipoUsuarioSug" value="<?=$datosUsuarioActual['uss_tipo'];?>">
										
										<div class="form-group row">
											<div class="col-sm-12">
												<textarea id="contenidoSugerencia" name="contenido" class="form-control" rows="3" placeholder="¿Cuál es tu opinión o sugerencia sobre la plataforma SINTIA?" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
											</div>
										</div>

										<div class="form-group">
											<div class="offset-md-3 col-md-9">
												<button type="button" onClick="insertarFirebase()" class="btn btn-info">Enviar ahora</button>
												<button type="reset" class="btn btn-default"><?=$frases[171][$datosUsuarioActual['uss_idioma']];?></button>
											</div>
										</div>
									</form>
									<?php }?>
								</div>
							</div>
							-->

						<nav>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true" onClick="listarInformacion('../compartido/async-ultimas-paginas.php?fotoPerfilUsr=<?=$fotoPerfilUsr;?>', 'nav-home')">Mis páginas recientes</a>

								<a class="nav-item nav-link" id="nav-cargas-tab" data-toggle="tab" href="#nav-cargas" role="tab" aria-controls="nav-cargas" aria-selected="false" onClick="listarInformacion('../compartido/async-mas-visitadas-pagina.php', 'nav-cargas')">Mis páginas más visitadas</a>
								<?php
								if($datosUsuarioActual['uss_tipo'] == TIPO_DEV || $datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO) {
								?>
									<a class="nav-item nav-link" id="peso-tab" data-toggle="tab" href="#peso" role="tab" aria-controls="peso" aria-selected="false">Uso del disco</a>
								<?php }?>
								
							</div>
						</nav>

						<div class="tab-content" id="nav-tabContent">
							<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"></div>
							<div class="tab-pane fade" id="nav-cargas" role="tabpanel" aria-labelledby="nav-profile-tab"></div>
							<div class="tab-pane fade" id="peso" role="tabpanel" aria-labelledby="peso-tab">
								<?php include("../compartido/peso.php");?>
							</div>
						</div>

						<script>
							document.addEventListener('DOMContentLoaded', function() {
								listarInformacion('../compartido/async-ultimas-paginas.php?fotoPerfilUsr=<?=$fotoPerfilUsr;?>', 'nav-home');
							});
						</script>
							
							

								
							<?php include("../compartido/modulo-frases-lateral.php");?>
							
						</div>	
						
						<!-- Activity feed start -->
						<div class="col-sm-7" data-hint="Este es tu asistente personal de actividades. Él te ayudará a decidir por donde empezar a hacer las tareas.">
							<?php if($datosUsuarioActual['uss_tipo']==TIPO_DOCENTE or $datosUsuarioActual['uss_tipo']==TIPO_DIRECTIVO || $datosUsuarioActual['uss_tipo']==TIPO_DEV){?>
								<?php include("../compartido/progreso-docentes.php");?>
							<?php }?>

						<!--
							<div class="card-box">
								<div class="card-head">
									<header>Contenido variado</header>
								</div>
								<div class="card-body">

									<ul class="feed-blog" id="listarDatos"></ul>
									
									
									<ul class="feed-blog">
										
										<li class="diactive-feed">
											<div class="feed-user-img">
												<img src="../../config-general/assets/img/std/std2.jpg" class="img-radius "
													alt="User-Profile-Image">
											</div>
											<h6>
												<span class="label label-sm label-success">Tarea </span> Lorem</span>
												<small class="text-muted">Hace 5 horas</small>
											</h6>
										</li>
										
									</ul>
									
								</div>
							</div>
							-->

						</div>	
						<!-- Activity feed end -->
					</div>
					

                </div>