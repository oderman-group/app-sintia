<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0148';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

CargaAcademica::eliminarCargaPorID($config, base64_decode($_GET["id"]));

$contenidoMsg = '
<p>Se eliminó una carga académica. A continuación relacionamos la información:</p>
<p>
	<b>ID carga:</b> '.base64_decode($_GET["id"]).'<br>
	<b>Institucion:</b> '.$config['conf_id_institucion'].'<br>
	<b>Año:</b> '.$_SESSION["bd"].'<br>
	<b>Responsable:</b> '.$_SESSION["id"].' - '.UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual).'
</p>
';

// Migrado a PDO - Consulta preparada
try {
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$idCarga = base64_decode($_GET["id"]);
	$sql = "INSERT INTO ".BD_ADMIN.".seguridad_historial_registros_borrados(
	    hrb_id_institucion, hrb_year, hrb_id_registro, hrb_responsable, hrb_referencia
	) VALUES (?, ?, ?, ?, 'CARGA_ACADEMICA')";
	$stmt = $conexionPDO->prepare($sql);
	$stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
	$stmt->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
	$stmt->bindParam(3, $idCarga, PDO::PARAM_STR);
	$stmt->bindParam(4, $_SESSION["id"], PDO::PARAM_STR);
	$stmt->execute();
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="cargas.php?success=SC_DT_3&id='.$_GET["id"].'";</script>';
exit();