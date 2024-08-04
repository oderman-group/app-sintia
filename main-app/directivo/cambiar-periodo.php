<?php 
include("session.php");

$idPaginaInterna = 'DT0053';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include("../compartido/historial-acciones-guardar.php");

try {
	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".configuracion SET 
	conf_periodo='" . base64_decode($_GET["periodo"]) . "'
	WHERE conf_id='".$config['conf_id']."'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

header("Location:".$_SERVER["HTTP_REFERER"]);
exit();
