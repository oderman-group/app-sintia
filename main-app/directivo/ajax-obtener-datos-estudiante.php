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
        $matId = $_POST['mat_id'] ?? null;
        
        
        if (empty($matId)) {
            jsonResponse(['success' => false, 'message' => 'ID de matrícula es obligatorio.']);
        }
        
        // Consultar datos del estudiante con información académica y opciones generales
        $sql = "SELECT 
                    m.*,
                    g.gra_nombre,
                    gr.gru_nombre,
                    og_genero.ogen_nombre as genero_nombre,
                    og_estrato.ogen_nombre as estrato_nombre,
                    og_tipo_sangre.ogen_nombre as tipo_sangre_nombre
                FROM ".BD_ACADEMICA.".academico_matriculas m
                LEFT JOIN ".BD_ACADEMICA.".academico_grados g ON m.mat_grado = g.gra_id
                LEFT JOIN ".BD_ACADEMICA.".academico_grupos gr ON m.mat_grupo = gr.gru_id
                LEFT JOIN ".BD_ADMIN.".opciones_generales og_genero ON m.mat_genero = og_genero.ogen_id
                LEFT JOIN ".BD_ADMIN.".opciones_generales og_estrato ON m.mat_estrato = og_estrato.ogen_id
                LEFT JOIN ".BD_ADMIN.".opciones_generales og_tipo_sangre ON m.mat_tipo_sangre = og_tipo_sangre.ogen_id
                WHERE m.mat_id = :mat_id 
                AND m.mat_eliminado = 0
                AND m.institucion = :institucion
                AND m.year = :year";
        
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(':mat_id', $matId, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        $stmt->execute();
        
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$estudiante) {
            jsonResponse(['success' => false, 'message' => 'Estudiante no encontrado.']);
        }
        
        jsonResponse(['success' => true, 'data' => $estudiante]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos del estudiante: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor.']);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>
