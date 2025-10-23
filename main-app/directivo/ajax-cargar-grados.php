<?php
include("session.php");
require_once("../class/Grados.php");

header('Content-Type: application/json');

$response = ['success' => false, 'data' => []];

try {
    $grados = Grados::listarGrados(1);
    
    if ($grados) {
        while ($grado = mysqli_fetch_array($grados, MYSQLI_BOTH)) {
            $response['data'][] = [
                'id' => $grado['gra_id'],
                'nombre' => $grado['gra_nombre']
            ];
        }
        
        $response['success'] = true;
        $response['message'] = count($response['data']) . ' grados encontrados';
    } else {
        $response['message'] = 'No se pudieron cargar los grados';
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
