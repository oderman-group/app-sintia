<?php
$input = json_decode(file_get_contents("php://input"), true);
if(!empty($input)){
    $_POST=$input;
}
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
$usuariosClase = new Usuarios;
$response = array();
try {
$parametros = ["cpp_id_clase" => $_POST['idClase'], "institucion" => $config['conf_id_institucion'], "year" => $_SESSION["bd"]];
$filtro = " AND (TRIM(cpp_padre) = ''  OR LENGTH(cpp_padre) < 0)";
$response["cantidad"] =Clases::contar($parametros,$filtro);
$limit = " LIMIT 1";
$preguntasConsulta = Clases::traerPreguntasClases($conexion, $config, $_POST["idClase"], $filtro,$limit);
if($preguntasConsulta){
    $response["codigo"] =base64_encode($preguntasConsulta[0]["cpp_id"]);
}
$response["ok"] = true;
} catch (Exception $e) {
    $response["ok"] = false;
    $response["msg"] = $e;
    include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
}
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo json_encode($response);
exit();