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
$num = mysql_num_rows(mysql_query("SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$_POST["codEst"]."'",$conexion));
if(mysql_errno()!=0){echo mysql_error(); exit();}

$datosRelacionados = mysql_fetch_array(mysql_query("SELECT * FROM academico_actividades 
INNER JOIN academico_cargas ON car_id=act_id_carga
INNER JOIN academico_materias AS mate ON mate.mat_id=car_materia
INNER JOIN academico_matriculas AS matri ON matri.mat_id='".$_POST["codEst"]."'
INNER JOIN usuarios ON uss_id=mat_acudiente
INNER JOIN academico_grados AS gra ON gra.gra_id=matri.mat_grado
WHERE act_id='".$_POST["codNota"]."'
",$conexion));
if(mysql_errno()!=0){echo mysql_error(); exit();}

$docente = mysql_fetch_array(mysql_query("SELECT * FROM usuarios WHERE uss_id='".$datosRelacionados['car_docente']."'",$conexion));
if(mysql_errno()!=0){echo mysql_error(); exit();}

$mensajeNot = 'Hubo un error al guardar las cambios';

//Para guardar notas
if($_POST["operacion"]==1){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<$config[3]) $_POST["nota"] = $config[3];

	if($num==0){
		mysql_query("DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0)",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
		//Si la instituci??n autoriza el env??o de mensajes
		if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
			if($datosRelacionados["mat_notificacion1"]==1){

				//INSERTAR CORREO PARA ENVIAR TODOS DESPU??S
				mysql_query("INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$datosRelacionados["car_id"]."', '".$_POST["codNota"]."', '".$_POST["nota"]."', 1, now(), 0, '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')",$conexion);
				if(mysql_errno()!=0){echo mysql_error(); exit();}

				//INICIO ENV??O DE MENSAJE
				$tituloMsj = "??REGISTRO DE NOTA PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
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
							Usted est?? recibiendo esta notificaci??n porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
							Gracias por haber adquirido el servicio de notificaciones por correo.
						</p>
					';	
				}
				else{	
					$contenidoMsj .= '
						<p>
							<h3 style="color:navy; text-align: center;"><b>MUY IMPORTANTE</b></h3>
							Este servicio de <b>notifiaciones por correo</b> lo hemos otorgado gratuitamente durante el mes de <b>SEPTIEMBRE DE 2019</b> para que usted vea sus beneficios.<br>
							Si desea adquirir este servicio de forma permanente durante todo el resto de este a??o 2019 y todo el a??o 2020, aproveche el <b>65% DE DESCUENTO</b> que hay ahora, y adquieralo por la m??dica suma de <b>$21.000</b>.<br>
							Recuerde que es por todo el resto de este a??o y todo el a??o siguiente.<br>
							<b>A PARTIR DE MA??ANA YA VALDR?? $60.000.</b>
							<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ??LTIMO D??A, A??N EST??S A TIEMPO</b></h1>
						</p>


						<h2 style="color:#eb4132; text-align: center;"><b>AHORRA $39.000</b></h2>
						<p style="text-align: center;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1001&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank"><img src="https://plataformasintia.com/files-general/email/ultimosdias.jpg"></a></p>



						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1000&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>??ADQUIRIR SERVICIO AHORA!</b></a></p>

						<p>
						?? para su <b>mayor facilidad</b> puede hacer una transferencia, sin costo adicional, a nuestra cuenta:<br>
						<img src="https://plataformasintia.com/files-general/iconos/bacolombia.png" width="40" align="middle"> Ahorros Bancolombia N??mero: <b>431-565882-54</b>.<br>
						<img src="https://plataformasintia.com/files-general/iconos/colpatria.png" width="40" align="middle"> Ahorros Colpatria N??mero: <b>789-20112-53</b>.<br>
						Si desea puede escribirnos al <b>WhatsApp: 313 752 5894</b> para brindarle mayor informaci??n.
						</p>

						<p>Para activar su servicio de inmediato, recuerde enviar el soporte de pago, o el pantallazo(si hace su pago en l??nea), al correo electr??nico <b>pagos@plataformasintia.com</b>. o al <b>WhatsApp: 313 752 5894</b></p>


						<p>
							<h3 style="color:navy; text-align: center;"><b>??QU?? NOTIFICACIONES RECIBIR??S?</b></h3>
							1. Registro de notas.<br>
							2. Modificaci??n de notas.<br>
							3. Registro de recuperaciones.<br>
							4. Registro de nivelaciones de fin de a??o.<br>
							5. Reportes disciplinarios<br>
							6. Cobros realizados por la insituci??n.<br>
							7. CUANDO EL DOCENTE TERMINA PERIODO, C??MO LE QUED?? LA DEFINITIVA.<br>
							8. Entre otras notificaciones importanes.
						</p>

						<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ??LTIMO D??A, A??N EST??S A TIEMPO</b></h1>
						<h2 style="color:#eb4132; text-align: center;"><b>AHORRA $39.000</b></h2>
						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1002&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>??ADQUIRIR SERVICIO AHORA!</b></a></p>

						<hr>
						<p>
							<h3 style="text-align: center;"><b>PREGUNTAS FRECUENTES</b></h3>
							<b>1. ??Por qu?? tiene un costo este servicio?</b><br>
							<b>R/.</b> Este servicio lo presta una entidad externa a la Instituci??n y el env??o de email masivo, como lo es en este caso, tiene un costo adicional para poder cubrir el servidor que se encarga de realizar este env??o de correos.<br><br>

							<b>2. ??Si retiro a mi(s) acudido(s) de la Instituci??n debo seguir pagando este valor?</b><br>
							<b>R/.</b> Definitivamente NO. Usted solo paga mientras lo desee y mientras le sea ??til este servicio.<br><br>

							<b>3. ??Si tengo alg??n problema con este servicio a qui??n debo contactar?</b><br>
							<b>R/.</b> Se puede contactar directamente con nosotros al correo <b>soporte@plataformasintia.com</b> o al n??mero de <b>WhatsApp: 313 752 5894.</b><br><br>

							<b>4. ??Si no quiero el servicio de notificaci??n por correo no podr?? acceder, yo o mis acudidos, a la plataforma?</b><br>
							<b>R/.</b> Usted como acudiente y sus acudidos siempre tendr??n acceso a la plataforma por el hecho de estar matriculados en la Instituci??n. El servicio de notificaciones por correo electr??nico es diferente.<br><br>

							<b>5. ??El pago se puede hacer en la Instituci??n?</b><br>
							<b>R/.</b> Por ser un servicio directo con los proveedores de la plataforma educativa, el pago del servicio s??lo se acepta a trav??s de los siguientes m??todos y entidades: pago electr??nico (PSE) con tarjeta d??bito o cr??dito, GANA, EFECTY, BALOTO, Trasnferencia directa a nuestra cuenta Bancolombia o Colpatria.<br><br>

							<b>6. ??El valor del servicio cubre todos los acudidos que tenga o es por cada uno?</b><br>
							<b>R/.</b> El valor del servicio es por cada uno de los acudidos de los cuales usted quiera recibir las notificaciones al correo electr??nico.<br><br>
						</p>

						<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ??LTIMO D??A, A??N EST??S A TIEMPO</b></h1>
						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1003&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>??ADQUIRIR SERVICIO AHORA!</b></a></p>

						<hr>
						<p style="font-size:8px;">
							<h6 style="text-align: center;"><b>T??RMINOS Y CONDICIONES</b></h6>
							<b>1.</b> Para recibir las notificaciones relacionadas con las notas debe estar paz y salvo con la Instituci??n.<br>
							<b>2.</b> El valor del servicio es por cada uno de los acudidos de los cuales usted quiera recibir la notificaci??n electr??nica.<br>
						</p>

						<h1 style="color:#eb4132; text-align: center;"><b>HOY ES EL ??LTIMO D??A, A??N EST??S A TIEMPO</b></h1>
						<p style="text-align: center; font-size:18px;"><a href="https://plataformasintia.com/icolven/v2.0/compartido/guardar.php?get=14&idPag=1000&idPub=66&idUb=1004&usrAct='.$datosRelacionados["uss_id"].'&idIns='.$config['conf_id_institucion'].'&url=https://payco.link/240384" target="_blank" style="color:#eb4132;"><b>??ADQUIRIR SERVICIO AHORA!</b></a></p>

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
				//FIN ENV??O DE MENSAJE
				*/

			}
		}
		
	}else{
		if($_POST["notaAnterior"]==""){$_POST["notaAnterior"] = "0.0";}
		
		mysql_query("UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1, cal_nota_anterior='".$_POST["notaAnterior"]."', cal_tipo=1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
		//Si la instituci??n autoriza el env??o de mensajes
		if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
			if($datosRelacionados["mat_notificacion1"]==1){
				//INSERTAR CORREO PARA ENVIAR TODOS DESPU??S
				mysql_query("INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_nota_anterior, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$datosRelacionados["car_id"]."', '".$_POST["codNota"]."', '".$_POST["nota"]."', 2, now(), 0, '".$_POST["notaAnterior"]."', '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')",$conexion);
				if(mysql_errno()!=0){echo mysql_error(); exit();}

				//INICIO ENV??O DE MENSAJE
				$tituloMsj = "??MODIFICACI??N DE NOTA PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
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
						Usted est?? recibiendo esta notificaci??n porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
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
				//FIN ENV??O DE MENSAJE
				*/
			}
		}
	}
	$mensajeNot = 'La nota se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones
if($_POST["operacion"]==2){
	if($num==0){
		mysql_query("DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO academico_calificaciones(cal_id_estudiante, cal_observaciones, cal_id_actividad)VALUES('".$_POST["codEst"]."','".mysql_real_escape_string($_POST["nota"])."','".$_POST["codNota"]."')",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE academico_calificaciones SET cal_observaciones='".mysql_real_escape_string($_POST["nota"])."' WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}
	$mensajeNot = 'La observaci??n se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para la misma nota para todos los estudiantes
if($_POST["operacion"]==3){	
	$consultaE = mysql_query("SELECT academico_matriculas.mat_id FROM academico_matriculas
	WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	
	$accionBD = 0;
	$datosInsert = '';
	$datosUpdate = '';
	$datosDelete = '';
	
	while($estudiantes = mysql_fetch_array($consultaE)){
		$numE = mysql_num_rows(mysql_query("SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
		WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$estudiantes['mat_id']."'",$conexion));
		if(mysql_errno()!=0){echo mysql_error(); exit();}
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
		
		mysql_query("DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND (".$datosDelete.")",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
		mysql_query("INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES
		".$datosInsert."
		",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		//echo "Este es:". $idNotify = mysql_insert_id(); exit();
	}
	
	if($accionBD==2){
		$datosUpdate = substr($datosUpdate,0,-4);
		mysql_query("UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 
		WHERE cal_id_actividad='".$_POST["codNota"]."' AND (".$datosUpdate.")",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}	
	}
	
	mysql_query("UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	
	$mensajeNot = 'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La p??gina se actualizar?? en unos segundos para que vea los cambios...';
}

//Para guardar recuperaciones
if($_POST["operacion"]==4){
	$notaA = mysql_fetch_array(mysql_query("SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$_POST["codEst"]." AND cal_id_actividad='".$_POST["codNota"]."'",$conexion));
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	mysql_query("INSERT INTO academico_recuperaciones_notas(rec_cod_estudiante, rec_nota, rec_id_nota, rec_fecha, rec_nota_anterior)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(),'".$notaA[3]."')",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	mysql_query("UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1, cal_nota_anterior='".$_POST["notaAnterior"]."', cal_tipo=2 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	
	$mensajeNot = 'La nota de recuperaci??n se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
	
	//Si la instituci??n autoriza el env??o de mensajes
	if($datosUnicosInstitucion['ins_notificaciones_acudientes']==1){
		if($datosRelacionados["mat_notificacion1"]==1){
			//INSERTAR CORREO PARA ENVIAR TODOS DESPU??S
			mysql_query("INSERT INTO ".$baseDatosServicios.".correos(corr_institucion, corr_carga, corr_actividad, corr_nota, corr_tipo, corr_fecha_registro, corr_estado, corr_nota_anterior, corr_usuario, corr_estudiante)VALUES('".$config['conf_id_institucion']."', '".$datosRelacionados["car_id"]."', '".$_POST["codNota"]."', '".$_POST["nota"]."', 3, now(), 0, '".$_POST["notaAnterior"]."', '".$datosRelacionados["uss_id"]."', '".$_POST["codEst"]."')",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}

				//INICIO ENV??O DE MENSAJE
				$tituloMsj = "??REGISTRO DE RECUPERACI??N PARA <b>".strtoupper($datosRelacionados["mat_nombres"])."</b>!";
				$bgTitulo = "#4086f4";
				$contenidoMsj = '
					<p>
						Hola <b>'.strtoupper($datosRelacionados["uss_nombre"]).'</b>, te informamos que fue registrada una recuperaci??n para el estudiante <b>'.strtoupper($datosRelacionados["mat_nombres"]).'</b>!<br>
						Estos son los datos relacionados:<br>
						<b>CURSO:</b> '.strtoupper($datosRelacionados["gra_nombre"]).'<br>
						<b>ASIGNATURA:</b> '.strtoupper($datosRelacionados["mat_nombre"]).'<br>
						<b>DOCENTE:</b> '.strtoupper($docente["uss_nombre"]).'<br>
						<b>PERIODO:</b> '.$datosRelacionados["act_periodo"].'<br>
						<b>ACTIVIDAD:</b> '.strtoupper($datosRelacionados["act_descripcion"]).' ('.$datosRelacionados["act_valor"].'%)<br>
						<b>NOTA ANTERIOR:</b> '.$_POST["notaAnterior"].'<br>
						<b>NOTA DE RECUPERACI??N:</b> '.$_POST["nota"].'<br>
					</p>

					<p>
						<h3 style="color:navy; text-align: center;"><b>ACUDIENTE PREMIUM SINTIA</b></h3>
						Usted est?? recibiendo esta notificaci??n porque hace parte del grupo de los <b>ACUDIENTES PREMIUM SINTIA</b>.<br>
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
						$mail->Subject = 'Nota de recuperaci??n para '.strtoupper($datosRelacionados["mat_nombres"]);
						$mail->Body = $fin;
						$mail->CharSet = 'UTF-8';

						$mail->send();
						echo 'Mensaje enviado correctamente.';
					} catch (Exception $e) {echo "Error: {$mail->ErrorInfo}"; exit();}
				echo '</div>';
				//FIN ENV??O DE MENSAJE
				*/
			}
		}
}

//PARA NOTAS DE COMPORTAMIENTO
$numD = mysql_num_rows( mysql_query("SELECT * FROM disiplina_nota
WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'",$conexion));
if(mysql_errno()!=0){echo mysql_error(); exit();}

//Para guardar notas de disciplina
if($_POST["operacion"]==5){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<$config[3]) $_POST["nota"] = $config[4];

	if($numD==0){
		mysql_query("DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', now(),'".$_POST["periodo"]."')",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE disiplina_nota SET dn_nota='".$_POST["nota"]."', dn_fecha=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."';",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}
	$mensajeNot = 'La nota de comportamiento se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones de disciplina
if($_POST["operacion"]==6){
	if($numD==0){
		mysql_query("DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".mysql_real_escape_string($_POST["nota"])."', now(),'".$_POST["periodo"]."')",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
	}else{
		mysql_query("UPDATE disiplina_nota SET dn_observacion='".mysql_real_escape_string($_POST["nota"])."', dn_fecha=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."'  AND dn_periodo='".$_POST["periodo"]."';",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}
	$mensajeNot = 'La observaci??n de comportamiento se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["codEst"]).'</b>';
}

//Para la misma nota de comportamiento para todos los estudiantes
if($_POST["operacion"]==7){
	
	$consultaE = mysql_query("SELECT academico_matriculas.mat_id FROM academico_matriculas
	WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	
	$accionBD = 0;
	$datosInsert = '';
	$datosUpdate = '';
	$datosDelete = '';

	while($estudiantes = mysql_fetch_array($consultaE)){
		$numE = mysql_num_rows(mysql_query("SELECT * FROM disiplina_nota
		WHERE dn_cod_estudiante='".$estudiantes['mat_id']."' AND dn_periodo='".$_POST["periodo"]."'",$conexion));
		if(mysql_errno()!=0){echo mysql_error(); exit();}
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
		
		mysql_query("DELETE FROM disiplina_nota WHERE dn_periodo='".$_POST["periodo"]."' AND (".$datosDelete.")",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
		mysql_query("INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo)VALUES
		".$datosInsert."
		",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}	
	}
	
	if($accionBD==2){
		$datosUpdate = substr($datosUpdate,0,-4);
		mysql_query("UPDATE disiplina_nota SET dn_nota='".$_POST["nota"]."', dn_fecha=now()
		WHERE dn_periodo='".$_POST["periodo"]."' AND (".$datosUpdate.")",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}	
	}
	
	
	$mensajeNot = 'Se ha guardado la misma nota de comportamiento para todos los estudiantes en esta actividad. La p??gina se actualizar?? en unos segundos para que vea los cambios...';
}
//Para guardar observaciones en el bolet??n de preescolar, Y TAMBI??N EN EL DE LOS DEM??S
if($_POST["operacion"]==8){
	
	$num = mysql_num_rows(mysql_query("SELECT * FROM academico_boletin 
	WHERE bol_carga='".$_POST["carga"]."' AND bol_estudiante='".$_POST["codEst"]."' AND bol_periodo='".$_POST["periodo"]."'",$conexion));
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	
	if($num==0){
		mysql_query("DELETE FROM academico_boletin WHERE bol_carga='".$_POST["carga"]."' AND bol_estudiante='".$_POST["codEst"]."' AND bol_periodo='".$_POST["periodo"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO academico_boletin(bol_carga, bol_estudiante, bol_periodo, bol_tipo, bol_observaciones_boletin, bol_fecha_registro, bol_actualizaciones)VALUES('".$_POST["carga"]."', '".$_POST["codEst"]."', '".$_POST["periodo"]."', 1, '".mysql_real_escape_string($_POST["nota"])."', now(), 0)",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE academico_boletin SET bol_observaciones_boletin='".mysql_real_escape_string($_POST["nota"])."', bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now() WHERE bol_carga='".$_POST["carga"]."' AND bol_estudiante='".$_POST["codEst"]."' AND bol_periodo='".$_POST["periodo"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}
	$mensajeNot = 'La observaci??n para el bolet??n de este periodo se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar recuperaciones de los INDICADORES - lo pidi?? el MAXTRUMMER. Y AHORA ICOLVEN TAMBI??N LO USA.
if($_POST["operacion"]==9){
	
	//Consultamos si tiene registros en el bolet??n
	$boletinDatos = mysql_fetch_array(mysql_query("SELECT * FROM academico_boletin 
	WHERE bol_carga='".$_POST["carga"]."' AND bol_periodo='".$_POST["periodo"]."' AND bol_estudiante='".$_POST["codEst"]."'",$conexion));
	
	$caso = 1; //Inserta la nueva definitiva del indicador normal
	if($boletinDatos['bol_id']==""){
 		$caso = 2;
		$mensajeNot = 'El estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b> no presenta registros en el bolet??n actualmente para este periodo, en esta asignatura.';
		$heading = 'No se gener?? ning??n cambio';
		$tipo = 'danger';
		$icon = 'error';
	}
	
	
	if($caso == 1){
		$indicador = mysql_fetch_array(mysql_query("SELECT * FROM academico_indicadores_carga 
		WHERE ipc_indicador='".$_POST["codNota"]."' AND ipc_carga='".$_POST["carga"]."' AND ipc_periodo='".$_POST["periodo"]."'",$conexion));
		$valorIndicador = ($indicador['ipc_valor']/100);
		$rindNotaActual = ($_POST["nota"] * $valorIndicador);

		$num = mysql_num_rows(mysql_query("SELECT * FROM academico_indicadores_recuperacion 
		WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_indicador='".$_POST["codNota"]."'",$conexion));
		if(mysql_errno()!=0){echo mysql_error(); exit();}

		if($num==0){
			mysql_query("DELETE FROM academico_indicadores_recuperacion WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_indicador='".$_POST["codNota"]."'",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}

			mysql_query("INSERT INTO academico_indicadores_recuperacion(rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_nota_actual, rind_valor_indicador_registro)VALUES(now(), '".$_POST["codEst"]."', '".$_POST["carga"]."', '".$_POST["nota"]."', '".$_POST["codNota"]."', '".$_POST["periodo"]."', 1, '".$rindNotaActual."', '".$indicador['ipc_valor']."')",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
		}else{
			if($_POST["notaAnterior"]==""){$_POST["notaAnterior"] = "0.0";}
			
			mysql_query("UPDATE academico_indicadores_recuperacion SET rind_nota='".$_POST["nota"]."', rind_nota_anterior='".$_POST["notaAnterior"]."', rind_actualizaciones=rind_actualizaciones+1, rind_ultima_actualizacion=now(), rind_nota_actual='".$rindNotaActual."', rind_tipo_ultima_actualizacion=2, rind_valor_indicador_actualizacion='".$indicador['ipc_valor']."' WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_indicador='".$_POST["codNota"]."'",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
		}
		
		//Actualizamos la nota actual a los que la tengan nula.
		mysql_query("UPDATE academico_indicadores_recuperacion SET rind_nota_actual=rind_nota_original
		WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."' AND rind_nota_actual IS NULL AND rind_nota_original=rind_nota
		",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
		//Se suman los decimales de todos los indicadores para obtener la definitiva de la asignatura
		$recuperacionIndicador = mysql_fetch_array(mysql_query("SELECT SUM(rind_nota_actual) FROM academico_indicadores_recuperacion 
		WHERE rind_carga='".$_POST["carga"]."' AND rind_estudiante='".$_POST["codEst"]."' AND rind_periodo='".$_POST["periodo"]."'",$conexion));
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		
		$notaDefIndicador = round($recuperacionIndicador[0],1);



		//if($notaDefIndicador == $boletinDatos['bol_nota']){
			mysql_query("UPDATE academico_boletin SET bol_nota_anterior=bol_nota, bol_nota='".$notaDefIndicador."', bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now(), bol_nota_indicadores='".$notaDefIndicador."', bol_tipo=3, bol_observaciones='Actualizada desde el indicador.' 
			WHERE bol_carga='".$_POST["carga"]."' AND bol_periodo='".$_POST["periodo"]."' AND bol_estudiante='".$_POST["codEst"]."'",$conexion);
			$lineaError = __LINE__;
			include("../compartido/reporte-errores.php");
			
			$mensajeNot = 'La recuperaci??n del indicador de este periodo se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>. La nota definitiva de la asignatura ahora es <b>'.round($recuperacionIndicador[0],1)."</b>.";
			$heading = 'Cambios guardados';
			$tipo = 'success';
			$icon = 'success';
		//}else{
			//$mensajeNot = 'No es posible registrar una definitiva de la asignatura igual a la que ya existe. Solo se guard?? la recuperaci??n del inidicador.';
			//$heading = 'Este cambio no afect?? en la definitiva';
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
$numD = mysql_num_rows( mysql_query("SELECT * FROM disiplina_nota
WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'",$conexion));
if(mysql_errno()!=0){echo mysql_error(); exit();}

//Para guardar ASPECTOS ESTUDIANTILES
if($_POST["operacion"]==10){
	
	if($numD==0){
		mysql_query("DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_aspecto_academico, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', '".$_POST["periodo"]."')",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE disiplina_nota SET dn_aspecto_academico='".$_POST["nota"]."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."'  AND dn_periodo='".$_POST["periodo"]."';",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}
	$mensajeNot = 'El aspecto academico se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["codEst"]).'</b>';
}

if($_POST["operacion"]==11){
	
	if($numD==0){
		mysql_query("DELETE FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_aspecto_convivencial, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', '".$_POST["periodo"]."')",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE disiplina_nota SET dn_aspecto_convivencial='".$_POST["nota"]."', dn_fecha_aspecto=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_periodo='".$_POST["periodo"]."';",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
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