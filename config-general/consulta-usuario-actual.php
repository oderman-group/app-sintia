<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

//SE RECARGA VARIABLE SESSION PARA EL USUARIO ACTUAL
if (isset($_SESSION["yearAnterior"])) {
	$datosUsuarioAnterior = UsuariosPadre::sesionUsuario($_SESSION["id"]);

	if (
		!empty($datosUsuarioAnterior) && 
		($datosUsuarioAnterior['uss_tipo'] == TIPO_DIRECTIVO || 
		$datosUsuarioAnterior['uss_tipo'] == TIPO_DEV)
	) {
		$_SESSION["datosUsuario"] = $datosUsuarioAnterior;
	}
}

if (!isset($idSession) || $idSession=="") {
	$idSession = $_SESSION["id"];
}

// Asignar datos del usuario desde la sesión
$datosUsuarioActual = $_SESSION["datosUsuario"] ?? null;

// FALLBACK DE SEGURIDAD: Si datosUsuario está vacío, recargarlo desde BD
if (empty($datosUsuarioActual) && !empty($idSession)) {
	error_log("⚠️ FALLBACK ACTIVADO - Recargando datos de usuario desde BD - ID: " . $idSession . " - Página: " . ($_SERVER['PHP_SELF'] ?? 'UNKNOWN') . " - Year: " . $_SESSION["bd"]);
	
	$datosUsuarioActual = UsuariosPadre::sesionUsuario($idSession);
	
	if (!empty($datosUsuarioActual)) {
		// Recargar también los sub-roles si existen
		require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/SubRoles.php");
		$infoRolesUsuario = Administrativo_Usuario_SubRoles::getInfoRolesFromUser($datosUsuarioActual['uss_id'], $_SESSION['idInstitucion']);
		
		$datosUsuarioActual["sub_roles"] = $infoRolesUsuario['datos_sub_roles_usuario'] ?? [];
		$datosUsuarioActual["sub_roles_paginas"] = $infoRolesUsuario['valores_paginas'] ?? [];
		
		// Actualizar la sesión con los datos recargados
		$_SESSION["datosUsuario"] = $datosUsuarioActual;
		
		error_log("✅ FALLBACK EXITOSO - Datos recargados para usuario: " . ($datosUsuarioActual['uss_usuario'] ?? 'UNKNOWN') . " - Tipo: " . ($datosUsuarioActual['uss_tipo'] ?? 'NULL'));
	} else {
		error_log("❌ FALLBACK FALLÓ - No se encontró usuario en BD para ID: " . $idSession . " en year: " . $_SESSION["bd"]);
	}
}

// Log para diagnóstico de sesiones (si aún está vacío después del fallback)
if (empty($datosUsuarioActual)) {
	error_log("🔴 DATOS USUARIO VACÍO (después de fallback) - ID Session: " . ($idSession ?? 'NULL') . " - yearAnterior: " . (isset($_SESSION["yearAnterior"]) ? 'SI' : 'NO') . " - Página: " . ($_SERVER['PHP_SELF'] ?? 'UNKNOWN'));
}