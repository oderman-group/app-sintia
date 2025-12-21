<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Areas.php");

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
        $areaId = $_POST['area_id'] ?? null;
        
        if (empty($areaId)) {
            jsonResponse(['success' => false, 'message' => 'ID de área es obligatorio.']);
        }
        
        // Obtener datos del área
        $datosArea = Areas::traerDatosArea($config, $areaId);
        
        if (!$datosArea) {
            jsonResponse(['success' => false, 'message' => 'Área no encontrada.']);
        }
        
        jsonResponse([
            'success' => true, 
            'area' => $datosArea
        ]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos de área: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

