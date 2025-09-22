<?php
include("session-compartida.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0016';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
$usuariosClase = new UsuariosFunciones;

$idInsercion=Utilidades::generateCode("MATA");

try {
    $positivos   = Utilidades::sanitizarTexto($_POST["positivos"] ?? '');
    $mejorar     = Utilidades::sanitizarTexto($_POST["mejorar"] ?? '');
    $tratamiento = Utilidades::sanitizarTexto($_POST["tratamiento"] ?? '');
    $descripcion = Utilidades::sanitizarTexto($_POST["descripcion"] ?? '');

    mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".matriculas_aspectos(mata_id, mata_estudiante, mata_usuario, mata_fecha_evento, mata_aspectos_positivos, mata_aspectos_mejorar, mata_tratamiento, mata_descripcion, mata_periodo, institucion, year)VALUES('" .$idInsercion . "', '" . $_POST["estudiante"] . "', '" . $_SESSION['id'] . "', '" . $_POST["fecha"] . "', '" . $positivos . "', '" . $mejorar . "', '" . $tratamiento . "', '" . $descripcion . "', '" . $_POST["periodo"] . "', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
    $idRegistro = mysqli_insert_id($conexion);
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$url= $usuariosClase->verificarTipoUsuario($datosUsuarioActual['uss_tipo'],'aspectos-estudiantiles.php?idR='.$_POST["idR"]."&success=SC_DT_1&id=".base64_encode($idRegistro));

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
exit();