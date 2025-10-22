<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}

require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Plataforma.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
require_once(ROOT_PATH . "/main-app/class/Calificaciones.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once ROOT_PATH.'/librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$Plataforma = new Plataforma;

$year = $_SESSION["bd"];
if (isset($_GET["year"])) {
	$year = base64_decode($_GET["year"]);
} elseif (isset($_POST["year"])) {
	$year = $_POST["year"];
}

$periodoActual = 4;
if (isset($_GET["periodo"])) {
	$periodoActual = base64_decode($_GET["periodo"]);
} elseif (isset($_POST["periodo"])) {
	$periodoActual = $_POST["periodo"];
}

switch ($periodoActual) {
	case 1:
		$periodoActuales = "Primero";
		break;
	case 2:
		$periodoActuales = "Segundo";
		break;
	case 3:
		$periodoActuales = "Tercero";
		break;
	case 4:
		$periodoActuales = "Final";
		break;
	case 5:
		$periodoActual = 4;
		$periodoActuales = "Final";
		break;
}

//CONSULTA ESTUDIANTES MATRICULADOS
$curso = '';
if (isset($_GET["curso"])) {
	$curso = base64_decode($_GET["curso"]);
} elseif (isset($_POST["curso"])) {
	$curso = $_POST["curso"];
}

$grupo = '';
if (isset($_GET["grupo"])) {
	$grupo = base64_decode($_GET["grupo"]);
} elseif (isset($_POST["grupo"])) {
	$grupo = $_POST["grupo"];
}

$id = '';
if (isset($_GET["id"])) {
	$id = base64_decode($_GET["id"]);
} elseif (isset($_POST["id"])) {
	$id = $_POST["id"];
}

if (!empty($id)) {
	$filtro               = " AND mat_id='" . $id . "'";
	$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
	$estudiante           = $matriculadosPorCurso->fetch_assoc();
	if (!empty($estudiante)) {
		$idEstudiante = $estudiante["mat_id"];
		$curso        = $estudiante["mat_grado"];
		$grupo        = $estudiante["mat_grupo"];
	}
}

$periodoFinal = $config["conf_periodos_maximos"];

$tiposNotas         = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
	$tiposNotas[] = $row;
}

$listaDatos = [];
$estudiantes = [];

