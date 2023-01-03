<?php
include("session.php");
include("../modelo/conexion.php");

	$consultaEst=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id='" . $_GET["id"] . "'");
	$est = mysqli_fetch_array($consultaEst, MYSQLI_BOTH);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "DELETE FROM usuarios WHERE uss_usuario='" . $est[12] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_genero, uss_bloqueado, uss_fecha_registro, uss_responsable_registro)VALUES('" . $est[12] . "','1234',4,'" . $est[5] . " " . $est[3] . " " . $est[4] . "',0,'default.png','default.png',1,'blue',0,'Estudiante','notiene@gmail.com','" . $est[9] . "','" . $est[8] . "',0,now(),'" . $_SESSION["id"] . "')");
	$idUsuario = mysqli_insert_id($conexion);
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_id_usuario='" . $idUsuario . "' WHERE mat_id='" . $_GET["id"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="usuario-modificar.php?id=' . $idUsuario . '";</script>';
	exit();