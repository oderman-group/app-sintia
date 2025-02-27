<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
include(ROOT_PATH."/main-app/class/Asignaciones.php");

if($datosUsuarioActual['uss_bloqueado']==1 && !strpos($_SERVER['PHP_SELF'], 'page-info.php'))
{
	require_once(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
	$destinos = validarUsuarioActual($datosUsuarioActual);
	header("Location:".$destinos."page-info.php?idmsg=221");
	exit();		
}

//validamos si el usuario tiene encuestas pendientes
try {
    $consultaAsignacionEncuesta = Asignaciones::traerAsignacionesUsuario($conexion, $config, $datosUsuarioActual['uss_id']);
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$numAsignacionesEncuesta = mysqli_num_rows($consultaAsignacionEncuesta);

//Contamos si de esas pendientes tiene obligatorias
$asignacionesObligatorias = 0;
if ($numAsignacionesEncuesta > 0){
	foreach ($consultaAsignacionEncuesta as $arrayAsignaciones) {
		if($arrayAsignaciones['evag_obligatoria'] == 1){
			$asignacionesObligatorias ++;
		}
	}
}