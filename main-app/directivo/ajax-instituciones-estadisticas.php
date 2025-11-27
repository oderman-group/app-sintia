<?php
/**
 * OBTENER ESTADÍSTICAS DE UNA INSTITUCIÓN
 * Estudiantes, docentes, directivos y acudientes
 */

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos
Modulos::verificarPermisoDev();

try {
    $institucionId = isset($_POST['institucion_id']) ? (int)$_POST['institucion_id'] : 0;
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
    
    if (empty($institucionId)) {
        throw new Exception('ID de institución no proporcionado');
    }
    
    $estadisticas = [];
    
    // ============================================
    // ESTUDIANTES
    // ============================================
    
    // Matriculados (estado 1)
    $sqlMatriculados = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_matriculas 
                        WHERE mat_estado_matricula = 1 
                        AND mat_eliminado = 0 
                        AND institucion = $institucionId 
                        AND year = $year";
    $resultMatriculados = mysqli_fetch_array(mysqli_query($conexion, $sqlMatriculados), MYSQLI_BOTH);
    $estadisticas['estudiantes']['matriculados'] = (int)$resultMatriculados['total'];
    
    // Asistentes (estado 2)
    $sqlAsistentes = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_matriculas 
                      WHERE mat_estado_matricula = 2 
                      AND mat_eliminado = 0 
                      AND institucion = $institucionId 
                      AND year = $year";
    $resultAsistentes = mysqli_fetch_array(mysqli_query($conexion, $sqlAsistentes), MYSQLI_BOTH);
    $estadisticas['estudiantes']['asistentes'] = (int)$resultAsistentes['total'];
    
    // Cancelados (estado 3)
    $sqlCancelados = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_matriculas 
                      WHERE mat_estado_matricula = 3 
                      AND mat_eliminado = 0 
                      AND institucion = $institucionId 
                      AND year = $year";
    $resultCancelados = mysqli_fetch_array(mysqli_query($conexion, $sqlCancelados), MYSQLI_BOTH);
    $estadisticas['estudiantes']['cancelados'] = (int)$resultCancelados['total'];
    
    // No matriculados (estado 4)
    $sqlNoMatriculados = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_matriculas 
                          WHERE mat_estado_matricula = 4 
                          AND mat_eliminado = 0 
                          AND institucion = $institucionId 
                          AND year = $year";
    $resultNoMatriculados = mysqli_fetch_array(mysqli_query($conexion, $sqlNoMatriculados), MYSQLI_BOTH);
    $estadisticas['estudiantes']['no_matriculados'] = (int)$resultNoMatriculados['total'];
    
    // En inscripción (estado 5)
    $sqlInscripcion = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_matriculas 
                       WHERE mat_estado_matricula = 5 
                       AND mat_eliminado = 0 
                       AND institucion = $institucionId 
                       AND year = $year";
    $resultInscripcion = mysqli_fetch_array(mysqli_query($conexion, $sqlInscripcion), MYSQLI_BOTH);
    $estadisticas['estudiantes']['en_inscripcion'] = (int)$resultInscripcion['total'];
    
    // Eliminados (mat_eliminado = 1)
    $sqlEliminados = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_matriculas 
                      WHERE mat_eliminado = 1 
                      AND institucion = $institucionId 
                      AND year = $year";
    $resultEliminados = mysqli_fetch_array(mysqli_query($conexion, $sqlEliminados), MYSQLI_BOTH);
    $estadisticas['estudiantes']['eliminados'] = (int)$resultEliminados['total'];
    
    // Total de estudiantes (todos los no eliminados)
    $estadisticas['estudiantes']['total'] = 
        $estadisticas['estudiantes']['matriculados'] + 
        $estadisticas['estudiantes']['asistentes'] + 
        $estadisticas['estudiantes']['cancelados'] + 
        $estadisticas['estudiantes']['no_matriculados'] + 
        $estadisticas['estudiantes']['en_inscripcion'];
    
    // ============================================
    // USUARIOS
    // ============================================
    
    // Docentes (tipo 2)
    $sqlDocentes = "SELECT COUNT(*) as total FROM " . BD_GENERAL . ".usuarios 
                    WHERE uss_tipo = " . TIPO_DOCENTE . " 
                    AND institucion = $institucionId 
                    AND year = $year";
    $resultDocentes = mysqli_fetch_array(mysqli_query($conexion, $sqlDocentes), MYSQLI_BOTH);
    $estadisticas['usuarios']['docentes'] = (int)$resultDocentes['total'];
    
    // Directivos (tipo 5)
    $sqlDirectivos = "SELECT COUNT(*) as total FROM " . BD_GENERAL . ".usuarios 
                      WHERE uss_tipo = " . TIPO_DIRECTIVO . " 
                      AND institucion = $institucionId 
                      AND year = $year";
    $resultDirectivos = mysqli_fetch_array(mysqli_query($conexion, $sqlDirectivos), MYSQLI_BOTH);
    $estadisticas['usuarios']['directivos'] = (int)$resultDirectivos['total'];
    
    // Acudientes (tipo 3)
    $sqlAcudientes = "SELECT COUNT(*) as total FROM " . BD_GENERAL . ".usuarios 
                      WHERE uss_tipo = " . TIPO_ACUDIENTE . " 
                      AND institucion = $institucionId 
                      AND year = $year";
    $resultAcudientes = mysqli_fetch_array(mysqli_query($conexion, $sqlAcudientes), MYSQLI_BOTH);
    $estadisticas['usuarios']['acudientes'] = (int)$resultAcudientes['total'];
    
    // Estudiantes usuarios (tipo 4)
    $sqlEstudiantesUss = "SELECT COUNT(*) as total FROM " . BD_GENERAL . ".usuarios 
                          WHERE uss_tipo = " . TIPO_ESTUDIANTE . " 
                          AND institucion = $institucionId 
                          AND year = $year";
    $resultEstudiantesUss = mysqli_fetch_array(mysqli_query($conexion, $sqlEstudiantesUss), MYSQLI_BOTH);
    $estadisticas['usuarios']['estudiantes'] = (int)$resultEstudiantesUss['total'];
    
    // Total usuarios
    $estadisticas['usuarios']['total'] = 
        $estadisticas['usuarios']['docentes'] + 
        $estadisticas['usuarios']['directivos'] + 
        $estadisticas['usuarios']['acudientes'] + 
        $estadisticas['usuarios']['estudiantes'];
    
    // ============================================
    // DATOS ADICIONALES
    // ============================================
    
    // Cursos/Grados
    $sqlCursos = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_grados 
                  WHERE institucion = $institucionId 
                  AND year = $year";
    $resultCursos = mysqli_fetch_array(mysqli_query($conexion, $sqlCursos), MYSQLI_BOTH);
    $estadisticas['otros']['cursos'] = (int)$resultCursos['total'];
    
    // Grupos
    $sqlGrupos = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_grupos 
                  WHERE institucion = $institucionId 
                  AND year = $year";
    $resultGrupos = mysqli_fetch_array(mysqli_query($conexion, $sqlGrupos), MYSQLI_BOTH);
    $estadisticas['otros']['grupos'] = (int)$resultGrupos['total'];
    
    // Cargas académicas activas
    $sqlCargas = "SELECT COUNT(*) as total FROM " . BD_ACADEMICA . ".academico_cargas 
                  WHERE car_activa = 1 
                  AND institucion = $institucionId 
                  AND year = $year";
    $resultCargas = mysqli_fetch_array(mysqli_query($conexion, $sqlCargas), MYSQLI_BOTH);
    $estadisticas['otros']['cargas'] = (int)$resultCargas['total'];
    
    echo json_encode([
        'success' => true,
        'estadisticas' => $estadisticas,
        'institucion_id' => $institucionId,
        'year' => $year
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
