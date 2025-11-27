<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0328';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/EvaluacionGeneral.php");
require_once(ROOT_PATH . "/main-app/class/Asignaciones.php");
require_once(ROOT_PATH . "/main-app/class/Respuesta.php");

$idE = base64_decode($_GET['idE']);

$asignacion = Asignaciones::traerDatosAsignacion($conexion, $config, $idE);

$evaluacion = EvaluacionGeneral::consultar($asignacion['gal_id_evaluacion']);


switch ($asignacion['gal_tipo']) {
	case CURSO:
		$consultaEvaluado = mysqli_query($conexion, "SELECT gra_nombre FROM ".BD_ACADEMICA.".academico_grados
		WHERE gra_id='".$asignacion['gal_id_evaluado']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
		$datosEvaluado = mysqli_fetch_array($consultaEvaluado, MYSQLI_BOTH);
		$nombreEvaluado = "CURSO: ".$datosEvaluado['gra_nombre'];
	break;

	case AREA:
		$datosEvaluado = Areas::traerDatosArea($config, $asignacion['gal_id_evaluado']);
		$nombreEvaluado = "AREA: ".$datosEvaluado['ar_nombre'];
	break;

	case MATERIA:
		$datosEvaluado = Asignaturas::consultarDatosAsignatura($conexion, $config, $asignacion['gal_id_evaluado']);
		$nombreEvaluado = "MATERIA: ".$datosEvaluado['mat_nombre'];
	break;

	default:
		if($asignacion['gal_tipo'] == DIRECTIVO || $asignacion['gal_tipo'] == DOCENTE) {
			$SRO = $asignacion['gal_tipo'] == DIRECTIVO ? "DIRECTIVO: " : "DOCENTE: ";
			$datosEvaluado = UsuariosPadre::sesionUsuario($asignacion['gal_id_evaluado']);
			$nombreEvaluado = $SRO.UsuariosPadre::nombreCompletoDelUsuario($datosEvaluado);
		}
	break;
}

?>

<head>
	<title>SINTIA | INFORME DE ENCUESTAS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
	<?php
	$nombreInforme = "INFORME DE ENCUESTA <br>".$evaluacion['evag_nombre']."<br>".$nombreEvaluado;
	include("../compartido/head-informes.php") ?>


	<table width="100%" cellspacing="5" cellpadding="5" rules="all" style="
  border:solid; 
  border-color:<?= $Plataforma->colorUno; ?>; 
  font-size:11px;
  ">

		<?php
		// =====================================
		// PRE-CARGAR PREGUNTAS DE LA EVALUACIÓN
		// =====================================
		$preguntasLista = [];
		$consultaPreguntas = EvaluacionGeneral::traerPreguntasEvaluacion($conexion, $config, $asignacion['gal_id_evaluacion']);
		while ($preg = mysqli_fetch_array($consultaPreguntas, MYSQLI_BOTH)) {
			$preguntasLista[] = $preg;
		}

		// =====================================
		// PRE-CARGAR EVALUADORES Y RESPUESTAS
		// =====================================
		$consultaEvaluadores = Asignaciones::resultadoEncuestasFinalizadas($conexion, $config, $asignacion['gal_id_evaluacion']);
		$filasEvaluadores    = [];
		$idsUsuarios         = [];

		while ($rowEval = mysqli_fetch_array($consultaEvaluadores, MYSQLI_BOTH)) {
			$filasEvaluadores[] = $rowEval;
			$idsUsuarios[]      = $rowEval['uss_id'];
		}

		$idsUsuarios = array_values(array_unique(array_filter($idsUsuarios, 'strlen')));

		// Construir mapa de respuestas [idUsuario][idPregunta] => fila respuesta
		$respuestasMapa = [];
		if (!empty($idsUsuarios) && !empty($preguntasLista)) {
			$idsUsuariosEsc = array_map('intval', $idsUsuarios);
			$idsPreguntas   = array_map(function($p){ return (int)$p['pregg_id']; }, $preguntasLista);
			$idsPreguntasEsc= $idsPreguntas;

			$inUsuarios  = implode(',', $idsUsuariosEsc);
			$inPreguntas = implode(',', $idsPreguntasEsc);

			try {
				$sqlResp = "SELECT gr.*, gr2.resg_descripcion, gr2.resg_valor 
							FROM ".BD_ADMIN.".general_resultados gr 
							LEFT JOIN ".BD_ADMIN.".general_respuestas gr2 
								ON gr2.resg_id = gr.resg_respuesta 
								AND gr2.resg_institucion = gr.resg_institucion 
								AND gr2.resg_year = gr.resg_year
							WHERE gr.resg_institucion = {$config['conf_id_institucion']} 
							  AND gr.resg_year = {$_SESSION['bd']}
							  AND gr.resg_id_usuario IN ({$inUsuarios})
							  AND gr.resg_id_pregunta IN ({$inPreguntas})";

				$consultaResp = mysqli_query($conexion, $sqlResp);
				while ($rowResp = mysqli_fetch_array($consultaResp, MYSQLI_BOTH)) {
					$idUsuario  = $rowResp['resg_id_usuario'];
					$idPregunta = $rowResp['resg_id_pregunta'];
					if (!isset($respuestasMapa[$idUsuario])) {
						$respuestasMapa[$idUsuario] = [];
					}
					$respuestasMapa[$idUsuario][$idPregunta] = $rowResp;
				}
			} catch (Exception $e) {
				include("../compartido/error-catch-to-report.php");
			}
		}

		$numContestados = count($filasEvaluadores);
		$promedioFinal  = 0;
		$sumaPregunta   = [];
		?>

		<tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">

			<th style="font-size:9px;">Cod</th>
			<th style="font-size:9px;">Evaluador</th>
			<?php foreach ($preguntasLista as $preguntas) { ?>
				<th style="font-size:9px; text-align:center; border:groove;"><?= !empty($preguntas['pregg_descripcion']) ? $preguntas['pregg_descripcion'] : "" ?></th>
			<?php } ?>
			<th style="text-align:center;">PROM</th>
		</tr>
		<?php
		foreach ($filasEvaluadores as $resultado) {
		?>
			<tr style="border-color:<?= $Plataforma->colorDos; ?>;">
				<td style="font-size:9px;"><?= $resultado['uss_id']; ?></td>
				<td style="font-size:9px;"><?= UsuariosPadre::nombreCompletoDelUsuario($resultado); ?></td>
				<?php
					foreach ($preguntasLista as $preguntas) {
						$idPregunta = $preguntas['pregg_id'];
						$respuesta  = $respuestasMapa[$resultado['uss_id']][$idPregunta] ?? null;

						if ($preguntas['pregg_tipo_pregunta'] == TEXT) {
							$valorRespuesta = !empty($respuesta['resg_respuesta']) ? $respuesta['resg_respuesta'] : "";
						} else {
							if (!empty($respuesta['resg_valor'])) {
								$valorRespuesta = $respuesta['resg_valor']." Ptos <mark style='color: darkgrey'>(".($respuesta['resg_descripcion'] ?? "").")</mark>";
							} else {
								$valorRespuesta = "";
							}
						}
				?>
					<td style="text-align:center;"><?=$valorRespuesta?></td>
				<?php
						if (!empty($respuesta['resg_valor'])) {
							$sumaPregunta[$idPregunta] = !empty($sumaPregunta[$idPregunta])
								? $sumaPregunta[$idPregunta] + $respuesta['resg_valor']
								: $respuesta['resg_valor'];
						}
					}
				?>
				<td style="text-align:center; width:40px; font-weight:bold;"><?= $resultado['promedio']; ?></td>
			</tr>
		<?php
				$promedioFinal += $resultado['promedio'];
			}
		?>
		<tr style="border-color:<?= $Plataforma->colorDos; ?>;">
			<td style="font-size:9px;" colspan="2">Promedio General</td>
			<?php
				foreach ($preguntasLista as $preguntas) {
			?>
				<td style="text-align:center;"><?= !empty($sumaPregunta[$preguntas['pregg_id']]) && $numContestados > 0 ? ($sumaPregunta[$preguntas['pregg_id']] / $numContestados) : ""?></td>
			<?php
			}
			?>
			<td style="text-align:center; width:40px; font-weight:bold;"><?=!empty($promedioFinal) ? ($promedioFinal / $numContestados) : ""?></td>
		</tr>
	</table>
	<?php include("../compartido/footer-informes.php");
	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); ?>
	
    <script type="application/javascript">
        print();
    </script>
</body>
</html>