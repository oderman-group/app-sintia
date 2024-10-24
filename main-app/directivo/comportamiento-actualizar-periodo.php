<?php
include("session.php");
$idPaginaInterna = 'DT0344';

include("../compartido/historial-acciones-guardar.php");
Modulos::validarAccesoDirectoPaginas();

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

require_once("../class/Disciplina.php");

Disciplina::actualizarPeriodoComportamiento($config, $_REQUEST['id'], $_REQUEST['periodo']);	

include("../compartido/guardar-historial-acciones.php");
exit();
