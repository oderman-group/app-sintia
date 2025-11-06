<?php 
// 🔧 CONFIGURAR LOGS ANTES DE TODO (para capturar información incluso si hacemos exit() temprano)
// Esto es necesario porque constantes.php (que configura error_log) está en conexion.php
// y si bloqueamos antes, nunca llegaríamos a esa configuración

// Determinar el archivo de log según el entorno
$logFile = $_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/errores_prod.log";

// Verificar si estamos en local (para usar errores_local.log)
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    $logFile = $_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/errores_local.log";
}

// Configurar PHP para guardar logs en el archivo correcto
ini_set('log_errors', 1);
ini_set('error_log', $logFile);
date_default_timezone_set("America/Bogota");

// 🔍 LOG DETALLADO DE QUIEN LLAMA A SALIR.PHP (ANTES de session_start para evitar bloqueos)
error_log("🚪 SALIR.PHP LLAMADO - INICIO");
error_log("   └─ Timestamp: " . date('Y-m-d H:i:s'));
error_log("   └─ IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'));
error_log("   └─ User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN'));
error_log("   └─ Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'DIRECTO/SIN REFERER'));
error_log("   └─ Query String: " . ($_SERVER['QUERY_STRING'] ?? 'VACÍO'));
error_log("   └─ Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN'));
error_log("   └─ Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'UNKNOWN'));
error_log("   └─ HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'UNKNOWN'));
error_log("   └─ Archivo de log configurado: " . $logFile);

// 🛡️ PROTECCIÓN MEJORADA: Bloquear SOLO recursos automáticos sospechosos
// IMPORTANTE: Esta validación debe estar ANTES de include("../modelo/conexion.php")
// para prevenir que conexion.php detecte sesión zombie y haga redirect con urlDefault=salir.php

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// Identificar llamados LEGÍTIMOS del usuario o del sistema:
// 1. Tiene parámetro 'logout=true' (botón de cerrar sesión en encabezado)
// 2. Tiene parámetro 'urlDefault' (redirect desde session.php)
// 3. Tiene parámetro 'directory' (redirect desde session.php)
// 4. Tiene parámetro 'invalid_user=true' (validación de tipo de usuario inválido)
// 5. Tiene parámetro 'msg' (mensajes del sistema)
// 6. Tiene parámetro 'session_empty' (sesión vacía desde estudiante/acudiente/compartida)
// 7. Tiene parámetro 'return_admin' (retorno al panel admin)
$isLegitimateLogout = (
    isset($_GET['logout']) ||
    isset($_GET['urlDefault']) ||
    isset($_GET['directory']) ||
    isset($_GET['invalid_user']) ||
    isset($_GET['msg']) ||
    isset($_GET['session_empty']) ||
    isset($_GET['return_admin'])
);

// 🛡️ BLOQUEAR: CUALQUIER llamado sin parámetros legítimos
// CAMBIO CRÍTICO: Ya no verificamos si tiene referer o no
// TODOS los llamados sin parámetros válidos son sospechosos y se bloquean
// Esto previene el bug donde llamados "DIRECTO/SIN REFERER" cerraban sesión
if (!$isLegitimateLogout) {
    error_log("⚠️⚠️⚠️ SALIR.PHP: LLAMADO SOSPECHOSO BLOQUEADO ⚠️⚠️⚠️");
    error_log("   └─ Referer: " . ($referer ?: 'DIRECTO/SIN REFERER'));
    error_log("   └─ Query String: " . ($queryString ?: 'VACÍO (sin parámetros legítimos)'));
    error_log("   └─ Razón: Llamado sin parámetros válidos - BLOQUEADO automáticamente");
    error_log("   └─ IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'));
    error_log("   └─ User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN'));
    error_log("   └─ Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'UNKNOWN'));
    error_log("   └─ Posibles causas:");
    error_log("      • Recurso con ruta incorrecta (imagen, CSS, JS)");
    error_log("      • Link con href malformado o vacío (<a href=''>)");
    error_log("      • Navegador haciendo prefetch/preload automático");
    error_log("      • Extensión del navegador interceptando requests");
    error_log("      • JavaScript redirigiendo incorrectamente");
    error_log("   └─ Acción: BLOQUEADO - Devolviendo HTTP 204 No Content");
    error_log("   └─ Seguridad: Usuario PUEDE cerrar sesión con botón legítimo (?logout=true)");
    error_log("   └─ PREVIENE: Cierre inesperado + urlDefault=c2FsaXIucGhw en login");
    error_log("⚠️⚠️⚠️ FIN BLOQUEO ⚠️⚠️⚠️");
    
    // 🔥 CRÍTICO: NO redirigir (causa loop infinito)
    // En su lugar, devolver HTTP 204 No Content
    // Esto hace que el navegador reciba una respuesta válida pero sin contenido
    // Previene loops infinitos porque el recurso no se vuelve a cargar
    http_response_code(204); // 204 No Content
    exit();
}

// Si llegamos aquí, es un logout LEGÍTIMO → proceder normalmente
if ($isLegitimateLogout) {
    error_log("✅ SALIR.PHP: Logout legítimo detectado - Procediendo a cerrar sesión");
}

// Ahora sí, incluir conexión y demás
include("../modelo/conexion.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaLogger.php");

$idPaginaInterna = 'GN0002';

// Log adicional DESPUÉS de session_start (desde conexion.php)
error_log("   └─ Session ID (después de conexion): " . session_id());
error_log("   └─ SESSION[id]: " . ($_SESSION["id"] ?? 'NULL'));
error_log("   └─ SESSION[bd]: " . ($_SESSION["bd"] ?? 'NULL'));
error_log("   └─ SESSION[idInstitucion]: " . ($_SESSION["idInstitucion"] ?? 'NULL'));
if (!empty($_SESSION["datosUsuario"])) {
	error_log("   └─ Usuario: " . ($_SESSION["datosUsuario"]["uss_usuario"] ?? 'N/A') . " - Tipo: " . ($_SESSION["datosUsuario"]["uss_tipo"] ?? 'N/A'));
}

$auth = Autenticate::getInstance();

if (empty($_SESSION["id"])) {
	$urlDefault = $_GET["urlDefault"] ?? '';
	$directory = $_GET["directory"] ?? '';
	$urlRedirect = "../index.php?error=4&urlDefault=".$urlDefault."&directory=".$directory;
	error_log("⚠️ SALIR.PHP: La sesión está vacía, sacamos al usuario");
	error_log("   └─ URL Redirect: " . $urlRedirect);
	$auth->cerrarSesion($urlRedirect);
	exit();
}

try {
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_so, hil_pagina_anterior)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', '".$idPaginaInterna."', now(),'".php_uname()."','".$_SERVER['HTTP_REFERER']."')");

	mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_estado=0, uss_ultima_salida=now() 
	WHERE uss_id='".$_SESSION["id"]."' AND institucion={$_SESSION["idInstitucion"]} AND year={$_SESSION["bd"]}");

	// Registrar logout en auditoría
	$usuarioNombre = isset($_SESSION['datosUsuario']['uss_usuario']) ? $_SESSION['datosUsuario']['uss_usuario'] : $_SESSION["id"];
	AuditoriaLogger::registrarLogout($_SESSION["id"], $usuarioNombre);

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