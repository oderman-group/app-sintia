<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0254';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

$creado = null;
if(!empty($_REQUEST['itemModificar'])){
    $idInsercion=$_REQUEST['itemModificar'];
    try {
        mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".transaction_items SET id_item='".$_REQUEST['idItem']."', cantity='".$_REQUEST['cantidad']."', subtotal='".$_REQUEST['subtotal']."', price='".$_REQUEST['precio']."', discount=0, tax=0 WHERE id='".$_REQUEST['itemModificar']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
    } catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }

    $creado = 0;
}else{
    $idInsercion=Utilidades::generateCode("TXI_");
    try {
        mysqli_query($conexion, "INSERT INTO ".BD_FINANCIERA.".transaction_items(id, id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, price)VALUES('".$idInsercion."', '".$_REQUEST['idTransaction']."', '".$_REQUEST['typeTransaction']."', 0, '".$_REQUEST['cantidad']."', '".$_REQUEST['subtotal']."', '".$_REQUEST['idItem']."', {$config['conf_id_institucion']}, {$_SESSION["bd"]}, '".$_REQUEST['precio']."')");
    } catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }
    
    $creado = 1;
}

$arrayIdInsercion=["idInsercion"=>$idInsercion, "creado"=>$creado];

header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");