<?php
include("session.php");
require_once("../class/Estudiantes.php");
include("verificar-carga.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
require_once(ROOT_PATH."/main-app/class/AjaxCalificaciones.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

$existeNota = Calificaciones::traerCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);

$mensajeNot = 'Hubo un error al guardar las cambios';

//Para guardar notas
if($_POST["operacion"]==1){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;

	if(empty($existeNota['cal_id'])){
		Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
		
		Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year, cal_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], date("Y-m-d H:i:s"), 0, $config['conf_id_institucion'], $_SESSION["bd"]]);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
	}else{
		$update = "cal_nota=".$_POST["nota"]."";
		Calificaciones::actualizarNotaActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"], $update);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
	}
	$mensajeNot = 'La nota se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones
if($_POST["operacion"]==2){
	if(empty($existeNota['cal_id'])){
		Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
		
		Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_observaciones, cal_id_actividad, institucion, year, cal_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], $config['conf_id_institucion'], $_SESSION["bd"]]);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
	}else{
		$update = "cal_observaciones=".mysqli_real_escape_string($conexion,$_POST["nota"])."";
		Calificaciones::actualizarNotaActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"], $update);

		Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
	}
	$mensajeNot = 'La observación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para la misma nota para todos los estudiantes
if($_POST["operacion"]==3){
	
	$filtroAdicional= "AND mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
	$consultaE =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"");
	

	while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){

		$existeNota = Calificaciones::traerCalificacionActividadEstudiante($config, $_POST["codNota"], $estudiantes['mat_id']);
		
		if(empty($existeNota['cal_id'])){
			Calificaciones::eliminarCalificacionActividadEstudiante($config, $_POST["codNota"], $estudiantes['mat_id']);
			
			Calificaciones::guardarNotaActividadEstudiante($conexionPDO, "cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones, institucion, year, cal_id", [$estudiantes['mat_id'],$_POST["nota"],$_POST["codNota"], date("Y-m-d H:i:s"), 0, $config['conf_id_institucion'], $_SESSION["bd"]]);
			
			Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
		}else{
			$update = "cal_nota=".$_POST["nota"]."";
			Calificaciones::actualizarNotaActividadEstudiante($config, $_POST["codNota"], $estudiantes['mat_id'], $update);
			
			Actividades::marcarActividadRegistrada($config, $_POST["codNota"]);
		}
	}
	$mensajeNot = 'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...';
}

//Para guardar recuperaciones
if($_POST["operacion"]==4){
	$notaA = Calificaciones::traerCalificacionActividadEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
	
	AjaxCalificaciones::ajaxGuardarNotaRecuperacion($conexion, $config, $_POST["codEst"], $_POST["nombreEst"], $_POST["codNota"], $_POST["nota"], $notaA['cal_nota']);
	
	$mensajeNot = 'La nota de recuperación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}
?>

<script type="text/javascript">
function notifica(){
	$.toast({
		heading: 'Cambios guardados',  
		text: '<?=$mensajeNot;?>',
		position: 'bottom-right',
        showHideTransition: 'slide',
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
<?php 
if($_POST["operacion"]==3){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',5000);
	</script>
<?php
}
?>