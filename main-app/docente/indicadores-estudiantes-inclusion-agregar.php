<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0152';

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Academico/Indicador_Estudiantes_Inclusion.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();

try {
	$campos = [
		'aii_id_estudiante'   		=> $_POST["aii_id_estudiante"],
		'aii_id_indicador'       	=> $_POST["aii_id_indicador"],
		'aii_descripcion_indicador'	=> $_POST["aii_descripcion_indicador"] ,
		'institucion'         		=> $_SESSION["idInstitucion"],
		'year'              		=> $_SESSION["bd"]
	];

	if(!Academico_Indicadores_Estudiantes_Inclusion::Insert($campos,BD_ACADEMICA)){	

		$respuesta["estado"]   = 'ko';
		$respuesta["mensaje"]  = "No se pudo guardar el indicador!";
	}else{

		$respuesta["estado"]   = 'ok';
		$respuesta["mensaje"]  = "Indicador guardado con Exito!";
	}

	

} catch (Exception $e) {
	$respuesta["estado"]  = 'ko';
	$respuesta["mensaje"] = $e;
	include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
	
}

echo json_encode($respuesta);
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();