<?php
include("../modelo/conexion.php");
include("../../config-general/config.php");
$cdnota=mysqli_query($conexion, "SELECT * FROM disiplina_nota WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_id_carga='".$_POST["carga"]."' AND dn_periodo='".$_POST["periodo"]."';");

if(mysqli_num_rows($cdnota)==0){
	if(isset($_POST["nota"])){
	mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_nota, dn_fecha, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["nota"]."', now(),'".$_POST["periodo"]."')");
	}else{
	mysqli_query($conexion, "INSERT INTO disiplina_nota(dn_cod_estudiante, dn_id_carga, dn_observacion, dn_fecha, dn_periodo)VALUES('".$_POST["codEst"]."','".$_POST["carga"]."','".$_POST["observacion"]."', now(),'".$_POST["periodo"]."')");	
		}
	
}else{
	if(isset($_POST["nota"])){
	mysqli_query($conexion, "UPDATE disiplina_nota SET dn_nota='".$_POST["nota"]."', dn_fecha=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_id_carga='".$_POST["carga"]."' AND dn_periodo='".$_POST["periodo"]."';");
	}else{
	mysqli_query($conexion, "UPDATE disiplina_nota SET dn_observacion='".$_POST["observacion"]."', dn_fecha=now() WHERE dn_cod_estudiante='".$_POST["codEst"]."' AND dn_id_carga='".$_POST["carga"]."' AND dn_periodo='".$_POST["periodo"]."';");	
		}
	
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
<?php	
	exit();

?>