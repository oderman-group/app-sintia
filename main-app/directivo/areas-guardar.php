<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(trim($_POST["nombreA"])=="" or trim($_POST["posicionA"])==""){
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}
	mysql_query("INSERT INTO academico_areas (ar_nombre,ar_posicion)VALUES('".$_POST["nombreA"]."',".$_POST["posicionA"].");",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();