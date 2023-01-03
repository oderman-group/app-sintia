<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_resultados");
	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_comentarios");
	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_respuestas");
	mysqli_query($conexion, "DELETE FROM academico_actividad_tareas_entregas");
	mysqli_query($conexion, "DELETE FROM academico_ausencias");
	mysqli_query($conexion, "DELETE FROM academico_boletin");
	mysqli_query($conexion, "DELETE FROM academico_calificaciones");
	mysqli_query($conexion, "DELETE FROM academico_matriculas"); //ELIMINA TODO
	mysqli_query($conexion, "DELETE FROM academico_nivelaciones");
	mysqli_query($conexion, "DELETE FROM academico_recuperaciones_notas");
	mysqli_query($conexion, "DELETE FROM disciplina_matricula_condicional");
	mysqli_query($conexion, "DELETE FROM disciplina_reportes");
	mysqli_query($conexion, "DELETE FROM disiplina_nota");
	mysqli_query($conexion, "DELETE FROM general_resultados");
	mysqli_query($conexion, "DELETE FROM usuarios WHERE uss_tipo=4");
	mysqli_query($conexion, "DELETE FROM usuarios_por_estudiantes");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();