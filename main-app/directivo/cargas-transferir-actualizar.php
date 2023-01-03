<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "UPDATE academico_cargas SET car_docente='" . $_POST["para"] . "' WHERE car_docente='" . $_POST["de"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();