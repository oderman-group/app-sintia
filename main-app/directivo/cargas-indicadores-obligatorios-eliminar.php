<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysql_query("DELETE FROM academico_indicadores WHERE ind_id=" . $_GET["idN"] . ";", $conexion);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();