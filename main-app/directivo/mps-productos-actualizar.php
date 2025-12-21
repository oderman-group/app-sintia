<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0065';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(empty($_POST["nombre"]) || empty($_POST["descripcion"]) || $_POST["precio"]=='' || empty($_POST["categoria"]) || empty($_POST["empresa"])){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="mps-productos-aditar.php?error=ER_DT_4&idR='.base64_encode($_POST["idR"]).'";</script>';
        exit();
    }
    
	if (!empty($_FILES['imagen']['name'])) {
        $explode = explode(".", $_FILES['imagen']['name']);
        $extension = end($explode);
        $archivo = uniqid($_POST["empresa"] . '_prod_') . "." . $extension;
        $destino = "../files/marketplace/productos";
        move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $archivo);

        // Migrado a PDO - Consulta preparada para imagen
        try{
            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            $conexionPDO = Conexion::newConnection('PDO');
            $sql = "UPDATE " . $baseDatosMarketPlace . ".productos SET prod_foto=? WHERE prod_id=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $archivo, PDO::PARAM_STR);
            $stmt->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
	}

    $findme   = '?v=';
    $pos = strpos($_POST["video"], $findme) + 3;
    $video = substr($_POST["video"], $pos, 11);

    // Migrado a PDO - Consulta preparada principal
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "UPDATE " . $baseDatosMarketPlace . ".productos SET 
                prod_nombre=?, prod_descripcion=?, prod_existencias=?, prod_precio=?, 
                prod_empresa=?, prod_video=?, prod_keywords=?, prod_categoria=?, prod_activo=?
                WHERE prod_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["existencia"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["precio"], PDO::PARAM_STR);
        $stmt->bindParam(5, $_POST["empresa"], PDO::PARAM_STR);
        $stmt->bindParam(6, $video, PDO::PARAM_STR);
        $stmt->bindParam(7, $_POST["keyw"], PDO::PARAM_STR);
        $stmt->bindParam(8, $_POST["categoria"], PDO::PARAM_STR);
        $stmt->bindParam(9, $_POST["estado"], PDO::PARAM_STR);
        $stmt->bindParam(10, $_POST["idR"], PDO::PARAM_STR);
        $stmt->execute();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="mps-productos.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'";</script>';
    exit();