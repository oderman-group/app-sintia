<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "DELETE FROM academico_materias WHERE mat_id=".$_GET["id"].";");
	
	echo '<script type="text/javascript">window.location.href="asignaturas.php?msgAsignatura=4";</script>';
	exit();