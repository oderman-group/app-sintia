<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0041';

require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

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
    
    // Actualizar en la base de datos
    $sql = "UPDATE ".BD_DISCIPLINA.".academico_ausencias 
            SET aus_justificadas = ? 
            WHERE aus_id = ? 
            AND institucion = ? 
            AND year = ?";
    
    $parametros = [$justificada, $idAusencia, $config['conf_id_institucion'], $_SESSION["bd"]];
    
    $resultado = BindSQL::prepararSQL($sql, $parametros);
    
    if ($resultado) {
        error_log("✅ Ausencia actualizada exitosamente");
        
        echo json_encode([
            'success' => true,
            'message' => $justificada == 1 ? 'Ausencia marcada como justificada' : 'Ausencia marcada como no justificada',
            'justificada' => $justificada
        ]);
    } else {
        throw new Exception("Error al actualizar la base de datos");
    }
    
} catch (Exception $e) {
    error_log("❌ Error al justificar ausencia: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

exit();

