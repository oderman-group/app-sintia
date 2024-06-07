<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$update = "mat_tipo=128";
Estudiantes::actualizarMatriculasInstitucion($config, $update);

echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
exit();