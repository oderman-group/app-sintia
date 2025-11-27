<?php
include("session.php");
include("verificar-carga.php");

Modulos::validarAccesoDirectoPaginas();
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Evaluaciones.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

$mensajeNot = 'Hubo un error al guardar las cambios';

//Actualizar respuesta de una pregunta
if ($_POST["operacion"] == 1) {

	$idPaginaInterna = 'DC0091';
	include("../compartido/historial-acciones-guardar.php");

	Evaluaciones::actualizarRespuesta($conexion, $config, $_POST);

	include("../compartido/guardar-historial-acciones.php");
	$mensajeNot = 'La respuesta se ha actualizado correctamente.';
}

//Agregar respuesta a una pregunta
if ($_POST["operacion"] == 2) {

	$idPaginaInterna = 'DC0047';
	include("../compartido/historial-acciones-guardar.php");

    try {
        Evaluaciones::guardarRespuesta($config, $_POST);
        $mensajeNot = 'La respuesta se ha agregado correctamente.';
        include("../compartido/guardar-historial-acciones.php");
    } catch (Exception $e) {
        $mensajeNot = 'Problema para guardar la respuesta: '.$e;
    }
    
}

//Clase disponible o no
if ($_POST["operacion"] == 3) {

	$idPaginaInterna = 'DC0048';
	include("../compartido/historial-acciones-guardar.php");
	
	Clases::cambiarEstadoClase($conexion, $config, $_POST);

	include("../compartido/guardar-historial-acciones.php");
	$mensajeNot = 'La clase ha cambiado de estado correctamente.';
}

//Impedir retrasos o no en las actividades
if ($_POST["operacion"] == 4) {

	$idPaginaInterna = 'DC0049';
	include("../compartido/historial-acciones-guardar.php");
	
	Actividades::impedirRetrasoActividad($conexion, $config, $_POST);

	include("../compartido/guardar-historial-acciones.php");
	$mensajeNot = 'La actividad ha cambiado de estado correctamente.';
}
?>

<script type="text/javascript">
function notifica(){
	$.toast({
		heading: 'Cambios guardados',  
		text: '<?=$mensajeNot;?>',
		position: 'bottom-right',
        showHideTransition: 'slide',
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

if($_POST["operacion"]==2){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',2000);
	</script>
<?php
}
?>