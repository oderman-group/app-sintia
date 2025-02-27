<?php
session_start();

if (!empty($_SESSION["admin"])) {
    $_SESSION['id'] = $_SESSION['admin'];
    $_SESSION['admin'] = '';
    unset($_SESSION["admin"]);
}

if (!empty($_SESSION["infoCargaActual"])) {
    $_SESSION["infoCargaActual"] = '';
    unset($_SESSION["infoCargaActual"]);
}

if (empty($_SESSION["id"])) {
    header("Location:../controlador/salir.php");
    exit();
}

require_once("../../config-general/config.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");

$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

$infoRolesUsuario = Administrativo_Usuario_SubRoles::getInfoRolesFromUser($_SESSION["id"], $config['conf_id_institucion']);
$_SESSION["datosUsuario"]["sub_roles"]         = $infoRolesUsuario['datos_sub_roles_usuario'];
$_SESSION["datosUsuario"]["sub_roles_paginas"] = $infoRolesUsuario['valores_paginas'];

$auth = Autenticate::getInstance();
$auth->limpiarCookiesDocentes();
$auth->limpiarCookiesEstudiantes();

header("Location:../directivo/usuarios.php?tipo=".$_GET['tipo']);