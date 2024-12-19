<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}

include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/Clases.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Calificaciones.php");
$porcentajes = false;

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
	$year = base64_decode($_GET["year"]);
}

$modulo = 1;
if (empty($_GET["periodo"])) {
	$periodoActual = 1;
} else {
	$periodoActual = base64_decode($_GET["periodo"]);
}

if ($periodoActual == 1)
	$periodoActuales = "Primero";
if ($periodoActual == 2)
	$periodoActuales = "Segundo";
if ($periodoActual == 3)
	$periodoActuales = "Tercero";
if ($periodoActual == 4)
	$periodoActuales = "Final";
if ($periodoActual == $config["conf_periodos_maximos"])
	$periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if (!empty($_GET["curso"])) {
	$curso = base64_decode($_GET["curso"]);
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
	$grupo = base64_decode($_GET["grupo"]);
}

$idEstudiante = '';
if (!empty($_GET["id"])) {

	$filtro = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
	$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
	Utilidades::validarInfoBoletin($matriculadosPorCurso);
	$estudiante = $matriculadosPorCurso->fetch_assoc();
	if (!empty($estudiante)) {
		$idEstudiante = $estudiante["mat_id"];
		$curso = $estudiante["mat_grado"];
		$grupo = $estudiante["mat_grupo"];
	} else {
		echo "Excepción catpurada: Estudiante no encontrado ";
		exit();
	}
}

$consultaPuestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $curso, $grupo, $year);
$puestosCurso = [];
while ($puesto = mysqli_fetch_array($consultaPuestos, MYSQLI_BOTH)) {
	$puestosCurso[$puesto['bol_estudiante']] = $puesto['puesto'];
}

$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
	$tiposNotas[] = $row;
}

$listaDatos = [];
if (!empty($curso) && !empty($grupo) && !empty($year)) {
	$periodos = [];
	for ($i = 1; $i <= $periodoActual; $i++) {
		$periodos[$i] = $i;
	}
	$datos = Boletin::datosBoletin($curso, $grupo, $periodos, $year, $idEstudiante,true);
	while ($row = $datos->fetch_assoc()) {
		$listaDatos[] = $row;
	}
	include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}
