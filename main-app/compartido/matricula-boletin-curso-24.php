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
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Calificaciones.php");

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

if (!empty($_GET["curso"])) {
	$curso = base64_decode($_GET["curso"]);
}

$grupo = 1;
if (!empty($_GET["grupo"])) {
	$grupo = base64_decode($_GET["grupo"]);
}

if ($periodoActual == 1)
	$periodoActuales = "Primero";
if ($periodoActual == 2)
	$periodoActuales = "Segundo";
if ($periodoActual == 3)
	$periodoActuales = "Tercero";
if ($periodoActual == 4)
	$periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$idEstudiante = '';
if (!empty($_GET["id"])) {

	$filtro = " AND mat_id='" . base64_decode($_GET["id"]) . "'";
	$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, yearBd: $year);
	Utilidades::validarInfoBoletin($matriculadosPorCurso);
	$estudiante = $matriculadosPorCurso->fetch_assoc();
	if (!empty($estudiante)) {
		$idEstudiante = $estudiante["mat_id"];
		$curso = $estudiante["mat_grado"];
		$grupo = $estudiante["mat_grupo"];
	}
}

$consultaPuestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual, $curso, $grupo, $year);
$puestosCurso = [];
foreach ($consultaPuestos as $puesto) {
	$puestosCurso[$puesto['estudiante_id']] = $puesto['puesto'];
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
	$datos = Boletin::datosBoletin($curso, $grupo, $periodos, $year, $idEstudiante);
	while ($row = $datos->fetch_assoc()) {
		$listaDatos[] = $row;
	}
	include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}
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
<?php foreach ($estudiantes as $estudiante) {
	Utilidades::valordefecto($puestosCurso[$estudiante["mat_id"]], 0); ?>

	<body style="font-family:Arial; font-size:9px;">

		<div>

			<!--<div align="center" style="margin-bottom: 10px;"><img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="350"></div>-->

			<div align="center" style="margin-bottom: 10px;">
				<img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" height="150" width="200"><br>
			</div>

			<div style="width:100%">
				<table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
					<tr>
						<td>C&oacute;digo:<br>
							<?= strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"]; ?>
						</td>
						<td>Nombre:<br> <?= $estudiante["nombre"] ?></td>
						<td>Grado:<br> <?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></td>
						<td>Puesto Curso:<br> <?= $puestosCurso[$estudiante["mat_id"]]; ?></td>
					</tr>

					<tr>
						<td>Jornada:<br> Mañana</td>
						<td>Sede:<br> <?= $informacion_inst["info_nombre"] ?></td>
						<td colspan="2">Periodo:<br> <b><?= $periodoActual . " (" . $year . ")"; ?></b></td>
						<!-- <td>Puesto Colegio:<br> &nbsp;</td>   -->
					</tr>
				</table>
				<p>&nbsp;</p>
			</div>
		</div>

		<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
			<thead>
				<tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
					<td width="20%" rowspan="2">AREAS / ASIGNATURAS</td>
					<td width="2%" rowspan="2">I.H.</td>

					<?php
					for ($j = 1; $j <= $periodoActual; $j++) {
						$valorPeriodo =empty($estudiante["promedios_generales"][$j]["porcentaje_periodo"])?(100/$periodoActual):$estudiante["promedios_generales"][$j]["porcentaje_periodo"];
						?>
						<td width="3%" colspan="2">Periodo <?= $j . "<br>($valorPeriodo%)" ?></>
						</td>
					<?php } ?>
					<td width="3%" colspan="2">Acumulado</td>
				</tr>

				<tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
					<?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
						<td width="3%">Nota</td>
						<td width="3%">Desempeño</td>
					<?php } ?>
					<td width="3%">Nota</td>
					<td width="3%">Desempeño</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($estudiante["areas"] as $area) { ?>
					<!-- AREAS -->
					<tr style="background: lightgray; color:black; height: 30px; font-weight: bold; font-size: 14px;">
						<td colspan="<?= 2 + (2 * $periodoActual); ?>"><?= strtoupper($area['ar_nombre']); ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
					<?php foreach ($area["cargas"] as $carga) { ?>
						<tr style="background:#fff; height: 25px; font-weight: bold;">
							<td><?= strtoupper($carga['mat_nombre']); ?></td>
							<td align="center"><?= $carga['car_ih']; ?></td>
							<?php for ($k = 1; $k <= $periodoActual; $k++) {
								Utilidades::valordefecto($carga["periodos"][$k]['bol_tipo'], '1');
								Utilidades::valordefecto($carga["periodos"][$k]['bol_nota'], 0);

								$recupero = $carga["periodos"][$k]['bol_tipo'] == '2';
								$nota = $carga["periodos"][$k]["bol_nota"];

								$nota = round($nota, $config['conf_decimales_notas']);
								$nota = number_format($nota, $config['conf_decimales_notas']);

								$colorNota = '';
								if ($nota < $config['conf_nota_minima_aprobar']) {
									$materiasPerdidas++;
									$colorNota = 'tomato';
								}
								?>

								<td align="center" style="background-color: <?= $colorNota ?>;"> <?= $nota ?> </td>
								<td align="center"><?= Boletin::determinarRango($nota, $tiposNotas)['notip_nombre'] ?></td>
							<?php }
							Utilidades::valordefecto($carga["nota_carga_acumulada"], 0);
							$notaCargaAcumulada = $carga["nota_carga_acumulada"];
							$notaCargaAcumulada = round($notaCargaAcumulada, $config['conf_decimales_notas']);
							$notaCargaAcumulada = number_format($notaCargaAcumulada, $config['conf_decimales_notas']);
							$colorNota = '';
							if ($notaCargaAcumulada < $config['conf_nota_minima_aprobar']) {
								$colorNota = 'tomato';
							}
							Utilidades::valordefecto($carga["periodos"][$periodoActual]["indicadores"], []);
							?>
							<td align="center" rowspan="<?= count($carga["periodos"][$periodoActual]["indicadores"]) + 1 ?>"
								style="background-color: <?= $colorNota ?>"><?= $notaCargaAcumulada; ?></td>
							<td align="center" rowspan="<?= count($carga["periodos"][$periodoActual]["indicadores"]) + 1 ?>">
								<?= Boletin::determinarRango($notaCargaAcumulada, $tiposNotas)['notip_nombre'] ?>
							</td>
						</tr>
						<?php foreach ($carga["periodos"][$periodoActual]["indicadores"] as $indicador) {
							$recuperoIndicador = $indicador["recuperado"];
							$notaIndicador = $indicador["nota_final"];
							$notaIndicador = round($notaIndicador, $config['conf_decimales_notas']);
							$notaIndicador = number_format($notaIndicador, $config['conf_decimales_notas']);
							?>
							<!-- INDICADORES -->
							<tr>
								<td colspan="<?= (2 * $periodoActual) - 1 ?>">
									<?= $indicador['ind_id'] . " " . $indicador['ind_nombre']; ?>
								</td>
								<td align="center"> <?= $indicador['valor_porcentaje_indicador'] . "%"; ?></td>
								<td align="center"
									style="<?= $recuperoIndicador ? 'color: #2b34f4;" title="Nota indicador recuperada ' . $indicador['valor_indicador'] . '"' : '' ?>""><?= $notaIndicador ?></td>
								<td align=" center"
									style="<?= $recuperoIndicador ? 'color: #2b34f4;" title="Nota indicador recuperada ' . $indicador['valor_indicador'] . '"' : '' ?>""><?= Boletin::determinarRango($notaIndicador, $tiposNotas)['notip_nombre'] ?></td>
							</tr>

						<?php } ?>
					<?php } ?>
				<?php } ?>
			</tbody>
			<tfoot>
			<tr style=" font-weight:bold; text-align:center; font-size:13px;">
					<td style="text-align:left;" colspan="2">PROMEDIO/TOTAL</td>
					<?php
					$promedioAcumulado = 0;
					for ($k = 1; $k <= $periodoActual; $k++) {
						if ( !empty($estudiante["promedios_generales"][$k]["suma_notas_materias"]) ) {
							$notaPromedio      = $estudiante["promedios_generales"][$k]["suma_notas_materias"] ;
							$cantidadMaterias  = $estudiante["promedios_generales"][$k]["cantidad_materias"] ;
							$porcentajePeriodo = $estudiante["promedios_generales"][$k]["porcentaje_periodo"] ;
							$promedio = round(( $notaPromedio/ ( $cantidadMaterias)), $config['conf_decimales_notas']);
						}else{
							$notaPromedio      = 0;
							$porcentajePeriodo = 0;
							$promedio          = 0;
						}
						$promedioAcumulado += $promedio;						
						$promedio = number_format($promedio, $config['conf_decimales_notas']);
						
						?>
						<td><?= $promedio ?></td>
						<td><?= Boletin::determinarRango($promedio, $tiposNotas)['notip_nombre'] ?></td>
					<?php }
					$promedioAcumulado = $promedioAcumulado / $config['conf_periodos_maximos'];
					$promedioAcumulado = round($promedioAcumulado, $config['conf_decimales_notas']);
					$promedioAcumulado = number_format($promedioAcumulado, $config['conf_decimales_notas']);
					$colorNota = '';
					if ($promedioAcumulado < $config['conf_nota_minima_aprobar']) {
						$colorNota = 'tomato';
					}
					?>

					<td style="background-color: <?= $colorNota ?>"><?= $promedioAcumulado ?></td>
					<td><?= Boletin::determinarRango($promedioAcumulado, $tiposNotas)['notip_nombre'] ?></td>
				</tr>
				</tfoot>
		</table>
		<p>&nbsp;</p>

		<?php
		$estadoAgno = '';
		if ($periodoActual == $config['conf_periodos_maximos']) {
			if ($materiasPerdidas == 0) {
				$estadoAgno = 'PROMOVIDO';
			} elseif ($materiasPerdidas > 0 and $materiasPerdidas < $config["conf_num_materias_perder_agno"]) {
				$estadoAgno = 'DEBE NIVELAR';
			} elseif ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
				$estadoAgno = 'NO FUE PROMOVIDO';
			}
		}
		?>
		<table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
			<tr>
				<td width="40%">
					________________________________________________________________<br>
					DIRECTOR DE GRADO
				</td>
				<td width="20%">
					<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
						<?php
						$contador = 1;
						foreach ($tiposNotas as $tipos) {
							if ($contador % 2 == 1) {
								$fondoFila = '#EAEAEA';
							} else {
								$fondoFila = '#FFF';
							}
							?>
							<tr style="background:<?= $fondoFila; ?>">
								<td><?= $tipos['notip_nombre']; ?></td>
								<td align="center"><?= $tipos['notip_desde'] . " - " . $tipos['notip_hasta']; ?></td>
							</tr>
							<?php $contador++;
						} ?>
					</table>
				</td>
				<td width="60%">
					<p style="font-weight:bold;">Observaciones: <?= $estadoAgno; ?></p>
					______________________________________________________________________<br><br>
					______________________________________________________________________<br><br>
					______________________________________________________________________
				</td>
			</tr>
		</table>
		<p>&nbsp;</p>
		<div id="saltoPagina"></div>

		<?php
}// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>

	<!--
<script type="application/javascript">
print();
</script>   
-->

</body>

</html>