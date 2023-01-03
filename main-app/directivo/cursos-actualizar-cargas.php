<?php
	include("session.php");
	include("../modelo/conexion.php");
	
	$cargas = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_ih!=''");
	while ($c = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
		mysqli_query($conexion, "DELETE FROM academico_intensidad_curso WHERE ipc_curso='" . $c[2] . "' AND ipc_materia='" . $c[4] . "'");
		
		mysqli_query($conexion, "INSERT INTO academico_intensidad_curso(ipc_curso, ipc_materia, ipc_intensidad)VALUES('" . $c[2] . "','" . $c[4] . "','" . $c['car_ih'] . "')");
		
	}
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();