<?php 
include("../modelo/conexion.php");

$idPaginaInterna = 'GN0002';

if ($_SESSION["id"]=="") {
	session_destroy();
	header("Location:../index.php?error=4&urlDefault=".$_GET["urlDefault"]."&directory=".$_GET["directory"]);
	exit();
}

try {

	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_so, hil_pagina_anterior)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', '".$idPaginaInterna."', now(),'".php_uname()."','".$_SERVER['HTTP_REFERER']."')");

	mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_estado=0, uss_ultima_salida=now() WHERE uss_id='".$_SESSION["id"]."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]}");

	$urlRedirect = REDIRECT_ROUTE."?inst=".$_SESSION["idInstitucion"]."&year=".$_SESSION["bd"];

	setcookie("carga","",time()-3600);
	setcookie("periodo","",time()-3600);
	setcookie("cargaE","",time()-3600);
	setcookie("periodoE","",time()-3600);
	session_destroy();

	header("Location:".$urlRedirect);

} catch (Exception $e) {

	setcookie("carga","",time()-3600);
	setcookie("periodo","",time()-3600);
	setcookie("cargaE","",time()-3600);
	setcookie("periodoE","",time()-3600);
	session_destroy();

	header("Location:".REDIRECT_ROUTE."?error=".$e->getMessage());

}