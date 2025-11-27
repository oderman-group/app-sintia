<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademicaOptimizada.php");

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $cargaId = $_POST['carga_id'] ?? null;
        $periodo = $_POST['periodo'] ?? null;
        
        if (empty($cargaId) || empty($periodo)) {
            jsonResponse(['success' => false, 'message' => 'ID de carga y periodo son obligatorios.']);
        }
        
        // Obtener datos adicionales (solo cuando se expande)
        $datos = CargaAcademicaOptimizada::obtenerDatosAdicionalesCarga($config, $cargaId, $periodo);
        
        jsonResponse(['success' => true, 'datos' => $datos]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos adicionales de carga: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>


