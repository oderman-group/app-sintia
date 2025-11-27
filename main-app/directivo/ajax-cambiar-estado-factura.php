<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0301';
require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

    $estado = $_REQUEST['estado'] == 1 ? COBRADA : POR_COBRAR;

    // Migrado a PDO - Consulta preparada
    try {
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas SET fcu_status=? 
                WHERE fcu_id=? AND institucion=? AND year=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $estado, PDO::PARAM_STR);
        $stmt->bindParam(2, $_REQUEST['idFactura'], PDO::PARAM_STR);
        $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
    } catch(Exception $e) {
        echo $e->getMessage();
        exit();
    }

    $arrayEstado=["estado"=>$estado];
    
    header('Content-Type: application/json');
    echo json_encode($arrayEstado);

require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
exit;