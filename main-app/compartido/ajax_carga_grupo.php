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
        'curso' => $_POST['curso'],
        'grupo' => $_POST['grupo']
    ];

    $listadoGrupos = CargaAcademica::traerCargasMateriasPorCursoGrupo($config,  $_POST['curso'], $_POST['grupo']);
   
    if ($listadoGrupos) {
        $response['result'] =  $listadoGrupos->fetch_all(MYSQLI_ASSOC);
    }

    $response['ok'] = true;
} catch (Exception $e) {
    $response['ok'] = false;
    $response['msg'] = $e;
}


echo json_encode($response);
exit();
