<?php 
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../librerias/phpmailer/Exception.php';
require '../librerias/phpmailer/PHPMailer.php';
require '../librerias/phpmailer/SMTP.php';

include("../conexion-datos.php");
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM $baseDatosServicios.instituciones WHERE ins_id='".$_POST["rBd"]."'");

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $institucion['ins_bd']."_2022");

$usuario = mysqli_query($conexion, "SELECT * FROM usuarios 
WHERE uss_email='".$_POST["email"]."' OR uss_usuario='".$_POST["email"]."'");
$usuarioCantidad = mysqli_num_rows($usuario);
$datosUsuario = mysqli_fetch_array($usuario, MYSQLI_BOTH);

if($usuarioCantidad > 0){
	mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".restaurar_clave(resc_id_usuario, resc_fec_solicitud, resc_id_institucion) VALUES('".$datosUsuario['uss_id']."', now(), '".$institucion['ins_id']."')");
	$idUltimoRegistro = mysqli_insert_id($conexion);

	mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".restaurar_clave SET resc_id_md5='".md5($idUltimoRegistro)."' WHERE resc_id='".$idUltimoRegistro."'");
	
	//Este es link que se debe enviar por correo
	echo '<script type="text/javascript">window.location.href="restaurar-contrasena.php?idRegistro='.md5($idUltimoRegistro).'";</script>';
	exit();
	
}else{
	echo '<script type="text/javascript">window.location.href="index.php?error=3&msg=Usuario-no-encontrado-con-ese-correo";</script>';
	exit();	
}

