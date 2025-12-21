<?php
include("session.php");
$idPaginaInterna = 'DT0326';
require_once(ROOT_PATH . "/main-app/class/EvaluacionGeneral.php");
require_once(ROOT_PATH . "/main-app/class/PreguntaGeneral.php");
require_once(ROOT_PATH . "/main-app/class/Asignaciones.php");
include("../compartido/historial-acciones-guardar.php");
include("../compartido/head.php");

$id = "";
if (!empty($_GET["id"])) {
	$id = base64_decode($_GET["id"]);
}

$asignacion = Asignaciones::traerDatosAsignaciones($conexion, $config, $id);

$evaluacion = EvaluacionGeneral::consultar($asignacion['epag_id_evaluacion']);
?>
</head>
<!-- END HEAD -->
<?php include("../compartido/body.php"); ?>
<div class="page-wrapper">
	<?php include("../compartido/encabezado.php");?>
	<?php include("../compartido/panel-color.php"); ?>
	<!-- start page container -->
	<div class="page-container">
		<?php include("../compartido/menu.php");?>
		<div class="page-content-wrapper">
			<div class="page-content">
				<div class="page-bar">
					<div class="page-title-breadcrumb">
						<div class=" pull-left">
							<div class="page-title"><?= $evaluacion['evag_nombre']; ?></div>
						</div>
						<ol class="breadcrumb page-breadcrumb pull-right">
							<li><a class="parent-item" href="javascript:void(0);" name="encuestas-resultados.php?idE=<?= base64_encode($evaluacion['evag_id']); ?>" onClick="deseaRegresar(this)">Resultados</a>&nbsp;<i class="fa fa-angle-right"></i></li>
							<li class="active"><?= $evaluacion['evag_nombre']; ?></li>
						</ol>
					</div>
				</div>

				<div class="row">
					<div class="col-md-9">
						<form method="post" enctype="multipart/form-data">
							<?php
							$contPreguntas = 1;
							$contPreguntasObligatorias = 0;
							$puntos = 0;
							$fechaHora = "";
							$preguntasConsulta = EvaluacionGeneral::traerPreguntasEvaluacion($conexion, $config, $asignacion['epag_id_evaluacion']);
							while ($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)) {
								if($preguntas['pregg_visible'] != 1) { continue;}
								if($preguntas['pregg_obligatoria'] == 1) { $contPreguntasObligatorias++;}

								$respuestasConsulta = PreguntaGeneral::traerRespuestasPreguntas($conexion, $config, $preguntas['pregg_id']);
								$existeRespuestas = PreguntaGeneral::existeRespuestaPregunta($conexion, $config, $preguntas['pregg_id'], $id, $asignacion['epag_id_evaluador']);
								$fechaHora = !empty($existeRespuestas['resg_fecha_actualizacion']) ? $existeRespuestas['resg_fecha_actualizacion'] : $fechaHora;
							?>
								<div class="panel">
									<header class="panel-heading panel-heading-blue"><?=$preguntas['pregg_descripcion']; ?> <?=$preguntas['pregg_obligatoria'] == 1 ? '<span style="color: red;">(*)</span>': '<span>(Opcional)</span>'; ?></header>
									<div class="panel-body">
										<?php
											$contRespuestas = 1;
											while ($respuestas = mysqli_fetch_array($respuestasConsulta, MYSQLI_BOTH)) {
										?>
											<div>
												<?php if ($preguntas['pregg_tipo_pregunta'] == MULTIPLE || $preguntas['pregg_tipo_pregunta'] == SINGLE) { ?>
													<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-<?= $contPreguntas; ?><?= $contRespuestas; ?>">

														<input type="radio" name="R<?=$contPreguntas;?>" id="option-<?= $contPreguntas; ?><?= $contRespuestas; ?>" class="mdl-radio__button" value="<?=$respuestas['resg_id']; ?>" <?=!empty($existeRespuestas['resg_respuesta']) && $existeRespuestas['resg_respuesta'] == $respuestas['resg_id'] ? "checked" : "";?> disabled>

													</label>
													<span class="mdl-radio__label"><?=$respuestas['resg_descripcion']; ?></span>
												<?php } ?>
											</div>
											<hr>
										<?php
											$contRespuestas++;
											if ($existeRespuestas['resg_respuesta'] == $respuestas['resg_id']) {
												$puntos += $respuestas['resg_valor'];
											}
										}
										?>
										<?php if ($preguntas['pregg_tipo_pregunta'] == TEXT) { ?>
											<div>
												<textarea cols="100" rows="8" placeholder="Escribe tu respuesta" style="margin-top: 0px; margin-bottom: 0px; height: 100px; resize: none;" disabled><?=!empty($existeRespuestas['resg_respuesta']) ? $existeRespuestas['resg_respuesta'] : "";?></textarea>
											</div>
										<?php } ?>
									</div>
								</div>
							<?php
								$contPreguntas++;
							}
							?>
							<a href="javascript:void(0);" name="encuestas-resultados.php?idE=<?= base64_encode($evaluacion['evag_id']); ?>" class="btn btn-secondary" onClick="deseaRegresar(this)"><i class="fa fa-long-arrow-left"></i>Regresar</a>
						</form>
					</div>
					<div class="col-md-3">
						<!-- BEGIN PROFILE SIDEBAR -->
						<div class="profile-sidebar" style="position: sticky; top:0;">
							<div class="card">
								<div class="card-head card-topline-aqua">
									<header><?= $evaluacion['evag_nombre']; ?></header>
								</div>
								<div class="card-body no-padding height-9">
									<div class="profile-desc">
										<?= $evaluacion['evag_descripcion']; ?><br>
										<b>Resultado: </b><?= $puntos; ?> Ptos.<br>
										<b>Fecha y hora de finalización: </b><?= $fechaHora; ?>
									</div>
								</div>
							</div>
						</div>
						<!-- END BEGIN PROFILE SIDEBAR -->
					</div>
				</div>
			</div>
		</div>
		<!-- end page container -->
		<?php include("../compartido/footer.php"); ?>
	</div>
	<!-- start js include path -->
	<script src="../../config-general/assets/plugins/jquery/jquery.min.js"></script>
	<script src="../../config-general/assets/plugins/popper/popper.js"></script>
	<script src="../../config-general/assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
	<script src="../../config-general/assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
	<!-- bootstrap -->
	<script src="../../config-general/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<script src="../../config-general/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
	<!-- Common js-->
	<script src="../../config-general/assets/js/app.js"></script>

	<!-- notifications -->
	<script src="../../config-general/assets/plugins/jquery-toast/dist/jquery.toast.min.js"></script>
	<script src="../../config-general/assets/plugins/jquery-toast/dist/toast.js"></script>

	<script src="../../config-general/assets/js/layout.js"></script>
	<script src="../../config-general/assets/js/theme-color.js"></script>
	<!-- Material -->
	<script src="../../config-general/assets/plugins/material/material.min.js"></script>
	<script src="../../config-general/assets/js/pages/material-select/getmdl-select.js"></script>
	<script src="../../config-general/assets/plugins/material-datetimepicker/moment-with-locales.min.js"></script>
	<script src="../../config-general/assets/plugins/material-datetimepicker/bootstrap-material-datetimepicker.js"></script>
	<script src="../../config-general/assets/plugins/material-datetimepicker/datetimepicker.js"></script>
	<!-- end js include path -->
	</body>

	

	</html>