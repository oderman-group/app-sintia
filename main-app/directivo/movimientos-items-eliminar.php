<?php
include("session.php");
$idPaginaInterna = 'DT0256';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "DELETE FROM ".BD_FINANCIERA.".transaction_items WHERE id=? AND institucion=? AND year=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_GET["idR"], PDO::PARAM_STR);
    $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="movimientos.php?error=ER_DT_3";</script>';
exit();