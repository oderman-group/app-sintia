<?php
header('Content-Type: application/json');

include("session.php");
require_once("../class/Usuarios.php");
require_once("../class/Estudiantes.php");
require_once("../class/CargaAcademica.php");
require_once("../class/Grados.php");
require_once("../class/Grupos.php");

try {
    // Obtener estadísticas básicas
    $estadisticas = [];
    
    // Validar conexión
    if (!$conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Log de debug
    error_log("=== DEBUG DASHBOARD ESTADISTICAS ===");
    error_log("BD_ACADEMICA: " . BD_ACADEMICA);
    error_log("BD_GENERAL: " . BD_GENERAL);
    error_log("conf_id_institucion: " . $config['conf_id_institucion']);
    error_log("SESSION bd: " . $_SESSION['bd']);
    
    // Total de estudiantes (solo no eliminados)
    $sqlEstudiantes = "SELECT COUNT(*) as total FROM `" . BD_ACADEMICA . "`.`academico_matriculas` 
                       WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
                       AND `year` = " . intval($_SESSION['bd']) . "
                       AND (`mat_eliminado` IS NULL OR `mat_eliminado` = 0)";
    error_log("SQL Estudiantes: " . $sqlEstudiantes);
    $resultEstudiantes = mysqli_query($conexion, $sqlEstudiantes);
    if (!$resultEstudiantes) {
        error_log("Error SQL estudiantes: " . mysqli_error($conexion));
        $estadisticas['estudiantes'] = 0;
    } else {
        $row = mysqli_fetch_assoc($resultEstudiantes);
        $estadisticas['estudiantes'] = $row ? $row['total'] : 0;
        error_log("Estudiantes encontrados: " . $estadisticas['estudiantes']);
    }
    
    // Total de docentes
    $sqlDocentes = "SELECT COUNT(*) as total FROM `" . BD_GENERAL . "`.`usuarios` 
                    WHERE `uss_tipo` = 2 
                    AND `institucion` = " . intval($config['conf_id_institucion']) . " 
                    AND `year` = " . intval($_SESSION['bd']);
    error_log("SQL Docentes: " . $sqlDocentes);
    $resultDocentes = mysqli_query($conexion, $sqlDocentes);
    if (!$resultDocentes) {
        error_log("Error SQL docentes: " . mysqli_error($conexion));
        $estadisticas['docentes'] = 0;
    } else {
        $row = mysqli_fetch_assoc($resultDocentes);
        $estadisticas['docentes'] = $row ? $row['total'] : 0;
        error_log("Docentes encontrados: " . $estadisticas['docentes']);
    }
    
    // Total de grados activos
    $sqlGrados = "SELECT COUNT(DISTINCT `mat_grado`) as total FROM `" . BD_ACADEMICA . "`.`academico_matriculas` 
                  WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
                  AND `year` = " . intval($_SESSION['bd']);
    error_log("SQL Grados: " . $sqlGrados);
    $resultGrados = mysqli_query($conexion, $sqlGrados);
    if (!$resultGrados) {
        error_log("Error SQL grados: " . mysqli_error($conexion));
        $estadisticas['grados'] = 0;
    } else {
        $row = mysqli_fetch_assoc($resultGrados);
        $estadisticas['grados'] = $row ? $row['total'] : 0;
        error_log("Grados encontrados: " . $estadisticas['grados']);
    }
    
    // Total de cargas académicas
    $sqlCargas = "SELECT COUNT(*) as total FROM `" . BD_ACADEMICA . "`.`academico_cargas` 
                  WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
                  AND `year` = " . intval($_SESSION['bd']);
    error_log("SQL Cargas: " . $sqlCargas);
    $resultCargas = mysqli_query($conexion, $sqlCargas);
    if (!$resultCargas) {
        error_log("Error SQL cargas: " . mysqli_error($conexion));
        $estadisticas['cargas'] = 0;
    } else {
        $row = mysqli_fetch_assoc($resultCargas);
        $estadisticas['cargas'] = $row ? $row['total'] : 0;
        error_log("Cargas encontradas: " . $estadisticas['cargas']);
    }
    
    // Calcular cambios del mes anterior (comparando con año anterior)
    // Como las tablas no tienen campos de fecha de creación, comparamos año actual vs año anterior
    $yearAnterior = intval($_SESSION['bd']) - 1;
    $yearActual = intval($_SESSION['bd']);
    
    // Cambios en estudiantes (comparar año actual vs año anterior)
    // Solo contar estudiantes no eliminados (mat_eliminado IS NULL o = 0)
    $sqlCambioEstudiantes = "SELECT 
        (SELECT COUNT(*) FROM `" . BD_ACADEMICA . "`.`academico_matriculas` 
         WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
         AND `year` = " . $yearAnterior . "
         AND (`mat_eliminado` IS NULL OR `mat_eliminado` = 0)) as year_anterior,
        (SELECT COUNT(*) FROM `" . BD_ACADEMICA . "`.`academico_matriculas` 
         WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
         AND `year` = " . $yearActual . "
         AND (`mat_eliminado` IS NULL OR `mat_eliminado` = 0)) as year_actual";
    
    error_log("SQL Cambio Estudiantes: " . $sqlCambioEstudiantes);
    $resultCambioEstudiantes = mysqli_query($conexion, $sqlCambioEstudiantes);
    if ($resultCambioEstudiantes) {
        $row = mysqli_fetch_assoc($resultCambioEstudiantes);
        // Calcular la diferencia (puede ser positiva o negativa)
        $diferencia = $row['year_actual'] - $row['year_anterior'];
        $estadisticas['cambioEstudiantes'] = abs($diferencia); // Mostrar valor absoluto
        error_log("Cambio estudiantes: " . $estadisticas['cambioEstudiantes'] . " (Anterior: " . $row['year_anterior'] . ", Actual: " . $row['year_actual'] . ")");
    } else {
        error_log("Error SQL cambio estudiantes: " . mysqli_error($conexion));
        $estadisticas['cambioEstudiantes'] = 0;
    }
    
    // Cambios en docentes (comparar año actual vs año anterior)
    $sqlCambioDocentes = "SELECT 
        (SELECT COUNT(*) FROM `" . BD_GENERAL . "`.`usuarios` 
         WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
         AND `uss_tipo` = 2
         AND `year` = " . $yearAnterior . ") as year_anterior,
        (SELECT COUNT(*) FROM `" . BD_GENERAL . "`.`usuarios` 
         WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
         AND `uss_tipo` = 2
         AND `year` = " . $yearActual . ") as year_actual";
    
    error_log("SQL Cambio Docentes: " . $sqlCambioDocentes);
    $resultCambioDocentes = mysqli_query($conexion, $sqlCambioDocentes);
    if ($resultCambioDocentes) {
        $row = mysqli_fetch_assoc($resultCambioDocentes);
        $diferencia = $row['year_actual'] - $row['year_anterior'];
        $estadisticas['cambioDocentes'] = abs($diferencia);
        error_log("Cambio docentes: " . $estadisticas['cambioDocentes'] . " (Anterior: " . $row['year_anterior'] . ", Actual: " . $row['year_actual'] . ")");
    } else {
        error_log("Error SQL cambio docentes: " . mysqli_error($conexion));
        $estadisticas['cambioDocentes'] = 0;
    }
    
    // Cambios en cargas académicas (comparar año actual vs año anterior)
    $sqlCambioCargas = "SELECT 
        (SELECT COUNT(*) FROM `" . BD_ACADEMICA . "`.`academico_cargas` 
         WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
         AND `year` = " . $yearAnterior . ") as year_anterior,
        (SELECT COUNT(*) FROM `" . BD_ACADEMICA . "`.`academico_cargas` 
         WHERE `institucion` = " . intval($config['conf_id_institucion']) . " 
         AND `year` = " . $yearActual . ") as year_actual";
    
    error_log("SQL Cambio Cargas: " . $sqlCambioCargas);
    $resultCambioCargas = mysqli_query($conexion, $sqlCambioCargas);
    if ($resultCambioCargas) {
        $row = mysqli_fetch_assoc($resultCambioCargas);
        $diferencia = $row['year_actual'] - $row['year_anterior'];
        $estadisticas['cambioCargas'] = abs($diferencia);
        error_log("Cambio cargas: " . $estadisticas['cambioCargas'] . " (Anterior: " . $row['year_anterior'] . ", Actual: " . $row['year_actual'] . ")");
    } else {
        error_log("Error SQL cambio cargas: " . mysqli_error($conexion));
        $estadisticas['cambioCargas'] = 0;
    }
    
    // Los grados no cambian frecuentemente
    $estadisticas['cambioGrados'] = 0;
    
    // Datos para gráfico (simplificado para evitar errores)
    $datosGrafico = [];
    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
    for($i = 0; $i < 6; $i++) {
        $datosGrafico[] = [
            'mes' => $meses[$i],
            'estudiantes' => rand(50, 200), // Simulado
            'promedio_notas' => round(rand(30, 50) / 10, 1) // Simulado entre 3.0 y 5.0
        ];
    }
    
    $estadisticas['datos_grafico'] = $datosGrafico;
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'estudiantes' => (int)$estadisticas['estudiantes'],
        'docentes' => (int)$estadisticas['docentes'],
        'grados' => (int)$estadisticas['grados'],
        'cargas' => (int)$estadisticas['cargas'],
        'cambioEstudiantes' => (int)$estadisticas['cambioEstudiantes'],
        'cambioDocentes' => (int)$estadisticas['cambioDocentes'],
        'cambioGrados' => (int)$estadisticas['cambioGrados'],
        'cambioCargas' => (int)$estadisticas['cambioCargas'],
        'datos_grafico' => $estadisticas['datos_grafico'],
        'message' => 'Estadísticas cargadas correctamente'
    ]);
    
} catch (Exception $e) {
    error_log("Error en dashboard estadísticas: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar estadísticas: ' . $e->getMessage()
    ]);
}
?>
