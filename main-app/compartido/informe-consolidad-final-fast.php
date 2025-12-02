<?php
// Configuraciones para reportes grandes
set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

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
require_once(ROOT_PATH . "/main-app/class/Plataforma.php");
$Plataforma = new Plataforma;

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

// Validar parámetros
if(empty($cursoV)){
	echo '<html><body><div style="text-align: center; padding: 50px; font-family: Arial;">
		<h2>Error: Parámetros Incompletos</h2>
		<p>No se especificó el curso para generar el informe.</p>
		<button onclick="window.close()">Cerrar</button>
	</div></body></html>';
	exit();
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

// OPTIMIZACIÓN: Pre-cargar cache de notas cualitativas para evitar búsquedas repetidas
$notasCualitativasCache = [];
if ($config['conf_forma_mostrar_notas'] == CUALITATIVA && !empty($tiposNotas)) {
	foreach ($tiposNotas as $notaTipo) {
		// Crear cache para todos los valores posibles en el rango
		for ($i = $notaTipo['notip_desde']; $i <= $notaTipo['notip_hasta']; $i += 0.1) {
			$key = number_format((float)$i, 1, '.', '');
			if (!isset($notasCualitativasCache[$key])) {
				$notasCualitativasCache[$key] = $notaTipo['notip_nombre'];
			}
		}
	}
}

// OPTIMIZACIÓN: Pre-cargar colores de notas para evitar cálculos repetidos
$colorCache = [
	'perdida' => $config['conf_color_perdida'] ?? '#dc3545',
	'ganada' => $config['conf_color_ganada'] ?? '#28a745'
];
$notaMinima = $config['conf_nota_minima_aprobar'] ?? $config[5] ?? 3.0;

$listaDatos = [];
$estudiantes = [];
if (!empty($cursoV) && !empty($grupoV) && !empty($year)) {
	$periodosArray = [];
	for ($i = 1; $i <= $config["conf_periodos_maximos"]; $i++) {
		$periodosArray[$i] = $i;
	}
	$datos = Boletin::datosBoletinPeriodos($cursoV, $grupoV, $periodosArray, $year);
	while ($row = $datos->fetch_assoc()) {
		$listaDatos[] = $row;
	}
	// Corregir nombre del archivo (guion en lugar de guion bajo)
	include("agrupar-datos-boletin-periodos-mejorado.php");
}

// Obtener porcentajes de periodos para el curso
$porcentajesPeriodos = [];
if (!empty($cursoV)) {
	$sqlPorcentajes = "SELECT gvp_periodo, gvp_valor FROM " . BD_ACADEMICA . ".academico_grados_periodos 
	                   WHERE gvp_grado = ? AND institucion = ? AND year = ?";
	$parametrosPorcentajes = [$cursoV, $config['conf_id_institucion'], $year];
	$resultadoPorcentajes = BindSQL::prepararSQL($sqlPorcentajes, $parametrosPorcentajes);
	if ($resultadoPorcentajes) {
		while ($porc = mysqli_fetch_array($resultadoPorcentajes, MYSQLI_BOTH)) {
			$porcentajesPeriodos[$porc['gvp_periodo']] = (float)$porc['gvp_valor'] / 100; // Convertir a decimal
		}
	}
}

// Si no hay porcentajes configurados, usar valores por defecto
if (empty($porcentajesPeriodos)) {
	for ($p = 1; $p <= $config["conf_periodos_maximos"]; $p++) {
		$porcentajesPeriodos[$p] = 1 / $config["conf_periodos_maximos"]; // Distribución equitativa
	}
}

$porcPeriodo = array("", 0.25, 0.15, 0.35, 0.25);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Consolidado Final - SINTIA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?= $Plataforma->logo; ?>">
	
	<style>
		body {
			font-family: Arial, sans-serif;
			font-size: 11px;
			line-height: 1.5;
			color: #000;
			margin: 0;
			padding: 20px;
			background-color: #f5f5f5;
		}
		.container-consolidado {
			max-width: 100%;
			margin: 0 auto;
			padding: 30px;
			background-color: #fff;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		
		/* Tabla */
		.tabla-consolidado {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}
		.tabla-consolidado thead tr {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: #fff;
			font-weight: bold;
			height: 40px;
		}
		.tabla-consolidado thead th {
			padding: 12px 8px;
			text-align: center;
			border: 1px solid rgba(255,255,255,0.2);
			font-size: 11px;
		}
		.tabla-consolidado tbody tr {
			transition: background-color 0.2s ease;
		}
		.tabla-consolidado tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		.tabla-consolidado tbody tr:hover {
			background-color: #e8f4f8;
		}
		.tabla-consolidado tbody td {
			padding: 8px 4px;
			border: 1px solid #ddd;
			font-size: 11px;
			text-align: center;
		}
		.tabla-consolidado thead th.nombre-estudiante,
		.tabla-consolidado tbody td.nombre-estudiante {
			width: 250px;
			min-width: 250px;
			max-width: 250px;
			text-align: left;
			white-space: nowrap;
			overflow: visible;
		}
		.tabla-consolidado tbody td.nota-cell {
			font-weight: bold;
		}
		.tabla-consolidado tbody td.definitiva-cell {
			background-color: #fff3cd;
			font-weight: bold;
		}
		.tabla-consolidado tbody td.promedio-cell {
			font-weight: bold;
			background-color: #f0f0f0;
		}
		
		/* Botones flotantes */
		.no-print {
			position: fixed;
			top: 20px;
			right: 20px;
			z-index: 1000;
			display: flex;
			gap: 10px;
		}
		.btn-print, .btn-close {
			padding: 12px 24px;
			border: none;
			border-radius: 5px;
			font-size: 14px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			box-shadow: 0 2px 5px rgba(0,0,0,0.2);
		}
		.btn-print {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
		}
		.btn-print:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
		}
		.btn-close {
			background: #f44336;
			color: white;
		}
		.btn-close:hover {
			background: #da190b;
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(244, 67, 54, 0.4);
		}
		
		@media print {
			body {
				margin: 0;
				background-color: white;
				padding: 0;
			}
			.container-consolidado {
				max-width: 100%;
				box-shadow: none;
				padding: 10px;
			}
			.no-print {
				display: none !important;
			}
			@page {
				size: landscape;
				margin: 1cm;
			}
			.tabla-consolidado {
				page-break-inside: auto;
				box-shadow: none;
			}
			.tabla-consolidado thead {
				display: table-header-group;
			}
			.tabla-consolidado tr {
				page-break-inside: avoid;
				page-break-after: auto;
			}
			.tabla-consolidado thead tr {
				background: #667eea !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			.tabla-consolidado tbody td.definitiva-cell {
				background-color: #fff3cd !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
		}
	</style>
</head>

<body style="font-family:Arial;">

	<!-- Botones de Acción -->
	<div class="no-print">
		<button class="btn-print" onclick="window.print();">
			<i class="fa fa-print"></i> Imprimir
		</button>
		<button class="btn-close" onclick="window.close();">
			<i class="fa fa-times"></i> Cerrar
		</button>
	</div>

	<div class="container-consolidado">
	<?php
	$nombreInforme = "CONSOLIDADO FINAL " . $year . "<br>" . "CURSO: " . Utilidades::getToString($curso['gra_nombre']) . "<br>" . "GRUPO: " . Utilidades::getToString($grupo['gru_nombre']);
	include("../compartido/head-informes.php");
	
	// Pre-cargar cargas/materias del curso
	$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoV, $grupoV, $year);
	$codigosCargas = [];
	$numCargasPorCurso = 0;
	if($cargas){
		while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
			$codigosCargas[$carga['car_id']] = [
				"car_id" => $carga['car_id'], 
				"id" => $carga['mat_id'], 
				"nombre" => !empty($carga['mat_nombre']) ? $carga['mat_nombre'] : 'N/A'
			];
			$numCargasPorCurso++;
		}
	}
	
	// Inicializar arrays para promedios
	$promediosPorPeriodo = [];
	$promediosDefinitivas = [];
	$promediosGenerales = ['prom1' => 0, 'prom2' => 0, 'contador_prom1' => 0, 'contador_prom2' => 0];
	
	foreach ($codigosCargas as $carga) {
		$promediosDefinitivas[$carga['car_id']] = ['def1' => 0, 'def2' => 0, 'contador_def1' => 0, 'contador_def2' => 0];
		for ($p = 1; $p <= $config["conf_periodos_maximos"]; $p++) {
			$promediosPorPeriodo[$carga['car_id']][$p] = ['suma' => 0, 'contador' => 0];
		}
	}
	?>
	
	<!-- Explicación de columnas -->
	<div class="explicacion-columnas" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); padding: 20px; border-radius: 12px; border: 2px solid #667eea; margin-bottom: 20px; font-size: 12px;">
		<h4 style="color: #92400e; font-weight: 700; margin-top: 0; margin-bottom: 15px;">📊 Explicación de Columnas</h4>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Primera Columna DEF (Fondo amarillo):</strong> Calcula la definitiva usando todos los períodos configurados con sus porcentajes respectivos, incluso si algún período no tiene nota registrada.</p>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Segunda Columna DEF (Fondo azul claro):</strong> Calcula la definitiva usando <strong>solo los períodos que tienen nota registrada</strong>, ajustando los porcentajes proporcionalmente. Si un período no tiene nota, no se considera en el cálculo.</p>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Primera Columna PROM:</strong> Promedio general del estudiante basado en la primera columna DEF de todas sus materias.</p>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Segunda Columna PROM (Fondo azul claro):</strong> Promedio general del estudiante basado en la segunda columna DEF de todas sus materias. Solo considera materias con definitiva mayor a cero.</p>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Fila de Promedios (Footer):</strong></p>
		<ul>
			<li><strong>Promedio por Período:</strong> Muestra el promedio de todos los estudiantes en cada período de cada materia. Se calcula sumando las notas de todos los estudiantes en ese período y dividiendo entre el número de estudiantes que tienen nota.</li>
			<li><strong>Promedio de Primera DEF:</strong> Muestra el promedio de todas las primeras definitivas de todos los estudiantes en cada materia.</li>
			<li><strong>Promedio de Segunda DEF:</strong> Muestra el promedio de todas las segundas definitivas de todos los estudiantes en cada materia. Solo considera estudiantes con definitiva mayor a cero.</li>
			<li><strong>Promedio de Primera PROM:</strong> Muestra el promedio de todas las primeras PROM de todos los estudiantes.</li>
			<li><strong>Promedio de Segunda PROM:</strong> Muestra el promedio de todas las segundas PROM de todos los estudiantes. Solo considera estudiantes con definitiva mayor a cero.</li>
		</ul>
	</div>

	<table class="tabla-consolidado">
		<thead>
			<tr>
				<th rowspan="2">Mat</th>
				<th rowspan="2" class="nombre-estudiante">Estudiante</th>
				<?php foreach ($codigosCargas as $carga) { ?>
					<th colspan="<?= $config[19] + 2; ?>">
						<?= htmlspecialchars($carga['nombre']); ?>
					</th>
				<?php } ?>
				<th colspan="2" rowspan="2">PROM</th>
			</tr>
			<tr>
				<?php
				foreach ($codigosCargas as $codigo) {
					$p = 1;
					// PERIODOS DE CADA MATERIA
					while ($p <= $config["conf_periodos_maximos"]) {
						echo '<th>' . $p . '</th>';
						$p++;
					}
					// DEFINITIVAS DE CADA MATERIA (dos columnas)
					echo '<th style="background:#fffbeb; color:#000;">DEF</th>';
					echo '<th style="background:#e0f2fe; color:#000;">DEF</th>';
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(!empty($estudiantes) && is_array($estudiantes)){
			foreach ($estudiantes as $estudiante) {
				$defPorEstudiante = 0;
				$defPorEstudianteConNotas = 0;
				$numCargasPorCurso = 0;
				$materiasConNota = 0;
				?>
				<tr>
					<td><?= htmlspecialchars(!empty($estudiante['mat_matricula']) ? $estudiante['mat_matricula'] : ''); ?></td>
					<td class="nombre-estudiante"><?= htmlspecialchars(!empty($estudiante['nombre']) ? $estudiante['nombre'] : ''); ?></td>
					<?php foreach ($codigosCargas as $codigo) {
						if(!empty($estudiante["areas"]) && is_array($estudiante["areas"])){
							foreach ($estudiante["areas"] as $area) {
								$buscarCarga = isset($area["cargas"][$codigo["car_id"]]) ? $area["cargas"][$codigo["car_id"]] : "";
								$defPorMateria = 0;
								$defPorMateriaConNotas = 0;
								$periodosConNota = 0;
								$sumaPorcentajesConNota = 0;
								
								if (!empty($buscarCarga)) {
									$numCargasPorCurso++;
									foreach ($periodosArray as $periodo) {
										$boletin = isset($buscarCarga["periodos"][$periodo]) ? $buscarCarga["periodos"][$periodo] : "";
										
										// Obtener porcentaje del período
										$porcentaje = isset($porcentajesPeriodos[$periodo]) ? $porcentajesPeriodos[$periodo] : (1 / $config["conf_periodos_maximos"]);
										
										if (!empty($boletin["bol_nota"])) {
											$notaValor = $boletin["bol_nota"];
											
											// Primera definitiva: suma ponderada usando porcentajes configurados
											$defPorMateria += ($notaValor * $porcentaje);
											
											// Segunda definitiva: solo periodos con nota
											$defPorMateriaConNotas += ($notaValor * $porcentaje);
											$sumaPorcentajesConNota += $porcentaje;
											$periodosConNota++;
											
											// Acumular para promedio por período
											$promediosPorPeriodo[$codigo["car_id"]][$periodo]['suma'] += $notaValor;
											$promediosPorPeriodo[$codigo["car_id"]][$periodo]['contador']++;
											
											// OPTIMIZACIÓN: Calcular color directamente
											$color = ($notaValor < $notaMinima) ? $colorCache['perdida'] : $colorCache['ganada'];
											
											// OPTIMIZACIÓN: Formatear nota usando cache
											$title = '';
											if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
												$title = 'title="Nota Cuantitativa: ' . $notaValor . '"';
												$notaRedondeada = number_format((float)$notaValor, 1, '.', '');
												$notaFormat = isset($notasCualitativasCache[$notaRedondeada]) 
													? $notasCualitativasCache[$notaRedondeada] 
													: Boletin::formatoNota($notaValor, $tiposNotas);
											} else {
												$notaFormat = Boletin::notaDecimales($notaValor);
											}
											?>
											<td class="nota-cell" style="color:<?= $color; ?>;" <?= $title; ?>>
												<?= htmlspecialchars($notaFormat, ENT_QUOTES, 'UTF-8'); ?>
											</td>
											<?php
										} else { ?>
											<td class="nota-cell"> </td>
										<?php }
									}
									
									// Primera definitiva (basada en total de periodos con porcentajes)
									$defPorMateria = round($defPorMateria, 2);
									
									// Segunda definitiva (basada solo en periodos con nota)
									$defPorMateriaConNotasCalculada = 0;
									if ($sumaPorcentajesConNota > 0) {
										$defPorMateriaConNotasCalculada = round($defPorMateriaConNotas / $sumaPorcentajesConNota, 2);
									}
									
									// OPTIMIZACIÓN: Calcular color directamente
									$color = ($defPorMateria < $notaMinima) ? $colorCache['perdida'] : $colorCache['ganada'];
									$colorConNotas = ($defPorMateriaConNotasCalculada < $notaMinima && $defPorMateriaConNotasCalculada > 0) ? $colorCache['perdida'] : $colorCache['ganada'];
									
									// OPTIMIZACIÓN: Formatear nota usando cache
									$title = '';
									$titleConNotas = '';
									if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
										$title = 'title="Nota Cuantitativa: ' . $defPorMateria . '"';
										$defPorMateriaRedondeada = number_format((float)$defPorMateria, 1, '.', '');
										$defPorMateriaFormat = isset($notasCualitativasCache[$defPorMateriaRedondeada]) 
											? $notasCualitativasCache[$defPorMateriaRedondeada] 
											: Boletin::formatoNota($defPorMateria, $tiposNotas);
										
										$titleConNotas = 'title="Nota Cuantitativa: ' . $defPorMateriaConNotasCalculada . '"';
										$defPorMateriaConNotasRedondeada = number_format((float)$defPorMateriaConNotasCalculada, 1, '.', '');
										$defPorMateriaConNotasFormat = isset($notasCualitativasCache[$defPorMateriaConNotasRedondeada]) 
											? $notasCualitativasCache[$defPorMateriaConNotasRedondeada] 
											: Boletin::formatoNota($defPorMateriaConNotasCalculada, $tiposNotas);
									} else {
										$defPorMateriaFormat = Boletin::notaDecimales($defPorMateria);
										$defPorMateriaConNotasFormat = $defPorMateriaConNotasCalculada > 0 ? Boletin::notaDecimales($defPorMateriaConNotasCalculada) : '-';
									}
									?>
									<!-- Primera DEF -->
									<td class="definitiva-cell" style="background:#fffbeb; color:<?= $color; ?>; text-decoration:underline; font-weight:700;" <?= $title; ?>>
										<?= htmlspecialchars($defPorMateriaFormat, ENT_QUOTES, 'UTF-8'); ?>
									</td>
									<!-- Segunda DEF -->
									<td class="definitiva-cell" style="background:#e0f2fe; color:<?= $colorConNotas; ?>; text-decoration:underline; font-weight:700;" <?= $titleConNotas; ?>>
										<?= htmlspecialchars($defPorMateriaConNotasFormat, ENT_QUOTES, 'UTF-8'); ?>
									</td>
									<?php
									//DEFINITIVA POR CADA ESTUDIANTE (basada en primera DEF)
									$defPorEstudiante += $defPorMateria;
									
									//DEFINITIVA POR CADA ESTUDIANTE (basada en segunda DEF)
									if ($defPorMateriaConNotasCalculada > 0) {
										$defPorEstudianteConNotas += $defPorMateriaConNotasCalculada;
										$materiasConNota++;
									}
									
									// Acumular definitivas para promedio
									$promediosDefinitivas[$codigo["car_id"]]['def1'] += $defPorMateria;
									$promediosDefinitivas[$codigo["car_id"]]['contador_def1']++;
									
									if ($defPorMateriaConNotasCalculada > 0) {
										$promediosDefinitivas[$codigo["car_id"]]['def2'] += $defPorMateriaConNotasCalculada;
										$promediosDefinitivas[$codigo["car_id"]]['contador_def2']++;
									}
									
									continue;
								}
							}
						}
					}
					
					// Promedio basado en primera DEF
					$prom = 0;
					if ($numCargasPorCurso > 0) {
						$prom = round($defPorEstudiante / $numCargasPorCurso, 2);
					}
					
					// Promedio basado en segunda DEF
					$promConNotas = 0;
					if ($materiasConNota > 0) {
						$promConNotas = round($defPorEstudianteConNotas / $materiasConNota, 2);
					}
					
					// OPTIMIZACIÓN: Calcular color directamente
					$color = ($prom < $notaMinima) ? $colorCache['perdida'] : $colorCache['ganada'];
					$colorConNotas = ($promConNotas < $notaMinima && $promConNotas > 0) ? $colorCache['perdida'] : $colorCache['ganada'];
					
					$promFormat = Boletin::notaDecimales($prom);
					$promConNotasFormat = $promConNotas > 0 ? Boletin::notaDecimales($promConNotas) : '-';
					
					// Acumular promedios generales
					$promediosGenerales['prom1'] += $prom;
					$promediosGenerales['contador_prom1']++;
					if ($promConNotas > 0) {
						$promediosGenerales['prom2'] += $promConNotas;
						$promediosGenerales['contador_prom2']++;
					}
					?>
					<!-- Primera PROM -->
					<td class="promedio-cell" style="font-weight:bold; color:<?= $color; ?>;">
						<?= htmlspecialchars($promFormat, ENT_QUOTES, 'UTF-8'); ?>
					</td>
					<!-- Segunda PROM -->
					<td class="promedio-cell" style="font-weight:bold; background:#e0f2fe; color:<?= $colorConNotas; ?>;">
						<?= htmlspecialchars($promConNotasFormat, ENT_QUOTES, 'UTF-8'); ?>
					</td>
				</tr>
			<?php 
			} // Cierre foreach estudiantes
		} else { ?>
			<tr>
				<td colspan="100" style="text-align: center; padding: 40px; color: #999;">
					No se encontraron estudiantes con calificaciones para este curso y grupo.
				</td>
			</tr>
		<?php } ?>
		</tbody>
		
		<!-- Footer con promedios -->
		<tfoot>
			<tr style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); font-weight: 700;">
				<td colspan="2" style="text-align: center; border: 2px solid #667eea;">
					<strong>PROMEDIO</strong>
				</td>
				<?php 
				foreach ($codigosCargas as $codigo) {
					for ($p = 1; $p <= $config["conf_periodos_maximos"]; $p++) {
						$promedioPeriodo = 0;
						if ($promediosPorPeriodo[$codigo["car_id"]][$p]['contador'] > 0) {
							$promedioPeriodo = round($promediosPorPeriodo[$codigo["car_id"]][$p]['suma'] / $promediosPorPeriodo[$codigo["car_id"]][$p]['contador'], 2);
						}
						
						$colorPromedio = '#000000';
						if ($promedioPeriodo > 0) {
							if ($promedioPeriodo < $notaMinima) {
								$colorPromedio = $colorCache['perdida'];
							} elseif ($promedioPeriodo >= $notaMinima) {
								$colorPromedio = $colorCache['ganada'];
							}
						}
					?>
						<td style="text-align: center; color: <?= $colorPromedio; ?>; font-weight: 700; border: 1px solid #e2e8f0;">
							<?= $promedioPeriodo > 0 ? $promedioPeriodo : '-'; ?>
						</td>
					<?php } ?>
					
					<?php 
					// Promedio de primera definitiva
					$promedioDef1 = 0;
					if ($promediosDefinitivas[$codigo["car_id"]]['contador_def1'] > 0) {
						$promedioDef1 = round($promediosDefinitivas[$codigo["car_id"]]['def1'] / $promediosDefinitivas[$codigo["car_id"]]['contador_def1'], 2);
					}
					
					$colorDef1 = '#000000';
					if ($promedioDef1 > 0) {
						if ($promedioDef1 < $notaMinima) {
							$colorDef1 = $colorCache['perdida'];
						} elseif ($promedioDef1 >= $notaMinima) {
							$colorDef1 = $colorCache['ganada'];
						}
					}
					
					// Promedio de segunda definitiva
					$promedioDef2 = 0;
					if ($promediosDefinitivas[$codigo["car_id"]]['contador_def2'] > 0) {
						$promedioDef2 = round($promediosDefinitivas[$codigo["car_id"]]['def2'] / $promediosDefinitivas[$codigo["car_id"]]['contador_def2'], 2);
					}
					
					$colorDef2 = '#000000';
					if ($promedioDef2 > 0) {
						if ($promedioDef2 < $notaMinima) {
							$colorDef2 = $colorCache['perdida'];
						} elseif ($promedioDef2 >= $notaMinima) {
							$colorDef2 = $colorCache['ganada'];
						}
					}
					?>
					<td style="text-align: center; background:#fffbeb; color: <?= $colorDef1; ?>; font-weight: 700; border: 1px solid #e2e8f0;">
						<?= $promedioDef1 > 0 ? $promedioDef1 : '-'; ?>
					</td>
					<td style="text-align: center; background:#e0f2fe; color: <?= $colorDef2; ?>; font-weight: 700; border: 1px solid #e2e8f0;">
						<?= $promedioDef2 > 0 ? $promedioDef2 : '-'; ?>
					</td>
				<?php } ?>
				
				<?php 
				// Promedio de primera columna PROM
				$promedioProm1 = 0;
				if ($promediosGenerales['contador_prom1'] > 0) {
					$promedioProm1 = round($promediosGenerales['prom1'] / $promediosGenerales['contador_prom1'], 2);
				}
				
				$colorProm1 = '#000000';
				if ($promedioProm1 > 0) {
					if ($promedioProm1 < $notaMinima) {
						$colorProm1 = $colorCache['perdida'];
					} elseif ($promedioProm1 >= $notaMinima) {
						$colorProm1 = $colorCache['ganada'];
					}
				}
				
				// Promedio de segunda columna PROM
				$promedioProm2 = 0;
				if ($promediosGenerales['contador_prom2'] > 0) {
					$promedioProm2 = round($promediosGenerales['prom2'] / $promediosGenerales['contador_prom2'], 2);
				}
				
				$colorProm2 = '#000000';
				if ($promedioProm2 > 0) {
					if ($promedioProm2 < $notaMinima) {
						$colorProm2 = $colorCache['perdida'];
					} elseif ($promedioProm2 >= $notaMinima) {
						$colorProm2 = $colorCache['ganada'];
					}
				}
				?>
				<td style="text-align: center; font-weight: 700; border: 2px solid #f59e0b; color: <?= $colorProm1; ?>;">
					<?= $promedioProm1 > 0 ? $promedioProm1 : '-'; ?>
				</td>
				<td style="text-align: center; font-weight: 700; border: 2px solid #0369a1; background:#e0f2fe; color: <?= $colorProm2; ?>;">
					<?= $promedioProm2 > 0 ? $promedioProm2 : '-'; ?>
				</td>
			</tr>
		</tfoot>
	</table>

	</div> <!-- Cierre container-consolidado -->

	<?php 
	include("../compartido/footer-informes.php");
	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php"); 
	?>
	
	<script type="text/javascript">
		// Atajo de teclado para imprimir
		document.addEventListener('DOMContentLoaded', function() {
			document.addEventListener('keydown', function(e) {
				if (e.ctrlKey && e.key === 'p') {
					e.preventDefault();
					window.print();
				}
			});
		});
	</script>

</body>

</html>