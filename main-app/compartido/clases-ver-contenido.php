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
							$contReg = 1;
							while ($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
								$nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($resultado);
								$consultaIngresoClase = mysqli_query($conexion, "SELECT hil_id, hil_usuario, hil_url, hil_titulo, hil_fecha
												FROM " . $baseDatosServicios . ".seguridad_historial_acciones 
												WHERE hil_url LIKE '%" . $urlClase . "%' AND hil_usuario='" . $resultado['uss_id'] . "' AND hil_fecha LIKE '%" . $_SESSION["bd"] . "%'
												UNION 
												SELECT hil_id, hil_usuario, hil_url, hil_titulo, hil_fecha 
												FROM " . $baseDatosServicios . ".seguridad_historial_acciones 
												WHERE hil_url LIKE '%" . $urlClase . "%' AND hil_usuario='" . $resultado['uss_id'] . "' AND hil_institucion='" . $config['conf_id_institucion'] . "' AND hil_fecha LIKE '%" . $_SESSION["bd"] . "%'");
								$ingresoClase = mysqli_fetch_array($consultaIngresoClase, MYSQLI_BOTH);

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
								<div class="form-group row">
									<div class="col-sm-12">
										<textarea id="feedbackContent" name="feedbackContent" class="form-control" rows="3" placeholder="Dejanos tu opinión sobre este tema" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;"></textarea>
									</div>
								</div>

								<div class="d-flex justify-content-center">
									<span class="rating">
										<span class="star" id="star-5" onClick="feedbackSend(this)"></span>
										<span class="star" id="star-4" onClick="feedbackSend(this)"></span>
										<span class="star" id="star-3" onClick="feedbackSend(this)"></span>
										<span class="star" id="star-2" onClick="feedbackSend(this)"></span>
										<span class="star" id="star-1" onClick="feedbackSend(this)"></span>
									</span>
								</div>
							</div>


						</div>

					</div>
				<?php } ?>


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
				<div class="col-12" style="max-height: 700px; overflow-y: auto;">
					<div>

						<ul class="comments-list animate__animated animate__flipInX" id="lista-preguntas">




						</ul>
					</div>
				</div>
			</div>

			<script>
				var cantidadActual = 0.1;
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

				function feedbackSend(data) {
					var starSplit = data.id.split('-');
					var star = starSplit[1];
					var panel = document.getElementById("feedbackPanel");
					var comment = document.getElementById("feedbackContent");
					var claseId = '<?= $idR; ?>';
					var usuarioActual = '<?= $datosUsuarioActual['uss_id']; ?>';

					datos = "claseId=" + claseId +
						"&usuarioActual=" + usuarioActual +
						"&comment=" + comment.value +
						"&star=" + star;

					$.ajax({
						type: "POST",
						url: "../compartido/ajax-feedback.php",
						data: datos,
						success: function(response) {
							data = JSON.parse(response);
							comment.value = "";
							console.log(data, response);
							$.toast({
								heading: data.titulo,
								text: data.mensaje,
								position: 'bottom-right',
								showHideTransition: 'slide',
								loaderBg: '#26c281',
								icon: data.estado,
								hideAfter: 5000,
								stack: 6
							});
							panel.style.display = "none";
						}
					});

				}

				setInterval('contarPreguntas()', 10000);

				window.onload = consultarPreguntas();

				async function guardar(idPadre) {

					idClase = '<?= $idR; ?>';
					indice = 0;
					nivel = 0;
					sesionUsuario = '<?= $_SESSION["id"]; ?>';


					id = '';
					if (idPadre === undefined) {
						idPadre = null;
						btn = document.getElementById("btnEnviar");
						contenido = document.getElementById("contenido");
					} else {
						btn = document.getElementById("btnEnviar-" + idPadre);
						contenido = document.getElementById("respuesta-" + idPadre);
						idNivel = btoa(idPadre);
						nivel = document.getElementById("nivel" + idNivel).value;

					}
					if (validar(contenido.value)) {
						var data = {
							"idClase": idClase,
							"idPadre": idPadre,
							"sesionUsuario": sesionUsuario,
							"contenido": contenido.value,
							"nivel": nivel
						};

						var url = "../compartido/clases-guardar-comentarios.php";
						resultado = await metodoFetchAsync(url, data, 'json', false);
						data = resultado["data"];
						if (data["ok"]) {
							mostrarPregunta(data);
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
						}
					} else {
						btn.disabled = false;
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
					idPregunta = dato["codigo"];
					cantidad = dato["cantidad"];
					idPadre = dato["padre"];
					nivel = dato["nivel"];
					var data = {
						"claseId": '<?= $idR; ?>',
						"idPregunta": idPregunta,
						"usuarioActual": '<?= $datosUsuarioActual['uss_id']; ?>',
						"usuarioDocente": '<?= $datosCargaActual['car_docente']; ?>',
						"nivel": nivel
					};

					var url = "../compartido/clase-comentario.php";
					resultado = await metodoFetchAsync(url, data, 'html', false);

					if (idPadre == undefined) {
						lista = document.getElementById("lista-preguntas");
					} else {
						lista = document.getElementById("lista-respuesta-" + idPadre);
					}


					var primerElemento = lista.firstChild;
					var nuevoElemento = document.createElement("li");
					nuevoElemento.innerHTML = resultado["data"];
					lista.insertBefore(nuevoElemento, primerElemento);
					// esto sucede cunado es una respuesta
					if (idPadre != undefined) {
						respuesta = document.getElementById("cantidad-respuestas-" + idPadre);
						respuesta.innerText = cantidad + " Respuestas ";
						var icon = document.createElement('i');
						icon.classList.add('fa', 'fa-comments-o');
						respuesta.appendChild(icon);
						var miDiv = document.getElementById("div-respuesta-" + idPadre);
						miDiv.classList.remove('show');
						lista.classList.add('show');
					}else{
						cantidadActual = parseInt(dato["cantidad"]);
					}


				}

				function eliminarAnimacion(id) {
					var pregunta = document.getElementById(id);
					pregunta.classList = [];
				}


				async function consultarPreguntas() {
					var data = {
						"claseId": '<?= $idR; ?>',
						"usuarioActual": '<?= $datosUsuarioActual['uss_id']; ?>',
						"usuarioDocente": '<?= $datosCargaActual['car_docente']; ?>'
					};

					var url = "../compartido/ajax-comentarios-preguntas.php";
					resultado = await metodoFetchAsync(url, data, 'html', false);
					contarPreguntas();
					var lista = document.getElementById("lista-preguntas");
					lista.innerHTML = resultado["data"];
				}
			</script>

		</div>
	</div>
</div>