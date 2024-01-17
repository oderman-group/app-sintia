<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0277';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

if (empty($_POST["fechaInicio"]) or empty($_POST["detalle"]) or (isset($_POST["valor"]) && $_POST["valor"]=="") or empty($_POST["tipo"]) or empty($_POST["metodoPago"])) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="factura-recurrente-agregar.php?error=ER_DT_4";</script>';
    exit();
}

Movimientos::guardarRecurrentes($conexion, $config, $_POST);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="factura-recurrente-editar.php?success=SC_DT_1&id='.base64_encode($_POST["id"]).'";</script>';
exit();