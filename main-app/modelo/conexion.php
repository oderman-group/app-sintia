<?php 
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);

if (strpos($_SERVER['PHP_SELF'], 'salir.php')) {
    session_start();
}

switch($_SERVER['HTTP_HOST']){
	case 'localhost';
	$REDIRECT_ROUTE = 'http://localhost/app-sintia/main-app';
	break;

	case 'developer.plataformasintia.com';
	$REDIRECT_ROUTE = 'https://developer.plataformasintia.com/app-sintia/main-app';
	break;

	case 'main.plataformasintia.com';
	$REDIRECT_ROUTE = 'https://main.plataformasintia.com/app-sintia/main-app';
	break;
}

if(isset($_SESSION["id"]) and $_SESSION["id"]!=""){
	$_SESSION["id"] = $_SESSION["id"];
}

include("../../conexion-datos.php");

//seleccionamos la base de datos
if($_SESSION["inst"]==""){
	session_destroy();
	header("Location:".$REDIRECT_ROUTE."?error=no_hay_sesion_institucion");
	exit();
}else{
	
	//seleccionamos el año de la base de datos
	$agno = date("Y");
	if($_SESSION["bd"]!=""){
		$agno = $_SESSION["bd"];
	}

	$bdActual = $_SESSION["inst"]."_".$agno;
	$bdApasar = $_SESSION["inst"]."_".($agno+1);
	//Conexion con el Servidor
	$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $_SESSION["inst"]."_".$agno);

}