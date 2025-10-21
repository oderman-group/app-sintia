<?php
include("session.php");
require '../../librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

try {
    if (empty($_FILES['planilla']['name'])) {
        throw new Exception("No se ha seleccionado ningún archivo.");
    }

    $fileName = $_FILES['planilla']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($fileExtension !== 'xlsx') {
        throw new Exception("El archivo debe ser un formato .xlsx");
    }

    $temName = $_FILES['planilla']['tmp_name'];
    $spreadsheet = IOFactory::load($temName);
    $worksheet = $spreadsheet->getActiveSheet();

    // Header validation
    $expectedHeaders = ['mat_documento', 'mat_nombres', 'mat_primer_apellido', 'mat_grado', 'mat_grupo', 'mat_tipo_documento', 'mat_nombre2', 'mat_segundo_apellido', 'mat_fecha_nacimiento', 'mat_email', 'acudiente_documento'];
    $headerRow = $worksheet->getRowIterator(1, 1)->current();
    $cellIterator = $headerRow->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    $actualHeaders = [];
    foreach ($cellIterator as $cell) {
        $actualHeaders[] = $cell->getValue();
    }

    if ($expectedHeaders !== $actualHeaders) {
        throw new Exception("Las cabeceras del archivo no coinciden con la plantilla. Cabeceras esperadas: " . implode(', ', $expectedHeaders));
    }

    echo json_encode(['status' => 'success', 'message' => 'Archivo válido.']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
