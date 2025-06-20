<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0154';

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Academico/Indicador_Estudiantes_Inclusion.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();

try {

	$camposWhere = [
		'aii_id'  => $_POST["aii_id"],
	]; 

	if(!Academico_Indicadores_Estudiantes_Inclusion::delete($camposWhere,BD_ACADEMICA)){
		$respuesta["estado"]   = 'ko';
		$respuesta["mensaje"]  =  "No se pudo eliminar el indicador!";
	}else{
		$respuesta["estado"]   = 'ok';
		$respuesta["mensaje"]  = "Indicador eliminado con Exito!";
	}
			
} catch (Exception $e) {
	$respuesta["estado"]  = 'ko';
	$respuesta["mensaje"] = $e;
	include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");		
}

echo json_encode($respuesta);
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();