<?php
include("../directivo/session.php");
include("../modelo/conexion.php");

    $consultaExistencia= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".contratos_usuarios WHERE cxu_id_contrato='".$_POST["id"]."'  AND cxu_id_institucion='".$config['conf_id_institucion']."'");
    $numDatos = mysqli_num_rows($consultaExistencia);

    if($numDatos==0){
        mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".contratos_usuarios(cxu_id_usuario, cxu_id_contrato, cxu_fecha_aceptacion, cxu_id_institucion)VALUES('".$_POST["idUsuario"]."', '".$_POST["id"]."', now(), '".$config['conf_id_institucion']."')");
        
    }else{
        mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".contratos_usuarios SET cxu_fecha_aceptacion=now() WHERE cxu_id_contrato='".$_POST["id"]."'  AND cxu_id_institucion='".$config['conf_id_institucion']."'");
        
    }

    echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
    exit();