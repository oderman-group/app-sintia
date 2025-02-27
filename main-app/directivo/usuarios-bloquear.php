<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0091';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$input = json_decode(file_get_contents("php://input"), true);

include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
if (!empty($input)) {
	$_POST = $input;
	$response = array();
	require_once '../class/App/Administrativo/usuario/Usuario.php';
	try {
		Administrativo_Usuario_Usuario::BloquearUsuarios($_POST["usuarios"], $_POST["bloquear"]);
		$response["ok"]   = true;
		$response["msg"]  = "Usuarios actualizados con Exito!";

	} catch (Exception $e) {
		$response["ok"]  = false;
		$response["msg"] = $e;
		include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
		
	}
	echo json_encode($response);
	exit();
} else {
	require_once '../class/Usuarios.php';

	Usuarios::bloquearDesbloquearUsuarios($conexion, base64_decode($_GET["tipo"]), 1);

	include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="usuarios.php?tipo=' . $_GET["tipo"] . '";</script>';
	exit();
}