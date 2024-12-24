<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Autenticate.php");

$idPaginaInterna = 'DC0065';

$_SESSION['docente'] = $_SESSION['id'];

$_SESSION['id'] = base64_decode($_GET['user']);

$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

include("../compartido/guardar-historial-acciones.php");

$url = '../estudiante/index.php';

$auth = Autenticate::getInstance();
$auth->limpiarCookiesEstudiantes();

header("Location:".$url);