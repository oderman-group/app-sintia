<?php 
include("../modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");

$idPaginaInterna = 'GN0002';

$auth = Autenticate::getInstance();

if (empty($_SESSION["id"])) {
	$urlRedirect = "../index.php?error=4&urlDefault=".$_GET["urlDefault"]."&directory=".$_GET["directory"];
	error_log("La sesion está vacía, sacamos al usuario desde salir. REF: ".$_SERVER["HTTP_REFERER"]);
	$auth->cerrarSesion($urlRedirect);
	exit();
}

try {
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_so, hil_pagina_anterior)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', '".$idPaginaInterna."', now(),'".php_uname()."','".$_SERVER['HTTP_REFERER']."')");

	mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_estado=0, uss_ultima_salida=now() 
	WHERE uss_id='".$_SESSION["id"]."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]}");

	$urlRedirect = REDIRECT_ROUTE."?inst=".base64_encode($_SESSION["idInstitucion"])."&year=".base64_encode($_SESSION["bd"]);
	$auth->cerrarSesion($urlRedirect);
} catch (Exception $e) {
	$urlRedirect = REDIRECT_ROUTE."?error=".$e->getMessage();
	$auth->cerrarSesion($urlRedirect);
}