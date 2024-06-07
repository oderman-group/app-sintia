<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0087';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo 2;
	exit();
}

if (base64_decode($_GET["lock"]) == 1) $estado = 0;
else $estado = 1;

$update = "uss_bloqueado='" . $estado . "'";
UsuariosPadre::actualizarUsuarios($config, base64_decode($_GET["idR"]), $update);

include("../compartido/guardar-historial-acciones.php");
echo $estado;