<?php 
session_start();
include("../conexion-datos.php");
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM $baseDatosServicios.instituciones WHERE ins_id='".$_POST["rBd"]."'");

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $institucion['ins_bd']."_2022");


//Verificar que las claves ingresadas conincidan.
if($_POST["clave"]!=$_POST["clave2"])
{
    echo "no son iguales, vuelve a escribir las contraseñas correctamente."; 
    exit();
}

$registroConsulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".restaurar_clave WHERE resc_id_md5='".$_POST["idRegistro"]."'");
$datosRegistro = mysqli_fetch_array($registroConsulta, MYSQLI_BOTH);

//Verificamos que el link no tenga más de 24 horas.
mysqli_query($conexion, "SELECT TIMESTAMPDIFF(HOUR, resc_fec_solicitud, NOW()) as horas FROM mobiliar_dev_2022.restaurar_clave WHERE resc_id = 5");

mysqli_query($conexion, "UPDATE usuarios SET uss_clave='".$_POST["clave"]."' 
WHERE uss_id='".$datosRegistro['resc_id_usuario']."'");
echo "Todo salio bien, tu clave fue cambiada correctamente.";

