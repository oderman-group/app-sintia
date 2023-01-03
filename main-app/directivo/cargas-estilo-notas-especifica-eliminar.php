<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "DELETE FROM academico_notas_tipos WHERE notip_id=" . $_GET["idN"] . ";");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="academico-categoria-notas-especifica.php?id=' . $_GET["idNC"] . '";</script>';
	exit();