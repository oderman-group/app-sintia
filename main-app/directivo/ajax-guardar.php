<?php include("session.php");?>
<?php //include("verificar-carga.php");?>
<?php
$mensajeNot = 'Hubo un error al guardar las cambios';
//Bloquear y desbloquear
if($_POST["operacion"]==1){
	mysqli_query($conexion, "UPDATE usuarios SET uss_bloqueado='".$_POST["valor"]."' WHERE uss_id='".$_POST["idR"]."'");
	
	$mensajeNot = 'El usuario ha cambiado de estado correctamente.';
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
if($_POST["operacion"]<1){
?>
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$mensajeNot;?>
</div>
<?php }?>

<?php 
if($_POST["operacion"]==2){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',2000);
	</script>
<?php
}
?>