<?php
include("session.php");
require_once("../class/Estudiantes.php");
require_once("../class/Grados.php");
require '../../librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

Modulos::validarAccesoDirectoPaginas();

// Get parameters
$columnasSeleccionadas = isset($_POST['columnas']) ? $_POST['columnas'] : [];
$filtroGrado = isset($_POST['filtroGrado']) ? $_POST['filtroGrado'] : '';
$filtroGrupo = isset($_POST['filtroGrupo']) ? $_POST['filtroGrupo'] : '';
$limiteRegistros = isset($_POST['limiteRegistros']) ? intval($_POST['limiteRegistros']) : 1000;

if (empty($columnasSeleccionadas)) {
    http_response_code(400);
    echo json_encode(['error' => 'Debe seleccionar al menos una columna']);
    exit();
}

// Build filter
$filtro = '';
if (!empty($filtroGrado)) {
    $filtro .= " AND mat.mat_grado = '$filtroGrado'";
}
if (!empty($filtroGrupo)) {
    $filtro .= " AND mat.mat_grupo = '$filtroGrupo'";
}

// Get students data
$estudiantes = Estudiantes::listarEstudiantes(0, $filtro, "LIMIT 0, $limiteRegistros", null, null, [
    'mat.*',
    'gra.gra_nombre',
    'gru.gru_nombre',
    'uss.uss_usuario',
    'uss.uss_nombre as nombre_usuario',
    'acud.uss_usuario as acudiente_usuario',
    'acud.uss_nombre as acudiente_nombre'
]);

// Create spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Define column mappings
$columnasDisponibles = [
    'mat_matricula' => ['titulo' => 'Número de Matrícula', 'letra' => 'A'],
    'mat_tipo_documento' => ['titulo' => 'Tipo Documento', 'letra' => 'B'],
    'mat_documento' => ['titulo' => 'Nro. Documento', 'letra' => 'C'],
    'mat_nombres' => ['titulo' => 'Primer Nombre', 'letra' => 'D'],
    'mat_nombre2' => ['titulo' => 'Segundo Nombre', 'letra' => 'E'],
    'mat_primer_apellido' => ['titulo' => 'Primer Apellido', 'letra' => 'F'],
    'mat_segundo_apellido' => ['titulo' => 'Segundo Apellido', 'letra' => 'G'],
    'mat_genero' => ['titulo' => 'Género', 'letra' => 'H'],
    'mat_fecha_nacimiento' => ['titulo' => 'Fecha Nacimiento', 'letra' => 'I'],
    'mat_grado' => ['titulo' => 'Grado', 'letra' => 'J'],
    'mat_grupo' => ['titulo' => 'Grupo', 'letra' => 'K'],
    'mat_direccion' => ['titulo' => 'Dirección', 'letra' => 'L'],
    'mat_barrio' => ['titulo' => 'Barrio', 'letra' => 'M'],
    'mat_celular' => ['titulo' => 'Celular', 'letra' => 'N'],
    'mat_email' => ['titulo' => 'Email', 'letra' => 'O'],
    'mat_estrato' => ['titulo' => 'Estrato', 'letra' => 'P'],
    'mat_tipo_sangre' => ['titulo' => 'Tipo Sangre', 'letra' => 'Q'],
    'mat_eps' => ['titulo' => 'EPS', 'letra' => 'R'],
    'acudiente_usuario' => ['titulo' => 'Usuario Acudiente', 'letra' => 'S'],
    'acudiente_nombre' => ['titulo' => 'Nombre Acudiente', 'letra' => 'T']
];

// Filter selected columns
$columnasExportar = [];
$columnaActual = 0;
foreach ($columnasSeleccionadas as $columna) {
    if (isset($columnasDisponibles[$columna])) {
        $columnasExportar[$columna] = $columnasDisponibles[$columna];
        $columnasExportar[$columna]['columna'] = $columnaActual;
        $columnaActual++;
    }
}

