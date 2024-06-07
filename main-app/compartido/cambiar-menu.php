<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0013';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");

UsuariosPadre::cambiarTipoMenu($config, $_GET["tipoMenu"]);
$_SESSION["datosUsuario"]["uss_tipo_menu"] = $_GET["tipoMenu"];

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit();