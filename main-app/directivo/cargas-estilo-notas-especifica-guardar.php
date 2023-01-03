<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombreCN"]) == "" or trim($_POST["ndesdeCN"]) == "" or trim($_POST["nhastaCN"]) == "" or trim($_POST["idCN"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.<br>
			<a href='javascript:history.go(-1)'>[Volver al formulario]</a></samp>";
		exit();
	}
	mysqli_query($conexion, "INSERT INTO academico_notas_tipos (notip_nombre, notip_desde, notip_hasta,notip_categoria)VALUES('" . $_POST["nombreCN"] . "'," . $_POST["ndesdeCN"] . "," . $_POST["nhastaCN"] . "," . $_POST["idCN"] . ");");
	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
	echo '<script type="text/javascript">window.location.href="academico-categoria-notas-especifica.php?id=' . $_POST["idCN"] . '";</script>';
	exit();