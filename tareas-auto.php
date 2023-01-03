<?php
include("conexion.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'librerias/phpmailer/Exception.php';
require 'librerias/phpmailer/PHPMailer.php';
require 'librerias/phpmailer/SMTP.php';


//=====CORREOS PARA LOS INTERESADOS EN SINTIA - DEMO=====//
$correosDemo = mysql_query("SELECT DATEDIFF(now(), demo_fecha_ingreso), demo_usuario, uss_nombre, uss_email, uss_ultimo_ingreso FROM demo
INNER JOIN mobiliar_sintiademo.usuarios ON uss_id=demo_usuario
WHERE (demo_correo_enviado<5 AND demo_nocorreos=0)
",$conexion);




$paraEnviar = 0;
while($cDemo = mysql_fetch_array($correosDemo)){
	echo $cDemo[0]."<br>";
	//Correo 2/ 1er día/ ¿Necesitas Ayuda?
	if($cDemo[0]==1){
		$tituloMsj = "¿Requiere una cita virtual para aclarar dudas?";
		$bgTitulo = "#31a952";
		$contenidoMsj = '
			<p>
				Hola <b>'.strtoupper($cDemo[2]).'</b><br>
				Mi nombre es Jhon Mejía, su asesor personal.<br>
				Si lo requiere podemos hacer una demostración virtual de la plataforma.<br>
				Sólo tiene que responder a este correo o escribir directamente a mi Whatsapp para programar la cita.<br><br>
				
				<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
				<a href="https://api.whatsapp.com/send?phone=573007304428&text=Hola, quisiera una demostración virtual de la Plataforma SINTIA." target="_blank" style="background-color: #31A952; color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 300 730 4428</a></div><br><br>
				
				Gracias por su atención a este mensaje.
			</p>	
			<p>
				Cordialmente,<br><br>
				
				Jhon Mejía M.<br>
				<b>Email:</b> info@plataformasintia.com<br>
				<b>WhatsApp:</b> (+57) 300 730 4428<br>
			</p>
		';
		$paraEnviar = 1;
	}
	
	//Si ya ha ingresado a la plataforma
	if($cDemo[4]!=""){
		//Correo 3/ 5 días/ ¿Cómo te ha ido?
		if($cDemo[0]==5){
			$tituloMsj = "¿Cómo le ha ido con la plataforma SINTIA?";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.strtoupper($cDemo[2]).'</b><br>
					Como ya lo sabe, mi nombre es Jhon Mejía, soy su asesor personal.<br>
					Quería preguntarle cómo le ha ido con la plataforma, y recordarle que estoy a su disposición.<br>
					Cualquier cosa que requiera me puede escribir a este correo o directamente a mi Whatsapp.<br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 300 730 4428<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 4/ 10 días/ Pronto vence, llamada a la acción
		if($cDemo[0]==10){
			$tituloMsj = "En 5 días terminará la versión de prueba.";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.strtoupper($cDemo[2]).'</b><br>
					Esperamos que haya podido disfrutar la versión de prueba de la plataforma SINTIA<br>
					Por ahora quedan sólo 5 días para que la versión de prueba termine.<br>
					¿Ha podido tomar usted o la Institución alguna decisión sobre el plan que desean?<br>
					Quedo atento a su respuesta.<br><br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573007304428&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="background-color: #31A952; color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 300 730 4428</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 300 730 4428<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 5/ 10 días/ Hoy cence, llamada a la acción más fuerte
		if($cDemo[0]==14){
			$tituloMsj = "Hoy termina la versión de prueba";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.strtoupper($cDemo[2]).'</b><br>
					Esperamos que haya podido disfrutar estos días de prueba con la plataforma SINTIA<br>
					Hoy es el último día para usar la versión gratuita. Pero no se preocupe, tenemos un plan para que la puedan seguir utilizando.<br>
					Si le interesa puede contactarme, ya sabe que estoy a su servicio.<br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573007304428&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 300 730 4428</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 300 730 4428<br>
				</p>
			';
			$paraEnviar = 1;
		}	
	}
	//Si no han ingresado a la plataforma
	else{
		//Correo 3/ 5 días/ ¿Cómo te ha ido?
		if($cDemo[0]==5){
			$tituloMsj = "¿Alguna dificultad para usar la plataforma SINTIA?";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.strtoupper($cDemo[2]).'</b><br>
					Como ya lo sabe, mi nombre es Jhon Mejía, soy su asesor personal.<br>
					Quería preguntarle si ha tenido alguna dificultad para ingresar a la plataforma.<br>
					Cualquier cosa sabe que puede contactarme.

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 300 730 4428<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 4/ 10 días/ Pronto vence, llamada a la acción
		if($cDemo[0]==10){
			$tituloMsj = "En 5 días terminará la versión de prueba y aún no la has disfrutado";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.strtoupper($cDemo[2]).'</b><br>
					Aún no has podido disfrutar la versión de prueba de la plataforma SINTIA<br>
					Por ahora quedan sólo 5 días para que la versión de prueba termine.<br>
					¿Ha podido tomar usted o la Institución alguna decisión sobre el plan que desean?<br>
					Quedo atento a su respuesta.<br><br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573007304428&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="background-color: #31A952; color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 300 730 4428</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 300 730 4428<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 5/ 10 días/ Hoy vence, llamada a la acción más fuerte
		if($cDemo[0]==14){
			$tituloMsj = "Hoy termina la versión de prueba";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.strtoupper($cDemo[2]).'</b><br>
					Finalmente no has podido disfrutar estos días de prueba con la plataforma SINTIA<br>
					Hoy es el último día para usar la versión gratuita. Pero no se preocupe, tenemos un plan para que la puedan seguir utilizando.<br>
					Si le interesa puede contactarme, ya sabe que estoy a su servicio.<br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573007304428&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 300 730 4428</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 300 730 4428<br>
				</p>
			';
			$paraEnviar = 1;
		}
	}
	
	
	if($paraEnviar==1){
		//INICIO ENVÍO DE MENSAJE
		include("config-general/plantilla-email-1.php");

		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		echo "Enviando información. Por favor espere unos pocos segundos...<br>";
		
		echo '<div style="display:none;">';
			try {
				include("config-general/mail.php");

				$mail->addAddress($cDemo[3], $cDemo[2]);     // Add a recipient
				//$mail->addBCC('jhooderman@gmail.com');     // Add a recipient
				//$mail->addBCC('info@plataformasintia.com');     // Add a recipient

				// Attachments
				//$mail->addAttachment('files-general/plan-sintia-info.pdf', 'Información SINTIA');    // Optional name

				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $tituloMsj;
				$mail->Body = $fin;
				$mail->CharSet = 'UTF-8';

				$mail->send();
				echo 'Mensaje enviado.';
			} catch (Exception $e) {echo "Error: {$mail->ErrorInfo}"; exit();}
		echo '</div>';
		//FIN ENVÍO DE MENSAJE
	}
}


//=====CORREOS PROGRAMADOS DE LAS NOTIFICACIONES A ACUDIENTES=====//
$correosProg = mysql_query("SELECT * FROM correos
INNER JOIN instituciones ON ins_id=corr_institucion
WHERE corr_estado=0 AND corr_usuario IS NOT NULL
GROUP BY corr_institucion, corr_usuario
ORDER BY corr_institucion, corr_usuario
",$conexion);


$mensajesEnviados = 0;
$mensajesTotales = 0;
while($cProg = mysql_fetch_array($correosProg)){

	$numeros = mysql_fetch_array(mysql_query("
	SELECT
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=1 AND corr_estado=0),
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=2 AND corr_estado=0),
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=3 AND corr_estado=0),
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=4 AND corr_estado=0)
	",$conexion));
	
	
	$institucionAgno = $cProg['ins_bd']."_".date("Y");
	
	$acudiente = mysql_fetch_array(mysql_query("SELECT * FROM ".$institucionAgno.".usuarios WHERE uss_id='".$cProg['corr_usuario']."'",$conexion));
	
	$tituloMsj = "INFORME DIARIO DE SINTIA";
	$bgTitulo = "#4086f4";
	$contenidoMsj = '
		<b>INSTITUCIÓN:</b> '.strtoupper($cProg['ins_nombre']).'
		<p>
			Hola <b>'.strtoupper($acudiente["uss_nombre"]).'</b>, queremos entregarte el informe detallado de todas las novedades de hoy relacionadas con tu(s) acudido(s).
		</p>
			
			<table width="100%">
				<tr style="height:50px; text-align:center; color:white;">
					<td style="background-color:#4086F4; padding: 10px;">
						<h6>NOTAS REGISTRADAS</h6>
						<span>'.$numeros[0].'</span>
					</td>
					<td style="background-color:#31A952;">
						<h6>NOTAS MODIFICADAS</h6>
						<span>'.$numeros[1].'</span>
					</td>
					<td style="background-color:#FBBD01;">
						<h6>NOTAS RECUPERADAS</h6>
						<span>'.$numeros[2].'</span>
					</td>
					<td style="background-color:#EB4132;">
						<h6>PERIODOS RECUPERADOS</h6>
						<span>'.$numeros[3].'</span>
					</td>
				</tr>
			</table>
		
		
			
	';
	
	$correosDatos = mysql_query("SELECT * FROM correos
	WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_estado=0
	ORDER BY corr_tipo
	",$conexion);
	
	
	$novedades = 0; 
	//Recorrer por institución y por usuario
	while($cDat = mysql_fetch_array($correosDatos)){	
		
		//De los primeros tipos: 1, 2 y 3
		if($cDat['corr_tipo']==1 or $cDat['corr_tipo']==2 or $cDat['corr_tipo']==3){
			
			$datosRelacionados = mysql_fetch_array(mysql_query("SELECT * FROM ".$institucionAgno.".academico_actividades 
			INNER JOIN ".$institucionAgno.".academico_cargas ON car_id=act_id_carga
			INNER JOIN ".$institucionAgno.".academico_materias AS mate ON mate.mat_id=car_materia
			INNER JOIN ".$institucionAgno.".academico_matriculas AS matri ON matri.mat_id='".$cDat["corr_estudiante"]."'
			INNER JOIN ".$institucionAgno.".usuarios ON uss_id=mat_acudiente
			INNER JOIN ".$institucionAgno.".academico_grados AS gra ON gra.gra_id=matri.mat_grado
			WHERE act_id='".$cDat["corr_actividad"]."'
			",$conexion));
			
			$docente = mysql_fetch_array(mysql_query("SELECT * FROM ".$institucionAgno.".usuarios WHERE uss_id='".$datosRelacionados['car_docente']."'",$conexion));
			
			
			if($datosRelacionados[0]!=""){
				if($cDat['corr_tipo']==1){
					$contenidoMsj .= '
						<h3 align="center">REGISTRO DE NOTA</h3>
						<p>
							<b>FECHA Y HORA:</b> '.$cDat["corr_fecha_registro"].'<br>
							<b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
							<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
							<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
							<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
							<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
							<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
							<b>NOTA:</b> '.$cDat["corr_nota"].'<br>
						</p><hr>
					';
				}

				if($cDat['corr_tipo']==2){
					$contenidoMsj .= '
						<h3 align="center">MODIFICACIÓN DE NOTA</h3>
						<p>
							<b>FECHA Y HORA:</b> '.$cDat["corr_fecha_registro"].'<br>
							<b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
							<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
							<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
							<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
							<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
							<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
							<b>NOTA ANTERIOR:</b> '.$cDat["corr_nota_anterior"].'<br>
							<b>NUEVA NOTA:</b> '.$cDat["corr_nota"].'<br>
						</p><hr>
					';
				}

				if($cDat['corr_tipo']==3){
					$contenidoMsj .= '
						<h3 align="center">RECUPERACIÓN DE NOTA</h3>
						<p>
							<b>FECHA Y HORA:</b> '.$cDat["corr_fecha_registro"].'<br>
							<b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
							<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
							<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
							<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
							<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
							<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
							<b>NOTA ANTERIOR:</b> '.$cDat["corr_nota_anterior"].'<br>
							<b>NOTA RECUPERACIÓN:</b> '.$cDat["corr_nota"].'<br>
						</p><hr>
					';
				}
				
				mysql_query("UPDATE correos SET corr_estado=1, corr_fecha_envio=now() WHERE corr_id='".$cDat["corr_id"]."'",$conexion);
				
				$novedades ++;
				$mensajesTotales++;
				
			}else{
				mysql_query("UPDATE correos SET corr_observacion='No hay datos relacionados en correos tipo 1, 2, 3.' WHERE corr_id='".$cDat["corr_id"]."'",$conexion);
				
			}
		}
		
		//Del tipo 4
		if($cDat['corr_tipo']==4){
			$datosRelacionados = mysql_fetch_array(mysql_query("SELECT * FROM ".$institucionAgno.".academico_cargas 
			INNER JOIN ".$institucionAgno.".academico_materias AS mate ON mate.mat_id=car_materia
			INNER JOIN ".$institucionAgno.".academico_matriculas AS matri ON matri.mat_id='".$cDat["corr_estudiante"]."'
			INNER JOIN ".$institucionAgno.".usuarios ON uss_id=mat_acudiente
			INNER JOIN ".$institucionAgno.".academico_grados AS gra ON gra.gra_id=matri.mat_grado
			WHERE car_id='".$cDat["corr_carga"]."'
			",$conexion));
			
			
			$docente = mysql_fetch_array(mysql_query("SELECT * FROM ".$institucionAgno.".usuarios WHERE uss_id='".$datosRelacionados['car_docente']."'",$conexion));
			
			
			if($datosRelacionados[0]!=""){

				$contenidoMsj .= '
					<h3 align="center">RECUPERACIÓN DE PERIODO</h3>
					<p>
						<b>FECHA Y HORA:</b> '.$cDat["corr_fecha_registro"].'<br>
						<b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
						<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
						<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
						<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
						<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
						<b>NOTA ANTERIOR:</b> '.$cDat["corr_nota_anterior"].'<br>
						<b>NUEVA NOTA:</b> '.$cDat["corr_nota"].'<br>
					</p><hr>
				';
			
				mysql_query("UPDATE correos SET corr_estado=1, corr_fecha_envio=now() WHERE corr_id='".$cDat["corr_id"]."'",$conexion);
				
				$novedades ++;
				$mensajesTotales++;
				
			}else{
				mysql_query("UPDATE correos SET corr_observacion='No hay datos relacionados en correos tipo 4.' WHERE corr_id='".$cDat["corr_id"]."'",$conexion);
				
			}
		}

	}
	
	$contenidoMsj .= '
		<b>TOTAL NOVEDADES:</b> '.$novedades.'
	';
	
	
	//INICIO ENVÍO DE MENSAJE
	include("config-general/plantilla-email-1.php");
	//echo $fin;
	
	if($acudiente['uss_email']!=""){
		$mensajesEnviados++;
		// Instantiation and passing `true` enables exceptions
		$mail = new PHPMailer(true);
		echo '<div style="display:none;">';
			try {
				include("config-general/mail.php");
				$mail->addAddress(strtolower($acudiente['uss_email']), $acudiente['uss_nombre']);    
				//$mail->addAddress('tecmejia2010@gmail.com', 'Plataforma SINTIA');

				// Attachments
				//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $tituloMsj;
				$mail->Body = $fin;
				$mail->CharSet = 'UTF-8';

				$mail->send();
				echo 'Mensaje enviado correctamente.';
			} catch (Exception $e) {
				mysql_query("INSERT INTO correos_enviados(cenv_fecha, cenv_cantidad, cenv_novedades)VALUES(now(), '".$mensajesEnviados."', '".$mensajesTotales."')",$conexion);
				
				
				echo "Error: {$mail->ErrorInfo}"; exit();
			}
		echo '</div>';
	}else{
		mysql_query("UPDATE correos SET corr_observacion='El acudiente no tiene email registrado.' WHERE corr_id='".$cDat["corr_id"]."'",$conexion);
		
	}
	//FIN ENVÍO DE MENSAJE
	
	
}
?>