// Set headers
$row = 1;
foreach ($columnasExportar as $columna => $config) {
    $sheet->setCellValue($config['letra'] . $row, $config['titulo']);

    // Style header
    $sheet->getStyle($config['letra'] . $row)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4CAF50']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN
            ]
        ]
    ]);
}

// Set data rows
$row = 2;
$contador = 0;
while ($estudiante = mysqli_fetch_array($estudiantes, MYSQLI_BOTH)) {
    $contador++;

    foreach ($columnasExportar as $columna => $config) {
        $valor = '';

        switch ($columna) {
            case 'mat_matricula':
                $valor = $estudiante['mat_matricula'];
                break;
            case 'mat_tipo_documento':
                // Convert document type code to text
                $tiposDocumento = ['105' => 'CC', '106' => 'NUIP', '107' => 'TI', '108' => 'RC', '109' => 'CE', '110' => 'PP'];
                $valor = isset($tiposDocumento[$estudiante['mat_tipo_documento']]) ? $tiposDocumento[$estudiante['mat_tipo_documento']] : $estudiante['mat_tipo_documento'];
                break;
            case 'mat_documento':
                $valor = $estudiante['mat_documento'];
                break;
            case 'mat_nombres':
                $valor = $estudiante['mat_nombres'];
                break;
            case 'mat_nombre2':
                $valor = $estudiante['mat_nombre2'];
                break;
            case 'mat_primer_apellido':
                $valor = $estudiante['mat_primer_apellido'];
                break;
            case 'mat_segundo_apellido':
                $valor = $estudiante['mat_segundo_apellido'];
                break;
            case 'mat_genero':
                $generos = ['126' => 'M', '127' => 'F'];
                $valor = isset($generos[$estudiante['mat_genero']]) ? $generos[$estudiante['mat_genero']] : $estudiante['mat_genero'];
                break;
            case 'mat_fecha_nacimiento':
                if (!empty($estudiante['mat_fecha_nacimiento']) && $estudiante['mat_fecha_nacimiento'] != '0000-00-00') {
                    $fecha = date('d/m/Y', strtotime($estudiante['mat_fecha_nacimiento']));
                    $valor = $fecha;
                }
                break;
            case 'mat_grado':
                $valor = $estudiante['gra_nombre'];
                break;
            case 'mat_grupo':
                $grupos = ['1' => 'A', '2' => 'B', '3' => 'C'];
                $valor = isset($grupos[$estudiante['mat_grupo']]) ? $grupos[$estudiante['mat_grupo']] : $estudiante['mat_grupo'];
                break;
            case 'mat_direccion':
                $valor = $estudiante['mat_direccion'];
                break;
            case 'mat_barrio':
                $valor = $estudiante['mat_barrio'];
                break;
            case 'mat_celular':
                $valor = $estudiante['mat_celular'];
                break;
            case 'mat_email':
                $valor = $estudiante['mat_email'];
                break;
            case 'mat_estrato':
                $valor = $estudiante['mat_estrato'];
                break;
            case 'mat_tipo_sangre':
                $valor = $estudiante['mat_tipo_sangre'];
                break;
            case 'mat_eps':
                $valor = $estudiante['mat_eps'];
                break;
            case 'acudiente_usuario':
                $valor = $estudiante['acudiente_usuario'];
                break;
            case 'acudiente_nombre':
                $valor = $estudiante['acudiente_nombre'];
                break;
        }

        $sheet->setCellValue($config['letra'] . $row, $valor);

        // Style data cells
        $sheet->getStyle($config['letra'] . $row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
    }

    $row++;
}

// Auto-size columns
foreach ($columnasExportar as $config) {
    $sheet->getColumnDimension($config['letra'])->setAutoSize(true);
}

// Set filename
$timestamp = date('Y-m-d_H-i-s');
$filename = "estudiantes_exportados_{$timestamp}.xlsx";

// Send file to browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>