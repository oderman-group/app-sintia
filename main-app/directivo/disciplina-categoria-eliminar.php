<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0159';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consultas preparadas
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idCategoria = base64_decode($_GET["id"]);
	
	// Eliminar faltas asociadas
	$sql1 = "DELETE FROM ".BD_DISCIPLINA.".disciplina_faltas 
	         WHERE dfal_id_categoria=? AND dfal_institucion=? AND dfal_year=?";
	$stmt1 = $conexionPDO->prepare($sql1);
	$stmt1->bindParam(1, $idCategoria, PDO::PARAM_STR);
	$stmt1->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt1->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt1->execute();
	
	// Eliminar categoría
	$sql2 = "DELETE FROM ".BD_DISCIPLINA.".disciplina_categorias 
	         WHERE dcat_id=? AND dcat_institucion=? AND dcat_year=?";
	$stmt2 = $conexionPDO->prepare($sql2);
	$stmt2->bindParam(1, $idCategoria, PDO::PARAM_STR);
	$stmt2->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt2->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt2->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="disciplina-categorias.php?error=ER_DT_3";</script>';
exit();