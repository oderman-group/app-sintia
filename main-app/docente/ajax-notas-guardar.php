<?php 
include("session.php");
include("verificar-carga.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/AjaxCalificaciones.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

Modulos::validarAccesoDirectoPaginas();

$infoCargaActual = CargaAcademica::cargasDatosEnSesion($cargaConsultaActual, $_SESSION["id"]);
$_SESSION["infoCargaActual"] = $infoCargaActual;
$datosCargaActual = $_SESSION["infoCargaActual"]['datosCargaActual'];

if( !CargaAcademica::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual) ) { 
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=208";</script>';
    exit();
}

$idPaginaInterna = 'DC0092';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

$data = [
	'codEst'          => $_POST["codEst"],
	'nombreEst'       => $_POST["nombreEst"],
	'codNota'         => $_POST["codNota"],
	'nota'            => $_POST["nota"],
	'notaAnterior'    => $_POST["notaAnterior"],
	'tipoNota'        => 1,
	'target'          => Calificaciones::TIPO_GUARDAR_NOTA,
];

$datosMensaje = Calificaciones::direccionarCalificacion($data);

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
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

// Ocultar la notificación automáticamente después de 5 segundos
setTimeout(function() {
	const alertDiv = document.querySelector('.alert.alert-<?=$datosMensaje['estado']?>');
	if (alertDiv) {
		alertDiv.style.transition = 'opacity 0.3s ease-out';
		alertDiv.style.opacity = '0';
		setTimeout(function() {
			alertDiv.style.display = 'none';
		}, 300);
	}
}, 5000);
</script>

<div class="alert alert-<?=$datosMensaje['estado']?>" style="z-index: 999;">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$datosMensaje['mensaje']?>
</div>
