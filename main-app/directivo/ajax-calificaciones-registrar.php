<?php include("session.php");?>
<?php include("verificar-carga.php");?>
<?php
$consultaNum=mysqli_query($conexion, "SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$_POST["codEst"]."'");
$num = mysqli_num_rows($consultaNum);


$mensajeNot = 'Hubo un error al guardar las cambios';

//Para guardar notas
if($_POST["operacion"]==1){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;

	if($num==0){
		mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0)");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'");
		
	}else{
		mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'");
		
	}
	$mensajeNot = 'La nota se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones
if($_POST["operacion"]==2){
	if($num==0){
		mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_observaciones, cal_id_actividad)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."')");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'");
		
	}else{
		mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_observaciones='".$_POST["nota"]."' WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
		
		mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'");
		
	}
	$mensajeNot = 'La observación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para la misma nota para todos los estudiantes
if($_POST["operacion"]==3){
	
	$consultaE = mysqli_query($conexion, "SELECT academico_matriculas.mat_id FROM academico_matriculas
	WHERE mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
	

	while($estudiantes = mysqli_fetch_array($consultaE, MYSQLI_BOTH)){
		$consultaNumE=mysqli_query($conexion, "SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
		WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$estudiantes['mat_id']."'");
		$numE = mysqli_num_rows($consultaNumE);
		
		if($numE==0){
			mysqli_query($conexion, "DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$estudiantes['mat_id']."'");
			
			mysqli_query($conexion, "INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$estudiantes['mat_id']."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0)");
			
			mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'");
			
		}else{
			mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$estudiantes['mat_id']."'");
			
			mysqli_query($conexion, "UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'");
			
		}
	}
	$mensajeNot = 'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La página se actualizará en unos segundos para que vea los cambios...';
}

//Para guardar recuperaciones
if($_POST["operacion"]==4){
	$consultaNotaA=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$_POST["codEst"]." AND cal_id_actividad='".$_POST["codNota"]."'");
	$notaA = mysqli_fetch_array($consultaNotaA, MYSQLI_BOTH);
	
	mysqli_query($conexion, "INSERT INTO academico_recuperaciones_notas(rec_cod_estudiante, rec_nota, rec_id_nota, rec_fecha, rec_nota_anterior)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(),'".$notaA[3]."')");
	
	mysqli_query($conexion, "UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'");
	
	
	$mensajeNot = 'La nota de recuperación se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}
?>

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
<?php 
if($_POST["operacion"]==3){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',5000);
	</script>
<?php
}
?>