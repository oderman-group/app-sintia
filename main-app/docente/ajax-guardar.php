<?php include("session.php");?>
<?php include("verificar-carga.php");?>
<?php
$mensajeNot = 'Hubo un error al guardar las cambios';

//Actualizar respuesta de una pregunta
if($_POST["operacion"]==1){
	mysqli_query($conexion, "UPDATE academico_actividad_respuestas SET resp_descripcion='".$_POST["valor"]."' WHERE resp_id='".$_POST["idR"]."'");
	
	$mensajeNot = 'La respuesta se ha actualizado correctamente.';
}

//Agregar respuesta a una pregunta
if($_POST["operacion"]==2){
	mysqli_query($conexion, "INSERT INTO academico_actividad_respuestas(resp_descripcion, resp_correcta, resp_id_pregunta)VALUES('".$_POST["valor"]."', 0, '".$_POST["pregunta"]."')");
	
	$mensajeNot = 'La respuesta se ha agregado correctamente.';
}
//Clase disponible o no
if($_POST["operacion"]==3){
	mysqli_query($conexion, "UPDATE academico_clases SET cls_disponible='".$_POST["valor"]."' WHERE cls_id='".$_POST["idR"]."'");
	
	$mensajeNot = 'La clase ha cambiado de estado correctamente.';
}
//Impedir retrasos o no en las actividades
if($_POST["operacion"]==4){
	mysqli_query($conexion, "UPDATE academico_actividad_tareas SET tar_impedir_retrasos='".$_POST["valor"]."' WHERE tar_id='".$_POST["idR"]."'");
	
	$mensajeNot = 'La actividad ha cambiado de estado correctamente.';
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

<?php 
if($_POST["operacion"]<3){
?>
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$mensajeNot;?>
</div>
<?php
}
?>



<?php 
if($_POST["operacion"]==2){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',2000);
	</script>
<?php
}
?>