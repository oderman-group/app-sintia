<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["idH"]) == "" or trim($_POST["inicioH"]) == "" or trim($_POST["finH"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE academico_horarios SET hor_dia=" . $_POST["diaH"] . ", hor_desde='" . $_POST["inicioH"] . "', hor_hasta='" . $_POST["finH"] . "' WHERE hor_id=" . $_POST["idH"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="cargas-horarios.php?id=' . $_POST["idC"] . '";</script>';
	exit();