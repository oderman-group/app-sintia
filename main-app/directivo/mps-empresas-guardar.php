<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0057';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(empty($_POST["nombre"]) || empty($_POST["email"]) || empty($_POST["telefono"]) || empty($_POST["sector"]) || empty($_POST["institucion"])){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="mps-empresas-agregar.php?error=ER_DT_4";</script>';
        exit();
    }

    $responsable=''; if(!empty($_POST["responsable"])){ $responsable=$_POST["responsable"]; }

    // Migrado a PDO - Consultas preparadas
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sql = "INSERT INTO " . $baseDatosMarketPlace . ".empresas(
            emp_nombre, emp_email, emp_telefono, emp_web, emp_usuario, emp_institucion
        ) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["email"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["telefono"], PDO::PARAM_STR);
        $stmt->bindParam(4, $_POST["web"], PDO::PARAM_STR);
        $stmt->bindParam(5, $responsable, PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST["institucion"], PDO::PARAM_STR);
        $stmt->execute();
        $idRegistro = $conexionPDO->lastInsertId();

        if(!empty($_POST["sector"])){
            $sqlCat = "INSERT INTO " . $baseDatosMarketPlace . ".empresas_categorias(excat_empresa, excat_categoria) VALUES (?, ?)";
            $stmtCat = $conexionPDO->prepare($sqlCat);
            
            foreach ($_POST["sector"] as $sector) {
                $stmtCat->bindParam(1, $idRegistro, PDO::PARAM_INT);
                $stmtCat->bindParam(2, $sector, PDO::PARAM_STR);
                $stmtCat->execute();
            }
        }
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="mps-empresas.php?success=SC_DT_1&id='.base64_encode($idRegistro).'";</script>';
    exit();