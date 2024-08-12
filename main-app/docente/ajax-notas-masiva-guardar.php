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

$idPaginaInterna = 'DC0102';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

$datosMensaje = AjaxCalificaciones::ajaxGuardarNotasMasiva($datosCargaActual, $_POST["codNota"], $_POST["nota"]);

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
</script>

<div class="alert alert-<?=$datosMensaje['estado']?>">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<i class="icon-exclamation-sign"></i><strong>INFORMACI&Oacute;N:</strong> <?=$datosMensaje['mensaje']?>
</div>

<?php 
if($_POST["recargarPanel"]==1){
?>
	<script type="text/javascript">
	setTimeout(function() {
    	listarInformacion('listar-calificaciones-todas.php', 'nav-calificaciones-todas');
  	}, 3000);
	</script>
<?php
}

if($_POST["recargarPanel"]==0){
?>
	<script type="text/javascript">
	setTimeout('document.location.reload()',5000);
	</script>
<?php
}
