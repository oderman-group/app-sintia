<?php
// Headers de seguridad HTTP (deben enviarse ANTES de cualquier output)
require_once(__DIR__ . "/../class/App/Seguridad/SecurityHeaders.php");

// Incluir clase CSRF
require_once(__DIR__ . "/../class/App/Seguridad/Csrf.php");

// Configuración segura de sesiones ANTES de session_start()
ini_set('session.cookie_httponly', 1); // Previene acceso a cookies desde JavaScript
ini_set('session.use_only_cookies', 1); // Solo cookies, no URLs
ini_set('session.cookie_samesite', 'Lax'); // Protección CSRF (Lax en lugar de Strict por compatibilidad)
ini_set('session.use_strict_mode', 1); // No aceptar IDs de sesión no inicializados
ini_set('session.gc_maxlifetime', 7200); // Sesiones expiran en 2 horas
ini_set('session.cookie_lifetime', 0); // Cookie expira al cerrar navegador

// HTTPS solo en producción (descomentar cuando se tenga SSL)
// ini_set('session.cookie_secure', 1);

session_start();

// Regenerar ID de sesión periódicamente (prevenir session fixation)
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutos
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Validar User-Agent (prevenir session hijacking básico)
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $userAgent;
} elseif ($_SESSION['user_agent'] !== $userAgent) {
    // Posible intento de hijacking
    session_destroy();
    header("Location:../controlador/salir.php?msg=session_hijack");
    exit();
}

//Si otro usuario de mayor rango entra como él
if(isset($_SESSION["idO"]) and $_SESSION["idO"]!=""){
    $idSession = $_SESSION["idO"];
} else {
    $idSession = $_SESSION["id"] ?? '';
}

if (empty($idSession)) {
	session_destroy();
	header("Location:../controlador/salir.php?session_empty=true");
	exit();
} else {
	require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
	require_once(ROOT_PATH."/config-general/config.php");
	require_once(ROOT_PATH."/config-general/consulta-usuario-actual.php");
	require_once(ROOT_PATH."/config-general/idiomas.php"); // Movido después de consulta-usuario-actual
	require_once(ROOT_PATH."/config-general/verificar-usuario-bloqueado.php");

	if($datosUsuarioActual['uss_tipo'] != TIPO_DIRECTIVO && $datosUsuarioActual['uss_tipo'] != TIPO_DEV && $datosUsuarioActual['uss_tipo'] != TIPO_DOCENTE && $datosUsuarioActual['uss_tipo'] != TIPO_ACUDIENTE && $datosUsuarioActual['uss_tipo'] != TIPO_ESTUDIANTE && !strpos($_SERVER['PHP_SELF'], 'page-info.php'))
	{
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=304";</script>';
		exit();		
	}
}
