<?php 
include("conexion-datos.php");
//Conexion con el Servidor
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
//seleccionamos la base de datos
//mysql_select_db($baseDatosServicios, $conexion);
?>