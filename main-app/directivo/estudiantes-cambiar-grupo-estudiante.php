<?php
include("session.php");
include("../modelo/conexion.php");

	$consultaEstudiante=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='" . $_POST["estudiante"] . "'");
	$estudiante = mysqli_fetch_array($consultaEstudiante, MYSQLI_BOTH);
	$cargasConsulta = mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='" . $estudiante["mat_grado"] . "' AND car_grupo='" . $estudiante["mat_grupo"] . "'");
	while ($cargasDatos = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)) {
		$consultaCargas=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso='" . $_POST["cursoNuevo"] . "' AND car_grupo='" . $_POST["grupoNuevo"] . "' AND car_materia='" . $cargasDatos["car_materia"] . "'");
		$cargasConsultaNuevo = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH);

		mysqli_query($conexion, "UPDATE academico_boletin SET bol_carga='" . $cargasConsultaNuevo["car_id"] . "' 
		WHERE bol_carga='" . $cargasDatos["car_id"] . "' AND bol_estudiante='" . $_POST["estudiante"] . "'");
	}
	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_grado='" . $_POST["cursoNuevo"] . "', mat_grupo='" . $_POST["grupoNuevo"] . "' WHERE mat_id='" . $_POST["estudiante"] . "'");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '"</script>';
	exit();