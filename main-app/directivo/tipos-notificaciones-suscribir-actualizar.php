<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0351';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once ROOT_PATH . '/main-app/class/App/Comunicativo/Usuarios_Notificaciones.php';

$_POST = json_decode(file_get_contents("php://input"), true);
$respuesta = array();

if($_POST["suscribirTodos"] == "true"){

	

	$camposBorrar = [
		'upn_tipo_notificacion' => $_POST["idNotificacion"],
		'institucion'           => $_POST["idInstitucion"],
		'year'                  => $_POST["year"]
	];

	Comunicativo_Usuarios_Notificaciones::Delete($camposBorrar,BD_GENERAL);

	if($_POST["suscribir"] == "true"){
		try {
			$lista = Comunicativo_Usuarios_Notificaciones::ObtenerUsuariosDirectivosxTipoNotificacionSuscripcion($_POST["idNotificacion"],$_POST["year"],$_POST["idInstitucion"]);
			foreach ($lista as $usuario) {
				$campos = [
					'upn_tipo_notificacion' => $_POST["idNotificacion"],
					'upn_usuario'           => $usuario['uss_id'],
					'institucion'           => $_POST["idInstitucion"],
					'year'                  => $_POST["year"]
				];

				Comunicativo_Usuarios_Notificaciones::Insert($campos,BD_GENERAL);
			}
			$respuesta["ok"]   = true;
			$respuesta["msg"]  = "Todos los usuarios suscritos con Exito!";
			
		} catch (Exception $e) {
			$respuesta["ok"]  = false;
			$respuesta["msg"] = $e;
			include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
			
		}
		
		echo json_encode($respuesta);	
		exit();
		
	} else {
		
		$respuesta["ok"]   = true;
		$respuesta["msg"]  = "Todos los usuarios desuscritos con Exito!";
		
		echo json_encode($respuesta);	
		exit();
		
	}

}else{

	if($_POST["suscribir"] == "true"){
		
		try {
			$campos = [
				'upn_tipo_notificacion' => $_POST["idNotificacion"],
				'upn_usuario'           => $_POST["idUsuario"],
				'institucion'           => $_POST["idInstitucion"],
				'year'                  => $_POST["year"]
			];

			Comunicativo_Usuarios_Notificaciones::Insert($campos,BD_GENERAL);
			$respuesta["ok"]   = true;
			$respuesta["msg"]  = "Usuario suscrito con Exito!";

		} catch (Exception $e) {
			$respuesta["ok"]  = false;
			$respuesta["msg"] = $e;
			include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
			
		}
		echo json_encode($respuesta);	
		exit();
	} else {

		try {

			$campos = [
				'upn_tipo_notificacion' => $_POST["idNotificacion"],
				'upn_usuario'           => $_POST["idUsuario"],
				'institucion'           => $_POST["idInstitucion"],
				'year'                  => $_POST["year"]
			];

			Comunicativo_Usuarios_Notificaciones::Delete($campos,BD_GENERAL);
			$respuesta["ok"]   = true;
			$respuesta["msg"]  = "Usuario desuscrito con Exito!";

		} catch (Exception $e) {
			$respuesta["ok"]  = false;
			$respuesta["msg"] = $e;
			include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
			
		}
		echo json_encode($respuesta);	
		exit();
	}
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="tipos-notificaciones-suscribir.php?id='.base64_encode($_POST["idNotificacion"]).'";</script>';
exit();