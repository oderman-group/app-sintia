<?php
if($periodoConsultaActual!=$datosCargaActual['car_periodo'] and $datosCargaActual['car_permiso2']!=1)
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
	exit();		
}
//Verificar registro de calificaciones en periodos anteriores
$URL = 'calificaciones-registrar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividades WHERE act_id='".$_GET["idR"]."'"));
	if($datosHistoricos['act_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar edición de calificaciones en periodos anteriores
$URL = 'calificaciones-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividades WHERE act_id='".$_GET["idR"]."'"));
	if($datosHistoricos['act_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}

//Verificar editar indicadores en periodos anteriores
$URL = 'indicadores-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_indicadores_carga WHERE ipc_id='".$_GET["idR"]."'"));
	if($datosHistoricos['ipc_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}

//Verificar editar evaluaciones en periodos anteriores
$URL = 'evaluaciones-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividad_evaluaciones WHERE eva_id='".$_GET["idR"]."'"));
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar preguntas evaluaciones en periodos anteriores
$URL = 'evaluaciones-preguntas.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividad_evaluaciones WHERE eva_id='".$_GET["idE"]."'"));
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar preguntas evaluaciones en periodos anteriores
$URL = 'preguntas-editar.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividad_evaluaciones WHERE eva_id='".$_GET["idE"]."'"));
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
}
//Verificar resultados de evaluaciones en periodos anteriores
$URL = 'evaluaciones-resultados.php';
$existeURL = strpos($_SERVER['PHP_SELF'], $URL);
if($existeURL != false){
	$datosHistoricos = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividad_evaluaciones WHERE eva_id='".$_GET["idE"]."'"));
	/* Ya está siendo evaluado en la misma pagina para NO mostrar algunas opciones de edición.
	if($datosHistoricos['eva_periodo']!=$periodoConsultaActual and $datosCargaActual['car_permiso2']!=1){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
		exit();	
	}
	*/
}
?>