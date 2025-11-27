<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0041';

require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");

header('Content-Type: application/json; charset=utf-8');

try {
    error_log("🔵 Iniciando actualización de ausencia justificada");
    
    // Validar parámetros
    $idAusencia = !empty($_POST['idAusencia']) ? $_POST['idAusencia'] : null;
    $justificada = isset($_POST['justificada']) ? (int)$_POST['justificada'] : 0;
    
    if (!$idAusencia) {
        throw new Exception("ID de ausencia no proporcionado");
    }
    
    error_log("📊 Parámetros: ID Ausencia: $idAusencia, Justificada: $justificada");
    
    // ✅ Usar el método existente de la clase Ausencias para actualizar
    $update = [
        "aus_justificadas" => $justificada
    ];
    
    Ausencias::actualizarAusencia($config, $idAusencia, $update);
    
    error_log("✅ Ausencia actualizada exitosamente - ID: $idAusencia, Justificada: $justificada");
    
    echo json_encode([
        'success' => true,
        'message' => $justificada == 1 ? 'Ausencia marcada como justificada' : 'Ausencia marcada como no justificada',
        'justificada' => $justificada
    ]);
    
} catch (Exception $e) {
    error_log("❌ Error al justificar ausencia: " . $e->getMessage());
    error_log("❌ Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

exit();

