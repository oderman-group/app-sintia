<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");

// Restaurar ID del DEV admin original
$devAdminId = $_SESSION['devAdmin'] ?? null;
if (empty($devAdminId)) {
	// fallback: algunos flujos guardan también "admin"
	$devAdminId = $_SESSION['admin'] ?? null;
}

if (empty($devAdminId)) {
	// No hay forma segura de restaurar, forzar logout limpio
	header("Location:../controlador/salir.php?logout=true");
	exit();
}

// Contexto original (si existe) para volver exactamente a donde inició el autologin
$ctxIdInstitucion = $_SESSION['devAdmin_idInstitucion'] ?? null;
$ctxInst          = $_SESSION['devAdmin_inst'] ?? null;
$ctxBd            = $_SESSION['devAdmin_bd'] ?? null;

// Fallback histórico: si no hay contexto, volver a la institución DEV fija
$idInstitucionFallback = (ENVIROMENT == 'PROD') ? DEVELOPER_PROD : DEVELOPER;
$idInstitucionReturn = !empty($ctxIdInstitucion) ? $ctxIdInstitucion : $idInstitucionFallback;

// IMPORTANTÍSIMO:
// Hay que setear estos valores ANTES de incluir config.php, porque config.php -> modelo/conexion.php
// depende de SESSION[inst]/bd y si viene vacío o apunta a otra institución puede destruir sesión.
$_SESSION['id']            = $devAdminId;
$_SESSION["idInstitucion"] = $idInstitucionReturn;
$_SESSION["inst"]          = !empty($ctxInst) ? $ctxInst : 'dev'; // fallback mínimo para evitar sesión zombie.
$_SESSION["bd"]            = !empty($ctxBd) ? $ctxBd : date("Y"); // fallback mínimo.

// Limpiar flags de autologin (después de restaurar el id)
unset($_SESSION['admin']);
unset($_SESSION['devAdmin']);
unset($_SESSION['devAdmin_idInstitucion']);
unset($_SESSION['devAdmin_inst']);
unset($_SESSION['devAdmin_bd']);

include("../../config-general/config.php");
require_once("../class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/RedisInstance.php");

// Cargar institución del retorno y ajustar datosUnicosInstitucion (evita inconsistencias)
$datosUnicosInstitucionConsulta = mysqli_query(
	$conexion,
	"SELECT * FROM ".$baseDatosServicios.".instituciones WHERE ins_id='".$idInstitucionReturn."' AND ins_enviroment='".ENVIROMENT."'"
);
$datosUnicosInstitucion = mysqli_fetch_array($datosUnicosInstitucionConsulta, MYSQLI_BOTH);

if (!empty($datosUnicosInstitucion)) {
	$_SESSION["datosUnicosInstitucion"] = $datosUnicosInstitucion;
}

// Recargar usuario ya con year/institución correctos
$_SESSION["datosUsuario"] = UsuariosPadre::sesionUsuario($_SESSION['id']);

// Cargar módulos e información general del año seleccionado
$_SESSION["modulos"] = RedisInstance::getModulesInstitution();

$informacionInstConsulta = mysqli_query(
	$conexion,
	"SELECT * FROM ".$baseDatosServicios.".general_informacion WHERE info_institucion='" . $_SESSION["idInstitucion"] . "' AND info_year='" . $_SESSION["bd"] . "'"
);
$informacion_inst = mysqli_fetch_array($informacionInstConsulta, MYSQLI_BOTH);
$_SESSION["informacionInstConsulta"] = $informacion_inst;

header("Location:../directivo/dev-instituciones.php");
exit();