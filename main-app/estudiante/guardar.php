<?php include("session.php");?>

<?php 

include("verificar-usuario.php");

include("../compartido/sintia-funciones.php");

$archivoSubido = new Archivos;

?>

<?php

//ACTUALIZAR MATRICULA

if($_POST["id"]==1000){

	mysqli_query($conexion, "UPDATE academico_matriculas SET mat_tipo_documento='".$_POST["tipoD"]."', mat_documento='".$_POST["nDoc"]."', mat_religion='".$_POST["religion"]."', mat_email='".$_POST["email"]."', mat_direccion='".$_POST["direccion"]."', mat_barrio='".$_POST["barrio"]."', mat_telefono='".$_POST["telefono"]."', mat_celular='".$_POST["celular"]."', mat_estrato='".$_POST["estrato"]."', mat_genero='".$_POST["genero"]."', mat_fecha_nacimiento='".$_POST["fNac"]."', mat_primer_apellido='".$_POST["apellido1"]."', mat_segundo_apellido='".$_POST["apellido2"]."', mat_nombres='".$_POST["nombres"]."', mat_grado='".$_POST["grado"]."', mat_tipo='".$_POST["tipoEst"]."' WHERE mat_id_usuario='".$_SESION["id"]."'");

	mysqli_query($conexion, "UPDATE usuarios SET uss_usuario='".$_POST["nDoc"]."', uss_email='".$_POST["email"]."' WHERE uss_id='".$_SESION["id"]."'");

	

	echo '<script type="text/javascript">window.location.href="matricula.php";</script>';

	exit();

}







//GUARDAR COMENTARIO

if($_POST["id"]==7){

	mysqli_query($conexion, "INSERT INTO academico_actividad_foro_comentarios(com_id_foro, com_descripcion, com_id_estudiante, com_fecha)VALUES('".$_POST["idForo"]."', '".$_POST["com"]."', '".$_SESION["id"]."', now())");

	

?>

		<script type="text/javascript">

		function notifica(){

			var unique_id = $.gritter.add({

				// (string | mandatory) the heading of the notification

				title: 'Correcto',

				// (string | mandatory) the text inside the notification

				text: 'Los cambios se ha guardado correctamente!',

				// (string | optional) the image to display on the left

				image: 'files/iconos/Accept-Male-User.png',

				// (bool | optional) if you want it to fade out on its own or just sit there

				sticky: false,

				// (int | optional) the time you want it to be alive for before fading out

				time: '3000',

				// (string | optional) the class name you want to apply to that specific message

				class_name: 'my-sticky-class'

			});

		}

		

		setTimeout ("notifica()", 100);	

	</script>

    <div class="alert alert-success">

		<button type="button" class="close" data-dismiss="alert">&times;</button>

		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.

	</div>

    <script type="text/javascript">

		function redirige(){

			window.location.href='foros-ver.php?idForo=<?=$_POST["idForo"];?>';

		}

		

		setTimeout ("redirige()", 2000);	

	</script>

<?php

	exit();

}

//GUARDAR RESPUESTA

if($_POST["id"]==8){

	mysqli_query($conexion, "INSERT INTO academico_actividad_foro_respuestas(fore_id_comentario, fore_respuesta, fore_id_estudiante, fore_fecha)VALUES('".$_POST["idCom"]."', '".$_POST["respu"]."', '".$_SESION["id"]."', now())");

	

?>

		<script type="text/javascript">

		function notifica(){

			var unique_id = $.gritter.add({

				// (string | mandatory) the heading of the notification

				title: 'Correcto',

				// (string | mandatory) the text inside the notification

				text: 'Los cambios se ha guardado correctamente!',

				// (string | optional) the image to display on the left

				image: 'files/iconos/Accept-Male-User.png',

				// (bool | optional) if you want it to fade out on its own or just sit there

				sticky: false,

				// (int | optional) the time you want it to be alive for before fading out

				time: '3000',

				// (string | optional) the class name you want to apply to that specific message

				class_name: 'my-sticky-class'

			});

		}

		

		setTimeout ("notifica()", 100);	

	</script>

    <div class="alert alert-success">

		<button type="button" class="close" data-dismiss="alert">&times;</button>

		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.

	</div>

    <script type="text/javascript">

		function redirige(){

			window.location.href='foros-ver.php?idForo=<?=$_POST["idForo"];?>';

		}

		

		setTimeout ("redirige()", 2000);	

	</script>

<?php

	exit();

}

//GUARDAR RESPUESTAS EVALUACIONES

if($_POST["id"]==9){



	//SABER SI EL ESTUDIANTE YA HIZO LA EVALUACION

	$nume = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones_resultados 

	WHERE res_id_evaluacion='".$_POST["idE"]."' AND res_id_estudiante='".$datosEstudianteActual[0]."'"));

	

	if($nume>0 and $_POST["envioauto"]=='0'){

		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=200";</script>';

		exit();

	}



	

	//BORRAR LAS RESPUESTAS ANTES DE VOLVER A GUARDAR

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_resultados

	WHERE res_id_estudiante='".$datosEstudianteActual[0]."' AND res_id_evaluacion='".$_POST["idE"]."'");

	

	

	//Cantidad de preguntas de la evaluación

	$preguntasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluacion_preguntas

	INNER JOIN academico_actividad_preguntas ON preg_id=evp_id_pregunta

	WHERE evp_id_evaluacion='".$_POST["idE"]."'

	");

	

	$cantPreguntas = mysqli_num_rows($preguntasConsulta);

	$contPreguntas = 1;

	while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){

		$respuestasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_respuestas

		WHERE resp_id_pregunta='".$preguntas['preg_id']."'

		");

		

		$cantRespuestas = mysqli_num_rows($respuestasConsulta);

		if($cantRespuestas==0) {

			continue;

		}

		//GUARDAR RESPUESTAS

		if($preguntas['preg_tipo_pregunta']==3){

			$idPregunta = $preguntas['preg_id'];

			$destino = "../files/evaluaciones";

			if($_FILES['file'.$idPregunta]['name']!=""){

				$nombreInputFile = 'file'.$idPregunta;

				$archivoSubido->validarArchivo($_FILES['file'.$idPregunta]['size'], $_FILES['file'.$idPregunta]['name']);

				$_FILES['file'.$idPregunta]['name'];

				$extension = end(explode(".", $_FILES['file'.$idPregunta]['name']));

				$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_eva_res_').".".$extension;

				@unlink($destino."/".$archivo);

				//move_uploaded_file($_FILES['file'.$idPregunta]['tmp_name'], $destino ."/".$archivo);

				$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile);

			}

		}

		if($_POST["R$contPreguntas"]=="") $_POST["R$contPreguntas"] = 0;

		mysqli_query($conexion, "INSERT INTO academico_actividad_evaluaciones_resultados(res_id_pregunta, res_id_respuesta, res_id_estudiante, res_id_evaluacion, res_archivo)

		VALUES('".$_POST["P$contPreguntas"]."', '".$_POST["R$contPreguntas"]."', '".$datosEstudianteActual[0]."', '".$_POST["idE"]."', '".$archivo."')");

		

		

		

		

		$contPreguntas ++;

	}

	

	//ACTUALIZAR QUE EL ESTUDIANTE TERMINÓ

	mysqli_query($conexion, "UPDATE academico_actividad_evaluaciones_estudiantes SET epe_fin=now() 

	WHERE epe_id_estudiante='".$datosEstudianteActual[0]."' AND epe_id_evaluacion='".$_POST["idE"]."'");

	

	

	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=103&idE='.$_POST["idE"].'";</script>';

	exit();

}

//ENVIAR ACTIVIDAD

