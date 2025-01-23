<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/Notificacion.php");

$notificacion = new Notificacion();

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

$year_actual=date('Y');

$usuariosEncontrados = 1;
if (!empty($_REQUEST['Usuario'])) {
    $datosUsuario = Usuarios::buscarUsuariosRecuperarClave($_REQUEST['Usuario'],$year_actual);
	$usuariosEncontrados = count($datosUsuario);
} elseif (!empty($_REQUEST['usuarioId'])) {
    $datosUsuario = Usuarios::buscarUsuarioIdNuevo($_REQUEST['usuarioId']);
}

if ($usuariosEncontrados == 1) {
	$datosUsuario = !empty($_REQUEST['Usuario']) ? $datosUsuario[0] : $datosUsuario;
	if (!empty($datosUsuario)) {
        $data = [
            'usuario_nombre'      => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
            'institucion_id'      => $datosUsuario['institucion'],
            'usuario_id'          => $datosUsuario['uss_id'],
            'year'                => $datosUsuario['year'],
            'asunto'              => 'Código de Confirmación: ',
            'body_template_route' => ROOT_PATH .'/config-general/template-email-recuperar-clave-codigo.php',
            'usuario_email'       => $datosUsuario['uss_email'],
            'telefono'            => $datosUsuario['uss_celular'],
            'id_nuevo'            => $datosUsuario['id_nuevo'],
            'datos_codigo'        => [],
        ];

		$canal = Notificacion::CANAL_EMAIL;
		$datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_RECUPERAR_CLAVE);

		if (!empty($_REQUEST['async'])) {

			$arrayIdInsercion=[
				"success"=>true,
				"message"=>"Código enviado exitosamente",
				'usuarioEmail'=> $datosUsuario['uss_email'],
				"code"=>$datosCodigo
			];

			header('Content-Type: application/json');
			echo json_encode($arrayIdInsercion);
			exit;
		} else {
			$data['datos_codigo'] = $datosCodigo;
			$datosUsuarioSerializados = serialize($data);

			echo '<script type="text/javascript">window.location.href="recuperar-clave-validar-codigo.php?datosUsuario=' . base64_encode($datosUsuarioSerializados) . '";</script>';
			exit();
		}
	} else {

		if (!empty($_REQUEST['async'])) {
			$arrayIdInsercion=["success"=>false, "message"=>"Error al enviar el código: ".$e->getMessage()];

			header('Content-Type: application/json');
			echo json_encode($arrayIdInsercion);
			exit;
		} else {
			echo '<script type="text/javascript">window.location.href="recuperar-clave.php?error=1";</script>';
			exit();
		}
	}
}
if ($usuariosEncontrados > 1) {
	// Serializar el array para pasarlo como un campo oculto
	$usuariosSerializados = serialize($datosUsuario);
	// Crear el formulario de redirección automática
	echo '<form id="form" method="post" action="recuperar-clave.php?valor=' . base64_encode($_REQUEST["Usuario"]) . '">';
	echo '<input type="hidden" name="usuariosEncontrados" value="' . htmlspecialchars($usuariosSerializados) . '">';
	echo '</form>';
	echo '<script>document.getElementById("form").submit();</script>';
	exit();
} else {
	echo '<script type="text/javascript">window.location.href="recuperar-clave.php?error=1";</script>';
	exit();
}
