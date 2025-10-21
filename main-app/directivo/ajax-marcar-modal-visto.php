<?php
include("session.php");
require_once("../class/Usuarios.php");

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

try {
    // Actualizar la fecha de última salida para marcar que ya vio el modal
    global $conexion, $config;
    
    $sql = "UPDATE " . BD_GENERAL . ".usuarios 
            SET uss_ultima_salida = NOW() 
            WHERE uss_id = ? AND institucion = ? AND year = ?";
    
    $parametros = [$_SESSION['id'], $config['conf_id_institucion'], $_SESSION["bd"]];
    $consulta = BindSQL::prepararSQL($sql, $parametros);
    
    if ($consulta) {
        echo json_encode(['success' => true, 'message' => 'Modal marcado como visto']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
