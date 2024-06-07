<?php
session_start();
include("../../config-general/config.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$indicadorObg = Indicadores::traerIndicadoresDatos($_POST["indicador"]);

$cargaEjemplo = CargaAcademica::traerCargaMateriaPorID($config, $_POST["carga"]);

$cargas = CargaAcademica::traerCargasMateriasPorCursoMateria($config, $cargaEjemplo['car_curso'], $cargaEjemplo['car_materia']);

while($cgs = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
	$ipc = Indicadores::traerRelacionCargaIndicador($conexion, $config, $cgs['car_id'], $_POST["indicador"]);
	if($ipc[0]==""){
		$p=1;
		while($p<=$config['conf_periodos_maximos']){

			Indicadores::guardarIndicadorCarga($conexion, $conexionPDO, $config, $cgs['car_id'], $_POST["indicador"], $p, $_POST, NULL, 0, $indicadorObg['ind_valor']);
			
			$p++;
		}
	}else{
		Indicadores::eliminarRelacionCargaIndicador($conexion, $config, $cgs['car_id'], $_POST["indicador"]);
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