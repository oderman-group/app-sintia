<?php include("../../config-general/config.php");?>
<?php
if(trim($_POST["ih"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una I.H correcta</span>";
	exit();
}
include("../modelo/conexion.php");
mysqli_query($conexion, "DELETE FROM academico_intensidad_curso WHERE ipc_curso='".$_POST["curso"]."' AND ipc_materia='".$_POST["materia"]."'");

mysqli_query($conexion, "INSERT INTO academico_intensidad_curso(ipc_curso, ipc_materia, ipc_intensidad)VALUES('".$_POST["curso"]."','".$_POST["materia"]."','".$_POST["ih"]."')");

mysqli_query($conexion, "UPDATE academico_cargas SET car_ih='".$_POST["ih"]."' WHERE car_curso='".$_POST["curso"]."' AND car_materia='".$_POST["materia"]."'");

else{
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
		<button type="button" class="close" data-dismiss="alert">x</button>
		<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> Los cambios se ha guardado correctamente!.
	</div>
<?php
	exit();
}
?>