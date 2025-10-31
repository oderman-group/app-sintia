<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0051';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(empty($_POST["nombre"])){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="mps-categorias-servicios-agregar.php?error=ER_DT_4";</script>';
        exit();
    }

    // Migrado a PDO - Consulta preparada
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "INSERT INTO " . $baseDatosMarketPlace . ".servicios_categorias(svcat_nombre, svcat_icon) VALUES (?, ?)";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["icon"], PDO::PARAM_STR);
        $stmt->execute();
        $idRegistro = $conexionPDO->lastInsertId();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="mps-categorias-servicios.php?success=SC_DT_1&id='.base64_encode($idRegistro).'";</script>';
    exit();