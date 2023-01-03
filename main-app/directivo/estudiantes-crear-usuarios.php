<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	$estud = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_eliminado=0");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	while ($est = mysqli_fetch_array($estud, MYSQLI_BOTH)) {
		$consultaUsComp=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='" . $est["mat_id_usuario"] . "'");
		$usComp = mysqli_num_rows($consultaUsComp);
		if ($usComp == 0) {
			mysqli_query($conexion, "DELETE FROM usuarios WHERE uss_usuario='" . $est[12] . "'");
			

			mysqli_query($conexion, "INSERT INTO usuarios(uss_usuario, uss_clave, uss_tipo, uss_nombre, uss_estado, uss_foto, uss_portada, uss_idioma, uss_tema, uss_perfil, uss_ocupacion, uss_email, uss_fecha_nacimiento, uss_genero, uss_bloqueado, uss_fecha_registro, uss_responsable_registro)VALUES('" . $est[12] . "','1234',4,'" . $est[5] . " " . $est[3] . " " . $est[4] . "',0,'default.png','default.png',1,'blue',0,'Estudiante','notiene@gmail.com','" . $est[9] . "','" . $est[8] . "',0,now(),'" . $_SESSION["id"] . "')");
			$idUsuario = mysqli_insert_id($conexion);
			

			mysqli_query($conexion, "UPDATE academico_matriculas SET mat_id_usuario='" . $idUsuario . "' WHERE mat_id='" . $est[0] . "'");
			
		}
	}
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();