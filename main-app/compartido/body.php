<?php
$consultaFinanzas=mysqli_query($conexion, "SELECT
(SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=1),
(SELECT sum(fcu_valor) FROM finanzas_cuentas WHERE fcu_usuario='".$_SESSION["id"]."' AND fcu_anulado=0 AND fcu_tipo=3)
");
$resumenEC = mysqli_fetch_array($consultaFinanzas, MYSQLI_BOTH);
$saldoEC = ($resumenEC[0] - $resumenEC[1]) * -1;
?>

<body onLoad="listarTareas()" class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md <?=$datosUsuarioActual['uss_tema_header'];?> <?=$datosUsuarioActual['uss_tema_sidebar'];?> 
			 <?=$datosUsuarioActual['uss_tema_logo'];?>"> <!-- chat-sidebar-open-->
	
	
<div class="loader"></div>

<?php include("../compartido/modal-licencia.php");?>

<?php include("../compartido/modal-anuncios.php");?>
	
<?php include("../compartido/modal-acciones.php");?>

<?php include("../compartido/modal-terminos.php");?>

<?php include("../compartido/modal-contrato.php");?>