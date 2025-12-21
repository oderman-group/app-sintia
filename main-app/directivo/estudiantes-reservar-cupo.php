<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0219';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

// Migrado a PDO - Consulta preparada con ON DUPLICATE KEY
try{
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idEstudiante = base64_decode($_GET["idEstudiante"]);
	$comentario = 'Reservado por un directivo ('.UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual).').';
	$sql = "INSERT INTO ".$baseDatosServicios.".general_encuestas(
	    genc_estudiante, genc_fecha, genc_respuesta, genc_comentario, genc_institucion, genc_year
	) VALUES (?, now(), 1, ?, ?, ?) 
	ON DUPLICATE KEY UPDATE
	    genc_fecha = VALUES(genc_fecha),
	    genc_comentario = VALUES(genc_comentario),
	    genc_respuesta = VALUES(genc_respuesta)";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $idEstudiante, PDO::PARAM_STR);
	$stmt->bindParam(2, $comentario, PDO::PARAM_STR);
	$stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
exit();