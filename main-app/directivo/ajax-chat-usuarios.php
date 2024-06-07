<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
include("../compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
//Instancia de Clases generales
$usuariosClase = new Usuarios();

$consultaUsuariosOnline = UsuariosPadre::consultaUsuariosOnline($config, $_SESSION['id']);
$consultaUsuariosOffline = UsuariosPadre::consultaUsuariosOffline($config, $_SESSION['id']);

$resultadosOnline = array();
$resultadosOfline = array();

while ($datosUsuarios = mysqli_fetch_array($consultaUsuariosOnline, MYSQLI_BOTH)) {
    $fotoPerfil = $usuariosClase->verificarFoto($datosUsuarios['uss_foto']);

    $nombre = $datosUsuarios['uss_nombre'];
    if ($datosUsuarios['uss_apellido1'] != "" || $datosUsuarios['uss_apellido1'] != NULL) {
        $nombre .= " ".$datosUsuarios['uss_apellido1'];
    }

    $resultadosOnline[] = array(
        'datosUsuarios' => $datosUsuarios,
        'nombre'        => $nombre,
        'fotoPerfil'    => $fotoPerfil
    );
}
while ($datosUsuarios = mysqli_fetch_array($consultaUsuariosOffline, MYSQLI_BOTH)) {
    $fotoPerfil = $usuariosClase->verificarFoto($datosUsuarios['uss_foto']);

    $nombre = $datosUsuarios['uss_nombre'];
    if ($datosUsuarios['uss_apellido1'] != "" || $datosUsuarios['uss_apellido1'] != NULL) {
        $nombre .= " ".$datosUsuarios['uss_apellido1'];
    }

    $resultadosOfline[] = array(
        'datosUsuarios' => $datosUsuarios,
        'nombre'        => $nombre,
        'fotoPerfil'    => $fotoPerfil
    );
}

$resultados = array_merge($resultadosOnline, $resultadosOfline);
// Devolver los resultados como JSON
header('Content-Type: application/json');
echo json_encode($resultados);
exit();