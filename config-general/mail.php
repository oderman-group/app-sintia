<?php
//include("../../conexion.php");
$datosEmpresa = mysqli_fetch_array(mysqli_query($conexionBaseDatosServicios, "SELECT * FROM datos_contacto WHERE dtc_id=1"), MYSQLI_BOTH);

//echo "Envia: ".$datosEmpresa['dtc_clave_email'];

//Server settings
$mail->SMTPDebug = 2;                                       // Enable verbose debug output
$mail->isSMTP();                                            // Set mailer to use SMTP
$mail->Host       = 'mail.'.$datosEmpresa['dtc_dominio'];  	// Specify main and backup SMTP servers
$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
$mail->Username   = $datosEmpresa['dtc_email'];                  
$mail->Password   = $datosEmpresa['dtc_clave_email'];                      
$mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
$mail->Port       = 465;

//Recipients
$mail->setFrom($datosEmpresa['dtc_email'], $datosEmpresa['dtc_nombre']);
?>