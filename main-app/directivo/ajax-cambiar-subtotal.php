<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0253';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

try {
    $itemsConsulta = mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".transaction_items SET cantity='".$_REQUEST['cantidad']."', price='".$_REQUEST['precio']."', subtotal='".$_REQUEST['subtotal']."', discount='".$_REQUEST['porcentajeDescuento']."', tax='".$_REQUEST['impuesto']."' WHERE id='".$_REQUEST['idItem']."' AND institucion = {$config['conf_id_institucion']} AND year = {$_SESSION["bd"]}");
} catch(Exception $e) {
    echo $e->getMessage();
    exit();
}

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");