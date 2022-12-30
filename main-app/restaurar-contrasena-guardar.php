<?php 
session_start();
include("../conexion-datos.php");
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM $baseDatosServicios.instituciones WHERE ins_id='".$_POST["rBd"]."'");

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $institucion['ins_bd']."_".date("Y"));


//Verificar que las claves ingresadas conincidan.


$registroConsulta = mysqli_query($conexion, "SELECT * FROM restaurar_clave WHERE resc_id_md5='".$_POST["idRegistro"]."'");
$datosRegistro = mysqli_fetch_array($registroConsulta, MYSQLI_BOTH);

//Verificamos que el link no tenga más de 24 horas.


mysqli_query($conexion, "UPDATE usuarios SET uss_clave='".$_POST["clave"]."' 
WHERE uss_id='".$datosRegistro['resc_id_usuario']."'");

echo "hola, todo salió bien... Tu clave fue cambiada correctamente.";
