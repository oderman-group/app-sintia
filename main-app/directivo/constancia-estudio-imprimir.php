<?php
// Documento imprimible (sin menú)
include("../compartido/session-compartida.php");

$idPaginaInterna = 'DT0099';
if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");

require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/Plataforma.php");

$Plataforma = new Plataforma();

$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$curso = isset($_GET['curso']) ? (string)$_GET['curso'] : '';
$idEst = isset($_GET['id']) ? (string)$_GET['id'] : '';

if ($year <= 0 || $curso === '' || $idEst === '') {
	echo '<div style="padding:40px;font-family:Arial;text-align:center;">
		<h2>Parámetros incompletos</h2>
		<p>Debe seleccionar año, curso y estudiante.</p>
		<button onclick="window.close()">Cerrar</button>
	</div>';
	exit();
}

// Obtener nombre de ciudad/departamento desde el código
if (!empty($informacion_inst["info_ciudad"]) && is_numeric($informacion_inst["info_ciudad"])) {
	$consultaCiudad = mysqli_query($conexion, "SELECT ciu_nombre, dep_nombre 
		FROM ".BD_ADMIN.".localidad_ciudades 
		INNER JOIN ".BD_ADMIN.".localidad_departamentos ON dep_id = ciu_departamento 
		WHERE ciu_id = " . intval($informacion_inst["info_ciudad"]) . " 
		LIMIT 1");
	if ($consultaCiudad && mysqli_num_rows($consultaCiudad) > 0) {
		$datosCiudad = mysqli_fetch_array($consultaCiudad, MYSQLI_BOTH);
		$informacion_inst["ciu_nombre"] = $datosCiudad["ciu_nombre"];
		$informacion_inst["dep_nombre"] = $datosCiudad["dep_nombre"];
	}
}

$estudiante = Estudiantes::obtenerDatosEstudiante($idEst, (string)$year);
if (empty($estudiante) || !is_array($estudiante)) {
	echo '<div style="padding:40px;font-family:Arial;text-align:center;">
		<h2>Estudiante no encontrado</h2>
		<p>No se encontró matrícula del estudiante para el año seleccionado.</p>
		<button onclick="window.close()">Cerrar</button>
	</div>';
	exit();
}

$nombreEst = strtoupper(Estudiantes::NombreCompletoDelEstudiante($estudiante));
$documento = (string)($estudiante["mat_documento"] ?? 'N/A');
$grado = strtoupper((string)($estudiante["gra_nombre"] ?? 'N/A'));

$jornada = !empty($informacion_inst["info_jornada"]) ? strtoupper((string)$informacion_inst["info_jornada"]) : 'N/A';
$nombreInst = strtoupper((string)($informacion_inst["info_nombre"] ?? 'INSTITUCIÓN EDUCATIVA'));
$direccion = (string)($informacion_inst["info_direccion"] ?? '');
$telefono = (string)($informacion_inst["info_telefono"] ?? '');
$ciudad = strtoupper((string)($informacion_inst["ciu_nombre"] ?? ''));
$departamento = strtoupper((string)($informacion_inst["dep_nombre"] ?? ''));

$dane = (string)($informacion_inst["info_dane"] ?? 'N/A');
$resolucion = (string)($informacion_inst["info_resolucion"] ?? 'N/A');

$logo = !empty($informacion_inst["info_logo"]) ? $informacion_inst["info_logo"] : 'sintia-logo-2023.png';

// Firmas
$nombreRector = 'N/A';
$docRector = '';
if (!empty($informacion_inst["info_rector"])) {
	$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
	if (!empty($rector) && is_array($rector)) {
		$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
		$docRector = (string)($rector['uss_documento'] ?? '');
	}
}

$nombreSecretaria = 'N/A';
$docSecretaria = '';
if (!empty($informacion_inst["info_secretaria_academica"])) {
	$secretaria = Usuarios::obtenerDatosUsuario($informacion_inst["info_secretaria_academica"]);
	if (!empty($secretaria) && is_array($secretaria)) {
		$nombreSecretaria = UsuariosPadre::nombreCompletoDelUsuario($secretaria);
		$docSecretaria = (string)($secretaria['uss_documento'] ?? '');
	}
}

// Fecha de expedición
$meses = ["", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
$dia = (int)date('d');
$mes = (int)date('m');
$anioHoy = (int)date('Y');
$mesTxt = $meses[$mes] ?? '';

$codigoAutenticidad = Utilidades::generateCode("CE");

include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<title>Constancia de estudio - SINTIA</title>
	<style>
		* { box-sizing: border-box; }
		body {
			font-family: Arial, sans-serif;
			font-size: 12px;
			line-height: 1.6;
			color: #000;
			margin: 0;
			padding: 0;
			background: #fff;
		}
		.page {
			width: 100%;
			max-width: 900px;
			margin: 0 auto;
			padding: 40px 50px;
		}
		.header {
			display: flex;
			align-items: flex-start;
			gap: 16px;
			margin-bottom: 25px;
		}
		.header img {
			width: 70px;
			height: 70px;
			object-fit: contain;
		}
		.header .inst {
			flex: 1;
			text-align: center;
		}
		.header .inst .name {
			font-weight: bold;
			font-size: 14px;
		}
		.header .inst .meta {
			font-size: 11px;
			margin-top: 4px;
		}
		.hr {
			border-top: 1px solid #999;
			margin: 18px 0 26px 0;
		}
		.center-title {
			text-align: center;
			font-weight: bold;
			margin: 22px 0;
			font-size: 16px;
			letter-spacing: 1px;
		}
		.text {
			text-align: justify;
			font-size: 12px;
		}
		.text strong { font-weight: bold; }
		.signatures {
			display: flex;
			justify-content: space-between;
			gap: 30px;
			margin-top: 55px;
		}
		.sig {
			width: 45%;
			text-align: left;
		}
		.sig .line {
			border-top: 1px solid #000;
			margin-bottom: 6px;
			width: 80%;
		}
		.sig .name { font-weight: bold; }
		.footer {
			margin-top: 35px;
			font-size: 10px;
			color: #333;
			display: flex;
			justify-content: space-between;
			gap: 12px;
		}
		.footer .right {
			text-align: right;
			white-space: nowrap;
		}
		.no-print {
			position: fixed;
			top: 20px;
			right: 20px;
			display: flex;
			gap: 10px;
			z-index: 1000;
		}
		.no-print button {
			padding: 10px 16px;
			border: 1px solid #999;
			background: #fff;
			cursor: pointer;
			border-radius: 4px;
		}
		@media print {
			.no-print { display: none !important; }
			@page { size: letter; margin: 1.8cm; }
			/* Dejar espacio para el pie fijo */
			.page { padding: 0 0 60px 0; }
			/* Pie fijo real para impresión/PDF */
			.footer {
				position: fixed;
				left: 1.8cm;
				right: 1.8cm;
				bottom: 1.2cm;
				margin: 0;
				background: #fff;
			}
		}
	</style>
</head>
<body>
	<div class="no-print">
		<button onclick="window.print()">Imprimir</button>
		<button onclick="window.close()">Cerrar</button>
	</div>

	<div class="page">
		<div class="header">
			<img src="<?=MAIN_URL;?>/files/images/logo/<?= htmlspecialchars($logo); ?>" alt="Logo del Colegio">
			<div class="inst">
				<div class="name"><?= htmlspecialchars($nombreInst); ?></div>
				<div class="meta">
					<?php if($direccion !== '' || $ciudad !== '' || $departamento !== '') { ?>
						Dirección: <?= htmlspecialchars(trim($direccion)); ?><?= $ciudad !== '' ? ', '.htmlspecialchars($ciudad) : '' ?><?= $departamento !== '' ? ', '.htmlspecialchars($departamento) : '' ?><br>
					<?php } ?>
					<?php if($telefono !== '') { ?>
						Teléfono: <?= htmlspecialchars($telefono); ?>
					<?php } ?>
				</div>
			</div>
			<div style="width:70px;"></div>
		</div>

		<div class="hr"></div>

		<div class="text">
			La rectora y la Secretaria del plantel, con código DANE No. <strong><?= htmlspecialchars($dane); ?></strong> y resolución <strong><?= htmlspecialchars($resolucion); ?></strong>.
		</div>

		<div class="center-title">HACEN CONSTAR</div>

		<div class="text">
			Que el estudiante <strong><?= htmlspecialchars($nombreEst); ?></strong>, identificado con documento No. <strong><?= htmlspecialchars($documento); ?></strong>
			se encuentra matriculado en esta institución y cursando los estudios correspondientes al grado <strong><?= htmlspecialchars($grado); ?></strong>,
			jornada <strong><?= htmlspecialchars($jornada); ?></strong> en el año lectivo comprendido entre enero de <strong><?= (int)$year; ?></strong> y diciembre de <strong><?= (int)$year; ?></strong>.
		</div>

		<div class="text" style="margin-top: 18px;">
			Expedida en <strong><?= htmlspecialchars($ciudad !== '' ? $ciudad : 'N/A'); ?></strong> el día <strong><?= $dia; ?></strong> de <strong><?= htmlspecialchars($mesTxt); ?></strong> de <strong><?= $anioHoy; ?></strong>.
		</div>

		<div class="signatures">
			<div class="sig">
				<div class="line"></div>
				<div class="name"><?= htmlspecialchars($nombreRector); ?></div>
				<?php if($docRector !== '') { ?><div>CC No. <?= htmlspecialchars($docRector); ?></div><?php } ?>
				<div>Rector(a)</div>
			</div>
			<div class="sig">
				<div class="line"></div>
				<div class="name"><?= htmlspecialchars($nombreSecretaria); ?></div>
				<?php if($docSecretaria !== '') { ?><div>CC No. <?= htmlspecialchars($docSecretaria); ?></div><?php } ?>
				<div>Secretaria</div>
			</div>
		</div>

		<div class="footer">
			<div>Generado: <?= date('Y-m-d'); ?></div>
			<div class="right">Código de Autenticidad: <?= htmlspecialchars($codigoAutenticidad); ?> &nbsp;&nbsp; Plataforma SINTIA</div>
		</div>
	</div>
</body>
</html>

