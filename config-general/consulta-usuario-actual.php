<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

//SE RECARGA VARIABLE SESSION PARA EL USUARIO ACTUAL
if (isset($_SESSION["yearAnterior"])) {
	error_log("🔄 CONSULTA-USUARIO: Detectado cambio de año - Usuario: " . ($_SESSION["id"] ?? 'NULL') . " - Año anterior: " . $_SESSION["yearAnterior"] . " - Año nuevo: " . ($_SESSION["bd"] ?? 'NULL'));
	
	$datosUsuarioAnterior = UsuariosPadre::sesionUsuario($_SESSION["id"]);

	// BUG CRÍTICO CORREGIDO: Cambiar && por || 
	// El bug original hacía que esta condición SIEMPRE fuera falsa
	// (nadie puede ser DIRECTIVO Y DEV al mismo tiempo)
	if (
		!empty($datosUsuarioAnterior) && 
		($datosUsuarioAnterior['uss_tipo'] == TIPO_DIRECTIVO || 
		$datosUsuarioAnterior['uss_tipo'] == TIPO_DEV)
	) {
		$_SESSION["datosUsuario"] = $datosUsuarioAnterior;
		error_log("✅ CONSULTA-USUARIO: datosUsuario actualizado exitosamente - Tipo: " . $datosUsuarioAnterior['uss_tipo']);
	} else {
		error_log("⚠️ CONSULTA-USUARIO: No se actualizó datosUsuario - Vacío: " . (empty($datosUsuarioAnterior) ? 'SÍ' : 'NO') . " - Tipo: " . ($datosUsuarioAnterior['uss_tipo'] ?? 'NULL'));
	}
}

if (!isset($idSession) || $idSession=="") {
	$idSession = $_SESSION["id"];
	error_log("⚠️ CONSULTA-USUARIO: idSession no estaba definida, tomada de SESSION[id]: " . ($idSession ?? 'NULL'));
}

// PROTECCIÓN: Verificar que datosUsuario exista antes de usarla
$datosUsuarioActual = $_SESSION["datosUsuario"] ?? null;

// FALLBACK DE SEGURIDAD: Si datosUsuario está vacío, intentar recargar desde BD
if (empty($datosUsuarioActual) && !empty($idSession)) {
	error_log("🔴 CONSULTA-USUARIO: datosUsuario VACÍO - Intentando recargar desde BD - Usuario: " . $idSession);
	
	$datosUsuarioActual = UsuariosPadre::sesionUsuario($idSession);
	
	if (!empty($datosUsuarioActual)) {
		// Recargar también los sub-roles si existen
		if (file_exists(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/SubRoles.php")) {
			require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/SubRoles.php");
			$infoRolesUsuario = Administrativo_Usuario_SubRoles::getInfoRolesFromUser($datosUsuarioActual['uss_id'], $_SESSION['idInstitucion']);
			$datosUsuarioActual["sub_roles"] = $infoRolesUsuario['datos_sub_roles_usuario'] ?? [];
			$datosUsuarioActual["sub_roles_paginas"] = $infoRolesUsuario['valores_paginas'] ?? [];
		}
		
		// Actualizar la sesión con los datos recargados
		$_SESSION["datosUsuario"] = $datosUsuarioActual;
		
		error_log("✅ CONSULTA-USUARIO: datosUsuario recargado desde BD - Tipo: " . ($datosUsuarioActual['uss_tipo'] ?? 'NULL'));
	} else {
		error_log("🔴 CONSULTA-USUARIO: FALLBACK FALLÓ - No se encontró usuario en BD - ID: " . $idSession . " - Año: " . ($_SESSION["bd"] ?? 'NULL'));
	}
}

// Log final del estado
if (empty($datosUsuarioActual)) {
	error_log("🔴 CONSULTA-USUARIO: datosUsuario AÚN VACÍO después de todos los intentos - Página: " . ($_SERVER['PHP_SELF'] ?? 'UNKNOWN'));
} else {
	error_log("✅ CONSULTA-USUARIO: datosUsuario OK - Usuario: " . ($datosUsuarioActual['uss_usuario'] ?? 'NULL') . " - Tipo: " . ($datosUsuarioActual['uss_tipo'] ?? 'NULL'));
}