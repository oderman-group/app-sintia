<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0158';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

$update = "gra_estado=0";
Grados::actualizarCursos($config, base64_decode($_GET["id"]), $update);

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="cursos.php?error=ER_DT_3";</script>';
exit();