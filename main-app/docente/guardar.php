<?php

include("session.php");

include("../compartido/sintia-funciones.php");

$archivoSubido = new Archivos;

$operacionBD = new BaseDatos;

?>

<?php

if(isset($_POST["id"])){

	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_ip, hil_so)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Acciones POST - ".$_SERVER['HTTP_REFERER']."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER['HTTP_USER_AGENT']."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");	

}elseif(isset($_GET["get"])){

	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_ip, hil_so)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Acciones GET - ".$_SERVER['HTTP_REFERER']."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER['HTTP_USER_AGENT']."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");	

}else{

	mysqli_query($conexion, "INSERT INTO seguridad_historial_acciones(hil_usuario, hil_url, hil_titulo, hil_fecha, hil_ip, hil_so)VALUES('".$_SESSION["id"]."', '".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."', 'Acciones DESCONOCIDA - ".$_SERVER['HTTP_REFERER']."', now(), '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER['HTTP_USER_AGENT']."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

}

?>

<?php

//SELECCIONAR UNA CARGA - DEBE ESTAR ARRIBA POR LAS COOKIES QUE CREA.

if($_GET["get"]==100){

	if(is_numeric($_GET["carga"])){

		setcookie("carga",$_GET["carga"]);

		setcookie("periodo",$_GET["periodo"]);

		echo '<script type="text/javascript">window.location.href="pagina-opciones.php?carga='.$_GET["carga"].'&periodo='.$_GET["periodo"].'";</script>';

		exit();

	}

}

?>



<?php

//GUARDAR CONFIGURACION CARGA

if($_POST["id"]==1){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	if($_POST["indicadores"]=="") $_POST["indicadores"] = '0';

	if($_POST["calificaciones"]=="") $_POST["calificaciones"] = '0';

	if($_POST["fechaInforme"]=="") $_POST["fechaInforme"] = '2000-12-31';

	if($_POST["posicion"]=="") $_POST["posicion"] = '0';

	

	mysqli_query($conexion, "UPDATE academico_cargas SET car_valor_indicador='".$_POST["indicadores"]."', car_configuracion='".$_POST["calificaciones"]."', car_fecha_generar_informe_auto='".$_POST["fechaInforme"]."', car_posicion_docente='".$_POST["posicion"]."' WHERE car_id='".$cargaConsultaActual."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="cargas-configurar.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}



//AGREGAR RESPUESTAS

if($_POST["id"]==6){

	mysqli_query($conexion, "INSERT INTO academico_actividad_respuestas(resp_descripcion, resp_correcta, resp_id_pregunta)VALUES('".mysqli_real_escape_string($conexion,$_POST["respuesta"])."',0,'".$_POST["idPregunta"]."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="academico-evaluaciones-ver.php?idEvaluacion='.$_POST["idEvaluacion"].'#P'.$_POST["idPregunta"].'";</script>';

	exit();

}

//AGREGAR PREGUNTAS

if($_POST["id"]==7){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	if($_POST["bancoDatos"]==0){

		//Archivos para evaluaciones

		$destino = "../files/evaluaciones";

		if($_FILES['file']['name']!=""){

			$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

			

			$extension = end(explode(".", $_FILES['file']['name']));

			$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_eva_').".".$extension;

			@unlink($destino."/".$archivo);

			move_uploaded_file($_FILES['file']['tmp_name'], $destino ."/".$archivo);

		}

		

		mysqli_query($conexion, "INSERT INTO academico_actividad_preguntas(preg_descripcion, preg_valor, preg_id_carga, preg_tipo_pregunta, preg_archivo)VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."','".$_POST["valor"]."','".$_COOKIE["carga"]."', '".$_POST["opcionR"]."', '".$archivo."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$idPregunta = mysqli_insert_id($conexion);

		mysqli_query($conexion, "INSERT INTO academico_actividad_evaluacion_preguntas(evp_id_evaluacion, evp_id_pregunta)VALUES('".$_POST["idE"]."','".$idPregunta."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");



		if($_POST["opcionR"]==1){

			$cont=1;

			$datosInsert = '';

			while($cont<=4){

				if(trim($_POST["r$cont"]!="")){

					if($_POST["c$cont"]==""){$_POST["c$cont"]=0;}

					$datosInsert .="('".mysqli_real_escape_string($conexion,$_POST["r$cont"])."','".$_POST["c$cont"]."','".$idPregunta."'),";

					$cont++;

				}

			}

			if($datosInsert!=""){

				$datosInsert = substr($datosInsert,0,-1);

				mysqli_query($conexion, "INSERT INTO academico_actividad_respuestas(resp_descripcion, resp_correcta, resp_id_pregunta)VALUES

				".$datosInsert."

				");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

			}

		}

		if($_POST["opcionR"]==2){

			$cont=1;

			$datosInsert = '';

			while($cont<=2){

				if(trim($_POST["rv$cont"]!="")){

					if($_POST["cv$cont"]==""){$_POST["cv$cont"]=0;}

					$datosInsert .="('".mysqli_real_escape_string($conexion,$_POST["rv$cont"])."','".$_POST["cv$cont"]."','".$idPregunta."'),";

					$cont++;

				}

			}

			if($datosInsert!=""){

				$datosInsert = substr($datosInsert,0,-1);

				mysqli_query($conexion, "INSERT INTO academico_actividad_respuestas(resp_descripcion, resp_correcta, resp_id_pregunta)VALUES

				".$datosInsert."

				");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

			}

		}

		if($_POST["opcionR"]==3){

			

			$datosInsert .="('Adjuntar un archivo','0','".$idPregunta."'),";



			if($datosInsert!=""){

				$datosInsert = substr($datosInsert,0,-1);

				mysqli_query($conexion, "INSERT INTO academico_actividad_respuestas(resp_descripcion, resp_correcta, resp_id_pregunta)VALUES

				".$datosInsert."

				");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

			}

		}



	}else{
		$consultaPreguntaBD=mysqli_query($conexion, "SELECT * FROM academico_actividad_preguntas WHERE preg_id='".$_POST["bancoDatos"]."'");
		$preguntaBD = mysqli_fetch_array($consultaPreguntaBD, MYSQLI_BOTH);

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		mysqli_query($conexion, "INSERT INTO academico_actividad_preguntas(preg_descripcion, preg_valor, preg_id_carga, preg_imagen1, preg_imagen2, preg_tipo_pregunta, preg_archivo)VALUES('".$preguntaBD['preg_descripcion']."', '".$preguntaBD['preg_valor']."', '".$cargaConsultaActual."', '".$preguntaBD['preg_imagen1']."', '".$preguntaBD['preg_imagen2']."', '".$preguntaBD['preg_tipo_pregunta']."', '".$preguntaBD['preg_archivo']."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$idPregunta = mysqli_insert_id($conexion);

		

		$respuestasPreguntaConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_respuestas WHERE resp_id_pregunta='".$_POST["bancoDatos"]."'");

		while($respuestasPreguntaDatos = mysqli_fetch_array($respuestasPreguntaConsulta, MYSQLI_BOTH)){

			mysqli_query($conexion, "INSERT INTO academico_actividad_respuestas(resp_descripcion, resp_correcta, resp_id_pregunta, resp_imagen)VALUES('".$respuestasPreguntaDatos['resp_descripcion']."', '".$respuestasPreguntaDatos['resp_correcta']."', '".$idPregunta."', '".$respuestasPreguntaDatos['resp_imagen']."')");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		

		mysqli_query($conexion, "INSERT INTO academico_actividad_evaluacion_preguntas(evp_id_evaluacion, evp_id_pregunta)VALUES('".$_POST["idE"]."', '".$idPregunta."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		

	}

	

	echo '<script type="text/javascript">window.location.href="evaluaciones-preguntas.php?idE='.$_POST["idE"].'#pregunta'.$idPregunta.'";</script>';

	exit();

}

//MODIFICAR PREGUNTAS

if($_POST["id"]==8){

	mysqli_query($conexion, "UPDATE academico_actividad_preguntas SET preg_descripcion='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', preg_valor='".$_POST["valor"]."' WHERE preg_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	//Archivos para evaluaciones

	$destino = "../files/evaluaciones";

	if($_FILES['file']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

		$extension = end(explode(".", $_FILES['file']['name']));

		$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_eva_').".".$extension;

		@unlink($destino."/".$archivo);

		move_uploaded_file($_FILES['file']['tmp_name'], $destino ."/".$archivo);

		

		mysqli_query($conexion, "UPDATE academico_actividad_preguntas SET preg_archivo='".$archivo."' WHERE preg_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	echo '<script type="text/javascript">window.location.href="evaluaciones-preguntas.php?idE='.$_POST["idE"].'#pregunta'.$_POST["idR"].'";</script>';

	exit();

}

//AGREGAR INDICADORES

if($_POST["id"]==9){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	
	$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=0),
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1),
	(SELECT count(*) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1)
	");
	$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$porcentajePermitido = 100 - $sumaIndicadores[0];

	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

	

	if($sumaIndicadores[2]>=$datosCargaActual['car_maximos_indicadores']){

		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=209";</script>';

		exit();

	}

	

	$infoCompartir=0;

	if($_POST["compartir"]==1) $infoCompartir=1;

	

	if($_POST["bancoDatos"]==0){

		mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio, ind_publico) VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."', 0, '".$infoCompartir."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$idRegistro = mysqli_insert_id($conexion);

		//Si decide poner los valores porcentuales de los indicadores de forma manual

		if($datosCargaActual['car_valor_indicador']==1){

			if($porcentajeRestante<=0){

				echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=210&restante='.$porcentajeRestante.'";</script>';

				exit();

			}

			if(!is_numeric($_POST["valor"])){$_POST["valor"]=1;}

			//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.

			if($_POST["valor"]>$porcentajeRestante and $porcentajeRestante>0){$_POST["valor"] = $porcentajeRestante;}

			mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_evaluacion)

			VALUES('".$cargaConsultaActual."', '".$idRegistro."', '".$_POST["valor"]."', '".$periodoConsultaActual."', 1, '".$_POST["saberes"]."')");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		//El sistema reparte los porcentajes automáticamente y equitativamente.

		else{

			$valorIgualIndicador = ($porcentajePermitido/($sumaIndicadores[2]+1));

			mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_periodo, ipc_creado, ipc_evaluacion)

			VALUES('".$cargaConsultaActual."', '".$idRegistro."', '".$periodoConsultaActual."', 1, '".$_POST["saberes"]."')");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			//Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.

			mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$valorIgualIndicador."' 

			WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

	}

	//Si escoge del banco de datos

	else{
		$consultaIndicadorBD=mysqli_query($conexion, "SELECT * FROM academico_indicadores
		INNER JOIN academico_indicadores_carga ON ipc_indicador=ind_id
		WHERE ind_id='".$_POST["bancoDatos"]."'");
		$indicadorBD = mysqli_fetch_array($consultaIndicadorBD, MYSQLI_BOTH);

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio, ind_publico) VALUES('".$indicadorBD['ind_nombre']."', 0, 1)");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$idRegistro = mysqli_insert_id($conexion);

		//Si decide poner los valores porcentuales de los indicadores de forma manual

		if($datosCargaActual['car_valor_indicador']==1){

			if($porcentajeRestante<=0){

				echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=210&restante='.$porcentajeRestante.'";</script>';

				exit();

			}

			//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.

			if($indicadorBD['ipc_valor']>$porcentajeRestante and $porcentajeRestante>0){$indicadorBD['ipc_valor'] = $porcentajeRestante;}

			mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, ipc_evaluacion)

			VALUES('".$cargaConsultaActual."', '".$idRegistro."', '".$indicadorBD['ipc_valor']."', '".$periodoConsultaActual."', 1, '".$indicadorBD['ind_id']."', '".$indicadorBD['ipc_evaluacion']."')");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		//El sistema reparte los porcentajes automáticamente y equitativamente.

		else{

			$valorIgualIndicador = ($porcentajePermitido/($sumaIndicadores[2]+1));

			mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_periodo, ipc_creado, ipc_copiado, ipc_evaluacion)

			VALUES('".$cargaConsultaActual."', '".$idRegistro."', '".$periodoConsultaActual."', 1, '".$indicadorBD['ind_id']."', '".$indicadorBD['ipc_evaluacion']."')");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			//Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.

			mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$valorIgualIndicador."' 

			WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

	}

	

	

	//Si las calificaciones son de forma automática.

	if($datosCargaActual['car_configuracion']==0){

		//Repetimos la consulta de los indicadores porque los valores fueron actualizados

		$indicadoresConsultaActualizado = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 

		WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1");

		//Actualizamos todas las actividades por cada indicador

		while($indicadoresDatos = mysqli_fetch_array($indicadoresConsultaActualizado, MYSQLI_BOTH)){
			$consultaActividadesNum=mysqli_query($conexion, "SELECT * FROM academico_actividades 
			WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."' AND act_estado=1");
			$actividadesNum = mysqli_num_rows($consultaActividadesNum);

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			//Si hay actividades relacionadas al indicador, actualizamos su valor.

			if($actividadesNum>0){

				$valorIgualActividad = ($indicadoresDatos['ipc_valor']/$actividadesNum);

				mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorIgualActividad."' 

				WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."' AND act_estado=1");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

			}

		}			

	}

	

	echo '<script type="text/javascript">window.location.href="indicadores.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}

//AGREGAR CALIFICACIONES

if($_POST["id"]==10){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	
	$consultaIndicadoresDatos=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
	WHERE ipc_indicador='".$_POST["indicador"]."' AND ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");
	$indicadoresDatos = mysqli_fetch_array($consultaIndicadoresDatos, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	
	$consultaValores=mysqli_query($conexion, "SELECT
	(SELECT sum(act_valor) FROM academico_actividades 
	WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_id_tipo='".$_POST["indicador"]."' AND act_estado=1),
	(SELECT count(*) FROM academico_actividades 
	WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1)
	");
	$valores = mysqli_fetch_array($consultaValores, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$porcentajeRestante = $indicadoresDatos['ipc_valor'] - $valores[0];

	

	

	if($valores[1]>=$datosCargaActual['car_maximas_calificaciones']){

		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=211";</script>';

		exit();

	}

	

	$infoCompartir=0;

	if($_POST["compartir"]==1) $infoCompartir=1;

	$fecha = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

	

	if($_POST["bancoDatos"]==0){

		//Si los valores de las calificaciones son de forma automática.

		if($datosCargaActual['car_configuracion']==0){

			//Insertamos la calificación

			mysqli_query($conexion, "INSERT INTO academico_actividades(act_descripcion, act_fecha, act_periodo, act_id_tipo, act_id_carga, act_estado, act_compartir, act_fecha_creacion, act_id_evidencia)"." VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$fecha."', '".$periodoConsultaActual."','".$_POST["indicador"]."','".$cargaConsultaActual."', 1, '".$infoCompartir."', now(),'".$_POST["evidencia"]."')");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			//Actualizamos el valor de todas las actividades del indicador
			$consultaActividadesNum=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1");
			$actividadesNum = mysqli_num_rows($consultaActividadesNum);

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			//Si hay actividades relacionadas al indicador, actualizamos su valor.

			if($actividadesNum>0){

				$valorIgualActividad = ($indicadoresDatos['ipc_valor']/$actividadesNum);

				mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorIgualActividad."' WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

			}	

		}

		//Si los valores de las calificaciones son de forma manual.

		else{

			if($porcentajeRestante<=0){

				echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=212&restante='.$porcentajeRestante.'";</script>';

				exit();

			}

			if(!is_numeric($_POST["valor"])){$_POST["valor"]=1;}

			//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.

			if($_POST["valor"]>$porcentajeRestante and $porcentajeRestante>0){$_POST["valor"] = $porcentajeRestante;}

			//Insertamos la calificación

			mysqli_query($conexion, "INSERT INTO academico_actividades(act_descripcion, act_fecha, act_periodo, act_id_tipo, act_id_carga, act_estado, act_compartir, act_valor, act_fecha_creacion)"." VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$fecha."', '".$periodoConsultaActual."','".$_POST["indicador"]."','".$cargaConsultaActual."', 1, '".$infoCompartir."', '".$_POST["valor"]."', now())");

			$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		}

	}

	//Si escoge del banco de datos

	else{

		

	}

	

	echo '<script type="text/javascript">window.location.href="calificaciones.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}

//AGREGAR CLASES

if($_POST["id"]==11){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	//Archivos

	$destino = "../files/clases";

	

	if($_FILES['file']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

		$extension = end(explode(".", $_FILES['file']['name']));

		$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file1_').".".$extension;

		@unlink($destino."/".$archivo);

		move_uploaded_file($_FILES['file']['tmp_name'], $destino ."/".$archivo);

	}

	

	if($_FILES['file2']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file2']['size'], $_FILES['file2']['name']);

		$extension2 = end(explode(".", $_FILES['file2']['name']));

		$archivo2 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file2_').".".$extension2;

		@unlink($destino."/".$archivo2);

		move_uploaded_file($_FILES['file2']['tmp_name'], $destino ."/".$archivo2);

	}

	

	if($_FILES['file3']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file3']['size'], $_FILES['file3']['name']);

		$extension3 = end(explode(".", $_FILES['file3']['name']));

		$archivo3 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file3_').".".$extension3;

		@unlink($destino."/".$archivo3);

		move_uploaded_file($_FILES['file3']['tmp_name'], $destino ."/".$archivo3);

	}

	

	$findme   = '?v=';

	$pos = strpos($_POST["video"], $findme) + 3;

	$video = substr($_POST["video"],$pos,11);

	

	if($_POST["bancoDatos"]==0){

		$date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

		

		$disponible=0;

		if($_POST["disponible"]==1) $disponible=1;

		

		mysqli_query($conexion, "INSERT INTO academico_clases(cls_tema, cls_fecha, cls_id_carga, cls_estado, cls_periodo, cls_video, cls_video_url, cls_archivo, cls_archivo2, cls_archivo3, cls_nombre_archivo1, cls_nombre_archivo2, cls_nombre_archivo3, cls_descripcion, cls_disponible, cls_meeting, cls_clave_docente, cls_clave_estudiante)"." VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$date."', '".$cargaConsultaActual."', 1, '".$periodoConsultaActual."', '".$video."', '".$_POST["video"]."', '".$archivo."', '".$archivo2."', '".$archivo3."', '".$_POST["archivo1"]."', '".$_POST["archivo2"]."', '".$_POST["archivo3"]."', '".mysqli_real_escape_string($conexion,$_POST["descripcion"])."', '".$disponible."', '".$_POST["idMeeting"]."', '".$_POST["claveDocente"]."', '".$_POST["claveEstudiante"]."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	echo '<script type="text/javascript">window.location.href="clases.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}

//EDITAR CALIFICACIONES

if($_POST["id"]==12){	

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	
	$consultaIndicadoresDatosC=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
	WHERE ipc_indicador='".$_POST["indicador"]."' AND ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");
	$indicadoresDatosC = mysqli_fetch_array($consultaIndicadoresDatosC, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	
	$consultaValores=mysqli_query($conexion, "SELECT
	(SELECT sum(act_valor) FROM academico_actividades 
	WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_id_tipo='".$_POST["indicador"]."' AND act_estado=1)
	");
	$valores = mysqli_fetch_array($consultaValores, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$porcentajeRestante = $indicadoresDatosC['ipc_valor'] - $valores[0];

	$porcentajeRestante = ($porcentajeRestante + $_POST["valorCalificacion"]);

	



	$fecha = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));



	//Si las calificaciones son de forma automática.

	if($datosCargaActual['car_configuracion']==0){

		mysqli_query($conexion, "UPDATE academico_actividades SET act_descripcion='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', act_fecha='".$fecha."', act_id_tipo='".$_POST["indicador"]."', act_fecha_modificacion=now(), act_id_evidencia='".$_POST["evidencia"]."' 

		WHERE act_id='".$_POST["idR"]."'  AND act_estado=1");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		//Repetimos la consulta de los indicadores porque los valores fueron actualizados

		$indicadoresConsultaActualizado = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 

		WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");

		//Actualizamos todas las actividades por cada indicador

		while($indicadoresDatos = mysqli_fetch_array($indicadoresConsultaActualizado, MYSQLI_BOTH)){
			$consultaActividadesNum=mysqli_query($conexion, "SELECT * FROM academico_actividades 
			WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."' AND act_estado=1");
			$actividadesNum = mysqli_num_rows($consultaActividadesNum);

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			//Si hay actividades relacionadas al indicador, actualizamos su valor.

			if($actividadesNum>0){

				$valorIgualActividad = ($indicadoresDatos['ipc_valor']/$actividadesNum);

				mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorIgualActividad."' 

				WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."' AND act_estado=1");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

			}

		}			

	}

	//Si las calificaciones son de forma manual.

	else{

		if($porcentajeRestante<=0){

			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=212&restante='.$porcentajeRestante.'";</script>';

			exit();

		}

		if(!is_numeric($_POST["valor"])){$_POST["valor"]=1;}

		//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.

		if($_POST["valor"]>$porcentajeRestante and $porcentajeRestante>0){$_POST["valor"] = $porcentajeRestante;}

		mysqli_query($conexion, "UPDATE academico_actividades SET act_descripcion='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', act_fecha='".$fecha."', act_id_tipo='".$_POST["indicador"]."', act_valor='".$_POST["valor"]."', act_fecha_modificacion=now() 

		WHERE act_id='".$_POST["idR"]."'  AND act_estado=1");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	echo '<script type="text/javascript">window.location.href="calificaciones.php";</script>';

	exit();

}

//EDITAR CLASES

if($_POST["id"]==13){	

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	//Archivos

	$destino = "../files/clases";

	

	

	if($_FILES['file']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

		

		$extension = end(explode(".", $_FILES['file']['name']));

		$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file1_').".".$extension;

		@unlink($destino."/".$archivo);

		move_uploaded_file($_FILES['file']['tmp_name'], $destino ."/".$archivo);

		

		mysqli_query($conexion, "UPDATE academico_clases SET cls_archivo='".$archivo."' WHERE cls_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	if($_FILES['file2']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file2']['size'], $_FILES['file2']['name']);

		

		$extension2 = end(explode(".", $_FILES['file2']['name']));

		$archivo2 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file2_').".".$extension2;

		@unlink($destino."/".$archivo2);

		move_uploaded_file($_FILES['file2']['tmp_name'], $destino ."/".$archivo2);

		

		mysqli_query($conexion, "UPDATE academico_clases SET cls_archivo2='".$archivo2."' WHERE cls_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	if($_FILES['file3']['name']!=""){

		$archivoSubido->validarArchivo($_FILES['file3']['size'], $_FILES['file3']['name']);

		

		$extension3 = end(explode(".", $_FILES['file3']['name']));

		$archivo3 = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file3_').".".$extension3;

		@unlink($destino."/".$archivo3);

		move_uploaded_file($_FILES['file3']['tmp_name'], $destino ."/".$archivo3);

		

		mysqli_query($conexion, "UPDATE academico_clases SET cls_archivo3='".$archivo3."' WHERE cls_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	$findme   = '?v=';

	$pos = strpos($_POST["video"], $findme) + 3;

	$video = substr($_POST["video"],$pos,11);

	

	$disponible=0;

	if($_POST["disponible"]==1) $disponible=1;

	

	$date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

	mysqli_query($conexion, "UPDATE academico_clases SET cls_tema='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', cls_fecha='".$date."', cls_video='".$video."', cls_video_url='".$_POST["video"]."', cls_descripcion='".mysqli_real_escape_string($conexion,$_POST["descripcion"])."', cls_nombre_archivo1='".$_POST["archivo1"]."', cls_nombre_archivo2='".$_POST["archivo2"]."', cls_nombre_archivo3='".$_POST["archivo3"]."', cls_disponible='".$disponible."'

	WHERE cls_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="clases.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}

//AGREGAR CRONOGRAMA

if($_POST["id"]==14){	

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	$date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

	mysqli_query($conexion, "INSERT INTO academico_cronograma(cro_tema, cro_fecha, cro_id_carga, cro_recursos, cro_periodo, cro_color)"." VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$date."', '".$cargaConsultaActual."', '".$_POST["recursos"]."', '".$periodoConsultaActual."', '".$_POST["colorFondo"]."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="cronograma-calendario.php";</script>';

	exit();

}

//EDITAR CRONOGRAMA

if($_POST["id"]==15){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	$date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

	mysqli_query($conexion, "UPDATE academico_cronograma SET cro_tema='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', cro_fecha='".$date."', cro_recursos='".$_POST["recursos"]."', cro_color='".$_POST["colorFondo"]."' 

	WHERE cro_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="cronograma-calendario.php";</script>';

	exit();

}



//AGREGAR PLAN DE CLASE

if($_POST["id"]==16){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

	$extension = end(explode(".", $_FILES['file']['name']));

	$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file_').".".$extension;

	$destino = "../files/pclase";

	@unlink($destino."/".$archivo);

	move_uploaded_file($_FILES['file']['tmp_name'], $destino ."/".$archivo);

	

	mysqli_query($conexion, "DELETE FROM academico_pclase WHERE pc_id_carga='".$cargaConsultaActual."' AND pc_periodo='".$periodoConsultaActual."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	mysqli_query($conexion, "INSERT INTO academico_pclase(pc_plan, pc_id_carga, pc_periodo, pc_fecha_subido)VALUES('".$archivo."', '".$cargaConsultaActual."', '".$periodoConsultaActual."', now())");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="clases.php";</script>';

	exit();

}

//GUARDAR COMENTARIO

if($_POST["id"]==17){

	mysqli_query($conexion, "INSERT INTO academico_actividad_foro_comentarios(com_id_foro, com_descripcion, com_id_estudiante, com_fecha)VALUES('".$_POST["idForo"]."', '".mysqli_real_escape_string($conexion,$_POST["com"])."', '".$idSession."', now())");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

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

			window.location.href='academico-foros-ver.php?idForo=<?=$_POST["idForo"];?>';

		}

		

		setTimeout ("redirige()", 2000);	

	</script>

<?php

	exit();

}

//GUARDAR RESPUESTA

if($_POST["id"]==18){

	mysqli_query($conexion, "INSERT INTO academico_actividad_foro_respuestas(fore_id_comentario, fore_respuesta, fore_id_estudiante, fore_fecha)VALUES('".$_POST["idCom"]."', '".mysqli_real_escape_string($conexion,$_POST["respu"])."', '".$idSession."', now())");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

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

			window.location.href='academico-foros-ver.php?idForo=<?=$_POST["idForo"];?>';

		}

		

		setTimeout ("redirige()", 2000);	

	</script>

<?php

	exit();

}

//AGREGAR FORO

if($_POST["id"]==19){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	mysqli_query($conexion, "INSERT INTO academico_actividad_foro(foro_nombre, foro_descripcion, foro_id_carga, foro_periodo, foro_estado)VALUES('".mysqli_real_escape_string($conexion,$_POST["titulo"])."', '".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$cargaConsultaActual."', '".$periodoConsultaActual."', 1)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="foros.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}



//EDITAR FORO

if($_POST["id"]==20){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	mysqli_query($conexion, "UPDATE academico_actividad_foro SET foro_nombre='".mysqli_real_escape_string($conexion,$_POST["titulo"])."', foro_descripcion='".mysqli_real_escape_string($conexion,$_POST["contenido"])."' WHERE foro_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="foros.php";</script>';

	exit();

}

//AGREGAR ACTIVIDAD

if($_POST["id"]==21){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	if($_FILES['file']['name']!=""){

		$nombreInputFile = 'file';

		$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

		$extension = end(explode(".", $_FILES['file']['name']));

		$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file_').".".$extension;

		$destino = "../files/tareas";

		@unlink($destino."/".$archivo);

		$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile); 

		$pesoMB = round($_FILES['file']['size']/1048576,2);

	}



	if($_POST["retrasos"]!=1) $_POST["retrasos"]='0';

	mysqli_query($conexion, "INSERT INTO academico_actividad_tareas(tar_titulo, tar_descripcion, tar_id_carga, tar_periodo, tar_estado, tar_fecha_disponible, tar_fecha_entrega, tar_impedir_retrasos, tar_archivo, tar_peso1)

	VALUES('".mysqli_real_escape_string($conexion,$_POST["titulo"])."', '".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$cargaConsultaActual."', '".$periodoConsultaActual."', 1, '".$_POST["desde"]."', '".$_POST["hasta"]."', '".$_POST["retrasos"]."', '".$archivo."', '".$pesoMB."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="actividades.php";</script>';

	exit();

}



//EDITAR ACTIVIDAD

if($_POST["id"]==22){

	if($_FILES['file']['name']!=""){

		$nombreInputFile = 'file';

		$archivoSubido->validarArchivo($_FILES['file']['size'], $_FILES['file']['name']);

		$extension = end(explode(".", $_FILES['file']['name']));

		$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_file_').".".$extension;

		$destino = "../files/tareas";

		@unlink($destino."/".$archivoAnterior);

		$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile); 

		mysqli_query($conexion, "UPDATE academico_actividad_tareas SET tar_archivo='".$archivo."' WHERE tar_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	if($_POST["retrasos"]!=1) $_POST["retrasos"]='0';

	mysqli_query($conexion, "UPDATE academico_actividad_tareas SET tar_titulo='".mysqli_real_escape_string($conexion,$_POST["titulo"])."', tar_descripcion='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', tar_fecha_disponible='".$_POST["desde"]."', tar_fecha_entrega='".$_POST["hasta"]."', tar_impedir_retrasos='".$_POST["retrasos"]."' WHERE tar_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="actividades.php";</script>';

	exit();

}

//AGREGAR EVALUACIONES

if($_POST["id"]==23){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	if($_POST["bancoDatos"]==0){

		mysqli_query($conexion, "INSERT INTO academico_actividad_evaluaciones(eva_nombre, eva_descripcion, eva_id_carga, eva_periodo, eva_estado, eva_desde, eva_hasta, eva_clave)"." VALUES('".mysqli_real_escape_string($conexion,$_POST["titulo"])."', '".mysqli_real_escape_string($conexion,$_POST["contenido"])."', '".$cargaConsultaActual."', '".$periodoConsultaActual."', 1, '".$_POST["desde"]."', '".$_POST["hasta"]."', '".mysqli_real_escape_string($conexion,$_POST["clave"])."')");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$idRegistro = mysqli_insert_id($conexion);

	}else{

		

	}

	echo '<script type="text/javascript">window.location.href="preguntas-agregar.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'&idE='.$idRegistro.'&idMsg=1";</script>';

	exit();

}



//EDITAR EVALUACIONES

if($_POST["id"]==24){

	mysqli_query($conexion, "UPDATE academico_actividad_evaluaciones SET eva_nombre='".mysqli_real_escape_string($conexion,$_POST["titulo"])."', eva_descripcion='".mysqli_real_escape_string($conexion,$_POST["contenido"])."', eva_desde='".$_POST["desde"]."', eva_hasta='".$_POST["hasta"]."', eva_clave='".mysqli_real_escape_string($conexion,$_POST["clave"])."' WHERE eva_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="evaluaciones-editar.php?idR='.$_POST["idR"].'";</script>';

	exit();

}



//EDITAR INDICADORES

if($_POST["id"]==25){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	
	$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=0),
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1),
	(SELECT count(*) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1)
	");
	$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);

	$porcentajePermitido = 100 - $sumaIndicadores[0];

	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

	$porcentajeRestante = ($porcentajeRestante + $_POST["valorIndicador"]);

	

	

	mysqli_query($conexion, "UPDATE academico_indicadores SET ind_nombre='".mysqli_real_escape_string($conexion,$_POST["contenido"])."' WHERE ind_id='".$_POST["idInd"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	//Si vamos a relacionar los indicadores con los SABERES

	if($datosCargaActual['car_saberes_indicador']==1){

		mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_evaluacion='".$_POST["saberes"]."' WHERE ipc_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	//Si los valores de los indicadores son de forma manual

	if($datosCargaActual['car_valor_indicador']==1){

		if(!is_numeric($_POST["valor"])){$_POST["valor"]=1;}

		//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.

		if($_POST["valor"]>$porcentajeRestante and $porcentajeRestante>0){$_POST["valor"] = $porcentajeRestante;}

		mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$_POST["valor"]."' WHERE ipc_id='".$_POST["idR"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	echo '<script type="text/javascript">window.location.href="indicadores.php";</script>';

	exit();

}

//GUARDAR PREGUNTA/OPINIÓN

if($_POST["id"]==26){

	mysqli_query($conexion, "INSERT INTO academico_clases_preguntas(cpp_usuario, cpp_fecha, cpp_id_clase, cpp_contenido)VALUES('".$idSession."', now(), '".$_POST["idClase"]."', '".mysqli_real_escape_string($conexion,$_POST["contenido"])."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

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

			window.location.href='clases-ver.php?idClase=<?=$_POST["idClase"];?>';

		}

		

		setTimeout ("redirige()", 2000);	

	</script>

<?php

	exit();

}





//CONFIGURAR REPARTO DE PORCENTAJES

if($_POST["id"]==29){	

	mysqli_query($conexion, "UPDATE academico_cargas SET car_configuracion='".$_POST["config"]."' WHERE car_id='".$datosCargaActual[0]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="academico-config-porcentajes.php";</script>';

	exit();

}

//AGREGAR INDICADORES PRIMER PERIODO

if($_POST["id"]==30){

	$consultaI = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_periodo=1 AND ipc_creado=1");

	$numI = mysqli_num_rows($consultaI);

	if($numI>=$config[20]){

		echo '<script type="text/javascript">window.location.href="indicadores1.php?error=1";</script>';

		exit();

	}else{

		$numI++;

		$valor = ($config[21]/$numI);

	}

	mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio) VALUES('".mysqli_real_escape_string($conexion,$_POST["contenido"])."', 0)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$Id = mysqli_insert_id($conexion);

	mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado) VALUES('".$_COOKIE["carga"]."', '".$Id."', '".$valor."',1,1)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$valor."' WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_periodo=1 AND ipc_creado=1");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="indicadores1.php";</script>';

	exit();

}

//EDITAR INDICADORES PRIMER PERIODO

if($_POST["id"]==31){

	mysqli_query($conexion, "UPDATE academico_indicadores SET ind_nombre='".mysqli_real_escape_string($conexion,$_POST["contenido"])."' WHERE ind_id='".$_POST["idInd"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="indicadores1.php";</script>';

	exit();

}



//EDITAR CALIFICACIONES

if($_POST["id"]==34){	

	$date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));
	$consultaRegistroActual=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id='".$_POST["idActividad"]."'");
	$registroActual = mysqli_fetch_array($consultaRegistroActual, MYSQLI_BOTH);

	if($_POST["indicador"]==$registroActual[4]){

		mysqli_query($conexion, "UPDATE academico_actividades SET act_descripcion='".$_POST["contenido"]."', act_fecha='".$date."' WHERE act_id='".$_POST["idActividad"]."'");

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	}else{

		//INDICADOR Y PORCENTAJES ANTERIORES
		$consultaIndicadorAntes=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_indicador='".$registroActual[4]."' AND ipc_periodo=1");
		$indicadorAntes = mysqli_fetch_array($consultaIndicadorAntes, MYSQLI_BOTH);

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
		$consultaNumActividadesAntes=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$registroActual[4]."' AND act_periodo=1  AND act_estado=1");
		$numActividadesAntes = mysqli_num_rows($consultaNumActividadesAntes);

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		$numActividadesAntes = $numActividadesAntes - 1;

		if($numActividadesAntes>0){

			$valorActividadAntes = ($indicadorAntes[3]/$numActividadesAntes);

			mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorActividadAntes."' WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$registroActual[4]."' AND act_periodo=1  AND act_estado=1");

			$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		}

		//INDICADOR Y PORCENTAJES NUEVOS
		$consultaIndicadorNuevo=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_indicador='".$_POST["indicador"]."' AND ipc_periodo=1");
		$indicadorNuevo = mysqli_fetch_array($consultaIndicadorNuevo, MYSQLI_BOTH);

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");
		$consultaNumActividadesNuevo=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_POST["indicador"]."' AND act_periodo=1 AND act_estado=1");
		$numActividadesNuevo = mysqli_num_rows($consultaNumActividadesNuevo);

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		$numActividadesNuevo = $numActividadesNuevo + 1;

		if($numActividadesNuevo>0){

			$valorActividadNuevo = ($indicadorNuevo[3]/$numActividadesNuevo);

			mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorActividadNuevo."' WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_POST["indicador"]."' AND act_periodo=1  AND act_estado=1");

			$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		}

		mysqli_query($conexion, "UPDATE academico_actividades SET act_descripcion='".$_POST["contenido"]."', act_fecha='".$date."', act_valor='".$valorActividadNuevo."', act_id_tipo='".$_POST["indicador"]."' WHERE act_id='".$_POST["idActividad"]."'  AND act_estado=1");

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	}

	echo '<script type="text/javascript">window.location.href="calificaciones1.php";</script>';

	exit();

}

//EDITAR CALIFICACIONES CON VALOR MANUAL

if($_POST["id"]==35){	

	$tipos = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_indicador='".$_POST["indicador"]."' AND ipc_periodo=1");

    $tipo = mysqli_fetch_array($tipos, MYSQLI_BOTH);

	//============================================= VERIFICAMOS CUANTAS NOTAS EXISTEN DEL MISMO TIPO ================================================
	$consultaSumaN=mysqli_query($conexion, "SELECT sum(act_valor) FROM academico_actividades WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_POST["indicador"]."' AND act_periodo=1 AND act_estado=1 AND act_id!='".$_POST["idActividad"]."'");
	$sumaN = mysqli_fetch_array($consultaSumaN, MYSQLI_BOTH);

	$sumaTotal = ($sumaN[0]+$_POST["valor"]);

	if($sumaTotal>$tipo[3]){

		echo "<span style='font-family:Arial; color:red;'>La suma de estos valores sobrepasa el valor del indicador. La suma actual con esta actividad es de <b>".$sumaTotal."</b>. Y el indicador relacionado tiene un valor de <b>".$tipo[3]."</b>. Por favor verifique.<br>

		<a href='javascript:history.go(-1);'>[Regresar]</a></span>";

		exit();

	}

	$date = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

	mysqli_query($conexion, "UPDATE academico_actividades SET act_descripcion='".$_POST["contenido"]."', act_fecha='".$date."', act_valor='".$_POST["valor"]."', act_id_tipo='".$_POST["indicador"]."' WHERE act_id='".$_POST["idActividad"]."'  AND act_estado=1");

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="calificaciones1.php";</script>';

	exit();

}

//AGREGAR INDICADORES CON VALOR MANUAL

if($_POST["id"]==36){
	$consultaSumaIndObg=mysqli_query($conexion, "SELECT sum(ipc_valor) FROM academico_indicadores_carga WHERE ipc_carga='".$datosCargaActual[0]."' AND ipc_periodo='".$datosCargaActual[5]."' AND ipc_creado=0");
	$sumaIndObg = mysqli_fetch_array($consultaSumaIndObg, MYSQLI_BOTH);

	$porcentajeRestante = 100 - $sumaIndObg[0];

	

	$consultaI = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_periodo='".$datosCargaActual[5]."' AND ipc_creado=1");

	$numI = mysqli_num_rows($consultaI);

	if($numI>=$config[20] and $datosCargaActual[2]<40){

		echo '<script type="text/javascript">window.location.href="indicadores.php?error=1";</script>';

		exit();

	}else{

		$numI++;

		$valor = $_POST["valor"];



	}

	mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio) VALUES('".$_POST["contenido"]."', 0)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$Id = mysqli_insert_id($conexion);

	mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado) VALUES('".$_COOKIE["carga"]."', '".$Id."', '".$valor."', '".$datosCargaActual[5]."',1)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="indicadores.php";</script>';

	exit();

}

//EDITAR INDICADORES CON VALOR MANUAL

if($_POST["id"]==37){

	mysqli_query($conexion, "UPDATE academico_indicadores SET ind_nombre='".$_POST["contenido"]."' WHERE ind_id='".$_POST["idInd"]."'");

	mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$_POST["valor"]."' WHERE ipc_indicador='".$_POST["idInd"]."' AND ipc_carga='".$_COOKIE["carga"]."' AND ipc_periodo='".$datosCargaActual[5]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="indicadores.php";</script>';

	exit();

}

//CONFIGURAR REPARTO DE PORCENTAJES EN LOS INDICADORES

if($_POST["id"]==38){	

	mysqli_query($conexion, "UPDATE academico_cargas SET car_valor_indicador='".$_POST["config"]."' WHERE car_id='".$datosCargaActual[0]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="academico-config-porcentajes-ind.php";</script>';

	exit();

}

//AGREGAR O ACTUALIZAR TEMÁTICA

if($_POST["id"]==39){

	include("verificar-carga.php");


	$consultaNumTema=mysqli_query($conexion, "SELECT * FROM academico_indicadores 
	WHERE ind_periodo='".$periodoConsultaActual."' AND ind_carga='".$cargaConsultaActual."' AND ind_tematica=1");
	$numTema = mysqli_num_rows($consultaNumTema);



	if($numTema>0){

		mysqli_query($conexion, "UPDATE academico_indicadores SET ind_nombre='".$_POST["contenido"]."', ind_fecha_modificacion=now() WHERE ind_periodo='".$periodoConsultaActual."' AND ind_carga='".$cargaConsultaActual."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}else{

		mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio, ind_periodo, ind_carga, ind_fecha_creacion, ind_tematica) VALUES('".$_POST["contenido"]."', 0, '".$periodoConsultaActual."', '".$cargaConsultaActual."', now(), 1)");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	echo '<script type="text/javascript">window.location.href="tematica.php";</script>';

	exit();

}

//IMPORTAR INFORMACIÓN

if($_POST["id"]==40){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	//Importar indicadores

	if($_POST["indicadores"]==1 and $_POST["calificaciones"]==0){

		mysqli_query($conexion, "DELETE FROM academico_indicadores_carga

		WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");

		

		mysqli_query($conexion, "UPDATE academico_actividades SET act_estado=0, act_fecha_eliminacion=now(), act_motivo_eliminacion='Importar indicadores de carga: ".$cargaConsultaActual.", del P: ".$_POST["periodoImportar"]." al P: ".$periodoConsultaActual."'

		WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."'");

		

		//Consultamos los indicadores a importar

		$indImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga

		INNER JOIN academico_indicadores ON ind_id=ipc_indicador

		WHERE ipc_carga='".$_POST["cargaImportar"]."' AND ipc_periodo='".$_POST["periodoImportar"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		

		$datosInsert = '';

		while($indImpDatos = mysqli_fetch_array($indImpConsulta, MYSQLI_BOTH)){

			$idRegInd = $indImpDatos['ind_id'];

			//Si el indicador NO es de los obligatorios lo REcreamos.

			if($indImpDatos['ind_obligatorio']==0){

				mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_periodo, ind_carga, ind_publico)VALUES('".mysqli_real_escape_string($conexion,$indImpDatos['ind_nombre'])."', '".$periodoConsultaActual."', '".$cargaConsultaActual."', '".$indImpDatos['ind_publico']."')");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

				$idRegInd = mysqli_insert_id($conexion);

			}

			$copiado = 0;

			if($indImpDatos['ipc_copiado']!=0) $copiado = $indImpDatos['ipc_copiado'];

			$datosInsert .="('".$cargaConsultaActual."', '".$idRegInd."', '".$indImpDatos['ipc_valor']."', '".$periodoConsultaActual."', 1, '".$copiado."'),";	

		}

		if($datosInsert!=""){

			$datosInsert = substr($datosInsert,0,-1);

			mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado)VALUES

			".$datosInsert."

			");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		

		$ULR = 'indicadores.php';

	}

	

	//Importar calificaciones y los indicadores también porque están realacionados.

	if($_POST["calificaciones"]==1){

		mysqli_query($conexion, "DELETE FROM academico_indicadores_carga

		WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");

		

		mysqli_query($conexion, "UPDATE academico_actividades SET act_estado=0, act_fecha_eliminacion=now(), act_motivo_eliminacion='Importar de calificaciones de carga: ".$cargaConsultaActual.", del P: ".$_POST["periodoImportar"]." al P: ".$periodoConsultaActual."'

		WHERE act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."'");

		

		//Consultamos los indicadores a importar

		$indImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga

		INNER JOIN academico_indicadores ON ind_id=ipc_indicador

		WHERE ipc_carga='".$_POST["cargaImportar"]."' AND ipc_periodo='".$_POST["periodoImportar"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$datosInsertInd = '';

		while($indImpDatos = mysqli_fetch_array($indImpConsulta, MYSQLI_BOTH)){

			$idRegInd = $indImpDatos['ind_id'];

			//Si el indicador NO es de los obligatorios lo REcreamos.

			if($indImpDatos['ind_obligatorio']==0){

				mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_periodo, ind_carga, ind_publico)VALUES('".mysqli_real_escape_string($conexion,$indImpDatos['ind_nombre'])."', '".$periodoConsultaActual."', '".$cargaConsultaActual."', '".$indImpDatos['ind_publico']."')");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

				$idRegInd = mysqli_insert_id($conexion);

			}

			$copiado = 0;

			if($indImpDatos['ipc_copiado']!=0) $copiado = $indImpDatos['ipc_copiado'];

			$datosInsertInd .="('".$cargaConsultaActual."', '".$idRegInd."', '".$indImpDatos['ipc_valor']."', '".$periodoConsultaActual."', 1, '".$copiado."'),";

			

			//Consultamos las calificaciones del indicador a Importar

			$calImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividades

			WHERE act_id_carga='".$_POST["cargaImportar"]."' AND act_periodo='".$_POST["periodoImportar"]."' AND act_id_tipo='".$indImpDatos['ind_id']."' AND act_estado=1");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

			$datosInsert = '';



			while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)){

				$datosInsert .="('".mysqli_real_escape_string($conexion,$calImpDatos['act_descripcion'])."', '".$calImpDatos['act_fecha']."', '".$calImpDatos['act_valor']."', '".$idRegInd."', '".$cargaConsultaActual."', 0, now(), 1, '".$periodoConsultaActual."','".$calImpDatos['act_compartir']."'),";

			}

			

			if($datosInsert!=""){

				$datosInsert = substr($datosInsert,0,-1);

				mysqli_query($conexion, "INSERT INTO academico_actividades(act_descripcion, act_fecha, act_valor, act_id_tipo, act_id_carga, act_registrada, act_fecha_creacion, act_estado, act_periodo, act_compartir)VALUES

				".$datosInsert."

				");

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");	

			}

			

		}

		

		if($datosInsertInd!=""){

			$datosInsertInd = substr($datosInsertInd,0,-1);

			mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado)VALUES

			".$datosInsertInd."

			");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		

		$ULR = 'calificaciones.php';

	}

	

	//Importar clases

	if($_POST["clases"]==1){	

		mysqli_query($conexion, "UPDATE academico_clases SET cls_estado=0

		WHERE cls_id_carga='".$cargaConsultaActual."' AND cls_periodo='".$periodoConsultaActual."'");

			

		//Consultamos las clases a Importar

		$calImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_clases

		WHERE cls_id_carga='".$_POST["cargaImportar"]."' AND cls_periodo='".$_POST["periodoImportar"]."' AND cls_estado=1");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$datosInsert = '';

		while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)){

			$datosInsert .="('".$calImpDatos['cls_tema']."', now(), '".$cargaConsultaActual."', 0, now(), 1, '".$periodoConsultaActual."', '".$calImpDatos['cls_archivo']."', '".$calImpDatos['cls_video_url']."', '".$calImpDatos['cls_descripcion']."', '".$calImpDatos['cls_archivo2']."', '".$calImpDatos['cls_archivo3']."', '".$calImpDatos['cls_nombre_archivo1']."', '".$calImpDatos['cls_nombre_archivo2']."', '".$calImpDatos['cls_nombre_archivo3']."', '".$calImpDatos['cls_disponible']."'),";

		}

		if($datosInsert!=""){

			$datosInsert = substr($datosInsert,0,-1);

			mysqli_query($conexion, "INSERT INTO academico_clases(cls_tema, cls_fecha, cls_id_carga, cls_registrada, cls_fecha_creacion, cls_estado, cls_periodo, cls_archivo, cls_video, cls_video_url, cls_descripcion, cls_archivo2, cls_archivo3, cls_nombre_archivo1, cls_nombre_archivo2, cls_nombre_archivo3, cls_disponible)VALUES

			".$datosInsert."

			");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		$ULR = 'clases.php';

	}

	

	//Importar actividades

	if($_POST["actividades"]==1){		

		mysqli_query($conexion, "UPDATE academico_actividad_tareas SET tar_estado=0

		WHERE tar_id_carga='".$cargaConsultaActual."' AND tar_periodo='".$periodoConsultaActual."'");

			

		//Consultamos las actividades a Importar

		$calImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas

		WHERE tar_id_carga='".$_POST["cargaImportar"]."' AND tar_periodo='".$_POST["periodoImportar"]."' AND tar_estado=1");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$datosInsert = '';

		while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)){

			$datosInsert .="('".$calImpDatos['tar_titulo']."', '".$calImpDatos['tar_descripcion']."', '".$cargaConsultaActual."', '".$calImpDatos['tar_fecha_disponible']."', '".$calImpDatos['tar_fecha_entrega']."', '".$calImpDatos['tar_archivo']."', '".$calImpDatos['tar_impedir_retrasos']."', '".$periodoConsultaActual."', 1, '".$calImpDatos['tar_archivo2']."', '".$calImpDatos['ar_archivo3']."'),";	

		}

		if($datosInsert!=""){

			$datosInsert = substr($datosInsert,0,-1);

			mysqli_query($conexion, "INSERT INTO academico_actividad_tareas(tar_titulo, tar_descripcion, tar_id_carga, tar_fecha_disponible, tar_fecha_entrega, tar_archivo, tar_impedir_retrasos, tar_periodo, tar_estado, tar_archivo2, ar_archivo3)VALUES

			".$datosInsert."

			");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		

		$ULR = 'actividades.php';

	}

	

	//Importar foros

	if($_POST["foros"]==1){		

		mysqli_query($conexion, "UPDATE academico_actividad_foro SET foro_estado=0

		WHERE foro_id_carga='".$cargaConsultaActual."' AND foro_periodo='".$periodoConsultaActual."'");

			

		//Consultamos las foros a Importar

		$calImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_foro

		WHERE foro_id_carga='".$_POST["cargaImportar"]."' AND foro_periodo='".$_POST["periodoImportar"]."' AND foro_estado=1");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$datosInsert = '';

		while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)){

			$datosInsert .="('".$calImpDatos['foro_nombre']."', '".$calImpDatos['foro_descripcion']."', '".$cargaConsultaActual."', '".$periodoConsultaActual."', 1),";

		}

		if($datosInsert!=""){

			$datosInsert = substr($datosInsert,0,-1);

			mysqli_query($conexion, "INSERT INTO academico_actividad_foro(foro_nombre, foro_descripcion, foro_id_carga, foro_periodo, foro_estado)VALUES

			".$datosInsert."

			");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		

		$ULR = 'foros.php';

	}

	

	//Importar cronograma

	if($_POST["cronograma"]==1){		

			

		//Consultamos la información del cronograma a Importar

		$calImpConsulta = mysqli_query($conexion, "SELECT * FROM academico_cronograma

		WHERE cro_id_carga='".$_POST["cargaImportar"]."' AND cro_periodo='".$_POST["periodoImportar"]."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		$datosInsert = '';

		while($calImpDatos = mysqli_fetch_array($calImpConsulta, MYSQLI_BOTH)){

			$datosInsert .="('".$calImpDatos['cro_tema']."', '".$calImpDatos['cro_fecha']."', '".$cargaConsultaActual."', '".$calImpDatos['cro_recursos']."', '".$periodoConsultaActual."', '".$calImpDatos['cro_color']."'),";

		}

		if($datosInsert!=""){

			$datosInsert = substr($datosInsert,0,-1);

			mysqli_query($conexion, "INSERT INTO academico_cronograma(cro_tema, cro_fecha, cro_id_carga, cro_recursos, cro_periodo, cro_color)VALUES

			".$datosInsert."

			");

			$lineaError = __LINE__;

			include("../compartido/reporte-errores.php");

		}

		

		$ULR = 'cronograma.php';

	}

	

	

	echo '<script type="text/javascript">window.location.href="'.$ULR.'?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}

//AGREGAR PREGUNTAS A LAS CATEGORIAS DE FORMATOS MONITOREO

if($_POST["id"]==41){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	if($_POST["critica"]!=1) $_POST["critica"]='0';

	mysqli_query($conexion, "INSERT INTO academico_actividad_preguntas(preg_descripcion, preg_critica)VALUES('".$_POST["contenido"]."','".$_POST["critica"]."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$idPregunta = mysqli_insert_id($conexion);

	mysqli_query($conexion, "INSERT INTO academico_actividad_evaluacion_preguntas(evp_id_evaluacion, evp_id_pregunta)VALUES('".$_POST["idE"]."','".$idPregunta."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		

	echo '<script type="text/javascript">window.location.href="formatos-categorias-preguntas.php?idE='.$_POST["idE"].'&idF='.$_POST["idF"].'#pregunta'.$idPregunta.'";</script>';

	exit();

}

//AGREGAR  MONITOREO

if($_POST["id"]==42){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	mysqli_query($conexion, "INSERT INTO academico_monitoreo(moni_fecha, moni_evaluador, moni_evaluado, moni_id_formato)

	VALUES(now(), '".$_SESSION["id"]."','".$_POST["evaluado"]."','".$_POST["idF"]."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$idRegistro = mysqli_insert_id($conexion);

	

	$consultaCat = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones 

	WHERE eva_formato='".$_POST["idF"]."'

	");

	$contReg = 1;

	while($resultadoCat = mysqli_fetch_array($consultaCat, MYSQLI_BOTH)){

		$preguntasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluacion_preguntas

		INNER JOIN academico_actividad_preguntas ON preg_id=evp_id_pregunta

		WHERE evp_id_evaluacion='".$resultadoCat['eva_id']."'

		");

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");



		while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){

			//GUARDAR RESPUESTAS

			if($_POST["P$contReg"]=="") $_POST["P$contReg"] = 0;

			mysqli_query($conexion, "INSERT INTO academico_actividad_evaluaciones_resultados(res_id_pregunta, res_id_respuesta, res_id_estudiante, res_id_evaluacion, res_id_monitoreo)

			VALUES('".$preguntas['preg_id']."', '".$_POST["P$contReg"]."', '".$_POST["evaluado"]."', '".$resultadoCat['eva_id']."', '".$idRegistro."')");

			$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

			$contReg++;

		}

	}

	

	mysqli_query($conexion, "INSERT INTO general_alertas (alr_nombre, alr_descripcion, alr_tipo, alr_usuario, alr_fecha_envio, alr_categoria, alr_importancia, alr_url_acceso, alr_vista)

	VALUES('Nuevo monitoreo', 'Acaban de hacerte un nuevo monitoreo', 2, '".$_POST["evaluado"]."', now(), 3, 2, '', 0)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		

	echo '<script type="text/javascript">window.location.href="formatos.php";</script>';

	exit();

}



//AGREGAR CATEGORIAS A LOS FORMATOS

if($_POST["id"]==43){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");



	mysqli_query($conexion, "INSERT INTO academico_actividad_evaluaciones(eva_nombre, eva_estado, eva_formato)VALUES('".$_POST["titulo"]."', 1, '".$_POST["idF"]."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");



	echo '<script type="text/javascript">window.location.href="formatos-categorias.php?idF='.$_POST['idF'].'";</script>';

	exit();

}



//EDITAR CATEGORIAS A LOS FORMATOS

if($_POST["id"]==44){

	mysqli_query($conexion, "UPDATE academico_actividad_evaluaciones SET eva_nombre='".$_POST["titulo"]."' WHERE eva_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	echo '<script type="text/javascript">window.location.href="formatos-categorias.php?idF='.$_POST['idF'].'";</script>';

	exit();

}



//EDITAR PREGUNTAS A LAS CATEGORIAS DE FORMATOS MONITOREO

if($_POST["id"]==45){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	if($_POST["critica"]!=1) $_POST["critica"]='0';

	mysqli_query($conexion, "UPDATE academico_actividad_preguntas SET preg_descripcion='".$_POST["contenido"]."', preg_critica='".$_POST["critica"]."' WHERE preg_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		

	echo '<script type="text/javascript">window.location.href="formatos-categorias-preguntas.php?idE='.$_POST["idE"].'&idF='.$_POST["idF"].'#pregunta'.$_POST["idR"].'";</script>';

	exit();

}



//AGREGAR FORMATOS

if($_POST["id"]==46){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");



	mysqli_query($conexion, "INSERT INTO academico_formatos(form_nombre, form_carga)VALUES('".$_POST["titulo"]."', '".$cargaConsultaActual."')");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");



	echo '<script type="text/javascript">window.location.href="formatos.php";</script>';

	exit();

}



//EDITAR FORMATOS

if($_POST["id"]==47){

	mysqli_query($conexion, "UPDATE academico_formatos SET form_nombre='".$_POST["titulo"]."' WHERE form_id='".$_POST["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	echo '<script type="text/javascript">window.location.href="formatos.php";</script>';

	exit();

}



//========================================== GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET GET  GET GET GET GET GET GET GET GET GET GET GET GET GET ======================================================

//CAMBIAR DE ESTADO LAS NOTICIAS

if($_GET["get"]==1){

	$consulta = mysqli_query($conexion, "SELECT * FROM social_noticias WHERE not_id='".$_GET["id"]."'");

	$resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

	if($resultado[5]==0) $estado=1; else $estado=0;

	mysqli_query($conexion, "UPDATE social_noticias SET not_estado='".$estado."' WHERE not_id='".$_GET["id"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="noticias.php#N'.$_GET["id"].'";</script>';

	exit();

}

//ELIMINAR NOTICIAS

if($_GET["get"]==2){

	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=2 WHERE not_id='".$_GET["id"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="noticias.php";</script>';

	exit();

}

//MOSTRAR TODAS MIS NOTICIAS

if($_GET["get"]==3){

	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=1 WHERE not_usuario='".$idSession."' AND not_estado!=2");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="noticias.php";</script>';

	exit();

}

//OCULTAR TODAS MIS NOTICIAS

if($_GET["get"]==4){

	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=0 WHERE not_usuario='".$idSession."' AND not_estado!=2");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="noticias.php";</script>';

	exit();

}

//ELIMINAR TODAS MIS NOTICIAS

if($_GET["get"]==5){

	mysqli_query($conexion, "UPDATE social_noticias SET not_estado=2 WHERE not_usuario='".$idSession."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="noticias.php";</script>';

	exit();

}

//CONFIRMAR AMISTAD

if($_GET["get"]==6){

	mysqli_query($conexion, "UPDATE social_amigos SET ams_estado=1 WHERE ams_usuario='".$_GET["usuario"]."' AND ams_amigo='".$idSession."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="perfil.php";</script>';

	exit();

}

//ENVIAR SOLICITUD AMISTAD

if($_GET["get"]==7){

	mysqli_query($conexion, "INSERT INTO social_amigos(ams_usuario, ams_amigo, ams_estado, ams_destacado)VALUES('".$idSession."', '".$_GET["usuario"]."', 0, 0)");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="perfil.php";</script>';

	exit();

}

//CAMBIAR DE ESTADO LAS RESPUESTAS DE LOS EXAMENES

if($_GET["get"]==8){

	if($_GET["estado"]==0) $estado=1; else $estado=0;

	mysqli_query($conexion, "UPDATE academico_actividad_respuestas SET resp_correcta='".$estado."' WHERE resp_id='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'#pregunta'.$_GET["preg"].'";</script>';

	exit();

}

//ELIMINAR RESPUESTAS

if($_GET["get"]==9){

	mysqli_query($conexion, "DELETE FROM academico_actividad_respuestas WHERE resp_id='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR INDICADORES

if($_GET["get"]==10){



	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	

	$actividadesRelacionadasConsulta = mysqli_query($conexion, "SELECT * FROM academico_actividades 

	WHERE act_id_tipo='".$_GET["idIndicador"]."' AND act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	while($actividadesRelacionadasDatos = mysqli_fetch_array($actividadesRelacionadasConsulta, MYSQLI_BOTH)){

		/*

		mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$actividadesRelacionadasDatos['act_id']."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

		*/

		mysqli_query($conexion, "UPDATE academico_actividades SET act_estado=0, act_fecha_eliminacion=now(), act_motivo_eliminacion='Eliminar indicadores de carga: ".$cargaConsultaActual.", del P: ".$periodoConsultaActual."' WHERE act_id='".$actividadesRelacionadasDatos['act_id']."'");

		$lineaError = __LINE__;

		include("../compartido/reporte-errores.php");

	}

	

	mysqli_query($conexion, "DELETE FROM academico_indicadores_carga WHERE ipc_id='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	
	$consultaSumaIndicadores=mysqli_query($conexion, "SELECT
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=0),
	(SELECT sum(ipc_valor) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1),
	(SELECT count(*) FROM academico_indicadores_carga 
	WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1)
	");
	$sumaIndicadores = mysqli_fetch_array($consultaSumaIndicadores, MYSQLI_BOTH);

	$porcentajePermitido = 100 - $sumaIndicadores[0];

	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

	

	//Si decide poner los valores porcentuales de los indicadores de forma manual

	if($datosCargaActual['car_valor_indicador']==1){



			

	}

	//El sistema reparte los porcentajes automáticamente y equitativamente.

	else{

		$valorIgualIndicador = ($porcentajePermitido/($sumaIndicadores[2]));

		//Actualiza todos valores de la misma carga y periodo.

		mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$valorIgualIndicador."' 

		WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1");

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

			

		//Si decide que los valores de las calificaciones son de forma automática.

		if($datosCargaActual['car_configuracion']==0){

			//Repetimos la consulta de los indicadores porque los valores fueron actualizados

			$indicadoresConsultaActualizado = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 

			WHERE ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."' AND ipc_creado=1");

			//Actualizamos todas las actividades por cada indicador

			while($indicadoresDatos = mysqli_fetch_array($indicadoresConsultaActualizado, MYSQLI_BOTH)){
				$consultaActividadesNum=mysqli_query($conexion, "SELECT * FROM academico_actividades 
				WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."' AND act_estado=1");
				$actividadesNum = mysqli_num_rows($consultaActividadesNum);

				$lineaError = __LINE__;

				include("../compartido/reporte-errores.php");

				//Si hay actividades relacionadas al indicador, actualizamos su valor.

				if($actividadesNum>0){

					$valorIgualActividad = ($indicadoresDatos['ipc_valor']/$actividadesNum);

					mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorIgualActividad."' 

					WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_periodo='".$periodoConsultaActual."' AND act_id_carga='".$cargaConsultaActual."' AND act_estado=1");

					$lineaError = __LINE__;

					include("../compartido/reporte-errores.php");

				}

			}

			

		}

	}



	echo '<script type="text/javascript">window.location.href="indicadores.php";</script>';

	exit();

}

//ELIMINAR CLASES

if($_GET["get"]==11){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	
	$consultaRegistro=mysqli_query($conexion, "SELECT * FROM academico_clases WHERE cls_id='".$_GET["idR"]."'");
	$registro = mysqli_fetch_array($consultaRegistro, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	$ruta = '../files/clases';

	if(file_exists($ruta."/".$registro['cls_archivo'])){

		unlink($ruta."/".$registro['cls_archivo']);	

	}

	if(file_exists($ruta."/".$registro['cls_archivo2'])){

		unlink($ruta."/".$registro['cls_archivo2']);	

	}

	if(file_exists($ruta."/".$registro['cls_archivo3'])){

		unlink($ruta."/".$registro['cls_archivo3']);	

	}

	

	

	

	mysqli_query($conexion, "UPDATE academico_clases SET cls_estado=0 WHERE cls_id=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	mysqli_query($conexion, "DELETE FROM academico_ausencias WHERE aus_id_clase=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	

	

	echo '<script type="text/javascript">window.location.href="clases.php?carga='.$cargaConsultaActual.'&periodo='.$periodoConsultaActual.'";</script>';

	exit();

}

//ELIMINAR CALIFICACIONES

if($_GET["get"]==12){

	include("verificar-carga.php");

	include("verificar-periodos-diferentes.php");

	
	$consultaIndicadoresDatos=mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga 
	WHERE ipc_indicador='".$_GET["idIndicador"]."' AND ipc_carga='".$cargaConsultaActual."' AND ipc_periodo='".$periodoConsultaActual."'");
	$indicadoresDatos = mysqli_fetch_array($consultaIndicadoresDatos, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	//"Borramos" la actividad

	mysqli_query($conexion, "UPDATE academico_actividades SET act_estado=0, act_fecha_eliminacion=now(), act_motivo_eliminacion='Eliminar la actividad de carga: ".$cargaConsultaActual.", del P: ".$periodoConsultaActual."' WHERE act_id=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	//Borramos las calificaciones asociadas a esa actividad.

	/*

	mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	*/

	

	//Si los valores de las calificaciones son de forma automática.

	if($datosCargaActual['car_configuracion']==0){

		//Actualizamos el valor de todas las actividades del indicador
		$consultaActividadesNum=mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1");
		$actividadesNum = mysqli_num_rows($consultaActividadesNum);

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		//Si hay actividades relacionadas al indicador, actualizamos su valor.

		if($actividadesNum>0){

			$valorIgualActividad = ($indicadoresDatos['ipc_valor']/($actividadesNum));

			mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valorIgualActividad."' WHERE act_id_tipo='".$indicadoresDatos['ipc_indicador']."' AND act_id_carga='".$cargaConsultaActual."' AND act_periodo='".$periodoConsultaActual."' AND act_estado=1");

			$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

		}	

	}



	

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR CRONOGRAMA

if($_GET["get"]==13){

	mysqli_query($conexion, "DELETE FROM academico_cronograma WHERE cro_id=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR COMENTARIOS DE FOROS

if($_GET["get"]==14){

	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_respuestas WHERE fore_id_comentario=".$_GET["idCom"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_comentarios WHERE com_id=".$_GET["idCom"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="academico-foros-ver.php?idForo='.$_GET["idForo"].'";</script>';

	exit();

}

//ELIMINAR RESPUESTAS A COMENTARIOS

if($_GET["get"]==15){

	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_respuestas WHERE fore_id=".$_GET["idRes"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="academico-foros-ver.php?idForo='.$_GET["idForo"].'";</script>';

	exit();

}

//ELIMINAR FOROS

if($_GET["get"]==16){

	$foroC = mysqli_query($conexion, "SELECT * FROM academico_actividad_foro_comentarios WHERE com_id_foro='".$_GET["idR"]."'");

	while($foro=mysqli_fetch_array($foroC, MYSQLI_BOTH)){

		mysqli_query($conexion, "DELETE FROM academico_actividad_foro_respuestas WHERE fore_id_comentario='".$foro[0]."'");

		$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");	

	}

	mysqli_query($conexion, "DELETE FROM academico_actividad_foro_comentarios WHERE com_id_foro='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	mysqli_query($conexion, "DELETE FROM academico_actividad_foro WHERE foro_id='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="foros.php";</script>';

	exit();

}

//ELIMINAR TAREAS

if($_GET["get"]==17){

	

	$rEntregas = mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas_entregas WHERE ent_id_actividad='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	$rutaEntregas = '../files/tareas-entregadas';

	while($registroEntregas = mysqli_fetch_array($rEntregas, MYSQLI_BOTH)){

		

		if(file_exists($rutaEntregas."/".$registroEntregas['ent_archivo'])){

			unlink($rutaEntregas."/".$registroEntregas['ent_archivo']);	

		}

		if(file_exists($rutaEntregas."/".$registroEntregas['ent_archivo2'])){

			unlink($rutaEntregas."/".$registroEntregas['ent_archivo2']);	

		}

		if(file_exists($rutaEntregas."/".$registroEntregas['ent_archivo3'])){

			unlink($rutaEntregas."/".$registroEntregas['ent_archivo3']);	

		}

	}

	

	mysqli_query($conexion, "DELETE FROM academico_actividad_tareas_entregas WHERE ent_id_actividad='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	
	$consultaRegistro=mysqli_query($conexion, "SELECT * FROM academico_actividad_tareas WHERE tar_id='".$_GET["idR"]."'");
	$registro = mysqli_fetch_array($consultaRegistro, MYSQLI_BOTH);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	$ruta = '../files/tareas';

	if(file_exists($ruta."/".$registro['tar_archivo'])){

		unlink($ruta."/".$registro['tar_archivo']);	

	}

	if(file_exists($ruta."/".$registro['tar_archivo2'])){

		unlink($ruta."/".$registro['tar_archivo2']);	

	}

	if(file_exists($ruta."/".$registro['tar_archivo3'])){

		unlink($ruta."/".$registro['tar_archivo3']);	

	}

	

	mysqli_query($conexion, "UPDATE academico_actividad_tareas SET tar_estado=0 WHERE tar_id='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR EVALUACIONES

if($_GET["get"]==18){

	

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluacion_preguntas WHERE evp_id_evaluacion='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	

	//Eliminamos los archivos de respuestas de las preguntas de esta evaluacion.

	$rEntregas = mysqli_query($conexion, "SELECT * FROM academico_actividad_evaluaciones_resultados WHERE res_id_evaluacion='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	$rutaEntregas = '../files/evaluaciones';

	while($registroEntregas = mysqli_fetch_array($rEntregas, MYSQLI_BOTH)){

		if(file_exists($ruta."/".$registro['res_archivo'])){

			unlink($ruta."/".$registro['res_archivo']);	

		}

	}

	

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_resultados WHERE res_id_evaluacion='".$_GET["idR"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_estudiantes WHERE epe_id_evaluacion=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones WHERE eva_id=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//RECALCULAR VALOR INDICADORES

if($_GET["get"]==19){

	$consultaI = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_periodo='".$datosCargaActual[5]."' AND ipc_creado=1");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	$numI = mysqli_num_rows($consultaI);

	$valor = ($config[21]/$numI);

	mysqli_query($conexion, "UPDATE academico_indicadores_carga SET ipc_valor='".$valor."' WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_periodo='".$datosCargaActual[5]."' AND ipc_creado=1");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="indicadores.php";</script>';

	exit();

}

//RECALCULAR VALOR CALIFICACIONES POR INDICADOR

if($_GET["get"]==20){

	$tipos = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_indicador='".$_GET["ind"]."' AND ipc_periodo='".$datosCargaActual[5]."'");

    $tipo = mysqli_fetch_array($tipos, MYSQLI_BOTH);

	//============================================= VERIFICAMOS CUANTAS NOTAS EXISTEN DEL MISMO TIPO ================================================

	$registros = mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_GET["ind"]."' AND act_periodo='".$datosCargaActual[5]."' AND act_estado=1");


   	$num_reg = mysqli_num_rows($registros);

	//================== VALOR INDIVIDUAL DE CADA NOTA QUE PERTENECE AL MISMO TIPO ======================================

	@$valnota=($tipo[3]/$num_reg);

	if($valnota==0 or !is_numeric($valnota) or $valnota=="")

		$valnota = 0;

	mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valnota."' WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_GET["ind"]."' AND act_periodo='".$datosCargaActual[5]."'");

	//echo $valnota; exit();


	echo '<script type="text/javascript">window.location.href="calificaciones.php";</script>';

	exit();

}

//ELIMINAR NOTA ACADEMICA DE UN ESTUDIANTE

if($_GET["get"]==21){

	$tabla = 'academico_calificaciones';

	$clave = 'cal_id'; 

	$id = $_GET["id"];

	$urlRetorno = $_SERVER['HTTP_REFERER'];

	$operacionBD->eliminarPorId($tabla, $clave, $id, $urlRetorno);

	

}

//ELIMINAR NOTA DISCIPLINARIA DE UN ESTUDIANTE

if($_GET["get"]==22){

	mysqli_query($conexion, "DELETE FROM disiplina_nota WHERE dn_id=".$_GET["id"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}





//RECALCULAR VALOR CALIFICACIONES POR INDICADOR1

if($_GET["get"]==25){

	$tipos = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$_COOKIE["carga"]."' AND ipc_indicador='".$_GET["ind"]."' AND ipc_periodo=1");

    $tipo = mysqli_fetch_array($tipos, MYSQLI_BOTH);

	//============================================= VERIFICAMOS CUANTAS NOTAS EXISTEN DEL MISMO TIPO ================================================

	$registros = mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_GET["ind"]."' AND act_periodo=1 AND act_estado=1");

   	$num_reg = mysqli_num_rows($registros);

	//================== VALOR INDIVIDUAL DE CADA NOTA QUE PERTENECE AL MISMO TIPO ======================================

	@$valnota=($tipo[3]/$num_reg);

	if($valnota==0 or !is_numeric($valnota) or $valnota=="")

		$valnota = 0;

	mysqli_query($conexion, "UPDATE academico_actividades SET act_valor='".$valnota."' WHERE act_id_carga='".$_COOKIE["carga"]."' AND act_id_tipo='".$_GET["ind"]."' AND act_periodo=1");

	echo '<script type="text/javascript">window.location.href="calificaciones1.php";</script>';

	exit();

}

if($_GET["get"]==26){

//REPLICAR INDICADOR CON CODIGOS DIFERENTES

	$cont=1;

	$asignacionT = mysqli_query($conexion, "SELECT * FROM academico_indicadores_carga WHERE ipc_carga='".$datosCargaActual[0]."' AND ipc_periodo='".$datosCargaActual[5]."' AND ipc_creado=1");

	while($asgT = mysqli_fetch_array($asignacionT, MYSQLI_BOTH)){
		$consultaNInd=mysqli_query($conexion, "SELECT * FROM academico_indicadores WHERE ind_id='".$asgT[2]."'");
		$nInd = mysqli_fetch_array($consultaNInd, MYSQLI_BOTH);

		mysqli_query($conexion, "INSERT INTO academico_indicadores(ind_nombre, ind_obligatorio)VALUES('".$nInd[1]."',0)");

		$idInd = mysqli_insert_id($conexion);


		mysqli_query($conexion, "INSERT INTO academico_indicadores_carga(ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado)VALUES('".$_COOKIE["carga"]."','".$idInd."','".$asgT[3]."','".$datosCargaActual[5]."','".$asgT[5]."')");


		mysqli_query($conexion, "UPDATE academico_actividades SET act_id_tipo='".$idInd."' WHERE act_id_tipo='".$asgT[2]."' AND act_id_carga='".$_COOKIE["carga"]."' AND act_periodo='".$datosCargaActual[5]."'");


		mysqli_query($conexion, "DELETE FROM academico_indicadores_carga WHERE ipc_carga=".$_COOKIE["carga"]." AND ipc_periodo='".$datosCargaActual[5]."' AND ipc_indicador='".$asgT[2]."'");

		$cont++;

	}

	echo '<script type="text/javascript">window.location.href="indicadores.php";</script>';

	exit();

}

//ELIMINAR PREGUNTAS DE LAS EVALUACIONES

if($_GET["get"]==27){

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluacion_preguntas 

	WHERE evp_id_evaluacion='".$_GET["idE"]."' AND evp_id_pregunta='".$_GET["idP"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");



	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR INTENTO DE LAS EVALUACIONES

if($_GET["get"]==28){

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_estudiantes 

	WHERE epe_id_evaluacion='".$_GET["idE"]."' AND epe_id_estudiante='".$_GET["idEstudiante"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones_resultados 

	WHERE res_id_evaluacion='".$_GET["idE"]."' AND res_id_estudiante='".$_GET["idEstudiante"]."'");

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");



	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR AUSENCIA DE UN ESTUDIANTE

if($_GET["get"]==29){

	$tabla = 'academico_ausencias';

	$clave = 'aus_id'; 

	$id = $_GET["id"];

	$urlRetorno = $_SERVER['HTTP_REFERER'];

	$operacionBD->eliminarPorId($tabla, $clave, $id, $urlRetorno);

}

//ELIMINAR FORMATO

if($_GET["get"]==30){

	mysqli_query($conexion, "DELETE FROM academico_actividad_evaluaciones WHERE eva_formato=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	

	mysqli_query($conexion, "DELETE FROM academico_formatos WHERE form_id=".$_GET["idR"]);

	$lineaError = __LINE__;

	include("../compartido/reporte-errores.php");

	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';

	exit();

}

//ELIMINAR NOTA DISCIPLINARIA DE UN ESTUDIANTE

if($_GET["get"]==31){

	$tabla = 'disiplina_nota';

	$clave = 'dn_id'; 

	$id = $_GET["id"];

	$urlRetorno = $_SERVER['HTTP_REFERER'];

	$operacionBD->eliminarPorId($tabla, $clave, $id, $urlRetorno);

}

?>