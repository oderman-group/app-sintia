<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0035';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

// Validar campos requeridos
if (empty($_POST["carga"])) {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>Debe especificar la carga académica.</samp>";
	exit();
}

if (empty($_POST["indicadores"]) || !is_array($_POST["indicadores"])) {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>Debe seleccionar al menos un indicador.</samp>";
	exit();
}

if (empty($_POST["periodos"]) || !is_array($_POST["periodos"])) {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>Debe seleccionar al menos un período.</samp>";
	exit();
}

$idCarga = $_POST["carga"];
$totalAsignadas = 0;
$totalDuplicadas = 0;
$errores = [];

// Aplicar cada indicador seleccionado
foreach ($_POST["indicadores"] as $idIndicador) {
	// Obtener el valor del indicador
	$datosIndicador = Indicadores::traerIndicadoresDatos($idIndicador);
	if (empty($datosIndicador)) {
		$errores[] = "Indicador ID {$idIndicador} no encontrado.";
		continue;
	}

	// Asignar indicador a la carga y períodos seleccionados
	$resultadoAsignacion = Indicadores::asignarIndicadorACargas(
		$conexion,
		$conexionPDO,
		$config,
		$idIndicador,
		[$idCarga],
		$_POST["periodos"],
		(float)$datosIndicador['ind_valor']
	);

	$totalAsignadas += $resultadoAsignacion['asignadas'];
	$totalDuplicadas += $resultadoAsignacion['duplicadas'];
	
	if (!empty($resultadoAsignacion['errores'])) {
		$errores = array_merge($errores, $resultadoAsignacion['errores']);
	}
}

// Preparar mensaje de resultado
$mensaje = "";
if ($totalAsignadas > 0) {
	$mensaje = "Se aplicaron {$totalAsignadas} indicador(es) a la carga académica.";
}
if ($totalDuplicadas > 0) {
	$mensaje .= ($mensaje ? " " : "") . "Se omitieron {$totalDuplicadas} asignación(es) duplicada(s).";
}
if (!empty($errores)) {
	$mensaje .= ($mensaje ? " " : "") . "Errores: " . implode(", ", array_slice($errores, 0, 3));
}

include("../compartido/guardar-historial-acciones.php");

if (!empty($mensaje)) {
	echo '<script type="text/javascript">alert("' . addslashes($mensaje) . '"); window.location.href="cargas-editar.php?idR='.base64_encode($idCarga).'";</script>';
} else {
	echo '<script type="text/javascript">window.location.href="cargas-editar.php?idR='.base64_encode($idCarga).'";</script>';
}
exit();
