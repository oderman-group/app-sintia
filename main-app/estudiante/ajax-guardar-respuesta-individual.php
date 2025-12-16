<?php
include("session.php");
include("verificar-usuario.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'ES0057';
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

header('Content-Type: application/json; charset=utf-8');

function jsonResponse($data) {
    while (ob_get_level()) { ob_end_clean(); }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    $idE = $_POST['idE'] ?? '';
    $idPregunta = $_POST['idPregunta'] ?? '';
    $idRespuesta = $_POST['idRespuesta'] ?? '';
    $archivo = '';
    
    if (empty($idE) || empty($idPregunta)) {
        jsonResponse(['success' => false, 'message' => 'Datos incompletos']);
    }
    
    // Verificar si el estudiante ya hizo la evaluación
    $nume = Evaluaciones::verificarEstudianteEvaluacion($conexion, $config, $idE, $datosEstudianteActual['mat_id']);
    if ($nume > 0) {
        jsonResponse(['success' => false, 'message' => 'Ya completaste esta evaluación']);
    }
    
    // Si es pregunta de tipo archivo (tipo 3)
    if (!empty($_FILES['archivo']['name'])) {
        $idPreguntaFile = $idPregunta;
        $destino = "../files/evaluaciones";
        $nombreInputFile = 'archivo';
        $archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
        $extension = end(explode(".", $_FILES['archivo']['name']));
        $archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_eva_res_').".".$extension;
        @unlink($destino."/".$archivo);
        $archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile);
    }
    
    // Si no hay respuesta seleccionada, usar 0
    if (empty($idRespuesta)) {
        $idRespuesta = '0';
    }
    
    // Eliminar respuesta anterior de esta pregunta si existe
    // Primero eliminamos todas las respuestas de este estudiante para esta evaluación y pregunta
    $sqlEliminar = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones_resultados 
                    WHERE res_id_evaluacion=? AND res_id_estudiante=? AND res_id_pregunta=? AND institucion=? AND year=?";
    $parametrosEliminar = [$idE, $datosEstudianteActual['mat_id'], $idPregunta, $config['conf_id_institucion'], $_SESSION["bd"]];
    BindSQL::prepararSQL($sqlEliminar, $parametrosEliminar);
    
    // Guardar la nueva respuesta
    Evaluaciones::guardarResultado($conexion, $config, $idE, $datosEstudianteActual['mat_id'], $idPregunta, $idRespuesta, $archivo);
    
    jsonResponse(['success' => true, 'message' => 'Respuesta guardada correctamente']);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

