<?php
include_once("socket.php");
$consultaFinanzas=mysqli_query($conexion, "SELECT
(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
(SELECT sum(fcu_valor) FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=3 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]})
");
$resumenEC = mysqli_fetch_array($consultaFinanzas, MYSQLI_BOTH);
$saldoEC = ($resumenEC[0] - $resumenEC[1]) * -1;
?>

<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md 
<?=$datosUsuarioActual['uss_tema_header'];?>  
<?=$datosUsuarioActual['uss_tema_sidebar'];?>  
<?=$datosUsuarioActual['uss_tema_logo'];?> 
<?=$datosUsuarioActual['uss_tipo_menu'];?> 
"> <!-- chat-sidebar-open-->
	
<script src="<?=BASE_URL;?>/main-app/js/Mensajes.js?v=<?php echo getFileVersion(ROOT_PATH.'/main-app/js/Mensajes.js'); ?>" ></script>
<div class="loader"></div>
 
<?php include_once(ROOT_PATH."/main-app/compartido/overlay.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/ComponenteModal.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-centralizado.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-general.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-licencia.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-anuncios.php");?>
	
<?php include_once(ROOT_PATH."/main-app/compartido/modal-acciones.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-terminos.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-contrato.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-asignaciones.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-comprar-modulo.php");?>

<?php include_once(ROOT_PATH."/main-app/compartido/modal-comprar-paquete.php");?>