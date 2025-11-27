<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");

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
    
    $opcionesConsulta = Grupos::listarGrupos();
    
    $grupos = [];
    while ($grupo = mysqli_fetch_array($opcionesConsulta, MYSQLI_BOTH)) {
        $grupos[] = [
            'id' => $grupo['gru_id'],
            'nombre' => $grupo['gru_nombre'],
            'texto_completo' => $grupo['gru_id'] . ". " . strtoupper($grupo['gru_nombre'])
        ];
    }
    
    // Restaurar año actual
    $_SESSION["bd"] = $yearActual;
    
    $response['success'] = true;
    $response['data'] = $grupos;
    $response['message'] = count($grupos) . ' grupos encontrados';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>

