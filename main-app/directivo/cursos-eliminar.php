<?php
	include("session.php");
	include("../modelo/conexion.php");
	
	mysql_query("UPDATE academico_grados SET gra_estado=0 WHERE gra_id='" . $_GET["id"] . "'", $conexion);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();