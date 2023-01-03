<?php
	include("session.php");
	include("../modelo/conexion.php");
	
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_promocionado=0 WHERE mat_grado='" . $_GET["curso"] . "'");
	$consultaG=mysqli_query($conexion, "SELECT * FROM academico_grados WHERE gra_id='" . $_GET["curso"] . "'");
	$g = mysqli_fetch_array($consultaG, MYSQLI_BOTH);
	if ($g[7] != "") {
		mysqli_query($conexion, "UPDATE academico_matriculas SET mat_grado='" . $g[7] . "', mat_promocionado=1 WHERE mat_grado='" . $g[0] . "' AND mat_promocionado=0 AND mat_eliminado=0");
	}
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();