if($_POST["id"]==10){

	

	$fechas = mysqli_fetch_array(mysqli_query($conexion, "SELECT DATEDIFF(tar_fecha_disponible, now()), DATEDIFF(tar_fecha_entrega, now()), tar_fecha_entrega, tar_impedir_retrasos FROM academico_actividad_tareas 

	WHERE tar_id='".$_POST["idR"]."' AND tar_estado=1"), MYSQLI_BOTH);

	

	if($fechas[1]<0 and $fechas[3]==1){

		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=207&fechaH='.$fechas[2].'&diasP='.$fechas[1].'";</script>';

		exit();

	}

	

	$destino = "../files/tareas-entregadas";

	

	$num = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas_entregas WHERE ent_id_estudiante='".$datosEstudianteActual[0]."' AND ent_id_actividad='".$_POST["idR"]."'"));

	

	if($num == 0){

		if($_FILES['file']['name']!=""){

			$nombreInputFile = 'file';

			$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

			$extension = end(explode(".", $_FILES['file']['name']));

			$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file1_').".".$extension;

			@unlink($destino."/".$archivo);

			$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile);
			
			$pesoMB1 = round($_FILES['file']['size']/1048576,2);

		}

		if($_FILES['file2']['name']!=""){

			$nombreInputFile = 'file2';

			$archivoSubido->validarArchivo($_FILES['file2']['size'], $_FILES['file2']['name']);

			$extension2 = end(explode(".", $_FILES['file2']['name']));

			$archivo2 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file2_').".".$extension2;

			@unlink($destino."/".$archivo2);

			$archivoSubido->subirArchivo($destino, $archivo2, $nombreInputFile);

			$pesoMB2 = round($_FILES['file2']['size']/1048576,2);

		}

		if($_FILES['file3']['name']!=""){

			$nombreInputFile = 'file3';

			$archivoSubido->validarArchivo($_FILES['file3']['size'], $_FILES['file3']['name']);

			$extension3 = end(explode(".", $_FILES['file3']['name']));

			$archivo3 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file3_').".".$extension3;

			@unlink($destino."/".$archivo3);

			$archivoSubido->subirArchivo($destino, $archivo3, $nombreInputFile); 

			$pesoMB3 = round($_FILES['file3']['size']/1048576,2);

		}

		mysqli_query($conexion, "DELETE FROM academico_actividad_tareas_entregas WHERE ent_id_estudiante='".$datosEstudianteActual[0]."' AND ent_id_actividad='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");



		mysqli_query($conexion, "INSERT INTO academico_actividad_tareas_entregas (ent_id_estudiante, ent_id_actividad, ent_archivo, ent_fecha, ent_comentario, ent_archivo2, ent_archivo3, ent_peso1, ent_peso2, ent_peso3) VALUES(".$datosEstudianteActual[0].", '".$_POST["idR"]."', '".$archivo."', now(), '".mysqli_real_escape_string($conexion,$_POST["comentario"])."', '".$archivo2."', '".$archivo3."', '".$pesoMB1."', '".$pesoMB2."', '".$pesoMB3."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}else{

		if($_FILES['file']['name']!=""){

			$nombreInputFile = 'file';

			$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

			$extension = end(explode(".", $_FILES['file']['name']));

			$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file1_').".".$extension;

			@unlink($destino."/".$archivo);

			$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile);

			mysqli_query($conexion, "UPDATE academico_actividad_tareas_entregas SET ent_archivo='".$archivo."' WHERE ent_id_estudiante='".$datosEstudianteActual[0]."' AND ent_id_actividad='".$_POST["idR"]."'");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		if($_FILES['file2']['name']!=""){

			$nombreInputFile = 'file2';

			$archivoSubido->validarArchivo($_FILES['file2']['size'], $_FILES['file2']['name']);

			$extension2 = end(explode(".", $_FILES['file2']['name']));

			$archivo2 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file2_').".".$extension2;

			@unlink($destino."/".$archivo2);

			$archivoSubido->subirArchivo($destino, $archivo2, $nombreInputFile);

			mysqli_query($conexion, "UPDATE academico_actividad_tareas_entregas SET ent_archivo2='".$archivo2."' WHERE ent_id_estudiante='".$datosEstudianteActual[0]."' AND ent_id_actividad='".$_POST["idR"]."'");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		if($_FILES['file3']['name']!=""){

			$nombreInputFile = 'file3';

			$archivoSubido->validarArchivo($_FILES['file3']['size'], $_FILES['file3']['name']);

			$extension3 = end(explode(".", $_FILES['file3']['name']));

			$archivo3 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file3_').".".$extension3;

			@unlink($destino."/".$archivo3);

			$archivoSubido->subirArchivo($destino, $archivo3, $nombreInputFile);

			mysqli_query($conexion, "UPDATE academico_actividad_tareas_entregas SET ent_archivo3='".$archivo3."' WHERE ent_id_estudiante='".$datosEstudianteActual[0]."' AND ent_id_actividad='".$_POST["idR"]."'");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		mysqli_query($conexion, "UPDATE academico_actividad_tareas_entregas SET ent_comentario='".mysqli_real_escape_string($conexion,$_POST["comentario"])."' WHERE ent_id_estudiante='".$datosEstudianteActual[0]."' AND ent_id_actividad='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=107";</script>';

	exit();

}

//FIRMAR ASPECTOS
if($_POST["id"]==11){

	mysqli_query($conexion, "UPDATE disiplina_nota SET dn_aprobado=1, dn_fecha_aprobado=now()
    WHERE dn_cod_estudiante=" . $_POST["estudiante"] . " AND dn_periodo='" . $_POST["periodo"] . "'");

	

	echo '<script type="text/javascript">window.location.href="aspectos.php";</script>';

	exit();

}





//GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET

//FIRMA DIGITAL DE LOS REPORTES

if($_GET["get"]==1){

	mysqli_query($conexion, "UPDATE disciplina_reportes SET dr_aprobacion_estudiante=1, dr_aprobacion_estudiante_fecha=now() WHERE dr_id='".$_GET["id"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="reportes-disciplinarios.php";</script>';

	exit();

}

?>