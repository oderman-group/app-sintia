<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/General_Solicitud.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");
require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
require_once(ROOT_PATH.'/main-app/class/EnviarEmail.php');
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/Social_Email.php");
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/Usuarios_Notificaciones.php");
require_once(ROOT_PATH."/main-app/compartido/socket.php");

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

Administrativo_Usuario_Usuario::foreignKey(self::INNER, [
	'uss_id' => Comunicativo_Usuarios_Notificaciones::$tableAs.'.upn_usuario'
]);

$predicadoJoin = [
	Comunicativo_Usuarios_Notificaciones::$tableAs.'.upn_tipo_notificacion' => Comunicativo_Usuarios_Notificaciones::TIPO_NOTIFICACION_DESBLOQUEO_USUARIO
];

$camposJoin = Comunicativo_Usuarios_Notificaciones::$tableAs.'.uss_id,' . Comunicativo_Usuarios_Notificaciones::$tableAs . '.uss_email, '.Comunicativo_Usuarios_Notificaciones::$tableAs.'.uss_nombre';

$consultaDirectivosDesbloqueo = Comunicativo_Usuarios_Notificaciones::SelectJoin($predicadoJoin, $camposJoin, Comunicativo_Usuarios_Notificaciones::class, [Administrativo_Usuario_Usuario::class]);

$asunto = 'SOLICITUD DE DESBLOQUEO PARA DIRECTIVOS';
$contenido = 'Ha recibido una nueva solicitud de desbloqueo para el usuario ' . $nombreUsuario['uss_nombre'];

while ($datosDirectivosDesbloqueo = $consultaDirectivosDesbloqueo) {

	//Envío al correo interno de la plataforma
	$datos = [
        'ema_de'             => $_POST['usuario'],
        'ema_para'           => $datosDirectivosDesbloqueo['uss_id'],
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
			'asunto' => $asunto,
            'contenido_msj'    => $contenidoMsj,
            'directivo_email'  => $datosDirectivosDesbloqueo['uss_email'],
            'directivo_nombre' => $datosDirectivosDesbloqueo['uss_nombre']
        ];

        $bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-enviar-solicitud-desbloqueo-directivos.php';
        
        EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
	?>
		<script>
			var year            = '<?=($datosMotivo["soli_year"])?>';
			var institucion     = <?=($_POST['inst'])?>;
			var emisor          = '<?=($_POST['usuario'])?>';
			var nombreEmisor    = '<?=($nombreUsuario['uss_nombre'])?>';
			var asunto          = '<?=($asunto)?>';
			var contenido       = '<?=($contenido)?>';
			var receptor        = '<?=($datosDirectivosDesbloqueo['uss_id'])?>';
			socket.emit("enviar_mensaje_correo", {
				year: year,
				institucion: institucion,
				emisor: emisor,
				nombreEmisor: nombreEmisor,
				asunto: asunto,
				contenido: contenido,
				receptor: receptor
			});
		</script>
	<?php
}

?>
	<script>
		var year        = '<?=($datosMotivo["soli_year"])?>';
		var institucion = <?=($_POST['inst'])?>;
		var idRecurso   = '<?=($_POST['usuario'])?>';
		var ENVIROMENT  = '<?=ENVIROMENT?>';
		socket.emit("solicitud_desbloqueo", {
			year: year,
			institucion: institucion,
			idRecurso: idRecurso,
			ENVIROMENT: ENVIROMENT
		});
	</script>
<?php

echo '<script type="text/javascript">window.location.href="index.php?success='.Mensajes_Informativos::SOLICITUD_DESBLOQUEO.'&inst='.base64_encode($_POST["inst"]).'";</script>';
exit();