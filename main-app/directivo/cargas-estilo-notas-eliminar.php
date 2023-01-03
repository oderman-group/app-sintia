<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "DELETE FROM academico_notas_tipos WHERE notip_categoria='" . $_GET["idR"] . "'");
	mysqli_query($conexion, "DELETE FROM academico_categorias_notas WHERE catn_id='" . $_GET["idR"] . "'");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();