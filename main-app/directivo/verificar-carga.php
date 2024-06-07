<?php
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$periodo=$config['conf_periodo'];
$periodoConsultaActual=$config['conf_periodo'];
if(!isset($_GET["carga"]) or !is_numeric(base64_decode($_GET["carga"]))){
	if($_COOKIE["carga"]!="" and $_COOKIE["periodo"]!=""){
		$cargaConsultaActual = $_COOKIE["carga"];
		$periodoConsultaActual = $_COOKIE["periodo"];
	}else{
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
			exit();
	}
}else{
	if(!empty($_GET["carga"])){
		$cargaConsultaActual = base64_decode($_GET["carga"]);
	}
	if(!empty($_GET["periodo"])){
		$periodo = base64_decode($_GET["periodo"]);
		$periodoConsultaActual = base64_decode($_GET["periodo"]);
	}
}

$cargaHconsulta = CargaAcademica::traerCargaMateriaPorID($config, $cargaConsultaActual);

if($cargaHconsulta['car_primer_acceso_docente']==""){
	$update = "car_primer_acceso_docente=".date("Y-m-d H:i:s")."";
	CargaAcademica::actualizarCargaPorID($config, $cargaConsultaActual, $update);
}else{
	$update = "car_ultimo_acceso_docente=".date("Y-m-d H:i:s")."";
	CargaAcademica::actualizarCargaPorID($config, $cargaConsultaActual, $update);
}
//A los directivos no se les consulta el docente ni tampoco el estado de la carga (Activa o Inactiva)
$datosCargaActual = CargaAcademica::traerCargaMateriaPorID($config, $cargaConsultaActual);

$configCargasArray = array ("Automático","Manual"); 
$dgArray = array ("NO","SI"); 

if(empty($datosCargaActual))
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
	exit();		
}
?>