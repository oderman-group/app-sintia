<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

try {
    $year = isset($_POST['year']) ? $_POST['year'] : '';
    
    if (empty($year)) {
        $response['message'] = 'Debe seleccionar un año';
        echo json_encode($response);
        exit();
    }
    
    // Cambiar temporalmente la BD para consultar el año seleccionado
    $yearActual = $_SESSION["bd"];
    $_SESSION["bd"] = $year;
    
    $opcionesConsulta = Grados::traerGradosInstitucion($config, GRADO_GRUPAL);
    
    $cursos = [];
    while ($curso = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
        $disabled = ($curso['gra_estado'] == '0');
        if (!$disabled) {
            $cursos[] = [
                'id' => $curso['gra_id'],
                'nombre' => $curso['gra_nombre'],
                'texto_completo' => $curso['gra_id'] . ". " . strtoupper($curso['gra_nombre'])
            ];
        }
    }
    
    // Restaurar año actual
    $_SESSION["bd"] = $yearActual;
    
    $response['success'] = true;
    $response['data'] = $cursos;
    $response['message'] = count($cursos) . ' cursos encontrados';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>

