<?php 
if (strpos($_SERVER['PHP_SELF'], 'salir.php') !== false) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");

if(isset($_SESSION["id"]) and $_SESSION["id"]!=""){
	$_SESSION["id"] = $_SESSION["id"];
}

date_default_timezone_set("America/Bogota");//Zona horaria

//seleccionamos la base de datos
if (empty($_SESSION["inst"])) {
	session_destroy();
	require_once ROOT_PATH.'/main-app/class/Utilidades.php';
	$directory = Utilidades::getDirectoryUserFromUrl($_SERVER['PHP_SELF']);
	$page      = Utilidades::getPageFromUrl($_SERVER['PHP_SELF']);
	header("Location:".REDIRECT_ROUTE."?error=4&urlDefault=".$page."&directory=".$directory);
	exit();
} else {
	
	//seleccionamos el año de la base de datos
	$agnoBD = date("Y");
	if($_SESSION["bd"]!=""){
		$agnoBD = $_SESSION["bd"];
	}

	$bdActual = $baseDatosServicios;
	$bdApasar = $baseDatosServicios;
	require_once ROOT_PATH."/main-app/class/Conexion.php";
	try{

	//Conexion con el Servidor
	$conexionInstancia = new Conexion;

	$conexion = $conexionInstancia->conexion($servidorConexion, $usuarioConexion, $claveConexion, $bdActual);
	
	//Conexion con el Servidor PDO
	$conexionPDO = $conexionInstancia->conexionPDO($servidorConexion, $usuarioConexion, $claveConexion, $bdActual);

	// Crear una instancia de PDO
    $conexionPDO = new PDO("mysql:host=$servidorConexion;dbname=$bdActual", $usuarioConexion, $claveConexion);
	$conexionPDO->exec("SET NAMES 'utf8mb4'");

    // Establecer el modo de error PDO a excepciones
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch(Exception $e){

		switch($e->getCode()){
			case 1044:
				$exception = "error=7&inst=".base64_encode($_POST["bd"]);
			break;

			default:
				$exception = "error=".$e->getMessage()."&inst=".base64_encode($_POST["bd"]);
			break;	
		}

		session_destroy();
		header("Location:".REDIRECT_ROUTE."/index.php?".$exception);
		exit();
	}
	if (!mysqli_set_charset($conexion, "utf8mb4")) 
    {
      printf("Error cargando el conjunto de caracteres utf8mb4: %s\n", mysqli_error($link));
      exit();
    }

}