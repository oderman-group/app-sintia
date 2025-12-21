<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");

header('Content-Type: application/json');

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

try {
    // Actualizar la fecha de última salida para marcar que ya vio el modal
    // Usar mysqli directamente
    $sql = "UPDATE " . BD_GENERAL . ".usuarios 
            SET uss_ultima_salida = NOW() 
            WHERE uss_id = ? 
            AND institucion = ? 
            AND year = ?";
    
    $stmt = mysqli_prepare($conexion, $sql);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conexion));
    }
    
    mysqli_stmt_bind_param($stmt, 'iii', $_SESSION['id'], $config['conf_id_institucion'], $_SESSION["bd"]);
    $ejecutado = mysqli_stmt_execute($stmt);
    
    if (!$ejecutado) {
        throw new Exception('Error al ejecutar: ' . mysqli_stmt_error($stmt));
    }
    
    $filasAfectadas = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    if ($filasAfectadas > 0) {
        echo json_encode(['success' => true, 'message' => 'Modal marcado como visto']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar']);
    }
    
} catch (Exception $e) {
    error_log('Error en ajax-marcar-modal-visto.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
