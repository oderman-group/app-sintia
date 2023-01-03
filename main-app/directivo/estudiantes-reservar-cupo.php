<?php
include("session.php");
include("../modelo/conexion.php");

	mysqli_query($conexion, "INSERT INTO general_encuestas(genc_estudiante, genc_fecha, genc_respuesta, genc_comentario) VALUES('".$_GET["idEstudiante"]."', now(), 1, 'Reservado por un directivo.')");
	
	
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();