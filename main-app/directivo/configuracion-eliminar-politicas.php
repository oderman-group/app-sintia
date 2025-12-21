<?php 
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0016';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Migrado a PDO - Consulta preparada
try {
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idConfig = base64_decode($_GET["id"]);
	$sql = "UPDATE ".$baseDatosAdmisiones.".config_instituciones SET cfgi_politicas_adjunto='' WHERE cfgi_id=?";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $idConfig, PDO::PARAM_STR);
	$stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

$nombreArchivo= '../files/imagenes-generales/'.base64_decode($_GET["archivo"]);
if(file_exists($nombreArchivo)){
	unlink($nombreArchivo);
}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="configuracion-admisiones.php";</script>';
exit();