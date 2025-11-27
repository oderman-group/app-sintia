<?php
include("../directivo/session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0003';
include("../compartido/historial-acciones-guardar.php");

    // Migrado a PDO - Consultas preparadas
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sqlCheck = "SELECT * FROM ".$baseDatosServicios.".contratos_usuarios 
                     WHERE cxu_id_contrato=? AND cxu_id_institucion=?";
        $stmtCheck = $conexionPDO->prepare($sqlCheck);
        $stmtCheck->bindParam(1, $_POST["id"], PDO::PARAM_STR);
        $stmtCheck->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmtCheck->execute();
        $numDatos = $stmtCheck->rowCount();

        if($numDatos==0){
            $sqlInsert = "INSERT INTO ".$baseDatosServicios.".contratos_usuarios(
                cxu_id_usuario, cxu_id_contrato, cxu_fecha_aceptacion, cxu_id_institucion
            ) VALUES (?, ?, now(), ?)";
            $stmtInsert = $conexionPDO->prepare($sqlInsert);
            $stmtInsert->bindParam(1, $_POST["idUsuario"], PDO::PARAM_STR);
            $stmtInsert->bindParam(2, $_POST["id"], PDO::PARAM_STR);
            $stmtInsert->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtInsert->execute();
        }else{
            $sqlUpdate = "UPDATE ".$baseDatosServicios.".contratos_usuarios 
                          SET cxu_fecha_aceptacion=now() 
                          WHERE cxu_id_contrato=? AND cxu_id_institucion=?";
            $stmtUpdate = $conexionPDO->prepare($sqlUpdate);
            $stmtUpdate->bindParam(1, $_POST["id"], PDO::PARAM_STR);
            $stmtUpdate->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtUpdate->execute();
        }
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }

	include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
    exit();