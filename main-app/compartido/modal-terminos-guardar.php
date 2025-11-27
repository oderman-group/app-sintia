<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0004';
include("../compartido/historial-acciones-guardar.php");

    // Migrado a PDO - Consultas preparadas
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sqlCheck = "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios 
                     WHERE ttpxu_id_termino_tratamiento_politicas=? AND ttpxu_id_usuario=? AND ttpxu_id_institucion=?";
        $stmtCheck = $conexionPDO->prepare($sqlCheck);
        $stmtCheck->bindParam(1, $_POST["id"], PDO::PARAM_STR);
        $stmtCheck->bindParam(2, $_POST["idUsuario"], PDO::PARAM_STR);
        $stmtCheck->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtCheck->execute();
        $numDatos = $stmtCheck->rowCount();

        if($numDatos==0){
            $sqlInsert = "INSERT INTO ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios(
                ttpxu_id_termino_tratamiento_politicas, ttpxu_id_usuario, ttpxu_id_institucion, ttpxu_fecha_aceptacion
            ) VALUES (?, ?, ?, now())";
            $stmtInsert = $conexionPDO->prepare($sqlInsert);
            $stmtInsert->bindParam(1, $_POST["id"], PDO::PARAM_STR);
            $stmtInsert->bindParam(2, $_POST["idUsuario"], PDO::PARAM_STR);
            $stmtInsert->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtInsert->execute();
        }else{
            $sqlUpdate = "UPDATE ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios 
                          SET ttpxu_fecha_aceptacion=now() 
                          WHERE ttpxu_id_termino_tratamiento_politicas=? AND ttpxu_id_usuario=? AND ttpxu_id_institucion=?";
            $stmtUpdate = $conexionPDO->prepare($sqlUpdate);
            $stmtUpdate->bindParam(1, $_POST["id"], PDO::PARAM_STR);
            $stmtUpdate->bindParam(2, $_POST["idUsuario"], PDO::PARAM_STR);
            $stmtUpdate->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtUpdate->execute();
        }
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }

	include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
    exit();