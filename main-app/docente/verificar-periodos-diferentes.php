<?php
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
if($periodoConsultaActual!=$datosCargaActual['car_periodo'] and $datosCargaActual['car_permiso2']!=1)
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
	exit();		
}
$idR="";
if(!empty($_GET["idR"])){ $idR=base64_decode($_GET["idR"]);}
if(!empty($_GET["idE"])){ $idR=base64_decode($_GET["idE"]);}

//Verificar registro de calificaciones en periodos anteriores
$URL = 'calificaciones-registrar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$consultaDatosHistoricos=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_actividades WHERE act_id='".base64_decode($_GET["idR"])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	$datosHistoricos = mysqli_fetch_array($consultaDatosHistoricos, MYSQLI_BOTH);
	if($datosHistoricos['act_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar edición de calificaciones en periodos anteriores
$URL = 'calificaciones-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$consultaDatosHistoricos=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_actividades WHERE act_id='".base64_decode($_GET["idR"])."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	$datosHistoricos = mysqli_fetch_array($consultaDatosHistoricos, MYSQLI_BOTH);
	if($datosHistoricos['act_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}

//Verificar editar indicadores en periodos anteriores
$URL = 'indicadores-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = Indicadores::traerDatosIndicador($conexion, $config, base64_decode($_GET["idR"]));
	if($datosHistoricos['ipc_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}

//Verificar editar evaluaciones en periodos anteriores
$URL = 'evaluaciones-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = Evaluaciones::consultaEvaluacion($conexion, $config, $idR);
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar preguntas evaluaciones en periodos anteriores
$URL = 'evaluaciones-preguntas.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = Evaluaciones::consultaEvaluacion($conexion, $config, $idR);
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar preguntas evaluaciones en periodos anteriores
$URL = 'preguntas-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = Evaluaciones::consultaEvaluacion($conexion, $config, $idR);
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar resultados de evaluaciones en periodos anteriores
$URL = 'evaluaciones-resultados.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = Evaluaciones::consultaEvaluacion($conexion, $config, $idR);
}
?>