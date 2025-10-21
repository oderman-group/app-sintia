<?php
// Desactivar errores de PHP para evitar que aparezcan en la respuesta JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
$idPaginaInterna = 'GN0001';
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");
require_once(ROOT_PATH."/main-app/class/Instituciones.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/SubRoles.php';
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/Usuario.php';

// Función para devolver respuesta JSON
function sendJsonResponse($success, $message, $redirect = null, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect,
        'data' => $data
    ]);
    exit();
}

$auth = Autenticate::getInstance();

$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

if(!empty($_GET)) {
	$_POST["Usuario"]		=	base64_decode($_GET["Usuario"]);
	$_POST["Clave"] 		= 	base64_decode($_GET["Clave"]);

	$_POST["suma"] 			= 	base64_decode($_GET["suma"]);
	$_POST["sumaReal"] 		= 	base64_decode($_GET["sumaReal"]);
	
	$_POST["urlDefault"] 	= 	base64_decode($_GET["urlDefault"]);
	$_POST["directory"] 	= 	base64_decode($_GET["directory"]);
}

try {
	$usrE = $auth->getUserData($_POST["Usuario"], $_POST["Clave"]);
} catch (Exception $e) {

	if ( $e->getCode() == -2 ) {
		sendJsonResponse(false, "Usuario no encontrado en el sistema", null);
	}

	if ( $e->getCode() == -3 ) {
        $datosUsuario = Administrativo_Usuario_Usuario::consultarUltimoIngresoPorUsuario($_POST["Usuario"], "uss_id, institucion, year");

		mysqli_query($conexionBaseDatosServicios, "UPDATE ".BD_GENERAL.".usuarios SET uss_intentos_fallidos=uss_intentos_fallidos+1 WHERE uss_id='".$datosUsuario["uss_id"]."' AND institucion='".$datosUsuario["institucion"]."' AND year='".$datosUsuario["year"]."'");

		mysqli_query($conexionBaseDatosServicios, "INSERT INTO ".BD_ADMIN.".usuarios_intentos_fallidos(uif_usuarios, uif_ip, uif_clave, uif_institucion, uif_year)VALUES('".$datosUsuario["uss_id"]."', '".$_SERVER['REMOTE_ADDR']."', '".$_POST["Clave"]."', '".$datosUsuario["institucion"]."', '".$datosUsuario["year"]."')");

		sendJsonResponse(false, "Contraseña incorrecta. Intenta nuevamente.", null);
	}

	sendJsonResponse(false, "Error de autenticación: " . $e->getMessage(), null);
}

$_POST["bd"] = $usrE["institucion"];
$institucionConsulta = Instituciones::getDataInstitution($_POST["bd"]);

$numInsti = mysqli_num_rows($institucionConsulta);
if ($numInsti==0) {
	sendJsonResponse(false, "Institución no válida o no encontrada", null);
}

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);
$yearArray   = explode(",", $institucion['ins_years']);
$yearStart   = $yearArray[0];
$yearEnd     = $yearArray[1];

$_SESSION["inst"]			= $institucion['ins_bd'];
$_SESSION["idInstitucion"]	= $institucion['ins_id'];

if( !empty($institucion['ins_year_default']) && is_numeric($institucion['ins_year_default']) ) {
	$_SESSION["bd"] = $institucion['ins_year_default'];
} elseif( isset($yearEnd) && is_numeric($yearEnd) ) {
	$_SESSION["bd"] = $yearEnd;
} else {
	$_SESSION["bd"] = date("Y");
}

include("../modelo/conexion.php");
require_once("../class/Plataforma.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Modulos.php");

if($usrE['uss_intentos_fallidos']>=3 && (!array_key_exists("suma", $_POST) || md5($_POST["suma"]) != $_POST["sumaReal"])){
	sendJsonResponse(false, "Demasiados intentos fallidos. Completa la verificación matemática.", null);
}

$rst_usr = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_usuario='".trim($_POST["Usuario"])."' AND uss_clave=SHA1('".$_POST["Clave"]."') AND TRIM(uss_usuario)!='' AND uss_usuario IS NOT NULL AND TRIM(uss_clave)!='' AND uss_clave IS NOT NULL");

