<?php
include("session.php");
require_once("../class/UsuariosPadre.php");

$idPaginaInterna = 'DC0065';

$_SESSION['docente'] = $_SESSION['id'];

$_SESSION['id'] = base64_decode($_GET['user']);

$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

include("../compartido/guardar-historial-acciones.php");

$url = '../estudiante/index.php';

header("Location:".$url);