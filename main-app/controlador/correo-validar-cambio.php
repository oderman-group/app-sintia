<?php
session_start();
require_once("../../conexion.php");
if(!empty($_POST["bd"])){
	require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");

	$sql="SELECT * FROM ".BD_GENERAL.".usuarios 
	INNER JOIN ".BD_ADMIN.".instituciones ON ins_id=institucion
	WHERE (uss_usuario='".trim($_POST["Usuario"])."-".$_POST["bd"]."' OR uss_usuario LIKE '".trim($_REQUEST["Usuario"])."%' ) AND TRIM(uss_usuario)!='' AND uss_clave=SHA1('".$_POST["Clave"]."')  AND uss_usuario IS NOT NULL  AND institucion=".$_POST["bd"]." ORDER BY uss_ultimo_ingreso DESC LIMIT 1";
	$rst_usrE = mysqli_query($conexion, $sql);
	$numE = mysqli_num_rows($rst_usrE);
	if($numE>0){
		$datosUsuarioActual = mysqli_fetch_array($rst_usrE, MYSQLI_BOTH);


		$_SESSION["inst"] 									= $datosUsuarioActual['ins_bd'];
		$_SESSION["idInstitucion"] 							= $datosUsuarioActual['ins_id'];
		$config['conf_id_institucion'] 						= $datosUsuarioActual['ins_id'];
		$_SESSION["bd"] 									= $datosUsuarioActual['ins_year_default'];
		$_SESSION["id"]										= $datosUsuarioActual['uss_id'];
		$_SESSION["datosUnicosInstitucion"]["ins_nombre"] 	= $datosUsuarioActual['ins_nombre'];

		$data = [
			'institucion_id'   		=> $datosUsuarioActual['ins_id'],
			'institucion_nombre'   	=> $datosUsuarioActual['ins_siglas'],
			'institucion_agno' 		=> $datosUsuarioActual['ins_year_default'],
			'usuario_id'       		=> $datosUsuarioActual['uss_id'],
			'usuario_email'    		=> $datosUsuarioActual['uss_email'],
			'usuario_nombre'   		=> $datosUsuarioActual["uss_nombre"]." ".$datosUsuarioActual["uss_apellido1"],
			'usuario_usuario'  		=> $datosUsuarioActual["uss_usuario"],
			'usuario2_email'   		=> 'info@oderman-group.com',
			'usuario2_nombre'   	=> 'Jhon Oderman / Soporte SINTIA',
			'usuario3_email'   		=> 'enuarlara@oderman-group.com',
			'usuario3_nombre'   	=> 'Enuar Lara / Soporte SINTIA'
		];
		$asunto = 'Mensaje Importante: Cambio de Usuario';
		$bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-notificacion-cambio.php';

		EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);

		mysqli_query($conexion, "UPDATE ".BD_GENERAL.".usuarios SET uss_cambio_notificacion=1 WHERE uss_usuario='".$datosUsuarioActual["uss_usuario"]."' AND uss_clave=SHA1('".$_POST["Clave"]."')  AND institucion=".$_POST["bd"]."");

		//FIN ENVÍO DE MENSAJE
		header("Location:autentico.php?Usuario=".base64_encode($datosUsuarioActual["uss_usuario"])."&Clave=".base64_encode($_REQUEST["Clave"])."&suma=".base64_encode($_REQUEST["suma"])."&sumaReal=".base64_encode($_REQUEST["sumaReal"])."&urlDefault=".base64_encode($_REQUEST["urlDefault"])."&directory=".base64_encode($_REQUEST["directory"]));
		exit();
	}else{
		header("Location:".REDIRECT_ROUTE."/index.php?error=1");
		exit();
	}
}else{
	header("Location:".REDIRECT_ROUTE."/controlador/autentico-validar-cambio.php?error=2");
	exit();
}