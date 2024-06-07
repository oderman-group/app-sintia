<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0175';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

UsuariosPadre::actualizarUsuariosEstudiantesDocumento($config);

	include("../compartido/guardar-historial-acciones.php");

	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();