<?php
include("session.php");
$idPaginaInterna = 'DT0269';
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

Modulos::validarAccesoDirectoPaginas();

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

$id = '';
if (!empty($_POST['id'])) {
    $id = $_POST['id'];
}

$razonAnulacion = '';
if (!empty($_POST['razon_anulacion'])) {
    $razonAnulacion = trim($_POST['razon_anulacion']);
}

if (empty($id)) {
    echo '<script type="text/javascript">alert("ID de abono no proporcionado."); window.location.href="abonos.php";</script>';
    exit();
}

if (empty($razonAnulacion)) {
    echo '<script type="text/javascript">alert("Debe ingresar una razón de anulación."); window.location.href="abonos-anular.php?id='.base64_encode($id).'";</script>';
    exit();
}

Movimientos::anularAbono($conexion, $config, $id, $razonAnulacion);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="abonos.php?success=SC_DT_3&id='.base64_encode($id).'";</script>';
exit();


