<?php
require '../../librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear nuevo spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configurar encabezados
$headers = [
    'A1' => 'Tipo de Usuario',
    'B1' => 'Primer Apellido', 
    'C1' => 'Segundo Apellido',
    'D1' => 'Primer Nombre',
    'E1' => 'Segundo Nombre',
    'F1' => 'Usuario',
    'G1' => 'Documento'
];

// Escribir encabezados
foreach ($headers as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Agregar datos de ejemplo
$ejemplos = [
    ['2', 'García', 'López', 'Juan', 'Carlos', 'jgarcia', '12345678'],
    ['3', 'Rodríguez', 'Martínez', 'María', 'Elena', 'mrodriguez', '87654321'],
    ['5', 'Fernández', '', 'Pedro', '', 'pfernandez', '11223344'],
    ['2', 'Sánchez', 'González', 'Ana', 'Lucía', '', '55667788'],
    ['3', 'Torres', 'Vargas', 'Carlos', 'Alberto', 'ctorres', '99887766']
];

// Escribir ejemplos empezando desde fila 2
$fila = 2;
foreach ($ejemplos as $ejemplo) {
    $columna = 'A';
    foreach ($ejemplo as $valor) {
        $sheet->setCellValue($columna . $fila, $valor);
        $columna++;
    }
    $fila++;
}

// Estilo para encabezados
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4']
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    ]
];

$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Ajustar ancho de columnas
$sheet->getColumnDimension('A')->setWidth(15);
$sheet->getColumnDimension('B')->setWidth(18);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(18);
$sheet->getColumnDimension('E')->setWidth(18);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);

// Configurar headers para descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="plantilla-usuarios.xlsx"');
header('Cache-Control: max-age=0');

// Crear archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
?>
