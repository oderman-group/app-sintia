<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
// Aumentar límite de memoria para este script
ini_set('memory_limit', '256M');

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Estudiantes.php");
require_once(ROOT_PATH . "/main-app/class/Conexion.php");

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
        // Obtener conexión PDO
        $conexionPDO = Conexion::newConnection('PDO');
        $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $matId = $_POST['mat_id'] ?? null;
        
        if (empty($matId)) {
            jsonResponse(['success' => false, 'message' => 'ID de matrícula es obligatorio.']);
        }
        
        // OPTIMIZACIÓN: Seleccionar solo los campos necesarios basados en la estructura real de la tabla
        // Evita campos BLOB o TEXT grandes que consumen memoria
        $sql = "SELECT 
                    m.mat_id,
                    m.mat_matricula,
                    m.mat_fecha,
                    m.mat_primer_apellido,
                    m.mat_segundo_apellido,
                    m.mat_nombres,
                    m.mat_nombre2,
                    m.mat_grado,
                    m.mat_grupo,
                    m.mat_genero,
                    m.mat_fecha_nacimiento,
                    m.mat_lugar_nacimiento,
                    m.mat_tipo_documento,
                    m.mat_documento,
                    m.mat_lugar_expedicion,
                    m.mat_religion,
                    m.mat_direccion,
                    m.mat_barrio,
                    m.mat_telefono,
                    m.mat_celular,
                    m.mat_celular2,
                    m.mat_estrato,
                    m.mat_tipo,
                    m.mat_estado_matricula,
                    m.mat_email,
                    m.mat_acudiente,
                    m.mat_inclusion,
                    m.mat_eps,
                    m.mat_ciudad_residencia,
                    g.gra_nombre,
                    gr.gru_nombre,
                    og_genero.ogen_nombre as genero_nombre,
                    og_estrato.ogen_nombre as estrato_nombre
                FROM ".BD_ACADEMICA.".academico_matriculas m
                LEFT JOIN ".BD_ACADEMICA.".academico_grados g ON m.mat_grado = g.gra_id AND g.institucion = m.institucion AND g.year = m.year
                LEFT JOIN ".BD_ACADEMICA.".academico_grupos gr ON m.mat_grupo = gr.gru_id AND gr.institucion = m.institucion AND gr.year = m.year
                LEFT JOIN ".BD_ADMIN.".opciones_generales og_genero ON m.mat_genero = og_genero.ogen_id
                LEFT JOIN ".BD_ADMIN.".opciones_generales og_estrato ON m.mat_estrato = og_estrato.ogen_id
                WHERE m.mat_id = :mat_id 
                AND m.mat_eliminado = 0
                AND m.institucion = :institucion
                AND m.year = :year
                LIMIT 1";
        
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
        error_log("Stack trace: " . $e->getTraceAsString());
        jsonResponse([
            'success' => false, 
            'message' => 'Error interno del servidor.',
            'error_detail' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>
