<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
include("../../config-general/config.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Modulos.php");

$_SESSION['id']       = $_SESSION['devAdmin'];
$_SESSION['admin']    = '';
$_SESSION['devAdmin'] = '';

$idInstitucion  = DEVELOPER;
$instInstitucion       = 'dev';
if(ENVIROMENT == 'PROD') {
    $idInstitucion  = DEVELOPER_PROD;
    $instInstitucion       = 'mobiliar_test_enuar';
}

$_SESSION["idInstitucion"] = $idInstitucion;
$_SESSION["inst"]          = $instInstitucion;
$_SESSION["bd"]            = date("Y");

unset( $_SESSION["admin"] );
unset( $_SESSION["devAdmin"] );

$config = Plataforma::sesionConfiguracion();
$_SESSION["configuracion"] = $config;

$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

$datosUnicosInstitucionConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".instituciones 
WHERE ins_id='".$idInstitucion."' AND ins_enviroment='".ENVIROMENT."'");
$datosUnicosInstitucion = mysqli_fetch_array($datosUnicosInstitucionConsulta, MYSQLI_BOTH);
$_SESSION["datosUnicosInstitucion"] = $datosUnicosInstitucion;

    
$arregloModulos = Modulos::consultarModulosIntitucion($conexion, $_SESSION["idInstitucion"]);
$_SESSION["modulos"] = $arregloModulos;

$informacionInstConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_informacion WHERE info_institucion='" . $_SESSION["idInstitucion"] . "' AND info_year='" . $_SESSION["bd"] . "'");
$informacion_inst = mysqli_fetch_array($informacionInstConsulta, MYSQLI_BOTH);
$_SESSION["informacionInstConsulta"] = $informacion_inst;

header("Location:../directivo/dev-instituciones.php");