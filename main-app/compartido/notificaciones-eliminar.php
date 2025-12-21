<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0047';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $sql = "DELETE FROM ".$baseDatosServicios.".general_alertas WHERE alr_id=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $_GET["idR"], PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $_SERVER["HTTP_REFERER"] . '";</script>';
exit();