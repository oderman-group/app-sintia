<?php
include("session-compartida.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0015';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
$usuariosClase = new Usuarios;
$idCom = !empty($_GET['idCom']) ? base64_decode($_GET['idCom']) : "";

Clases::eliminarPreguntasClases($conexion, $config, $idCom);

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'clases-ver.php?idR='.$_GET["idR"]);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();