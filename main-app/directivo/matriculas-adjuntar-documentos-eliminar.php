<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0355';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Academico/Matricula_Adjuntos.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();

try {

	$camposWhere = [
		'ama_id'  => $_POST["ama_id"],
	]; 

	if(!Academico_Matriculas_Adjuntos::delete($camposWhere,BD_ACADEMICA)){
		$respuesta["estado"]   = 'ko';
		$respuesta["mensaje"]  =  "No se pudo eliminar el documento!";
	}else{
		$respuesta["estado"]   = 'ok';
		$respuesta["mensaje"]  = "Documento eliminado con Exito!";
	}
			
} catch (Exception $e) {
	$respuesta["estado"]  = 'ko';
	$respuesta["mensaje"] = $e;
	include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");		
}

echo json_encode($respuesta);
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();