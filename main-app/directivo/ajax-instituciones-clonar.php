<?php
/**
 * OBTENER INSTITUCIONES PARA CLONAR USUARIO
 * Endpoint para cargar instituciones disponibles para clonación
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar que el usuario sea tipo DEV
if ($datosUsuarioActual['uss_tipo'] != TIPO_DEV) {
    echo json_encode([
        'success' => false,
        'message' => 'No tiene permisos para realizar esta acción'
    ]);
    exit();
}

try {
    // Obtener todas las instituciones activas
    $consulta = mysqli_query($conexion, "
        SELECT ins_id, ins_nombre, ins_siglas, ins_years
        FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_estado = 1 
        AND ins_enviroment = '" . ENVIROMENT . "'
        ORDER BY ins_nombre ASC
    ");
    
    $instituciones = [];
    while ($resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC)) {
        $instituciones[] = [
            'ins_id' => $resultado['ins_id'],
            'ins_nombre' => $resultado['ins_nombre'],
            'ins_siglas' => $resultado['ins_siglas'],
            'ins_years' => $resultado['ins_years']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'instituciones' => $instituciones
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar instituciones: ' . $e->getMessage()
    ]);
}
