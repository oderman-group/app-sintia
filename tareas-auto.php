<?php
include("conexion.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once ROOT_PATH."/main-app/class/Asignaturas.php";
require_once ROOT_PATH."/main-app/class/Grupos.php";
require_once(ROOT_PATH."/main-app/class/categoriasNotas.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Areas.php");
$year=date("Y");


//=====CORREOS PARA LOS INTERESADOS EN SINTIA - DEMO=====//
$correosDemo = mysqli_query($conexion,"SELECT demo_id, DATEDIFF(now(), demo_fecha_ingreso) AS fecha, demo_usuario, demo_correo_enviado, demo_institucion, uss_nombre, uss_apellido1, uss_email, uss_ultimo_ingreso, ins_fecha_renovacion FROM demo
INNER JOIN ".BD_ADMIN.".instituciones ON ins_id=demo_institucion
INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=demo_usuario AND uss.institucion=demo_institucion AND uss.year={$year} 
WHERE demo_correo_enviado<=5 AND demo_nocorreos=0");




$paraEnviar = 0;
while($cDemo = mysqli_fetch_array($correosDemo, MYSQLI_BOTH)){
	//Correo 2/ 1er día/ ¿Necesitas Ayuda?
	if($cDemo['fecha']==1){
		$tituloMsj = "¿Requiere una cita virtual para aclarar dudas?";
		$bgTitulo = "#31a952";
		$contenidoMsj = '
			<p>
				Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
				Mi nombre es Jhon Mejía, su asesor personal.<br>
				Si lo requiere podemos hacer una demostración virtual de la plataforma.<br>
				Sólo tiene que responder a este correo o escribir directamente a mi Whatsapp para programar la cita.<br><br>
				
				<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
				<a href="https://api.whatsapp.com/send?phone=573113932970&text=Hola, quisiera una demostración virtual de la Plataforma SINTIA." target="_blank" style="background-color: #31A952; color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 311 393 2970</a></div><br><br>
				
				Gracias por su atención a este mensaje.
			</p>	
			<p>
				Cordialmente,<br><br>
				
				Jhon Mejía M.<br>
				<b>Email:</b> info@plataformasintia.com<br>
				<b>WhatsApp:</b> (+57) 311 393 2970<br>
			</p>
		';
		$paraEnviar = 1;
	}
	
	//Si ya ha ingresado a la plataforma
	if(!empty($cDemo['uss_ultimo_ingreso'])){
		//Correo 3/ 5 días/ ¿Cómo te ha ido?
		if($cDemo['fecha']==5){
			$tituloMsj = "¿Cómo le ha ido con la plataforma SINTIA?";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
					Como ya lo sabe, mi nombre es Jhon Mejía, soy su asesor personal.<br>
					Quería preguntarle cómo le ha ido con la plataforma, y recordarle que estoy a su disposición.<br>
					Cualquier cosa que requiera me puede escribir a este correo o directamente a mi Whatsapp.<br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 311 393 2970<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 4/ 10 días/ Pronto vence, llamada a la acción
		if($cDemo['fecha']==10){
			$tituloMsj = "En 5 días terminará la versión de prueba.";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
					Esperamos que haya podido disfrutar la versión de prueba de la plataforma SINTIA<br>
					Por ahora quedan sólo 5 días para que la versión de prueba termine.<br>
					¿Ha podido tomar usted o la Institución alguna decisión sobre el plan que desean?<br>
					Quedo atento a su respuesta.<br><br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573113932970&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="background-color: #31A952; color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 311 393 2970</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 311 393 2970<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 5/ 10 días/ Hoy cence, llamada a la acción más fuerte
		if($cDemo['fecha']==14){
			$tituloMsj = "Hoy termina la versión de prueba";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
					Esperamos que haya podido disfrutar estos días de prueba con la plataforma SINTIA<br>
					Hoy es el último día para usar la versión gratuita. Pero no se preocupe, tenemos un plan para que la puedan seguir utilizando.<br>
					Si le interesa puede contactarme, ya sabe que estoy a su servicio.<br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573113932970&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 311 393 2970</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 311 393 2970<br>
				</p>
			';
			$paraEnviar = 1;
		}	
	}
	//Si no han ingresado a la plataforma
	else{
		//Correo 3/ 5 días/ ¿Cómo te ha ido?
		if($cDemo['fecha']==5){
			$tituloMsj = "¿Alguna dificultad para usar la plataforma SINTIA?";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
					Como ya lo sabe, mi nombre es Jhon Mejía, soy su asesor personal.<br>
					Quería preguntarle si ha tenido alguna dificultad para ingresar a la plataforma.<br>
					Cualquier cosa sabe que puede contactarme.

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 311 393 2970<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 4/ 10 días/ Pronto vence, llamada a la acción
		if($cDemo['fecha']==10){
			$tituloMsj = "En 5 días terminará la versión de prueba y aún no la has disfrutado";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
					Aún no has podido disfrutar la versión de prueba de la plataforma SINTIA<br>
					Por ahora quedan sólo 5 días para que la versión de prueba termine.<br>
					¿Ha podido tomar usted o la Institución alguna decisión sobre el plan que desean?<br>
					Quedo atento a su respuesta.<br><br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573113932970&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="background-color: #31A952; color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 311 393 2970</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 311 393 2970<br>
				</p>
			';
			$paraEnviar = 1;
		}

		//Correo 5/ 10 días/ Hoy vence, llamada a la acción más fuerte
		if($cDemo['fecha']==14){
			$tituloMsj = "Hoy termina la versión de prueba";
			$bgTitulo = "#31a952";
			$contenidoMsj = '
				<p>
					Hola <b>'.UsuariosPadre::nombreCompletoDelUsuario($cDemo).'</b><br>
					Finalmente no has podido disfrutar estos días de prueba con la plataforma SINTIA<br>
					Hoy es el último día para usar la versión gratuita. Pero no se preocupe, tenemos un plan para que la puedan seguir utilizando.<br>
					Si le interesa puede contactarme, ya sabe que estoy a su servicio.<br>

					<div style="text-align: center; background-color: #31A952; width: 250px; height:30px; padding: 12px; font-size: 16px;">
					<a href="https://api.whatsapp.com/send?phone=573113932970&text=Hola, quisiera hablar respecto a los planes que manejan con la Plataforma SINTIA." target="_blank" style="color: white;"><img src="https://plataformasintia.com/files-general/iconos/whatsapp.png" width="20"> WhatsApp: 311 393 2970</a></div><br><br>

					Gracias por su atención a este mensaje.
				</p>	
				<p>
					Cordialmente,<br><br>

					Jhon Mejía M.<br>
					<b>Email:</b> info@plataformasintia.com<br>
					<b>WhatsApp:</b> (+57) 311 393 2970<br>
				</p>
			';
			$paraEnviar = 1;
		}
	}

	//15 días/ Si no se a realizado ningun pago se desactiva la institución
	if($cDemo['fecha']==15 && $cDemo['ins_fecha_renovacion'] == "0000-00-00 00:00:00"){
		mysqli_query($conexion, "UPDATE ".BD_ADMIN.".instituciones SET ins_estado=0 WHERE ins_id='".$cDemo['demo_institucion']."'");
	}	

	//Al mes si no se a realizado ningun pago se elimina todo lo relacionado con la institución
	if($cDemo['fecha']==45 && $cDemo['ins_fecha_renovacion'] == "0000-00-00 00:00:00"){
		try{
			mysqli_query($conexion, "DELETE FROM ".BD_ADMIN.".instituciones_modulos WHERE ipmod_institucion='".$cDemo['demo_institucion']."'");
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
		
		try{
			mysqli_query($conexion, "DELETE FROM ".BD_ADMIN.".configuracion WHERE conf_id_institucion='".$cDemo['demo_institucion']."'");
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
	
		try{
			mysqli_query($conexion, "DELETE FROM ".BD_ADMIN.".general_informacion WHERE info_institucion='".$cDemo['demo_institucion']."'");
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
	
		//CURSOS
		Grados::eliminarGradosInstitucion($cDemo['demo_institucion']);
	
		//GRUPOS
		Grupos::eliminarTodosGrupos($cDemo['demo_institucion']);
	
		//CATEGORIA NOTAS
		categoriasNota::eliminarTodasCategoriasNotas($cDemo['demo_institucion']);
	
		//TIPOS DE NOTAS
		CargaAcademica::eliminarTodosTiposNota($cDemo['demo_institucion']);
		
		//AREAS
		Areas::eliminarTodasAreas($cDemo['demo_institucion']);
		
		//MATERIAS
		Asignaturas::eliminarTodasAsignaturas($cDemo['demo_institucion']);
		
		//TODOS LOS USUARIOS
		try{
			mysqli_query($conexion, "DELETE FROM ".BD_GENERAL.".usuarios_por_estudiantes WHERE institucion='".$cDemo['demo_institucion']."'");
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
		
		//TODOS LOS USUARIOS
		UsuariosPadre::eliminarTodosUsuarios($cDemo['demo_institucion']);
		
		//TODOS LAS MATRICULAS
		Estudiantes::eliminarTodasMatriculas($cDemo['demo_institucion']);
	
		//CARGAS
		CargaAcademica::eliminarCargasInstitucion($cDemo['demo_institucion']);

		try{
			mysqli_query($conexion, "DELETE FROM ".BD_ADMIN.".instituciones WHERE ins_id='".$cDemo['demo_institucion']."'");
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}

		try{
			mysqli_query($conexion, "UPDATE ".BD_ADMIN.".demo SET demo_correo_enviado=demo_correo_enviado+1, demo_nocorreos=1 WHERE demo_id='".$cDemo['demo_id']."'");
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}
	
	if($paraEnviar==1){
		
		$data = [
			'contenido_msj'   => $contenidoMsj,
			'usuario_email'    => $cDemo['uss_email'],
			'usuario_nombre'   => UsuariosPadre::nombreCompletoDelUsuario($cDemo)
		  ];
		  $asunto = $tituloMsj;
		  $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';

		  EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);

	}

	if (($cDemo['demo_correo_enviado']+1) <= 5) {
		mysqli_query($conexion, "UPDATE ".BD_ADMIN.".demo SET demo_correo_enviado=demo_correo_enviado+1 WHERE demo_id='".$cDemo['demo_id']."'");
	}
}


//=====CORREOS PROGRAMADOS DE LAS NOTIFICACIONES A ACUDIENTES=====//
$correosProg = mysqli_query($conexion,"SELECT * FROM correos
INNER JOIN instituciones ON ins_id=corr_institucion AND ins_enviroment='".ENVIROMENT."'
WHERE corr_estado=0 AND corr_usuario IS NOT NULL
GROUP BY corr_institucion, corr_usuario
ORDER BY corr_institucion, corr_usuario");


$mensajesEnviados = 0;
$mensajesTotales = 0;
while($cProg = mysqli_fetch_array($correosProg, MYSQLI_BOTH)){

	$consultaNumeros=mysqli_query($conexion,"SELECT
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=1 AND corr_estado=0),
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=2 AND corr_estado=0),
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=3 AND corr_estado=0),
	(SELECT COUNT(corr_id) FROM correos WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_tipo=4 AND corr_estado=0)");
	$numeros = mysqli_fetch_array($consultaNumeros, MYSQLI_BOTH);

	$acudiente = UsuariosPadre::sesionUsuario($cProg['corr_usuario'], "", $cProg['corr_institucion'], $year);
	
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
	
	$correosDatos = mysqli_query($conexion,"SELECT * FROM correos
	WHERE corr_institucion='".$cProg['corr_institucion']."' AND corr_usuario='".$cProg['corr_usuario']."' AND corr_estado=0
	ORDER BY corr_tipo");
	
	
	$novedades = 0; 
	//Recorrer por institución y por usuario
	while($cDat = mysqli_fetch_array($correosDatos, MYSQLI_BOTH)){	
		
		//De los primeros tipos: 1, 2 y 3
		if($cDat['corr_tipo']==1 or $cDat['corr_tipo']==2 or $cDat['corr_tipo']==3){
			
			$datosRelacionados = Actividades::consultaGenerarParaCorreos($cDat["corr_estudiante"], $cDat["corr_actividad"], $cProg['corr_institucion'], $year);

			$docente = UsuariosPadre::sesionUsuario($datosRelacionados['car_docente'], "", $cProg['corr_institucion'], $year);
			
			
			if(!empty($datosRelacionados[0])){
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
				
				mysqli_query($conexion,"UPDATE correos SET corr_estado=1, corr_fecha_envio=now() WHERE corr_id='".$cDat["corr_id"]."'");
				
				$novedades ++;
				$mensajesTotales++;
				
			}else{
				mysqli_query($conexion,"UPDATE correos SET corr_observacion='No hay datos relacionados en correos tipo 1, 2, 3.' WHERE corr_id='".$cDat["corr_id"]."'");
				
			}
		}
		
		//Del tipo 4
		if($cDat['corr_tipo']==4){
			$datosRelacionados = CargaAcademica::traerDatosRelacionadosCargaEstudiante($cProg['corr_institucion'], $cDat["corr_estudiante"], $cDat["corr_carga"], $year);
			
			$docente = UsuariosPadre::sesionUsuario($datosRelacionados['car_docente'], "", $cProg['corr_institucion'], $year);
			
			
			if(!empty($datosRelacionados[0])){

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
			
				mysqli_query($conexion,"UPDATE correos SET corr_estado=1, corr_fecha_envio=now() WHERE corr_id='".$cDat["corr_id"]."'");
				
				$novedades ++;
				$mensajesTotales++;
				
			}else{
				mysqli_query($conexion,"UPDATE correos SET corr_observacion='No hay datos relacionados en correos tipo 4.' WHERE corr_id='".$cDat["corr_id"]."'");
				
			}
		}

	}
	
	$contenidoMsj .= '
		<b>TOTAL NOVEDADES:</b> '.$novedades.'
	';

	if(!empty($acudiente['uss_email'])){
		$mensajesEnviados++;

		$data = [
			'contenido_msj'   => $contenidoMsj,
			'usuario_email'    => $cDemo[3],
			'usuario_nombre'   => UsuariosPadre::nombreCompletoDelUsuario($cDemo)
		  ];
		  $asunto = $tituloMsj;
		  $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';
	
		  EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute,null,null);
		
	}else{
		mysqli_query($conexion,"UPDATE correos SET corr_observacion='El acudiente no tiene email registrado.' WHERE corr_id='".$cDat["corr_id"]."'");
		
	}
	//FIN ENVÍO DE MENSAJE
	
	
}