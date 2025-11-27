<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
Modulos::verificarPermisoDev();

$idPaginaInterna = 'DV0041';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idOpcion = base64_decode($_GET["idogen"]);
	$sql = "DELETE FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $idOpcion, PDO::PARAM_STR);
	$stmt->execute();
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="configuracion-opciones-generales.php";</script>';
exit();