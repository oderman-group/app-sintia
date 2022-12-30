<?php 
session_start();
include("../conexion-datos.php");
$conexionBaseDatosServicios = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
$institucionConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM $baseDatosServicios.instituciones WHERE ins_id='".$_POST["rBd"]."'");

$institucion = mysqli_fetch_array($institucionConsulta, MYSQLI_BOTH);

$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $institucion['ins_bd']."_".date("Y"));
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../librerias/phpmailer/Exception.php';
require '../librerias/phpmailer/PHPMailer.php';
require '../librerias/phpmailer/SMTP.php';
?>

<?php

//RECORDAR CLAVE
if($_POST["id"]==2){
	$usuario = mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_email='".$_POST["email"]."' OR uss_usuario='".$_POST["email"]."'");
	$nU = mysqli_num_rows($usuario);
	$dU = mysqli_fetch_array($usuario, MYSQLI_BOTH);

	if($nU>0){
		
		mysqli_query($conexion, "INSERT INTO restaurar_clave(resc_id_usuario, resc_fec_solicitud) VALUES('".$dU['uss_id']."',now())");
		$idUltimoRegistro = mysqli_insert_id($conexion);

		mysqli_query($conexion, "UPDATE restaurar_clave SET resc_id_md5='".md5($idUltimoRegistro)."' WHERE resc_id='".$idUltimoRegistro."'");
		
		echo '<script type="text/javascript">window.location.href="restaurar-contrasena.php?idRegistro='.md5($idUltimoRegistro).'";</script>';
		exit();
	
	}else{
		echo '<script type="text/javascript">window.location.href="index.php?error=3";</script>';
		exit();	
	}
}
if($_POST["id"]==3){
	//Verificar que las claves ingresadas conincidan.

	$registroConsulta = mysqli_query($conexion, "SELECT * FROM restaurar_clave WHERE resc_id_md5='".$_POST["idRegistro"]."'");
	$datosRegistro = mysqli_fetch_array($usuario, MYSQLI_BOTH);

	//Verificamos que el link no tenga más de 24 horas.

	mysqli_query($conexion, "UPDATE usuarios SET uss_clave='".$_POST["clave"]."' 
	WHERE uss_id='".$datosRegistro['resc_id_usuario']."'");
}
