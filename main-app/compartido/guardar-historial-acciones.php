<?php
$ip=$_SERVER["REMOTE_ADDR"];

//$paisIP=getCountryFromIP($ip);
$tiempo_final = microtime(true);
$tiempo = $tiempo_final - $tiempo_inicial;
$tiempoMostrar = round($tiempo,3);

$memoria_fin_script  = memory_get_usage();
$memoria_pico_script = memory_get_peak_usage();

$memoria_consumida = $memoria_fin_script - $memoria_inicio_script;

// Guardar en Megabytes (MB)
$memoria_consumida_mb = $memoria_consumida / (1024 * 1024);
$memoria_pico_mb = $memoria_pico_script / (1024 * 1024);

$idLogin=null;
if(isset($_SESSION['admin'])){
    $idLogin=$_SESSION['admin'];
}
if(isset($_SESSION['docente'])){
    $idLogin=$_SESSION['docente'];
}
if(isset($_SESSION['acudiente'])){
    $idLogin=$_SESSION['acudiente'];
}

$REFERER = 'NO APLICA';
if(!empty($_SERVER['HTTP_REFERER'])){
    $REFERER = $_SERVER['HTTP_REFERER'];
}

try {
    
    $post = ""; 
    if ( isset($_POST) && !empty($_POST) ) {
        $post = " | ";
        foreach ($_POST as $key => $value) {
            if(is_array($value)){
                $valor=json_encode($value);
                $post .= "&{$key}={$valor}";
            }else{
                $post .= "&{$key}={$value}";
            }
            
        }
    }

    if (empty($idPaginaInterna)) {
        $idPaginaInterna = 'GN0003';
    }
    
    //HISTORIAL DE ACCIONES
    mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".seguridad_historial_acciones(
        hil_usuario, 
        hil_url, 
        hil_titulo, 
        hil_ip, 
        hil_so, 
        hil_institucion, 
        hil_pagina_anterior, 
        hil_tiempo_carga, 
        hil_usuario_autologin,
        hil_momento,
        hil_uso_memoria_mb,
        hil_pico_memoria_mb
        )
    VALUES(
        '".$_SESSION['id']."', 
        '".$_SERVER['PHP_SELF']."?".mysqli_real_escape_string($conexion,$_SERVER['QUERY_STRING'])."".mysqli_real_escape_string($conexion,$post)."', 
        '".$idPaginaInterna."', 
        '".$ip."', 
        '".$_SERVER['HTTP_USER_AGENT']."', 
        '".$config['conf_id_institucion']."', 
        '".$REFERER."', 
        '".$tiempoMostrar."', 
        '".$idLogin."',
        'FIN',
        '".$memoria_consumida_mb."',
        '".$memoria_pico_mb."'
    )");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}