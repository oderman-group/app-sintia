<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Asignaturas.php");

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
        error_log("ajax-obtener-materias-area.php llamado");
        error_log("POST data: " . json_encode($_POST));
        
        $areaId = $_POST['area_id'] ?? null;
        $otrasAreas = isset($_POST['otras_areas']) ? true : false;
        $filtroArea = $_POST['filtro_area'] ?? '';
        
        error_log("areaId: $areaId, otrasAreas: " . ($otrasAreas ? 'true' : 'false') . ", filtroArea: $filtroArea");
        
        if (empty($areaId)) {
            error_log("Error: ID de área vacío");
            jsonResponse(['success' => false, 'message' => 'ID de área es obligatorio.']);
        }
        
        if ($otrasAreas) {
            // Obtener materias de otras áreas (no del área actual)
            if (!empty($filtroArea)) {
                // Filtrar por un área específica
                $sql = "SELECT 
                            mat.mat_id,
                            mat.mat_nombre,
                            mat.mat_area,
                            ar.ar_nombre
                        FROM ".BD_ACADEMICA.".academico_materias mat
                        LEFT JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id = mat.mat_area AND ar.institucion = mat.institucion AND ar.year = mat.year
                        WHERE mat.mat_area = :filtro_area
                        AND mat.institucion = :institucion
                        AND mat.year = :year
                        ORDER BY mat.mat_nombre";
                
                $stmt = $conexionPDO->prepare($sql);
                $stmt->bindParam(':filtro_area', $filtroArea, PDO::PARAM_STR);
                $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
                $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            } else {
                // Todas las áreas excepto la actual
                $sql = "SELECT 
                            mat.mat_id,
                            mat.mat_nombre,
                            mat.mat_area,
                            ar.ar_nombre
                        FROM ".BD_ACADEMICA.".academico_materias mat
                        LEFT JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id = mat.mat_area AND ar.institucion = mat.institucion AND ar.year = mat.year
                        WHERE (mat.mat_area != :area_id OR mat.mat_area IS NULL)
                        AND mat.institucion = :institucion
                        AND mat.year = :year
                        ORDER BY ar.ar_nombre, mat.mat_nombre";
                
                $stmt = $conexionPDO->prepare($sql);
                $stmt->bindParam(':area_id', $areaId, PDO::PARAM_STR);
                $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
                $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            }
        } else {
            // Obtener materias del área actual
            $sql = "SELECT 
                        mat.mat_id,
                        mat.mat_nombre,
                        mat.mat_area,
                        ar.ar_nombre
                    FROM ".BD_ACADEMICA.".academico_materias mat
                    LEFT JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id = mat.mat_area AND ar.institucion = mat.institucion AND ar.year = mat.year
                    WHERE mat.mat_area = :area_id
                    AND mat.institucion = :institucion
                    AND mat.year = :year
                    ORDER BY mat.mat_nombre";
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(':area_id', $areaId, PDO::PARAM_STR);
            $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Total de materias encontradas: " . count($materias));
        error_log("Materias: " . json_encode($materias));
        
        jsonResponse(['success' => true, 'materias' => $materias]);
        
    } catch (Exception $e) {
        error_log("Error al obtener materias del área: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

