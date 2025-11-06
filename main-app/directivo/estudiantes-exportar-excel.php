<?php
include("session.php");
$idPaginaInterna = 'DT0001';

if(!Modulos::validarSubRol([$idPaginaInterna])){
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
    exit();
}

require_once("../class/Estudiantes.php");
require_once("../class/servicios/GradoServicios.php"); 
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/librerias/ExcelPhp/vendor/autoload.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaLogger.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Obtener campos seleccionados
$camposSeleccionados = [];
if (!empty($_GET['campos'])) {
    $camposSeleccionados = json_decode(urldecode($_GET['campos']), true);
}

if (empty($camposSeleccionados)) {
    die('Error: No se seleccionaron campos para exportar.');
}

// Construir filtros (igual que en ajax-filtrar-estudiantes.php)
$filtro = "";

// Filtro de búsqueda general
if (!empty($_GET['busqueda'])) {
    $busquedaEscape = mysqli_real_escape_string($conexion, trim($_GET['busqueda']));
    $filtro .= " AND (
        mat.mat_nombres LIKE '%{$busquedaEscape}%' OR
        mat.mat_nombre2 LIKE '%{$busquedaEscape}%' OR
        mat.mat_primer_apellido LIKE '%{$busquedaEscape}%' OR
        mat.mat_segundo_apellido LIKE '%{$busquedaEscape}%' OR
        mat.mat_documento LIKE '%{$busquedaEscape}%' OR
        mat.mat_email LIKE '%{$busquedaEscape}%' OR
        uss.uss_usuario LIKE '%{$busquedaEscape}%' OR
        CONCAT(TRIM(mat.mat_nombres), ' ', TRIM(mat.mat_nombre2), ' ', TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_segundo_apellido)) LIKE '%{$busquedaEscape}%' OR
        CONCAT(TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_segundo_apellido), ' ', TRIM(mat.mat_nombres), ' ', TRIM(mat.mat_nombre2)) LIKE '%{$busquedaEscape}%' OR
        CONCAT(TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_nombres)) LIKE '%{$busquedaEscape}%'
    )";
}

// Filtro de cursos
if (!empty($_GET['cursos'])) {
    $cursosJson = is_string($_GET['cursos']) ? urldecode($_GET['cursos']) : $_GET['cursos'];
    $cursos = json_decode($cursosJson, true);
    if (!empty($cursos) && is_array($cursos)) {
        $cursosEscapados = array_map(function($curso) use ($conexion) {
            return mysqli_real_escape_string($conexion, $curso);
        }, $cursos);
        $cursosStr = implode("','", $cursosEscapados);
        $filtro .= " AND mat.mat_grado IN ('{$cursosStr}')";
    }
}

// Filtro de grupos
if (!empty($_GET['grupos'])) {
    $gruposJson = is_string($_GET['grupos']) ? urldecode($_GET['grupos']) : $_GET['grupos'];
    $grupos = json_decode($gruposJson, true);
    if (!empty($grupos) && is_array($grupos)) {
        $gruposEscapados = array_map(function($grupo) use ($conexion) {
            return mysqli_real_escape_string($conexion, $grupo);
        }, $grupos);
        $gruposStr = implode("','", $gruposEscapados);
        $filtro .= " AND mat.mat_grupo IN ('{$gruposStr}')";
    }
}

// Filtro de estados
if (!empty($_GET['estados'])) {
    $estadosJson = is_string($_GET['estados']) ? urldecode($_GET['estados']) : $_GET['estados'];
    $estados = json_decode($estadosJson, true);
    if (!empty($estados) && is_array($estados)) {
        $estadosEscapados = array_map(function($estado) use ($conexion) {
            return mysqli_real_escape_string($conexion, $estado);
        }, $estados);
        $estadosStr = implode("','", $estadosEscapados);
        $filtro .= " AND mat.mat_estado_matricula IN ('{$estadosStr}')";
    }
}

// Filtro por fecha de matrícula (desde)
if (!empty($_GET['fecha_desde'])) {
    $fechaDesdeEscape = mysqli_real_escape_string($conexion, trim($_GET['fecha_desde']));
    $filtro .= " AND DATE(mat.mat_fecha) >= '{$fechaDesdeEscape}'";
}

// Filtro por fecha de matrícula (hasta)
if (!empty($_GET['fecha_hasta'])) {
    $fechaHastaEscape = mysqli_real_escape_string($conexion, trim($_GET['fecha_hasta']));
    $filtro .= " AND DATE(mat.mat_fecha) <= '{$fechaHastaEscape}'";
}

// Campos a seleccionar (sin ciudad por ahora, se obtendrá después si es necesario)
$selectSql = [
    "mat.*",
    "uss.uss_id",
    "uss.uss_usuario",
    "uss.uss_bloqueado",
    "gra_nombre",
    "gru_nombre",
    "gra_formato_boletin",
    "acud.uss_nombre as acud_uss_nombre",
    "acud.uss_nombre2 as acud_uss_nombre2",
    "acud.uss_apellido1 as acud_uss_apellido1",
    "acud.uss_apellido2 as acud_uss_apellido2",
    "acud.uss_documento as acud_uss_documento",
    "acud.uss_email as acud_uss_email",
    "mat.id_nuevo AS mat_id_nuevo",
    "og_tipo_doc.ogen_nombre as tipo_doc_nombre",
    "og_genero.ogen_nombre as genero_nombre",
    "og_estrato.ogen_nombre as estrato_nombre",
    "og_tipo_sangre.ogen_nombre as tipo_sangre_nombre"
];

