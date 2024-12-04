<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0063';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/SubRoles.php';

$config = RedisInstance::getSystemConfiguration(true);

$informacion_inst = Instituciones::getGeneralInformationFromInstitution($config['conf_id_institucion'], $_SESSION["bd"]);
$_SESSION["informacionInstConsulta"] = $informacion_inst;

$datosUnicosInstitucionConsulta = Instituciones::getDataInstitution($config['conf_id_institucion']);
$datosUnicosInstitucion         = mysqli_fetch_array($datosUnicosInstitucionConsulta, MYSQLI_BOTH);
$_SESSION["datosUnicosInstitucion"]           = $datosUnicosInstitucion;
$_SESSION["datosUnicosInstitucion"]["config"] = $config;

$_SESSION["modulos"] = RedisInstance::getModulesInstitution(true);

$rst_usr = UsuariosPadre::obtenerTodosLosDatosDeUsuarios(" AND uss_id='".$_SESSION["id"]."'");
$fila = mysqli_fetch_array($rst_usr, MYSQLI_BOTH);
$_SESSION["datosUsuario"] = $fila;

$infoRolesUsuario = Administrativo_Usuario_SubRoles::getInfoRolesFromUser($_SESSION["id"], $config['conf_id_institucion']);

$_SESSION["datosUsuario"]["sub_roles"]         = $infoRolesUsuario['datos_sub_roles_usuario'];
$_SESSION["datosUsuario"]["sub_roles_paginas"] = $infoRolesUsuario['valores_paginas'];

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
exit();