<?php
	include("session.php");
	include("../modelo/conexion.php");
	
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombreC"]) == "" or trim($_POST["formatoB"]) == "" or trim($_POST["valorM"]) == "" or trim($_POST["valorP"]) == "") {
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysqli_query($conexion, "UPDATE academico_grados SET 
	gra_codigo='" . $_POST["codigoC"] . "', 
	gra_nombre='" . $_POST["nombreC"] . "', 
	gra_formato_boletin='" . $_POST["formatoB"] . "', 
	gra_valor_matricula='" . $_POST["valorM"] . "', 
	gra_valor_pension='" . $_POST["valorP"] . "', 
	gra_grado_siguiente='" . $_POST["graSiguiente"] . "', 
	gra_grado_anterior='" . $_POST["graAnterior"] . "', 
	gra_nota_minima='" . $_POST["notaMin"] . "', 
	gra_periodos='" . $_POST["periodosC"] . "', 
	gra_nivel='" . $_POST["nivel"] . "', 
	gra_estado='" . $_POST["estado"] . "' 
	WHERE gra_id='" . $_POST["id_curso"] . "'");


	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();