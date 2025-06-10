<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/General_Solicitud.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Usuario/Usuario.php");
require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");
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

$predicadoD = [
	'uss_tipo'      => TIPO_DIRECTIVO,
	'institucion'   => $_POST['inst'],
	'year'          => $datosMotivo["soli_year"]
];

$camposD = "uss_id";
$consultaDirectivos = Administrativo_Usuario_Usuario::Select($predicadoD, $camposD, BD_GENERAL)->fetchAll(PDO::FETCH_ASSOC);
foreach ($consultaDirectivos as $datosDirectivo) {
	echo '
		<script type="text/javascript">
			var year            = "' . $datosMotivo["soli_year"] . '";
			var institucion     = ' . $_POST['inst'] . ';
			var emisor          = "' . $_POST['usuario'] . '";
			var nombreEmisor    = "' . $nombreUsuario['uss_nombre'] . '";
			var asunto          = "SOLICITUD DE DESBLOQUEO";
			var contenido       = "Ha recibido una nueva solicitud de desbloqueo para el usuario ' . $nombreUsuario['uss_nombre'] . '.";
			var receptor        = "' . $datosDirectivo['uss_id'] . '";
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