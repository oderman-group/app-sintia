<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0028';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(trim($_POST["titulo"])=="" || trim($_POST["descripcion"])==""){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="dev-terminos.php?error=ER_DT_4";</script>';
        exit();
    }
    
    // Migrado a PDO - Consulta preparada
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "UPDATE ".$baseDatosServicios.".terminos_tratamiento_politica 
                SET ttp_nombre=?, ttp_descripcion=?, ttp_fecha_modificacion=now(), ttp_visible=? 
                WHERE ttp_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["visible"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST['id'], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-terminos.php?success=SC_DT_2&id='.base64_encode($_POST['id']).'";</script>';
    exit();