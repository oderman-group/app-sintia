<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/config-admisiones.php");
$server 		   = $servidorConexion;
$user   		   = $usuarioConexion;
$pass   		   = $claveConexion;
$dbName 		   = $baseDatosAdmisiones;
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion);
if ($conexion) {
	mysqli_set_charset($conexion, "utf8mb4");
}
if(!empty($_REQUEST['idInst'])){
	$idInsti=base64_decode($_REQUEST['idInst']);
	try{
		$pdoAdmin = new PDO('mysql:host='.$server.';dbname='.$baseDatosServicios.';charset=utf8mb4', $user, $pass);
		$pdoAdmin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdoAdmin->exec("SET NAMES 'utf8mb4'");
	}catch (PDOException $e) {
		echo "Error!: " . $e->getMessage() . "<br/>";
		header("Location:".REDIRECT_ROUTE."/admisiones".$e);
		die();
	}

	
	//configuración
	$configConsulta = "SELECT * FROM configuracion
	INNER JOIN {$baseDatosAdmisiones}.config_instituciones ON cfgi_id_institucion=conf_id_institucion 
	AND cfgi_inscripciones_activas=1 
	AND cfgi_year_inscripcion = conf_agno
	WHERE conf_id_institucion = ".$idInsti." AND conf_agno = cfgi_year_inscripcion";
	$configuracion = $pdoAdmin->prepare($configConsulta);
	$configuracion->execute();
	$config = $configuracion->fetch();

	if(empty($config['conf_id_institucion']) || empty($config['conf_agno'])) {
		header("Location:".REDIRECT_ROUTE."/admisiones");
		exit();
	}
	

	//información
	// Usar el año de inscripción para obtener la información correcta
	$yearInfo = !empty($config['cfgi_year_inscripcion']) ? $config['cfgi_year_inscripcion'] : date("Y");
	$infogConsulta = "SELECT * FROM general_informacion
	WHERE info_institucion = ".$idInsti." AND info_year = ".$yearInfo;
	$info = $pdoAdmin->prepare($infogConsulta);
	$info->execute();
	$datosInfo = $info->fetch();
	
	// Si no hay información para el año de inscripción, intentar con el año actual
	if(!$datosInfo){
		$infogConsulta = "SELECT * FROM general_informacion
		WHERE info_institucion = ".$idInsti." ORDER BY info_year DESC LIMIT 1";
		$info = $pdoAdmin->prepare($infogConsulta);
		$info->execute();
		$datosInfo = $info->fetch();
	}


	$BD_ADMISIONES_MOCK = $baseDatosServicios;

} else {
	header("Location:".REDIRECT_ROUTE."/admisiones");
	exit();
}

try{
	$pdo = new PDO('mysql:host='.$server.';dbname='.$dbName.';charset=utf8mb4', $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->exec("SET NAMES 'utf8mb4'");
}catch (PDOException $e) {
	echo "Error!: " . $e->getMessage() . "<br/>";
	header("Location:".REDIRECT_ROUTE."/admisiones".$e);
	die();
}

$dbNameInstitucion = !empty($BD_ADMISIONES_MOCK) ? $BD_ADMISIONES_MOCK : $baseDatosServicios;

try{
	$pdoI = new PDO('mysql:host='.$server.';dbname='.$dbNameInstitucion.';charset=utf8mb4', $user, $pass);
	$pdoI->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdoI->exec("SET NAMES 'utf8mb4'");
}catch (PDOException $e) {
	header("Location:".REDIRECT_ROUTE."/admisiones".$e);
	echo "Error!: " . $e->getMessage() . "<br/>";
	exit();
}