<?php 
include("session.php"); 

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0152';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idNota = base64_decode($_GET['id']);
	$sql = "DELETE FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_id=? AND institucion=? AND year=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $idNota, PDO::PARAM_STR);
	$stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
	include("../compartido/guardar-historial-acciones.php");
	
	echo '<script type="text/javascript">window.location.href="cargas-comportamiento.php?error=ER_DT_3&periodo='.$_GET["periodo"].'&carga='.$_GET["carga"].'&grado='.$_GET["grado"].'&grupo='.$_GET["grupo"].'";</script>';
	exit();