<?php
include("session.php");
require_once("../class/Estudiantes.php");

header('Content-Type: application/json');

$response = ['success' => false, 'anios' => [], 'anio_minimo' => null, 'anio_maximo' => null, 'message' => ''];

try {
    $identificador = isset($_POST['identificador']) ? trim($_POST['identificador']) : '';
    $tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'usuario'; // 'usuario' o 'documento'
    
    if (empty($identificador)) {
        $response['message'] = 'Debe proporcionar un identificador del estudiante';
        echo json_encode($response);
        exit();
    }
    
    $institucion = (int)$config['conf_id_institucion'];
    $identificadorEsc = mysqli_real_escape_string($conexion, $identificador);
    
    // Construir condición WHERE según el tipo
    $whereCondition = '';
    if ($tipo === 'usuario') {
        $whereCondition = "mat.mat_id_usuario = '{$identificadorEsc}'";
    } else if ($tipo === 'documento') {
        $whereCondition = "mat.mat_documento = '{$identificadorEsc}'";
    } else {
        // Si viene el identificador compuesto (del endpoint de búsqueda)
        if (strpos($identificadorEsc, 'doc_') === 0) {
            // Es un documento
            $doc = str_replace('doc_', '', $identificadorEsc);
            $whereCondition = "mat.mat_documento = '{$doc}' AND mat.mat_id_usuario IS NULL";
        } else {
            // Es un mat_id_usuario
            $whereCondition = "mat.mat_id_usuario = '{$identificadorEsc}'";
        }
    }
    
    // Obtener años de la institución para validar
    $insYears = $_SESSION["datosUnicosInstitucion"]["ins_years"] ?? '';
    if (empty($insYears)) {
        $response['message'] = 'No se encontraron años de la institución';
        echo json_encode($response);
        exit();
    }
    
    $yearArray = explode(",", $insYears);
    $yearStart = (int)trim($yearArray[0]);
    $yearEnd = (int)trim($yearArray[1] ?? $yearArray[0]);
    
    // Consultar todos los años donde el estudiante tiene matrícula
    $sql = "SELECT DISTINCT mat.year
            FROM " . BD_ACADEMICA . ".academico_matriculas mat
            WHERE mat.mat_eliminado = 0 
            AND mat.institucion = {$institucion}
            AND {$whereCondition}
            ORDER BY mat.year ASC";
    
    $consulta = mysqli_query($conexion, $sql);
    
    if (!$consulta) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conexion));
    }
    
    $anios = [];
    while ($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        $year = (int)$row['year'];
        // Solo incluir años que estén en el rango de la institución
        if ($year >= $yearStart && $year <= $yearEnd) {
            $anios[] = $year;
        }
    }
    
    if (empty($anios)) {
        $response['message'] = 'No se encontraron años de matrícula para este estudiante';
        echo json_encode($response);
        exit();
    }
    
    $response['success'] = true;
    $response['anios'] = $anios;
    $response['anio_minimo'] = min($anios);
    $response['anio_maximo'] = max($anios);
    $response['message'] = count($anios) . ' año(s) encontrado(s)';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
