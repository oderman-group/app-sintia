<?php 
include("../modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");

$idPaginaInterna = 'GN0002';

// 🔍 LOG DETALLADO DE QUIEN LLAMA A SALIR.PHP
error_log("🚪 SALIR.PHP LLAMADO - INICIO");
error_log("   └─ Timestamp: " . date('Y-m-d H:i:s'));
error_log("   └─ IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'));
error_log("   └─ User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN'));
error_log("   └─ Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'DIRECTO/SIN REFERER'));
error_log("   └─ Query String: " . ($_SERVER['QUERY_STRING'] ?? 'VACÍO'));
error_log("   └─ Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
error_log("   └─ Session ID: " . session_id());
error_log("   └─ SESSION[id]: " . ($_SESSION["id"] ?? 'NULL'));
error_log("   └─ SESSION[bd]: " . ($_SESSION["bd"] ?? 'NULL'));
error_log("   └─ SESSION[idInstitucion]: " . ($_SESSION["idInstitucion"] ?? 'NULL'));
if (!empty($_SESSION["datosUsuario"])) {
	error_log("   └─ Usuario: " . ($_SESSION["datosUsuario"]["uss_usuario"] ?? 'N/A') . " - Tipo: " . ($_SESSION["datosUsuario"]["uss_tipo"] ?? 'N/A'));
}

$auth = Autenticate::getInstance();

if (empty($_SESSION["id"])) {
	$urlRedirect = "../index.php?error=4&urlDefault=".$_GET["urlDefault"]."&directory=".$_GET["directory"];
	error_log("⚠️ SALIR.PHP: La sesión está vacía, sacamos al usuario");
	error_log("   └─ URL Redirect: " . $urlRedirect);
	$auth->cerrarSesion($urlRedirect);
	exit();
}

try {
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_so, hil_pagina_anterior)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', '".$idPaginaInterna."', now(),'".php_uname()."','".$_SERVER['HTTP_REFERER']."')");

	mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_estado=0, uss_ultima_salida=now() 
	WHERE uss_id='".$_SESSION["id"]."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]}");

	error_log("✅ SALIR.PHP: Historial guardado - Usuario cerró sesión correctamente");
	error_log("   └─ Usuario ID: " . $_SESSION["id"]);
	error_log("   └─ Institución: " . $_SESSION["idInstitucion"]);
	error_log("   └─ Año: " . $_SESSION["bd"]);
	
	$urlRedirect = REDIRECT_ROUTE."?inst=".base64_encode($_SESSION["idInstitucion"])."&year=".base64_encode($_SESSION["bd"]);
	error_log("   └─ Redirigiendo a: " . $urlRedirect);
	$auth->cerrarSesion($urlRedirect);
} catch (Exception $e) {
	error_log("❌ SALIR.PHP: Error al cerrar sesión - " . $e->getMessage());
	$urlRedirect = REDIRECT_ROUTE."?error=".$e->getMessage();
	$auth->cerrarSesion($urlRedirect);
}