// Si se requiere lugar de nacimiento, agregarlo al SELECT
// Nota: El JOIN de ciudades en listarEstudiantes no tiene alias, así que usamos directamente el nombre de columna
if (in_array('lugar_nacimiento', $camposSeleccionados)) {
    $selectSql[] = "ciu_nombre";
}

// Consultar estudiantes con filtros (sin límite para exportar todos)
// Nota: El método requiere un string para filtroLimite, así que usamos un valor que no afecte la consulta
$consulta = Estudiantes::listarEstudiantes(0, $filtro, 'LIMIT 999999', null, null, $selectSql);

// Crear nuevo Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Estudiantes');

// Mapeo de campos a títulos y funciones de obtención de valor
$mapaCampos = [
    'id' => ['title' => 'ID', 'func' => function($row) { return $row['mat_id'] ?? ''; }],
    'nombre_completo' => ['title' => 'Nombre Completo', 'func' => function($row) { return Estudiantes::NombreCompletoDelEstudiante($row); }],
    'documento' => ['title' => 'Documento', 'func' => function($row) { return $row['mat_documento'] ?? ''; }],
    'tipo_documento' => ['title' => 'Tipo de Documento', 'func' => function($row) { return $row['tipo_doc_nombre'] ?? ''; }],
    'usuario' => ['title' => 'Usuario', 'func' => function($row) { return $row['uss_usuario'] ?? ''; }],
    'email' => ['title' => 'Email', 'func' => function($row) { return !empty($row['mat_email']) ? strtolower($row['mat_email']) : ''; }],
    'telefono' => ['title' => 'Teléfono', 'func' => function($row) { return $row['mat_telefono'] ?? ''; }],
    'genero' => ['title' => 'Género', 'func' => function($row) { return $row['genero_nombre'] ?? ''; }],
    'fecha_nacimiento' => ['title' => 'Fecha de Nacimiento', 'func' => function($row) { return $row['mat_fecha_nacimiento'] ?? ''; }],
    'lugar_nacimiento' => ['title' => 'Lugar de Nacimiento', 'func' => function($row) {
        if (!empty($row['mat_lugar_nacimiento'])) {
            return is_numeric($row['mat_lugar_nacimiento']) ? ($row['ciu_nombre'] ?? '') : strtoupper($row['mat_lugar_nacimiento']);
        }
        return '';
    }],
    'grado' => ['title' => 'Grado', 'func' => function($row) { return $row['gra_nombre'] ?? ''; }],
    'grupo' => ['title' => 'Grupo', 'func' => function($row) { return $row['gru_nombre'] ?? ''; }],
    'estado_matricula' => ['title' => 'Estado de Matrícula', 'func' => function($row) {
        global $estadosMatriculasEstudiantes;
        return $estadosMatriculasEstudiantes[$row['mat_estado_matricula']] ?? '';
    }],
    'direccion' => ['title' => 'Dirección', 'func' => function($row) { return $row['mat_direccion'] ?? ''; }],
    'barrio' => ['title' => 'Barrio', 'func' => function($row) { return $row['mat_barrio'] ?? ''; }],
    'estrato' => ['title' => 'Estrato', 'func' => function($row) { return $row['estrato_nombre'] ?? ''; }],
    'lugar_expedicion' => ['title' => 'Lugar de Expedición', 'func' => function($row) { return $row['mat_lugar_expedicion'] ?? ''; }],
    'tipo_sangre' => ['title' => 'Tipo de Sangre', 'func' => function($row) { return $row['tipo_sangre_nombre'] ?? ''; }],
    'folio' => ['title' => 'Folio', 'func' => function($row) { return $row['mat_folio'] ?? ''; }],
    'codigo_tesoreria' => ['title' => 'Código de Tesorería', 'func' => function($row) { return $row['mat_codigo_tesoreria'] ?? ''; }],
    'acudiente_nombre' => ['title' => 'Nombre del Acudiente', 'func' => function($row) {
        if (!empty($row['mat_acudiente'])) {
            // Los datos del acudiente pueden venir directamente de la tabla acud
            // Intentar obtenerlos de las columnas con alias primero, luego sin alias
            $datosAcudiente = [
                'uss_nombre' => !empty($row['acud_uss_nombre']) ? $row['acud_uss_nombre'] : 
                               (!empty($row['uss_nombre']) && strpos($row['uss_id'], $row['mat_acudiente']) !== false ? $row['uss_nombre'] : ''),
                'uss_nombre2' => $row['acud_uss_nombre2'] ?? '',
                'uss_apellido1' => $row['acud_uss_apellido1'] ?? '',
                'uss_apellido2' => $row['acud_uss_apellido2'] ?? ''
            ];
            
            // Si no tenemos datos con alias, intentar obtenerlos directamente
            if (empty($datosAcudiente['uss_nombre']) && !empty($row['mat_acudiente'])) {
                global $conexion;
                $consultaDatosA = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$row['mat_acudiente']."'");
                if ($datosA = mysqli_fetch_array($consultaDatosA, MYSQLI_BOTH)) {
                    return UsuariosPadre::nombreCompletoDelUsuario($datosA);
                }
            }
            
            $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($datosAcudiente);
            return !empty($nombreCompleto) ? $nombreCompleto : '';
        }
        return '';
    }],
    'acudiente_documento' => ['title' => 'Documento del Acudiente', 'func' => function($row) {
        if (!empty($row['mat_acudiente'])) {
            if (!empty($row['acud_uss_documento'])) {
                return $row['acud_uss_documento'];
            }
            // Si no viene en el SELECT, obtenerlo directamente
            global $conexion;
            $consultaDatosA = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$row['mat_acudiente']."'");
            if ($datosA = mysqli_fetch_array($consultaDatosA, MYSQLI_BOTH)) {
                return $datosA['uss_documento'] ?? '';
            }
        }
        return '';
    }],
    'acudiente_email' => ['title' => 'Email del Acudiente', 'func' => function($row) {
        if (!empty($row['mat_acudiente'])) {
            if (!empty($row['acud_uss_email'])) {
                return strtolower($row['acud_uss_email']);
            }
            // Si no viene en el SELECT, obtenerlo directamente
            global $conexion;
            $consultaDatosA = UsuariosPadre::obtenerTodosLosDatosDeUsuarios("AND uss_id='".$row['mat_acudiente']."'");
            if ($datosA = mysqli_fetch_array($consultaDatosA, MYSQLI_BOTH)) {
                return !empty($datosA['uss_email']) ? strtolower($datosA['uss_email']) : '';
            }
        }
        return '';
    }]
];

