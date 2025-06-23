<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'AC0040';

include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Academico/Matricula_Adjuntos.php';
require_once ROOT_PATH . '/main-app/class/App/Comunicativo/Usuarios_Notificaciones.php';
require_once ROOT_PATH . '/main-app/class/App/Comunicativo/Social_Email.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();

try {
	$campos = [
		'ama_id_estudiante'   => $_POST["ama_id_estudiante"],
		'ama_documento'       => $_POST["ama_documento"],
		'ama_id_responsable'  => $_SESSION["id"],
		'ama_visible'         => $_POST["ama_visible"] ? 0 : 1,
		'institucion'         => $_POST["institucion"],
		'year'                => $_POST["year"],
		'ama_titulo'          => $_POST["ama_titulo"],
		'ama_descripcion'     => $_POST["ama_descripcion"]
	];

	if(!Academico_Matriculas_Adjuntos::Insert($campos,BD_ACADEMICA)){	

		$respuesta["estado"]   = 'ko';
		$respuesta["mensaje"]  = "No se pudo guardar el documento!";
	}else{

		$predicado = [
			'uss_id'        => $_SESSION["id"],
			'institucion'   => $_POST['institucion'],
			'year'          => $_POST["year"]
		];

		$campos = "TRIM(CONCAT(IFNULL(uss_nombre, ''), ' ', IFNULL(uss_nombre2, ''), ' ', IFNULL(uss_apellido1, ''), ' ', IFNULL(uss_apellido2, ''))) AS uss_nombre";
		$consultaNombre = Administrativo_Usuario_Usuario::Select($predicado, $campos, BD_GENERAL);
		$nombreUsuario = $consultaNombre->fetch(PDO::FETCH_ASSOC);

		$consultaDirectivoCorreo = Comunicativo_Usuarios_Notificaciones::ObtenerUsuariosSuscritosxTipoNotificacion(
			Comunicativo_Usuarios_Notificaciones::TIPO_NOTIFICACION_ADJUNTAR_DOCUMENTO_ESTUDIANTE_ACUDIENTE, 
			$_POST['year'], 
			$_POST["institucion"]
		);

		$asunto = 'NOTIFICACION DE DOCUMENTO ADJUNTO POR ACUDIENTE';
		$contenido = 'Ha recibido una nueva notificacion por crearcion de documentos adjuntos por el usuario ' . $nombreUsuario['uss_nombre'];

		foreach ($consultaDirectivoCorreo as $datosDirectivosCorreo) {

			//Envío al correo interno de la plataforma
			$datos = [
				'ema_de'             => $_SESSION["id"],
				'ema_para'           => $datosDirectivosCorreo['upn_usuario'],
				'ema_asunto'         => $asunto,
				'ema_contenido'      => $contenido,
				'ema_fecha'          => date("Y-m-d h:i:s"),
				'ema_visto'          => 0,
				'ema_eliminado_de'   => 0,
				'ema_eliminado_para' => 0,
				'ema_institucion'    => $_POST['institucion'],
				'ema_year'           => $_POST["year"]
			]; 

			Comunicativo_Social_Email::Insert($datos, BD_ADMIN);
		}

		$respuesta["estado"]   = 'ok';
		$respuesta["mensaje"]  = "Documento guardado con Exito!";
	}

	

} catch (Exception $e) {
	$respuesta["estado"]  = 'ko';
	$respuesta["mensaje"] = $e;
	include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
	
}

echo json_encode($respuesta);
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();