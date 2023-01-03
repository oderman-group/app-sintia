<?php 
include("session.php");
include("../modelo/conexion.php");

	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_compromiso=0 WHERE mat_id='" . $_GET["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="estudiantes.php";</script>';
	exit();