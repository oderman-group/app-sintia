<?php
/**
 * MANTENIMIENTO DE AUDITORÍA
 * Limpia logs antiguos (>90 días) excepto los CRITICAL
 */

require_once("session.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaLogger.php");

// Verificar permisos de desarrollador
Modulos::verificarPermisoDev();

header('Content-Type: application/json; charset=UTF-8');

try {
    $dias = isset($_POST['dias']) ? (int)$_POST['dias'] : 90;
    $registrosEliminados = AuditoriaLogger::limpiarLogsAntiguos($dias);
    
    echo json_encode([
        'success' => true,
        'message' => "✅ Mantenimiento completado. Eliminados {$registrosEliminados} registros de auditoría antiguos (>{$dias} días, excepto CRITICAL).",
        'registros_eliminados' => $registrosEliminados,
        'dias' => $dias
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en mantenimiento: ' . $e->getMessage()
    ]);
}

