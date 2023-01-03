<?php include("session.php");?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../librerias/phpmailer/Exception.php';
require '../../librerias/phpmailer/PHPMailer.php';
require '../../librerias/phpmailer/SMTP.php';
?>

<?php include("../../config-general/config.php");?>
<?php
$consultaDatosRelacionados=mysqli_query($conexion, "SELECT * FROM academico_cargas 
INNER JOIN academico_materias AS mate ON mate.mat_id=car_materia
INNER JOIN academico_matriculas AS matri ON matri.mat_id='".$_POST["codEst"]."'
INNER JOIN usuarios ON uss_id=mat_acudiente
INNER JOIN academico_grados AS gra ON gra.gra_id=matri.mat_grado
WHERE car_id='".$_COOKIE["carga"]."'");
$datosRelacionados = mysqli_fetch_array($consultaDatosRelacionados, MYSQLI_BOTH);

$consultaDocente=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$datosRelacionados['car_docente']."'");
$docente = mysqli_fetch_array($consultaDocente, MYSQLI_BOTH);


if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}
if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;
include("../modelo/conexion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_estudiante=".$_POST["codEst"]." AND bol_carga=".$_COOKIE["carga"]." AND bol_periodo=".$_POST["per"]);

$num = mysqli_num_rows($consulta);
$rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
if($num==0){
	mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_id='".$rB[0]."'");
	
	mysqli_query($conexion, "INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_fecha_registro, bol_actualizaciones, bol_observaciones)VALUES('".$_COOKIE["carga"]."', '".$_POST["codEst"]."', '".$_POST["per"]."', '".$_POST["nota"]."', 2, now(), 0, 'Recuperación del periodo.')");
	
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$idSession."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Inserción de notas en el periodo', now())");
		
}else{
	mysqli_query($conexion, "UPDATE academico_boletin SET bol_nota='".$_POST["nota"]."', bol_nota_anterior='".$_POST["notaAnterior"]."', bol_observaciones='Recuperación del periodo.', bol_tipo=2, bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now() WHERE bol_id=".$rB[0]);
	
	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha)VALUES('".$idSession."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Actualización de notas en el periodo', now())");
	
	
	//Si la institución autoriza el envío de mensajes
	if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
		if($datosRelacionados["mat_notificacion1"]==1){
			//INSERTAR CORREO PARA ENVIAR TODOS DESPUÉS
			mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_nota_anterior, corr_periodo, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$_COOKIE["carga"]."', '".$_POST["nota"]."', 4, now(), 0, '".$_POST["notaAnterior"]."', '".$_POST["per"]."', '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')");
			

				//INICIO ENVÍO DE MENSAJE
				$tituloMsj = "¡REGISTRO DE RECUPERACIÓN DEL PERIODO ".$_POST["per"]." PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
				$bgTitulo = "#4086f4";
				$contenidoMsj = '
					<p>
						Hola <b>'.strtoupper($datosRelacionados["uss_nombre"]).'</b>, te informamos que fue registrada una nota de recuperación de periodo para el estudiante <b>'.strtoupper($datosRelacionados["mat_nombres"]).'</b>!<br>
						Estos son los datos relacionados:<br>
						<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
						<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
						<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
						<b>PERIODO:</b> '.$_POST["per"].'<br>
						<b>NOTA ANTERIOR:</b> '.$_POST["notaAnterior"].'<br>
						<b>NUEVA NOTA:</b> '.$_POST["nota"].'<br>
					</p>

					<p>
						<h3 style="color:navy; text-align: center;"><b>ACUDIENTE PREMIUM</b></h3>
						Usted está recibiendo esta notificación porque hace parte del grupo de los <b>ACUDIENTES PREMIUM</b>.<br>
						Gracias por haber adquirido el servicio de notificaciones por correo.
					</p>

				';
				/*
				include("../../config-general/plantilla-email-1.php");
				// Instantiation and passing `true` enables exceptions
				$mail = new PHPMailer(true);
				echo '<div style="display:none;">';
					try {
						include("../../config-general/mail.php");

						$mail->addAddress(strtolower($datosRelacionados['uss_email']), $datosRelacionados['uss_nombre']);    
						$mail->addAddress('tecmejia2010@gmail.com', 'Plataforma SINTIA');

						// Attachments
						//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

						// Content
						$mail->isHTML(true);                                  // Set email format to HTML
						$mail->Subject = 'Nota modificada para '.strtoupper($datosRelacionados["mat_nombres"]);
						$mail->Body = $fin;
						$mail->CharSet = 'UTF-8';

						$mail->send();
						echo 'Mensaje enviado correctamente.';
					} catch (Exception $e) {echo "Error: {$mail->ErrorInfo}"; exit();}
				echo '</div>';
				//FIN ENVÍO DE MENSAJE
				*/
		}
	}
}	

?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: 'Cambios guardados',  
		text: 'Los cambios se ha guardado correctamente!.',
		position: 'botom-left',
		loaderBg:'#ff6849',
		icon: 'success',
		hideAfter: 3000, 
		stack: 6
	});
}
setTimeout ("notifica()", 100);
</script>

    <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
	</div>