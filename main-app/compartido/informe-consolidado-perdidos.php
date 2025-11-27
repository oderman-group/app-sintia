<?php
// Configuraciones para reportes grandes
set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

include("session-compartida.php");
$idPaginaInterna = 'DT0226';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
?>

<head>
    <title>Estudiantes con Asignaturas Perdidas - SINTIA</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
    
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
		.container-perdidos {
			max-width: 100%;
			margin: 0 auto;
			padding: 30px;
			background-color: #fff;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		
		/* Tabla */
		.tabla-perdidos {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}
		.tabla-perdidos thead tr {
			background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
			color: #fff;
			font-weight: bold;
			height: 40px;
		}
		.tabla-perdidos thead th {
			padding: 12px 8px;
			text-align: center;
			border: 1px solid rgba(255,255,255,0.2);
			font-size: 11px;
		}
		.tabla-perdidos thead th.materia-vertical {
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
		.tabla-perdidos tbody tr {
			transition: background-color 0.2s ease;
		}
		.tabla-perdidos tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}
		.tabla-perdidos tbody tr:hover {
			background-color: #fff3cd;
		}
		.tabla-perdidos tbody td {
			padding: 10px 8px;
			border: 1px solid #ddd;
			font-size: 11px;
		}
		.tabla-perdidos tbody td.nota-cell {
			font-weight: bold;
			text-align: center;
			font-size: 12px;
			width: 45px;
			min-width: 45px;
			max-width: 45px;
			padding: 8px 4px;
		}
		.tabla-perdidos tbody td.nota-reprobada {
			background-color: #ffe6e6;
		}
		.tabla-perdidos tbody td.promedio-cell {
			font-weight: bold;
			text-align: center;
			font-size: 13px;
			background-color: #f0f0f0;
			width: 60px;
		}
		.tabla-perdidos tbody td.perdidas-cell {
			font-weight: bold;
			text-align: center;
			font-size: 13px;
			background-color: #e9ecef;
			width: 50px;
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
			.container-perdidos {
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
			.tabla-perdidos {
				page-break-inside: auto;
				box-shadow: none;
			}
			.tabla-perdidos thead {
				display: table-header-group;
			}
			.tabla-perdidos tr {
				page-break-inside: avoid;
				page-break-after: auto;
			}
			.tabla-perdidos thead tr {
				background: #dc3545 !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			.tabla-perdidos thead th.materia-vertical {
				writing-mode: vertical-rl;
				text-orientation: mixed;
				height: 100px;
			}
			.tabla-perdidos tbody td.nota-reprobada {
				background-color: #ffe6e6 !important;
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

<?php
// Obtener parámetros PRIMERO
$curso = "";
$grupo = "";
if(isset($_GET["curso"])){
    $curso = $_GET["curso"]; 
    $grupo = !empty($_GET["grupo"]) ? $_GET["grupo"] : "";
}
if(isset($_POST["grado"])){
    $curso = $_POST["grado"]; 
    $grupo = !empty($_POST["grupo"]) ? $_POST["grupo"] : "";
}

// Validar parámetros requeridos
if(empty($curso)){
    echo '<div class="container-perdidos" style="text-align: center; padding: 50px;">
        <h2>Error: Parámetros Incompletos</h2>
        <p>No se especificó el curso para generar el informe.</p>
        <button onclick="window.close()" class="btn-close" style="position: static;">Cerrar</button>
    </div></body></html>';
    exit();
}

// Pre-cargar datos del curso
$consultaCurso = Grados::obtenerDatosGrados($curso);
$datosCurso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);

// Pre-cargar todas las cargas/materias del curso (UNA SOLA VEZ)
$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $curso, $grupo);
$materiasArray = [];
$cargasIds = [];
while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
	$materiasArray[$carga['car_id']] = $carga;
	$cargasIds[] = $carga['car_id'];
}
$numCargasPorCurso = count($materiasArray);

// Pre-cargar TODAS las notas de todos los estudiantes y todas las cargas en UNA consulta
$notasCache = [];
if (!empty($cargasIds)) {
	// Entrecomillar cada ID para el IN clause
	$cargasIdsStr = "'" . implode("','", $cargasIds) . "'";
	$consultaNotas = mysqli_query($conexion, "
		SELECT bol_carga, bol_estudiante, bol_periodo, bol_nota 
		FROM ".BD_ACADEMICA.".academico_boletin 
		WHERE bol_carga IN ($cargasIdsStr) 
		AND institucion = {$config['conf_id_institucion']} 
		AND year = {$_SESSION["bd"]}
	");
	if($consultaNotas){
		while($nota = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH)){
			$key = $nota['bol_estudiante'].'_'.$nota['bol_carga'].'_'.$nota['bol_periodo'];
			$notasCache[$key] = $nota['bol_nota'];
		}
	}
}
?>

<div class="container-perdidos">
<?php
$nombreInforme = "ESTUDIANTES CON ASIGNATURAS PERDIDAS";
include("../compartido/head-informes.php");
?>

<table class="tabla-perdidos">
	<thead>
		<tr>
			<th>Mat</th>
			<th>Estudiante</th>
			<?php foreach($materiasArray as $materia){ ?>
				<th class="materia-vertical" title="<?=htmlspecialchars($materia['mat_nombre']);?>"><?=htmlspecialchars($materia['mat_nombre']);?></th>
			<?php } ?>
			<th>PROM</th>
			<th>#MP</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$filtroAdicional= "AND mat_grado='".$curso."' AND mat_grupo='".$grupo."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
		$consulta = Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$datosCurso,$grupo);
		
		while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
			$nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($resultado);
			$defPorEstudiante = 0;
			$materiasPerdidas = 0;
			$cargasPromedio = 0;
			?>
			<tr>
				<td><?=htmlspecialchars(!empty($resultado['mat_id']) ? $resultado['mat_id'] : '')?></td>
				<td><?=htmlspecialchars($nombreCompleto)?></td>
				<?php
				// Iterar por las materias pre-cargadas
				foreach($materiasArray as $carga){
					$p = 1;
					$porcPeriodo = array("", 0.25, 0.25, 0.25, 0.25);
					$defPorMateria = 0;
					
					// Calcular definitiva de la materia sumando todos los periodos
					while($p <= $config[19]){
						// Obtener nota del cache en lugar de consultar BD
						$keyNota = $resultado['mat_id'].'_'.$carga['car_id'].'_'.$p;
						$notaPeriodo = !empty($notasCache[$keyNota]) ? $notasCache[$keyNota] : 0;
						
						if(!empty($notaPeriodo)){
							$defPorMateria += ($notaPeriodo * $porcPeriodo[$p]);
						}
						$p++;
					}
					
					$defPorMateria = round($defPorMateria, 2);
					
					// Determinar color y si está perdida
					$color = ($defPorMateria < $config[5] && $defPorMateria != "") ? '#dc3545' : '#28a745';
					$claseReprobada = ($defPorMateria < $config[5] && $defPorMateria != "") ? 'nota-reprobada' : '';
					
					if($defPorMateria < $config[5] && $defPorMateria != ""){
						$materiasPerdidas++;
					}
					?>
					<td class="nota-cell <?=$claseReprobada?>" style="color:<?=$color?>;">
						<?=$defPorMateria != "" ? $defPorMateria : '-'?>
					</td>
					<?php
					// Sumar al promedio general si la materia lo requiere
					if (!empty($carga['mat_sumar_promedio']) && $carga['mat_sumar_promedio'] == SI) {
						$defPorEstudiante += $defPorMateria; 
						$cargasPromedio++;
					}
				}
				
				// Calcular promedio del estudiante
				$defPorEstudiante = ($cargasPromedio > 0) ? round($defPorEstudiante / $cargasPromedio, 2) : 0;
				$colorPromedio = ($defPorEstudiante < $config[5] && $defPorEstudiante != "") ? '#dc3545' : '#28a745';
				?>
				<td class="promedio-cell" style="color:<?=$colorPromedio?>;"><?=$defPorEstudiante?></td>
				<td class="perdidas-cell"><?=$materiasPerdidas?></td>
			</tr>
		<?php }?>
	</tbody>
</table>

</div> <!-- Cierre container-perdidos -->

<?php 
include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); 
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