<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0300';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

if($_REQUEST['type'] == INVOICE){
    if($_REQUEST['abonoAnterior'] > 0){
        try {
            $sql = "UPDATE ".BD_FINANCIERA.".payments_invoiced SET payment=? 
                    WHERE invoiced=? AND institucion=? AND year=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $_REQUEST['abono'], PDO::PARAM_STR);
            $stmt->bindParam(2, $_REQUEST['idFactura'], PDO::PARAM_STR);
            $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }else{
        try {
            $sql = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced(payment, invoiced, institucion, year) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $_REQUEST['abono'], PDO::PARAM_STR);
            $stmt->bindParam(2, $_REQUEST['idFactura'], PDO::PARAM_STR);
            $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }
}

if($_REQUEST['type'] == ACCOUNT){
    if(!empty($_REQUEST['conceptoModificar'])){
        $idInsercion=$_REQUEST['conceptoModificar'];
        try {
            $sql = "UPDATE ".BD_FINANCIERA.".payments_invoiced 
                    SET invoiced=?, payment=?, cantity=?, subtotal=? 
                    WHERE id=? AND institucion=? AND year=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $_REQUEST['concepto'], PDO::PARAM_STR);
            $stmt->bindParam(2, $_REQUEST['precio'], PDO::PARAM_STR);
            $stmt->bindParam(3, $_REQUEST['cantidad'], PDO::PARAM_STR);
            $stmt->bindParam(4, $_REQUEST['subtotal'], PDO::PARAM_STR);
            $stmt->bindParam(5, $_REQUEST['conceptoModificar'], PDO::PARAM_STR);
            $stmt->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }else{
        try {
            $sql = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced(invoiced, institucion, year) 
                    VALUES (?, ?, ?)";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $_REQUEST['concepto'], PDO::PARAM_STR);
            $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
            $idInsercion = $conexionPDO->lastInsertId();
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }
}

$arrayIdInsercion=["idInsercion"=>$idInsercion];

header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit;