<?php
/**
 * MANTENIMIENTO DE RATE LIMITING
 * Limpia intentos fallidos antiguos (>30 días)
 * Ejecutar periódicamente via cronjob o manualmente
 */

require_once("session.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/RateLimit.php");

// Verificar permisos de desarrollador
Modulos::verificarPermisoDev();

header('Content-Type: application/json; charset=UTF-8');

try {
    $registrosEliminados = RateLimit::limpiarIntentosAntiguos();
    
    echo json_encode([
        'success' => true,
        'message' => "✅ Mantenimiento completado. Eliminados {$registrosEliminados} registros antiguos.",
        'registros_eliminados' => $registrosEliminados
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en mantenimiento: ' . $e->getMessage()
    ]);
}

