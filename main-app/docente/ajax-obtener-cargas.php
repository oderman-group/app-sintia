<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

header('Content-Type: application/json');

try {
    // Obtener todas las cargas del docente actual
    $sql = "SELECT car.*, am.*, gra.*, gru.*, car.id_nuevo AS id_nuevo_carga 
            FROM ".BD_ACADEMICA.".academico_cargas car 
            INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
            INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
            INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
            WHERE car_docente=? AND car_activa=1 AND car.institucion=? AND car.year=?
            ORDER BY car.car_periodo DESC, am.mat_nombre ASC";

    $parametros = [$_SESSION["id"], $config['conf_id_institucion'], $_SESSION["bd"]];
    $resultado = BindSQL::prepararSQL($sql, $parametros);
    
    $cargas = [];
    while ($carga = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
        // Calcular cantidad de estudiantes para cada carga
        $cantidadEstudiantes = 0;
        
        if ($carga['gra_tipo'] == GRADO_INDIVIDUAL) {
            $cantidadEstudiantes = Estudiantes::contarEstudiantesParaDocentesMT($carga);
        } else {
            $filtroDocentes = " AND mat_grado='".$carga['car_curso']."' AND mat_grupo='".$carga['car_grupo']."'";
            $cantidadEstudiantes = Estudiantes::contarEstudiantesParaDocentes($filtroDocentes);
        }
        
        $cargas[] = [
            'car_id' => $carga['car_id'],
            'car_periodo' => $carga['car_periodo'],
            'car_curso' => $carga['car_curso'],
            'car_grupo' => $carga['car_grupo'],
            'car_materia' => $carga['car_materia'],
            'car_ih' => $carga['car_ih'],
            'car_director_grupo' => $carga['car_director_grupo'],
            'mat_nombre' => $carga['mat_nombre'],
            'mat_valor' => $carga['mat_valor'],
            'gra_nombre' => $carga['gra_nombre'],
            'gra_tipo' => $carga['gra_tipo'],
            'gra_periodos' => $carga['gra_periodos'],
            'gru_nombre' => $carga['gru_nombre'],
            'cantidad_estudiantes' => $cantidadEstudiantes,
            'id_nuevo_carga' => $carga['id_nuevo_carga']
        ];
    }
    
    // Filtrar la carga actual (no mostrarla en la lista)
    $cargaActual = $_COOKIE["carga"] ?? '';
    $cargas = array_filter($cargas, function($carga) use ($cargaActual) {
        return $carga['car_id'] != $cargaActual;
    });
    
    echo json_encode([
        'success' => true,
        'data' => array_values($cargas),
        'total' => count($cargas)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener las cargas académicas: ' . $e->getMessage()
    ]);
}
?>
