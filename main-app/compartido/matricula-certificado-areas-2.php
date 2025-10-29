<?php
date_default_timezone_set("America/Bogota");
include("session-compartida.php");
$idPaginaInterna = 'DT0225';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

// Configuraciones para manejo de archivos grandes
set_time_limit(300);
ini_set('memory_limit', '256M');

$id="";
if(isset($_REQUEST["id"])){$id=base64_decode($_REQUEST["id"]);}
$desde="";
if(isset($_REQUEST["desde"])){$desde=base64_decode($_REQUEST["desde"]);}
$hasta="";
if(isset($_REQUEST["hasta"])){$hasta=base64_decode($_REQUEST["hasta"]);}
$estampilla="";
if(isset($_REQUEST["estampilla"])){$estampilla=base64_decode($_REQUEST["estampilla"]);}

$modulo = 1;

// Optimización: Cachear tipos de notas para evitar consultas repetidas
$notasCualitativasCache = [];

?>

<!doctype html>
<html class="no-js" lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<title>Certificado de Estudios - SINTIA</title>
	
	<style>
		/* ============================
		   ESTILOS GENERALES
		   ============================ */
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Arial', 'Times New Roman', serif;
			font-size: 11pt;
			line-height: 1.6;
			color: #000;
			background-color: #fff;
			padding: 20px;
		}

		.container-certificado {
			max-width: 850px;
			margin: 0 auto;
			background: white;
			padding: 30px;
		}

		/* ============================
		   BOTONES DE ACCIÓN
		   ============================ */
		.botones-accion {
			position: fixed;
			bottom: 30px;
			right: 30px;
			z-index: 1000;
			display: flex;
			flex-direction: column;
			gap: 10px;
		}

		.btn-flotante {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			padding: 10px 20px;
			border: 1px solid #999;
			border-radius: 4px;
			font-size: 13px;
			font-weight: 500;
			cursor: pointer;
			transition: all 0.2s ease;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			min-width: 140px;
			text-decoration: none;
			background: white;
			color: #333;
		}

		.btn-print {
			border-color: #2c3e50;
			color: #2c3e50;
		}

		.btn-print:hover {
			background: #2c3e50;
			color: white;
		}

		.btn-close {
			border-color: #7f8c8d;
			color: #7f8c8d;
		}

		.btn-close:hover {
			background: #7f8c8d;
			color: white;
		}

		/* ============================
		   ENCABEZADO
		   ============================ */
		.header-certificado {
			text-align: center;
			margin-bottom: 30px;
			padding-bottom: 15px;
			border-bottom: 2px solid #000;
		}

		.logo-institucion {
			max-width: 100px;
			height: auto;
			margin-bottom: 15px;
		}

		.nombre-institucion {
			font-size: 14pt;
			font-weight: bold;
			color: #000;
			margin-bottom: 8px;
		}

		.info-institucion {
			font-size: 10pt;
			color: #333;
			line-height: 1.8;
		}

		.director-titulo {
			font-size: 11pt;
			font-weight: bold;
			color: #000;
			margin-top: 15px;
		}

		/* ============================
		   TEXTO INTRODUCTORIO
		   ============================ */
		.texto-centrado {
			text-align: center;
			font-weight: bold;
			font-size: 14pt;
			margin: 25px 0;
			letter-spacing: 2px;
		}

		.texto-estudiante {
			text-align: justify;
			margin: 20px 0;
			line-height: 1.8;
			font-size: 11pt;
		}

		/* ============================
		   TABLAS
		   ============================ */
		.titulo-periodo {
			text-align: center;
			font-weight: bold;
			font-size: 12pt;
			margin: 25px 0 10px 0;
			padding: 10px;
			background: #f8f9fa;
			border: 1px solid #dee2e6;
			border-left: 4px solid #2c3e50;
		}

		.tabla-calificaciones {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 20px;
			font-size: 10pt;
		}

		.tabla-calificaciones th,
		.tabla-calificaciones td {
			border: 1px solid #000;
			padding: 8px 10px;
		}

		.tabla-calificaciones th {
			background-color: #e9ecef;
			font-weight: bold;
			text-align: center;
			font-size: 10pt;
		}

		.tabla-calificaciones td {
			vertical-align: middle;
		}

		.tabla-calificaciones tr.fila-area {
			background-color: #f8f9fa;
			font-weight: bold;
		}

		.tabla-calificaciones tr.fila-materia {
			background-color: white;
		}

		.tabla-calificaciones tr:hover {
			background-color: #f1f3f5;
		}

		/* ============================
		   MENSAJES DE PROMOCIÓN
		   ============================ */
		.mensaje-promocion {
			text-align: center;
			font-weight: bold;
			font-style: italic;
			font-size: 11pt;
			margin: 20px 0;
			padding: 15px;
			border: 1px solid #dee2e6;
			background: #f8f9fa;
		}

		.mensaje-promovido {
			border-left: 4px solid #27ae60;
			background: #d4edda;
		}

		.mensaje-no-promovido {
			border-left: 4px solid #e74c3c;
			background: #f8d7da;
		}

		.mensaje-retirado {
			border-left: 4px solid #f39c12;
			background: #fff3cd;
		}

		/* ============================
		   SECCIÓN DE NIVELACIONES
		   ============================ */
		.seccion-nivelaciones {
			margin: 20px 0;
			padding: 15px;
			background: #fff8e1;
			border: 1px solid #ffc107;
			border-radius: 4px;
		}

		.seccion-nivelaciones p {
			margin-bottom: 8px;
			line-height: 1.6;
		}

		/* ============================
		   PIE DEL CERTIFICADO
		   ============================ */
		.pie-certificado {
			font-size: 11pt;
			text-align: justify;
			line-height: 1.8;
			margin: 25px 0;
		}

		/* ============================
		   FIRMAS
		   ============================ */
		.tabla-firmas {
			width: 100%;
			border-collapse: collapse;
			margin-top: 40px;
		}

		.tabla-firmas td {
			text-align: center;
			vertical-align: bottom;
			padding: 10px 20px;
		}

		.firma-imagen {
			max-width: 100px;
			height: auto;
			margin-bottom: 10px;
		}

		.firma-linea {
			border-top: 1px solid #000;
			width: 60%;
			margin: 0 auto 5px auto;
		}

		.firma-nombre {
			font-weight: bold;
			font-size: 10pt;
			margin-top: 5px;
		}

		.firma-cargo {
			font-size: 9pt;
			color: #555;
		}

		/* ============================
		   PIE DE PÁGINA
		   ============================ */
		.footer-sintia {
			text-align: center;
			font-size: 9pt;
			color: #666;
			margin-top: 30px;
			padding-top: 20px;
			border-top: 1px solid #dee2e6;
		}

		.footer-sintia img {
			max-height: 60px;
			margin-bottom: 10px;
		}

		/* ============================
		   ESTILOS DE IMPRESIÓN
		   ============================ */
		@media print {
			@page {
				size: letter;
				margin: 1.5cm;
			}

			body {
				background-color: white;
				padding: 0;
			}

			.container-certificado {
				padding: 0;
			}

			.botones-accion {
				display: none !important;
			}

			.tabla-calificaciones th {
				background-color: #e9ecef !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.tabla-calificaciones tr.fila-area {
				background-color: #f8f9fa !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-promocion {
				background: #f8f9fa !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-promovido {
				background: #d4edda !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-no-promovido {
				background: #f8d7da !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.mensaje-retirado {
				background: #fff3cd !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.titulo-periodo {
				background: #f8f9fa !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.seccion-nivelaciones {
				background: #fff8e1 !important;
				border-color: #ffc107 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}

			.footer-sintia {
				border-top: 1px solid #999 !important;
			}

			/* Evitar saltos de página inapropiados */
			.titulo-periodo,
			.mensaje-promocion,
			.pie-certificado,
			.header-certificado {
				page-break-inside: avoid;
			}

			.tabla-calificaciones {
				page-break-inside: auto;
			}

			.tabla-firmas {
				page-break-inside: avoid;
			}
		}
	</style>
</head>

<body>
	<!-- Botones de acción -->
	<div class="botones-accion">
		<button class="btn-flotante btn-print" onclick="window.print()">
			<span>■</span>
			<span>Imprimir</span>
		</button>
		<button class="btn-flotante btn-close" onclick="window.close()">
			<span>×</span>
			<span>Cerrar</span>
		</button>
	</div>

	<div class="container-certificado">
		<!-- Encabezado -->
		<div class="header-certificado">
			<img class="logo-institucion" src="<?=MAIN_URL;?>/files/images/logo/<?= !empty($informacion_inst["info_logo"]) ? $informacion_inst["info_logo"] : 'sintia-logo-2023.png';?> " alt="Logo">
			<div class="nombre-institucion"><?= strtoupper($informacion_inst["info_nombre"] ?? 'INSTITUCIÓN EDUCATIVA') ?></div>
			<div class="info-institucion">
				Carácter <?= $informacion_inst["info_caracter"] ?? 'Oficial' ?> en Jornada <?= !empty($informacion_inst["info_jornada"]) ? ucwords(strtolower($informacion_inst["info_jornada"])) : 'Diurna' ?><br>
				Secretaría de Educación de <?= !empty($informacion_inst["dep_nombre"]) ? ucwords(strtolower($informacion_inst["dep_nombre"])) : 'N/A' ?><br>
				Código DANE <?= $informacion_inst["info_dane"] ?? 'N/A' ?> - NIT <?= $informacion_inst["info_nit"] ?? 'N/A' ?><br>
				Reconocimiento Oficial por resolución <?= $informacion_inst["info_resolucion"] ?? 'N/A' ?>
			</div>
			<div class="director-titulo">
				DIRECTOR(A) DEL <?= strtoupper($informacion_inst["info_nombre"] ?? 'INSTITUCIÓN EDUCATIVA') ?>
			</div>
		</div>

		<p class="texto-centrado">C E R T I F I C A</p>

		<?php
		$meses = array(" ","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$horas = array('CERO', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ');

		$restaAgnos = ($hasta - $desde) + 1;
		$i = 1;
		$inicio = $desde;

		// Optimización: Obtener información del nombre y grados de una sola vez
		$grados = "";
		$nombreEstudiante = "";
		$educacion = "BÁSICA";
		
		while ($i <= $restaAgnos) {
			$estudiante = Estudiantes::obtenerDatosEstudiante($id, $inicio);
			
			if ($i == 1) {
				$nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($estudiante);
				
				// Determinar tipo de educación
				switch ($estudiante["gra_nivel"]) {
					case PREESCOLAR: 
						$educacion = "PREESCOLAR"; 
					break;
					case BASICA_PRIMARIA: 
						$educacion = "BÁSICA PRIMARIA"; 
					break;
					case BASICA_SECUNDARIA: 
						$educacion = "BÁSICA SECUNDARIA"; 
					break;
					case MEDIA: 
						$educacion = "MEDIA"; 
					break;
					default: 
						$educacion = "BÁSICA"; 
					break;
				}
			}

			if ($i < $restaAgnos) {
				$grados .= $estudiante["gra_nombre"] . ", ";
			} else {
				$grados .= $estudiante["gra_nombre"];
			}

			$inicio++;
			$i++;
		}
		?>

		<p class="texto-estudiante">
			Que, <b><?=$nombreEstudiante?></b> cursó en esta Institución <b><?=strtoupper($grados);?> GRADO DE EDUCACIÓN <?=$educacion;?></b> y obtuvo las siguientes calificaciones:
		</p>

		<?php
		$restaAgnos = ($hasta - $desde) + 1;
		$i = 1;
		$inicio = $desde;
		$horasT = 0;

		while ($i <= $restaAgnos) {
			// Obtener datos del estudiante para este año
			$matricula = Estudiantes::obtenerDatosEstudiante($id, $inicio);
			
			// Optimización: Obtener configuración del año una sola vez
			$consultaConfig = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".configuracion WHERE conf_id_institucion='" . $_SESSION["idInstitucion"] . "' AND conf_agno='" . $inicio . "'");
			$configAA = mysqli_fetch_array($consultaConfig, MYSQLI_BOTH);
			?>

			<div class="titulo-periodo">
				<?= strtoupper($matricula["gra_nombre"]); ?> GRADO DE EDUCACIÓN <?=$educacion." ".$inicio?><br>
				MATRÍCULA <?= strtoupper($matricula["mat_matricula"] ?? 'N/A'); ?> FOLIO <?= strtoupper($matricula["mat_folio"] ?? 'N/A'); ?>
			</div>

			<?php if ($inicio < $config['conf_agno'] && $configAA['conf_periodo'] == 5) { ?>
				<!-- AÑO FINALIZADO: Mostrar solo definitivas -->
				<table class="tabla-calificaciones">
					<thead>
						<tr>
							<th style="width: 50%;">ÁREAS/ASIGNATURAS</th>
							<th style="width: 30%;">CALIFICACIONES</th>
							<th style="width: 20%;">HORAS</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// Optimización: Obtener todas las cargas de una vez
						$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
						$materiasPerdidas = 0;

						while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {
							// Obtener materias del área
							$materias = Asignaturas::consultarAsignaturasArea($conexion, $config, $matricula["gra_id"], $matricula["gru_id"], $cargas["ar_id"], $inicio);
							$numMat = mysqli_num_rows($materias);
							$mate = "";
							$j = 1;

							while ($mat = mysqli_fetch_array($materias, MYSQLI_BOTH)) {
								if ($j < $numMat) $mate .= "'" . $mat[0] . "',";
								else $mate .= "'" . $mat[0] . "'";
								$j++;
							}

							// Promedio del área
							$boletin = Boletin::obtenerPromedioDiferentesCargas($config, $id, $mate, $inicio);
							$nota = !empty($boletin[0]) ? round($boletin[0], 1) : 0;
							
							for ($n = 0; $n <= 5; $n++) {
								if ($nota == $n) $nota = $nota . ".0";
							}
							
							// Optimización: Usar cache para tipos de notas
							$cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
							if (!isset($notasCualitativasCache[$cacheKey])) {
								$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
							}
							$desempenoA = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
						?>
							<tr class="fila-area">
								<td><?= strtoupper($cargas["ar_nombre"]); ?></td>
								<td style="text-align: center;"><?= $nota; ?> (<?= strtoupper($desempenoA['notip_nombre']); ?>)</td>
								<td style="text-align: center;"><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>
							</tr>

							<?php
							$horasT += $cargas["car_ih"];
							
							// Materias del área
							$materiasDA = Asignaturas::consultarAsignaturaDefinitivaIntensidad($conexion, $config, $matricula["gra_id"], $matricula["mat_grado"], $matricula["gru_id"], $cargas["ar_id"], $inicio);

							while ($mda = mysqli_fetch_array($materiasDA, MYSQLI_BOTH)) {
								$notaDefMateria = Boletin::traerDefinitivaBoletinCarga($config, $mda["car_id"], $id, $inicio);
								$notaDefMateria = !empty($notaDefMateria['promedio']) ? round($notaDefMateria['promedio'], 1) : 0;
								
								for ($n = 0; $n <= 5; $n++) {
									if ($notaDefMateria == $n) $notaDefMateria = $notaDefMateria . ".0";
								}
								
								if ($notaDefMateria < $config[5]) {
									$materiasPerdidas++;
								}
								
								// Optimización: Usar cache para tipos de notas
								$cacheKey = $config['conf_notas_categoria'] . '_' . $notaDefMateria . '_' . $inicio;
								if (!isset($notasCualitativasCache[$cacheKey])) {
									$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDefMateria, $inicio);
								}
								$desempeno = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
								
								// Para preescolares
								if ($matricula["gra_id"] >= 12 and $matricula["gra_id"] <= 15) {
									$nota = ceil($nota);
									if ($notaDefMateria == 1) $notaDefMateria = 'DEFICIENTE';
									if ($notaDefMateria == 2) $notaDefMateria = 'INSUFICIENTE';
									if ($notaDefMateria == 3) $notaDefMateria = 'ACEPTABLE';
									if ($notaDefMateria == 4) $notaDefMateria = 'SOBRESALIENTE';
									if ($notaDefMateria == 5) $notaDefMateria = 'EXCELENTE';
								}
							?>
								<tr class="fila-materia">
									<td style="padding-left: 25px;"><?= $mda["mat_nombre"]; ?></td>
									<td style="text-align: center;">
										<?= $notaDefMateria; ?> 
										<?php if ($matricula["gra_id"] < 12 && !empty($desempeno['notip_nombre'])) { ?> 
											(<?= strtoupper($desempeno['notip_nombre']); ?>) 
										<?php } ?>
									</td>
									<td style="text-align: center;"><?= $mda["ipc_intensidad"] . " (" . $horas[$mda["ipc_intensidad"]] . ")"; ?></td>
								</tr>
							<?php } ?>
						<?php
						}

						// MEDIA TÉCNICA
						if (array_key_exists(10, $_SESSION["modulos"])){
							$consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config, $inicio, $id);
							while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
								if(!empty($datosEstudianteActualMT)){
									$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
									
									while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {
										$materias = Asignaturas::consultarAsignaturasArea($conexion, $config, $matricula["gra_id"], $matricula["gru_id"], $cargas["ar_id"], $inicio);
										$numMat = mysqli_num_rows($materias);
										$mate = "";
										$j = 1;

										while ($mat = mysqli_fetch_array($materias, MYSQLI_BOTH)) {
											if ($j < $numMat) $mate .= "'" . $mat[0] . "',";
											else $mate .= "'" . $mat[0] . "'";
											$j++;
										}

										$boletin = Boletin::obtenerPromedioDiferentesCargas($config, $id, $mate, $inicio);
										$nota = !empty($boletin[0]) ? round($boletin[0], 1) : 0;
										
										for ($n = 0; $n <= 5; $n++) {
											if ($nota == $n) $nota = $nota . ".0";
										}
										
										$cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
										if (!isset($notasCualitativasCache[$cacheKey])) {
											$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
										}
										$desempenoA = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
										if (!empty($desempenoA['notip_nombre'])) {
											$desempenoA['notip_nombre'] = $nota == 0 ? "Bajo" : $desempenoA['notip_nombre'];
										}
									?>
										<tr class="fila-area">
											<td><?= strtoupper($cargas["ar_nombre"]); ?></td>
											<td style="text-align: center;"><?= $nota; ?> (<?= strtoupper($desempenoA['notip_nombre']); ?>)</td>
											<td style="text-align: center;"><?= $cargas["car_ih"] . " (" . $horas[$cargas["car_ih"]] . ")"; ?></td>
										</tr>
									<?php
										$materiasDA = Asignaturas::consultarAsignaturaDefinitivaIntensidad($conexion, $config, $matricula["gra_id"], $matricula["mat_grado"], $matricula["gru_id"], $cargas["ar_id"], $inicio);

										while ($mda = mysqli_fetch_array($materiasDA, MYSQLI_BOTH)) {
											$notaDefMateria = Boletin::traerDefinitivaBoletinCarga($config, $mda["car_id"], $id, $inicio);
											$notaDefMateria = !empty($notaDefMateria['promedio']) ? round($notaDefMateria['promedio'], 1) : 0;
											
											for ($n = 0; $n <= 5; $n++) {
												if ($notaDefMateria == $n) $notaDefMateria = $notaDefMateria . ".0";
											}
											
											$cacheKey = $config['conf_notas_categoria'] . '_' . $notaDefMateria . '_' . $inicio;
											if (!isset($notasCualitativasCache[$cacheKey])) {
												$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDefMateria, $inicio);
											}
											$desempeno = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
											
											if ($matricula["gra_id"] >= 12 and $matricula["gra_id"] <= 15) {
												$nota = ceil($nota);
												if ($notaDefMateria == 1) $notaDefMateria = 'DEFICIENTE';
												if ($notaDefMateria == 2) $notaDefMateria = 'INSUFICIENTE';
												if ($notaDefMateria == 3) $notaDefMateria = 'ACEPTABLE';
												if ($notaDefMateria == 4) $notaDefMateria = 'SOBRESALIENTE';
												if ($notaDefMateria == 5) $notaDefMateria = 'EXCELENTE';
											}
										?>
											<tr class="fila-materia">
												<td style="padding-left: 25px;"><?= $mda["mat_nombre"]; ?></td>
												<td style="text-align: center;">
													<?= $notaDefMateria; ?> 
													<?php if ($matricula["gra_id"] < 12 && !empty($desempeno['notip_nombre'])) { ?> 
														(<?= strtoupper($desempeno['notip_nombre']); ?>) 
													<?php } ?>
												</td>
												<td style="text-align: center;"><?= $mda["ipc_intensidad"] . " (" . $horas[$mda["ipc_intensidad"]] . ")"; ?></td>
											</tr>
										<?php } ?>
									<?php
									}
								}
							}
						}
						?>
					</tbody>
				</table>

				<?php
				// Nivelaciones
				$nivelaciones = Calificaciones::consultarNivelacionesEstudiante($conexion, $config, $id, $inicio);
				$numNiv = mysqli_num_rows($nivelaciones);

				if ($numNiv > 0) {
				?>
					<div class="seccion-nivelaciones">
						<p style="font-weight: bold; margin-bottom: 10px;">El(la) Estudiante niveló las siguientes materias:</p>
						<?php while ($niv = mysqli_fetch_array($nivelaciones, MYSQLI_BOTH)) { ?>
							<p>
								<b><?= strtoupper($niv["mat_nombre"]) ?> (<?= $niv["niv_definitiva"] ?>)</b> 
								Según acta <?= $niv["niv_acta"] ?> en la fecha de <?= $niv["niv_fecha_nivelacion"] ?>
							</p>
						<?php } ?>
					</div>
				<?php
				}

				// Determinar promoción
				$cargasAcademicasC = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
				$materiasPerdidas = 0;
				$vectorMP = array();
				$periodoFinal = $config['conf_periodos_maximos'];
				
				while ($cargasC = mysqli_fetch_array($cargasAcademicasC, MYSQLI_BOTH)) {
					$boletinC = Boletin::traerDefinitivaBoletinCarga($config, $cargasC["car_id"], $id, $inicio);
					$notaC = !empty($boletinC['promedio']) ? round($boletinC['promedio'], 1) : 0;
					
					if ($notaC < $config[5]) {
						$vectorMP[$materiasPerdidas] = $cargasC["car_id"];
						$materiasPerdidas++;
					}

					if ($boletinC['periodo'] < $config['conf_periodos_maximos']){
						$periodoFinal = $boletinC['periodo'];
					}
				}

				// Verificar nivelaciones
				$niveladas = 0;
				if ($materiasPerdidas > 0) {
					for ($m = 0; $m < $materiasPerdidas; $m++) {
						$nMP = Calificaciones::validarMateriaNivelada($conexion, $config, $id, $vectorMP[$m], $inicio);
						if (mysqli_num_rows($nMP) > 0) {
							$niveladas++;
						}
					}
				}

				// Mensaje de promoción
				$claseMensaje = 'mensaje-promocion';
				if($materiasPerdidas == 0 || $niveladas >= $materiasPerdidas){
					$msj = "EL (LA) ESTUDIANTE ".$nombreEstudiante." FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-promovido';
				} else {
					$msj = "EL (LA) ESTUDIANTE ".$nombreEstudiante." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-no-promovido';
				}

				if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
					$msj = "EL(LA) ESTUDIANTE ".$nombreEstudiante." FUE RETIRADO SIN FINALIZAR AÑO LECTIVO";
					$claseMensaje = 'mensaje-promocion mensaje-retirado';
				}
				?>
				<div class="<?= $claseMensaje; ?>"><?= $msj; ?></div>

			<?php } else { ?>
				<!-- AÑO EN CURSO: Mostrar por periodos -->
				<table class="tabla-calificaciones">
					<thead>
						<tr>
							<th style="width: 35%; text-align: left;">ÁREAS/ASIGNATURAS</th>
							<th style="width: 8%;">HS</th>
							<?php
							for ($p = 1; $p <= $config[19]; $p++) {
								echo '<th style="width: ' . (30 / $config[19]) . '%;">' . $p . 'P</th>';
							}
							?>
							<th style="width: 12%;">DEF</th>
							<th style="width: 15%;">DESEMPEÑO</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
						$materiasPerdidas = 0;
						$periodoFinal = $config['conf_periodos_maximos'];

						while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {
							$boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $id, $inicio);
							$nota = !empty($boletin['promedio']) ? round($boletin['promedio'], 1) : 0;

							if ($nota < $config[5]) {
								$materiasPerdidas++;
							}

							if (!empty($boletin['periodo']) && $boletin['periodo'] < $config['conf_periodos_maximos']){
								$periodoFinal = $boletin['periodo'];
							}

							$cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
							if (!isset($notasCualitativasCache[$cacheKey])) {
								$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
							}
							$desempeno = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
						?>
							<tr style="text-align: center;">
								<td style="text-align: left; font-weight: bold;"><?= strtoupper($cargas["mat_nombre"]); ?></td>
								<td><?= $cargas["car_ih"]; ?></td>
								<?php
								$horasT += $cargas["car_ih"];

								for ($p = 1; $p <= $config[19]; $p++) {
									$notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $id, $cargas["car_id"], $inicio);
									$notasPeriodoFinal = '';
									
									if(!empty($notasPeriodo['bol_nota'])){
										$notasPeriodoFinal = $notasPeriodo['bol_nota'];
										if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
											$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasPeriodo['bol_nota']);
											$notasPeriodoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
										}
									}
									echo '<td>' . $notasPeriodoFinal . '</td>';
								}
								?>
								<td style="font-weight: bold;"><?= $nota; ?></td>
								<td><?= $desempeno['notip_nombre']; ?></td>
							</tr>
						<?php
						}

						// MEDIA TÉCNICA para año en curso
						if (array_key_exists(10, $_SESSION["modulos"])){
							$consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config, $inicio, $id);
							while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
								if(!empty($datosEstudianteActualMT)){
									$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
									
									while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {
										$boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $id, $inicio);
										$nota = !empty($boletin[0]) ? round($boletin[0], 1) : 0;

										$cacheKey = $config['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
										if (!isset($notasCualitativasCache[$cacheKey])) {
											$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota, $inicio);
										}
										$desempeno = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
									?>
										<tr style="text-align: center;">
											<td style="text-align: left; font-weight: bold;"><?= strtoupper($cargas["mat_nombre"]); ?></td>
											<td><?= $cargas["car_ih"]; ?></td>
											<?php
											for ($p = 1; $p <= $config[19]; $p++) {
												$notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $id, $cargas["car_id"], $inicio);
												$notasPeriodoFinal = '';
												
												if(!empty($notasPeriodo['bol_nota'])){
													$notasPeriodoFinal = $notasPeriodo['bol_nota'];
													if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
														$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notasPeriodo['bol_nota']);
														$notasPeriodoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
													}
												}
												echo '<td>' . $notasPeriodoFinal . '</td>';
											}
											?>
											<td style="font-weight: bold;"><?= $nota; ?></td>
											<td><?= $desempeno['notip_nombre']; ?></td>
										</tr>
									<?php
									}
								}
							}
						}
						?>
					</tbody>
				</table>

				<?php
				// Mensaje de promoción para año en curso
				$claseMensaje = 'mensaje-promocion';
				if($materiasPerdidas == 0){
					$msj = "EL (LA) ESTUDIANTE ".$nombreEstudiante." FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-promovido';
				} else {
					$msj = "EL (LA) ESTUDIANTE ".$nombreEstudiante." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
					$claseMensaje .= ' mensaje-no-promovido';
				}

				if ($periodoFinal < $config["conf_periodos_maximos"] && $matricula["mat_estado_matricula"] == CANCELADO) {
					$msj = "EL(LA) ESTUDIANTE ".$nombreEstudiante." FUE RETIRADO SIN FINALIZAR AÑO LECTIVO";
					$claseMensaje = 'mensaje-promocion mensaje-retirado';
				}
				?>
				<div class="<?= $claseMensaje; ?>"><?= $msj; ?></div>

			<?php } ?>

		<?php
			$inicio++;
			$i++;
		}
		?>

		<!-- PIE DEL CERTIFICADO -->
		<?php if (date('m') < 10) {
			$mes = substr(date('m'), 1);
		} else {
			$mes = date('m');
		} ?>
		
		<div class="pie-certificado">
			<b>PLAN DE ESTUDIOS:</b> <?= $informacion_inst["info_decreto_plan_estudio"] ?? 'Decreto vigente' ?>. Intensidad horaria <?= $horasT; ?> horas semanales de 55 minutos.<br><br>
			Se expide el presente certificado en <?= !empty($informacion_inst["ciu_nombre"]) ? ucwords(strtolower($informacion_inst["ciu_nombre"])) : 'la ciudad' ?> el <?= date("d"); ?> de <?= $meses[$mes]; ?> de <?= date("Y"); ?>.
		</div>

		<!-- FIRMA -->
		<table class="tabla-firmas">
			<tr>
				<td style="width: 100%;">
					<?php
					$nombreRector = 'DIRECTOR(A)';
					if (!empty($informacion_inst["info_rector"])) {
						$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
						if (!empty($rector)) {
							$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
							if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
								echo '<img class="firma-imagen" src="../files/fotos/'.$rector["uss_firma"].'" alt="Firma Director">';
							}
						}
					}
					?>
					<div class="firma-linea"></div>
					<div class="firma-nombre"><?=strtoupper($nombreRector)?></div>
					<div class="firma-cargo">Director(a)</div>
				</td>
			</tr>
		</table>

		<!-- Footer SINTIA -->
		<div class="footer-sintia">
			<img src="<?=$Plataforma->logo?>" alt="SINTIA">
			<div>SINTIA - SISTEMA INTEGRAL DE GESTIÓN INSTITUCIONAL - <?=date("l, d-M-Y");?></div>
		</div>
	</div>

	<?php 
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	?>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Atajo de teclado para imprimir
			document.addEventListener('keydown', function(e) {
				if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
					e.preventDefault();
					window.print();
				}
			});
		});
	</script>
</body>
</html>
