<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/General_Solicitud.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");
require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/Social_Email.php");
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/Usuarios_Notificaciones.php");
echo '
	<script src="https://cdn.socket.io/3.1.3/socket.io.min.js" integrity="sha384-cPwlPLvBTa3sKAgddT6krw0cJat7egBga3DJepJyrLl4Q9/5WLra3rrnMcyTyOnh" crossorigin="anonymous"></script>
	<script>
		var socket = io("' . URL_API . '", {
			transports: ["websocket", "polling", "flashsocket"]
		});
	</script>';

$datosMotivo = [
	'soli_id_recurso'   => $_POST["usuario"],
	'soli_remitente'    => $_POST["usuario"],
	'soli_fecha'        => date('Y-m-d H:i:s'),
	'soli_mensaje'      => $_POST["contenido"],
	'soli_estado'       => 1,
	'soli_tipo'         => 1,
	'soli_institucion'  => $_POST["inst"],
	'soli_year'         => date("Y")
];
Administrativo_General_Solicitud::Insert($datosMotivo, BD_GENERAL);

$predicado = [
	'uss_id'        => $_POST['usuario'],
	'institucion'   => $_POST['inst'],
	'year'          => $datosMotivo["soli_year"]
];

$campos = "TRIM(CONCAT(IFNULL(uss_nombre, ''), ' ', IFNULL(uss_nombre2, ''), ' ', IFNULL(uss_apellido1, ''), ' ', IFNULL(uss_apellido2, ''))) AS uss_nombre";
$consultaNombre = Administrativo_Usuario_Usuario::Select($predicado, $campos, BD_GENERAL);
$nombreUsuario = $consultaNombre->fetch(PDO::FETCH_ASSOC);

Administrativo_Usuario_Usuario::foreignKey(Administrativo_Usuario_Usuario::INNER, [
	'uss_id'      => 'upn_usuario',
	'year'        => Comunicativo_Usuarios_Notificaciones::$tableAs.'.year',
	'institucion' => Comunicativo_Usuarios_Notificaciones::$tableAs.'.institucion'
]);

$predicadoJoin = [
	'upn_tipo_notificacion' => Comunicativo_Usuarios_Notificaciones::TIPO_NOTIFICACION_DESBLOQUEO_USUARIO,
	'year'                  => $datosMotivo["soli_year"],
	'institucion'           => $_POST['inst'],
];

$camposJoin = 'upn_usuario, uss_email,  uss_nombre';

$consultaDirectivoDesbloqueo = Comunicativo_Usuarios_Notificaciones::SelectJoin($predicadoJoin, $camposJoin, [Administrativo_Usuario_Usuario::class]);

$asunto = 'SOLICITUD DE DESBLOQUEO PARA DIRECTIVOS';
$contenido = 'Ha recibido una nueva solicitud de desbloqueo para el usuario ' . $nombreUsuario['uss_nombre'];

foreach ($consultaDirectivoDesbloqueo as $datosDirectivosDesbloqueo) {

	//Envío al correo interno de la plataforma
	$datos = [
        'ema_de'             => $_POST['usuario'],
        'ema_para'           => $datosDirectivosDesbloqueo['upn_usuario'],
        'ema_asunto'         => $asunto,
        'ema_contenido'      => $contenido,
        'ema_fecha'          => date("Y-m-d h:i:s"),
        'ema_visto'          => 0,
        'ema_eliminado_de'   => 0,
        'ema_eliminado_para' => 0,
        'ema_institucion'    => $_POST['inst'],
        'ema_year'           => $datosMotivo["soli_year"]
    ]; 

	Comunicativo_Social_Email::Insert($datos, BD_ADMIN);


	//Envío al correo real del directivo
        $contenidoMsj = '<p style="color:navy;">'.$contenido.'</p>';

        $data = [
			'asunto'         => $asunto,
            'contenido_msj'  => $contenidoMsj,
            'usuario_email'  => $datosDirectivosDesbloqueo['uss_email'],
            'usuario_nombre' => $datosDirectivosDesbloqueo['uss_nombre'],
			'institucion_id' => $_POST['inst'],
			'usuario_id'     => $datosDirectivosDesbloqueo['upn_usuario']
        ];

        $bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-enviar-solicitud-desbloqueo-directivos.php';
        
        EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
	echo '
		<script type="text/javascript">
			var year            = "' . $datosMotivo["soli_year"] . '";
			var institucion     = ' . $_POST['inst'] . ';
			var emisor          = "' . $_POST['usuario'] . '";
			var nombreEmisor    = "' . $nombreUsuario['uss_nombre'] . '";
			var asunto          = "SOLICITUD DE DESBLOQUEO";
			var contenido       = "Ha recibido una nueva solicitud de desbloqueo para el usuario ' . $nombreUsuario['uss_nombre'] . '.";
			var receptor        = "' . $datosDirectivosDesbloqueo['uss_id'] . '";
			socket.emit("enviar_mensaje_correo", {
				year: year,
				institucion: institucion,
				emisor: emisor,
				nombreEmisor: nombreEmisor,
				asunto: asunto,
				contenido: contenido,
				receptor: receptor
			});
		</script>';
}

echo '
	<script type="text/javascript">
		var year        = "' . $datosMotivo["soli_year"] . '";
		var institucion = ' . $_POST['inst'] . ';
		var idRecurso   = "' . $_POST['usuario'] . '";
		var ENVIROMENT  = "' . ENVIROMENT . '";
		socket.emit("solicitud_desbloqueo", {
			year: year,
			institucion: institucion,
			idRecurso: idRecurso,
			ENVIROMENT: ENVIROMENT
		});
	</script>';

echo '<script type="text/javascript">window.location.href="index.php?success='.Mensajes_Informativos::SOLICITUD_DESBLOQUEO.'&inst='.base64_encode($_POST["inst"]).'";</script>';
exit();