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
	mysqli_query($conexion, "INSERT INTO restaurar_clave(resc_id_usuario, resc_fec_solicitud) VALUES('".$dU['uss_id']."',now())");

	if($nU>0){
		//INICIO ENVÍO DE MENSAJE
		$tituloMsj = "¡".strtoupper($dU["uss_nombre"])." TUS CREDENCIALES!";
		$bgTitulo = "#4086f4";
		$contenidoMsj = '
			Hola!<br>
			<b>'.strtoupper($dU["uss_nombre"]).'</b>, tus credenciales de acceso a la plataforma SINTIA son:<br>
			Usuario: <b>'.$dU['uss_usuario'].'</b><br>
			Contraseña: <b>'.$dU['uss_clave'].'</b>';

				
				include("../config-general/plantilla-email-1.php");
				// Instantiation and passing `true` enables exceptions
				$mail = new PHPMailer(true);
				echo '<div style="display:block;">';
					try {
						include("../config-general/mail.php");

						$mail->addAddress(strtolower($dU['uss_email']), $dU['uss_nombre']);    
						$mail->addAddress('tecmejia2010@gmail.com', 'Plataforma SINTIA');

						// Content
						$mail->isHTML(true);                                  // Set email format to HTML
						$mail->Subject = 'TUS CREDENCIALES DE ACCESO A SINTIA';
						$mail->Body = $fin;
						$mail->CharSet = 'UTF-8';

						$mail->send();
						echo 'Mensaje enviado correctamente.';
					} catch (Exception $e) {echo "Error: {$mail->ErrorInfo}"; exit();}
				echo '</div>';
				//FIN ENVÍO DE MENSAJE 
				
						
		echo '<script type="text/javascript">window.location.href="restaurar-contrasena.php?idU='.$dU['uss_id'].'&idI='.$_POST["rBd"].'";</script>';
		exit();
	
	}else{
		echo '<script type="text/javascript">window.location.href="index.php?error=3";</script>';
		exit();	
	}
}
if($_POST["id"]==3){
	$usuario = mysqli_query($conexion, "SELECT * FROM restaurar_clave WHERE resc_id_usuario='".$_POST["idU"]."'");
	$dU = mysqli_fetch_array($usuario, MYSQLI_BOTH);
	/*$resta = $dU['resc_fec_solicitud'] - now();
	echo $resta;
	exit();*/
}

?>
