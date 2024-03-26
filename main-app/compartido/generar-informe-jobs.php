<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(dirname(dirname(__FILE__))));
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";

$conexionPDOInstance = new Conexion;
$conexionPDO         = $conexionPDOInstance->conexionPDO(SERVER, USER, PASSWORD, BD_ADMIN);
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion);

require_once(ROOT_PATH."/main-app/class/Sysjobs.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
$parametrosBuscar = array(
	"tipo" =>JOBS_TIPO_GENERAR_INFORMES,
	"estado" =>JOBS_ESTADO_PENDIENTE
);										
$listadoCrobjobs=SysJobs::listar($parametrosBuscar);


while($resultadoJobs = mysqli_fetch_array($listadoCrobjobs, MYSQLI_BOTH)){

$datos = array(
	"id" 	  => $resultadoJobs['job_id'],
	"estado"  => JOBS_ESTADO_PROCESO,
);
SysJobs::actualizar($datos);

// fecha1 es la primera fecha
$fechaInicio = new DateTime();
$finalizado = false;
$parametros = json_decode($resultadoJobs["job_parametros"], true);
$institucionId = $resultadoJobs["job_id_institucion"];
$anio = $resultadoJobs["job_year"];
$intento = intval($resultadoJobs["job_intentos"]);

$_SESSION["id"]=$resultadoJobs["job_responsable"];
$_SESSION["bd"]=$resultadoJobs["job_year"];
$_SESSION["idInstitucion"]=$resultadoJobs["job_id_institucion"];

$grado =$parametros["grado"];
$grupo =$parametros["grupo"];
$carga = $parametros["carga"];
$periodo = $parametros["periodo"];

$informacionAdicional = [
	'carga'   => $carga,
	'periodo' => $periodo
];


if(empty($config)){
	$configConsulta = mysqli_query($conexion, "SELECT * FROM ".BD_ADMIN.".configuracion WHERE conf_id_institucion='".$institucionId."' AND conf_agno='".$anio."'");
	$config = mysqli_fetch_array($configConsulta, MYSQLI_BOTH);
}

//Consultamos los estudiantes del grado y grupo
$filtroAdicional= "AND mat_grado='".$grado."' AND mat_grupo='".$grupo."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
$cursoActual=GradoServicios::consultarCurso($grado);
$consultaListaEstudante =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,$grupo,$anio);
$numEstudiantes=0;
$finalizado = true;
$erroresNumero=0;
$listadoEstudiantesError="";
$mensaje="";
	if($config['conf_porcentaje_completo_generar_informe']==1){
		$consultaListaEstudantesError =Estudiantes::listarEstudiantesNotasFaltantes($carga,$periodo,$cursoActual["gra_tipo"]);
		//Verificamos que el estudiante tenga sus notas al 100%
		if(mysqli_num_rows($consultaListaEstudantesError)>0){
			$erroresNumero=mysqli_num_rows($consultaListaEstudantesError);
			$contador=0;
			while($estudianteResultadoError = mysqli_fetch_array($consultaListaEstudantesError, MYSQLI_BOTH)){
			$contador++;
			$porcentajeAcumulado = $estudianteResultadoError['acumulado'] > 0 ? $estudianteResultadoError['acumulado'] : 0;
			$listadoEstudiantesError=$listadoEstudiantesError."<br><br>".$contador."): ".$estudianteResultadoError['mat_nombres']
			." ".$estudianteResultadoError['mat_primer_apellido']." ".$estudianteResultadoError['mat_segundo_apellido']
			." no tiene notas completas.<br>
			ID: <b>".$estudianteResultadoError['mat_id']."</b><br>
			Valor Actual: <b>".$porcentajeAcumulado."% </b>";
			}
			$finalizado = false;
		}
	}

	if($finalizado){
		$contBol=1;
		while($estudianteResultado = mysqli_fetch_array($consultaListaEstudante, MYSQLI_BOTH)){
			$estudiante = $estudianteResultado["mat_id"];
			include(ROOT_PATH."/main-app/definitivas.php");
			
			$idNumericoEstudiante = preg_replace('/^MAT/', '', $estudiante); //Para ser usado en el codigo de los registros

			//Consultamos si tiene registros en el boletín
			$consultaBoletinDatos=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_boletin 
			WHERE bol_carga='".$carga."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante."' AND institucion={$config['conf_id_institucion']} AND year={$anio}");
			$boletinDatos = mysqli_fetch_array($consultaBoletinDatos, MYSQLI_BOTH); 

			if($config['conf_porcentaje_completo_generar_informe']==2){
				//Verificamos que el estudiante tenga sus notas al porcentaje minimo permitido
				if($porcentajeActual < PORCENTAJE_MINIMO_GENERAR_INFORME and empty($boletinDatos['bol_nota'])){
					$erroresNumero++;
					$mensaje=$mensaje."<br><br>".$erroresNumero."): ".$estudianteResultado['mat_nombres']." ".$estudianteResultado['mat_primer_apellido']." ".$estudianteResultado['mat_segundo_apellido'] ." no tiene notas completas.<br>
					ID: <b>".$estudianteResultado['mat_id']."</b><br>
					Valor Actual: <b>".$porcentajeActual."% </b>";
					$finalizado = false;
					continue;
				}
			}
			$caso = 1; //Inserta la definitiva que viene normal 
			//Si ya existe un registro previo de definitiva TIPO 1
			if(!empty($boletinDatos['bol_id']) and $boletinDatos['bol_tipo']==1){
				
				if($boletinDatos['bol_nota']!=$definitiva || $boletinDatos['bol_porcentaje']!=$porcentajeActual){
					$caso = 2;//Se cambia la definitiva que tenía por la que viene. Sea menor o mayor, o igual solo si cambia el porcentaje.
				}else{
					$caso = 3;//No se hacen cambios. Todo sigue igual
					continue;
				}
				
			}
			//Si ya existe un registro previo de recuperación de periodo TIPO 2
			elseif(!empty($boletinDatos['bol_id']) and $boletinDatos['bol_tipo']==2){
				
				//Si la definitiva que viene está perdida 
				if($definitiva<$config[5]){
					$caso = 3;//No se hacen cambios. Todo sigue igual
					continue;
				}else{
					$caso = 4;//Se reemplaza la nota de recuperación actual por la definitiva que viene. Igual está ganada y no requiere de recuperación.
				}
				
			}
			//Si ya existe un registro previo de recuperación por Indicadores TIPO 3
			elseif(!empty($boletinDatos['bol_id']) and ($boletinDatos['bol_tipo']==3 or $boletinDatos['bol_tipo']==4)){
				$caso = 5;//Se actualiza la definitiva que viene y se cambia la recuperación del Indicador a nota anterior. 
			}
			//Vamos a obtener las definitivas por cada indicador y la definitiva general de la asignatura
			$notasPorIndicador = mysqli_query($conexion, "SELECT SUM((cal_nota*(act_valor/100))), act_id_tipo, ipc_valor FROM ".BD_ACADEMICA.".academico_calificaciones aac
			INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad AND aa.act_estado=1 AND aa.act_registrada=1 AND aa.act_periodo='".$periodo."' AND aa.act_id_carga='".$carga."' AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$anio}
			INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga ipc ON ipc.ipc_indicador=aa.act_id_tipo AND ipc.ipc_carga='".$carga."' AND ipc.ipc_periodo='".$periodo."' AND ipc.institucion={$config['conf_id_institucion']} AND ipc.year={$anio}
			WHERE aac.cal_id_estudiante='".$estudiante."' AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$anio}
			GROUP BY aa.act_id_tipo");
			$sumaNotaIndicador = 0; 
			while($notInd = mysqli_fetch_array($notasPorIndicador, MYSQLI_BOTH)){
				$consultaNum = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $notInd[1], $estudiante, $carga, $periodo, $anio);
				$num = mysqli_num_rows($consultaNum);

				
				$sumaNotaIndicador  += $notInd[0];
				
				if($num==0){
					Indicadores::eliminarRecuperacionIndicadorPeriodo($config, $notInd[1], $estudiante, $carga, $periodo, $anio);				
					
					Indicadores::guardarRecuperacionIndicador($conexionPDO, $config, $estudiante, $carga, $notInd[0], $notInd[1], $periodo, $notInd[2], $anio);
				}else{
					Indicadores::actualizarRecuperacionIndicador($config, $estudiante, $carga, $notInd[0], $notInd[1], $periodo, $notInd[2], $anio);
				}
			}
			$sumaNotaIndicador = round($sumaNotaIndicador,1); 
			if($caso == 2 or $caso == 4 or $caso == 5){
		
				if(!empty($boletinDatos['bol_historial_actualizacion']) && $boletinDatos['bol_historial_actualizacion']!=NULL){
					$actualizacion = json_decode($boletinDatos['bol_historial_actualizacion'], true);
				}else{
					$actualizacion = array();
				}
		
				$fecha=$boletinDatos['bol_fecha_registro'];
				if(!empty($boletinDatos['bol_ultima_actualizacion']) && $boletinDatos['bol_ultima_actualizacion']!=NULL){
					$fecha=$boletinDatos['bol_ultima_actualizacion'];
				}
		
				$numActualizacion= $boletinDatos['bol_actualizaciones']+1;
				$actualizacion[$numActualizacion] = [
					"nota anterior" 			=> $boletinDatos['bol_nota'],
					"fecha de actualización" 		=> $fecha,
					"porcentaje" 	=> $boletinDatos['bol_porcentaje']
				];
		
				mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_boletin SET bol_nota_anterior=bol_nota, bol_nota='".$definitiva."', bol_actualizaciones=bol_actualizaciones+1, bol_ultima_actualizacion=now(), bol_nota_indicadores='".$sumaNotaIndicador."', bol_tipo=1, bol_observaciones='Reemplazada', bol_porcentaje='".$porcentajeActual."', bol_historial_actualizacion='".json_encode($actualizacion)."' WHERE bol_carga='".$carga."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante."' AND institucion={$config['conf_id_institucion']} AND year={$anio}");
			}elseif($caso == 1){
				//Eliminamos por si acaso hay algún registro
				mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_boletin 
				WHERE bol_carga='".$carga."' AND bol_periodo='".$periodo."' AND bol_estudiante='".$estudiante."' AND institucion={$config['conf_id_institucion']} AND year={$anio}");			
				//INSERTAR LOS DATOS EN LA TABLA BOLETIN
				$codigoBOL=Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_boletin').$contBol;
				mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_boletin(bol_id, bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_fecha_registro, bol_actualizaciones, bol_nota_indicadores, bol_porcentaje, institucion, year)VALUES('".$codigoBOL."', '".$carga."', '".$estudiante."', '".$periodo."', '".$definitiva."', 1, now(), 0, '".$sumaNotaIndicador."', '".$porcentajeActual."', {$config['conf_id_institucion']}, {$anio})");
				
				$contBol++;
			}
			
			$numEstudiantes++;

		}
    
	
		mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_cargas SET car_periodo=car_periodo+1 WHERE car_id='".$carga."' AND institucion={$config['conf_id_institucion']} AND year={$anio}");
		$consulta_mat_area_est = mysqli_fetch_array(mysqli_query($conexion,"SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
		INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car.car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$anio}
		WHERE  car_id='".$carga."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$anio}"));
		$respuesta ="
		<h4>Resumen del proceso:</h4>
		- Total estudiantes calificados: {$numEstudiantes}<br><br>
		Datos releacionados:<br>
		- Cod. Carga : {$carga}<br>
		- Asignatura :{$consulta_mat_area_est["mat_nombre"]}<br>
		- Grado : {$grado}<br>
		- Grupo : {$grupo}<br>
		- Periodo : {$periodo}<br><br>
		";
		// fecha2 en este caso es la fecha actual
		$fechaFinal = new DateTime();
		$tiempoTrasncurrido=minutosTranscurridos($fechaInicio,$fechaFinal);
		$mensaje="La generaci&oacute;n de informe concluy&oacute; exitosamente.<br>
		".$tiempoTrasncurrido."<br>
		".$respuesta;
		$datos = array(
			"id" 	  => $resultadoJobs['job_id'],
			"mensaje" => $mensaje,
			"estado"  => JOBS_ESTADO_FINALIZADO,
		);
		SysJobs::actualizar($datos);
		SysJobs::enviarMensaje($resultadoJobs['job_responsable'],$mensaje,$resultadoJobs['job_id'],JOBS_TIPO_GENERAR_INFORMES,JOBS_ESTADO_FINALIZADO, $informacionAdicional);
	}else{
		
		if($intento>=3){				
		$mensaje="<a target=\"_blank\" href=\"../docente/calificaciones-faltantes.php?carga=".base64_encode($carga)."&periodo=".base64_encode($periodo)."&get=".base64_encode(100)."\">El informe no se pudo generar, coloque las notas a todos los estudiantes y vuelva a intentarlo.</a>";
		SysJobs::actualizarMensaje($resultadoJobs['job_id'],$intento,$mensaje,JOBS_ESTADO_FINALIZADO);
		SysJobs::enviarMensaje($resultadoJobs['job_responsable'],$mensaje.$listadoEstudiantesError,$resultadoJobs['job_id'],JOBS_TIPO_GENERAR_INFORMES,JOBS_ESTADO_ERROR, $informacionAdicional);
		}else{
			$texto="";
			if($erroresNumero>1){
				$texto= $erroresNumero."  estudiantes que les";
			 }else{
				$texto= $erroresNumero."  estudiante que le";
			 }
			 $mensaje="<a target=\"_blank\" href=\"../docente/calificaciones-faltantes.php?carga=".base64_encode($carga)."&periodo=".base64_encode($periodo)."&get=".base64_encode(100)."\"> El informe no se ha podido generar porque hay ".$texto." faltan notas.</a>";
			 SysJobs::actualizarMensaje($resultadoJobs['job_id'],$intento,$mensaje,JOBS_ESTADO_PENDIENTE);
		}
	}
	

}



function minutosTranscurridos($fecha_i,$fecha_f)
{
	$intervalo = $fecha_i->diff($fecha_f);
	$minutos = $intervalo->i;
	$segundos = $intervalo->s;
	return " Finaliz&oacute; en: <i> {$minutos} min y {$segundos} seg.</i>";
}

exit()
?>