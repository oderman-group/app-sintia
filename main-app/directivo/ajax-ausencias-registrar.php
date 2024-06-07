<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");

$rC = Ausencias::traerAusenciasClaseEstudiante($config, $_POST["codNota"], $_POST["codEst"]);
if(empty($rC)){
	if(!empty($rC['aus_id'])){
		Ausencias::eliminarAusenciasID($config, $rC['aus_id']);
	}

	Ausencias::guardarAusencia($conexionPDO, "aus_id_estudiante, aus_ausencias, aus_id_clase, institucion, year, aus_id", [$_POST["codEst"],$_POST["nota"],$_POST["codNota"], $config['conf_id_institucion'], $_SESSION["bd"]]);
	
	try{
		mysqli_query($conexion, "UPDATE academico_clases SET cls_registrada=1, cls_fecha_registro=now() WHERE cls_id='".$_POST["codNota"]."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}	
	
}else{
	$update = "aus_ausencias=".$_POST["nota"]."";
	Ausencias::actualizarAusencia($config, $rC['aus_id'], $update);
	
	try{
		mysqli_query($conexion, "UPDATE academico_clases SET cls_registrada=1, cls_fecha_modificacion=now() WHERE cls_id='".$_POST["codNota"]."'");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
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
