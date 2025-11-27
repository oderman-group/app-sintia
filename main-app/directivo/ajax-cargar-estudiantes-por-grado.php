<?php
include("session.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

try {
    $gradoId = isset($_POST['grado']) ? $_POST['grado'] : '';
    
    if (empty($gradoId)) {
        $response['message'] = 'Debe seleccionar un grado';
        echo json_encode($response);
        exit();
    }
    
    $filtro = ' AND mat_grado="' . mysqli_real_escape_string($conexion, $gradoId) . '"';
    $opcionesConsulta = Estudiantes::listarEstudiantesEnGrados($filtro, '');
    
    $estudiantes = [];
    while ($estudiante = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
        $nombreCompleto = trim(
            $estudiante['mat_primer_apellido'] . ' ' . 
            $estudiante['mat_segundo_apellido'] . ' ' . 
            $estudiante['mat_nombres'] . ' ' . 
            $estudiante['mat_nombre2']
        );
        
        $grupo = isset($estudiante['gru_nombre']) ? $estudiante['gru_nombre'] : '';
        $grado = isset($estudiante['gra_nombre']) ? $estudiante['gra_nombre'] : '';
        
        $estudiantes[] = [
            'id' => $estudiante['mat_id'],
            'nombre' => strtoupper($nombreCompleto),
            'grado' => strtoupper($grado),
            'grupo' => strtoupper($grupo),
            'texto_completo' => "[" . $estudiante['mat_id'] . "] " . strtoupper($nombreCompleto) . " - " . strtoupper($grado . " " . $grupo)
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $estudiantes;
    $response['message'] = count($estudiantes) . ' estudiantes encontrados';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>