// Filtrar solo los campos seleccionados
$camposAEscribir = [];
foreach ($camposSeleccionados as $campo) {
    if (isset($mapaCampos[$campo])) {
        $camposAEscribir[$campo] = $mapaCampos[$campo];
    }
}

// Escribir encabezados
$columna = 1;
foreach ($camposAEscribir as $campo => $info) {
    $columnaLetra = Coordinate::stringFromColumnIndex($columna);
    $cellCoordinate = $columnaLetra . '1';
    
    $sheet->setCellValue($cellCoordinate, $info['title']);
    $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
    $sheet->getStyle($cellCoordinate)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF4472C4');
    $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setARGB('FFFFFFFF');
    $sheet->getStyle($cellCoordinate)->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);
    $columna++;
}

// Escribir datos
$fila = 2;
$totalEstudiantes = 0;

if (!empty($consulta)) {
    while ($resultado = $consulta->fetch_assoc()) {
        $columna = 1;
        foreach ($camposAEscribir as $campo => $info) {
            $valor = $info['func']($resultado);
            $columnaLetra = Coordinate::stringFromColumnIndex($columna);
            $cellCoordinate = $columnaLetra . $fila;
            
            $sheet->setCellValue($cellCoordinate, $valor);
            $columna++;
        }
        
        $fila++;
        $totalEstudiantes++;
    }
    $consulta->free();
}

// Ajustar ancho de columnas
for ($col = 1; $col <= count($camposAEscribir); $col++) {
    $columnaLetra = Coordinate::stringFromColumnIndex($col);
    $sheet->getColumnDimension($columnaLetra)->setAutoSize(true);
}

// Aplicar bordes a todas las celdas con datos
$ultimaColumna = count($camposAEscribir);
$ultimaFila = $fila - 1;
if ($ultimaFila > 1) {
    $primeraColumnaLetra = Coordinate::stringFromColumnIndex(1);
    $ultimaColumnaLetra = Coordinate::stringFromColumnIndex($ultimaColumna);
    $rango = $primeraColumnaLetra . '1:' . $ultimaColumnaLetra . $ultimaFila;
    
    $sheet->getStyle($rango)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
}

// Guardar y enviar al navegador
$nombreArchivo = 'Estudiantes_' . date('Y-m-d_H-i-s') . '.xlsx';

// Registrar auditoría de exportación de datos
$cantidadEstudiantes = $ultimaFila - 1; // Restamos la fila del encabezado
AuditoriaLogger::registrarExportacion(
    'ESTUDIANTES',
    $cantidadEstudiantes,
    [
        'archivo' => $nombreArchivo,
        'campos_exportados' => array_keys($camposAEscribir),
        'filtros' => [
            'busqueda' => $_GET['busqueda'] ?? null,
            'cursos' => $_GET['cursos'] ?? null,
            'grupos' => $_GET['grupos'] ?? null,
            'estados' => $_GET['estados'] ?? null
        ]
    ]
);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Guardar historial
include("../compartido/historial-acciones-guardar.php");
exit();

