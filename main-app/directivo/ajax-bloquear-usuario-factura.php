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

require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

$idUsuario = $_POST['usuario'] ?? '';
$idFactura = $_POST['factura'] ?? '';
$saldoInput = $_POST['saldo'] ?? '0';
$saldoPendiente = floatval(str_replace(['.', ','], ['', '.'], $saldoInput));
$motivo = $_POST['motivo'] ?? '';

if (empty($idUsuario) || empty($idFactura)) {
    echo json_encode([
        'success' => false,
        'message' => 'Información incompleta para realizar el bloqueo.'
    ]);
    exit();
}

// Validar que la factura no esté en proceso ni anulada
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
$detallesFactura = Movimientos::obtenerDetallesFactura($conexion, $config, $idFactura);

if (empty($detallesFactura['factura'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Factura no encontrada.'
    ]);
    exit();
}

$factura = $detallesFactura['factura'];

// Validar que no esté anulada
if ((int)$factura['fcu_anulado'] === 1) {
    echo json_encode([
        'success' => false,
        'message' => 'No se puede bloquear por una factura anulada.'
    ]);
    exit();
}

// Validar que no esté en proceso
if ($factura['fcu_status'] === EN_PROCESO) {
    echo json_encode([
        'success' => false,
        'message' => 'No se puede bloquear por una factura en proceso.'
    ]);
    exit();
}

// Validar que no esté anulada por status
if ($factura['fcu_status'] === ANULADA) {
    echo json_encode([
        'success' => false,
        'message' => 'No se puede bloquear por una factura anulada.'
    ]);
    exit();
}

try {
    if (empty($motivo)) {
        $consecutivo = $factura['fcu_consecutivo'] ?? $idFactura;
        $motivo = "Saldo pendiente en factura {$consecutivo}";
    }

    UsuariosPadre::bloquearUsuario(
        $config,
        $idUsuario,
        1,
        $motivo
    );

    echo json_encode([
        'success' => true,
        'message' => 'Usuario bloqueado correctamente.'
    ]);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo json_encode([
        'success' => false,
        'message' => 'No fue posible bloquear al usuario.'
    ]);
}

