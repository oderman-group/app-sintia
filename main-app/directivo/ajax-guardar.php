<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0178';

include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

$mensajeNot = 'Hubo un error al guardar las cambios';
//Bloquear y desbloquear
if($_POST["operacion"]==1){
	UsuariosPadre::bloquearUsuario($config, $_POST["idR"], $_POST["valor"]);

	$mensajeNot = 'El usuario ha cambiado de estado correctamente.';
}

if($_POST["operacion"]==3){
	try{
		mysqli_query($conexion, "UPDATE ".$baseDatosServicios.".instituciones SET ins_bloqueada='".$_POST["valor"]."' 
		WHERE ins_id='".$_POST["idR"]."' AND ins_enviroment='".ENVIROMENT."'");
	} catch (Exception $e) {
		include("../compartido/error-catch-to-report.php");
	}

	$mensajeNot = 'La institución ha cambiado de estado correctamente.';
}

include("../compartido/guardar-historial-acciones.php");
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
if($_POST["operacion"]==1 || $_POST["operacion"]==3){
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