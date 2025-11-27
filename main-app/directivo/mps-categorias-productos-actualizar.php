<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0047';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(empty($_POST["nombre"])){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="mps-categorias-productos-aditar.php?error=ER_DT_4&idR='.base64_encode($_POST["idR"]).'";</script>';
        exit();
    }

    // Migrado a PDO - Consulta preparada
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "UPDATE " . $baseDatosMarketPlace . ".categorias_productos SET catp_nombre=? WHERE catp_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="mps-categorias-productos.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'";</script>';
    exit();