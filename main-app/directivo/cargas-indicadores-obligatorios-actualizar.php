<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	$consultaInd=mysqli_query($conexion, "SELECT sum(ind_valor)+" . $_POST["valor"] . " FROM academico_indicadores where ind_obligatorio=1 AND ind_id!='" . $_POST["idI"] . "'");
	$ind = mysqli_fetch_array($consultaInd, MYSQLI_BOTH);
	if ($ind[0] > 100) {
		echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE academico_indicadores SET ind_nombre='" . $_POST["nombre"] . "', ind_valor='" . $_POST["valor"] . "' WHERE ind_id='" . $_POST["idI"] . "'");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();