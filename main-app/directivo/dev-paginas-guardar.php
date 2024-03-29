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
    try{
        $verificar=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".paginas_publicidad WHERE pagp_id='".$_POST["codigoPagina"]."' OR pagp_ruta='".$_POST["rutaPagina"]."'");
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    $numIdPaginas=mysqli_num_rows($verificar);
    
    if($numIdPaginas>0){
        $datosPaginas=mysqli_fetch_array($verificar, MYSQLI_BOTH);
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="dev-paginas-agregar.php?error=ER_DT_14&id='.base64_encode($datosPaginas['pagp_id']).'&nombrePagina='.base64_encode($datosPaginas['pagp_pagina']).'";</script>';
        exit();
    }

    $paginaDependencia=!empty($_POST["paginaDependencia"])?implode(',',$_POST["paginaDependencia"]):NULL;

    try{
        mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".paginas_publicidad(pagp_id, pagp_pagina, pagp_tipo_usuario, pagp_modulo, pagp_ruta, pagp_palabras_claves, pagp_navegable, pagp_asignable_subroles, pagp_crud, pagp_pagina_padre, pagp_paginas_dependencia) VALUES ('".$_POST["codigoPagina"]."', '".$_POST["nombrePagina"]."', '".$_POST["tipoUsuario"]."', '".$_POST["modulo"]."', '".$_POST["rutaPagina"]."', '".$_POST["palabrasClaves"]."', '".$_POST["navegable"]."', '".$_POST["subroles"]."', '".$_POST["crud"]."', '".$_POST["paginaPadre"]."', '".$paginaDependencia."')");
    } catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}
    
    include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="dev-paginas.php?success=SC_DT_1&id='.base64_encode($_POST["codigoPagina"]).'";</script>';
    exit();