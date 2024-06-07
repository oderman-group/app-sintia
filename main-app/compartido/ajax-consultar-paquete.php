<?php
include("session-compartida.php");

$idPaginaInterna = 'CM0064';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

    $resultado = Plataforma::traerDatosPlanes($conexion, $_REQUEST['idPaquete']);

    $imgPaquete = !empty($resultado['plns_imagen']) && file_exists("../files/paquetes/".$resultado['plns_imagen']) ? "../files/paquetes/".$resultado['plns_imagen'] : "../files/paquetes/default.png";
    $descripcionPaquete = !empty($resultado['plns_descripcion']) ? $resultado['plns_descripcion'] : "";

    try {
        $consultaTipoUsuario = mysqli_query($conexion, "SELECT pes_nombre FROM ".BD_ADMIN.".general_perfiles WHERE pes_id='".$datosUsuarioActual['uss_tipo']."'");
    } catch(Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $tipoUsuario = mysqli_fetch_array($consultaTipoUsuario, MYSQLI_BOTH);

    $mensaje = "Hola, mi nombre es ".UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual).", soy un ".$tipoUsuario['pes_nombre']." de la compañía ".$informacion_inst["info_nombre"].", me gustaría recibir más información sobre el paquete ".strtoupper($resultado['plns_nombre']);

    $arrayEstado=[
        "nombrePaquete"          =>      strtoupper($resultado['plns_nombre']),
        "imgPaquete"             =>      $imgPaquete,
        "descripcionPaquete"     =>      $descripcionPaquete,
        "mensaje"                =>      $mensaje,
        "montoPaquete"           =>      $resultado['plns_valor'],
        "tipoPaquete"            =>      $resultado['plns_tipo']
    ];
    
    header('Content-Type: application/json');
    echo json_encode($arrayEstado);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit;