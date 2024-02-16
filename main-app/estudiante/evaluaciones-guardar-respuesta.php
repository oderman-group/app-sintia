<?php
include("session.php");
include("verificar-usuario.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'ES0057';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");

//SABER SI EL ESTUDIANTE YA HIZO LA EVALUACION
try{
	$consultaEvaluacion=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones_resultados 
	WHERE res_id_evaluacion='".$_POST["idE"]."' AND res_id_estudiante='".$datosEstudianteActual['mat_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}
$nume = mysqli_num_rows($consultaEvaluacion);

if($nume>0 and $_POST["envioauto"]=='0'){

	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=200";</script>';
	exit();
}
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

//BORRAR LAS RESPUESTAS ANTES DE VOLVER A GUARDAR
try{
	mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones_resultados
	WHERE res_id_estudiante='".$datosEstudianteActual['mat_id']."' AND res_id_evaluacion='".$_POST["idE"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

//Cantidad de preguntas de la evaluación
$cantPreguntas = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $_POST["idE"]);

$contPreguntas = 1;
$preguntasConsulta = Evaluaciones::preguntasEvaluacion($conexion, $config, $_POST["idE"]);
while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
	try{
		$respuestasConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_respuestas
		WHERE resp_id_pregunta='".$preguntas['preg_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	} catch (Exception $e) {
		include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
	}		
	$cantRespuestas = mysqli_num_rows($respuestasConsulta);
	if($cantRespuestas==0) {
		continue;
	}

	//GUARDAR RESPUESTAS
	$archivo = '';
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
			$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile);
		}
	}
	if($_POST["R$contPreguntas"]=="") $_POST["R$contPreguntas"] = 0;
	$codigo=Utilidades::generateCode("RES");
	try{
		mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_actividad_evaluaciones_resultados(res_id, res_id_pregunta, res_id_respuesta, res_id_estudiante, res_id_evaluacion, res_archivo, institucion, year)
		VALUES('".$codigo."', '".$_POST["P$contPreguntas"]."', '".$_POST["R$contPreguntas"]."', '".$datosEstudianteActual['mat_id']."', '".$_POST["idE"]."', '".$archivo."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
	} catch (Exception $e) {
		include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
	}
	$contPreguntas ++;
}

Evaluaciones::terminarEvaluacion($conexion, $config, $_POST["idE"], $datosEstudianteActual['mat_id']);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=103&idE='.base64_encode($_POST["idE"]).'";</script>';
exit();