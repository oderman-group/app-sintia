<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0353';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Academico/Matricula_Adjuntos.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();

try {
	$campos = [
		'ama_id_estudiante'   => $_POST["ama_id_estudiante"],
		'ama_documento'       => $_POST["ama_documento"],
		'ama_id_responsable'  => $_SESSION["id"],
		'ama_visible'         => $_POST["ama_visible"] ? 0 : 1,
		'institucion'         => $_POST["institucion"],
		'year'                => $_POST["year"],
		'ama_titulo'          => $_POST["ama_titulo"],
		'ama_descripcion'     => $_POST["ama_descripcion"]
	];

	if(!Academico_Matriculas_Adjuntos::Insert($campos,BD_ACADEMICA)){
		$respuesta["estado"]   = 'ko';
		$respuesta["mensaje"]  = "No se pudo guardar el documento!";
	}else{
		$respuesta["estado"]   = 'ok';
		$respuesta["mensaje"]  = "Documento guardado con Exito!";
	}

	

} catch (Exception $e) {
	$respuesta["estado"]  = 'ko';
	$respuesta["mensaje"] = $e;
	include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
	
}

echo json_encode($respuesta);
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();