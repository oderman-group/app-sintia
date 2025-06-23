<?php

$input = json_decode(file_get_contents("php://input"), true);

if (!empty($input)) {
    $_POST = $input;
}

include("session-compartida.php");

require_once(ROOT_PATH . "/main-app/class/App/Academico/Matricula.php");

$response = array();

try {
    $cursosSelect = $_POST['cursos'];
    $gruposSelect = $_POST['grupos'];
    $listadoGrupos = Matricula::listarEsdutiantes($cursosSelect, $gruposSelect);
    if ($listadoGrupos) {
        $response["result"] = $listadoGrupos;
        $response["ok"] = true;
    }else{
        $response["ok"] = false;
        $response["msg"] = 'No se encontraron estudiantes matriculados en el curso '.$cursosSelect.' con el grupo '.$gruposSelect. ' para el año '.$_SESSION["bd"];
    }
    
} catch (Exception $e) {
    $response["ok"] = false;
    $response["msg"] = $e;
    include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
echo json_encode($response);
exit();
