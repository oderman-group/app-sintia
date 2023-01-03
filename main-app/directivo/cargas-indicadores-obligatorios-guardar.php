<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	$consultaInd=mysqli_query($conexion, "SELECT sum(ind_valor)+" . $_POST["valor"] . " FROM academico_indicadores where ind_obligatorio=1");
	$ind = mysqli_fetch_array($consultaInd, MYSQLI_BOTH);
	if ($ind[0] > 100) {
		echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_valor, ind_obligatorio)VALUES('" . $_POST["nombre"] . "','" . $_POST["valor"] . "',1)");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();