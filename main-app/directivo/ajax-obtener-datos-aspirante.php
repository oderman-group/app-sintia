<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");

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
        $aspId = $_POST['asp_id'] ?? null;
        $matId = $_POST['mat_id'] ?? null;
        
        if (empty($aspId) || empty($matId)) {
            jsonResponse(['success' => false, 'message' => 'ID de aspirante y matrícula son obligatorios.']);
        }
        
        // Consultar datos del aspirante
        $sql = "SELECT 
                    mat.*,
                    asp.*,
                    gra.gra_nombre,
                    acud.uss_email as acudiente_email
                FROM ".BD_ACADEMICA.".academico_matriculas mat
                INNER JOIN ".BD_ADMISIONES.".aspirantes asp ON asp.asp_id = mat.mat_solicitud_inscripcion
                LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON mat.mat_grado = gra.gra_id
                LEFT JOIN ".BD_GENERAL.".usuarios acud ON acud.uss_id = mat.mat_acudiente
                WHERE asp.asp_id = :asp_id 
                AND mat.mat_id = :mat_id
                AND mat.institucion = :institucion
                AND mat.year = :year";
        
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(':asp_id', $aspId, PDO::PARAM_INT);
        $stmt->bindParam(':mat_id', $matId, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        $stmt->execute();
        
        $aspirante = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$aspirante) {
            jsonResponse(['success' => false, 'message' => 'Aspirante no encontrado.']);
        }
        
        jsonResponse(['success' => true, 'data' => $aspirante]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos del aspirante: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>


