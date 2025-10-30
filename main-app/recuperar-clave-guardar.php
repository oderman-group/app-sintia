<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");

require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH . "/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH . "/main-app/class/App/Seguridad/AuditoriaLogger.php");

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

$year_actual=date('Y');
$datosUsuario = Usuarios::buscarUsuarioIdNuevo($_POST['usuarioId']);

if (!empty($datosUsuario)) {
	$data = [
		'institucion_id'   => $datosUsuario['institucion'],
		'institucion_agno' => $year_actual,
		'usuario_id'       => $datosUsuario['uss_id'],
		'usuario_email'    => $datosUsuario['uss_email'],
		'usuario_nombre'   => $datosUsuario['uss_nombre'],
		'usuario_usuario'  => $datosUsuario['uss_usuario'],
		'nueva_clave'      => $_REQUEST['password'],
	];
	$asunto = 'Tus credenciales han llegado';
	$bodyTemplateRoute = ROOT_PATH . '/config-general/template-email-recuperar-clave.php';

	EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
	Usuarios::guardarRegistroRestauracion($data);

	// Registrar auditoría de recuperación de contraseña
	AuditoriaLogger::registrar(
		AuditoriaLogger::ACCION_EDITAR,
		'SEGURIDAD',
		'Usuario recuperó contraseña olvidada: ' . $datosUsuario['uss_usuario'],
		AuditoriaLogger::NIVEL_WARNING,
		[
			'accion' => 'recuperacion_clave',
			'usuario' => $datosUsuario['uss_usuario'],
			'email' => $datosUsuario['uss_email']
		],
		$datosUsuario['uss_id']
	);

	echo '<script type="text/javascript">window.location.href="index.php?success=SC_DT_5&email=' . base64_encode($datosUsuario['uss_email']) . '";</script>';
	exit();
} else {
	echo '<script type="text/javascript">window.location.href="recuperar-clave-restaurar.php?usuarioId='.base64_encode($_REQUEST['usuarioId']).'&error=1";</script>';
	exit();
}
