<?php
session_start();
include("../../config-general/config.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}

if($_POST["op"]==1){
	if($_POST["nota"]>$config[4]){ $_POST["nota"] = $config[4];} if($_POST["nota"]<1){ $_POST["nota"] = 1;}
}

$consulta = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $_POST["codEst"], $_POST["carga"]);

$num = mysqli_num_rows($consulta);
$rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
if($num==0 and $_POST["op"]==1){
	Calificaciones::eliminarNivelacion($conexion, $config, $rB['niv_id']);
	
	Calificaciones::guardarNivelacion($conexion, $conexionPDO, $config, $_POST);
	
}else{
	switch($_POST["op"]){
		case 1:
			Calificaciones::actualizarDefinitivaNivelacion($conexion, $config, $_POST["nota"], $rB['niv_id']);
			
		break;
		
		case 2:
			Calificaciones::actualizarActaNivelacion($conexion, $config, $_POST["nota"], $rB['niv_id']);
			
		break;
		
		case 3:
			Calificaciones::actualizarFechaNivelacion($conexion, $config, $_POST["nota"], $rB['niv_id']);
			
		break;
	}
}	
?>
	<script type="text/javascript">
		function notifica(){
			var unique_id = $.gritter.add({
				// (string | mandatory) the heading of the notification
				title: 'Correcto',
				// (string | mandatory) the text inside the notification
				text: 'Los cambios se han guardado correctamente!',
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
		<i class="icon-exclamation-sign"></i><strong>INFORMACIÓN:</strong> Los cambios se ha guardado correctamente!.
	</div>
<?php	
	exit();
?>