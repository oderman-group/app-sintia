<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
$datosMensaje = Calificaciones::ajaxNivelacionesRegistrar($_POST["codEst"],$_POST["carga"],$_POST["nota"]);
include("../compartido/guardar-historial-acciones.php");
?>
<script type="text/javascript">
function notifica(){
	$.toast({
		heading: '<?=$datosMensaje['heading']?>',  
		text: '<?=$datosMensaje['mensaje']?>',
		position: 'bottom-right',
        showHideTransition: 'slide',
		loaderBg:'#ff6849',
		icon: '<?=$datosMensaje['estado']?>',
		hideAfter: 3000, 
		stack: 6
	});
}
setTimeout ("notifica()", 100);
</script>

<div class="alert alert-<?=$datosMensaje['estado']?>">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$datosMensaje['mensaje']?>
</div>