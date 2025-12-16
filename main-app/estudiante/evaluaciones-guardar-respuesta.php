<?php
include("session.php");
include("verificar-usuario.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'ES0057';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");

//SABER SI EL ESTUDIANTE YA TERMINÓ LA EVALUACION (verificar si epe_fin está establecido)
$sqlVerificar = "SELECT epe_fin FROM ".BD_ACADEMICA.".academico_actividad_evaluaciones_estudiantes 
                 WHERE epe_id_estudiante=? AND epe_id_evaluacion=? AND institucion=? AND year=? AND epe_fin IS NOT NULL";
$parametrosVerificar = [$datosEstudianteActual['mat_id'], $_POST["idE"], $config['conf_id_institucion'], $_SESSION["bd"]];
$resultadoVerificar = BindSQL::prepararSQL($sqlVerificar, $parametrosVerificar);
$evaluacionTerminada = mysqli_num_rows($resultadoVerificar) > 0;

if($evaluacionTerminada && $_POST["envioauto"]=='0'){

	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=200";</script>';
	exit();
}
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

//BORRAR LAS RESPUESTAS ANTES DE VOLVER A GUARDAR
Evaluaciones::eliminarIntentosEstudiante($conexion, $config, $_POST["idE"], $datosEstudianteActual['mat_id']);

//Cantidad de preguntas de la evaluación
$cantPreguntas = Evaluaciones::numeroPreguntasEvaluacion($conexion, $config, $_POST["idE"]);

$contPreguntas = 1;
$preguntasConsulta = Evaluaciones::preguntasEvaluacion($conexion, $config, $_POST["idE"]);
while($preguntas = mysqli_fetch_array($preguntasConsulta, MYSQLI_BOTH)){
	$respuestasConsulta = Evaluaciones::traerRespuestaPregunta($conexion, $config, $preguntas['preg_id']);
	$cantRespuestas = mysqli_num_rows($respuestasConsulta);
	if($cantRespuestas==0) {
		continue;
	}

	//GUARDAR RESPUESTAS
	$archivo = '';
	if($preguntas['preg_tipo_pregunta']==3){
		$idPregunta = $preguntas['preg_id'];
		$destino = "../files/evaluaciones";
		if(isset($_FILES['file'.$idPregunta]) && $_FILES['file'.$idPregunta]['name']!=""){
			$nombreInputFile = 'file'.$idPregunta;
			$archivoSubido->validarArchivo($_FILES['file'.$idPregunta]['size'], $_FILES['file'.$idPregunta]['name']);
			$_FILES['file'.$idPregunta]['name'];
			$extension = end(explode(".", $_FILES['file'.$idPregunta]['name']));
			$archivo = uniqid($_SESSION["inst"].'_'.$_SESSION["id"].'_eva_res_').".".$extension;
			@unlink($destino."/".$archivo);
			$archivoSubido->subirArchivo($destino, $archivo, $nombreInputFile);
		}
	}
	
	// Validar que existan los campos P y R antes de usarlos
	// El JavaScript envía los campos en el mismo orden que las preguntas con respuestas
	$idPreguntaPost = isset($_POST["P$contPreguntas"]) && $_POST["P$contPreguntas"] != '' ? $_POST["P$contPreguntas"] : null;
	$idRespuestaPost = isset($_POST["R$contPreguntas"]) && $_POST["R$contPreguntas"] != '' ? $_POST["R$contPreguntas"] : '0';
	
	// Si no hay respuesta, usar '0'
	if($idRespuestaPost == "" || $idRespuestaPost === null) {
		$idRespuestaPost = '0';
	}
	
	// Solo guardar si tenemos el ID de pregunta válido
	// Si no existe el campo P, usar el ID de la pregunta de la base de datos
	if($idPreguntaPost === null || $idPreguntaPost == '') {
		$idPreguntaPost = $preguntas['preg_id'];
	}
	
	// Guardar el resultado
	Evaluaciones::guardarResultado($conexion, $config, $_POST["idE"], $datosEstudianteActual['mat_id'], $idPreguntaPost, $idRespuestaPost, $archivo);
	
	$contPreguntas ++;
}

Evaluaciones::terminarEvaluacion($conexion, $config, $_POST["idE"], $datosEstudianteActual['mat_id']);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=103&idE='.base64_encode($_POST["idE"]).'";</script>';
exit();