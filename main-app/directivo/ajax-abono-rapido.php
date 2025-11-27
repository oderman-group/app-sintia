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
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");

$idFactura     = $_POST['idFactura'] ?? '';
$valorAbono    = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
$metodoPago    = $_POST['metodo'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$voucher = '';

if (empty($idFactura) || $valorAbono <= 0 || empty($metodoPago)) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos para registrar el abono.'
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

if ($valorAbono > $saldoPendiente + 0.5) { // margen mínimo por si hay redondeos
    echo json_encode([
        'success' => false,
        'message' => 'El valor del abono supera el saldo pendiente.'
    ]);
    exit();
}

if (!empty($_FILES['comprobante']['name'])) {
    $destino = ROOT_PATH.'/main-app/files/comprobantes';
    if (!file_exists($destino)) {
        @mkdir($destino, 0777, true);
    }
    $explode = explode(".", $_FILES['comprobante']['name']);
    $extension = strtolower(end($explode));
    $voucher = uniqid('abono_'.$factura['fcu_usuario'].'_') . "." . $extension;
    move_uploaded_file($_FILES['comprobante']['tmp_name'], $destino . "/" . $voucher);
}

try {
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexionPDO->beginTransaction();

    $codigoAbono = Utilidades::generateCode('ABO');

    $sqlPayment = "INSERT INTO ".BD_FINANCIERA.".payments
        (registration_date, responsible_user, invoiced, cod_payment, type_payments, payment_method, observation, voucher, note, institucion, year)
        VALUES (NOW(), :responsable, :invoiced, :codigo, 'INVOICE', :metodo, :observaciones, :voucher, :nota, :institucion, :year)";
    $stmtPayment = $conexionPDO->prepare($sqlPayment);
    $stmtPayment->bindValue(':responsable', $_SESSION['id'], PDO::PARAM_INT);
    $stmtPayment->bindValue(':invoiced', $factura['fcu_usuario'], PDO::PARAM_STR);
    $stmtPayment->bindValue(':codigo', $codigoAbono, PDO::PARAM_STR);
    $stmtPayment->bindValue(':metodo', $metodoPago, PDO::PARAM_STR);
    $stmtPayment->bindValue(':observaciones', $observaciones, PDO::PARAM_STR);
    $stmtPayment->bindValue(':voucher', $voucher, PDO::PARAM_STR);
    $stmtPayment->bindValue(':nota', '', PDO::PARAM_STR);
    $stmtPayment->bindValue(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtPayment->bindValue(':year', $_SESSION['bd'], PDO::PARAM_INT);
    $stmtPayment->execute();

    $sqlPaymentInvoiced = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced
        (payment, invoiced, payments, institucion, year)
        VALUES (:valor, :factura, :codigo, :institucion, :year)";
    $stmtPaymentInv = $conexionPDO->prepare($sqlPaymentInvoiced);
    $stmtPaymentInv->bindValue(':valor', $valorAbono, PDO::PARAM_STR);
    $stmtPaymentInv->bindValue(':factura', $idFactura, PDO::PARAM_STR);
    $stmtPaymentInv->bindValue(':codigo', $codigoAbono, PDO::PARAM_STR);
    $stmtPaymentInv->bindValue(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtPaymentInv->bindValue(':year', $_SESSION['bd'], PDO::PARAM_INT);
    $stmtPaymentInv->execute();

    $totalAbonadoNuevo = $totalAbonos + $valorAbono;
    $estadoNuevo = ($totalAbonadoNuevo >= $totalNeto - 0.5) ? COBRADA : POR_COBRAR;

    $sqlUpdateFactura = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas
        SET fcu_status = :estado
        WHERE fcu_id = :idFactura
          AND institucion = :institucion
          AND year = :year";
    $stmtUpdate = $conexionPDO->prepare($sqlUpdateFactura);
    $stmtUpdate->bindValue(':estado', $estadoNuevo, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':idFactura', $idFactura, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtUpdate->bindValue(':year', $_SESSION['bd'], PDO::PARAM_INT);
    $stmtUpdate->execute();

    $conexionPDO->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Abono registrado correctamente.'
    ]);
} catch (Exception $e) {
    if (isset($conexionPDO)) {
        $conexionPDO->rollBack();
    }
    include("../compartido/error-catch-to-report.php");
    if (!empty($voucher)) {
        @unlink(ROOT_PATH.'/main-app/files/comprobantes/' . $voucher);
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar el abono.'
    ]);
}

