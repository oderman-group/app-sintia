<?php
    include("session.php");

    Modulos::validarAccesoDirectoPaginas();
    $idPaginaInterna = 'DV0021';
    include("../compartido/historial-acciones-guardar.php");

    //COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
    if(trim($_POST["nombrePagina"])=="" || trim($_POST["rutaPagina"])==""){
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="dev-paginas-editar.php?error=ER_DT_4&idP='.base64_encode($_POST["codigoPagina"]).'";</script>';
        exit();
    }

    //COMPROBAMOS QUE NO EXISTA LA RUTA
    // Migrado a PDO - Consultas preparadas
    try{
        require_once(ROOT_PATH."/main-app/class/Conexion.php");
        $conexionPDO = Conexion::newConnection('PDO');
        
        $sql = "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad 
                WHERE pagp_id!=? AND pagp_ruta=? AND pagp_tipo_usuario=?";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $_POST["codigoPagina"], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST["rutaPagina"], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST["tipoUsuario"], PDO::PARAM_STR);
        $stmt->execute();
        $numIdPaginas = $stmt->rowCount();
        
        if($numIdPaginas>0){
            $datosPaginas = $stmt->fetch(PDO::FETCH_ASSOC);
            include("../compartido/guardar-historial-acciones.php");
            echo '<script type="text/javascript">window.location.href="dev-paginas-editar.php?error=ER_DT_14&idP='.base64_encode($_POST["codigoPagina"]).'&id='.base64_encode($datosPaginas['pagp_id']).'&nombrePagina='.$datosPaginas['pagp_pagina'].'";</script>';
            exit();
        }

        $paginaDependencia=!empty($_POST["paginaDependencia"])?implode(',',$_POST["paginaDependencia"]):NULL;

        $sqlUpdate = "UPDATE ".$baseDatosServicios.".paginas_publicidad SET 
                      pagp_pagina=?, pagp_tipo_usuario=?, pagp_modulo=?, pagp_ruta=?, pagp_palabras_claves=?, 
                      pagp_navegable=?, pagp_asignable_subroles=?, pagp_crud=?, pagp_pagina_padre=?, 
                      pagp_url_youtube=?, pagp_descripcion=?, pagp_paginas_dependencia=? 
                      WHERE pagp_id=?";
        $stmtUpdate = $conexionPDO->prepare($sqlUpdate);
        $stmtUpdate->bindParam(1, $_POST["nombrePagina"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(2, $_POST["tipoUsuario"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(3, $_POST["modulo"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(4, $_POST["rutaPagina"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(5, $_POST["palabrasClaves"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(6, $_POST["navegable"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(7, $_POST["subroles"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(8, $_POST["crud"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(9, $_POST["paginaPadre"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(10, $_POST["urlYoutube"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(11, $_POST["descripcion"], PDO::PARAM_STR);
        $stmtUpdate->bindParam(12, $paginaDependencia, PDO::PARAM_STR);
        $stmtUpdate->bindParam(13, $_POST["codigoPagina"], PDO::PARAM_STR);
        $stmtUpdate->execute();
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-paginas.php?success=SC_DT_2&id='.base64_encode($_POST["codigoPagina"]).'";</script>';
    exit();