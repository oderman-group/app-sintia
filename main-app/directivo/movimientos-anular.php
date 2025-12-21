<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0089';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$sql = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas SET fcu_anulado=1 WHERE fcu_id=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $_GET["idR"], PDO::PARAM_STR);
	$stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="movimientos.php?usuario=' . base64_encode($_GET["id"]) . '";</script>';
exit();