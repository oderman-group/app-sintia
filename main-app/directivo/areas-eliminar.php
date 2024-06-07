<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0150';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

Areas::eliminarAreasID($config, base64_decode($_GET["id"]));

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="areas.php?error=ER_DT_3";</script>';
exit();