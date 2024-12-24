<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0235';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
require_once("../class/Boletin.php");
require_once("../class/servicios/GradoServicios.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");

$year = $_SESSION["bd"];
if (isset($_POST["year"])) {
	$year = $_POST["year"];
}
if (isset($_GET["year"])) {
	$year = base64_decode($_GET["year"]);
}

$periodoActual = 1;
if (isset($_POST["per"])) {
	$periodoActual = $_POST["per"];
} 
if (isset($_GET["per"])) {
	$periodoActual = base64_decode($_GET["per"]);
} 

$curso = "";
if (isset($_POST["curso"])) {
	$curso = $_POST["curso"];
}
if (isset($_GET["curso"])) {
	$curso = base64_decode($_GET["curso"]);
}

$grupo = 1;
if (isset($_POST["grupo"])) {
	$grupo = $_POST["grupo"];
}
if (isset($_GET["grupo"])) {
	$grupo = base64_decode($_GET["grupo"]);
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
	$datos = Boletin::datosBoletin($curso, $grupo, $periodos, $year);
	while ($row = $datos->fetch_assoc()) {
		$listaDatos[] = $row;
	}
	include("agrupar-datos-boletin-periodos-mejorado.php");
}

$grados = Grados::traerGradosGrupos($config, $curso, $grupo, $year);

$numeroMaterias = 0;
$materias1 = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo, $year, "", "ar_posicion");
while ($row = $materias1->fetch_assoc()) {
	$materias[$row["car_id"]] = $row;
	$numeroMaterias ++;
}

?>

<head>
	<title>Sabanas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
</head>

<body style="font-family:Arial;">
	<?php
	$nombreInforme = "INFORME DE SABANAS" . "<br>" . "PERIDODO " . $periodoActual . "<br>" . $grados["gra_nombre"] . " " . $grados["gru_nombre"] . " " . $year;
	include("../compartido/head-informes.php") ?>


	<table width="100%" cellspacing="5" cellpadding="5" rules="all"
		style="border:solid; border-color:#6017dc; font-size:11px;">
		<tr style="font-weight:bold; height:30px; background:#6017dc; color:#FFF;">
			<td align="center">No</b></td>
			<td align="center">ID</td>
			<td align="center">Estudiante</td>
			<?php foreach ($materias as $materia) { ?>
				<td align="center"><?= $materia['mat_siglas']; ?></td>
			<?php }	?>
			<td align="center" style="font-weight:bold;">PROM</td>
		</tr>


		<?php foreach ($estudiantes as $estudiante) { ?>
			<tr style="border-color:#41c4c4;">
				<td align="center"> <?= $estudiante["nro"]; ?></td>
				<td align="center"> <?= $estudiante["mat_id"]; ?></td>
				<td><?= $estudiante["nombre"]; ?></td>
				<?php $sumaDefini=0; 
					foreach ($estudiante["areas"] as $area) { ?>
					<?php foreach ($area["cargas"] as $carga) {
						$recupero =false;
						Utilidades::valordefecto($carga["periodos"][$periodoActual]['bol_nota'],0);
						$defini  = $carga["periodos"][$periodoActual]['bol_nota'];
						$title   ='';
						if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
							$title = 'title="Nota Cuantitativa: ' . $defini  . '"';
						}
						$sumaDefini += $defini;
						if ($defini < $config[5]) $color = 'red';
						else $color = '#417BC4';
						?>
						<td align="center" style="color:<?= $color; ?>;" <?= $title; ?>
							style="font-size: 12px; font-weight: bold;<?= $recupero ? 'color: #2b34f4;" title="Nota del periodo Recuperada ' . $carga['periodos'][$periodoActual]['bol_nota_anterior'] . '"' : '' ?>">
							<?= Boletin::formatoNota($carga["periodos"][$periodoActual]['bol_nota'], $tiposNotas); ?>
						</td>
					<?php } ?>
				<?php } ?> 
				<?php 
				$promedio 				    = $sumaDefini/$numeroMaterias;
				$notas1[$estudiante["nro"]] = $promedio;
				$grupo1[$estudiante["nro"]] = $estudiante["nombre"];

				if ($promedio < $config[5]) $color = 'red';
						else $color = '#417BC4';

				if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
					$title = 'title="Nota Cuantitativa: ' . $promedio  . '"';
				}
				 ?>
				<td align="center" style="font-weight:bold; color:<?= $color; ?>;" <?= $title; ?>><?=  Boletin::formatoNota($promedio, $tiposNotas); ?></td>

			</tr>
		<?php } ?>


	</table>

	<p>&nbsp;</p>
	<table width="100%" cellspacing="5" cellpadding="5" rules="all" style="
  border:solid; 
  border-color:<?= $Plataforma->colorUno; ?>; 
  font-size:11px;">
		<tr style="font-weight:bold; height:30px; background:<?= $Plataforma->colorUno; ?>; color:#FFF;">
			<td colspan="4" align="center" style="color:#FFFFFF;">PRIMEROS PUESTOS</td>
		</tr>

		<tr style="font-weight:bold; font-size:14px; height:40px;">
			<td align="center">No</b></td>
			<td align="center">Estudiante</td>
			<td align="center">Promedio</td>
			<td align="center">Puesto</td>
		</tr>
		<?php
		$j = 1;
		$cambios = 0;
		$valor = 0;
		if (!empty($notas1)) {
			arsort($notas1);
			foreach ($notas1 as $key => $val) {
				if ($val != $valor) {
					$valor = $val;
					$cambios++;
				}
				if ($cambios == 1) {
					$color = '#CCFFCC';
					$puesto = 'Primero';
				}
				if ($cambios == 2) {
					$color = '#CCFFFF';
					$puesto = 'Segundo';
				}
				if ($cambios == 3) {
					$color = '#FFFFCC';
					$puesto = 'Tercero';
				}
				if ($cambios == 4) {
					break;
				}

				$valTotal = $val;
				$title = '';
				if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
					$title = 'title="Nota Cuantitativa: ' . $val . '"';
					// $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $val, $year);
					$estiloNota = Boletin::determinarRango($val, $tiposNotas);
					$valTotal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
				}
		?>
				<tr style="border-color:#41c4c4; background-color:<?= $color; ?>">
					<td align="center"><?= $j; ?></td>
					<td><?= $grupo1[$key]; ?></td>
					<td align="center" <?= $title; ?>><?=  Boletin::formatoNota($valTotal, $tiposNotas); ?></td>
					<td align="center"><?= $puesto; ?></td>
				</tr>
		<?php
				$j++;
			}
		}
		?>


	</table>


	<?php include("../compartido/footer-informes.php");
	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); ?>
</body>

</html>