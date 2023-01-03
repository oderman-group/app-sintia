<?php
if(!isset($idSession) || $idSession==""){$idSession = $_SESSION["id"];}
//USUARIO ACTUAL
$consultaUsuarioActual = mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$idSession."'");


$numUsuarioActual = mysqli_num_rows($consultaUsuarioActual);
$datosUsuarioActual = mysqli_fetch_array($consultaUsuarioActual, MYSQLI_BOTH);

//Verificar si cumpleaños
$cumpleUsuarioConsulta = mysqli_query($conexion, "SELECT YEAR(uss_fecha_nacimiento) AS agno FROM usuarios 
WHERE MONTH(uss_fecha_nacimiento)='".date("m")."' AND DAY(uss_fecha_nacimiento)='".date("d")."' AND uss_id='".$idSession."'");
$cumpleUsuario = mysqli_fetch_array($cumpleUsuarioConsulta, MYSQLI_BOTH);
if(isset($cumpleUsuario['agno'])){
    $edadUsuario = date("Y") - $cumpleUsuario['agno'];
}