<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/AuditoriaLogger.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0090';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Verificar token CSRF (soporta GET)
Csrf::verificar();

$idUsuario = base64_decode($_GET["id"]);

// Configurar respuesta JSON
header('Content-Type: application/json; charset=utf-8');

try {
	// Obtener datos del usuario ANTES de eliminar
	$datosUsuario = UsuariosPadre::sesionUsuario($idUsuario);
	
	if (empty($datosUsuario)) {
		echo json_encode([
			'success' => false,
			'message' => 'Usuario no encontrado',
			'code' => 'USER_NOT_FOUND'
		]);
		exit();
	}
	
	// Prevenir eliminar al usuario actual
	if ($idUsuario == $_SESSION['id']) {
		echo json_encode([
			'success' => false,
			'message' => 'No puedes eliminar tu propia cuenta',
			'code' => 'CANNOT_DELETE_SELF'
		]);
		exit();
	}
	
	// Eliminar usuario
	UsuariosPadre::eliminarUsuarioPorID($config, $idUsuario);
	
	// Registrar auditoría de eliminación
	AuditoriaLogger::registrarEliminacion(
		'USUARIOS',
		$idUsuario,
		'Eliminado usuario: ' . $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'] . ' (Usuario: ' . $datosUsuario['uss_usuario'] . ')',
		[
			'nombre_completo' => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
			'usuario' => $datosUsuario['uss_usuario'],
			'email' => $datosUsuario['uss_email'],
			'tipo_usuario' => $datosUsuario['uss_tipo'],
			'documento' => $datosUsuario['uss_documento']
		]
	);
	
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	
	// Respuesta exitosa
	echo json_encode([
		'success' => true,
		'message' => 'Usuario eliminado correctamente: ' . $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
		'code' => 'USER_DELETED',
		'data' => [
			'id' => $idUsuario,
			'nombre' => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1']
		]
	]);
	exit();
	
} catch (Exception $e) {
	echo json_encode([
		'success' => false,
		'message' => 'Error al eliminar usuario: ' . $e->getMessage(),
		'code' => 'DELETE_ERROR'
	]);
	exit();
}