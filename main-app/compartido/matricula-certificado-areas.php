<?php
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

// Opción para mostrar encabezado (por defecto true, para papel membrete usar false)
$mostrarEncabezado = true;
if(isset($_REQUEST["sin_encabezado"])){
    $sinEncabezadoDecoded = base64_decode($_REQUEST["sin_encabezado"]);
    if($sinEncabezadoDecoded == "1"){
        $mostrarEncabezado = false;
    }
}

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
		   TEXTO INTRODUCTORIO
		   ============================ */
		.texto-intro {
			text-align: justify;
			margin: 20px 0;
			line-height: 1.8;
			font-size: 11pt;
		}

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

			/* Evitar saltos de página inapropiados */
			.titulo-periodo,
			.mensaje-promocion,
			.pie-certificado {
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
		<?php if($mostrarEncabezado) { ?>
			<?php
			$nombreInforme = "CERTIFICADO DE ESTUDIOS "."<br>"."No. 12114";
			include("../compartido/head-informes.php");
			?>

			<div class="texto-intro">
				<b>CÓDIGO DEL DANE <?= $informacion_inst["info_dane"] ?></b><br><br>
				Los suscritos Rector y Secretaria del <?= $informacion_inst["info_nombre"] ?>, establecimiento de carácter <?= $informacion_inst["info_caracter"] ?>, calendario <?= $informacion_inst["info_calendario"] ?>, con sus estudios aprobados de Primaria y Bachillerato, según Resolución <?= $informacion_inst["info_resolucion"] ?>.
			</div>

			<p class="texto-centrado">C E R T I F I C A N</p>
		<?php } else { ?>
			<p class="texto-centrado" style="margin-top: 40px;">C E R T I F I C A N</p>
		<?php } ?>

		<?php
		$meses = array(" ", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$horas = array('CERO', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ', 'ONCE', 'DOCE');

		$restaAgnos = ($hasta - $desde) + 1;
		$i = 1;
		$inicio = $desde;

		// Optimización: Obtener información del nombre y grados de una sola vez
		$grados = "";
		$nombreEstudiante = "";
		$educacion = "BÁSICA";
		
		while ($i <= $restaAgnos) {	
			$estudiante = Estudiantes::obtenerDatosEstudiante($id, $inicio);
			
			// Validar que el estudiante exista
			if (empty($estudiante) || !is_array($estudiante)) {
				?>
				<div style="padding: 15px; margin: 20px 0; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
					<strong>Nota:</strong> El estudiante no tiene registro en el año <?= $inicio; ?>. Se omite este año y se continúa con el siguiente.
				</div>
				<?php
				$i++;
				$inicio++;
				continue;
			}
			
			if ($i == 1) {
				$nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($estudiante);
				
				// Determinar tipo de educación
				switch (!empty($estudiante["gra_nivel"]) ? $estudiante["gra_nivel"] : null) {
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
				$grados .= (!empty($estudiante["gra_nombre"]) ? $estudiante["gra_nombre"] : '') . ", ";
			} else {
				$grados .= !empty($estudiante["gra_nombre"]) ? $estudiante["gra_nombre"] : '';
			}

			$inicio++;
			$i++;
		}
		?>

		<p class="texto-estudiante">
			Que, <b><?=$nombreEstudiante?></b> cursó en esta Institución <b><?=strtoupper($grados);?> GRADO DE EDUCACIÓN <?=$educacion;?></b> y obtuvo las siguientes calificaciones:
		</p>

		<?php
		// Obtener datos del estudiante del año actual (donde sabemos que existe) para información general
		$estudianteActual = Estudiantes::obtenerDatosEstudiante($id, $config['conf_agno']);
		if (empty($estudianteActual) || !is_array($estudianteActual)) {
			// Si no existe en el año actual, intentar obtener del último año disponible
			$estudianteActual = Estudiantes::obtenerDatosEstudiante($id, $hasta);
		}
		
		// Obtener nombre y tipo de educación desde el año actual
		$nombreEstudiante = "";
		$educacion = "BÁSICA";
		if (!empty($estudianteActual) && is_array($estudianteActual)) {
			$nombreEstudiante = Estudiantes::NombreCompletoDelEstudiante($estudianteActual);
			
			// Determinar tipo de educación
			switch (!empty($estudianteActual["gra_nivel"]) ? $estudianteActual["gra_nivel"] : '') {
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
		
		$restaAgnos = ($hasta - $desde) + 1;
		$i = 1;
		$inicio = $desde;
		$horasT = 0;

		while ($i <= $restaAgnos) {
			// Obtener datos del estudiante para este año
			$matricula = Estudiantes::obtenerDatosEstudiante($id, $inicio);
			
			// Validar que el estudiante exista en este año
			if (empty($matricula) || !is_array($matricula)) {
				?>
				<div style="padding: 15px; margin: 20px 0; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
					<strong>Nota:</strong> El estudiante no tiene registro en el año <?= $inicio; ?>. Se omite este año y se continúa con el siguiente.
				</div>
				<?php
				$inicio++;
				$i++;
				continue;
			}
			
			// Optimización: Obtener configuración del año una sola vez
			$consultaConfig = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".configuracion WHERE conf_id_institucion='" . $_SESSION["idInstitucion"] . "' AND conf_agno='" . $inicio . "'");
			$configAA = mysqli_fetch_array($consultaConfig, MYSQLI_BOTH);
			?>

			<div class="titulo-periodo">
				<?= strtoupper($matricula["gra_nombre"]); ?> GRADO DE EDUCACIÓN <?=$educacion." ".$inicio?><br>
				MATRÍCULA <?= strtoupper($matricula["mat_numero_matricula"] ?? 'N/A'); ?> FOLIO <?= strtoupper($matricula["mat_folio"] ?? 'N/A'); ?>
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
							// Obtener todas las materias del área usando consultaMaterias (incluye todas, no solo las con intensidad)
							$consultaMaterias = CargaAcademica::consultaMaterias($config, $configAA['conf_periodos_maximos'], $id, $matricula["mat_grado"], $matricula["mat_grupo"], $cargas["ar_id"], $inicio);
							
							// Promedio del área (usando método centralizado)
							$periodosArray = [];
							$periodosMaximos = !empty($configAA['conf_periodos_maximos']) ? $configAA['conf_periodos_maximos'] : 4;
							for($p = 1; $p <= $periodosMaximos; $p++){
								$periodosArray[] = $p;
							}
							$promedioAreaCompleto = Boletin::calcularPromedioAreaCompleto($configAA, $id, $cargas["ar_id"], $periodosArray, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
							$notaArea = $promedioAreaCompleto['acumulado'];
							
							// Usar directamente el valor calculado (ya considera ponderado/simple según configuración)
							$nota = (float)$notaArea;
							$notaFormateada = Boletin::notaDecimales($nota);
							
							// Optimización: Usar cache para tipos de notas
							$cacheKey = $configAA['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
							if (!isset($notasCualitativasCache[$cacheKey])) {
								$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $nota, $inicio);
							}
							$desempenoA = $notasCualitativasCache[$cacheKey];
							
							// Calcular I.H del área como suma de I.H de las asignaturas
							// Obtener todas las materias con su intensidad horaria (usando LEFT JOIN para no excluir materias sin intensidad)
							$ihArea = 0;
							$materiasTemp = [];
							
							// Consulta para obtener todas las materias del área con su intensidad horaria
							$consultaMateriasCompleta = mysqli_query($conexion, "SELECT car.car_id, am.mat_nombre, 
								COALESCE(ipc.ipc_intensidad, car.car_ih, 0) AS ipc_intensidad
								FROM ".BD_ACADEMICA.".academico_cargas car
								INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id = car.car_materia 
									AND am.institucion = car.institucion 
									AND am.year = car.year
								LEFT JOIN ".BD_ACADEMICA.".academico_intensidad_curso ipc ON ipc.ipc_curso = car.car_curso 
									AND ipc.ipc_materia = am.mat_id 
									AND ipc.institucion = car.institucion 
									AND ipc.year = car.year
								WHERE car.car_curso = '" . $matricula["mat_grado"] . "' 
									AND car.car_grupo = '" . $matricula["mat_grupo"] . "' 
									AND am.mat_area = '" . $cargas["ar_id"] . "'
									AND car.institucion = {$config['conf_id_institucion']} 
									AND car.year = {$inicio}
									AND am.institucion = {$config['conf_id_institucion']} 
									AND am.year = {$inicio}
								ORDER BY am.mat_nombre");
							
							// Calcular suma de I.H y guardar las materias
							while ($mdaTemp = mysqli_fetch_array($consultaMateriasCompleta, MYSQLI_BOTH)) {
								$ihMateria = !empty($mdaTemp["ipc_intensidad"]) ? (int)$mdaTemp["ipc_intensidad"] : 0;
								$ihArea += $ihMateria;
								
								// Guardar datos de la materia para mostrar después
								$materiaData = [
									'car_id' => $mdaTemp["car_id"],
									'mat_nombre' => $mdaTemp["mat_nombre"],
									'ipc_intensidad' => $ihMateria
								];
								$materiasTemp[] = $materiaData;
							}
						?>
							<tr class="fila-area">
								<td><?= strtoupper($cargas["ar_nombre"]); ?></td>
								<td style="text-align: center;"><?= $notaFormateada; ?> (<?php if(!empty($desempenoA['notip_nombre'])) echo strtoupper($desempenoA['notip_nombre']); ?>)</td>
								<td style="text-align: center;"><?= $ihArea . " (" . (!empty($horas[$ihArea]) ? $horas[$ihArea] : 'N/A') . ")"; ?></td>
							</tr>

							<?php
							$horasT += $ihArea;
							
							// Materias del área (usar el array guardado)
							foreach ($materiasTemp as $mda) {
								$notaDefMateria = Boletin::traerDefinitivaBoletinCarga($config, $mda["car_id"], $id, $inicio);
								$notaDefMateriaNum = !empty($notaDefMateria['promedio']) ? (float)$notaDefMateria['promedio'] : 0;
								$notaDefMateriaFormateada = Boletin::notaDecimales($notaDefMateriaNum);
								
								if ($notaDefMateriaNum < $config[5]) {
									$materiasPerdidas++;
								}
								
								// Optimización: Usar cache para tipos de notas
								$cacheKey = $configAA['conf_notas_categoria'] . '_' . $notaDefMateriaNum . '_' . $inicio;
								if (!isset($notasCualitativasCache[$cacheKey])) {
									$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $notaDefMateriaNum, $inicio);
								}
								$desempeno = $notasCualitativasCache[$cacheKey];
								
								// Para preescolares
								if (!empty($matricula["gra_nivel"]) && $matricula["gra_nivel"] == PREESCOLAR) {
									$nota = ceil($nota);
									if ($notaDefMateriaNum == 1) $notaDefMateriaFormateada = 'DEFICIENTE';
									if ($notaDefMateriaNum == 2) $notaDefMateriaFormateada = 'INSUFICIENTE';
									if ($notaDefMateriaNum == 3) $notaDefMateriaFormateada = 'ACEPTABLE';
									if ($notaDefMateriaNum == 4) $notaDefMateriaFormateada = 'SOBRESALIENTE';
									if ($notaDefMateriaNum == 5) $notaDefMateriaFormateada = 'EXCELENTE';
								}
							?>
								<tr class="fila-materia">
									<td style="padding-left: 25px;"><?= $mda["mat_nombre"]; ?></td>
									<td style="text-align: center;">
										<?= $notaDefMateriaFormateada; ?> 
										<?php if (!empty($matricula["gra_nivel"]) && $matricula["gra_nivel"] != PREESCOLAR && !empty($desempeno['notip_nombre'])) { ?> 
											(<?= strtoupper($desempeno['notip_nombre']); ?>) 
										<?php } ?>
									</td>
									<td style="text-align: center;"><?= $mda["ipc_intensidad"] . " (" . (!empty($horas[$mda["ipc_intensidad"]]) ? $horas[$mda["ipc_intensidad"]] : 'N/A') . ")"; ?></td>
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
										// Calcular promedio del área usando calcularPromedioAreaCompleto (considera ponderado/simple)
										$periodosArrayMT = [];
										$periodosMaximosMT = !empty($configAA['conf_periodos_maximos']) ? (int)$configAA['conf_periodos_maximos'] : 4;
										for($p = 1; $p <= $periodosMaximosMT; $p++){
											$periodosArrayMT[] = $p;
										}
										$promedioAreaCompletoMT = Boletin::calcularPromedioAreaCompleto($configAA, $id, $cargas["ar_id"], $periodosArrayMT, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio);
										$notaAreaMT = $promedioAreaCompletoMT['acumulado'];
										
										// Usar directamente el valor calculado (ya considera ponderado/simple según configuración)
										$nota = (float)$notaAreaMT;
										$notaFormateada = Boletin::notaDecimales($nota);
										
										$cacheKey = $configAA['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
										if (!isset($notasCualitativasCache[$cacheKey])) {
											$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $nota, $inicio);
										}
										$desempenoA = $notasCualitativasCache[$cacheKey];
										$desempenoA['notip_nombre'] = $nota == 0 ? "Bajo" : $desempenoA['notip_nombre'];
									?>
										<?php
										// Calcular I.H del área como suma de I.H de las asignaturas (Media Técnica)
										// Obtener todas las materias del área con su intensidad horaria (usando LEFT JOIN para no excluir materias sin intensidad)
										$ihAreaMT = 0;
										$materiasTempMT = [];
										
										// Consulta para obtener todas las materias del área con su intensidad horaria
										$consultaMateriasCompletaMT = mysqli_query($conexion, "SELECT car.car_id, am.mat_nombre, 
											COALESCE(ipc.ipc_intensidad, car.car_ih, 0) AS ipc_intensidad
											FROM ".BD_ACADEMICA.".academico_cargas car
											INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id = car.car_materia 
												AND am.institucion = car.institucion 
												AND am.year = car.year
											LEFT JOIN ".BD_ACADEMICA.".academico_intensidad_curso ipc ON ipc.ipc_curso = car.car_curso 
												AND ipc.ipc_materia = am.mat_id 
												AND ipc.institucion = car.institucion 
												AND ipc.year = car.year
											WHERE car.car_curso = '" . $datosEstudianteActualMT["matcur_id_curso"] . "' 
												AND car.car_grupo = '" . $datosEstudianteActualMT["matcur_id_grupo"] . "' 
												AND am.mat_area = '" . $cargas["ar_id"] . "'
												AND car.institucion = {$config['conf_id_institucion']} 
												AND car.year = {$inicio}
												AND am.institucion = {$config['conf_id_institucion']} 
												AND am.year = {$inicio}
											ORDER BY am.mat_nombre");
										
										// Calcular suma de I.H y guardar las materias
										while ($mdaTemp = mysqli_fetch_array($consultaMateriasCompletaMT, MYSQLI_BOTH)) {
											$ihMateria = !empty($mdaTemp["ipc_intensidad"]) ? (int)$mdaTemp["ipc_intensidad"] : 0;
											$ihAreaMT += $ihMateria;
											
											// Guardar datos de la materia para mostrar después
											$materiaData = [
												'car_id' => $mdaTemp["car_id"],
												'mat_nombre' => $mdaTemp["mat_nombre"],
												'ipc_intensidad' => $ihMateria
											];
											$materiasTempMT[] = $materiaData;
										}
										?>
										<tr class="fila-area">
											<td><?= strtoupper($cargas["ar_nombre"]); ?></td>
											<td style="text-align: center;"><?= $notaFormateada; ?> (<?= strtoupper($desempenoA['notip_nombre']); ?>)</td>
											<td style="text-align: center;"><?= $ihAreaMT . " (" . (!empty($horas[$ihAreaMT]) ? $horas[$ihAreaMT] : 'N/A') . ")"; ?></td>
										</tr>
									<?php
										$horasT += $ihAreaMT;
										
										// Materias del área (usar el array guardado)
										foreach ($materiasTempMT as $mda) {
											$notaDefMateria = Boletin::traerDefinitivaBoletinCarga($config, $mda["car_id"], $id, $inicio);
											$notaDefMateriaNum = !empty($notaDefMateria['promedio']) ? (float)$notaDefMateria['promedio'] : 0;
											$notaDefMateriaFormateada = Boletin::notaDecimales($notaDefMateriaNum);
											
											$cacheKey = $config['conf_notas_categoria'] . '_' . $notaDefMateriaNum . '_' . $inicio;
											if (!isset($notasCualitativasCache[$cacheKey])) {
												$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDefMateriaNum, $inicio);
											}
											$desempeno = $notasCualitativasCache[$cacheKey];
											
											// Obtener nivel educativo del curso para Media Técnica
											$consultaNivelMT = mysqli_query($conexion, "SELECT gra_nivel FROM ".BD_ACADEMICA.".academico_grados WHERE gra_id='" . $datosEstudianteActualMT["matcur_id_curso"] . "' AND institucion='" . $config['conf_id_institucion'] . "' AND year='" . $inicio . "'");
											$nivelMT = mysqli_fetch_array($consultaNivelMT, MYSQLI_BOTH);
											
											if (!empty($nivelMT["gra_nivel"]) && $nivelMT["gra_nivel"] == PREESCOLAR) {
												$nota = ceil($nota);
												if ($notaDefMateriaNum == 1) $notaDefMateriaFormateada = 'DEFICIENTE';
												if ($notaDefMateriaNum == 2) $notaDefMateriaFormateada = 'INSUFICIENTE';
												if ($notaDefMateriaNum == 3) $notaDefMateriaFormateada = 'ACEPTABLE';
												if ($notaDefMateriaNum == 4) $notaDefMateriaFormateada = 'SOBRESALIENTE';
												if ($notaDefMateriaNum == 5) $notaDefMateriaFormateada = 'EXCELENTE';
											}
										?>
											<tr class="fila-materia">
												<td style="padding-left: 25px;"><?= $mda["mat_nombre"]; ?></td>
												<td style="text-align: center;"><?= $notaDefMateriaFormateada; ?> <?php if (!empty($nivelMT["gra_nivel"]) && $nivelMT["gra_nivel"] != PREESCOLAR && !empty($desempeno['notip_nombre'])) { ?>(<?= strtoupper($desempeno['notip_nombre']); ?>)<?php } ?></td>
												<td style="text-align: center;"><?= $mda["ipc_intensidad"] . " (" . (!empty($horas[$mda["ipc_intensidad"]]) ? $horas[$mda["ipc_intensidad"]] : 'N/A') . ")"; ?></td>
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
					$notaC = !empty($boletinC['promedio']) ? (float)$boletinC['promedio'] : 0;
					
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

				// Verificar si hay notas en el último periodo configurado
				$tieneNotasUltimoPeriodo = false;
				$ultimoPeriodo = $config["conf_periodos_maximos"];
				$cargasParaVerificar = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio);
				while ($cargaVerificar = mysqli_fetch_array($cargasParaVerificar, MYSQLI_BOTH)) {
					$notaUltimoPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $ultimoPeriodo, $id, $cargaVerificar["car_id"], $inicio);
					if (!empty($notaUltimoPeriodo['bol_nota'])) {
						$tieneNotasUltimoPeriodo = true;
						break;
					}
				}

				// Mensaje de promoción (solo si hay notas en el último periodo)
				if ($tieneNotasUltimoPeriodo) {
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
						$claseMensaje .= ' mensaje-retirado';
					}
					?>
					<div class="<?= $claseMensaje; ?>"><?= $msj; ?></div>
				<?php } ?>

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
						// Obtener todas las materias (sin agrupar por área) para mostrar todas las asignaturas
						$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $matricula["mat_grado"], $matricula["mat_grupo"], $inicio, "");
						$materiasPerdidas = 0;
						$periodoFinal = $config['conf_periodos_maximos'];

						while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {
							$boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $id, $inicio);
							$nota = !empty($boletin['promedio']) ? (float)$boletin['promedio'] : 0;
							$notaFormateada = Boletin::notaDecimales($nota);

							if ($nota < $config[5]) {
								$materiasPerdidas++;
							}

							if (!empty($boletin['periodo']) && $boletin['periodo'] < $config['conf_periodos_maximos']){
								$periodoFinal = $boletin['periodo'];
							}

							$cacheKey = $configAA['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
							if (!isset($notasCualitativasCache[$cacheKey])) {
								$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $nota, $inicio);
							}
							$desempeno = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
							
							// Obtener intensidad horaria (intentar de academico_intensidad_curso, si no usar car_ih)
							$consultaIntensidad = mysqli_query($conexion, "SELECT ipc_intensidad FROM ".BD_ACADEMICA.".academico_intensidad_curso 
								WHERE ipc_curso='" . $matricula["mat_grado"] . "' 
								AND ipc_materia='" . $cargas["car_materia"] . "' 
								AND institucion={$config['conf_id_institucion']} 
								AND year={$inicio} 
								LIMIT 1");
							$intensidadData = mysqli_fetch_array($consultaIntensidad, MYSQLI_BOTH);
							$ihMateria = !empty($intensidadData["ipc_intensidad"]) ? (int)$intensidadData["ipc_intensidad"] : (!empty($cargas["car_ih"]) ? (int)$cargas["car_ih"] : 0);
						?>
							<tr style="text-align: center;">
								<td style="text-align: left; font-weight: bold;"><?= strtoupper($cargas["mat_nombre"]); ?></td>
								<td><?= $ihMateria; ?></td>
								<?php
								$horasT += $ihMateria;

								for ($p = 1; $p <= $config[19]; $p++) {
									$notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $id, $cargas["car_id"], $inicio);
									$notasPeriodoFinal = '';
									
									if(!empty($notasPeriodo['bol_nota'])){
										$notaPeriodoNum = (float)$notasPeriodo['bol_nota'];
										if($configAA['conf_forma_mostrar_notas'] == CUALITATIVA){
											$estiloNota = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $notaPeriodoNum, $inicio);
											$notasPeriodoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
										} else {
											$notasPeriodoFinal = Boletin::notaDecimales($notaPeriodoNum);
										}
									}
									echo '<td>' . $notasPeriodoFinal . '</td>';
								}
								?>
								<td style="font-weight: bold;"><?= $notaFormateada; ?></td>
								<td><?= $desempeno['notip_nombre']; ?></td>
							</tr>
						<?php
						}

						// MEDIA TÉCNICA para año en curso
						if (array_key_exists(10, $_SESSION["modulos"])){
							$consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config, $inicio, $id);
							while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
								if(!empty($datosEstudianteActualMT)){
									// Obtener todas las materias (sin agrupar por área) para mostrar todas las asignaturas
									$cargasAcademicas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $inicio, "");
									
									while ($cargas = mysqli_fetch_array($cargasAcademicas, MYSQLI_BOTH)) {
										$boletin = Boletin::traerDefinitivaBoletinCarga($config, $cargas["car_id"], $id, $inicio);
										$nota = !empty($boletin['promedio']) ? (float)$boletin['promedio'] : 0;
										$notaFormateada = Boletin::notaDecimales($nota);

										$cacheKey = $configAA['conf_notas_categoria'] . '_' . $nota . '_' . $inicio;
										if (!isset($notasCualitativasCache[$cacheKey])) {
											$notasCualitativasCache[$cacheKey] = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $nota, $inicio);
										}
										$desempeno = !empty($notasCualitativasCache[$cacheKey]) ? $notasCualitativasCache[$cacheKey] : ['notip_nombre' => 'N/A'];
										
										// Obtener intensidad horaria (intentar de academico_intensidad_curso, si no usar car_ih)
										$consultaIntensidad = mysqli_query($conexion, "SELECT ipc_intensidad FROM ".BD_ACADEMICA.".academico_intensidad_curso 
											WHERE ipc_curso='" . $datosEstudianteActualMT["matcur_id_curso"] . "' 
											AND ipc_materia='" . $cargas["car_materia"] . "' 
											AND institucion={$config['conf_id_institucion']} 
											AND year={$inicio} 
											LIMIT 1");
										$intensidadData = mysqli_fetch_array($consultaIntensidad, MYSQLI_BOTH);
										$ihMateria = !empty($intensidadData["ipc_intensidad"]) ? (int)$intensidadData["ipc_intensidad"] : (!empty($cargas["car_ih"]) ? (int)$cargas["car_ih"] : 0);
									?>
										<tr style="text-align: center;">
											<td style="text-align: left; font-weight: bold;"><?= strtoupper($cargas["mat_nombre"]); ?></td>
											<td><?= $ihMateria; ?></td>
											<?php
											for ($p = 1; $p <= $config[19]; $p++) {
												$notasPeriodo = Boletin::traerNotaBoletinCargaPeriodo($config, $p, $id, $cargas["car_id"], $inicio);
												$notasPeriodoFinal = '';
												
												if(!empty($notasPeriodo['bol_nota'])){
													$notaPeriodoNum = (float)$notasPeriodo['bol_nota'];
													if($configAA['conf_forma_mostrar_notas'] == CUALITATIVA){
														$estiloNota = Boletin::obtenerDatosTipoDeNotas($configAA['conf_notas_categoria'], $notaPeriodoNum, $inicio);
														$notasPeriodoFinal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
													} else {
														$notasPeriodoFinal = Boletin::notaDecimales($notaPeriodoNum);
													}
												}
												echo '<td>' . $notasPeriodoFinal . '</td>';
											}
											?>
											<td style="font-weight: bold;"><?= $notaFormateada; ?></td>
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
					$claseMensaje .= ' mensaje-retirado';
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

		<!-- FIRMAS -->
		<table class="tabla-firmas">
			<tr>
				<td style="width: 50%;">
					<?php
					$nombreRector = 'RECTOR(A)';
					if (!empty($informacion_inst["info_rector"])) {
						$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
						if (!empty($rector)) {
							$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
							if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])){
								echo '<img class="firma-imagen" src="../files/fotos/'.$rector["uss_firma"].'" alt="Firma Rector">';
							}
						}
					}
					?>
					<div class="firma-linea"></div>
					<div class="firma-nombre"><?=strtoupper($nombreRector)?></div>
					<div class="firma-cargo">Rector(a)</div>
				</td>
				<td style="width: 50%;">
					<?php
					$nombreSecretaria = 'SECRETARIO(A)';
					if (!empty($informacion_inst["info_secretaria_academica"])) {
						$secretaria = Usuarios::obtenerDatosUsuario($informacion_inst["info_secretaria_academica"]);
						if (!empty($secretaria)) {
							$nombreSecretaria = UsuariosPadre::nombreCompletoDelUsuario($secretaria);
							if(!empty($secretaria["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretaria['uss_firma'])){
								echo '<img class="firma-imagen" src="../files/fotos/'.$secretaria["uss_firma"].'" alt="Firma Secretaria">';
							}
						}
					}
					?>
					<div class="firma-linea"></div>
					<div class="firma-nombre"><?=strtoupper($nombreSecretaria)?></div>
					<div class="firma-cargo">Secretario(a)</div>
				</td>
			</tr>
		</table>
	</div>

	<?php 
	include("footer-informes.php");
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
