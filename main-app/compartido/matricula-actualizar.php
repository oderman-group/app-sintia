<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0037';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
$usuariosClase = new Usuarios;

$update = "
    uss_celular=" . $_POST["celular"] . ",
    uss_telefono=" . $_POST["telefono"] . "
";
UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

//Actualizar matricula a los estudiantes
$update = "mat_celular=" . $_POST["celular"] . ", mat_telefono=" . $_POST["telefono"] . ", mat_direccion=" . $_POST["dir"] . ", mat_barrio=" . $_POST["barrio"] . ", mat_estrato=" . $_POST["estrato"] . ", mat_actualizar_datos=1, mat_modalidad_estudio=" . $_POST["modalidad"] . "";
Estudiantes::actualizarMatriculasPorIdUsuario($config, $_SESSION["id"], $update);

//Actualizar datos del acudiente
$update = "uss_email=" . $_POST["emailA"] . ", uss_celular=" . $_POST["celularA"] . ", uss_ocupacion=" . $_POST["ocupacion"] . ", uss_direccion=" . $_POST["dir"] . "";
UsuariosPadre::actualizarUsuarios($config, $_SESSION["id"], $update);

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'matricula.php');

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
exit();