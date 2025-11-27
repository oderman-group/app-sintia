<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0160';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idFalta = base64_decode($_GET["id"]);
	$sql = "DELETE FROM ".BD_DISCIPLINA.".disciplina_faltas WHERE dfal_id_nuevo=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $idFalta, PDO::PARAM_STR);
	$stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="disciplina-faltas.php?error=ER_DT_3";</script>';
exit();