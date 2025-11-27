<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Grados.php");
require_once(ROOT_PATH . "/main-app/class/Grupos.php");
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

try {
    // Consulta optimizada de cargas con todos los datos necesarios
    $sql = "SELECT 
        car.car_id,
        car.car_docente,
        car.car_curso,
        car.car_grupo,
        car.car_materia,
        car.car_periodo,
        car.car_activa,
        car.car_director_grupo,
        car.car_ih,
        car.car_permiso2,
        car.car_indicador_automatico,
        car.car_maximos_indicadores,
        car.car_maximas_calificaciones,
        CONCAT_WS(' ', uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2) as docente_nombre,
        gra.gra_nombre as curso_nombre,
        gru.gru_nombre as grupo_nombre,
        mat.mat_nombre as asignatura_nombre
    FROM " . BD_ACADEMICA . ".academico_cargas car
    LEFT JOIN " . BD_GENERAL . ".usuarios uss ON uss.uss_id = car.car_docente 
        AND uss.institucion = car.institucion 
        AND uss.year = car.year
    LEFT JOIN " . BD_ACADEMICA . ".academico_grados gra ON gra.gra_id = car.car_curso 
        AND gra.institucion = car.institucion 
        AND gra.year = car.year
    LEFT JOIN " . BD_ACADEMICA . ".academico_grupos gru ON gru.gru_id = car.car_grupo 
        AND gru.institucion = car.institucion 
        AND gru.year = car.year
    LEFT JOIN " . BD_ACADEMICA . ".academico_materias mat ON mat.mat_id = car.car_materia 
        AND mat.institucion = car.institucion 
        AND mat.year = car.year
    WHERE car.institucion = ? 
        AND car.year = ?
    ORDER BY docente_nombre ASC, gra.gra_nombre ASC, mat.mat_nombre ASC";
    
    $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
    $resultado = BindSQL::prepararSQL($sql, $parametros);
    
    $cargas = [];
    $docentes = [];
    $cursos = [];
    
    while ($row = mysqli_fetch_assoc($resultado)) {
        $cargas[] = $row;
        
        // Contar docentes únicos
        if (!isset($docentes[$row['car_docente']])) {
            $docentes[$row['car_docente']] = true;
        }
        
        // Contar cursos únicos
        if (!isset($cursos[$row['car_curso']])) {
            $cursos[$row['car_curso']] = true;
        }
    }
    
    $stats = [
        'docentes' => count($docentes),
        'cargas' => count($cargas),
        'cursos' => count($cursos)
    ];
    
    jsonResponse([
        'success' => true,
        'data' => $cargas,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Error en ajax-obtener-cargas-visual.php: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Error al obtener las cargas: ' . $e->getMessage()
    ]);
}
?>