// Utilidades::validarInfoBoletin($listaDatos);
foreach ($estudiantes as $estudiante) {

	?>
	<!doctype html>
	<html class="no-js" lang="en">

	<head>
		<meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
		<style>
			#saltoPagina {
				PAGE-BREAK-AFTER: always;
			}
		</style>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	</head>

	<body style="font-family:Arial; font-size:9px;">

		<div>
			<div style="float:right; width:100%">
				<table width="100%" border="1" rules="all">
					<tr>
						<td width="20%" align="center"><img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>"
								width="100"></td>

						<td width="50%">
							<table align="center">
								<tr align="center">
									<td align="center">
										<h2><?= $informacion_inst["info_nombre"] ?></h2>
										Jornada: <?= $informacion_inst["info_jornada"] ?><br>
										<?= !empty($informacion_inst["info_resolucion"]) ? strtoupper($informacion_inst["info_resolucion"]) : ""; ?><br>
										<?= !empty($informacion_inst["info_direccion"]) ? strtoupper($informacion_inst["info_direccion"]) : ""; ?>
										<?= !empty($informacion_inst["info_telefono"]) ? "Tel(s). " . $informacion_inst["info_telefono"] : ""; ?><br>
										<?= !empty($informacion_inst["ciu_nombre"]) ? $informacion_inst["ciu_nombre"] . "/" . $informacion_inst["dep_nombre"] : ""; ?>
									</td>
								</tr>
							</table>
						</td>

						<td width="30%">
							<table width="100%" border="1" rules="all">
								<tr align="center">
									<td colspan="2"><strong>EVALUACIÓN ACADÉMICA</strong></td>
								</tr>

								<tr>
									<td colspan="2"><strong>Alumno:</strong> <?= $nombre ?></td>
								</tr>

								<tr>
									<td><strong>Ruv:</strong>
										<?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?>
									</td>
									<td><strong>Documento:</strong><br><?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?>
									</td>
								</tr>

								<tr>
									<td colspan="2"><strong>Grado:
										</strong><?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
								</tr>

								<tr>
									<td><strong>Periodo:</strong> <?= $periodoActuales; ?></td>
									<td><strong>Año escolar:</strong> <?= $year; ?></td>
								</tr>

								<tr>
									<td><strong># Estudiantes:</strong> <?= count($puestosCurso); ?></td>
									<td>
										<?php if ($estudiante['gra_id'] < 27) { 
											Utilidades::valordefecto($puestosCurso[$estudiante["mat_id"]],0);
											?>
											<strong>Puesto Curso: </strong><?= $puestosCurso[$estudiante["mat_id"]] ?>
										<?php } ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>

				</table>

			</div>
		</div>

		<br>

		<table width="100%" rules="all" border="1">
			<thead>
				<tr style="font-weight:bold; text-align:center;">
					<td width="12%">ASIGNATURAS</td>
					<td width="2%">Ihs.</td>
					<td width="2%">Aus.</td>
					<td width="2%">Eva.</td>
					<td width="80%">AREAS/ LOGROS ACADÉMICOS/ Observaciones</td>
					<td width="2%">Acumulado</td>
				</tr>

			</thead>
			<tbody>
				<?php foreach ($estudiante["areas"] as $area) {
					Utilidades::valordefecto($area["periodos"][$periodoActual]["nota_area"],0);					
					?>
					<tr style="font-weight:bold;">
						<td width="12%">&nbsp;</td>
						<td width="2%">&nbsp;</td>
						<td width="2%">&nbsp;</td>
						<td width="2%" align="center" style="font-size: 14px; font-weight: bold;">
							<?= Boletin::formatoNota($area["periodos"][$periodoActual]["nota_area"], $tiposNotas) ?>
						</td>
						<td width="80%"><?= $area['ar_nombre']; ?></td>
						<td width="2%">&nbsp;</td>
					</tr>
					<?php foreach ($area["cargas"] as $carga) {
						$notaAcumulado = 0;						
						Utilidades::valordefecto($carga["periodos"][$periodoActual]["bol_tipo"],'1');
						Utilidades::valordefecto($carga["periodos"][$periodoActual]["bol_nota"],0);
						Utilidades::valordefecto($carga["periodos"][$periodoActual]["bol_nota_anterior"],valorDefecto: 0);
						Utilidades::valordefecto($carga["periodos"][$periodoActual]["indicadores"],[]);
						$recupero = $carga["periodos"][$periodoActual]['bol_tipo'] == '2';
						?>
						<tr>
							<td><?= $carga['mat_nombre']; ?>
							<?php if ($porcentajes) { ?>(<?= $carga['mat_valor'];?>%)<?php } ?>
							</td>
							<td align="center"><?= $carga['car_ih']; ?></td>
							<td align="center"><?= $carga['fallas']; ?></td>
							<td align="center"
								style="font-size: 12px; font-weight: bold;<?= $recupero ? 'color: #2b34f4;" title="Nota del periodo Recuperada ' . $carga['periodos'][$periodoActual]['bol_nota_anterior'] . '"' : '' ?>">
								<?= Boletin::formatoNota($carga["periodos"][$periodoActual]['bol_nota'], $tiposNotas); ?>
								<?php
								if ($config['conf_forma_mostrar_notas'] == CUANTITATIVA) {
									$desempeno = Boletin::determinarRango($carga["periodos"][$periodoActual]['bol_nota'], $tiposNotas);
									echo $desempeno['notip_nombre'];
								}
								?>
							</td>
							<td>
								<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
									<?php for ($i = 1; $i <= $periodoActual - 1; $i++) {
										Utilidades::valordefecto($carga["periodos"][$i]["indicadores"],[]);										
										foreach ($carga["periodos"][$i]['indicadores'] as $indicador) {
											$recuperoIndicador = $indicador["recuperado"];
											if ($recuperoIndicador) { ?>
												<tr>
													<td width="90%" colspan="2"><b>P.<?=$i;?> Nota <?=Boletin::formatoNota($indicador['valor_indicador'], $tiposNotas);?>  Rec. <?=Boletin::formatoNota($indicador['valor_indicador_recuperado'], $tiposNotas);?></b> <?=$indicador['ind_nombre'];?></td>
												</tr>
											<?php }
										}
									} ?>
									<?php 
									foreach ($carga["periodos"][$periodoActual]['indicadores'] as $indicador) {
										$recuperoIndicador = $indicador["recuperado"];
										?>
										<tr>
											<?php if ($porcentajes) { ?>
												<td align="center"><?= $indicador['valor_porcentaje_indicador']; ?>%</td>
											<?php } ?>
											<td width="90%"><?= $indicador['ind_nombre']; ?></td>
											<td width="10%"
												style="<?= $recuperoIndicador ? 'color: #2b34f4;" title="Nota indicador recuperada ' . $indicador['valor_indicador'] . '"' : '' ?>"" <?= $indicador['ind_nombre']; ?> align="
												center">
												<?php
												$nota = $indicador['nota_final'];
												echo Boletin::formatoNota($nota, $tiposNotas);
												if ($nota < $config['conf_nota_minima_aprobar']) {
													$materiasPerdidas++;
												}
												?>
											</td>
										</tr>
									<?php } ?>
								</table>
							</td>
							<td align="center" style="font-size: 12px; font-weight: bold;">
								
								<?= Boletin::formatoNota($carga["carga_acumulada"], $tiposNotas); ?>
								<?php
								if ($config['conf_forma_mostrar_notas'] == CUANTITATIVA) {
									$desempeno = Boletin::determinarRango($carga["carga_acumulada"], $tiposNotas);
									echo $desempeno['notip_nombre'];
								}
								?>

							</td>

						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>

		</table>
		<p>&nbsp;</p>

		<table width="100%" rules="all" border="1">
			<tr>

				<?php
				$contador = 1;
				foreach ($tiposNotas as $eN) {
					?>

					<td><?= $eN['notip_desde'] . " - " . $eN['notip_hasta']; ?> 		<?= $eN['notip_nombre']; ?></td>
					<?php $contador++;
				} ?>
			</tr>

		</table>

		<?php
		$msjPromocion = '';
		if ($periodoActual == $config['conf_periodos_maximos']) {
			if ($materiasPerdidas == 0) {
				$msjPromocion = 'PROMOVIDO';
			} else {
				$msjPromocion = 'NO PROMOVIDO';
			}
		}
		?>
		<table width="100%" rules="all" border="1">
			<tr>
				<td width="50%">
					Observaciones:
					<p>&nbsp;</p>
					<p>&nbsp;</p>
				</td>

				<td width="50%" align="center">
					<p>&nbsp;</p>
					<p>&nbsp;</p>
					Director de grupo<br>
				</td>
			</tr>
		</table>


		<div id="saltoPagina"></div>
		<?php
}// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>


	<script type="application/javascript">
		print();
	</script>


</body>

</html>