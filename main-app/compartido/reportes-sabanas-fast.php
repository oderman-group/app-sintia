<?php
// Configuraciones para manejo de reportes grandes
set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

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
foreach ($consultaPuestos as $puesto) {
	$puestosCurso[$puesto['estudiante_id']] = $puesto['puesto'];
}

$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
	$tiposNotas[] = $row;
}

$listaDatos = [];
$estudiantes = [];
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
$materias = [];
$materias1 = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo, $year, "", "ar_posicion,car_id");
if (!empty($materias1)) {
	while ($row = $materias1->fetch_assoc()) {
		$materias[$row["car_id"]] = $row;
		$numeroMaterias ++;
	}
}

?>

<head>
	<title>Informe de Sábanas - SINTIA</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
		.container-sabanas {
			max-width: 100%;
			margin: 0 auto;
			padding: 30px;
			background-color: #fff;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		
		/* Tablas */
		.tabla-sabanas {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}
		.tabla-sabanas thead tr {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: #fff;
			font-weight: bold;
			height: 40px;
		}
		.tabla-sabanas thead th {
			padding: 12px 8px;
			text-align: center;
			border: 1px solid rgba(255,255,255,0.2);
			font-size: 11px;
		}
		.tabla-sabanas thead th.materia-vertical {
			writing-mode: vertical-rl;
			text-orientation: mixed;
			padding: 50px 8px 12px 8px;
			width: 45px;
			min-width: 45px;
			max-width: 45px;
			height: 120px;
			vertical-align: bottom;
			text-align: center;
		}
		.tabla-sabanas tbody tr {
			transition: background-color 0.2s ease;
		}
		.tabla-sabanas tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		.tabla-sabanas tbody tr:hover {
			background-color: #e8f4f8;
		}
		.tabla-sabanas tbody td {
			padding: 10px 8px;
			border: 1px solid #ddd;
			font-size: 11px;
		}
		.tabla-sabanas tbody td.nota-cell {
			font-weight: bold;
			text-align: center;
			font-size: 12px;
			width: 45px;
			min-width: 45px;
			max-width: 45px;
			padding: 8px 4px;
		}
		.tabla-sabanas tbody td.promedio-cell {
			font-weight: bold;
			text-align: center;
			font-size: 13px;
			background-color: #f0f0f0;
		}
		
		/* Tabla de puestos */
		.tabla-puestos {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}
		.tabla-puestos thead tr {
			background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
			color: #fff;
			font-weight: bold;
			height: 40px;
		}
		.tabla-puestos thead th {
			padding: 12px;
			text-align: center;
			border: 1px solid rgba(255,255,255,0.2);
		}
		.tabla-puestos tbody tr {
			height: 50px;
			font-size: 13px;
		}
		.tabla-puestos tbody td {
			padding: 12px;
			border: 1px solid #ddd;
		}
		.puesto-primero {
			background-color: #fff9e6;
			border-left: 4px solid #d4af37;
			font-weight: bold;
		}
		.puesto-segundo {
			background-color: #f5f5f5;
			border-left: 4px solid #a8a9ad;
			font-weight: bold;
		}
		.puesto-tercero {
			background-color: #fef5f1;
			border-left: 4px solid #cd7f32;
			font-weight: bold;
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
		
		/* Estilos de impresión */
		@media print {
			body {
				margin: 0;
				background-color: white;
				padding: 0;
			}
			.container-sabanas {
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
			.tabla-sabanas, .tabla-puestos {
				page-break-inside: auto;
				box-shadow: none;
			}
			.tabla-sabanas thead, .tabla-puestos thead {
				display: table-header-group;
			}
			.tabla-sabanas tr, .tabla-puestos tr {
				page-break-inside: avoid;
				page-break-after: auto;
			}
			.tabla-sabanas thead tr, .tabla-puestos thead tr {
				background: #667eea !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			.tabla-sabanas thead th.materia-vertical {
				writing-mode: vertical-rl;
				text-orientation: mixed;
				height: 100px;
			}
			.puesto-primero {
				background-color: #fff9e6 !important;
				border-left: 4px solid #d4af37 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			.puesto-segundo {
				background-color: #f5f5f5 !important;
				border-left: 4px solid #a8a9ad !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			.puesto-tercero {
				background-color: #fef5f1 !important;
				border-left: 4px solid #cd7f32 !important;
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

	<div class="container-sabanas">
	<?php
	$gradoNombre = !empty($grados["gra_nombre"]) ? $grados["gra_nombre"] : 'N/A';
	$grupoNombre = !empty($grados["gru_nombre"]) ? $grados["gru_nombre"] : 'N/A';
	$nombreInforme = "INFORME DE SABANAS" . "<br>" . "PERIODO " . $periodoActual . "<br>" . $gradoNombre . " " . $grupoNombre . " " . $year;
	include("../compartido/head-informes.php") ?>


	<table class="tabla-sabanas">
		<thead>
			<tr>
				<th>No</th>
				<th>ID</th>
				<th>Estudiante</th>
				<?php foreach ($materias as $materia) { ?>
					<th class="materia-vertical" title="<?= htmlspecialchars($materia['mat_nombre']); ?>"><?= htmlspecialchars($materia['mat_siglas']); ?></th>
				<?php }	?>
				<th>PROM</th>
			</tr>
		</thead>
		<tbody>


		<?php 
		if (!empty($estudiantes)) {
			foreach ($estudiantes as $estudiante) { ?>
			<tr>
				<td align="center"><?= !empty($estudiante["nro"]) ? $estudiante["nro"] : ''; ?></td>
				<td align="center"><?= !empty($estudiante["mat_id"]) ? $estudiante["mat_id"] : ''; ?></td>
				<td><?= !empty($estudiante["nombre"]) ? htmlspecialchars($estudiante["nombre"]) : ''; ?></td>
				<?php 
				$sumaDefini = 0; 
				if (!empty($estudiante["areas"]) && is_array($estudiante["areas"])) {
					foreach ($estudiante["areas"] as $area) {
						if (!empty($area["cargas"]) && is_array($area["cargas"])) {
							foreach ($area["cargas"] as $carga) {
								$recupero = false;
								$defini = 0;
								if (!empty($carga["periodos"][$periodoActual]['bol_nota'])) {
									$defini = $carga["periodos"][$periodoActual]['bol_nota'];
								}
								$title = '';
								if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
									$title = 'title="Nota Cuantitativa: ' . $defini . '"';
								}
								$sumaDefini += $defini;
								$color = ($defini < $config[5]) ? '#dc3545' : '#28a745';
								?>
								<td class="nota-cell" style="color:<?= $color; ?>;" <?= $title; ?>>
									<?= Boletin::formatoNota(!empty($carga["periodos"][$periodoActual]['bol_nota']) ? $carga["periodos"][$periodoActual]['bol_nota'] : 0, $tiposNotas); ?>
								</td>
							<?php 
							}
						}
					}
				} 
				
				// Calcular promedio
				$promedio = $numeroMaterias > 0 ? round($sumaDefini / $numeroMaterias, 2) : 0;
				$notas1[$estudiante["nro"]] = $promedio;
				$grupo1[$estudiante["nro"]] = $estudiante["nombre"];
				
				$colorPromedio = ($promedio < $config[5]) ? '#dc3545' : '#28a745';
				$titlePromedio = '';
				if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
					$titlePromedio = 'title="Nota Cuantitativa: ' . $promedio . '"';
				}
				?>
				<td class="promedio-cell" style="color:<?= $colorPromedio; ?>;" <?= $titlePromedio; ?>>
					<?= Boletin::formatoNota($promedio, $tiposNotas); ?>
				</td>
			</tr>
		<?php 
			} // Cierre foreach estudiantes
		} // Cierre if estudiantes
		?>
		</tbody>


	</table>

	<table class="tabla-puestos">
		<thead>
			<tr>
				<th colspan="4">PRIMEROS PUESTOS</th>
			</tr>
			<tr>
				<th>No</th>
				<th>Estudiante</th>
				<th>Promedio</th>
				<th>Puesto</th>
			</tr>
		</thead>
		<tbody>
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
				
				$classPuesto = '';
				$puesto = '';
				if ($cambios == 1) {
					$classPuesto = 'puesto-primero';
					$puesto = '🥇 Primero';
				} elseif ($cambios == 2) {
					$classPuesto = 'puesto-segundo';
					$puesto = '🥈 Segundo';
				} elseif ($cambios == 3) {
					$classPuesto = 'puesto-tercero';
					$puesto = '🥉 Tercero';
				} elseif ($cambios == 4) {
					break;
				}

				$valTotal = $val;
				$title = '';
				if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
					$title = 'title="Nota Cuantitativa: ' . $val . '"';
					$estiloNota = Boletin::determinarRango($val, $tiposNotas);
					$valTotal = !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
				}
		?>
				<tr class="<?= $classPuesto; ?>">
					<td align="center"><?= $j; ?></td>
					<td><?= htmlspecialchars($grupo1[$key]); ?></td>
					<td align="center" <?= $title; ?>><?= Boletin::formatoNota($valTotal, $tiposNotas); ?></td>
					<td align="center"><?= $puesto; ?></td>
				</tr>
		<?php
				$j++;
			}
		}
		?>
		</tbody>


	</table>


	</div> <!-- Cierre container-sabanas -->

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