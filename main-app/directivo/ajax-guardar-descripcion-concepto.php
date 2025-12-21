<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0302';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try {
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "UPDATE ".BD_FINANCIERA.".payments_invoiced SET description=? 
            WHERE id=? AND institucion=? AND year=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_REQUEST['descripcion'], PDO::PARAM_STR);
    $stmt->bindParam(2, $_REQUEST['idConcepto'], PDO::PARAM_STR);
    $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch(Exception $e) {
    echo $e->getMessage();
    exit();
}

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");