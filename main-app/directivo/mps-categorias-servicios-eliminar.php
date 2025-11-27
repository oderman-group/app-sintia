<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0054';
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $idCategoria = base64_decode($_GET["idR"]);
    $sql = "UPDATE " . $baseDatosMarketPlace . ".servicios_categorias SET svcat_activa=0 WHERE svcat_id=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idCategoria, PDO::PARAM_STR);
    $stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="mps-categorias-servicios.php?error=ER_DT_3";</script>';
exit();