<?php
$numError     = $e->getCode();
$lineaError   = $e->getLine();
$aRemplezar   = array("'", '"', "#", "´");
$enRemplezo   = array("\'", "\"", "\#", "\´");
$detalleError = str_replace($aRemplezar, $enRemplezo, $e->getMessage());
$request_data = json_encode($_REQUEST);
global $conexion;
global $baseDatosServicios;
global $config;
global $datosUsuarioActual;
$request_data_sanitizado = mysqli_real_escape_string($conexion, $request_data);

require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

if(ENVIROMENT === 'PROD') {
	$contenidoMsg = '
		<p>An user has got an error:</p>
		<p>
			<b>Enviroment:</b> '.ENVIROMENT.'<br>
			<b>Institution:</b> '.$config['conf_id_institucion'].' '.$_SESSION["datosUnicosInstitucion"]["ins_nombre"].'<br>
			<b>Year:</b> '.$_SESSION["bd"].'<br>
			<b>User:</b> '.$_SESSION["id"].' - '.$datosUsuarioActual['uss_nombre'].'<br>
			<b>User contact data:</b> '.$datosUsuarioActual['uss_email'].' - '.$datosUsuarioActual['uss_celular'].' - '.$datosUsuarioActual['uss_telefono'].'<br>
			<b>Date:</b> '.date("d/m/Y h:i:s").'<br>
			<b>Cod. Error:</b> '.$numError.'<br>
			<b>Current URL:</b> '.$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'].'<br>
			<b>URL Reference:</b> '.$_SERVER['HTTP_REFERER'].'<br>
			<b>Error detail:</b> '.$detalleError.'<br>
			<b>Line of error:</b> '.$lineaError.'<br>
			<b>Request:</b> '.$request_data_sanitizado.'<br>
			<b>Error trace:</b> '.$e->getTraceAsString().'<br>
		</p>
		';

	$data = [
		'usuario_email'    => 'info@oderman-group.com',
		'usuario_nombre'   => 'Jhon Oderman',
		'usuario2_email'   => 'enuar2110@gmail.com',
		'usuario2_nombre'  => 'Enuar Lara',
		'institucion_id'   => $config['conf_id_institucion'],
		'institucion_agno' => $_SESSION["bd"],
		'usuario_id'       => $_SESSION["id"],
		'contenido_msj'    => $contenidoMsg
	];
	$asunto = 'Error report - COD: '.$numError;
	$bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';

	EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
}

try {
	$idReporteError = Utilidades::logError($e);
} catch (Exception $e) {
	throw new Exception("Error al intentar guardar el error: ".$e->getMessage());
}
?>
	<div style="font-family: Consolas; padding: 10px; background-color: black; color:greenyellow;">
		<strong>ERROR DE EJECUCIÓN</strong><br>
		Lo sentimos, ha ocurrido un error.<br>
		Pero no se preocupe, hemos reportado este error automáticamente al personal de soporte de la plataforma SINTIA para que lo solucione lo antes posible.<br>
		
		<p>
			Si necesita ayuda urgente, comuniquese con el personal encargado de la plataforma y reporte los siguientes datos:<br>
			<b>ID del reporte del error:</b> <?=$idReporteError;?>.<br>
			<b>Número del error:</b> <?=$numError;?>.

			<?php if($datosUsuarioActual['uss_tipo'] == TIPO_DEV){?>
				<hr>
				<b>Detalle del error:</b> <?=$detalleError;?><br>
				<b>Linea del error:</b> <?=$lineaError;?><br>
				<b>Error trace:</b> <?=$e->getTraceAsString();?>
				<p><?php print_r(debug_backtrace());?></p>
			<?php }?>
		</p>
		
		<p>
			<a href="javascript:history.go(-1);" style="color: yellow;">Regresar a la página anterior</a>
		</p>
	</div>
<?php
exit();
