<?php 
include("session.php");

$idPaginaInterna = 'DT0030';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include("../compartido/historial-acciones-guardar.php");

$_SESSION["yearAnterior"]=$_SESSION["bd"];
$_SESSION["bd"] = base64_decode($_GET["agno"]);

include("../compartido/guardar-historial-acciones.php");

header("Location:".$_SERVER["HTTP_REFERER"]);
exit();
