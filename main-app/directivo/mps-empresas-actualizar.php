<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0059';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(empty($_POST["nombre"]) || empty($_POST["email"]) || empty($_POST["telefono"]) || empty($_POST["sector"]) || empty($_POST["institucion"])){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="mps-empresas-aditar.php?error=ER_DT_4&idR='.base64_encode($_POST["idR"]).'";</script>';
        exit();
    }
    
	if (!empty($_FILES['logoEmp']['name'])) {
        $explode = explode(".", $_FILES['logoEmp']['name']);
        $extension = end($explode);
        $archivo = uniqid('logo_'.date('Ymd')) . "." . $extension;
        $destino = "../files/marketplace/logos";
        move_uploaded_file($_FILES['logoEmp']['tmp_name'], $destino . "/" . $archivo);

        // Migrado a PDO - Consulta preparada para logo
        try{
            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            $conexionPDO = Conexion::newConnection('PDO');
            $sql = "UPDATE " . $baseDatosMarketPlace . ".empresas SET emp_logo=? WHERE emp_id=?";
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $archivo, PDO::PARAM_STR);
            $stmt->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
	}

    $responsable=''; if(!empty($_POST["responsable"])){ $responsable=$_POST["responsable"]; }

    // Migrado a PDO - Consulta preparada principal
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sql = "UPDATE " . $baseDatosMarketPlace . ".empresas SET 
                emp_nombre=?, emp_email=?, emp_telefono=?, emp_web=?, emp_usuario=?, emp_institucion=?
                WHERE emp_id=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["email"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["telefono"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["web"], PDO::PARAM_STR);
        $stmt->bindParam(5, $responsable, PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST["institucion"], PDO::PARAM_STR);
        $stmt->bindParam(7, $_POST["idR"], PDO::PARAM_STR);
        $stmt->execute();

        if(!empty($_POST["sector"])){
            $sqlCat = "SELECT excat_categoria FROM ".$baseDatosMarketPlace.".empresas_categorias WHERE excat_empresa=?";
            $stmtCat = $conexionPDO->prepare($sqlCat);
            $stmtCat->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
            $stmtCat->execute();
            $catSector = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
            $idCategoriasSector = array_column($catSector, 'excat_categoria');

            $resultadoAgregar= array_diff($_POST["sector"],$idCategoriasSector);
            if(!empty($resultadoAgregar)){
                $sqlInsert = "INSERT INTO ".$baseDatosMarketPlace.".empresas_categorias(excat_empresa, excat_categoria) VALUES (?, ?)";
                $stmtInsert = $conexionPDO->prepare($sqlInsert);
                foreach ($resultadoAgregar as $idSectorGuardar) {
                    $stmtInsert->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
                    $stmtInsert->bindParam(2, $idSectorGuardar, PDO::PARAM_STR);
                    $stmtInsert->execute();
                }
            }

            $resultadoEliminar= array_diff($idCategoriasSector,$_POST["sector"]);
            if(!empty($resultadoEliminar)){
                $sqlDelete = "DELETE FROM ".$baseDatosMarketPlace.".empresas_categorias WHERE excat_categoria=? AND excat_empresa=?";
                $stmtDelete = $conexionPDO->prepare($sqlDelete);
                foreach ($resultadoEliminar as $idSectorEliminar) {
                    $stmtDelete->bindParam(1, $idSectorEliminar, PDO::PARAM_STR);
                    $stmtDelete->bindParam(2, $_POST["idR"], PDO::PARAM_STR);
                    $stmtDelete->execute();
                }
            }
        }else{
            $sqlDeleteAll = "DELETE FROM ".$baseDatosMarketPlace.".empresas_categorias WHERE excat_empresa=?";
            $stmtDeleteAll = $conexionPDO->prepare($sqlDeleteAll);
            $stmtDeleteAll->bindParam(1, $_POST["idR"], PDO::PARAM_STR);
            $stmtDeleteAll->execute();
        }
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="mps-empresas.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'";</script>';
    exit();