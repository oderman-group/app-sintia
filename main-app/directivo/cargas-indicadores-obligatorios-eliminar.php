<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0157';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

$idIndicador = base64_decode($_GET["idN"]);

// Verificar si el indicador está en uso antes de permitir eliminación
$verificacionUso = Indicadores::verificarIndicadorEnUso($config, $idIndicador);
if ($verificacionUso['enUso']) {
	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">alert("' . addslashes($verificacionUso['mensaje']) . '"); window.location.href="cargas-indicadores-obligatorios.php";</script>';
	exit();
}

Indicadores::eliminarIndicadores($idIndicador);

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
exit();