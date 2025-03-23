<?php
$input = json_decode(file_get_contents("php://input"), true);
if(!empty($input)){
    $_POST=$input;
}
include("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/App/Academico/Calificacion/Vista_historial_calificaiones.php");
$response = [];
try {
$historialEstudiante = Vista_historial_calificaciones::listarHistorialCalificaionesEstudiante(grado: $_POST["grado"], grupo: $_POST["grupo"],  idEstudiante: $_POST["estudiante"],year: $_POST["year"]);
if($historialEstudiante){
    $response["data"] = $historialEstudiante;
}else{
	$response["data"] = [];
}
$response["ok"] = true;
} catch (Exception $e) {
    $response["ok"] = false;
    $response["msg"] =$e->getMessage();
    include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
}
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo json_encode($response);
exit();