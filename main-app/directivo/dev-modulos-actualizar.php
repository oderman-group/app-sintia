<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0013';
include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(trim($_POST["nombreModulo"])==""){
        echo '<script type="text/javascript">window.location.href="dev-modulos-editar.php?error=ER_DT_4";</script>';
        exit();
    }

    if (!empty($_FILES['portada']['name'])) {
        $destino = ROOT_PATH.'/main-app/files/modulos';
        $explode = explode(".", $_FILES['portada']['name']);
        $extension = end($explode);
        $portada= uniqid('mod_') . "." . $extension;
        @unlink($destino . "/" . $portada);
        move_uploaded_file($_FILES['portada']['tmp_name'], $destino . "/" . $portada);

        // Migrado a PDO - Consulta preparada para portada
        try {
            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            $conexionPDO = Conexion::newConnection('PDO');
            $sql = "UPDATE ".BD_ADMIN.".modulos SET mod_imagen=? WHERE mod_id=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $portada, PDO::PARAM_STR);
            $stmt->bindParam(2, $_POST["id"], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
    }

    $clientes = "";
    if (!empty($_POST["clientes"])) {
        $clientes = implode(",", $_POST["clientes"]);
    }

    // Migrado a PDO - Consulta preparada principal
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "UPDATE ".BD_ADMIN.".modulos SET 
                mod_nombre=?, mod_estado=?, mod_padre=?, mod_namespace=?, 
                mod_description=?, mod_precio=?, mod_order=?, mod_types_customer=?
                WHERE mod_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombreModulo"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["moduloEstado"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["moduloPadre"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["namespace"], PDO::PARAM_STR);
        $stmt->bindParam(5, $_POST["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST["precio"], PDO::PARAM_STR);
        $stmt->bindParam(7, $_POST["order"], PDO::PARAM_STR);
        $stmt->bindParam(8, $clientes, PDO::PARAM_STR);
        $stmt->bindParam(9, $_POST["id"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
	include("../compartido/guardar-historial-acciones.php");

    echo '<script type="text/javascript">window.location.href="dev-modulos.php?success=SC_DT_2&id='.base64_encode($_POST["id"]).'";</script>';
	exit();