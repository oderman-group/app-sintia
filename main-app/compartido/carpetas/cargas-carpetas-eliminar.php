<?php
include("../session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0025';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
$usuariosClase = new UsuariosFunciones;
$archivoSubido = new Archivos;

try{
    mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_folders SET fold_estado='0', fold_fecha_eliminacion=now() WHERE fold_padre='" . base64_decode($_GET["idR"]) . "'");
    mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".general_folders SET fold_estado='0', fold_fecha_eliminacion=now() WHERE fold_id='" . base64_decode($_GET["idR"]) . "'");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'cargas-carpetas.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();