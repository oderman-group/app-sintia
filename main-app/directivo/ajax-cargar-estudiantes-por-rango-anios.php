<?php
include("session.php");
require_once("../class/Estudiantes.php");

header('Content-Type: application/json');

$response = ['success' => false, 'data' => [], 'message' => ''];

try {
    $desde = isset($_POST['desde']) ? (int)$_POST['desde'] : 0;
    $hasta = isset($_POST['hasta']) ? (int)$_POST['hasta'] : 0;
    
    if (empty($desde) || empty($hasta)) {
        $response['message'] = 'Debe seleccionar el rango de años (desde y hasta)';
        echo json_encode($response);
        exit();
    }
    
    if ($desde > $hasta) {
        $response['message'] = 'El año "desde" debe ser menor o igual al año "hasta"';
        echo json_encode($response);
        exit();
    }
    
    $institucion = (int)$config['conf_id_institucion'];
    
    // Construir lista de años
    $aniosConsulta = [];
    for ($y = $desde; $y <= $hasta; $y++) {
        $aniosConsulta[] = "'" . mysqli_real_escape_string($conexion, $y) . "'";
    }
    $aniosIn = implode(',', $aniosConsulta);
    
    // Todas las matrículas en el rango: identificador, year, grado, mat_id, nombres (para armar "GRADO - YEAR, ...")
    $sql = "SELECT 
                COALESCE(mat.mat_id_usuario, CONCAT('doc_', mat.mat_documento)) as identificador,
                mat.mat_id_usuario,
                mat.mat_documento,
                mat.mat_id,
                mat.mat_nombres,
                mat.mat_nombre2,
                mat.mat_primer_apellido,
                mat.mat_segundo_apellido,
                mat.year,
                gra.gra_nombre,
                gru.gru_nombre
            FROM " . BD_ACADEMICA . ".academico_matriculas mat
            LEFT JOIN " . BD_ACADEMICA . ".academico_grados gra 
                ON gra.gra_id = mat.mat_grado 
                AND gra.institucion = mat.institucion 
                AND gra.year = mat.year
            LEFT JOIN " . BD_ACADEMICA . ".academico_grupos gru 
                ON gru.gru_id = mat.mat_grupo 
                AND gru.institucion = mat.institucion 
                AND gru.year = mat.year
            WHERE mat.mat_eliminado = 0 
            AND mat.institucion = {$institucion}
            AND mat.year IN ({$aniosIn})
            AND (mat.mat_estado_matricula = 1 OR mat.mat_estado_matricula = 2)
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres, mat.year
            LIMIT 2000";
    
    $consulta = mysqli_query($conexion, $sql);
    
    if (!$consulta) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conexion));
    }
    
    $estudiantesAgrupados = [];
    
    while ($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
        $identificador = $row['identificador'];
        $nombreCompleto = trim(
            ($row['mat_primer_apellido'] ?? '') . ' ' . 
            ($row['mat_segundo_apellido'] ?? '') . ' ' . 
            ($row['mat_nombres'] ?? '') . ' ' . 
            ($row['mat_nombre2'] ?? '')
        );
        $nombreCompleto = strtoupper($nombreCompleto);
        $grado = strtoupper(trim($row['gra_nombre'] ?? ''));
        $year = (int)$row['year'];
        
        if (!isset($estudiantesAgrupados[$identificador])) {
            $estudiantesAgrupados[$identificador] = [
                'id' => $row['mat_id'],
                'mat_id_usuario' => $row['mat_id_usuario'],
                'mat_documento' => $row['mat_documento'],
                'nombre_completo' => $nombreCompleto,
                'pares' => []
            ];
        }
        
        $estudiantesAgrupados[$identificador]['pares'][$year] = $grado;
        $estudiantesAgrupados[$identificador]['id'] = $row['mat_id'];
    }
    
    $estudiantes = [];
    foreach ($estudiantesAgrupados as $datos) {
        $pares = $datos['pares'];
        ksort($pares);
        $partes = [];
        foreach ($pares as $y => $g) {
            $partes[] = ($g ? $g . ' - ' : '') . $y;
        }
        $sufijo = count($partes) > 0 ? ' (' . implode(', ', $partes) . ')' : '';
        $datos['texto_completo'] = '[' . $datos['id'] . '] ' . $datos['nombre_completo'] . $sufijo;
        unset($datos['pares']);
        $estudiantes[] = $datos;
    }
    
    usort($estudiantes, function ($a, $b) {
        return strcmp($a['nombre_completo'], $b['nombre_completo']);
    });
    
    $response['success'] = true;
    $response['data'] = $estudiantes;
    $response['message'] = count($estudiantes) . ' estudiante(s) encontrado(s) en el rango de años';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
