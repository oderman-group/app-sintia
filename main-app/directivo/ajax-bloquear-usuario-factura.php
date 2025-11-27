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

try {
    if (empty($motivo)) {
        $motivo = "Saldo pendiente en factura {$idFactura}";
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

