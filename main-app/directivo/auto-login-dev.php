<?php
include("session.php");
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");
Modulos::validarAccesoDirectoPaginas();

$idPaginaInterna = 'DV0073';

// Guardar contexto original para poder regresar correctamente al "DEV PANEL"
// (No sobrescribir si ya existe: soporta autologin encadenado)
if (!isset($_SESSION['devAdmin'])) {
	$_SESSION['devAdmin'] = $_SESSION['id'];
}
if (!isset($_SESSION['admin'])) {
	$_SESSION['admin'] = $_SESSION['id'];
}
if (!isset($_SESSION['devAdmin_idInstitucion'])) {
	$_SESSION['devAdmin_idInstitucion'] = $_SESSION["idInstitucion"] ?? null;
}
if (!isset($_SESSION['devAdmin_inst'])) {
	$_SESSION['devAdmin_inst'] = $_SESSION["inst"] ?? null;
}
if (!isset($_SESSION['devAdmin_bd'])) {
	$_SESSION['devAdmin_bd'] = $_SESSION["bd"] ?? null;
}

$_SESSION['id']            = base64_decode($_GET['user']);
$_SESSION["idInstitucion"] = base64_decode($_GET['idInstitucion']);
$_SESSION["inst"]          = base64_decode($_GET['bd']);
$_SESSION["bd"]            = base64_decode($_GET['yearDefault']);

$datosUnicosInstitucionConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones 
WHERE ins_id='".$_SESSION["idInstitucion"]."' AND ins_enviroment='".ENVIROMENT."'");
$datosUnicosInstitucion = mysqli_fetch_array($datosUnicosInstitucionConsulta, MYSQLI_BOTH);
$_SESSION["datosUnicosInstitucion"] = $datosUnicosInstitucion;

$_SESSION["modulos"] = RedisInstance::getModulesInstitution();

$informacionInstConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_informacion WHERE info_institucion='" . $_SESSION["idInstitucion"] . "' AND info_year='" . $_SESSION["bd"] . "'");
$informacion_inst = mysqli_fetch_array($informacionInstConsulta, MYSQLI_BOTH);
$_SESSION["informacionInstConsulta"] = $informacion_inst;

$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

include("../compartido/guardar-historial-acciones.php");

$url = 'index.php';

header("Location:".$url);