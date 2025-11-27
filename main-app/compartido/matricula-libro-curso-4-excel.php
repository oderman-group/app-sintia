<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if ($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])) {
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
    exit();
}

require_once(ROOT_PATH . "/main-app/class/componentes/Excel/ExcelUtil.php");
require_once(ROOT_PATH . "/main-app/class/Boletin.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/Tables/BDT_configuracion.php");
require_once(ROOT_PATH . "/main-app/class/Instituciones.php");

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$year = $_SESSION["bd"];

if (isset($_POST["year"])) {
    $year = base64_decode($_POST["year"]);
}

$periodoFinal = $config['conf_periodos_maximos'];

$grado = 1;
if (isset($_POST["curso"])) {
    $grado = base64_decode($_POST["curso"]);
}

$grupo = 1;
if (!empty($_POST["grupo"])) {
    $grupo = base64_decode($_POST["grupo"]);
}

$idEstudiante = '';
if (isset($_POST["id"])) {
    $idEstudiante = base64_decode($_POST["id"]);
}

if (!empty($idEstudiante)) {
    $filtro = " AND mat_id='" . $idEstudiante . "'";
    $matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
    $estudiante = $matriculadosPorCurso->fetch_assoc();
    if (!empty($estudiante)) {
        $idEstudiante = $estudiante["mat_id"];
        $grado = $estudiante["mat_grado"];
        $grupo = $estudiante["mat_grupo"];
    }
}

// Obtener información de la institución para el año consultado
try {
    $informacionInstYear = Instituciones::getGeneralInformationFromInstitution($config['conf_id_institucion'], $year);
} catch (Exception $e) {
    $informacionInstYear = $_SESSION["informacionInstConsulta"];
}

// Consultas iniciales
$listaDatos = [];
$tiposNotas = [];
$cosnultaTiposNotas = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while ($row = $cosnultaTiposNotas->fetch_assoc()) {
    $tiposNotas[] = $row;
}

if (!empty($grado) && !empty($grupo) && !empty($periodoFinal) && !empty($year)) {
    $periodos = [];
    for ($i = 1; $i <= $periodoFinal; $i++) {
        $periodos[$i] = $i;
    }
    $datos = Boletin::datosBoletin($grado, $grupo, $periodos, $year, $idEstudiante);
    while ($row = $datos->fetch_assoc()) {
        $listaDatos[] = $row;
    }
    include("../compartido/agrupar-datos-boletin-periodos-mejorado.php");
}

if ($grado >= 12 && $grado <= 15) {
    $educacion = "PREESCOLAR";
} elseif ($grado >= 1 && $grado <= 5) {
    $educacion = "PRIMARIA";
} elseif ($grado >= 6 && $grado <= 9) {
    $educacion = "SECUNDARIA";
} elseif ($grado >= 10 && $grado <= 11) {
    $educacion = "MEDIA";
}

// Crear instancia de Excel
$excelUtil = new ExcelUtil("Libro Final");
$indice = 0;
$primeraHoja = true;

