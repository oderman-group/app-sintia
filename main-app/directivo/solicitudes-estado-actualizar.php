<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0130';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

if( $_GET["estado"] == 3 ) {
    $update = "uss_bloqueado=0";
    UsuariosPadre::actualizarUsuarios($config, $_GET["idUsuario"], $update);
}

try{
    mysqli_query($conexion, "UPDATE ".BD_GENERAL.".general_solicitudes SET soli_estado='" . $_GET["estado"] . "' 
    WHERE soli_id='" . $_GET["idRegistro"] . "'");
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
}

exit();