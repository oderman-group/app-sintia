<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DV0060';
include("../compartido/historial-acciones-guardar.php");

try{
    mysqli_query($conexion, "UPDATE " . $baseDatosMarketPlace . ".empresas SET emp_eliminado=1 WHERE emp_id='".base64_decode($_GET["idR"])."'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

try{
    mysqli_query($conexion, "UPDATE " . $baseDatosMarketPlace . ".productos SET prod_estado=1 WHERE prod_empresa='".base64_decode($_GET["idR"])."'");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}

include("../compartido/guardar-historial-acciones.php");

echo '<script type="text/javascript">window.location.href="mps-empresas.php?error=ER_DT_3";</script>';
exit();