if (!empty($curso) && !empty($periodoFinal) && !empty($year)) {
	// PRIMERO: Obtener TODOS los estudiantes matriculados
	$filtroEstudiantes = " AND mat_grado='$curso' AND mat_eliminado=0";
	if (!empty($grupo)) {
		$filtroEstudiantes .= " AND mat_grupo='$grupo'";
	}
	if (!empty($id)) {
		$filtroEstudiantes .= " AND mat_id='$id'";
	}
	
	$matriculados = Estudiantes::estudiantesMatriculados($filtroEstudiantes, $year);
	
	while ($est = mysqli_fetch_array($matriculados, MYSQLI_BOTH)) {
		$nombreCompleto = Estudiantes::NombreCompletoDelEstudiante($est);
		
		$estudiantes[$est['mat_id']] = [
			'mat_id' => $est['mat_id'],
			'mat_matricula' => $est['mat_matricula'],
			'mat_numero_matricula' => $est['mat_numero_matricula'],
			'mat_folio' => $est['mat_folio'],
			'nombre' => $nombreCompleto,
			'gra_id' => $est['mat_grado'],
			'gra_nombre' => $est['gra_nombre'],
			'gru_nombre' => isset($est['gru_nombre']) ? $est['gru_nombre'] : '',
			'periodos' => $periodoFinal,
			'areas' => []
		];
	}
	
	// SEGUNDO: Buscar calificaciones
	if (count($estudiantes) > 0) {
		$periodosArray = [];
		for ($i = 1; $i <= $periodoFinal; $i++) {
			$periodosArray[$i] = $i;
		}
		$datos = Boletin::datosBoletin($curso, $grupo, $periodosArray, $year, $id, false);
		if ($datos) {
			while ($row = $datos->fetch_assoc()) {
				$listaDatos[] = $row;
			}
		}
		if (count($listaDatos) > 0) {
			include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
		}
	}
	
	// Convertir a array indexado
	if (count($estudiantes) > 0) {
		$estudiantes = array_values($estudiantes);
	}
}

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configurar encabezado
$sheet->setCellValue('A1', strtoupper($informacion_inst["info_nombre"]));
$sheet->mergeCells('A1:H1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'REGISTRO DE VALORACIÓN - LIBRO FINAL');
$sheet->mergeCells('A2:H2');
$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$fila = 4;

foreach ($estudiantes as $estudiante) {
	$nombre = Estudiantes::NombreCompletoDelEstudiante($estudiante);
	$materiasPerdidas = 0;
	
	// Información del estudiante
	$sheet->setCellValue('A' . $fila, 'Código:');
	$sheet->setCellValue('B' . $fila, $estudiante["mat_matricula"]);
	$sheet->setCellValue('C' . $fila, 'Nombre:');
	$sheet->setCellValue('D' . $fila, $nombre);
	$sheet->setCellValue('F' . $fila, 'Matrícula:');
	$sheet->setCellValue('G' . $fila, $estudiante["mat_numero_matricula"]);
	$sheet->getStyle('A'.$fila.':G'.$fila)->getFont()->setBold(true);
	$fila++;
	
	$sheet->setCellValue('A' . $fila, 'Grado:');
	$sheet->setCellValue('B' . $fila, $estudiante["gra_nombre"] . " " . $estudiante["gru_nombre"]);
	$sheet->setCellValue('C' . $fila, 'Periodo:');
	$sheet->setCellValue('D' . $fila, strtoupper($periodoActuales));
	$sheet->setCellValue('F' . $fila, 'Folio:');
	$sheet->setCellValue('G' . $fila, $estudiante["mat_folio"]);
	$sheet->getStyle('A'.$fila.':G'.$fila)->getFont()->setBold(true);
	$fila++;
	$fila++;
	
	// Encabezados de tabla
	$sheet->setCellValue('A' . $fila, 'ÁREAS/ASIGNATURAS');
	$sheet->setCellValue('B' . $fila, 'I.H');
	$sheet->setCellValue('C' . $fila, 'DEF');
	$sheet->setCellValue('D' . $fila, 'DESEMPEÑO');
	$sheet->setCellValue('E' . $fila, 'AUS');
	$sheet->setCellValue('F' . $fila, 'OBSERVACIONES');
	
	$sheet->getStyle('A'.$fila.':F'.$fila)->getFill()
		->setFillType(Fill::FILL_SOLID)
		->getStartColor()->setARGB('FF3498db');
	$sheet->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
	$sheet->getStyle('A'.$fila.':F'.$fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
	$fila++;
	
	// Datos de áreas y materias
	if (isset($estudiante["areas"]) && count($estudiante["areas"]) > 0) {
		foreach ($estudiante["areas"] as $area) {
		// Fila de área
		$sheet->setCellValue('A' . $fila, $area["ar_nombre"]);
		
		$notaArea = $area["nota_area_acumulada"];
		if ($estudiante["gra_id"] > 11 && $config['conf_id_institucion'] != EOA_CIRUELOS) {
			$notaFA = ceil($notaArea);
			$notaAreaTexto = "";
			switch ($notaFA) {
				case 1: $notaAreaTexto = "D"; break;
				case 2: $notaAreaTexto = "I"; break;
				case 3: $notaAreaTexto = "A"; break;
				case 4: $notaAreaTexto = "S"; break;
				case 5: $notaAreaTexto = "E"; break;
			}
			$sheet->setCellValue('C' . $fila, $notaAreaTexto);
		} else {
			$sheet->setCellValue('C' . $fila, Boletin::formatoNota($notaArea));
		}
		
		$sheet->getStyle('A'.$fila.':F'.$fila)->getFill()
			->setFillType(Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFECF0F1');
		$sheet->getStyle('A'.$fila.':F'.$fila)->getFont()->setBold(true);
		$fila++;
		
		// Materias del área
		foreach ($area["cargas"] as $carga) {
			$notaCarga = $carga["nota_carga_acumulada"];
			
			if ($notaCarga < $config['conf_nota_minima_aprobar']) {
				$materiasPerdidas++;
			}
			
			$notaCargaDeseno = Boletin::determinarRango($notaCarga, $tiposNotas);
			Utilidades::valordefecto($notaCargaDeseno["notip_desde"],0);
			Utilidades::valordefecto($notaCargaDeseno["notip_hasta"],1);
			
			if ($notaCarga >= $notaCargaDeseno["notip_desde"] && $notaCarga <= $notaCargaDeseno["notip_hasta"]) {
				if ($estudiante["gra_id"] > 11 && $config['conf_id_institucion'] != EOA_CIRUELOS) {
					$notaFD = ceil($notaCarga);
					switch ($notaFD) {
						case 1: $notaCarga = "D"; $notaCargaDeseno["notip_nombre"] = "BAJO"; break;
						case 2: $notaCarga = "I"; $notaCargaDeseno["notip_nombre"] = "BAJO"; break;
						case 3: $notaCarga = "A"; $notaCargaDeseno["notip_nombre"] = "BÁSICO"; break;
						case 4: $notaCarga = "S"; $notaCargaDeseno["notip_nombre"] = "ALTO"; break;
						case 5: $notaCarga = "E"; $notaCargaDeseno["notip_nombre"] = "SUPERIOR"; break;
					}
				}
			}
			
			$sheet->setCellValue('A' . $fila, '  ' . $carga["mat_nombre"]);
			$sheet->setCellValue('B' . $fila, $carga["car_ih"]);
			$sheet->setCellValue('C' . $fila, Boletin::formatoNota(3));
			$sheet->setCellValue('D' . $fila, $notaCargaDeseno["notip_nombre"]);
			$sheet->setCellValue('E' . $fila, $carga["fallas"]);
			
			$sheet->getStyle('B'.$fila.':E'.$fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$fila++;
		}
		}
	} else {
		// Si no hay áreas, mostrar mensaje
		$sheet->setCellValue('A' . $fila, 'No hay calificaciones registradas para este estudiante');
		$sheet->mergeCells('A'.$fila.':F'.$fila);
		$sheet->getStyle('A'.$fila)->getFont()->setItalic(true);
		$sheet->getStyle('A'.$fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$fila++;
	}
	
	$fila++;
	
	// Mensaje de promoción
	$msj = "";
	if (isset($estudiante["periodos"]) && $estudiante["periodos"] == $config["conf_periodos_maximos"]) {
		if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
			$msj = "EL (LA) ESTUDIANTE " . strtoupper($estudiante["nombre"]) . " NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
		} elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] and $materiasPerdidas > 0) {
			$msj = "EL (LA) ESTUDIANTE " . strtoupper($estudiante["nombre"]) . " DEBE NIVELAR LAS MATERIAS PERDIDAS";
		} else {
			$msj = "EL (LA) ESTUDIANTE " . strtoupper($estudiante["nombre"]) . " FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
		}
		
		if (isset($estudiante['mat_estado_matricula']) && $estudiante['mat_estado_matricula'] == CANCELADO) {
			$msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
		}
	} elseif (!isset($estudiante["areas"]) || count($estudiante["areas"]) == 0) {
		$msj = "No se han registrado calificaciones para este estudiante en el periodo actual.";
	}
	
	$sheet->setCellValue('A' . $fila, $msj);
	$sheet->mergeCells('A'.$fila.':F'.$fila);
	$sheet->getStyle('A'.$fila)->getFont()->setBold(true)->setItalic(true);
	$fila += 3;
}

// Ajustar anchos de columna
$sheet->getColumnDimension('A')->setWidth(40);
$sheet->getColumnDimension('B')->setWidth(8);
$sheet->getColumnDimension('C')->setWidth(10);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(8);
$sheet->getColumnDimension('F')->setWidth(30);

// Descargar archivo
$filename = 'Libro_Final_' . $curso . '_' . $grupo . '_' . date('Y-m-d') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>

