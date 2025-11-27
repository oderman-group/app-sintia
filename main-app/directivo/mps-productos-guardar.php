<?php
    include("session.php");
    include("../compartido/sintia-funciones.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0063';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(empty($_POST["nombre"]) || empty($_POST["descripcion"]) || $_POST["precio"]=='' || empty($_POST["categoria"]) || empty($_POST["empresa"])){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="mps-productos-agregar.php?error=ER_DT_4";</script>';
        exit();
    }

    if (!empty($_FILES['imagen']['name'])) {
        $explode=explode(".", $_FILES['imagen']['name']);
        $extension = end($explode);
        $foto = uniqid($_POST["empresa"] . '_prod_') . "." . $extension;
        $destino = "../files/marketplace/productos";
        move_uploaded_file($_FILES['imagen']['tmp_name'], $destino . "/" . $foto);
    }

    $findme   = '?v=';
    $pos = strpos($_POST["video"], $findme) + 3;
    $video = substr($_POST["video"], $pos, 11);

    // Migrado a PDO - Consulta preparada
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        $sql = "INSERT INTO " . $baseDatosMarketPlace . ".productos(
            prod_nombre, prod_descripcion, prod_foto, prod_existencias, prod_precio, 
            prod_empresa, prod_video, prod_keywords, prod_categoria
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(3, $foto, PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["existencia"], PDO::PARAM_STR);
        $stmt->bindParam(5, $_POST["precio"], PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST["empresa"], PDO::PARAM_STR);
        $stmt->bindParam(7, $video, PDO::PARAM_STR);
        $stmt->bindParam(8, $_POST["keyw"], PDO::PARAM_STR);
        $stmt->bindParam(9, $_POST["categoria"], PDO::PARAM_STR);
        $stmt->execute();
        $idRegistro = $conexionPDO->lastInsertId();
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="mps-productos.php?success=SC_DT_1&id='.base64_encode($idRegistro).'";</script>';
    exit();