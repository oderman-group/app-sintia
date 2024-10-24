<?php
include("session.php");
$idPaginaInterna = 'DT0345';

include("../compartido/historial-acciones-guardar.php");
Modulos::validarAccesoDirectoPaginas();

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once("../class/Disciplina.php");

$idNuevo = base64_decode($_REQUEST['id']);
Disciplina::eliminarComportamiento($config, $idNuevo);	

include("../compartido/guardar-historial-acciones.php");
exit();
