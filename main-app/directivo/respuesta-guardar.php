<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Respuesta.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0310';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

if (empty($_POST["descripcion"])) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="respuesta-agregar.php?error=ER_DT_4";</script>';
    exit();
}

$idInsercion = Respuesta::guardarRespuestas($conexion, $config, $_POST);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="respuesta-editar.php?success=SC_DT_1&id='.base64_encode($idInsercion).'";</script>';
exit();