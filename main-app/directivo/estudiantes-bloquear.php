<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

$update = "mat_compromiso=1";
Estudiantes::actualizarMatriculasPorId($config, base64_decode($_GET["id"]), $update);

echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
exit();