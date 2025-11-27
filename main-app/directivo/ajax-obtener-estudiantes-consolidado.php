<?php
header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

try {
    $cursoId = isset($_POST['curso_id']) ? trim($_POST['curso_id']) : '';
    $grupoId = isset($_POST['grupo_id']) ? trim($_POST['grupo_id']) : '';
    
    if (empty($cursoId) || empty($grupoId)) {
        throw new Exception('Curso y grupo son requeridos.');
    }
    
    // Obtener datos del curso
    $consultaCurso = Grados::obtenerDatosGrados($cursoId);
    $curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
    
    if (!$curso) {
        throw new Exception('Curso no encontrado.');
    }
    
    // Obtener estudiantes
    $filtro = " AND mat_grado='" . $cursoId . "' AND mat_grupo='" . $grupoId . "' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
    $consultaEstudiantes = Estudiantes::listarEstudiantesEnGrados($filtro, "", $curso, $grupoId);
    
    $estudiantes = [];
    
    if ($consultaEstudiantes !== null && $consultaEstudiantes !== false) {
        while ($estudiante = mysqli_fetch_array($consultaEstudiantes, MYSQLI_BOTH)) {
            $estudiantes[] = [
                'mat_id' => $estudiante['mat_id'],
                'nombre_completo' => trim($estudiante['mat_primer_apellido'] . ' ' . $estudiante['mat_segundo_apellido'] . ' ' . $estudiante['mat_nombres']),
                'mat_matricula' => $estudiante['mat_matricula'] ?? ''
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'estudiantes' => $estudiantes
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

