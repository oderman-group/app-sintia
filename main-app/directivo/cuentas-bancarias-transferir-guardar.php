<?php
include("session.php");
$idPaginaInterna = 'DT0278';
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

Modulos::validarAccesoDirectoPaginas();

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Validar que todos los campos necesarios estén llenos
if(empty($_POST["cuenta_origen"]) || empty($_POST["cuenta_destino"]) || empty($_POST["monto"]) || empty($_POST["fecha"])){
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_4";</script>';
	exit();
}

// Validar que cuenta origen y destino sean diferentes
if($_POST["cuenta_origen"] === $_POST["cuenta_destino"]){
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_CUENTAS_IGUALES";</script>';
	exit();
}

// Validar monto
$monto = floatval($_POST["monto"]);
if($monto <= 0){
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_MONTO_INVALIDO";</script>';
	exit();
}

// Validar fecha
$fecha = $_POST["fecha"];
$fechaActual = new DateTime();
$fechaDoc = DateTime::createFromFormat('Y-m-d', $fecha);
if ($fechaDoc === false) {
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_FECHA_INVALIDA";</script>';
	exit();
}

$fechaLimite = (clone $fechaActual)->modify('-1 year');

if ($fechaDoc > $fechaActual) {
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_FECHA_FUTURA";</script>';
	exit();
}

if ($fechaDoc < $fechaLimite) {
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_FECHA_ANTIGUA";</script>';
	exit();
}

// Registrar transferencia
$observaciones = !empty($_POST["observaciones"]) ? $_POST["observaciones"] : '';
$resultado = Movimientos::registrarTransferenciaEntreCuentas(
	$conexion,
	$config,
	$_POST["cuenta_origen"],
	$_POST["cuenta_destino"],
	$monto,
	$fecha,
	$observaciones
);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

if($resultado['success']){
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias.php?success=SC_DT_TRANSFERENCIA_OK";</script>';
} else {
	echo '<script type="text/javascript">alert("'.htmlspecialchars($resultado['message']).'"); window.location.href="cuentas-bancarias-transferir.php?error=ER_DT_TRANSFERENCIA";</script>';
}
exit();
