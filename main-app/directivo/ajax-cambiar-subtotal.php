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
    
    // Obtener snapshot del impuesto (type_tax y fee) si hay impuesto seleccionado
    $taxName = null;
    $taxFee = null;
    if ($impuestoValue !== null) {
        try {
            $sqlObtenerTax = "SELECT type_tax, fee FROM ".BD_FINANCIERA.".taxes 
                             WHERE id=? AND institucion=? AND year=? LIMIT 1";
            $stmtObtenerTax = $conexionPDO->prepare($sqlObtenerTax);
            $stmtObtenerTax->bindParam(1, $impuestoValue, PDO::PARAM_INT);
            $stmtObtenerTax->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtObtenerTax->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtObtenerTax->execute();
            $taxData = $stmtObtenerTax->fetch(PDO::FETCH_ASSOC);
            
            if ($taxData && !empty($taxData['type_tax'])) {
                $taxName = $taxData['type_tax'];
                $taxFee = floatval($taxData['fee']);
            }
        } catch(Exception $e) {
            // Si hay error al obtener el impuesto, continuar con NULL
            $taxName = null;
            $taxFee = null;
        }
    }
    
    if ($impuestoValue === null) {
        $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                SET cantity=?, price=?, subtotal=?, discount=?, tax=NULL, tax_name=NULL, tax_fee=NULL 
                WHERE id_autoincremental=? AND institucion=? AND year=?";
    } else {
        $sql = "UPDATE ".BD_FINANCIERA.".transaction_items 
                SET cantity=?, price=?, subtotal=?, discount=?, tax=?, tax_name=?, tax_fee=? 
                WHERE id_autoincremental=? AND institucion=? AND year=?";
    }
    
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_REQUEST['cantidad'], PDO::PARAM_STR);
    $stmt->bindParam(2, $_REQUEST['precio'], PDO::PARAM_STR);
    $stmt->bindParam(3, $_REQUEST['subtotal'], PDO::PARAM_STR);
    $stmt->bindParam(4, $_REQUEST['porcentajeDescuento'], PDO::PARAM_STR);
    if ($impuestoValue !== null) {
        $stmt->bindParam(5, $impuestoValue, PDO::PARAM_INT);
        $stmt->bindParam(6, $taxName, PDO::PARAM_STR);
        $stmt->bindParam(7, $taxFee, PDO::PARAM_STR);
        $stmt->bindParam(8, $_REQUEST['idItem'], PDO::PARAM_INT);
        $stmt->bindParam(9, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(10, $_SESSION["bd"], PDO::PARAM_INT);
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