<?php include("session.php");?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../librerias/phpmailer/Exception.php';
require '../../librerias/phpmailer/PHPMailer.php';
require '../../librerias/phpmailer/SMTP.php';
?>

<?php include("verificar-carga.php");?>
<?php
$consultaNum=mysqli_query($conexion, "SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$_POST["codEst"]."'");
$num = mysqli_num_rows($consultaNum);

$consultaDatosRelacionados=mysqli_query($conexion, "SELECT * FROM academico_actividades 
INNER JOIN academico_cargas ON car_id=act_id_carga
INNER JOIN academico_materias AS mate ON mate.mat_id=car_materia
INNER JOIN academico_matriculas AS matri ON matri.mat_id='".$_POST["codEst"]."'
INNER JOIN usuarios ON uss_id=mat_acudiente
INNER JOIN academico_grados AS gra ON gra.gra_id=matri.mat_grado
WHERE act_id='".$_POST["codNota"]."'
");
$datosRelacionados = mysqli_fetch_array($consultaDatosRelacionados, MYSQLI_BOTH);

$consultaDocente=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$datosRelacionados['car_docente']."'");
$docente = mysqli_fetch_array($consultaDocente, MYSQLI_BOTH);


$mensajeNot = 'Hubo un error al guardar las cambios';

//Para guardar notas
if($_POST["operacion"]==1){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<$config[3]) $_POST["nota"] = $config[3];

	if($num==0){
		mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0)");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'");
		
		
		//Si la institución autoriza el envío de mensajes
		if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
			if($datosRelacionados["mat_notificacion1"]==1){

				//INSERTAR CORREO PARA ENVIAR TODOS DESPUÉS
				mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$datosRelacionados["car_id"]."', '".$_POST["codNota"]."', '".$_POST["nota"]."', 1, now(), 0, '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')");
				

				//INICIO ENVÍO DE MENSAJE
				$tituloMsj = "¡REGISTRO DE NOTA PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
				$bgTitulo = "#4086f4";
				$contenidoMsj = '
					<p>
						Hola <b>'.strtoupper($datosRelacionados["uss_nombre"]).'</b>, te informamos que fue registrada una nueva nota para el estudiante <b>'.strtoupper($datosRelacionados["mat_nombres"]).'</b>!<br>
						Estos son los datos relacionados:<br>
						<b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
						<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
						<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
						<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
						<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
						<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
						<b>NOTA:</b> '.$_POST["nota"].'<br>
					</p>';

				if($datosRelacionados["mat_notificacion1"]==1){
					$contenidoMsj .= '
						<p>
							<h3 style="color:navy; text-align: center;"><b>ACUDIENTE PREMIUM SINTIA</b></h3>
							Usted está recibiendo esta notificación porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
							Gracias por haber adquirido el servicio de notificaciones por correo.
						</p>
					';	
				}
				else{	
					$contenidoMsj .= '
						<p>
							<h3 style="color:navy; text-align: center;"><b>MUY IMPORTANTE</b></h3>
							Este servicio de <b>notifiaciones por correo</b> lo hemos otorgado gratuitamente durante el mes de <b>SEPTIEMBRE DE 2019</b> para que usted vea sus beneficios.<br>
							Si desea adquirir este servicio de forma permanente durante todo el resto de este año 2019 y todo el año 2020, aproveche el <b>65% DE DESCUENTO</b> que hay ahora, y adquieralo por la módica suma de <b>$21.000</b>.<br>
							Recuerde que es por todo el resto de este año y todo el año siguiente.<br>
							<b>A PARTIR DE MAÑANA YA VALDRÁ $60.000.</b>
							<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
						</p>


						<h2 style="color:#eb4132; text-align: center;"><b>AHORRA $39.000</b></h2>
						<p style="text-align: center;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1001&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank"><img src="https://plataformasintia.com/files-general/email/ultimosdias.jpg"></a></p>



						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1000&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

						<p>
						Ó para su <b>mayor facilidad</b> puede hacer una transferencia, sin costo adicional, a nuestra cuenta:<br>
						<img src="https://plataformasintia.com/files-general/iconos/bacolombia.png" width="40" align="middle"> Ahorros Bancolombia Número: <b>431-565882-54</b>.<br>
						<img src="https://plataformasintia.com/files-general/iconos/colpatria.png" width="40" align="middle"> Ahorros Colpatria Número: <b>789-20112-53</b>.<br>
						Si desea puede escribirnos al <b>WhatsApp: 313 752 5894</b> para brindarle mayor información.
						</p>

						<p>Para activar su servicio de inmediato, recuerde enviar el soporte de pago, o el pantallazo(si hace su pago en línea), al correo electrónico <b>pagos@plataformasintia.com</b>. o al <b>WhatsApp: 313 752 5894</b></p>


						<p>
							<h3 style="color:navy; text-align: center;"><b>¿QUÉ NOTIFICACIONES RECIBIRÁS?</b></h3>
							1. Registro de notas.<br>
							2. Modificación de notas.<br>
							3. Registro de recuperaciones.<br>
							4. Registro de nivelaciones de fin de año.<br>
							5. Reportes disciplinarios<br>
							6. Cobros realizados por la insitución.<br>
							7. CUANDO EL DOCENTE TERMINA PERIODO, CÓMO LE QUEDÓ LA DEFINITIVA.<br>
							8. Entre otras notificaciones importanes.
						</p>

						<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
						<h2 style="color:#eb4132; text-align: center;"><b>AHORRA $39.000</b></h2>
						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1002&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

						<hr>
						<p>
							<h3 style="text-align: center;"><b>PREGUNTAS FRECUENTES</b></h3>
							<b>1. ¿Por qué tiene un costo este servicio?</b><br>
							<b>R/.</b> Este servicio lo presta una entidad externa a la Institución y el envío de email masivo, como lo es en este caso, tiene un costo adicional para poder cubrir el servidor que se encarga de realizar este envío de correos.<br><br>

							<b>2. ¿Si retiro a mi(s) acudido(s) de la Institución debo seguir pagando este valor?</b><br>
							<b>R/.</b> Definitivamente NO. Usted solo paga mientras lo desee y mientras le sea útil este servicio.<br><br>

							<b>3. ¿Si tengo algún problema con este servicio a quién debo contactar?</b><br>
							<b>R/.</b> Se puede contactar directamente con nosotros al correo <b>soporte@plataformasintia.com</b> o al número de <b>WhatsApp: 313 752 5894.</b><br><br>

							<b>4. ¿Si no quiero el servicio de notificación por correo no podré acceder, yo o mis acudidos, a la plataforma?</b><br>
							<b>R/.</b> Usted como acudiente y sus acudidos siempre tendrán acceso a la plataforma por el hecho de estar matriculados en la Institución. El servicio de notificaciones por correo electrónico es diferente.<br><br>

							<b>5. ¿El pago se puede hacer en la Institución?</b><br>
							<b>R/.</b> Por ser un servicio directo con los proveedores de la plataforma educativa, el pago del servicio sólo se acepta a través de los siguientes métodos y entidades: pago electrónico (PSE) con tarjeta débito o crédito, GANA, EFECTY, BALOTO, Trasnferencia directa a nuestra cuenta Bancolombia o Colpatria.<br><br>

							<b>6. ¿El valor del servicio cubre todos los acudidos que tenga o es por cada uno?</b><br>
							<b>R/.</b> El valor del servicio es por cada uno de los acudidos de los cuales usted quiera recibir las notificaciones al correo electrónico.<br><br>
						</p>

						<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1003&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

						<hr>
						<p style="font-size:8px;">
							<h6 style="text-align: center;"><b>TÉRMINOS Y CONDICIONES</b></h6>
							<b>1.</b> Para recibir las notificaciones relacionadas con las notas debe estar paz y salvo con la Institución.<br>
							<b>2.</b> El valor del servicio es por cada uno de los acudidos de los cuales usted quiera recibir la notificación electrónica.<br>
						</p>

						<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ÚLTIMO DÍA, AÚN ESTÁS A TIEMPO</b></h1>
						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1004&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>¡ADQUIRIR SERVICIO AHORA!</b></a></p>

					';
				}

				/*
				include("../../config-general/plantilla-email-1.php");
				// Instantiation and passing `true` enables exceptions
				$mail = new PHPMailer(true);
				echo '<div style="display:none;">';
					try {
						include("../../config-general/mail.php");

						$mail->addAddress(strtolower($datosRelacionados['uss_email']), $datosRelacionados['uss_nombre']);    
						//$mail->addAddress('tecmejia2010@gmail.com', 'Plataforma SINTIA');

						// Attachments
						//$mail->addAttachment('files/archivos/'.$ficha, 'FICHA');    // Optional name

						// Content
						$mail->isHTML(true);                                  // Set email format to HTML
						$mail->Subject = 'Nota registrada para '.strtoupper($datosRelacionados["mat_nombres"]);
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
		
	}else{
		if($_POST["notaAnterior"]==""){$_POST["notaAnterior"] = "0.0";}
		
		mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1, cal_nota_anterior='".$_POST["notaAnterior"]."', cal_tipo=1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'");
		
		
		//Si la institución autoriza el envío de mensajes
		if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
			if($datosRelacionados["mat_notificacion1"]==1){
				//INSERTAR CORREO PARA ENVIAR TODOS DESPUÉS
				mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_nota_anterior, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$datosRelacionados["car_id"]."', '".$_POST["codNota"]."', '".$_POST["nota"]."', 2, now(), 0, '".$_POST["notaAnterior"]."', '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')");
				

				//INICIO ENVÍO DE MENSAJE
				$tituloMsj = "¡MODIFICACIÓN DE NOTA PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
				$bgTitulo = "#4086f4";
				$contenidoMsj = '
					<p>
						Hola <b>'.strtoupper($datosRelacionados["uss_nombre"]).'</b>, te informamos que fue modificada una nota para el estudiante <b>'.strtoupper($datosRelacionados["mat_nombres"]).'</b>!<br>
						Estos son los datos relacionados:<br>
						<b>ESTUDIANTE:</b> '.strtoupper($datosRelacionados["mat_primer_apellido"]." ".$datosRelacionados["mat_segundo_apellido"]." ".$datosRelacionados["mat_nombres"]).'<br>
						<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
						<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
						<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
						<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
						<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
						<b>NOTA ANTERIOR:</b> '.$_POST["notaAnterior"].'<br>
						<b>NUEVA NOTA:</b> '.$_POST["nota"].'<br>
					</p>

					<p>
						<h3 style="color:navy; text-align: center;"><b>ACUDIENTE PREMIUM SINTIA</b></h3>
						Usted está recibiendo esta notificación porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
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
	$mensajeNot = 'La nota se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones
if($_POST["operacion"]==2){
	if($num==0){
		mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_observaciones, cal_id_actividad)VALUES('".$_POST["codEst"]."','".mysqli_real_escape_string($conexion,$_POST["nota"])."','".$_POST["codNota"]."')");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'");
		
	}else{
		mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_observaciones='".mysqli_real_escape_string($conexion,$_POST["nota"])."' WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'");
		
	}
	$mensajeNot = 'La observación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para la misma nota para todos los estudiantes
if($_POST["operacion"]==3){	
	$consultaE = mysqli_query($conexion, "SELECT academico_matriculas.mat_id FROM academico_matriculas
	WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
	
	
	$accionBD = 0;
	$datosInsert = '';
	$datosUpdate = '';
	$datosDelete = '';
	
	while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
		$consultaNumE=mysqli_query($conexion, "SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
		WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$estudiantes['mat_id']."'");
		$numE = mysqli_num_rows($consultaNumE);
		
		if($numE==0){
			$accionBD = 1;
			$datosDelete .="cal_id_estudiante='".$estudiantes['mat_id']."' OR ";
			$datosInsert .="('".$estudiantes['mat_id']."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0),";
		}else{
			$accionBD = 2;
			$datosUpdate .="cal_id_estudiante='".$estudiantes['mat_id']."' OR ";
		}
	}
	
	if($accionBD==1){
		$datosInsert = substr($datosInsert,0,-1);
		$datosDelete = substr($datosDelete,0,-4);
		
		mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND (".$datosDelete.")");
		
		
		mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES
		".$datosInsert."
		");
		
		//echo "Este es:". $idNotify = mysql_insert_id(); exit();
	}
	
	if($accionBD==2){
		$datosUpdate = substr($datosUpdate,0,-4);
		mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 
		WHERE cal_id_actividad='".$_POST["codNota"]."' AND (".$datosUpdate.")");
			
	}
	
	mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'");
	
	
	$mensajeNot = 'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...';
}

//Para guardar recuperaciones
if($_POST["operacion"]==4){
	$consultaNotaA=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$_POST["codEst"]." AND cal_id_actividad='".$_POST["codNota"]."'");
	$notaA = mysqli_fetch_array($consultaNotaA, MYSQLI_BOTH);
	
	mysqli_query($conexion, "INSERT INTO academico_recuperaciones_notas(rec_cod_estudiante, rec_nota, rec_id_nota, rec_fecha, rec_nota_anterior)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(),'".$notaA[3]."')");
	
	mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1, cal_nota_anterior='".$_POST["notaAnterior"]."', cal_tipo=2 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
	
	
	$mensajeNot = 'La nota de recuperación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
	
	//Si la institución autoriza el envío de mensajes
	if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
		if($datosRelacionados["mat_notificacion1"]==1){
			//INSERTAR CORREO PARA ENVIAR TODOS DESPUÉS
			mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_nota_anterior, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$datosRelacionados["car_id"]."', '".$_POST["codNota"]."', '".$_POST["nota"]."', 3, now(), 0, '".$_POST["notaAnterior"]."', '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')");
			

				//INICIO ENVÍO DE MENSAJE
				$tituloMsj = "¡REGISTRO DE RECUPERACIÓN PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
				$bgTitulo = "#4086f4";
				$contenidoMsj = '
					<p>
						Hola <b>'.strtoupper($datosRelacionados["uss_nombre"]).'</b>, te informamos que fue registrada una recuperación para el estudiante <b>'.strtoupper($datosRelacionados["mat_nombres"]).'</b>!<br>
						Estos son los datos relacionados:<br>
						<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
						<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
						<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
						<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
						<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
						<b>NOTA ANTERIOR:</b> '.$_POST["notaAnterior"].'<br>
						<b>NOTA DE RECUPERACIÓN:</b> '.$_POST["nota"].'<br>
					</p>

					<p>
						<h3 style="color:navy; text-align: center;"><b>ACUDIENTE PREMIUM SINTIA</b></h3>
						Usted está recibiendo esta notificación porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
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
						$mail->Subject = 'Nota de recuperación para '.strtoupper($datosRelacionados["mat_nombres"]);
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

//PARA NOTAS DE COMPORTAMIENTO
$consultaNumD=mysqli_query($conexion, "SELECT * FROM disiplina_nota
WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'");
$numD = mysqli_num_rows($consultaNumD);


//Para guardar notas de disciplina
if($_POST["operacion"]==5){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<$config[3]) $_POST["nota"] = $config[4];

	if($numD==0){
		mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'");
		
		mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', now(),'".$_POST["periodo"]."')");
		
	}else{
		mysqli_query($conexion, "UPDATE disiplina_nota SET dn_nota='".$_POST["nota"]."', dn_fecha=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."';");
		
	}
	$mensajeNot = 'La nota de comportamiento se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones de disciplina
if($_POST["operacion"]==6){
	if($numD==0){
		mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'");
		
		mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".mysqli_real_escape_string($conexion,$_POST["nota"])."', now(),'".$_POST["periodo"]."')");
		
		
	}else{
		mysqli_query($conexion, "UPDATE disiplina_nota SET dn_observacion='".mysqli_real_escape_string($conexion,$_POST["nota"])."', dn_fecha=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."'  AND dn_periodo='".$_POST["periodo"]."';");
		
	}
	$mensajeNot = 'La observación de comportamiento se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["codEst"]).'</b>';
}

//Para la misma nota de comportamiento para todos los estudiantes
if($_POST["operacion"]==7){
	
	$consultaE = mysqli_query($conexion, "SELECT academico_matriculas.mat_id FROM academico_matriculas
	WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
	
	
	$accionBD = 0;
	$datosInsert = '';
	$datosUpdate = '';
	$datosDelete = '';

	while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
		$consultaNumE=mysqli_query($conexion, "SELECT * FROM disiplina_nota
		WHERE dn_cod_estudiante='".$estudiantes['mat_id']."' AND dn_periodo='".$_POST["periodo"]."'");
		$numE = mysqli_num_rows($consultaNumE);
		
		if($numE==0){
			$accionBD = 1;
			$datosDelete .="dn_cod_estudiante='".$estudiantes['mat_id']."' OR ";
			$datosInsert .="('".$estudiantes['mat_id']."','".$_POST["carga"]."','".$_POST["nota"]."', now(),'".$_POST["periodo"]."'),";
		}else{
			$accionBD = 2;
			$datosUpdate .="dn_cod_estudiante='".$estudiantes['mat_id']."' OR ";
		}
	}
	
	if($accionBD==1){
		$datosInsert = substr($datosInsert,0,-1);
		$datosDelete = substr($datosDelete,0,-4);
		
		mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_periodo='".$_POST["periodo"]."' AND (".$datosDelete.")");
		
		
		mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo)VALUES
		".$datosInsert."
		");
			
	}
	
	if($accionBD==2){
		$datosUpdate = substr($datosUpdate,0,-4);
		mysqli_query($conexion, "UPDATE disiplina_nota SET dn_nota='".$_POST["nota"]."', dn_fecha=now()
		WHERE dn_periodo='".$_POST["periodo"]."' AND (".$datosUpdate.")");
			
	}
	
	
	$mensajeNot = 'Se ha guardado la misma nota de comportamiento para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...';
}
//Para guardar observaciones en el boletín de preescolar, Y TAMBIÉN EN EL DE LOS DEMÁS
if($_POST["operacion"]==8){
	$consultaNum=mysqli_query($conexion, "SELECT * FROM academico_boletin 
	WHERE bol_carga='".$_POST["carga"]."' AND bol_estudiante='".$_POST["codEst"]."' AND bol_periodo='".$_POST["periodo"]."'");
	$num = mysqli_num_rows($consultaNum);
	
	
	if($num==0){
		mysqli_query($conexion, "DELETE FROM academico_boletin WHERE bol_carga='".$_POST["carga"]."' AND bol_estudiante='".$_POST["codEst"]."' AND bol_periodo='".$_POST["periodo"]."'");
		
		mysqli_query($conexion, "INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_tipo, bol_observaciones_boletin, bol_fecha_registro, bol_actualizaciones)VALUES('".$_POST["carga"]."', '".$_POST["codEst"]."', '".$_POST["periodo"]."', 1, '".mysqli_real_escape_string($conexion,$_POST["nota"])."', now(), 0)");
		
	}else{
		mysqli_query($conexion, "UPDATE academico_boletin SET bol_observaciones_boletin='".mysqli_real_escape_string($conexion,$_POST["nota"])."', bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now() WHERE bol_carga='".$_POST["carga"]."' AND bol_estudiante='".$_POST["codEst"]."' AND bol_periodo='".$_POST["periodo"]."'");
		
	}
	$mensajeNot = 'La observación para el boletín de este periodo se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar recuperaciones de los INDICADORES - lo pidió el MAXTRUMMER. Y AHORA ICOLVEN TAMBIÉN LO USA.
if($_POST["operacion"]==9){
	
	//Consultamos si tiene registros en el boletín
	$consultaBoletinDatos=mysqli_query($conexion, "SELECT * FROM academico_boletin 
	WHERE bol_carga='".$_POST["carga"]."' AND bol_periodo='".$_POST["periodo"]."' AND bol_estudiante='".$_POST["codEst"]."'");
	$boletinDatos = mysqli_fetch_array($consultaBoletinDatos, MYSQLI_BOTH);
	
	$caso = 1; //Inserta la nueva definitiva del indicador normal
	if($boletinDatos['bol_id']==""){
 		$caso = 2;
		$mensajeNot = 'El estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b> no presenta registros en el boletín actualmente para este periodo, en esta asignatura.';
		$heading = 'No se generó ningún cambio';
		$tipo = 'danger';
		$icon = 'error';
	}
	
	
	if($caso == 1){
		$consultaIndicador=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
		WHERE ipc_indicador='".$_POST["codNota"]."' AND ipc_carga='".$_POST["carga"]."' AND ipc_periodo='".$_POST["periodo"]."'");
		$indicador = mysqli_fetch_array($consultaIndicador, MYSQLI_BOTH);
		$valorIndicador = ($indicador['ipc_valor']/100);
		$rindNotaActual = ($_POST["nota"] * $valorIndicador);
		$consultaNum=mysqli_query($conexion, "SELECT * FROM academico_indicadores_recuperacion 
		WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_indicador='".$_POST["codNota"]."'");
		$num = mysqli_num_rows($consultaNum);
		

		if($num==0){
			mysqli_query($conexion, "DELETE FROM academico_indicadores_recuperacion WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_indicador='".$_POST["codNota"]."'");
			

			mysqli_query($conexion, "INSERT INTO academico_indicadores_recuperacion(rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_nota_actual, rind_valor_indicador_registro)VALUES(now(), '".$_POST["codEst"]."', '".$_POST["carga"]."', '".$_POST["nota"]."', '".$_POST["codNota"]."', '".$_POST["periodo"]."', 1, '".$rindNotaActual."', '".$indicador['ipc_valor']."')");
			
		}else{
			if($_POST["notaAnterior"]==""){$_POST["notaAnterior"] = "0.0";}
			
			mysqli_query($conexion, "UPDATE academico_indicadores_recuperacion SET rind_nota='".$_POST["nota"]."', rind_nota_anterior='".$_POST["notaAnterior"]."', rind_actualizaciones=rind_actualizaciones+1, rind_ultima_actualizacion=now(), rind_nota_actual='".$rindNotaActual."', rind_tipo_ultima_actualizacion=2, rind_valor_indicador_actualizacion='".$indicador['ipc_valor']."' WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_indicador='".$_POST["codNota"]."'");
			
		}
		
		//Actualizamos la nota actual a los que la tengan nula.
		mysqli_query($conexion, "UPDATE academico_indicadores_recuperacion SET rind_nota_actual=rind_nota_original
		WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_nota_actual IS NULL AND rind_nota_original=rind_nota
		");
		
		
		//Se suman los decimales de todos los indicadores para obtener la definitiva de la asignatura
		$consultaRecuperacionIndicador=mysqli_query($conexion, "SELECT SUM(rind_nota_actual) FROM academico_indicadores_recuperacion 
		WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."'");
		$recuperacionIndicador = mysqli_fetch_array($consultaRecuperacionIndicador, MYSQLI_BOTH);
		
		
		$notaDefIndicador = round($recuperacionIndicador[0],1);



		//if($notaDefIndicador == $boletinDatos['bol_nota']){
			mysqli_query($conexion, "UPDATE academico_boletin SET bol_nota_anterior=bol_nota, bol_nota='".$notaDefIndicador."', bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now(), bol_nota_indicadores='".$notaDefIndicador."', bol_tipo=3, bol_observaciones='Actualizada desde el indicador.' 
			WHERE bol_carga='".$_POST["carga"]."' AND bol_periodo='".$_POST["periodo"]."' AND bol_estudiante='".$_POST["codEst"]."'");
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
			
			$mensajeNot = 'La recuperación del indicador de este periodo se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>. La nota definitiva de la asignatura ahora es <b>'.round($recuperacionIndicador[0],1)."</b>.";
			$heading = 'Cambios guardados';
			$tipo = 'success';
			$icon = 'success';
		//}else{
			//$mensajeNot = 'No es posible registrar una definitiva de la asignatura igual a la que ya existe. Solo se guardó la recuperación del inidicador.';
			//$heading = 'Este cambio no afectó en la definitiva';
			//$tipo = 'danger';
			//$icon = 'error';
		//}
		
	}
}
?>

<?php 
if($_POST["operacion"]==9){
?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: '<?=$heading;?>',  
		text: '<?=$mensajeNot;?>',
		position: 'top-right',
		loaderBg:'#ff6849',
		icon: '<?=$icon;?>',
		hideAfter: 5000, 
		stack: 6
	});
}
setTimeout ("notifica()", 100);
</script>

<div class="alert alert-<?=$tipo;?>">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$mensajeNot;?>
</div>
<?php }

//PARA ASPECTOS ESTUDIANTILES
$consultaNumD=mysqli_query($conexion, "SELECT * FROM disiplina_nota
WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'");
$numD = mysqli_num_rows($consultaNumD);


//Para guardar ASPECTOS ESTUDIANTILES
if($_POST["operacion"]==10){
	
	if($numD==0){
		mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'");
		
		mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_aspecto_academico, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', '".$_POST["periodo"]."')");
		
	}else{
		mysqli_query($conexion, "UPDATE disiplina_nota SET dn_aspecto_academico='".$_POST["nota"]."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."'  AND dn_periodo='".$_POST["periodo"]."';");
		
	}
	$mensajeNot = 'El aspecto academico se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["codEst"]).'</b>';
}

if($_POST["operacion"]==11){
	
	if($numD==0){
		mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'");
		
		mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_aspecto_convivencial, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', '".$_POST["periodo"]."')");
		
	}else{
		mysqli_query($conexion, "UPDATE disiplina_nota SET dn_aspecto_convivencial='".$_POST["nota"]."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."';");
		
	}
	$mensajeNot = 'El aspecto convivencial se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}


else{?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: 'Cambios guardados',  
		text: '<?=$mensajeNot;?>',
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
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$mensajeNot;?>
</div>

<?php }?>


<?php 
if($_POST["operacion"]==3 or $_POST["operacion"]==7){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',5000);
	</script>
<?php
}
?>