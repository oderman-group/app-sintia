<?php
session_start();
$idPaginaInterna = 'CM0006';
include("../../config-general/config.php");
require_once("../class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

$conexionPDOInstance = new Conexion;
$conexionPDO         = $conexionPDOInstance->conexionPDO(SERVER, USER, PASSWORD, BD_ADMIN);

$config = Plataforma::sesionConfiguracion();
$_SESSION["configuracion"] = $config;

$grado =base64_decode($_GET["grado"]);
$grupo =base64_decode($_GET["grupo"]);
$carga = base64_decode($_GET["carga"]);
$periodo = base64_decode($_GET["periodo"]);

if($config['conf_porcentaje_completo_generar_informe']==1){
	$cursoActual=GradoServicios::consultarCurso($grado);
	$consultaListaEstudantesError =Estudiantes::listarEstudiantesNotasFaltantes($carga,$periodo,$cursoActual["gra_tipo"]);
	//Verificamos que el estudiante tenga sus notas al 100%
	if(mysqli_num_rows($consultaListaEstudantesError)>0){
?>
		<script language="javascript">window.location.href="../docente/page-info.php?idmsg=108&carga=<?=$_GET["carga"];?>&periodo=<?=$_GET["periodo"];?>";</script>
<?php		
		exit();
	}
}

$infoCarga = CargaAcademica::cargasDatosEnSesion($carga, $_SESSION["id"]);
$datosCarga = $infoCarga['datosCargaActual'];

//Consultamos los estudiantes del grado y grupo
$consulta = Estudiantes::escogerConsultaParaListarEstudiantesParaDocentes($datosCarga);

$contBol=1;
 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	 
	//DEFINITIVAS
	$carga = $carga;
	$periodo = $periodo;
	$estudiante = $resultado['mat_id'];
	include("../definitivas.php");
			
	$idNumericoEstudiante = preg_replace('/^MAT/', '', $estudiante); //Para ser usado en el codigo de los registros
	 
	//Consultamos si tiene registros en el boletín
	$boletinDatos = Boletin::traerNotaBoletinCargaPeriodo($config, $periodo, $estudiante, $carga);

	if($config['conf_porcentaje_completo_generar_informe']==2){
		//Verificamos que el estudiante tenga sus notas al 100%
		if($porcentajeActual < PORCENTAJE_MINIMO_GENERAR_INFORME && empty($boletinDatos['bol_nota'])){
			continue;
		}
	}
	
	$caso = 1; //Inserta la definitiva que viene normal 
	
	 
	//Si ya existe un registro previo de definitiva TIPO 1
	 if(!empty($boletinDatos['bol_id']) and $boletinDatos['bol_tipo']==1){
		 
		if($boletinDatos['bol_nota']!=$definitiva || $boletinDatos['bol_porcentaje']!=$porcentajeActual){
			$caso = 2;//Se cambia la definitiva que tenía por la que viene. Sea menor o mayor.
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
	$notasPorIndicador = Calificaciones::traerNotasPorIndicador($config, $carga, $resultado['mat_id'], $periodo);
	$sumaNotaIndicador = 0; 
	
	while($notInd = mysqli_fetch_array($notasPorIndicador, MYSQLI_BOTH)){
		$consultaNum = Indicadores::consultaRecuperacionIndicadorPeriodo($config, $notInd[1], $resultado['mat_id'], $carga, $periodo);
		$num = mysqli_num_rows($consultaNum);
		
		$sumaNotaIndicador  += $notInd[0];
		
		if($num==0){
			Indicadores::eliminarRecuperacionIndicadorPeriodo($config, $notInd[1], $resultado['mat_id'], $carga, $periodo);				
			
			Indicadores::guardarRecuperacionIndicador($conexionPDO, $config, $resultado['mat_id'], $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
		}else{
			Indicadores::actualizarRecuperacionIndicador($config, $resultado['mat_id'], $carga, $notInd[0], $notInd[1], $periodo, $notInd[2]);
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
	
		$update = "bol_nota_anterior=bol_nota, bol_nota=".$definitiva.", bol_nota_indicadores=".$sumaNotaIndicador.", bol_tipo=1, bol_observaciones='Reemplazada', bol_porcentaje=".$porcentajeActual.", bol_historial_actualizacion=".json_encode($actualizacion)."";
		Boletin::actualizarNotaBoletin($config, $boletinDatos['bol_id'], $update);
	}elseif($caso == 1){
		//Eliminamos por si acaso hay algún registro
		if(!empty($boletinDatos['bol_id'])){
			Boletin::eliminarNotaBoletinID($config, $boletinDatos['bol_id']);
		}
			
		//INSERTAR LOS DATOS EN LA TABLA BOLETIN	
		Boletin::guardarNotaBoletin($conexionPDO, "bol_carga, bol_estudiante, bol_periodo, bol_nota, bol_tipo, bol_fecha_registro, bol_actualizaciones, bol_nota_indicadores, bol_porcentaje, institucion, year, bol_id", [$carga, $estudiante, $periodo, $definitiva, 1, date("Y-m-d H:i:s"), 0, $sumaNotaIndicador, $porcentajeActual, $config['conf_id_institucion'], $_SESSION["bd"]]);
		
		$contBol++;
	}
	 
		 	
}

$update = "car_periodo=car_periodo+1";
CargaAcademica::actualizarCargaPorID($config, $carga, $update);

include("../compartido/guardar-historial-acciones.php");
?>
	<script language="javascript">window.location.href="../docente/page-info.php?idmsg=109&curso=<?=$_GET["grado"];?>&grupo=<?=$_GET["grupo"];?>&periodo=<?=$_GET["periodo"];?>";</script>
<?php
	exit();
?>