<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0055';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$archivoSubido = new Archivos;
$usuariosClase = new UsuariosFunciones;

$update = ['mat_compromiso_convivencia' => 1];
Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $update);

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'matricula.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();