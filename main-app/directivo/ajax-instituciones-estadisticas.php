<?php
/**
 * OBTENER ESTADÍSTICAS DE INSTITUCIONES
 * Endpoint para obtener conteos globales
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos
Modulos::verificarPermisoDev();

try {
    // Total de instituciones
    $consultaTotal = mysqli_query($conexion, "
        SELECT COUNT(*) as total FROM " . BD_ADMIN . ".instituciones 
        WHERE ins_enviroment = '" . ENVIROMENT . "'
    ");
    $total = mysqli_fetch_array($consultaTotal, MYSQLI_BOTH)['total'];
    
    // Activas
    $consultaActivas = mysqli_query($conexion, "
        SELECT COUNT(*) as total FROM " . BD_ADMIN . ".instituciones 
        WHERE ins_enviroment = '" . ENVIROMENT . "' AND ins_estado = 1
    ");
    $activas = mysqli_fetch_array($consultaActivas, MYSQLI_BOTH)['total'];
    
    // Inactivas
    $consultaInactivas = mysqli_query($conexion, "
        SELECT COUNT(*) as total FROM " . BD_ADMIN . ".instituciones 
        WHERE ins_enviroment = '" . ENVIROMENT . "' AND ins_estado = 0
    ");
    $inactivas = mysqli_fetch_array($consultaInactivas, MYSQLI_BOTH)['total'];
    
    // Bloqueadas
    $consultaBloqueadas = mysqli_query($conexion, "
        SELECT COUNT(*) as total FROM " . BD_ADMIN . ".instituciones 
        WHERE ins_enviroment = '" . ENVIROMENT . "' AND ins_bloqueada = 1
    ");
    $bloqueadas = mysqli_fetch_array($consultaBloqueadas, MYSQLI_BOTH)['total'];
    
    echo json_encode([
        'success' => true,
        'estadisticas' => [
            'total' => (int)$total,
            'activas' => (int)$activas,
            'inactivas' => (int)$inactivas,
            'bloqueadas' => (int)$bloqueadas
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar estadísticas: ' . $e->getMessage()
    ]);
}

