<?php
require_once(ROOT_PATH . "/main-app/class/Clases.php");
$idR = "";
if (!empty($_GET["idR"])) {
	$idR = base64_decode($_GET["idR"]);
}
$usuario = 0;
if (!empty($_GET["usuario"])) {
	$usuario = base64_decode($_GET["usuario"]);
}
require_once("../class/Estudiantes.php");

$datosConsultaBD = Clases::traerDatosClases($conexion, $config, $idR);
?>
<link href="../compartido/comentarios.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="page-bar">
	<div class="page-title-breadcrumb">
		<div class="pull-left">
			<div class="page-title"><?= $datosConsultaBD['cls_tema']; ?></div>
			<?php include("../compartido/texto-manual-ayuda.php"); ?>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<?php include("../../config-general/mensajes-informativos.php"); ?>
		<div class="row">

			<div class="col-sm-6  col-lg-3">

				<div class="panel">
					<header class="panel-heading panel-heading-purple">Clases
						<?php if ($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) { ?>
							<a href="clases-agregar.php" class="btn float-right btn-primary"><i class="fa fa-plus"></i></a>
						<?php } ?>
					</header>

					<div class="panel-body">
						<p>&nbsp;</p>
						<ul class="list-group list-group-unbordered">
							<?php
							$consulta = Clases::traerClasesCargaPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
							while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
								$resaltaItem = $Plataforma->colorDos;
								if ($resultado['cls_id'] == $idR) {
									$resaltaItem = $Plataforma->colorUno;
								}

								$tachaItem = '';
								if ($resultado['cls_disponible'] == '0') {
									$tachaItem = 'line-through';
								}

								if ($resultado['cls_disponible'] == '0' and $datosUsuarioActual['uss_tipo'] == 4) {
									continue;
								}
							?>
								<li class="list-group-item">
									<a href="clases-ver.php?idR=<?= base64_encode($resultado['cls_id']); ?>" style="color:<?= $resaltaItem; ?>; text-decoration:<?= $tachaItem; ?>;"><?= $resultado[1]; ?></a>
									<div class="profile-desc-item pull-right">&nbsp;</div>
								</li>
							<?php } ?>
						</ul>

					</div>
				</div>

				<div class="panel">
					<header class="panel-heading panel-heading-blue">Participantes</header>

					<div class="panel-body">
						<p>Este es el listado de los que han entrado a esta clase.</p>
						<ul class="list-group list-group-unbordered">
							<?php
							$urlClase = 'clases-ver.php?idR=' . $_GET["idR"];
							$filtroAdicional = "AND mat_grado='" . $datosCargaActual['car_curso'] . "' AND mat_grupo='" . $datosCargaActual['car_grupo'] . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
							$cursoActual = GradoServicios::consultarCurso($datosCargaActual['car_curso']);
							$consulta = Estudiantes::listarEstudiantesEnGrados($filtroAdicional, "", $cursoActual, $datosCargaActual['car_grupo']);
							
							// PRE-CARGAR TODOS LOS INGRESOS A LA CLASE DE TODOS LOS ESTUDIANTES
							// EN UNA SOLA CONSULTA PARA EVITAR N+1 QUERIES
							// ============================================
							$ingresosMapa = [];
							$idsUsuarios = [];
							$listaEstudiantes = [];
							
							// Guardar estudiantes en array para reutilizar
							while ($est = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
								$listaEstudiantes[] = $est;
								$idsUsuarios[] = $est['uss_id'];
							}
							
							if (!empty($idsUsuarios)) {
								$idsUsuariosEsc = array_map(function($id) use ($conexion) {
									return "'" . mysqli_real_escape_string($conexion, $id) . "'";
								}, $idsUsuarios);
								$inUsuarios = implode(',', $idsUsuariosEsc);
								$urlClaseEsc = mysqli_real_escape_string($conexion, $urlClase);
								$yearEsc = mysqli_real_escape_string($conexion, $_SESSION["bd"]);
								$institucion = (int)$config['conf_id_institucion'];
								
								$sqlIngresos = "SELECT hil_id, hil_usuario, hil_url, hil_titulo, hil_fecha
												FROM " . $baseDatosServicios . ".seguridad_historial_acciones 
												WHERE hil_url LIKE '%" . $urlClaseEsc . "%' 
												AND hil_usuario IN ({$inUsuarios}) 
												AND (hil_fecha LIKE '%" . $yearEsc . "%' OR hil_institucion=" . $institucion . ")
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
								$nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($resultado);
								$ingresoClase = $ingresosMapa[$resultado['uss_id']] ?? null;

								if (empty($ingresoClase['hil_id'])) {
									continue;
								}
							?>
								<li class="list-group-item">
									<a href="clases-ver.php?idR=<?= $_GET["idR"]; ?>&usuario=<?= base64_encode($resultado['mat_id_usuario']); ?>"><?= $nombreCompleto ?></a>
									<div class="profile-desc-item pull-right"><?= $ingresoClase['hil_fecha']; ?></div>
								</li>
							<?php } ?>
						</ul>

						<p align="center"><a href="clases-ver.php?idR=<?= $_GET["idR"]; ?>">VER TODOS</a></p>

					</div>
				</div>


			</div>


			<div class="col-sm-6 col-lg-4">

				<?php
				if (!empty($datosConsultaBD['cls_meeting']) and !empty($datosConsultaBD['cls_clave_docente']) and !empty($datosConsultaBD['cls_clave_estudiante'])) {

					if ($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) {
						$nombreSala = trim($datosCargaActual['mat_nombre']) . "_" . trim($datosCargaActual['gra_nombre']) . "_" . trim($datosCargaActual['gru_nombre']);
				?>

						<input id="meetingID" name="meetingID" value="<?= $datosConsultaBD['cls_meeting']; ?>" type="hidden">
						<input id="moderatorPW" name="moderatorPW" type="hidden" value="<?= $datosConsultaBD['cls_clave_docente']; ?>">
						<input id="attendeePW" name="attendeePW" type="hidden" value="<?= $datosConsultaBD['cls_clave_estudiante']; ?>">
						<input id="meetingName" name="meetingName" type="hidden" value="<?= strtoupper($nombreSala); ?>">
						<input id="username" name="username" type="hidden" value="<?= $datosUsuarioActual['uss_nombre']; ?>">

						<button id="startClass" value="123" class="btn btn-success">Iniciar clase en vivo</button>
						</br>
						<div id="notificacion" class="alert alert-success" style="width: 450px; display: none;" role="alert"></div>

					<?php
					}
					if ($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE) {
					?>

						<input id="meetingID" name="meetingID" value="<?= $datosConsultaBD['cls_meeting']; ?>" type="hidden">
						<input id="attendeePW" name="attendeePW" type="hidden" value="<?= $datosConsultaBD['cls_clave_estudiante']; ?>">
						<input id="username" name="username" type="hidden" value="<?= $datosUsuarioActual['uss_nombre']; ?>">

						<button id="startClassStudent" value="123" class="btn btn-success">Entrar a clase en vivo</button>
						</br>
						<div id="notificacion" class="alert alert-success" style="width: 450px; display: none;" role="alert"></div>

				<?php
					}
				}
				?>

				<div class="card">

					<div class="card-head">
						<header><?= $datosConsultaBD['cls_tema']; ?></header>

						<?php if ($datosUsuarioActual['uss_tipo'] == TIPO_DOCENTE) { ?>
							<button id="panel-p" class="mdl-button mdl-js-button mdl-button--icon pull-right">
								<i class="material-icons">more_vert</i>
							</button>
							<ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" data-mdl-for="panel-p">
								<li class="mdl-menu__item"><a href="clases-editar.php?idR=<?= base64_encode($datosConsultaBD['cls_id']); ?>&carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>"><i class="fa fa-edit"></i>Editar</a></li>
								<li class="mdl-menu__item"><a href="javascript:void(0);" name="clases-eliminar.php?idR=<?= base64_encode($datosConsultaBD['cls_id']); ?>&carga=<?= base64_encode($cargaConsultaActual); ?>&periodo=<?= base64_encode($periodoConsultaActual); ?>" onClick="deseaEliminar(this)"><i class="fa fa-trash"></i>Eliminar</a></li>
							</ul>
						<?php } ?>

					</div>

					<div class="card-body">

						<?php if (!empty($datosConsultaBD['cls_video'])) { ?>
							<p class="iframe-container">
								<iframe width="100%" height="400" src="https://www.youtube.com/embed/<?= $datosConsultaBD['cls_video']; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</p>
						<?php } ?>

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
						<p><?= $datosConsultaBD['cls_descripcion']; ?></p>

						<?php if (!empty($datosConsultaBD['cls_hipervinculo'])) { ?>
							<p><a href="<?= $datosConsultaBD['cls_hipervinculo']; ?>" style="text-decoration: underline;" target="_blank"><?= $datosConsultaBD['cls_hipervinculo']; ?></a></p>
						<?php } ?>

						<?php if (!empty($datosConsultaBD['cls_archivo'])) {
							$nombre1 = $datosConsultaBD['cls_archivo'];
							if (!empty($datosConsultaBD['cls_nombre_archivo1'])) {
								$nombre1 = $datosConsultaBD['cls_nombre_archivo1'];
							}
						?>
							<h4 style="font-weight: bold;">Archivos adjuntos</h4>
							<p><a href="../files/clases/<?= $datosConsultaBD['cls_archivo']; ?>" style="text-decoration: underline;" target="_blank"><?= $nombre1; ?></a></p>
						<?php } ?>

						<?php if (!empty($datosConsultaBD['cls_archivo2'])) {
							$nombre2 = $datosConsultaBD['cls_archivo2'];
							if (!empty($datosConsultaBD['cls_nombre_archivo2'])) {
								$nombre2 = $datosConsultaBD['cls_nombre_archivo2'];
							}
						?>
							<p><a href="../files/clases/<?= $datosConsultaBD['cls_archivo2']; ?>" style="text-decoration: underline;" target="_blank"><?= $nombre2; ?></a></p>
						<?php } ?>

						<?php if (!empty($datosConsultaBD['cls_archivo3'])) {
							$nombre3 = $datosConsultaBD['cls_archivo3'];
							if (!empty($datosConsultaBD['cls_nombre_archivo3'])) {
								$nombre3 = $datosConsultaBD['cls_nombre_archivo3'];
							}
						?>
							<p><a href="../files/clases/<?= $datosConsultaBD['cls_archivo3']; ?>" style="text-decoration: underline;" target="_blank"><?= $nombre3; ?></a></p>
						<?php } ?>
					</div>

				</div>

				<?php 
				// Consultar feedbacks de esta clase
				require_once("../class/UsuariosPadre.php");
				$consultaFeedbacks = mysqli_query($conexion, 
					"SELECT f.*, 
						u.uss_nombre,
						u.uss_nombre2,
						u.uss_apellido1,
						u.uss_apellido2,
						u.uss_foto,
						u.uss_id
					FROM ".BD_ACADEMICA.".clases_feedback f
					INNER JOIN ".BD_GENERAL.".usuarios u ON f.fcls_usuario = u.uss_id 
						AND u.institucion = ".intval($config['conf_id_institucion'])."
						AND u.year = '".mysqli_real_escape_string($conexion, $_SESSION["bd"])."'
					WHERE f.fcls_id_clase = '".mysqli_real_escape_string($conexion, $idR)."'
					AND f.fcls_id_institucion = ".intval($config['conf_id_institucion'])."
					ORDER BY f.fcls_fecha DESC"
				);
				
				$totalFeedbacks = mysqli_num_rows($consultaFeedbacks);
				$sumaEstrellas = 0;
				$promedioEstrellas = 0;
				
				if ($totalFeedbacks > 0) {
					mysqli_data_seek($consultaFeedbacks, 0);
					while ($feedback = mysqli_fetch_array($consultaFeedbacks, MYSQLI_BOTH)) {
						$sumaEstrellas += intval($feedback['fcls_star']);
					}
					$promedioEstrellas = round($sumaEstrellas / $totalFeedbacks, 1);
					mysqli_data_seek($consultaFeedbacks, 0);
				}
				?>
				
				<?php if ($datosUsuarioActual['uss_tipo'] == TIPO_ESTUDIANTE) { ?>
					<div class="card card-box">
						<div class="card-head">
							<header>FEEDBACK</header>
						</div>

						<div class="card-body">

							<div class="alert alert-info" role="alert">
								<h4 class="alert-heading">Ayuda a mejorar!</h4>
								<p>Queremos saber cómo te fue en esta clase. Dejanos un comentario y una valoración. </p>
								<hr>
								<p class="mb-0">Recuerda que si ya has dejado una valoración previa, esta se actualizará si envias otra.</p>
							</div>

							<div id="feedbackPanel">
								<?php
								// Verificar si el estudiante ya envió feedback para esta clase
								$feedbackExistente = mysqli_query($conexion, 
									"SELECT fcls_comentario, fcls_star, fcls_fecha 
									FROM ".BD_ACADEMICA.".clases_feedback 
									WHERE fcls_id_clase = '".mysqli_real_escape_string($conexion, $idR)."'
									AND fcls_id_institucion = ".intval($config['conf_id_institucion'])."
									AND fcls_usuario = '".mysqli_real_escape_string($conexion, $datosUsuarioActual['uss_id'])."'
									LIMIT 1"
								);
								$yaTieneFeedback = mysqli_num_rows($feedbackExistente) > 0;
								$feedbackData = null;
								if ($yaTieneFeedback) {
									$feedbackData = mysqli_fetch_array($feedbackExistente, MYSQLI_BOTH);
								}
								?>
								<div class="form-group row">
									<div class="col-sm-12">
										<textarea id="feedbackContent" name="feedbackContent" class="form-control" rows="3" placeholder="Dejanos tu opinión sobre este tema" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"><?= $feedbackData ? htmlspecialchars($feedbackData['fcls_comentario']) : ''; ?></textarea>
										<?php if ($yaTieneFeedback) { ?>
										<small style="color: #95a5a6; margin-top: 5px; display: block;">
											<i class="fa fa-info-circle"></i> Ya has enviado feedback anteriormente. Puedes actualizarlo.
										</small>
										<?php } ?>
									</div>
								</div>

								<div class="d-flex justify-content-center">
									<span class="rating">
										<?php 
										$starSeleccionada = $feedbackData ? intval($feedbackData['fcls_star']) : 0;
										// Mostrar estrellas de 5 a 1 (de izquierda a derecha)
										for ($i = 5; $i >= 1; $i--) {
											$checked = ($i <= $starSeleccionada && $starSeleccionada > 0) ? ' checked' : '';
											$starId = 'star-' . $i;
										?>
										<span class="star<?=$checked;?>" 
										      id="<?=$starId;?>" 
										      onclick="feedbackSend(this, <?=$i;?>)" 
										      data-star="<?=$i;?>"
										      style="cursor: pointer;"
										      title="<?=$i;?> <?=$i == 1 ? 'estrella' : 'estrellas';?>"></span>
										<?php } ?>
									</span>
								</div>
								<?php if ($yaTieneFeedback) { ?>
								<div style="text-align: center; margin-top: 10px; color: #95a5a6; font-size: 12px;">
									<small>Última actualización: <?= date('d/m/Y H:i', strtotime($feedbackData['fcls_fecha'])); ?></small>
								</div>
								<?php } ?>
							</div>


						</div>

					</div>
				<?php } ?>
				
				<!-- Sección de Feedback de Estudiantes -->
				<div class="card card-box" style="margin-top: 20px;">
					<div class="card-head">
						<header>
							<i class="fa fa-star"></i> VALORACIONES DE ESTUDIANTES
							<?php if ($totalFeedbacks > 0) { ?>
							<span class="pull-right" style="font-size: 14px; font-weight: normal;">
								<span id="promedio-estrellas"><?=$promedioEstrellas;?></span>
								<i class="fa fa-star" style="color: #f39c12;"></i>
								(<?=$totalFeedbacks;?> <?=$totalFeedbacks == 1 ? 'valoración' : 'valoraciones';?>)
							</span>
							<?php } ?>
						</header>
					</div>
					
					<div class="card-body">
						<?php if ($totalFeedbacks > 0) { ?>
							<!-- Promedio destacado -->
							<div class="feedback-summary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; margin-bottom: 20px; color: white; text-align: center;">
								<div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">PROMEDIO DE VALORACIÓN</div>
								<div style="font-size: 48px; font-weight: 700; margin-bottom: 8px;"><?=$promedioEstrellas;?></div>
								<div class="rating-display-large" style="display: flex; justify-content: center; gap: 5px;">
									<?php 
									for ($i = 1; $i <= 5; $i++) {
										$starClass = ($i <= round($promedioEstrellas)) ? 'fa-star' : 'fa-star-o';
										$starColor = ($i <= $promedioEstrellas) ? '#f39c12' : 'rgba(255,255,255,0.3)';
										echo '<i class="fa '.$starClass.'" style="color: '.$starColor.'; font-size: 24px;"></i>';
									}
									?>
								</div>
								<div style="font-size: 12px; opacity: 0.8; margin-top: 10px;">Basado en <?=$totalFeedbacks;?> <?=$totalFeedbacks == 1 ? 'valoración' : 'valoraciones';?></div>
							</div>
							
							<!-- Lista de comentarios -->
							<div class="feedback-list" style="max-height: 500px; overflow-y: auto;">
								<?php 
								$contF = 0;
								while ($feedback = mysqli_fetch_array($consultaFeedbacks, MYSQLI_BOTH)) {
									$contF++;
									// Usar la función existente para obtener el nombre completo
									$nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($feedback);
									if (empty($nombreUsuario)) $nombreUsuario = 'Usuario';
									$comentario = $feedback['fcls_comentario'];
									$estrellas = intval($feedback['fcls_star']);
									$fecha = date('d/m/Y H:i', strtotime($feedback['fcls_fecha']));
									$fotoUsuario = !empty($feedback['uss_foto']) ? '../files/usuarios/'.$feedback['uss_foto'] : '../files/usuarios/default.png';
								?>
								<div class="feedback-item" style="border-bottom: 1px solid #e0e6ed; padding: 15px 0; display: flex; gap: 15px;">
									<div style="flex-shrink: 0;">
										<img src="<?=$fotoUsuario;?>" alt="<?=$nombreUsuario;?>" 
											style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #e0e6ed; background: #f0f0f0;"
											onerror="if(this.src.indexOf('default.png') === -1) { this.src='../files/usuarios/default.png'; this.onerror=null; } else { this.onerror=null; this.style.display='none'; }">
									</div>
									<div style="flex: 1;">
										<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
											<div>
												<strong style="color: #2d3e50; font-size: 14px;"><?=htmlspecialchars($nombreUsuario);?></strong>
												<div class="rating-display" style="margin-top: 4px;">
													<?php 
													for ($i = 1; $i <= 5; $i++) {
														$starClass = ($i <= $estrellas) ? 'fa-star' : 'fa-star-o';
														$starColor = ($i <= $estrellas) ? '#f39c12' : '#ddd';
														echo '<i class="fa '.$starClass.'" style="color: '.$starColor.'; font-size: 12px;"></i>';
													}
													?>
												</div>
											</div>
											<span style="font-size: 11px; color: #95a5a6;"><?=$fecha;?></span>
										</div>
										<?php if (!empty($comentario)) { ?>
										<div style="color: #555; font-size: 13px; line-height: 1.5; margin-top: 8px;">
											<?=nl2br(htmlspecialchars($comentario));?>
										</div>
										<?php } ?>
									</div>
								</div>
								<?php } ?>
							</div>
						<?php } else { ?>
							<div style="text-align: center; padding: 40px; color: #95a5a6;">
								<i class="fa fa-star-o" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
								<p style="margin: 0;">Aún no hay valoraciones para esta clase.</p>
								<p style="margin: 5px 0 0 0; font-size: 12px;">Sé el primero en dejar tu feedback.</p>
							</div>
						<?php } ?>
					</div>
				</div>


			</div>




			<div class="col-sm-12  col-lg-5">
				<div class="card card-box">

					<div class="card-head">
						<header>COMENTARIOS</header>
					</div>

					<div class="card-body">
						<form class="form-horizontal" action="#" method="post">
							<input type="hidden" name="id" value="14">
							<input type="hidden" name="idClase" value="<?= $idR; ?>">
							<input type="hidden" name="sesionUsuario" value="<?= $_SESSION["id"]; ?>">
							<input type="hidden" name="agnoConsulta" value="<?= $_SESSION["bd"]; ?>">

							<input type="hidden" name="envia" id="envia">

							<div class="form-group row">
								<div class="col-sm-12">
									<textarea id="contenido" name="contenido" class="form-control" rows="3" placeholder="Escribe aquí una pregunta o comentario para este tema..." style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" required></textarea>
								</div>
							</div>

							<div class="form-group">
								<div class="offset-md-3 col-md-9">
									<button id="btnEnviar" class="btn btn-info" onclick="this.disabled=true;guardar()">Enviar</button>

									<button type="reset" class="btn btn-default"><?= $frases[171][$datosUsuarioActual['uss_idioma']]; ?></button>
								</div>
							</div>
						</form>

					</div>
				</div>
				<div class="col-12">
					<div style="margin-bottom: 15px; text-align: right;">
						<button type="button" class="btn btn-sm btn-info" onclick="refrescarComentarios()" id="btnRefrescarComentarios" title="Actualizar comentarios">
							<i class="fa fa-refresh"></i> Actualizar Comentarios
						</button>
					</div>
					<div style="max-height: 700px; overflow-y: auto;">
						<ul class="comments-list animate__animated animate__flipInX" id="lista-preguntas">




						</ul>
					</div>
				</div>
			</div>

			<script>
				var cantidadActual = 0.1;
				var consultarPreguntasEnEjecucion = false; // Bandera para prevenir ejecuciones múltiples
				var consultarPreguntasEjecutado = false; // Bandera para prevenir múltiples ejecuciones al cargar
				
				async function contarPreguntas() {
					var url = "../compartido/clases-contar-comentarios.php";
					var data = {
						"idClase": '<?= $idR; ?>'
					};
					resultado = await metodoFetchAsync(url, data, 'json', false);
					resultData = resultado["data"];
					if (resultData["ok"]) {
						var cantidadConsulta=parseInt(resultData["cantidad"]);
						if (cantidadActual == 0.1) {
							cantidadActual = parseInt(resultData["cantidad"]);
						} else if (cantidadConsulta>cantidadActual) {
							if(!document.getElementById("reg"+resultData["codigo"])){
								await mostrarPregunta(resultData);
								$.toast({
								heading: 'Nuevo Comentario',
								text: 'Nuevo Comentrario',
								position: 'bottom-right',
								showHideTransition: 'slide',
								loaderBg: '#26c281',
								icon: "success",
								hideAfter: 5000,
								stack: 6
							});
								cantidadActual = parseInt(resultData["cantidad"]);
							}
							
						}



					}
				}

				function feedbackSend(element, starValue) {
					// Obtener estrella desde el parámetro o desde el atributo
					var star = starValue;
					if (!star && element) {
						star = element.getAttribute('data-star');
						if (!star) {
							var starSplit = element.id ? element.id.split('-') : [];
							star = starSplit.length > 1 ? starSplit[1] : null;
						}
					}
					
					var panel = document.getElementById("feedbackPanel");
					var comment = document.getElementById("feedbackContent");
					var claseId = '<?= $idR; ?>';
					var usuarioActual = '<?= $datosUsuarioActual['uss_id']; ?>';

					// Validar que se haya seleccionado una estrella
					if (!star || star === 'undefined' || star === null || star === '') {
						console.error('❌ No se pudo determinar la estrella seleccionada', {
							element: element,
							starValue: starValue,
							star: star
						});
						$.toast({
							heading: 'Error',
							text: 'Por favor selecciona una valoración con estrellas',
							position: 'bottom-right',
							showHideTransition: 'slide',
							loaderBg: '#f4516c',
							icon: 'error',
							hideAfter: 5000,
							stack: 6
						});
						return;
					}

					// Convertir a entero
					star = parseInt(star);
					if (isNaN(star) || star < 1 || star > 5) {
						console.error('❌ Valor de estrella inválido:', star);
						$.toast({
							heading: 'Error',
							text: 'La valoración debe estar entre 1 y 5 estrellas',
							position: 'bottom-right',
							showHideTransition: 'slide',
							loaderBg: '#f4516c',
							icon: 'error',
							hideAfter: 5000,
							stack: 6
						});
						return;
					}

					// Validar que los elementos existan
					if (!comment) {
						console.error('❌ No se encontró el textarea de comentarios');
						$.toast({
							heading: 'Error',
							text: 'Error al acceder al formulario',
							position: 'bottom-right',
							showHideTransition: 'slide',
							loaderBg: '#f4516c',
							icon: 'error',
							hideAfter: 5000,
							stack: 6
						});
						return;
					}

					// Validar que la clase y usuario existan
					if (!claseId || !usuarioActual) {
						console.error('❌ Faltan parámetros:', { claseId: claseId, usuarioActual: usuarioActual });
						$.toast({
							heading: 'Error',
							text: 'Error en los parámetros de la clase',
							position: 'bottom-right',
							showHideTransition: 'slide',
							loaderBg: '#f4516c',
							icon: 'error',
							hideAfter: 5000,
							stack: 6
						});
						return;
					}

					// Obtener el valor del comentario directamente
					var comentarioTexto = comment.value || '';
					console.log('🔍 Comentario antes de enviar:', {
						valor: comentarioTexto,
						tipo: typeof comentarioTexto,
						longitud: comentarioTexto.length,
						primeros_50: comentarioTexto.substring(0, 50)
					});
					
					// Preparar datos como string URL-encoded para asegurar que se envíen correctamente
					var datosEnvio = "claseId=" + encodeURIComponent(claseId.toString()) +
						"&usuarioActual=" + encodeURIComponent(usuarioActual.toString()) +
						"&comment=" + encodeURIComponent(comentarioTexto) +
						"&star=" + encodeURIComponent(star.toString());

					console.log('🔵 String de datos completo:', datosEnvio);
					console.log('🔵 Datos decodificados:', {
						claseId: claseId,
						usuarioActual: usuarioActual,
						comment: comentarioTexto,
						star: star
					});

					$.ajax({
						type: "POST",
						url: "../compartido/ajax-feedback.php",
						data: datosEnvio,
						contentType: "application/x-www-form-urlencoded; charset=UTF-8",
						dataType: 'json',
						success: function(response) {
							console.log('📥 Respuesta recibida:', response);
							try {
								// Si ya viene como objeto JSON (por dataType: 'json'), no necesita parse
								var data = typeof response === 'string' ? JSON.parse(response) : response;
								console.log('✅ Feedback procesado:', data);
								
								// Mostrar mensaje
								$.toast({
									heading: data.titulo || 'Resultado',
									text: data.mensaje || 'Operación completada',
									position: 'bottom-right',
									showHideTransition: 'slide',
									loaderBg: data.estado === 'success' ? '#26c281' : '#f4516c',
									icon: data.estado || 'info',
									hideAfter: 5000,
									stack: 6
								});

								// Solo ocultar panel y actualizar si fue exitoso
								if (data.estado === 'success') {
									// Limpiar formulario
									if (comment) comment.value = "";
									
									// Ocultar panel
									if (panel) panel.style.display = "none";
									
									// Recargar la página para mostrar el nuevo feedback y actualizar el promedio
									setTimeout(() => {
										//window.location.reload();
									}, 1000);
								}
							} catch (e) {
								console.error('❌ Error parseando respuesta:', e, response);
								$.toast({
									heading: 'Error',
									text: 'Error al procesar la respuesta del servidor',
									position: 'bottom-right',
									showHideTransition: 'slide',
									loaderBg: '#f4516c',
									icon: 'error',
									hideAfter: 5000,
									stack: 6
								});
							}
						},
						error: function(xhr, status, error) {
							console.error('❌ Error en la petición AJAX:', {
								status: status,
								error: error,
								responseText: xhr.responseText
							});
							$.toast({
								heading: 'Error',
								text: 'Error al enviar el feedback. Por favor intenta nuevamente.',
								position: 'bottom-right',
								showHideTransition: 'slide',
								loaderBg: '#f4516c',
								icon: 'error',
								hideAfter: 5000,
								stack: 6
							});
						}
					});

				}

				// Deshabilitado: Intervalo que causaba problemas de rendimiento y recargas innecesarias
				// setInterval('contarPreguntas()', 10000);

				// Ejecutar consulta de preguntas cuando el DOM esté completamente cargado
				// Solo ejecutar una vez al cargar la página
				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', function() {
						if (!consultarPreguntasEjecutado) {
							consultarPreguntas();
						}
					});
				} else {
					// El DOM ya está cargado
					if (!consultarPreguntasEjecutado) {
						consultarPreguntas();
					}
				}
				
				// Función para refrescar comentarios manualmente
				window.refrescarComentarios = function() {
					var btn = document.getElementById('btnRefrescarComentarios');
					if (btn) {
						btn.disabled = true;
						btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Actualizando...';
					}
					
					consultarPreguntasEjecutado = false;
					consultarPreguntasEnEjecucion = false;
					
					consultarPreguntas().finally(function() {
						if (btn) {
							btn.disabled = false;
							btn.innerHTML = '<i class="fa fa-refresh"></i> Actualizar Comentarios';
						}
					});
				}

				async function guardar(idPadre) {
					console.log('💬 Guardando comentario...', { idPadre: idPadre });

					idClase = '<?= $idR; ?>';
					indice = 0;
					nivel = 0;
					sesionUsuario = '<?= $_SESSION["id"]; ?>';

					id = '';
					if (idPadre === undefined || idPadre === null) {
						idPadre = null;
						btn = document.getElementById("btnEnviar");
						contenido = document.getElementById("contenido");
					} else {
						btn = document.getElementById("btnEnviar-" + idPadre);
						contenido = document.getElementById("respuesta-" + idPadre);
						idNivel = btoa(idPadre);
						var nivelElement = document.getElementById("nivel" + idNivel);
						if (nivelElement) {
							nivel = nivelElement.value || 0;
						} else {
							nivel = 0;
						}
					}
					
					if (validar(contenido.value)) {
						var data = {
							"idClase": idClase,
							"idPadre": idPadre,
							"sesionUsuario": sesionUsuario,
							"contenido": contenido.value,
							"nivel": nivel
						};

						console.log('📤 Enviando datos:', data);

						var url = "../compartido/clases-guardar-comentarios.php";
						resultado = await metodoFetchAsync(url, data, 'json', false);
						data = resultado["data"];
						
						console.log('📥 Respuesta recibida:', data);
						
						if (data["ok"]) {
							// ✅ Mostrar pregunta inmediatamente después de guardar
							await mostrarPregunta(data);
							
							// ✅ Actualizar cantidad actual
							if (data["cantidad"] !== undefined) {
								cantidadActual = parseInt(data["cantidad"]);
							}
							
							$.toast({
								heading: 'Acción realizada',
								text: data["msg"],
								position: 'bottom-right',
								showHideTransition: 'slide',
								loaderBg: '#26c281',
								icon: "success",
								hideAfter: 5000,
								stack: 6
							});
							btn.disabled = false;
							contenido.value = "";
							
							console.log('✅ Comentario guardado y mostrado correctamente');
						} else {
							console.error('❌ Error al guardar:', data["msg"]);
							btn.disabled = false;
							$.toast({
								heading: 'Error',
								text: data["msg"] || 'Error al guardar el comentario',
								position: 'bottom-right',
								showHideTransition: 'slide',
								loaderBg: '#f4516c',
								icon: "error",
								hideAfter: 5000,
								stack: 6
							});
						}
					} else {
						btn.disabled = false;
						console.warn('⚠️ Validación fallida: contenido vacío');
					}

				};

				function validar(contenido) {
					if (contenido == null || contenido.length == 0 || /^\s+$/.test(contenido)) {
						return false;
					} else {
						return true;
					}
				}

				async function mostrarPregunta(dato) {
					console.log('📝 Mostrando pregunta:', dato);
					
					idPregunta = dato["codigo"];
					cantidad = dato["cantidad"];
					idPadre = dato["padre"];
					nivel = dato["nivel"] || 0;
					
					var data = {
						"claseId": '<?= $idR; ?>',
						"idPregunta": idPregunta,
						"usuarioActual": '<?= $datosUsuarioActual['uss_id']; ?>',
						"usuarioDocente": '<?= $datosCargaActual['car_docente']; ?>',
						"nivel": nivel
					};

					var url = "../compartido/clase-comentario.php";
					resultado = await metodoFetchAsync(url, data, 'html', false);

					if (idPadre == undefined || idPadre == null) {
						lista = document.getElementById("lista-preguntas");
					} else {
						lista = document.getElementById("lista-respuesta-" + idPadre);
					}

					if (!lista) {
						console.error('❌ No se encontró la lista para insertar el comentario');
						return;
					}

					// ✅ Insertar al inicio de la lista
					var primerElemento = lista.firstChild;
					var nuevoElemento = document.createElement("li");
					// Limpiar scripts del HTML antes de insertarlo
					var contenidoHtml = resultado["data"];
					contenidoHtml = contenidoHtml.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
					nuevoElemento.innerHTML = contenidoHtml;
					
					if (primerElemento) {
						lista.insertBefore(nuevoElemento, primerElemento);
					} else {
						lista.appendChild(nuevoElemento);
					}
					
					console.log('✅ Comentario insertado en la lista');
					
					// esto sucede cuando es una respuesta
					if (idPadre != undefined && idPadre != null) {
						respuesta = document.getElementById("cantidad-respuestas-" + idPadre);
						if (respuesta) {
							respuesta.innerText = cantidad + " Respuestas ";
							var icon = document.createElement('i');
							icon.classList.add('fa', 'fa-comments-o');
							respuesta.appendChild(icon);
						}
						var miDiv = document.getElementById("div-respuesta-" + idPadre);
						if (miDiv) {
							miDiv.classList.remove('show');
						}
						lista.classList.add('show');
					} else {
						if (cantidad !== undefined) {
							cantidadActual = parseInt(cantidad);
							console.log('📊 Cantidad actualizada a:', cantidadActual);
						}
					}
				}

				function eliminarAnimacion(id) {
					var pregunta = document.getElementById(id);
					pregunta.classList = [];
				}


				async function consultarPreguntas() {
					// Prevenir ejecuciones múltiples
					if (consultarPreguntasEnEjecucion) {
						console.warn('⚠️ consultarPreguntas ya está en ejecución, ignorando llamada duplicada');
						return;
					}
					
					if (consultarPreguntasEjecutado) {
						console.warn('⚠️ consultarPreguntas ya fue ejecutado, ignorando llamada duplicada');
						return;
					}
					
					consultarPreguntasEnEjecucion = true;
					
					try {
						console.log('🔍 Consultando preguntas/comentarios...');
						
						var data = {
							"claseId": '<?= $idR; ?>',
							"usuarioActual": '<?= $datosUsuarioActual['uss_id']; ?>',
							"usuarioDocente": '<?= $datosCargaActual['car_docente']; ?>'
						};

						var url = "../compartido/ajax-comentarios-preguntas.php";
						
						// Agregar timeout para evitar que se quede colgado
						var resultado = await Promise.race([
							metodoFetchAsync(url, data, 'html', false),
							new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout después de 10 segundos')), 10000))
						]);
						
						// Deshabilitado: contarPreguntas() solo se ejecuta en el load inicial
						// contarPreguntas();
						
						var lista = document.getElementById("lista-preguntas");
						if (lista) {
							// ✅ Reemplazar contenido con datos frescos del servidor
							if (resultado && resultado["data"]) {
								// Limpiar cualquier script que pueda estar causando bucles
								var contenidoHtml = resultado["data"];
								// Remover scripts que puedan estar causando problemas
								contenidoHtml = contenidoHtml.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
								lista.innerHTML = contenidoHtml;
								console.log('✅ Comentarios cargados:', lista.children.length);
							} else {
								console.warn('⚠️ Respuesta sin datos, pero continuando...');
								lista.innerHTML = '<li style="padding: 20px; text-align: center; color: #95a5a6;">No hay comentarios aún.</li>';
							}
						} else {
							console.warn('⚠️ No se encontró la lista de preguntas, pero continuando...');
						}
						
						// Marcar como ejecutado solo si fue exitoso
						consultarPreguntasEjecutado = true;
					} catch (error) {
						console.error('❌ Error al consultar preguntas:', error);
						var lista = document.getElementById("lista-preguntas");
						if (lista) {
							lista.innerHTML = '<li style="padding: 20px; text-align: center; color: #f4516c;">Error al cargar comentarios. Por favor recarga la página.</li>';
						}
					} finally {
						consultarPreguntasEnEjecucion = false;
					}
				}
			</script>

		</div>
	</div>
</div>