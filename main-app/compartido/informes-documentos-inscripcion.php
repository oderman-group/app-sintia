<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0348';
require_once(ROOT_PATH . "/main-app/class/App/Academico/Matricula.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Matriculas_Documentos.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}

Matricula::foreignKey(Matricula::INNER, [
	"mat_id" => 'matd_matricula',
	"institucion" => 'matd.institucion',
	"year" => 'matd.year'
]);

$predicado = [
	"matd_matricula"	=> $_POST['estudiante'],
	"institucion"		=> $_SESSION['idInstitucion'],
	"year"				=> $_SESSION["bd"],
];
$opcionesConsulta = Matriculas_Documentos::SelectJoin(
	$predicado,
	"matd.*, mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido",
	[
		Matricula::class
	]
);
$datosDocumentos = $opcionesConsulta[0];
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
include("../compartido/head.php");
?>
<!doctype html>
<html>

<head></head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Informes SINTIA</title>
<link rel="shortcut icon" href="../files/images/ico.png">
</head>

<body style="font-family:Arial; font-size: 13px;">
	<div align="center" style="margin-bottom:20px; margin-top: 20px;">
		<img src="../files/images/logo/<?= $informacion_inst["info_logo"] ?>" width="200"><br>
		<div>&nbsp;</div>
		<?= $informacion_inst["info_nombre"] ?><br>
		<b><?=$datosDocumentos['matd_matricula'] . " - " . Estudiantes::NombreCompletoDelEstudiante($datosDocumentos);?></b>
		</br>
	</div>

	<div style="margin: 20px;">
		<table width="100%" border="1" rules="all" align="center" style="border-color:#6017dc;">
			<tr style="font-weight:bold; font-size:12px; height:30px; text-align: center; text-transform: uppercase; background:#6017dc; color:#FFF;">
				<td>Nº</td>
				<td>Archivo</td>
				<td>Descargar</td>
			</tr>
			<tr>
				<td align="center">1</td>
				<td>Paz y salvo del colegio de procedencia</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_pazysalvo']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_pazysalvo'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_pazysalvo']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_pazysalvo']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">2</td>
				<td>Ficha acumulativa u observador del alumno</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_observador']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_observador'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_observador']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_observador']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">3</td>
				<td>Fotocopia de la EPS</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_eps']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_eps'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_eps']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_eps']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">4</td>
				<td>Hoja de recomendación</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_recomendacion']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_recomendacion'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_recomendacion']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_recomendacion']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">5</td>
				<td>Vacunas</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_vacunas']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_vacunas'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_vacunas']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_vacunas']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">6</td>
				<td>Boletines actuales</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_boletines_actuales']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_boletines_actuales'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_boletines_actuales']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_boletines_actuales']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">7</td>
				<td>Documento de identidad (Ambas caras)</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_documento_identidad']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_documento_identidad'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_documento_identidad']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_documento_identidad']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td align="center">8</td>
				<td>Certificado</td>
				<td>
					<?php if (!empty($datosDocumentos['matd_certificados']) and file_exists(ROOT_PATH . '/main-app/admisiones/files/otros/' . $datosDocumentos['matd_certificados'])) { ?>
						<p><a href="<?= REDIRECT_ROUTE ?>/admisiones/files/otros/<?= $datosDocumentos['matd_certificados']; ?>" target="_blank" class="link"><?= $datosDocumentos['matd_certificados']; ?></a></p>
					<?php } ?>
				</td>
			</tr>
		</table>
	</div>
	<div style="font-size:10px; margin-top:10px; text-align:center;">
		<img src="https://main.plataformasintia.com/app-sintia/main-app/sintia-logo-2023.png" width="150"><br>
		PLATAFORMA EDUCATIVA SINTIA - <?= date("l, d-M-Y"); ?>
	</div>
</body>
<?php
include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>

</html>