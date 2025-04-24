<?php

$input = json_decode(file_get_contents('php://input'), true);
if (!empty($input)) {
    $_POST = $input;
}

include 'session-compartida.php';
require_once ROOT_PATH . '/main-app/class/CargaAcademica.php';

$response = [];

try {
    $parametros = [
        'cursos' => $_POST['cursos']
    ];

    $cursosSelect[0] = $_POST['cursos'];
    $listadoGrupos = CargaAcademica::listarGruposCursos($cursosSelect);

    if ($listadoGrupos) {
        $response['result'] = $listadoGrupos;
        $response['ok'] = true;
    }else{
        $response['ok'] = false;
        $response['msg'] = 'No se encontraron Grupos para el curso '.$_POST['cursos'].' en el año '.$_SESSION["bd"];
    }

    
} catch (Exception $e) {
    $response['ok'] = false;
    $response['msg'] = $e;
    include ROOT_PATH . '/main-app/compartido/error-catch-to-report.php';
}

include ROOT_PATH . '/main-app/compartido/guardar-historial-acciones.php';

echo json_encode($response);
exit();
