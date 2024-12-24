<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0230';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
$year = $_SESSION["bd"];
if (!empty($_REQUEST["agno"])) {
	$year = $_REQUEST["agno"];
}

$cursoV = '';
$grupoV = '';
if (!empty($_GET["curso"])) {
	$cursoV = base64_decode($_GET['curso']);
	$grupoV = base64_decode($_GET['grupo']);
} elseif (!empty($_POST["curso"])) {
	$cursoV = $_POST['curso'];
	$grupoV = $_POST['grupo'];
}

$consultaCurso = Grados::obtenerDatosGrados($cursoV);
$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);

$consultaGrupo = Grupos::obtenerDatosGrupos($grupoV);
$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);

$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
	$tiposNotas[] = $row;
}

$listaDatos = [];
if (!empty($cursoV) && !empty($grupoV) && !empty($year)) {
	$periodosArray = [];
	for ($i = 1; $i <= $config["conf_periodos_maximos"]; $i++) {
		$periodosArray[$i] = $i;
	}
	$datos = Boletin::datosBoletinPeriodos($cursoV, $grupoV, $periodosArray, $year);
	while ($row = $datos->fetch_assoc()) {
		$listaDatos[] = $row;
	}
	include("../compartido/agrupar-datos-boletin-periodos_mejorado.php");
}
$porcPeriodo = array("", 0.25, 0.15, 0.35, 0.25);
?>

<head>
	<title>SINTIA | Consolidado Final</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
	<?php
	$nombreInforme = "CONSOLIDADO FINAL " . $year . "<br>" . "CURSO: " . Utilidades::getToString($curso['gra_nombre']) . "<br>" . "GRUPO: " . Utilidades::getToString($grupo['gru_nombre']);
	include("../compartido/head-informes.php") ?>


	<table width="100%" cellspacing="5" cellpadding="5" rules="all" style="
  border:solid; 
  border-color:<?= $Plataforma->colorUno; ?>; 
  font-size:11px;
  ">

		<tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">

			<th rowspan="2" style="font-size:9px;">Mat</th>
			<th rowspan="2" style="font-size:9px;">Estudiante</th>
			<?php
			$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoV, $grupoV, $year);
			//SACAMOS EL NUMERO DE CARGAS O MATERIAS QUE TIENE UN CURSO PARA QUE SIRVA DE DIVISOR EN LA DEFINITIVA POR ESTUDIANTE
			$codigosCargas = [];
			$numCargasPorCurso = mysqli_num_rows($cargas);
			while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
				$codigosCargas[$carga['car_id']] = ["car_id" => $carga['car_id'], "id" => $carga['mat_id'], "nombre" => $carga['mat_nombre']];
				?>
				<th style="font-size:9px; text-align:center; border:groove;" colspan="<?= $config[19] + 1; ?>" width="5%">
					<?php if (!empty($carga['mat_nombre'])) {
						echo $carga['mat_nombre'];
					} ?>
				</th>
				<?php
			}
			?>
			<th rowspan="2" style="text-align:center;">PROM</th>
		</tr>

		<tr>
			<?php
			foreach ($codigosCargas as $codigo) {
				$p = 1;
				//PERIODOS DE CADA MATERIA
				while ($p <= $config["conf_periodos_maximos"]) {
					echo '<th style="text-align:center;">' . $p . '</th>';
					$p++;
				}
				//DEFINITIVA DE CADA MATERIA
				echo '<th style="text-align:center; background:#FFC">DEF</th>';
			}
			?>
		</tr>
		<?php foreach ($estudiantes as $estudiante) {
			$defPorEstudiante = 0;
			$numCargasPorCurso = 0;
			?>
			<tr style="border-color:<?= $Plataforma->colorDos; ?>;">
				<td style="font-size:9px;"><?= $estudiante['mat_matricula']; ?></td>
				<td style="font-size:9px;"><?= $estudiante['nombre']; ?></td>
				<?php foreach ($codigosCargas as $codigo) {
					foreach ($estudiante["areas"] as $area) {
						$buscarCarga = isset($area["cargas"][$codigo["car_id"]]) ? $area["cargas"][$codigo["car_id"]] : "";
						$defPorMateria = 0;
						if (!empty($buscarCarga)) {
							$numCargasPorCurso++;
							foreach ($periodosArray as $periodo) {
								$boletin = isset($buscarCarga["periodos"][$periodo]) ? $buscarCarga["periodos"][$periodo] : "";
								if (!empty($boletin["bol_nota"])) {
									$color = '';
									$title = '';
									$notaFormat = Boletin::formatoNota($boletin["bol_nota"], $tiposNotas);
									$color = Boletin::colorNota($boletin['bol_nota']);

									$defPorMateria += ($boletin["bol_nota"] * $porcPeriodo[$periodo]);

									if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
										$title = 'title="Nota Cuantitativa: ' . $boletin['bol_nota'] . '"';
									}

									?>
									<td style="text-align:center; color:<?= $color; ?>" <?= $title; ?>">
										<?= $notaFormat; ?>
									</td>
									<?php
									continue;
								} else { ?>
									<td style="text-align:center; color:<?= $color; ?>"> </td>
								<?php }
							}
							$color = Boletin::colorNota($defPorMateria);
							if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
								$title = 'title="Nota Cuantitativa: ' . $defPorMateria . '"';
							}
							$defPorMateriaFormat = Boletin::formatoNota($defPorMateria, $tiposNotas);
							?>
							<td style="text-align:center;background:#FFC; color:<?= $color; ?>; text-decoration:underline;" <?= $title; ?>>
								<?= $defPorMateriaFormat; ?>
							</td>
							<?php
							$defPorEstudiante += $defPorMateria;
							continue;
						}

					}

				}
				$prom = 0;
				if ($numCargasPorCurso > 0) {
					$prom = ($defPorEstudiante / $numCargasPorCurso);
				}
				$color = Boletin::colorNota($prom);
				$prom = round($prom, $config['conf_decimales_notas']);
				$prom = number_format($prom, $config['conf_decimales_notas']);
				?>

				<td style="text-align:center; width:40px; font-weight:bold;  color:<?= $color; ?>;"><?= $prom; ?></td>
			</tr>
		<?php } ?>

	</table>
	<?php include("../compartido/footer-informes.php");
	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>

</html>