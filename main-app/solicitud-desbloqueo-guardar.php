<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/App/Administrativo/Solicitud_Desbloqueo/General_Solicitud.php");
require_once(ROOT_PATH."/main-app/class/App/Mensajes_Informativos/Mensajes_Informativos.php");

$datosMotivo = [
	'soli_id_recurso'   => $_POST["usuario"],
	'soli_remitente'    => $_POST["usuario"],
	'soli_fecha'   		=> date('Y-m-d H:i:s'),
	'soli_mensaje'   	=> $_POST["contenido"],
	'soli_estado'       => 1,
	'soli_tipo'			=> 1,
	'soli_institucion'  => $_POST["inst"],
	'soli_year'			=> date("Y")
];
Administrativo_Solicitud_Desbloqueo_General_Solicitud::Insert($datosMotivo, BD_GENERAL);

echo '<script type="text/javascript">window.location.href="index.php?success='.Mensajes_Informativos::SOLICITUD_DESBLOQUEO.'&inst='.base64_encode($_POST["inst"]).'&year='.base64_encode($datosMotivo["soli_year"]).'";</script>';
exit();