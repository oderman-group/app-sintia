<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0051';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$archivoSubido = new Archivos;
$usuariosClase = new Usuarios;

if (!empty($_FILES['archivo']['name'])) {
    $archivoSubido->validarArchivo($_FILES['archivo']['size'], $_FILES['archivo']['name']);
    $explode=explode(".", $_FILES['archivo']['name']);
    $extension = end($explode);
    $archivo = uniqid($_SESSION["inst"] . '_' . $_SESSION["id"] . '_pagom_') . "." . $extension;
    $destino = "../files/comprobantes";
    move_uploaded_file($_FILES['archivo']['tmp_name'], $destino . "/" . $archivo);
}

$update = "mat_soporte_pago=".$archivo."";
Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $update);

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'matricula.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();