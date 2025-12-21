<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");

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
        $cursoId = $_POST['curso_id'] ?? null;
        
        if (empty($cursoId)) {
            jsonResponse(['success' => false, 'message' => 'ID de curso es obligatorio.']);
        }
        
        // Obtener datos del curso
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados WHERE gra_id=? AND institucion=? AND year=?";
        $parametros = [$cursoId, $config['conf_id_institucion'], $_SESSION["bd"]];
        $consulta = BindSQL::prepararSQL($sql, $parametros);
        $datosCurso = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        
        if (!$datosCurso) {
            jsonResponse(['success' => false, 'message' => 'Curso no encontrado.']);
        }
        
        // Validar si hay registros académicos en general (no por curso específico)
        $hayNotasRegistradas = Grados::hayRegistrosAcademicos($config);
        
        // Obtener formatos de boletín
        $formatos = [];
        $consultaFormatos = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_grupo=15");
        while($formato = mysqli_fetch_array($consultaFormatos, MYSQLI_BOTH)){
            $formatos[] = [
                'id' => $formato['ogen_id'],
                'nombre' => $formato['ogen_nombre']
            ];
        }
        
        // Obtener grados para "grado siguiente"
        $grados = [];
        $consultaGrados = mysqli_query($conexion, "SELECT gra_id, gra_nombre FROM ".BD_ACADEMICA.".academico_grados 
                                                   WHERE institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]} 
                                                   ORDER BY gra_nombre");
        while($grado = mysqli_fetch_array($consultaGrados, MYSQLI_BOTH)){
            $grados[] = [
                'id' => $grado['gra_id'],
                'nombre' => $grado['gra_nombre']
            ];
        }
        
        jsonResponse([
            'success' => true, 
            'curso' => $datosCurso,
            'formatos' => $formatos,
            'grados' => $grados,
            'hayNotasRegistradas' => $hayNotasRegistradas
        ]);
        
    } catch (Exception $e) {
        error_log("Error al obtener datos de curso: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>

