<?php
include("session-compartida.php");

$idPaginaInterna = 'CM0062';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

    // Migrado a PDO - Consultas preparadas
    try {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sql = "SELECT * FROM ".BD_ADMIN.".modulos WHERE mod_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_REQUEST['idModulo'], PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $imgModulo = !empty($resultado['mod_imagen']) && file_exists("../files/modulos/".$resultado['mod_imagen']) ? "../files/modulos/".$resultado['mod_imagen'] : "../files/modulos/default.png";
        $descripcionModulo = !empty($resultado['mod_description']) ? $resultado['mod_description'] : "";

        $sqlTipo = "SELECT pes_nombre FROM ".BD_ADMIN.".general_perfiles WHERE pes_id=?";
        $stmtTipo = $conexionPDO->prepare($sqlTipo);
        $stmtTipo->bindParam(1, $datosUsuarioActual['uss_tipo'], PDO::PARAM_INT);
        $stmtTipo->execute();
        $tipoUsuario = $stmtTipo->fetch(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }

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