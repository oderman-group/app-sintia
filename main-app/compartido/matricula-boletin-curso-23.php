<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");


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
	$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, yearBd: $year);
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
foreach ($consultaPuestos as $puesto) {
	$puestosCurso[$puesto['estudiante_id']] = $puesto['puesto'];
}

$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
	$tiposNotas[] = $row;
}

$listaCargas = [];
$conCargasDos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo, $year);
while ($row = $conCargasDos->fetch_assoc()) {

	$indicadores = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $row['car_id'], $periodoActual, $year);
	$listaIndicadores = [];
	while ($row2 = $indicadores->fetch_assoc()) {
		$listaIndicadores[] = $row2;
	}
	$row['indicadores'] = $listaIndicadores;
	$listaCargas[] = $row;
}
$listaDatos = [];
if (!empty($curso) && !empty($grupo) && !empty($year)) {
	$periodos = [];
	for ($i = 1; $i <= $periodoActual; $i++) {
		$periodos[$i] = $i;
	}
	$datos = Boletin::datosBoletin($curso, $grupo, $periodos, $year, $idEstudiante, true);
	while ($row = $datos->fetch_assoc()) {
		$listaDatos[] = $row;
	}
	include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}

if ($periodoActual == 1)
	$periodoActuales = "Primero";
if ($periodoActual == 2)
	$periodoActuales = "Segundo";
if ($periodoActual == 3)
	$periodoActuales = "Tercero";
