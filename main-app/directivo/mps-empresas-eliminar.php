<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0060';
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consultas preparadas
try{
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $idEmpresa = base64_decode($_GET["idR"]);
    
    $sql1 = "UPDATE " . $baseDatosMarketPlace . ".empresas SET emp_eliminado=1 WHERE emp_id=?";
    $stmt1 = $conexionPDO->prepare($sql1);
    $stmt1->bindParam(1, $idEmpresa, PDO::PARAM_STR);
    $stmt1->execute();
    
    $sql2 = "UPDATE " . $baseDatosMarketPlace . ".productos SET prod_estado=1 WHERE prod_empresa=?";
    $stmt2 = $conexionPDO->prepare($sql2);
    $stmt2->bindParam(1, $idEmpresa, PDO::PARAM_STR);
    $stmt2->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="mps-empresas.php?error=ER_DT_3";</script>';
exit();