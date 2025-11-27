<?php
include("session.php");
require_once("../class/Grupos.php");

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

try {
    $grupos = Grupos::listarGrupos();
    
    if ($grupos) {
        while ($grupo = mysqli_fetch_array($grupos, MYSQLI_BOTH)) {
            $response['data'][] = [
                'id' => $grupo['gru_id'],
                'nombre' => $grupo['gru_nombre']
            ];
        }
        
        $response['success'] = true;
        $response['message'] = count($response['data']) . ' grupos encontrados';
    } else {
        $response['message'] = 'No se pudieron cargar los grupos';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
