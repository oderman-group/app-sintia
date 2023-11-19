<?php
session_start();
include("../../config-general/config.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

if(trim($_POST["nota"])==""){
    echo "<span style='color:red; font-size:16px;'>Digite una nota correcta</span>";
	exit();
}

if($_POST["op"]==1){
	if($_POST["nota"]>$config[4]){ $_POST["nota"] = $config[4];} if($_POST["nota"]<1){ $_POST["nota"] = 1;}
}

$consulta = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_nivelaciones WHERE niv_cod_estudiante='".$_POST["codEst"]."' AND niv_id_asg='".$_POST["carga"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");

$num = mysqli_num_rows($consulta);
$rB = mysqli_fetch_array($consulta, MYSQLI_BOTH);
if($num==0 and $_POST["op"]==1){
	mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_nivelaciones WHERE niv_id='".$rB['niv_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	$codigo=Utilidades::generateCode("NIV");
	
	mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_nivelaciones(niv_id, niv_id_asg, niv_cod_estudiante, niv_definitiva, niv_fecha, institucion, year)VALUES('".$codigo."', '".$_POST["carga"]."','".$_POST["codEst"]."','".$_POST["nota"]."',now(), {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
	
}else{
	switch($_POST["op"]){
		case 1:
			mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_nivelaciones SET niv_definitiva='".$_POST["nota"]."' WHERE niv_id='".$rB['niv_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
			
		break;
		
		case 2:
			mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_nivelaciones SET niv_acta='".$_POST["nota"]."' WHERE niv_id='".$rB['niv_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
			
		break;
		
		case 3:
			mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_nivelaciones SET niv_fecha_nivelacion='".$_POST["nota"]."' WHERE niv_id='".$rB['niv_id']."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
			
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