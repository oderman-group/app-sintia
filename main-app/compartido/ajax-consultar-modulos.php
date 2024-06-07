<?php
include("session-compartida.php");

$idPaginaInterna = 'CM0062';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

    try {
        $consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".modulos WHERE mod_id='".$_REQUEST['idModulo']."'");
    } catch(Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

    $imgModulo = !empty($resultado['mod_imagen']) && file_exists("../files/modulos/".$resultado['mod_imagen']) ? "../files/modulos/".$resultado['mod_imagen'] : "../files/modulos/default.png";
    $descripcionModulo = !empty($resultado['mod_description']) ? $resultado['mod_description'] : "";

    try {
        $consultaTipoUsuario = mysqli_query($conexion, "SELECT pes_nombre FROM ".BD_ADMIN.".general_perfiles WHERE pes_id='".$datosUsuarioActual['uss_tipo']."'");
    } catch(Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $tipoUsuario = mysqli_fetch_array($consultaTipoUsuario, MYSQLI_BOTH);

    $mensaje = "Hola, mi nombre es ".UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual).", soy un ".$tipoUsuario['pes_nombre']." de la compañía ".$informacion_inst["info_nombre"].", me gustaría recibir más información sobre el módulo ".strtoupper($resultado['mod_nombre']);

    $arrayEstado=[
        "nombreModulo"          =>      strtoupper($resultado['mod_nombre']),
        "imgModulo"             =>      $imgModulo,
        "descripcionModulo"     =>      $descripcionModulo,
        "mensaje"               =>      $mensaje,
        "montoModulo"           =>      $resultado['mod_precio']
    ];
    
    header('Content-Type: application/json');
    echo json_encode($arrayEstado);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit;