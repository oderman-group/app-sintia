<?php
session_start();
//Si otro usuario de mayor rango entra como él
if (isset($_SESSION["idO"]) and $_SESSION["idO"]!="") {
	$idSession = $_SESSION["idO"];
} else {
	$idSession = $_SESSION["id"];
}
error_log("Hay sesion activa de directivo: ".$idSession);

if (empty($idSession)) {
	require_once '../class/Utilidades.php';
	$directory = Utilidades::getDirectoryUserFromUrl($_SERVER['PHP_SELF']);
	$page      = Utilidades::getPageFromUrl($_SERVER['PHP_SELF']);
	error_log("Sacando al usuario sin sesión desde la pagina session.php");
	header("Location:../controlador/salir.php?urlDefault=".$page."&directory=".$directory);
} else {
	require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
	require_once(ROOT_PATH."/config-general/config.php");
	require_once(ROOT_PATH."/config-general/idiomas.php");
	require_once(ROOT_PATH."/config-general/consulta-usuario-actual.php");
	require_once(ROOT_PATH."/config-general/verificar-usuario-bloqueado.php");

	if($datosUsuarioActual['uss_tipo'] != TIPO_DIRECTIVO && $datosUsuarioActual['uss_tipo'] != TIPO_DEV && !strpos($_SERVER['PHP_SELF'], 'page-info.php'))
	{
		if (isset($_SESSION["yearAnterior"])) {
			$_SESSION["cambioYear"] = $_SESSION["bd"];
			$_SESSION["bd"]         = $_SESSION["yearAnterior"];
		}
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=304";</script>';
		exit();		
	}
}

