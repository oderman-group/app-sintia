<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0151';

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Academico/Indicador_Estudiantes_Inclusion.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();


try {
	$camposWhere = [
		'aii_id'  => $_POST["aii_id"],
	]; 

	$datos = Academico_Indicadores_Estudiantes_Inclusion::Select($camposWhere,'*',BD_ACADEMICA);

	$respuesta["estado"]   = 'ok';
	$respuesta["mensaje"]  = "No hay datos para mostrar";
	$respuesta["datos"] = $datos->fetchAll(PDO::FETCH_ASSOC);

	if($datos->rowCount() > 0) 
		$respuesta["mensaje"] = "Listado de indicador x id: ".  $_POST["aii_id"]; 

	
} catch (Exception $e) {
	$respuesta["estado"]  = 'ko';
	$respuesta["mensaje"] = $e;
	include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
	
}

echo json_encode($respuesta);
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();