<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0089';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

try{
	mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".finanzas_cuentas SET fcu_anulado=1 WHERE fcu_id='" . $_GET["idR"] . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="movimientos.php?usuario=' . base64_encode($_GET["id"]) . '";</script>';
exit();