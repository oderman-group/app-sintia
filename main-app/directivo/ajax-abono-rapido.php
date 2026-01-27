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
$cuentaBancariaId = !empty($_POST['cuenta_bancaria_id']) ? intval($_POST['cuenta_bancaria_id']) : null;
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
    

    // Insertar con todos los campos necesarios siguiendo el patrón de Movimientos::guardarAbonos()
    $consecutivo = Movimientos::siguienteConsecutivoAbono($conexionPDO, $config);
    $sqlPaymentInvoiced = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced (
        responsible_user, payment_user, type_payments, payment_tipo, payment_method, 
        payment_cuenta_bancaria_id, invoiced, payment, observation, attachment, 
        fecha_registro, institucion, year, pi_consecutivo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtPaymentInv = $conexionPDO->prepare($sqlPaymentInvoiced);
    
    $fechaRegistro = date('Y-m-d H:i:s');
    $paymentTipo = 'INGRESO'; // Los abonos son siempre ingresos
    $paymentUser = $factura['fcu_usuario'] ?? null; // Usuario de la factura
    
    $stmtPaymentInv->bindValue(1, $_SESSION['id'], PDO::PARAM_STR); // responsible_user
    $stmtPaymentInv->bindValue(2, $paymentUser, $paymentUser ? PDO::PARAM_STR : PDO::PARAM_NULL); // payment_user
    $stmtPaymentInv->bindValue(3, INVOICE, PDO::PARAM_STR); // type_payments
    $stmtPaymentInv->bindValue(4, $paymentTipo, PDO::PARAM_STR); // payment_tipo
    $stmtPaymentInv->bindValue(5, $metodoPago, $metodoPago ? PDO::PARAM_STR : PDO::PARAM_NULL); // payment_method
    $stmtPaymentInv->bindValue(6, $cuentaBancariaId, $cuentaBancariaId ? PDO::PARAM_INT : PDO::PARAM_NULL); // payment_cuenta_bancaria_id
    $stmtPaymentInv->bindValue(7, $idFactura, PDO::PARAM_INT); // invoiced (fcu_id)
    $stmtPaymentInv->bindValue(8, $valorAbono, PDO::PARAM_STR); // payment
    $stmtPaymentInv->bindValue(9, $observaciones, $observaciones ? PDO::PARAM_STR : PDO::PARAM_NULL); // observation
    $stmtPaymentInv->bindValue(10, $voucher, $voucher ? PDO::PARAM_STR : PDO::PARAM_NULL); // attachment
    $stmtPaymentInv->bindValue(11, $fechaRegistro, PDO::PARAM_STR); // fecha_registro
    $stmtPaymentInv->bindValue(12, $config['conf_id_institucion'], PDO::PARAM_INT); // institucion
    $stmtPaymentInv->bindValue(13, $_SESSION['bd'], PDO::PARAM_INT); // year
    $stmtPaymentInv->bindValue(14, $consecutivo, PDO::PARAM_INT); // pi_consecutivo
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

