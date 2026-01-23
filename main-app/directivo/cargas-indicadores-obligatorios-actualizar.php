<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0170';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
	echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
	exit();
}

// Verificar si el indicador está en uso antes de permitir edición
$verificacionUso = Indicadores::verificarIndicadorEnUso($config, $_POST["idI"]);
if ($verificacionUso['enUso']) {
	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">alert("' . addslashes($verificacionUso['mensaje']) . '"); window.location.href="cargas-indicadores-obligatorios.php";</script>';
	exit();
}

$ind = Indicadores::consultarValorIndicadoresObligatorios($_POST["idI"]);
$sumaActual = !empty($ind[0]) ? (float)$ind[0] : 0;
$valorNuevo = (float)$_POST["valor"];

if (($sumaActual + $valorNuevo) > 100) {
	echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
	exit();
}

$update = [
	'ind_nombre' => $_POST["nombre"], 
	'ind_valor'  => $_POST["valor"]
];
Indicadores::actualizarIndicador($config, $_POST["idI"], $update);

// Procesar asignaciones de cargas si se enviaron períodos
$mensajeAsignacion = "";
if (!empty($_POST["periodos"]) && is_array($_POST["periodos"]) && count($_POST["periodos"]) > 0) {
	// Determinar cargas a asignar
	$cargas = [];
	if (!empty($_POST["asignacionTipo"]) && $_POST["asignacionTipo"] == "especificas" && !empty($_POST["cargas"]) && is_array($_POST["cargas"]) && count($_POST["cargas"]) > 0) {
		$cargas = $_POST["cargas"];
	}
	// Si es "todas", el array queda vacío y el método asignarIndicadorACargas obtendrá todas las cargas activas con car_indicadores_directivo=1

	// Asignar indicador a cargas y períodos
	$resultadoAsignacion = Indicadores::asignarIndicadorACargas(
		$conexion,
		$conexionPDO,
		$config,
		$_POST["idI"],
		$cargas,
		$_POST["periodos"],
		(float)$_POST["valor"]
	);

	// Construir mensaje de resultado
	if ($resultadoAsignacion['asignadas'] > 0) {
		$mensajeAsignacion = "Indicador actualizado y asignado a {$resultadoAsignacion['asignadas']} relación(es) de carga/período.";
	}
	if ($resultadoAsignacion['duplicadas'] > 0) {
		$mensajeAsignacion .= ($mensajeAsignacion ? " " : "") . "Se omitieron {$resultadoAsignacion['duplicadas']} asignación(es) duplicada(s).";
	}
	if (!empty($resultadoAsignacion['errores'])) {
		$mensajeAsignacion .= ($mensajeAsignacion ? " " : "") . "Errores: " . implode(", ", $resultadoAsignacion['errores']);
	}
	
	// Si no hubo asignaciones ni duplicadas ni errores, puede ser que no haya cargas disponibles
	if ($resultadoAsignacion['asignadas'] == 0 && $resultadoAsignacion['duplicadas'] == 0 && empty($resultadoAsignacion['errores'])) {
		$mensajeAsignacion = "No se pudieron asignar cargas. Verifique que existan cargas académicas con la configuración 'Indicadores definidos por directivo' activada.";
	}
}

include("../compartido/guardar-historial-acciones.php");

if (!empty($mensajeAsignacion)) {
	echo '<script type="text/javascript">alert("' . addslashes($mensajeAsignacion) . '"); window.location.href="cargas-indicadores-obligatorios.php";</script>';
} else {
	echo '<script type="text/javascript">window.location.href="cargas-indicadores-obligatorios.php";</script>';
}
exit();