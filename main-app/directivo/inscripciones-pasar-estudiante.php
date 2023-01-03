<?php
include("session.php");
include("../modelo/conexion.php");

	//SE CREA MATRICULA EN AÑO SIGUIENTE
	mysqli_query($conexion, "INSERT INTO ".$bdApasar.".academico_matriculas SELECT * FROM ".$bdActual.".academico_matriculas WHERE mat_id='".$_GET["matricula"]."' ");


	mysqli_query($conexion, "UPDATE ".$bdApasar.".academico_matriculas SET mat_estado_matricula=4, mat_grupo=1 WHERE mat_id='".$_GET["matricula"]."'");


	//CONSULTAMOS DATOS DEL ESTUDIANTE
	$consultaMatricula=mysqli_query($conexion, "SELECT * FROM ".$bdActual.".academico_matriculas WHERE mat_id='".$_GET["matricula"]."' ");
	$datosMatricula = mysqli_fetch_array($consultaMatricula, MYSQLI_BOTH);

	//SE CREA EL USUARIO DEL ESTUDIANTE
	mysqli_query($conexion, "INSERT INTO ".$bdApasar.".usuarios SELECT * FROM ".$bdActual.".usuarios WHERE uss_id='".$datosMatricula["mat_id_usuario"]."' ");


	//SE CREA EL USUARIO DEL ACUDIENTE
	mysqli_query($conexion, "INSERT INTO ".$bdApasar.".usuarios SELECT * FROM ".$bdActual.".usuarios WHERE uss_id='".$datosMatricula["mat_acudiente"]."' ");


	//SE CREA EL USUARIO DEL PADRE
	mysqli_query($conexion, "INSERT INTO ".$bdApasar.".usuarios SELECT * FROM ".$bdActual.".usuarios WHERE uss_id='".$datosMatricula["mat_padre"]."' ");


	//SE CREA EL USUARIO DE LA MADRE
	mysqli_query($conexion, "INSERT INTO ".$bdApasar.".usuarios SELECT * FROM ".$bdActual.".usuarios WHERE uss_id='".$datosMatricula["mat_madre"]."' ");


	//SE CREA RELACION ENTRE ACUDIENTE Y ESTUDIANTE
	mysqli_query($conexion, "INSERT INTO ".$bdApasar.".usuarios_por_estudiantes(upe_id_usuario, upe_id_estudiante)VALUES('".$datosMatricula["mat_acudiente"]."', '".$datosMatricula["mat_id_usuario"]."')");


	//SE ACTUALIZA EL ESTADO DEL ASPIRANTE
	mysqli_query($conexion, "UPDATE ".$baseDatosAdmisiones.".aspirantes SET asp_estado_solicitud=9 WHERE asp_id='".$datosMatricula["mat_solicitud_inscripcion"]."'");

	
	echo '<script type="text/javascript">window.location.href="inscripciones.php";</script>';
	exit();