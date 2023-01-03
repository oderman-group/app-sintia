<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	if (trim($_POST["nombre"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
		<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO academico_categorias_notas (catn_nombre)VALUES('" . $_POST["nombre"] . "')");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '"</script>';
	exit();