<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0154';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

CargaAcademica::eliminarTiposNotasCategoria($conexion, $config, base64_decode($_GET["idR"]));

categoriasNota::eliminarCategoriaNotaID($config, base64_decode($_GET["idR"]));

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
exit();