foreach ($estudiantes as $estudiante) {
    $totalNotasPeriodo = [];
    
    if (!$primeraHoja) {
        // Crear nueva hoja para cada estudiante
        $nombreHoja = substr($estudiante["nombre"], 0, 31); // Excel limita a 31 caracteres
        $excelUtil->agregarHoja($nombreHoja);
        $indice++;
    } else {
        $primeraHoja = false;
        $nombreHoja = substr($estudiante["nombre"], 0, 31);
        $excelUtil->sheet[$indice]->setTitle($nombreHoja);
    }
    
    $sheet = $excelUtil->sheet[$indice];
    
    // ENCABEZADO
    $fila = 1;
    
    // Logo y nombre de institución
    $sheet->mergeCells('A' . $fila . ':B' . ($fila + 2));
    $urlImage = '../files/images/logo/' . $informacionInstYear["info_logo"];
    if (!file_exists(ROOT_PATH . '/main-app/files/images/logo/' . $informacionInstYear["info_logo"])) {
        $urlImage = '../files/images/logo/sintia-logo-2023.png';
    }
    $excelUtil->agregarImagenLogo('A' . $fila, $urlImage, 25, 2);
    
    // Nombre de institución
    $sheet->mergeCells('C' . $fila . ':D' . ($fila + 2));
    $sheet->setCellValue('C' . $fila, strtoupper($informacionInstYear["info_nombre"]));
    $sheet->getStyle('C' . $fila)->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle('C' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
    $fila++;
    $sheet->setCellValue('C' . $fila, $informacionInstYear["info_direccion"]);
    $sheet->getStyle('C' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $fila++;
    $sheet->setCellValue('C' . $fila, 'Informes: ' . $informacionInstYear["info_telefono"]);
    $sheet->getStyle('C' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Documento y Nombre del estudiante
    $fila = 1;
    $sheet->setCellValue('E' . $fila, 'Documento:');
    $sheet->getStyle('E' . $fila)->getFont()->setBold(true);
    $documento = strpos($estudiante["mat_documento"], '.') !== true && is_numeric($estudiante["mat_documento"]) ? number_format($estudiante["mat_documento"], 0, ",", ".") : $estudiante["mat_documento"];
    $sheet->setCellValue('F' . $fila, $documento);
    $sheet->getStyle('F' . $fila)->getFont()->setBold(true);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getStyle('F' . $fila)->getAlignment()->setWrapText(true);
    
    $sheet->setCellValue('G' . $fila, 'Nombre:');
    $sheet->getStyle('G' . $fila)->getFont()->setBold(true);
    $sheet->setCellValue('H' . $fila, $estudiante["nombre"]);
    $sheet->getStyle('H' . $fila)->getFont()->setBold(true);
    $sheet->getColumnDimension('H')->setWidth(30);
    
    $fila++;
    $sheet->setCellValue('E' . $fila, 'Curso:');
    $sheet->getStyle('E' . $fila)->getFont()->setBold(true);
    $sheet->setCellValue('F' . $fila, strtoupper($estudiante["gra_nombre"]));
    $sheet->getStyle('F' . $fila)->getFont()->setBold(true);
    
    $sheet->setCellValue('G' . $fila, 'Sede:');
    $sheet->getStyle('G' . $fila)->getFont()->setBold(true);
    $sheet->setCellValue('H' . $fila, strtoupper($informacionInstYear["info_nombre"]));
    $sheet->getStyle('H' . $fila)->getFont()->setBold(true);
    
    $fila++;
    $sheet->setCellValue('E' . $fila, 'Jornada:');
    $sheet->getStyle('E' . $fila)->getFont()->setBold(true);
    $sheet->setCellValue('F' . $fila, strtoupper($informacionInstYear["info_jornada"]));
    $sheet->getStyle('F' . $fila)->getFont()->setBold(true);
    
    $sheet->setCellValue('G' . $fila, 'Documento:');
    $sheet->getStyle('G' . $fila)->getFont()->setBold(true);
    $sheet->setCellValue('H' . $fila, 'BOLETÍN DEFINITIVO DE NOTAS - EDUCACIÓN BÁSICA ' . strtoupper($educacion));
    $sheet->getStyle('H' . $fila)->getFont()->setBold(true);
    
    // Aplicar bordes al encabezado
    $sheet->getStyle('A1:H3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    $fila = 5;
    
    // Título de rangos de notas
    $consultaEstiloNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
    $numEstiloNota = mysqli_num_rows($consultaEstiloNota);
    $i = 1;
    $textoRangos = '';
    while ($estiloNota = mysqli_fetch_array($consultaEstiloNota, MYSQLI_BOTH)) {
        $diagonal = " / ";
        if ($i == $numEstiloNota) {
            $diagonal = "";
        }
        $textoRangos .= $estiloNota['notip_nombre'] . ": " . $estiloNota['notip_desde'] . " - " . $estiloNota['notip_hasta'] . $diagonal;
        $i++;
    }
    $sheet->mergeCells('A' . $fila . ':H' . $fila);
    $sheet->setCellValue('A' . $fila, $textoRangos);
    $sheet->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $fila += 2;
    
    // Año lectivo
    $sheet->mergeCells('A' . $fila . ':H' . $fila);
    $sheet->setCellValue('A' . $fila, 'AÑO LECTIVO: ' . $year);
    $sheet->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $fila += 2;
    
    // TABLA DE NOTAS
    $columnaInicio = 'A';
    $columnaFin = chr(ord('A') + 6 + $periodoFinal); // A + ASIGNATURAS + I.H + periodos + DEF + Desempeño
    
    // Encabezado de tabla
    $sheet->setCellValue($columnaInicio . $fila, 'ASIGNATURAS');
    $sheet->getStyle($columnaInicio . $fila)->getFont()->setBold(true);
    $sheet->getStyle($columnaInicio . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension($columnaInicio)->setWidth(25);
    
    $col = chr(ord($columnaInicio) + 1);
    $sheet->setCellValue($col . $fila, 'I.H');
    $sheet->getStyle($col . $fila)->getFont()->setBold(true);
    $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension($col)->setWidth(8);
    
    // Encabezado de periodos
    $sheet->mergeCells(chr(ord($col) + 1) . $fila . ':' . chr(ord($col) + $periodoFinal) . $fila);
    $sheet->setCellValue(chr(ord($col) + 1) . $fila, 'Periodo Cursados');
    $sheet->getStyle(chr(ord($col) + 1) . $fila)->getFont()->setBold(true);
    $sheet->getStyle(chr(ord($col) + 1) . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle(chr(ord($col) + 1) . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00ADEF');
    
    $fila++;
    // Números de periodos
    for ($i = 1; $i <= $periodoFinal; $i++) {
        $col = chr(ord($columnaInicio) + 1 + $i);
        $sheet->setCellValue($col . $fila, $i);
        $sheet->getStyle($col . $fila)->getFont()->setBold(true);
        $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00ADEF');
        $sheet->getColumnDimension($col)->setWidth(8);
    }
    
    // Columnas DEF y Desempeño
    $col = chr(ord($columnaInicio) + 1 + $periodoFinal + 1);
    $sheet->setCellValue($col . $fila, 'DEF');
    $sheet->getStyle($col . $fila)->getFont()->setBold(true);
    $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension($col)->setWidth(8);
    
    $col = chr(ord($col) + 1);
    $sheet->setCellValue($col . $fila, 'Desempeño');
    $sheet->getStyle($col . $fila)->getFont()->setBold(true);
    $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension($col)->setWidth(12);
    
    $fila++;
    
    // Datos de asignaturas
    $cantidadAreas = 0;
    $materiasPerdidas = 0;
    
    foreach ($estudiante["areas"] as $area) {
        $cantidadAreas++;
        $ihArea = 0;
        $notaAre = [];
        
        foreach ($area["cargas"] as $carga) {
            $promedioMateria = 0;
            $ihArea += $carga['car_ih'];
            
            $nombre = count($area["cargas"]) > 1 ? $carga["mat_nombre"] : $area["ar_nombre"];
            
            $sheet->setCellValue($columnaInicio . $fila, $nombre);
            $sheet->getStyle($columnaInicio . $fila)->getAlignment()->setWrapText(true);
            
            $col = chr(ord($columnaInicio) + 1);
            $sheet->setCellValue($col . $fila, $carga['car_ih']);
            $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            for ($j = 1; $j <= $periodoFinal; $j++) {
                $nota = isset($carga["periodos"][$j]["bol_nota"]) ? $carga["periodos"][$j]["bol_nota"] : 0;
                $nota = Boletin::agregarDecimales($nota);
                $promedioMateria += $nota;
                $porcentajeMateria = !empty($carga['mat_valor']) ? $carga['mat_valor'] : 100;
                
                if (isset($notaAre[$j])) {
                    $notaAre[$j] += $nota * ($porcentajeMateria / 100);
                } else {
                    $notaAre[$j] = $nota * ($porcentajeMateria / 100);
                }
                
                if (isset($totalNotasPeriodo[$j])) {
                    $totalNotasPeriodo[$j] += $nota * ($porcentajeMateria / 100);
                } else {
                    $totalNotasPeriodo[$j] = $nota * ($porcentajeMateria / 100);
                }
                
                $col = chr(ord($columnaInicio) + 1 + $j);
                if ($nota > 0) {
                    $sheet->setCellValue($col . $fila, number_format($nota, $config['conf_decimales_notas']));
                }
                $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('9ED8ED');
            }
            
            $periodoCalcular = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? COUNT($carga["periodos"]) : $config["conf_periodos_maximos"];
            $notaAcumulada = $promedioMateria / $periodoCalcular;
            $notaAcumulada = round($notaAcumulada, $config['conf_decimales_notas']);
            $desempenoAcumulado = Boletin::determinarRango($notaAcumulada, $tiposNotas);
            
            if ($notaAcumulada < $config['conf_nota_minima_aprobar']) {
                $materiasPerdidas++;
            }
            
            $col = chr(ord($columnaInicio) + 1 + $periodoFinal + 1);
            if ($notaAcumulada > 0) {
                $sheet->setCellValue($col . $fila, $notaAcumulada);
            }
            $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $col = chr(ord($col) + 1);
            if ($notaAcumulada > 0) {
                $sheet->setCellValue($col . $fila, $desempenoAcumulado["notip_nombre"]);
            }
            $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $fila++;
        }
        
        // Promedio del área si tiene múltiples cargas
        if ($ihArea != $carga['car_ih'] && count($area["cargas"]) > 1) {
            $sheet->setCellValue($columnaInicio . $fila, $area["ar_nombre"]);
            $sheet->getStyle($columnaInicio . $fila)->getFont()->setBold(true);
            $sheet->getStyle($columnaInicio . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
            
            $col = chr(ord($columnaInicio) + 1);
            $sheet->setCellValue($col . $fila, $ihArea);
            $sheet->getStyle($col . $fila)->getFont()->setBold(true);
            $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
            
            $notaAreAcumulada = 0;
            $periodoAreaCalcular = $config["conf_periodos_maximos"];
            
            for ($j = 1; $j <= $periodoFinal; $j++) {
                $notaAreAcumulada += $notaAre[$j];
                $col = chr(ord($columnaInicio) + 1 + $j);
                if ($notaAre[$j] > 0) {
                    $sheet->setCellValue($col . $fila, number_format($notaAre[$j], $config['conf_decimales_notas']));
                }
                $sheet->getStyle($col . $fila)->getFont()->setBold(true);
                $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
                
                if ($notaAre[$j] <= 0) {
                    $periodoAreaCalcular -= 1;
                }
            }
            
            $periodoAreaCalcular = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoAreaCalcular : $config["conf_periodos_maximos"];
            $notaAreAcumulada = number_format($notaAreAcumulada / $periodoAreaCalcular, $config['conf_decimales_notas']);
            $desenpenioAreAcumulado = Boletin::determinarRango($notaAreAcumulada, $tiposNotas);
            
            $col = chr(ord($columnaInicio) + 1 + $periodoFinal + 1);
            if ($notaAreAcumulada > 0) {
                $sheet->setCellValue($col . $fila, $notaAreAcumulada);
            }
            $sheet->getStyle($col . $fila)->getFont()->setBold(true);
            $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
            
            $col = chr(ord($col) + 1);
            if ($notaAreAcumulada > 0) {
                $sheet->setCellValue($col . $fila, $desenpenioAreAcumulado["notip_nombre"]);
            }
            $sheet->getStyle($col . $fila)->getFont()->setBold(true);
            $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
            
            $fila++;
        }
    }
    
    // PROMEDIO GENERAL
    $sheet->setCellValue($columnaInicio . $fila, 'PROMEDIO GENERAL');
    $sheet->getStyle($columnaInicio . $fila)->getFont()->setBold(true)->setSize(12);
    $sheet->getStyle($columnaInicio . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
    
    $col = chr(ord($columnaInicio) + 1);
    $sheet->setCellValue($col . $fila, '');
    $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
    
    $promedioFinal = 0;
    $periodoCalcular = $config["conf_periodos_maximos"];
    
    for ($j = 1; $j <= $periodoFinal; $j++) {
        $acumuladoPj = ($totalNotasPeriodo[$j] / $cantidadAreas);
        $acumuladoPj = round($acumuladoPj, $config['conf_decimales_notas']);
        $promedioFinal += $acumuladoPj;
        
        if ($acumuladoPj <= 0) {
            $periodoCalcular -= 1;
        }
        
        $col = chr(ord($columnaInicio) + 1 + $j);
        if ($acumuladoPj > 0) {
            $sheet->setCellValue($col . $fila, $acumuladoPj);
        }
        $sheet->getStyle($col . $fila)->getFont()->setBold(true);
        $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
    }
    
    $periodoCalcularFinal = $estudiante['mat_estado_matricula'] == CANCELADO && $config["conf_promedio_libro_final"] == BDT_Configuracion::PERIODOS_CURSADOS ? $periodoCalcular : $config["conf_periodos_maximos"];
    $promedioFinal = round($promedioFinal / $periodoCalcularFinal, $config['conf_decimales_notas']);
    $desempenoAcumuladoTotal = Boletin::determinarRango($promedioFinal, $tiposNotas);
    
    $col = chr(ord($columnaInicio) + 1 + $periodoFinal + 1);
    if ($promedioFinal > 0) {
        $sheet->setCellValue($col . $fila, $promedioFinal);
    }
    $sheet->getStyle($col . $fila)->getFont()->setBold(true);
    $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
    
    $col = chr(ord($col) + 1);
    if ($promedioFinal > 0) {
        $sheet->setCellValue($col . $fila, $desempenoAcumuladoTotal["notip_nombre"]);
    }
    $sheet->getStyle($col . $fila)->getFont()->setBold(true);
    $sheet->getStyle($col . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle($col . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAEAEA');
    
    $fila += 2;
    
    // Observación definitiva
    $sheet->mergeCells($columnaInicio . $fila . ':' . chr(ord($columnaInicio) + 7) . $fila);
    $sheet->setCellValue($columnaInicio . $fila, 'Observación definitiva:');
    $sheet->getStyle($columnaInicio . $fila)->getFont()->setBold(true)->setSize(12);
    $fila++;
    
    if ($periodoFinal == $config["conf_periodos_maximos"]) {
        if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"]) {
            $msj = "EL(LA) ESTUDIANTE NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
        } elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] && $materiasPerdidas > 0) {
            $msj = "EL(LA) ESTUDIANTE DEBE NIVELAR LAS MATERIAS PERDIDAS.";
        } else {
            $msj = "EL(LA) ESTUDIANTE FUE PROMOVIDO(A) AL GRADO SIGUIENTE.";
        }
        
        if ($estudiante['mat_estado_matricula'] == CANCELADO && $periodoCalcularFinal < $config["conf_periodos_maximos"]) {
            $msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
        }
    }
    
    $sheet->mergeCells($columnaInicio . $fila . ':' . chr(ord($columnaInicio) + 7) . $fila);
    $sheet->setCellValue($columnaInicio . $fila, $msj);
    $sheet->getStyle($columnaInicio . $fila)->getAlignment()->setWrapText(true);
    
    // Aplicar bordes a toda la tabla
    $ultimaColumna = chr(ord($columnaInicio) + 1 + $periodoFinal + 2);
    $sheet->getStyle($columnaInicio . '4:' . $ultimaColumna . $fila)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
}

// Descargar Excel
$nombreArchivo = "Libro_Final_" . $year . "_" . date("Y-m-d") . ".xlsx";
$excelUtil->descargarExcel($nombreArchivo);
exit();

