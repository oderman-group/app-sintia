<?php
/*session_start();
$_SESSION["bd"] = date("Y");*/
include("../modelo/conexion.php");
$rst_usr = mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$_GET["usuario"]."'");
$num = mysqli_num_rows($rst_usr);
$fila = mysqli_fetch_array($rst_usr, MYSQLI_BOTH);
if($num>0)
{
	//session_destroy();
	session_start();
	$_SESSION["idO"] = $fila[0];
	switch($fila[3]){
		case 1:
		  $url = '../admin';
		break;
		
		case 2:
		  setcookie("carga","",-3600);
		  $_SESSION["carga"] = "";
		  $url = '../docente';
		break;
		
		case 3:
		  $url = '../acudiente';
		break;
		
		case 4:
		  $url = '../estudiante';
		break;
		
		case 5:
		  $url = '../directivo';
		break;
	}
	header("Location:".$url);	
	exit();
}else{
	header("Location:../index.php?error=2");
	exit();
}
?>
