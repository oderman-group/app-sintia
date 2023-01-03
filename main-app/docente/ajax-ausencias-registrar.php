<?php include("session.php");?>
<?php include("../../config-general/config.php");?>
<?php
include("../modelo/conexion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM academico_ausencias WHERE aus_id_clase='".$_POST["codNota"]."' AND aus_id_estudiante='".$_POST["codEst"]."'");

$num = mysqli_num_rows($consulta);
$rC = mysqli_fetch_array($consulta, MYSQLI_BOTH);
if($num==0){
	mysqli_query($conexion, "DELETE FROM academico_ausencias WHERE aus_id_clase='".$_POST["codNota"]."' AND aus_id_estudiante='".$_POST["codEst"]."'");
	
	mysqli_query($conexion, "INSERT INTO academico_ausencias(aus_id_estudiante, aus_ausencias, aus_id_clase)VALUES('".$_POST["codEst"]."','".$_POST["nota"]."','".$_POST["codNota"]."')");
	
	mysqli_query($conexion, "UPDATE academico_clases SET cls_registrada=1, cls_fecha_registro=now() WHERE cls_id='".$_POST["codNota"]."'");
	
}else{
	mysqli_query($conexion, "UPDATE academico_ausencias SET aus_ausencias='".$_POST["nota"]."' WHERE aus_id='".$rC[0]."'");
	
	mysqli_query($conexion, "UPDATE academico_clases SET cls_registrada=1, cls_fecha_modificacion=now() WHERE cls_id='".$_POST["codNota"]."'");
	
}	
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
