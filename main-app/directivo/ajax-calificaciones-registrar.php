<?php include("session.php");?>
<?php include("verificar-carga.php");?>
<?php
$num = mysql_num_rows( mysql_query("SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$_POST["codEst"]."'",$conexion));
if(mysql_errno()!=0){echo mysql_error(); exit();}

$mensajeNot = 'Hubo un error al guardar las cambios';

//Para guardar notas
if($_POST["operacion"]==1){
	if(trim($_POST["nota"])==""){echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";exit();}
	if($_POST["nota"]>$config[4]) $_POST["nota"] = $config[4]; if($_POST["nota"]<1) $_POST["nota"] = 1;

	if($num==0){
		mysql_query("DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0)",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}
	$mensajeNot = 'La nota se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
}

//Para guardar observaciones
if($_POST["operacion"]==2){
	if($num==0){
		mysql_query("DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("INSERT INTO academico_calificaciones(cal_id_estudiante, cal_observaciones, cal_id_actividad)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."')",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		mysql_query("UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'",$conexion);
		if(mysql_errno()!=0){echo mysql_error(); exit();}
	}else{
		mysql_query("UPDATE academico_calificaciones SET cal_observaciones='".$_POST["nota"]."' WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
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

	while($estudiantes = mysql_fetch_array($consultaE)){
		$numE = mysql_num_rows(mysql_query("SELECT academico_calificaciones.cal_id_actividad, academico_calificaciones.cal_id_estudiante FROM academico_calificaciones 
		WHERE academico_calificaciones.cal_id_actividad='".$_POST["codNota"]."' AND academico_calificaciones.cal_id_estudiante='".$estudiantes['mat_id']."'",$conexion));
		if(mysql_errno()!=0){echo mysql_error(); exit();}
		if($numE==0){
			mysql_query("DELETE FROM academico_calificaciones WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$estudiantes['mat_id']."'",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
			mysql_query("INSERT INTO academico_calificaciones(cal_id_estudiante, cal_nota, cal_id_actividad, cal_fecha_registrada, cal_cantidad_modificaciones)VALUES('".$estudiantes['mat_id']."','".$_POST["nota"]."','".$_POST["codNota"]."', now(), 0)",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
			mysql_query("UPDATE academico_actividades SET act_registrada=1, act_fecha_registro=now() WHERE act_id='".$_POST["codNota"]."'",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
		}else{
			mysql_query("UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$estudiantes['mat_id']."'",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
			mysql_query("UPDATE academico_actividades SET act_registrada=1 WHERE act_id='".$_POST["codNota"]."'",$conexion);
			if(mysql_errno()!=0){echo mysql_error(); exit();}
		}
	}
	$mensajeNot = 'Se ha guardado la misma nota para todos los estudiantes en esta actividad. La p??gina se actualizar?? en unos segundos para que vea los cambios...';
}

//Para guardar recuperaciones
if($_POST["operacion"]==4){
	$notaA = mysql_fetch_array(mysql_query("SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$_POST["codEst"]." AND cal_id_actividad='".$_POST["codNota"]."'",$conexion));
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	mysql_query("INSERT INTO academico_recuperaciones_notas(rec_cod_estudiante, rec_nota, rec_id_nota, rec_fecha, rec_nota_anterior)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."', now(),'".$notaA[3]."')",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	mysql_query("UPDATE academico_calificaciones SET cal_nota='".$_POST["nota"]."', cal_fecha_modificada=now(), cal_cantidad_modificaciones=cal_cantidad_modificaciones+1 WHERE cal_id_actividad='".$_POST["codNota"]."' AND cal_id_estudiante='".$_POST["codEst"]."'",$conexion);
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	
	$mensajeNot = 'La nota de recuperaci??n se ha guardado correctamente para el estudiante <b>'.strtoupper($_POST["nombreEst"]).'</b>';
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