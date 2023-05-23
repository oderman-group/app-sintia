<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'CM0004';
include("../compartido/historial-acciones-guardar.php");

    try{
        $consultaExistencia= mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios WHERE ttpxu_id_termino_tratamiento_politicas='".$_POST["id"]."' AND ttpxu_id_usuario='".$_POST["idUsuario"]."' AND ttpxu_id_institucion='".$config['conf_id_institucion']."'");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $numDatos = mysqli_num_rows($consultaExistencia);

    if($numDatos==0){
        try{
            mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios(ttpxu_id_termino_tratamiento_politicas, ttpxu_id_usuario, ttpxu_id_institucion, ttpxu_fecha_aceptacion)VALUES('".$_POST["id"]."', '".$_POST["idUsuario"]."', '".$config['conf_id_institucion']."', now())");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
    }else{
        try{
            mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".terminos_tratamiento_politicas_usuarios SET ttpxu_fecha_aceptacion=now() WHERE ttpxu_id_termino_tratamiento_politicas='".$_POST["id"]."' AND ttpxu_id_usuario='".$_POST["idUsuario"]."' AND ttpxu_id_institucion='".$config['conf_id_institucion']."'");
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        
    }

	include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
    exit();