<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0104';

header('Content-Type: application/json; charset=utf-8');

if (!Modulos::validarSubRol([$idPaginaInterna])) {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso no autorizado.'
    ]);
    exit();
}

require_once(ROOT_PATH."/main-app/class/Movimientos.php");

$idFactura = $_POST['idFactura'] ?? '';

if (empty($idFactura)) {
    echo json_encode([
        'success' => false,
        'message' => 'Factura no especificada.'
    ]);
    exit();
}

$detallesFactura = Movimientos::obtenerDetallesFactura($conexion, $config, $idFactura);

if (empty($detallesFactura['factura'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Factura no encontrada.'
    ]);
    exit();
}

$factura = $detallesFactura['factura'];
$totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $idFactura, floatval($factura['fcu_valor'] ?? 0));
$totalAbonos = Movimientos::calcularTotalAbonado($conexion, $config, $idFactura);
$saldoPendiente = $totalNeto - $totalAbonos;

$estadoNuevo = ($saldoPendiente <= 0.5) ? COBRADA : POR_COBRAR;

try {
    $sqlUpdate = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas
        SET fcu_status = ?
        WHERE fcu_id = ?
          AND institucion = ?
          AND year = ?";
    $stmt = mysqli_prepare($conexion, $sqlUpdate);
    mysqli_stmt_bind_param($stmt, "ssii", $estadoNuevo, $idFactura, $config['conf_id_institucion'], $_SESSION['bd']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'success' => true,
        'estado' => $estadoNuevo,
        'saldoPendiente' => $saldoPendiente
    ]);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo json_encode([
        'success' => false,
        'message' => 'Error al sincronizar la factura.'
    ]);
}

