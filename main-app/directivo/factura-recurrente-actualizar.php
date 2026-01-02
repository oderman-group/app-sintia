<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0279';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Validar que el total neto sea mayor a 0 antes de actualizar
if (!empty($_POST['id'])) {
    $vlrAdicional = !empty($_POST["additional_value"]) ? floatval($_POST["additional_value"]) : 0;
    $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $_POST['id'], $vlrAdicional, TIPO_RECURRING);
    
    if ($totalNeto <= 0) {
        include("../compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">alert("No se puede guardar la factura recurrente. El total neto debe ser mayor a cero. Verifique que los items débito sumen más que los items crédito."); window.location.href="factura-recurrente-editar.php?error=ER_DT_TOTAL_INVALIDO&id='.base64_encode($_POST['id']).'";</script>';
        exit();
    }
}

try {
    Movimientos::actualizarRecurrente($conexion, $config, $_POST);
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="factura-recurrente-editar.php?success=SC_DT_2&id='.base64_encode($_POST['id']).'";</script>';
} catch (Exception $e) {
    echo '<script type="text/javascript">alert("Error: '.htmlspecialchars($e->getMessage()).'"); window.location.href="factura-recurrente-editar.php?error=ER_DT_CREATE&id='.base64_encode($_POST['id']).'";</script>';
}
exit();