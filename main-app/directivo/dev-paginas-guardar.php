<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0018';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(trim($_POST["nombrePagina"])=="" || trim($_POST["codigoPagina"])=="" || trim($_POST["rutaPagina"])==""){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="dev-paginas-agregar.php?error=ER_DT_4";</script>';
        exit();
    }

    //COMPROBAMOS QUE NO EXISTA EL ID O LA RUTA
    // Migrado a PDO - Consultas preparadas
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sql = "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_id=? OR pagp_ruta=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["codigoPagina"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["rutaPagina"], PDO::PARAM_STR);
        $stmt->execute();
        $numIdPaginas = $stmt->rowCount();
        
        if($numIdPaginas>0){
            $datosPaginas = $stmt->fetch(PDO::FETCH_ASSOC);
            include("../compartido/guardar-historial-acciones.php");
            echo '<script type="text/javascript">window.location.href="dev-paginas-agregar.php?error=ER_DT_14&id='.base64_encode($datosPaginas['pagp_id']).'&nombrePagina='.base64_encode($datosPaginas['pagp_pagina']).'";</script>';
            exit();
        }

        $paginaDependencia=!empty($_POST["paginaDependencia"])?implode(',',$_POST["paginaDependencia"]):NULL;

        $sqlInsert = "INSERT INTO ".$baseDatosServicios.".paginas_publicidad(
            pagp_id, pagp_pagina, pagp_tipo_usuario, pagp_modulo, pagp_ruta, pagp_palabras_claves, 
            pagp_navegable, pagp_asignable_subroles, pagp_crud, pagp_pagina_padre, pagp_paginas_dependencia
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conexionPDO->prepare($sqlInsert);
        $stmtInsert->bindParam(1, $_POST["codigoPagina"], PDO::PARAM_STR);
        $stmtInsert->bindParam(2, $_POST["nombrePagina"], PDO::PARAM_STR);
        $stmtInsert->bindParam(3, $_POST["tipoUsuario"], PDO::PARAM_STR);
        $stmtInsert->bindParam(4, $_POST["modulo"], PDO::PARAM_STR);
        $stmtInsert->bindParam(5, $_POST["rutaPagina"], PDO::PARAM_STR);
        $stmtInsert->bindParam(6, $_POST["palabrasClaves"], PDO::PARAM_STR);
        $stmtInsert->bindParam(7, $_POST["navegable"], PDO::PARAM_STR);
        $stmtInsert->bindParam(8, $_POST["subroles"], PDO::PARAM_STR);
        $stmtInsert->bindParam(9, $_POST["crud"], PDO::PARAM_STR);
        $stmtInsert->bindParam(10, $_POST["paginaPadre"], PDO::PARAM_STR);
        $stmtInsert->bindParam(11, $paginaDependencia, PDO::PARAM_STR);
        $stmtInsert->execute();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-paginas.php?success=SC_DT_1&id='.base64_encode($_POST["codigoPagina"]).'";</script>';
    exit();