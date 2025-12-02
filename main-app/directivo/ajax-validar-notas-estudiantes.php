<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Estudiante.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

header('Content-Type: application/json');

try {
    $estudiantes = isset($_POST['estudiantes']) ? $_POST['estudiantes'] : [];
    
    if (empty($estudiantes) || !is_array($estudiantes)) {
        echo json_encode([
            'success' => false,
            'error' => 'No se proporcionaron estudiantes para validar'
        ]);
        exit;
    }
    
    $estudiantesConNotas = [];
    $estudiantesMatriculados = [];
    $estudiantesAsistentes = [];
    $estudiantesNoMatriculados = [];
    $estudiantesEnInscripcion = [];
    $estudiantesCancelados = [];
    $estadosEstudiantes = []; // Array con estado de cada estudiante: [idEstudiante => estado]
    $puedeModificarGradoGrupo = $config['conf_puede_cambiar_grado_y_grupo'] == 1;
    
    foreach ($estudiantes as $idEstudiante) {
        if (empty($idEstudiante)) {
            continue;
        }
        
        // Obtener datos del estudiante para verificar estado de matrícula
        $datosEstudiante = Estudiantes::obtenerDatosEstudiante($idEstudiante);
        if (!empty($datosEstudiante)) {
            $estadoMatricula = (int)$datosEstudiante['mat_estado_matricula'];
            $estadosEstudiantes[$idEstudiante] = $estadoMatricula;
            
            // Clasificar estudiantes por estado
            if ($estadoMatricula == Estudiantes::ESTADO_MATRICULADO) {
                $estudiantesMatriculados[] = $idEstudiante;
            } elseif ($estadoMatricula == Estudiantes::ESTADO_ASISTENTE) {
                $estudiantesAsistentes[] = $idEstudiante;
            } elseif ($estadoMatricula == Estudiantes::ESTADO_NO_MATRICULADO) {
                $estudiantesNoMatriculados[] = $idEstudiante;
            } elseif ($estadoMatricula == Estudiantes::ESTADO_EN_INSCRIPCION) {
                $estudiantesEnInscripcion[] = $idEstudiante;
            } elseif ($estadoMatricula == Estudiantes::ESTADO_CANCELADO) {
                $estudiantesCancelados[] = $idEstudiante;
            }
        }
        
        // Crear instancia del estudiante para validar notas
        $EstudianteObj = new Administrativo_Usuario_Estudiante(['mat_id' => $idEstudiante]);
        $tieneRegistrosAcademicos = (bool) $EstudianteObj->tieneRegistrosAcademicos();
        
        if ($tieneRegistrosAcademicos && !$puedeModificarGradoGrupo) {
            $estudiantesConNotas[] = $idEstudiante;
        }
    }
    
    $tieneAlgunoConNotas = count($estudiantesConNotas) > 0;
    $tieneAlgunoMatriculado = count($estudiantesMatriculados) > 0;
    $tieneAlgunoAsistente = count($estudiantesAsistentes) > 0;
    $tieneAlgunoNoMatriculado = count($estudiantesNoMatriculados) > 0;
    
    echo json_encode([
        'success' => true,
        'tieneNotas' => $tieneAlgunoConNotas,
        'estudiantesConNotas' => $estudiantesConNotas,
        'cantidadConNotas' => count($estudiantesConNotas),
        'puedeModificarGradoGrupo' => $puedeModificarGradoGrupo,
        'tieneMatriculados' => $tieneAlgunoMatriculado,
        'estudiantesMatriculados' => $estudiantesMatriculados,
        'cantidadMatriculados' => count($estudiantesMatriculados),
        'tieneAsistentes' => $tieneAlgunoAsistente,
        'cantidadAsistentes' => count($estudiantesAsistentes),
        'tieneNoMatriculados' => $tieneAlgunoNoMatriculado,
        'cantidadNoMatriculados' => count($estudiantesNoMatriculados),
        'estadosEstudiantes' => $estadosEstudiantes // Información completa de estados
    ]);
    
} catch (Exception $e) {
    error_log('Error en ajax-validar-notas-estudiantes.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al validar notas: ' . $e->getMessage()
    ]);
}
?>

