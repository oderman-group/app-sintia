<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Método no permitido']);
}

try {
    $cargaId = $_POST['carga_id'] ?? null;
    $datosJson = $_POST['datos'] ?? null;
    
    if (empty($cargaId)) {
        jsonResponse(['success' => false, 'message' => 'ID de carga es obligatorio']);
    }
    
    if (empty($datosJson)) {
        jsonResponse(['success' => false, 'message' => 'No se recibieron datos para actualizar']);
    }
    
    $datos = json_decode($datosJson, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        jsonResponse(['success' => false, 'message' => 'Error al decodificar JSON: ' . json_last_error_msg()]);
    }
    
    if (empty($datos)) {
        jsonResponse(['success' => false, 'message' => 'No hay datos para actualizar']);
    }
    
    // Validar que la carga existe
    $cargaActual = CargaAcademica::traerCargaMateriaPorID($config, $cargaId);
    
    if (!$cargaActual) {
        jsonResponse(['success' => false, 'message' => 'Carga no encontrada']);
    }
    
    // Preparar array de actualización
    $updateArray = [];
    
    // Campos permitidos para actualizar
    $camposPermitidos = [
        'car_docente',
        'car_curso',
        'car_grupo',
        'car_materia',
        'car_periodo',
        'car_activa',
        'car_ih',
        'car_director_grupo',
        'car_permiso2',
        'car_indicador_automatico',
        'car_maximos_indicadores',
        'car_maximas_calificaciones',
        'car_configuracion',
        'car_valor_indicador'
    ];
    
    foreach ($datos as $campo => $valor) {
        if (in_array($campo, $camposPermitidos)) {
            $updateArray[$campo] = $valor;
        }
    }
    
    if (empty($updateArray)) {
        jsonResponse(['success' => false, 'message' => 'No hay campos válidos para actualizar']);
    }
    
    // Actualizar la carga
    CargaAcademica::actualizarCargaPorID($config, $cargaId, $updateArray);
    
    // Registrar en historial de acciones
    include("../compartido/guardar-historial-acciones.php");
    
    jsonResponse([
        'success' => true,
        'message' => 'Carga actualizada exitosamente',
        'carga_id' => $cargaId,
        'datos_actualizados' => $updateArray
    ]);
    
} catch (Exception $e) {
    error_log("Error en ajax-actualizar-carga-visual.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    jsonResponse([
        'success' => false,
        'message' => 'Error al actualizar la carga: ' . $e->getMessage()
    ]);
}
?>

