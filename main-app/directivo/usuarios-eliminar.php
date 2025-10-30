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

// Obtener datos del usuario ANTES de eliminar
$datosUsuario = UsuariosPadre::sesionUsuario($idUsuario);

UsuariosPadre::eliminarUsuarioPorID($config, $idUsuario);

// Registrar auditoría de eliminación
if (!empty($datosUsuario)) {
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
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="usuarios.php?error=ER_DT_3";</script>';
exit();