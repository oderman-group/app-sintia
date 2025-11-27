<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0254';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

$creado = null;
if(!empty($_REQUEST['itemModificar'])){
    $idInsercion=$_REQUEST['itemModificar'];
    try {
        $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                SET id_item=?, cantity=?, subtotal=?, price=?, discount=0, tax=0 
                WHERE id=? AND institucion=? AND year=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_REQUEST['idItem'], PDO::PARAM_STR);
        $stmt->bindParam(2, $_REQUEST['cantidad'], PDO::PARAM_STR);
        $stmt->bindParam(3, $_REQUEST['subtotal'], PDO::PARAM_STR);
        $stmt->bindParam(4, $_REQUEST['precio'], PDO::PARAM_STR);
        $stmt->bindParam(5, $_REQUEST['itemModificar'], PDO::PARAM_STR);
        $stmt->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
    } catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }
    $creado = 0;
}else{
    $idInsercion=Utilidades::generateCode("TXI_");
    try {
        $sql = "INSERT INTO ".BD_FINANCIERA.".transaction_items(
            id, id_transaction, type_transaction, discount, cantity, subtotal, id_item, institucion, year, price
        ) VALUES (?, ?, ?, 0, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $idInsercion, PDO::PARAM_STR);
        $stmt->bindParam(2, $_REQUEST['idTransaction'], PDO::PARAM_STR);
        $stmt->bindParam(3, $_REQUEST['typeTransaction'], PDO::PARAM_STR);
        $stmt->bindParam(4, $_REQUEST['cantidad'], PDO::PARAM_STR);
        $stmt->bindParam(5, $_REQUEST['subtotal'], PDO::PARAM_STR);
        $stmt->bindParam(6, $_REQUEST['idItem'], PDO::PARAM_STR);
        $stmt->bindParam(7, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(8, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->bindParam(9, $_REQUEST['precio'], PDO::PARAM_STR);
        $stmt->execute();
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