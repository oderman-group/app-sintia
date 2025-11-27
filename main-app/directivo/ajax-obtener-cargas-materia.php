<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

ob_clean();
ob_start();
include("session.php");
require_once(ROOT_PATH . "/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");

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
        $materiaId = $_POST['materia_id'] ?? null;
        
        if (empty($materiaId)) {
            jsonResponse(['success' => false, 'message' => 'ID de materia es obligatorio.']);
        }
        
        // Consultar cargas académicas de la materia
        $sql = "SELECT 
                    car.car_id,
                    car.car_ih,
                    car.car_periodo,
                    car.car_director_grupo,
                    CONCAT(gra.gra_nombre, ' ', gru.gru_nombre) as curso,
                    uss.uss_nombre,
                    uss.uss_nombre2,
                    uss.uss_apellido1,
                    uss.uss_apellido2
                FROM ".BD_ACADEMICA.".academico_cargas car
                INNER JOIN ".BD_ACADEMICA.".academico_grados gra 
                    ON gra.gra_id = car.car_curso 
                    AND gra.institucion = car.institucion 
                    AND gra.year = car.year
                INNER JOIN ".BD_ACADEMICA.".academico_grupos gru 
                    ON gru.gru_id = car.car_grupo 
                    AND gru.institucion = car.institucion 
                    AND gru.year = car.year
                INNER JOIN ".BD_GENERAL.".usuarios uss 
                    ON uss.uss_id = car.car_docente 
                    AND uss.institucion = car.institucion 
                    AND uss.year = car.year
                WHERE car.car_materia = :materia_id
                AND car.institucion = :institucion
                AND car.year = :year
                ORDER BY car.car_periodo, gra.gra_nombre, gru.gru_nombre";
        
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(':materia_id', $materiaId, PDO::PARAM_STR);
        $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
        
        $stmt->execute();
        
        $cargas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cargas[] = [
                'car_id' => $row['car_id'],
                'ih' => $row['car_ih'],
                'periodo' => $row['car_periodo'],
                'curso' => $row['curso'],
                'docente' => trim($row['uss_nombre'] . ' ' . $row['uss_nombre2'] . ' ' . $row['uss_apellido1'] . ' ' . $row['uss_apellido2']),
                'director_grupo' => $row['car_director_grupo'] == 1 ? 'SI' : 'NO'
            ];
        }
        
        jsonResponse(['success' => true, 'cargas' => $cargas]);
        
    } catch (Exception $e) {
        error_log("Error al obtener cargas de la materia: " . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
}
?>


