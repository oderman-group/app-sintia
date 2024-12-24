<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/General_Solicitud.php");

$predicado = [
	'soli_estado'  		=> Administrativo_General_Solicitud::SOLICITUD_PENDIENTE,
	'soli_institucion' 	=> $_SESSION["idInstitucion"],
	'soli_year'        	=> $_SESSION["bd"]
];

$numeroSolicitudes = Administrativo_General_Solicitud::numRows($predicado);

$arrayEstado=[
	"numeroSolicitudes"     =>      $numeroSolicitudes
];

header('Content-Type: application/json');
echo json_encode($arrayEstado);
exit;