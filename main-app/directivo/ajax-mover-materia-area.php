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
        // Verificar que la conexión esté disponible
        if (!isset($conexionPDO)) {
            jsonResponse(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
        }
        
        // Obtener datos del POST
        $materiaId = $_POST['materia_id'] ?? null;
        $nuevaAreaId = $_POST['nueva_area_id'] ?? null;
        
        error_log("Mover materia - materiaId: $materiaId, nuevaAreaId: " . ($nuevaAreaId ?? 'NULL'));
        
        // Validar campos obligatorios
        if (empty($materiaId)) {
            jsonResponse(['success' => false, 'message' => 'ID de materia es obligatorio.']);
        }
        
        // Si nueva_area_id es null, significa que se está quitando del área
        $quitandoArea = ($nuevaAreaId === null || $nuevaAreaId === 'null' || $nuevaAreaId === '');
        
        if ($quitandoArea) {
            $nuevaAreaId = null;
        }
        
        // Verificar que la materia exista
        $sqlVerificar = "SELECT mat_id, mat_nombre, mat_area 
                         FROM ".BD_ACADEMICA.".academico_materias 
                         WHERE mat_id = :materia_id 
                         AND institucion = :institucion 
                         AND year = :year";
        
        $stmtVerificar = $conexionPDO->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':materia_id', $materiaId, PDO::PARAM_STR);
        $stmtVerificar->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtVerificar->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        $stmtVerificar->execute();
        
        $materia = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
        
        if (!$materia) {
            jsonResponse(['success' => false, 'message' => 'Materia no encontrada.']);
        }
        
        // Obtener el nombre del área de destino (si no es null)
        $areaNombre = 'Sin área';
        
        if (!$quitandoArea) {
            $sqlArea = "SELECT ar_nombre 
                        FROM ".BD_ACADEMICA.".academico_areas 
                        WHERE ar_id = :area_id 
                        AND institucion = :institucion 
                        AND year = :year";
            
            $stmtArea = $conexionPDO->prepare($sqlArea);
            $stmtArea->bindParam(':area_id', $nuevaAreaId, PDO::PARAM_STR);
            $stmtArea->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtArea->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            $stmtArea->execute();
            
            $area = $stmtArea->fetch(PDO::FETCH_ASSOC);
            
            if (!$area) {
                jsonResponse(['success' => false, 'message' => 'Área de destino no encontrada.']);
            }
            
            $areaNombre = $area['ar_nombre'];
        }
        
        // Actualizar el área de la materia
        $sqlActualizar = "UPDATE ".BD_ACADEMICA.".academico_materias 
                         SET mat_area = :nueva_area 
                         WHERE mat_id = :materia_id 
                         AND institucion = :institucion 
                         AND year = :year";
        
        $stmtActualizar = $conexionPDO->prepare($sqlActualizar);
        
        // Si es null, usar PDO::PARAM_NULL, si no, usar PDO::PARAM_STR
        if ($nuevaAreaId === null) {
            $stmtActualizar->bindValue(':nueva_area', null, PDO::PARAM_NULL);
        } else {
            $stmtActualizar->bindParam(':nueva_area', $nuevaAreaId, PDO::PARAM_STR);
        }
        
        $stmtActualizar->bindParam(':materia_id', $materiaId, PDO::PARAM_STR);
        $stmtActualizar->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtActualizar->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        error_log("Ejecutando UPDATE - materia_id: $materiaId, nueva_area: " . ($nuevaAreaId ?? 'NULL') . ", institucion: " . $config['conf_id_institucion'] . ", year: " . $_SESSION["bd"]);
        
        $resultado = $stmtActualizar->execute();
        
        error_log("Resultado UPDATE: " . ($resultado ? 'true' : 'false'));
        
        if ($resultado === false) {
            jsonResponse(['success' => false, 'message' => 'No se pudo actualizar la materia.']);
        }
        
        $filasAfectadas = $stmtActualizar->rowCount();
        
        error_log("Filas afectadas: " . $filasAfectadas);
        
        if ($filasAfectadas === 0) {
            error_log("ADVERTENCIA: No se afectaron filas");
            jsonResponse(['success' => false, 'message' => 'No se realizaron cambios. Verifique que la materia pertenezca a la institución y año correctos.']);
        }
        
        $mensaje = $quitandoArea 
            ? 'La materia "' . $materia['mat_nombre'] . '" ha sido quitada del área'
            : 'La materia "' . $materia['mat_nombre'] . '" ha sido movida al área "' . $areaNombre . '"';
        
        jsonResponse([
            'success' => true, 
            'message' => $mensaje
        ]);
        
    } catch (Exception $e) {
        error_log("Error al mover materia entre áreas: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