$num  = mysqli_num_rows($rst_usr);
$fila = mysqli_fetch_array($rst_usr, MYSQLI_BOTH);
if ($num>0)
{	
	if($fila['uss_bloqueado'] == 1){
		sendJsonResponse(false, "Tu cuenta ha sido bloqueada. Contacta al administrador.", null);
	}

	$URLdefault = null;
	if (!empty($_POST["urlDefault"])) { 
		$URLdefault = base64_decode($_POST["urlDefault"]); 
	}
	
	$url = null;
	if (!empty($_POST["directory"])) {
		$directoriosPorUsuario = [
			'directivo'  => TIPO_DIRECTIVO,
			'docente'    => TIPO_DOCENTE,
			'acudiente'  => TIPO_ACUDIENTE,
			'estudiante' => TIPO_ESTUDIANTE
		];
		$directory      = base64_decode($_POST["directory"]);
		$tipoDirectorio = $directoriosPorUsuario[$directory];

		if($tipoDirectorio == $fila['uss_tipo'] || ($tipoDirectorio == TIPO_DIRECTIVO && $fila['uss_tipo'] == TIPO_DEV)) {
			$url = $directory."/".$URLdefault;
		}

	}
	
	if (empty($url)) {
		switch($fila['uss_tipo']){
			case 1:
				$url = 'directivo/usuarios.php';
			break;
			
			case 2:
				$url = 'docente/cargas.php';
			break;
			
			case 3:
				$url = 'acudiente/estudiantes.php';
			break;
			
			case 4:
				$url = 'estudiante/matricula.php';
			break;
			
			case 5:
				$url = 'directivo/estudiantes.php';
			break;
			
			default:
				$url = 'salir.php';
			break;
		}
	}

	$config = RedisInstance::getSystemConfiguration(true);

	$informacionInstitucion = Instituciones::getGeneralInformationFromInstitution($config['conf_id_institucion'], $_SESSION["bd"]);
	$_SESSION["informacionInstConsulta"] = $informacionInstitucion;

	$datosUnicosInstitucionConsulta = Instituciones::getDataInstitution($config['conf_id_institucion']);
	$datosUnicosInstitucion         = mysqli_fetch_array($datosUnicosInstitucionConsulta, MYSQLI_BOTH);
	$_SESSION["datosUnicosInstitucion"]           = $datosUnicosInstitucion;
	$_SESSION["datosUnicosInstitucion"]["config"] = $config;

	$arregloModulos = RedisInstance::getModulesInstitution(true);
	$_SESSION["modulos"] = $arregloModulos;

	$infoRolesUsuario = Administrativo_Usuario_SubRoles::getInfoRolesFromUser($fila['uss_id'], $config['conf_id_institucion']);

	//INICIO SESION - Configurar todas las variables de sesión como en el original
	$_SESSION["id"]                                = $fila['uss_id'];
	$_SESSION["datosUsuario"]                      = $fila;
	$_SESSION["datosUsuario"]["sub_roles"]         = $infoRolesUsuario['datos_sub_roles_usuario'];
	$_SESSION["datosUsuario"]["sub_roles_paginas"] = $infoRolesUsuario['valores_paginas'];

	// Actualizar estado del usuario como en el original
	mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_estado=1, uss_ultimo_ingreso=now(), uss_intentos_fallidos=0 WHERE uss_id='".$fila['uss_id']."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]}");

	// Login exitoso - devolver respuesta JSON
	sendJsonResponse(true, "Login exitoso", $url, [
		'usuario' => $fila['uss_nombre'] . ' ' . $fila['uss_apellido1'],
		'tipo' => $fila['uss_tipo'],
		'institucion' => $institucion['ins_nombre']
	]);

} else {
	mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_intentos_fallidos=uss_intentos_fallidos+1 WHERE uss_id='".$usrE['uss_id']."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]}");

	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".usuarios_intentos_fallidos(uif_usuarios, uif_ip, uif_clave, uif_institucion, uif_year)VALUES('".$usrE['uss_id']."', '".$_SERVER['REMOTE_ADDR']."', '".$_POST["Clave"]."', '".$_POST["bd"]."', '".$_SESSION["bd"]."')");

	sendJsonResponse(false, "Credenciales incorrectas. Verifica tu usuario y contraseña.", null);
}

// Si llegamos aquí, algo salió mal
sendJsonResponse(false, "Error inesperado en el proceso de autenticación.", null);
?>