if ($periodoActual == 4)
	$periodoActuales = "Final"; ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<br?php //CONSULTA ESTUDIANTES MATRICULADOS //=======================DATOS DEL ESTUDIANTE
	MATRICULADO=========================?>
	<!doctype html>
	<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
	<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
	<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
	<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
	<!--[if gt IE 8]><!-->
	<html class="no-js" lang="en"> <!--<![endif]-->

	<head>
		<meta name="tipo_contenido" content="text/html;" http-equiv="content-type" charset="utf-8">
		<style>
			#saltoPagina {
				PAGE-BREAK-AFTER: always;
			}
		</style>
	</head>

	<body style="font-family:Arial;">
		<?php


		foreach ($estudiantes as $estudiante) {
			    $materiasPerdidas=0;
			?>

			<?php
			$nombreInforme = "BOLETÍN DE CALIFICACIONES";
			include("../compartido/head-informes.php");
			Utilidades::valordefecto($puestosCurso[$estudiante["mat_id"]],0); ?>

			<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">
				<tr>
					<td>C&oacute;digo: <b><?= $estudiante["mat_matricula"]; ?></b></td>
					<td colspan="2">Nombre: <b><?= $estudiante["nombre"]; ?></b></td>
				</tr>

				<tr>
					<td>Grado: <b><?= $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]; ?></b></td>
					<td>Periodo: <b><?= strtoupper($periodoActuales); ?></b></td>
					<td>Puesto Curso:<br> <?=$puestosCurso[$estudiante["mat_id"]] ?></td>
				</tr>
			</table>
			<br>
			<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
				<tr
					style="font-weight:bold; background:#EAEAEA; border-color:#000; height:20px; color:#000; font-size:12px;">
					<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
					<td width="2%" align="center">I.H</td>

					<?php for ($j = 1; $j <= $periodoActual; $j++) { ?>
						<td width="3%" align="center"><a
								href="<?= $_SERVER['PHP_SELF']; ?>?id=<?= $estudiante['mat_id']; ?>&periodo=<?= $j ?>"
								style="color:#000; text-decoration:underline;"><?= $j ?>P</a></td>
					<?php } ?>
					<td width="4%" align="center">PRO</td>
					<!--<td width="5%" align="center">PER</td>-->
					<td width="8%" align="center">DESEMPE&Ntilde;O</td>
					<td width="5%" align="center">AUS</td>
				</tr>

				<?php
				foreach ($estudiante["areas"] as $area) { ?>
					<tr style="background:#F06;">
						<td class="area" id="" style="font-size:12px; font-weight:bold;"></td>
					</tr>
					<tr bgcolor="#ABABAB" style="font-size:12px;">
						<td style="font-size:12px; height:25px; font-weight:bold;"><?php echo $area["ar_nombre"]; ?>
						</td>
						<td align="center" style="font-weight:bold; font-size:12px;"></td>
						<?php for ($k = 1; $k <= $periodoActual; $k++) {
							?>
							<td class="" align="center" style="font-weight:bold;"></td>
						<?php } ?>
						<td align="center" style="font-weight:bold;">
							<?= Boletin::formatoNota($area["suma_nota_area"]/$periodoActual, $tiposNotas); ?>
						</td>
						<td align="center" style="font-weight:bold;"></td>
						<td align="center" style="font-weight:bold;"></td>
					</tr>
					<!-- listamos las cargas-->
					<?php foreach ($area["cargas"] as $carga) { ?>
						<tr bgcolor="#EAEAEA" style="font-size:12px;">
							<td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;">
								&raquo;<?php echo $carga["car_id"] . " - " . $carga["mat_nombre"]; ?></td>
							<td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;">
								<?php echo $carga["car_ih"]; ?>
							</td>
							<?php for ($k = 1; $k <= $periodoActual; $k++) {
                                
								Utilidades::valordefecto($carga["periodos"][$k]['bol_tipo'],'1');
								Utilidades::valordefecto($carga["periodos"][$k]['bol_nota'],0);
								$recupero = $carga["periodos"][$k]['bol_tipo'] == '2';
								$nota     = $carga["periodos"][$k]["bol_nota"];

								?>
								<td class="" align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;<?= $recupero ? 'color: #2b34f4;" title="Nota del periodo Recuperada ' . $carga['periodos'][$k]['bol_nota_anterior'] . '"' : '' ?>">
									<?= Boletin::formatoNota($nota, $tiposNotas); ?></br>
									<?php
									
									if ($config['conf_forma_mostrar_notas'] == CUANTITATIVA) {
										$desempeno = Boletin::determinarRango($carga["periodos"][$k]['bol_nota'], $tiposNotas);
										echo $desempeno['notip_nombre'];
									}
									?>
								</td>
							<?php } 
							
							
							if ($carga["carga_acumulada"]  < $config['conf_nota_minima_aprobar']) {
								$materiasPerdidas++;
							}
							?>
							<td align="center" style="font-weight:bold; background:#EAEAEA;">
								<?= Boletin::formatoNota($carga["carga_acumulada"], $tiposNotas) ?>
							</td>
							<td align="center" style="font-weight:bold; background:#EAEAEA;">
								<?= Boletin::determinarRango($carga["carga_acumulada"], $tiposNotas)["notip_nombre"] ?>
							</td>
							<td align="center" style="font-weight:bold; background:#EAEAEA;"><?= $carga["fallas"] ?></td>
						</tr>
						<!-- listamos los indicadores -->
						<?php 
						    Utilidades::valordefecto($carga["periodos"][$periodoActual]["indicadores"],[]);
						    foreach ($carga["periodos"][$periodoActual]["indicadores"] as $indicador) {
							$recuperoIndicador = $indicador["recuperado"];
							?>
							<tr bgcolor="#FFF" style="font-size:12px;">
								<td style="font-size:12px; height:15px;"><?php echo $indicador["ind_nombre"]; ?></td>
								<td align="center" style="font-weight:bold; font-size:12px;"></td>
								<?php for ($k = 1; $k <= $periodoActual - 1; $k++) { ?>
									<td align="center" style="font-weight:bold; font-size:12px;"></td>
								<?php } ?>
								<td align="center"
									style="font-size:12px; height:15px;<?= $recuperoIndicador ? 'color: #2b34f4;" title="Nota indicador recuperada ' . $indicador['valor_indicador'] . '"' : '' ?>"" <?= $indicador['ind_nombre']; ?>">
									<?php echo Boletin::formatoNota($indicador["nota_final"], $tiposNotas);; ?>
								</td>
								<td align="center" style="font-weight:bold;"></td>
								<td align="center" style="font-weight:bold;"></td>
								<td align="center" style="font-weight:bold;"></td>
							</tr>
						<?php } ?>
						<?php if (!empty($carga["periodos"][$periodoActual]["bol_observaciones_boletin"])) { ?>
							<tr>
								<td colspan="7">
									<h5 align="center" style="font-size:12px;font-weight:bold; height:15px;">Observaciones</h5>
									<p
										style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">
										<?= $carga["periodos"][$periodoActual]["bol_observaciones_boletin"]; ?>
									</p>
								</td>
							</tr>
						<?php } ?>

						<?php } ?>
				<?php } ?>
				<tr align="center" style="font-size:12px; font-weight:bold;">
					<td colspan="2" align="right">PROMEDIO</td>

					<?php foreach ($estudiante["promedios_generales"] as $promedio) {
						$promedio = round(($promedio["suma_notas_materias"] / ($promedio["cantidad_materias"])), $config['conf_decimales_notas']);
						
						?>
						<td align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;"><?= Boletin::formatoNota($promedio, $tiposNotas); ?></td>
					<?php } ?>
					<td></td>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>

			<p>&nbsp;</p>
			<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

				<tr
					style="font-weight:bold; background:#036; border-color:#036; height:40px; color:#FC0; font-size:12px; text-align:center">
					<td colspan="3">NOTA DE COMPORTAMIENTO</td>
				</tr>

				<tr
					style="font-weight:bold; background:#F06; border-color:#F06; height:25px; color:#FFF; font-size:12px; text-align:center">
					<td width="8%">Periodo</td>
					<!--<td width="8%">Nota</td>-->
					<td>Observaciones</td>
				</tr>
				<?php foreach ($estudiante["observaciones_generales"] as $observacion) {
					?>
					<tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
						<td><?= $observacion["periodo"] ?></td>
						<td align="left"><?= $observacion["observacion"] ?></td>
					</tr>
				<?php } ?>
			</table>
			<p>&nbsp;</p>
			<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0"
				style="text-align:center; font-size:10px;">
				<tr>
					<td align="center">_________________________________<br><!--<?= strtoupper(""); ?><br>-->Rector(a)
					</td>
					<td align="center">_________________________________<br><!--<?= strtoupper(""); ?><br>-->Director(a)
						de
						grupo</td>
				</tr>
			</table>





			</div>
			<?php
			Utilidades::valordefecto(valor: $msj);
			echo Boletin::mensajeFinalEstudainte($periodoActual,$materiasPerdidas,$estudiante["nombre"],$estudiante["genero"]);
			?>

			<p align="center">
			<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:12px;"
				align="center"><?= $msj; ?></div>
			</p>
			<?php include("../compartido/footer-informes.php") ?>
			<p>&nbsp;</p>

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