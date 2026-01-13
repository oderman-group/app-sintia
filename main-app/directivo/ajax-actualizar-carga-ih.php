<?php
/**
 * ACTUALIZAR INTENSIDAD HORARIA DE UNA CARGA
 * Endpoint AJAX para edición inline del campo I.H.
 */

header('Content-Type: application/json; charset=UTF-8');

try {
    require_once("session.php");
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    
    // Verificar permisos de edición
    if (!Modulos::validarPermisoEdicion() || !Modulos::validarSubRol(['DT0049'])) {
        throw new Exception('No tienes permisos para realizar esta acción');
    }
    
    // Obtener parámetros
    $cargaId = isset($_POST['carga_id']) ? trim($_POST['carga_id']) : '';
    $nuevoIH = isset($_POST['ih']) ? trim($_POST['ih']) : '';
    
    // Validar parámetros
    if (empty($cargaId)) {
        throw new Exception('ID de carga no especificado');
    }
    
    if (empty($nuevoIH) || !is_numeric($nuevoIH) || $nuevoIH < 1 || $nuevoIH > 100) {
        throw new Exception('El valor de Intensidad Horaria debe ser un número entre 1 y 100');
    }
    
    // Usar PDO para la actualización
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar que la carga pertenece a la institución actual
    $verificar = $conexionPDO->prepare("
        SELECT car_id, car_ih 
        FROM ".BD_ACADEMICA.".academico_cargas 
        WHERE car_id = ? AND institucion = ? AND year = ?
    ");
    $verificar->execute([$cargaId, $config['conf_id_institucion'], $_SESSION["bd"]]);
    $carga = $verificar->fetch(PDO::FETCH_ASSOC);
    
    if (!$carga) {
        throw new Exception('La carga especificada no existe o no pertenece a esta institución');
    }
    
    $ihAnterior = $carga['car_ih'];
    
    // Actualizar la intensidad horaria
    $stmt = $conexionPDO->prepare("
        UPDATE ".BD_ACADEMICA.".academico_cargas 
        SET car_ih = ? 
        WHERE car_id = ? AND institucion = ? AND year = ?
    ");
    $stmt->bindParam(1, $nuevoIH, PDO::PARAM_INT);
    $stmt->bindParam(2, $cargaId, PDO::PARAM_STR);
    $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
    
    $filasActualizadas = $stmt->rowCount();
    
    if ($filasActualizadas === 0) {
        throw new Exception('No se pudo actualizar la Intensidad Horaria. Intente nuevamente.');
    }
    
    // Registrar en historial de acciones
    try {
        $idPaginaInterna = 'DT0032';
        $error_reporting_original = error_reporting();
        error_reporting(0);
        ob_start();
        @include("../compartido/historial-acciones-guardar.php");
        ob_end_clean();
        error_reporting($error_reporting_original);
    } catch (Exception $e) {
        // No detener el proceso si falla el historial
        error_log("Error en historial: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Intensidad Horaria actualizada correctamente',
        'carga_id' => $cargaId,
        'ih_anterior' => $ihAnterior,
        'ih_nuevo' => $nuevoIH
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
