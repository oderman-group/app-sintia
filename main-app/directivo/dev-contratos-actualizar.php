<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0024';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(trim($_POST["titulo"])=="" || trim($_POST["descripcion"])==""){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="dev-contratos.php?error=ER_DT_4";</script>';
        exit();
    }
    
    // Migrado a PDO - Consulta preparada
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "UPDATE ".$baseDatosServicios.".contratos 
                SET cont_nombre=?, cont_descripcion=?, cont_fecha_modificacion=now(), cont_visible=? 
                WHERE cont_id=1";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["visible"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-contratos.php?success=SC_DT_2&id='.base64_encode(1).'";</script>';
    exit();