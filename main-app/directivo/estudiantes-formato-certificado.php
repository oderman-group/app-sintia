<?php
    include("session.php");
    $idPaginaInterna = 'DT0225';
    
    if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
        echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
        exit();
    }
    include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
    
    $id="";
    if(isset($_POST["id"])){$id=base64_encode($_POST["id"]);}
    $desde="";
    if(isset($_POST["desde"])){$desde=base64_encode($_POST["desde"]);}
    $hasta="";
    if(isset($_POST["hasta"])){$hasta=base64_encode($_POST["hasta"]);}
    $estampilla="";
    if(isset($_POST["estampilla"])){$estampilla=base64_encode($_POST["estampilla"]);}

    $ext = 1298;
    if (empty($_POST["certificado"])) {
        switch ($config['conf_certificado']) {
            case 1:
                $ext = '';
            break;
    
            case 2:
                $ext = '-2';
            break;
    
            case 3:
                $ext = '-3';
            break;
    
            default:
                $ext = '';
            break;
        }
    } else {
        $ext = $_POST["certificado"] != 1 ? "-".$_POST["certificado"] : "";
    }

    $ruta="informes-todos.php?error=ER_DT_9";
    if($ext != 1298){
        $ruta="../compartido/matricula-certificado-areas".$ext.".php?id=".$id."&desde=".$desde."&hasta=".$hasta."&estampilla=".$estampilla;
    }
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="'.$ruta.'";</script>';
	exit();