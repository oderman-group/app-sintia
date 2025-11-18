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
	?>

	<table class="tabla-consolidado">
		<thead>
			<tr>
				<th rowspan="2">Mat</th>
				<th rowspan="2">Estudiante</th>
				<?php foreach ($codigosCargas as $carga) { ?>
					<th colspan="<?= $config[19] + 1; ?>">
						<?= htmlspecialchars($carga['nombre']); ?>
					</th>
				<?php } ?>
				<th rowspan="2">PROM</th>
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
					// DEFINITIVA DE CADA MATERIA
					echo '<th style="background:#fff3cd; color:#000;">DEF</th>';
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(!empty($estudiantes) && is_array($estudiantes)){
			foreach ($estudiantes as $estudiante) {
				$defPorEstudiante = 0;
				$numCargasPorCurso = 0;
				?>
				<tr>
					<td><?= htmlspecialchars(!empty($estudiante['mat_matricula']) ? $estudiante['mat_matricula'] : ''); ?></td>
					<td><?= htmlspecialchars(!empty($estudiante['nombre']) ? $estudiante['nombre'] : ''); ?></td>
					<?php foreach ($codigosCargas as $codigo) {
						if(!empty($estudiante["areas"]) && is_array($estudiante["areas"])){
							foreach ($estudiante["areas"] as $area) {
								$buscarCarga = isset($area["cargas"][$codigo["car_id"]]) ? $area["cargas"][$codigo["car_id"]] : "";
								$defPorMateria = 0;
								if (!empty($buscarCarga)) {
									$numCargasPorCurso++;
									foreach ($periodosArray as $periodo) {
										$boletin = isset($buscarCarga["periodos"][$periodo]) ? $buscarCarga["periodos"][$periodo] : "";
										if (!empty($boletin["bol_nota"])) {
											$notaValor = $boletin["bol_nota"];
											$defPorMateria += ($notaValor * $porcPeriodo[$periodo]);
											
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
											continue;
										} else { ?>
											<td class="nota-cell"> </td>
										<?php }
									}
									// OPTIMIZACIÓN: Calcular color directamente
									$color = ($defPorMateria < $notaMinima) ? $colorCache['perdida'] : $colorCache['ganada'];
									
									// OPTIMIZACIÓN: Formatear nota usando cache
									$title = '';
									if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
										$title = 'title="Nota Cuantitativa: ' . $defPorMateria . '"';
										$defPorMateriaRedondeada = number_format((float)$defPorMateria, 1, '.', '');
										$defPorMateriaFormat = isset($notasCualitativasCache[$defPorMateriaRedondeada]) 
											? $notasCualitativasCache[$defPorMateriaRedondeada] 
											: Boletin::formatoNota($defPorMateria, $tiposNotas);
									} else {
										$defPorMateriaFormat = Boletin::notaDecimales($defPorMateria);
									}
									?>
									<td class="definitiva-cell" style="color:<?= $color; ?>;" <?= $title; ?>>
										<?= htmlspecialchars($defPorMateriaFormat, ENT_QUOTES, 'UTF-8'); ?>
									</td>
									<?php
									$defPorEstudiante += $defPorMateria;
									continue;
								}
							}
						}
					}
					$prom = 0;
					if ($numCargasPorCurso > 0) {
						$prom = ($defPorEstudiante / $numCargasPorCurso);
					}
					// OPTIMIZACIÓN: Calcular color directamente
					$color = ($prom < $notaMinima) ? $colorCache['perdida'] : $colorCache['ganada'];
					$prom = round($prom, $config['conf_decimales_notas']);
					$prom = number_format($prom, $config['conf_decimales_notas']);
					?>
					<td class="promedio-cell" style="color:<?= $color; ?>;"><?= htmlspecialchars($prom, ENT_QUOTES, 'UTF-8'); ?></td>
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