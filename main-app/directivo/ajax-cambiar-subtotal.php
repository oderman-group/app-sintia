<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0253';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try {
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    // Validar y convertir impuesto - si es 0 o vacío, usar NULL
    $impuestoValue = !empty($_REQUEST['impuesto']) && $_REQUEST['impuesto'] != '0' ? (int)$_REQUEST['impuesto'] : null;
    
    if ($impuestoValue === null) {
        $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                SET cantity=?, price=?, subtotal=?, discount=?, tax=NULL 
                WHERE id_autoincremental=? AND institucion=? AND year=?";
    } else {
        $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                SET cantity=?, price=?, subtotal=?, discount=?, tax=? 
                WHERE id_autoincremental=? AND institucion=? AND year=?";
    }
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_REQUEST['cantidad'], PDO::PARAM_STR);
    $stmt->bindParam(2, $_REQUEST['precio'], PDO::PARAM_STR);
    $stmt->bindParam(3, $_REQUEST['subtotal'], PDO::PARAM_STR);
    $stmt->bindParam(4, $_REQUEST['porcentajeDescuento'], PDO::PARAM_STR);
    if ($impuestoValue !== null) {
        $stmt->bindParam(5, $impuestoValue, PDO::PARAM_INT);
        $stmt->bindParam(6, $_REQUEST['idItem'], PDO::PARAM_INT);
        $stmt->bindParam(7, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(8, $_SESSION["bd"], PDO::PARAM_INT);
    } else {
        $stmt->bindParam(5, $_REQUEST['idItem'], PDO::PARAM_INT);
        $stmt->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
    }
    $stmt->execute();
} catch(Exception $e) {
    echo $e->getMessage();
    exit();
}

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");