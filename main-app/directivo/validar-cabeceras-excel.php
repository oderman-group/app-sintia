<?php
header('Content-Type: application/json');

include("session.php");
require '../../librerias/Excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    // Validar que se recibió el archivo
    if (!isset($_FILES['planilla']) || $_FILES['planilla']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se recibió ningún archivo o hubo un error al subirlo.');
    }
    
    $temName = $_FILES['planilla']['tmp_name'];
    $archivo = $_FILES['planilla']['name'];
    $destino = "../files/excel/";
    $explode = explode(".", $archivo);
    $extension = end($explode);
    $fullArchivo = uniqid('validar_').".".$extension;
    $nombreArchivo = $destino.$fullArchivo;
    
    // Validar extensión
    if (!in_array(strtolower($extension), ['xlsx', 'xls'])) {
        throw new Exception('El archivo debe ser formato Excel (.xlsx o .xls)');
    }
    
    // Mover archivo
    if (!move_uploaded_file($temName, $nombreArchivo)) {
        throw new Exception('No se pudo guardar el archivo subido.');
    }
    
    // Cargar el archivo Excel
    $documento = IOFactory::load($nombreArchivo);
    $hojaActual = $documento->getSheet(0);
    
    // Leer cabeceras de la primera fila
    $cabeceras = [];
    for ($col = 'A'; $col <= 'G'; $col++) {
        $valor = trim($hojaActual->getCell($col . '1')->getValue());
        $cabeceras[] = $valor;
    }
    
    // Cabeceras esperadas
    $cabecerasEsperadas = [
        'Tipo de Usuario',
        'Primer Apellido', 
        'Segundo Apellido',
        'Primer Nombre',
        'Segundo Nombre',
        'Usuario',
        'Documento'
    ];
    
    // Validar cabeceras
    $erroresCabeceras = [];
    $cabecerasCorrectas = true;
    
    for ($i = 0; $i < count($cabecerasEsperadas); $i++) {
        $columna = chr(65 + $i); // A, B, C, D, E, F, G
        $esperada = $cabecerasEsperadas[$i];
        $actual = $cabeceras[$i];
        
        if (empty($actual)) {
            $erroresCabeceras[] = "Columna $columna está vacía. Debe contener: '$esperada'";
            $cabecerasCorrectas = false;
        } elseif (strtolower(trim($actual)) !== strtolower(trim($esperada))) {
            $erroresCabeceras[] = "Columna $columna tiene '$actual' pero debería ser '$esperada'";
            $cabecerasCorrectas = false;
        }
    }
    
    // Contar filas de datos
    $numFilas = $hojaActual->getHighestDataRow();
    $filasConDatos = max(0, $numFilas - 1); // Restar encabezado
    
    // Eliminar archivo temporal
    if (file_exists($nombreArchivo)) {
        unlink($nombreArchivo);
    }
    
    // Respuesta
    if ($cabecerasCorrectas) {
        echo json_encode([
            'success' => true,
            'cabeceras_correctas' => true,
            'cabeceras_encontradas' => $cabeceras,
            'filas_datos' => $filasConDatos,
            'message' => "Archivo válido. Se encontraron $filasConDatos filas de datos para procesar."
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'cabeceras_correctas' => false,
            'cabeceras_encontradas' => $cabeceras,
            'cabeceras_esperadas' => $cabecerasEsperadas,
            'errores' => $erroresCabeceras,
            'filas_datos' => $filasConDatos,
            'message' => 'Las cabeceras del archivo no coinciden con el formato esperado.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error validando cabeceras: " . $e->getMessage());
    
    // Eliminar archivo si existe
    if (isset($nombreArchivo) && file_exists($nombreArchivo)) {
        unlink($nombreArchivo);
    }
    
    echo json_encode([
        'success' => false,
        'cabeceras_correctas' => false,
        'message' => $e->getMessage()
    ]);
}
?>
