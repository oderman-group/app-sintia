<?php 
if (strpos($_SERVER['PHP_SELF'], 'salir.php') !== false) {
    session_start();
}

date_default_timezone_set("America/Bogota");//Zona horaria

require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");

if(isset($_SESSION["id"]) and $_SESSION["id"]!=""){
	$_SESSION["id"] = $_SESSION["id"];
}


//seleccionamos la base de datos
if (empty($_SESSION["inst"])) {
	// 🔍 DETECTAR SESIÓN ZOMBIE: Sesión completamente vacía después de session_destroy()
	$isZombieSession = (count($_SESSION) === 0);
	
	// LOG EXTENSO del problema
	error_log("🔴 CONEXION.PHP: SESSION[inst] VACÍA - Intentando recuperar");
	error_log("   └─ Página: " . ($_SERVER["PHP_SELF"] ?? 'UNKNOWN'));
	error_log("   └─ SESSION[id]: " . ($_SESSION["id"] ?? 'NULL'));
	error_log("   └─ SESSION[idInstitucion]: " . ($_SESSION["idInstitucion"] ?? 'NULL'));
	error_log("   └─ SESSION[datosUnicosInstitucion] existe: " . (isset($_SESSION["datosUnicosInstitucion"]) ? 'SÍ' : 'NO'));
	error_log("   └─ Sesión Zombie (completamente vacía): " . ($isZombieSession ? 'SÍ' : 'NO'));
	error_log("   └─ Total keys en SESSION: " . count($_SESSION));
	
	// Si es una sesión zombie, redirigir directamente sin intentar fallbacks
	if ($isZombieSession) {
		error_log("🧟 CONEXION.PHP: SESIÓN ZOMBIE DETECTADA (cookie mantenida después de session_destroy)");
		error_log("   └─ Session ID: " . session_id());
		error_log("   └─ Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'N/A'));
		error_log("   └─ IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A'));
		
		// NO llamar session_destroy() aquí porque ya está destruida
		// Solo borrar la cookie zombie
		if (isset($_COOKIE[session_name()])) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}
		
		require_once ROOT_PATH.'/main-app/class/Utilidades.php';
		$directory = Utilidades::getDirectoryUserFromUrl($_SERVER['PHP_SELF']);
		$page      = Utilidades::getPageFromUrl($_SERVER['PHP_SELF']);
		header("Location:".REDIRECT_ROUTE."?error=4&urlDefault=".$page."&directory=".$directory."&zombie=1");
		exit();
	}
	
	// FALLBACK: Intentar recuperar desde datosUnicosInstitucion
	if (!empty($_SESSION["datosUnicosInstitucion"]) && isset($_SESSION["datosUnicosInstitucion"]["ins_bd"])) {
		$_SESSION["inst"] = $_SESSION["datosUnicosInstitucion"]["ins_bd"];
		error_log("✅ CONEXION.PHP: SESSION[inst] recuperada desde datosUnicosInstitucion: " . $_SESSION["inst"]);
	}
	// Segundo fallback: Buscar en información de institución en sesión
	elseif (!empty($_SESSION["informacionInstConsulta"]) && isset($_SESSION["informacionInstConsulta"]["ins_bd"])) {
		$_SESSION["inst"] = $_SESSION["informacionInstConsulta"]["ins_bd"];
		error_log("✅ CONEXION.PHP: SESSION[inst] recuperada desde informacionInstConsulta: " . $_SESSION["inst"]);
	}
	
	// Si después de todos los fallbacks sigue vacío, ENTONCES sí cerrar sesión
	if (empty($_SESSION["inst"])) {
		error_log("🔴🔴🔴 CONEXION.PHP: SESSION[inst] NO SE PUDO RECUPERAR - CERRANDO SESIÓN 🔴🔴🔴");
		error_log("   └─ Session ID: " . session_id());
		error_log("   └─ Todas las keys en SESSION: " . (count($_SESSION) > 0 ? implode(', ', array_keys($_SESSION)) : 'VACÍO'));
		error_log("🔴🔴🔴 FIN LOG SESSION[inst] NO RECUPERABLE 🔴🔴🔴");
		
		session_destroy();
		require_once ROOT_PATH.'/main-app/class/Utilidades.php';
		$directory = Utilidades::getDirectoryUserFromUrl($_SERVER['PHP_SELF']);
		$page      = Utilidades::getPageFromUrl($_SERVER['PHP_SELF']);
		header("Location:".REDIRECT_ROUTE."?error=4&urlDefault=".$page."&directory=".$directory);
		exit();
	}
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

	//Conexion con el Servidor Mysql
	$conexion = Conexion::newConnection('MYSQL');
	
	//Conexion con el Servidor PDO
	$conexionPDO = Conexion::newConnection('PDO');

	// Crear una instancia de PDO
    $conexionPDO = new PDO("mysql:host=".SERVIDOR_CONEXION.";dbname=".BD_ADMIN, USUARIO_CONEXION, CLAVE_CONEXION);
	$conexionPDO->exec("SET NAMES 'utf8mb4'");

    // Establecer el modo de error PDO a excepciones
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	} catch(Exception $e) {

		$bdSolicitada = $_POST["bd"] ?? ($_SESSION["inst"] ?? '');
		if ($bdSolicitada === null) {
			$bdSolicitada = '';
		}
		$bdSolicitada = (string)$bdSolicitada;
		$bdBase64 = base64_encode($bdSolicitada);

		switch($e->getCode()){
			case 1044:
				$exception = "error=7&inst=".$bdBase64;
			break;

			default:
				$exception = "error=".$e->getMessage()."&inst=".$bdBase64;
			break;	
		}

		error_log("Problemas de conexión ".$e->getMessage());

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