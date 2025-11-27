<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $grupoId = $_POST['grupo_id'] ?? null;
        
        if (empty($grupoId)) {
            jsonResponse(['success' => false, 'message' => 'ID de grupo es obligatorio.']);
        }
        
        // Obtener datos del grupo
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grupos WHERE gru_id=? AND institucion=? AND year=?";
        $parametros = [$grupoId, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);
        $datosGrupo = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        
        if (!$datosGrupo) {
            jsonResponse(['success' => false, 'message' => 'Grupo no encontrado.']);
        }
        
        jsonResponse([
            'success' => true, 
            'grupo' => $datosGrupo
        ]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos de grupo: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

