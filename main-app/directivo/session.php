<?php
// Log ANTES de session_start
error_log("🔵 SESSION.PHP INICIO - Página: " . ($_SERVER["PHP_SELF"] ?? 'UNKNOWN') . " - IP: " . ($_SERVER["REMOTE_ADDR"] ?? 'UNKNOWN') . " - Time: " . microtime(true));

session_start();

date_default_timezone_set('America/Bogota');

require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");

// Log detallado del estado de la sesión
error_log("✅ SESSION.PHP: session_start() exitoso - Session ID: " . session_id());
error_log("   └─ SESSION[id]: " . ($_SESSION["id"] ?? 'NULL'));
error_log("   └─ SESSION[bd]: " . ($_SESSION["bd"] ?? 'NULL'));
error_log("   └─ SESSION[idInstitucion]: " . ($_SESSION["idInstitucion"] ?? 'NULL'));
error_log("   └─ SESSION[datosUsuario] existe: " . (isset($_SESSION["datosUsuario"]) ? 'SÍ' : 'NO'));
error_log("   └─ SESSION[yearAnterior]: " . ($_SESSION["yearAnterior"] ?? 'NO EXISTE'));

//Si otro usuario de mayor rango entra como él
if (isset($_SESSION["idO"]) and $_SESSION["idO"]!="") {
	$idSession = $_SESSION["idO"];
	error_log("ℹ️ SESSION.PHP: Usando idSession de idO (auto-login) - idO: " . $_SESSION["idO"]);
} else {
	$idSession = $_SESSION["id"];
	error_log("ℹ️ SESSION.PHP: Usando idSession de SESSION[id] - id: " . ($_SESSION["id"] ?? 'NULL'));
}

if (empty($idSession)) {
	// LOG EXTENSO cuando se va a cerrar sesión
	error_log("🔴🔴🔴 SESSION.PHP: idSession VACÍO - CERRANDO SESIÓN 🔴🔴🔴");
	error_log("   └─ Página: " . ($_SERVER["PHP_SELF"] ?? 'UNKNOWN'));
	error_log("   └─ Query String: " . ($_SERVER["QUERY_STRING"] ?? 'NONE'));
	error_log("   └─ Referer: " . ($_SERVER["HTTP_REFERER"] ?? 'NONE'));
	error_log("   └─ IP: " . ($_SERVER["REMOTE_ADDR"] ?? 'UNKNOWN'));
	error_log("   └─ User-Agent: " . ($_SERVER["HTTP_USER_AGENT"] ?? 'UNKNOWN'));
	error_log("   └─ Session ID: " . session_id());
	error_log("   └─ SESSION[id]: " . ($_SESSION["id"] ?? 'NULL'));
	error_log("   └─ SESSION[idO]: " . ($_SESSION["idO"] ?? 'NULL'));
	error_log("   └─ SESSION[bd]: " . ($_SESSION["bd"] ?? 'NULL'));
	error_log("   └─ SESSION[idInstitucion]: " . ($_SESSION["idInstitucion"] ?? 'NULL'));
	error_log("   └─ Todas las keys en SESSION: " . (count($_SESSION) > 0 ? implode(', ', array_keys($_SESSION)) : 'VACÍO'));
	error_log("🔴🔴🔴 FIN LOG SESSION VACÍA 🔴🔴🔴");
	
	require_once '../class/Utilidades.php';
	$directory = Utilidades::getDirectoryUserFromUrl($_SERVER['PHP_SELF']);
	$page      = Utilidades::getPageFromUrl($_SERVER['PHP_SELF']);
	header("Location:../controlador/salir.php?urlDefault=".$page."&directory=".$directory."&msg=session_empty");
	exit();
} else {
	error_log("✅ SESSION.PHP: idSession válido - " . $idSession);
	
	require_once(ROOT_PATH."/config-general/config.php");
	require_once(ROOT_PATH."/config-general/idiomas.php");
	require_once(ROOT_PATH."/config-general/consulta-usuario-actual.php");
	require_once(ROOT_PATH."/config-general/verificar-usuario-bloqueado.php");

	// Validar que el usuario sea DIRECTIVO o DEV
	$tipoUsuario = $datosUsuarioActual['uss_tipo'] ?? 'NULL';
	error_log("ℹ️ SESSION.PHP: Validando tipo de usuario - Tipo: " . $tipoUsuario . " - Esperado: " . TIPO_DIRECTIVO . " o " . TIPO_DEV);
	
	if($tipoUsuario != TIPO_DIRECTIVO && $tipoUsuario != TIPO_DEV && !strpos($_SERVER['PHP_SELF'], 'page-info.php'))
	{
		error_log("⚠️ SESSION.PHP: Tipo de usuario NO válido para directivo - Tipo: " . $tipoUsuario . " - Redirigiendo a error");
		error_log("   └─ datosUsuarioActual completo: " . print_r($datosUsuarioActual, true));
		
		if (isset($_SESSION["yearAnterior"])) {
			$_SESSION["cambioYear"] = $_SESSION["bd"];
			$_SESSION["bd"]         = $_SESSION["yearAnterior"];
			error_log("   └─ Revirtiendo cambio de año - Restaurando año: " . $_SESSION["yearAnterior"]);
		}
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=304";</script>';
		exit();		
	}
	
	error_log("✅ SESSION.PHP: Tipo de usuario válido - Acceso permitido");
	error_log("🔵 SESSION.PHP FIN - Time: " . microtime(true));
}

