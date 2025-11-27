<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");
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
        $asignaturaId = $_POST['asignatura_id'] ?? null;
        
        if (empty($asignaturaId)) {
            jsonResponse(['success' => false, 'message' => 'ID de asignatura es obligatorio.']);
        }
        
        // Obtener datos de la asignatura
        $datosAsignatura = Asignaturas::consultarDatosAsignatura($conexion, $config, $asignaturaId);
        
        if (!$datosAsignatura) {
            jsonResponse(['success' => false, 'message' => 'Asignatura no encontrada.']);
        }
        
        // Obtener áreas
        $areas = [];
        $areasConsulta = Areas::traerAreasInstitucion($config);
        while($area = mysqli_fetch_array($areasConsulta, MYSQLI_BOTH)){
            $areas[] = [
                'id' => $area['ar_id'],
                'nombre' => $area['ar_nombre']
            ];
        }
        
        jsonResponse([
            'success' => true, 
            'asignatura' => $datosAsignatura,
            'areas' => $areas
        ]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos de asignatura: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

