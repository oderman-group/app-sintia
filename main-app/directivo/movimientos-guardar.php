<?php
// Iniciar output buffering para evitar problemas con header()
ob_start();
include("session.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0094';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

if (empty($_POST["fecha"]) or empty($_POST["detalle"]) or (isset($_POST["valor"]) && $_POST["valor"]=="") or empty($_POST["tipo"]) or empty($_POST["forma"])) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-agregar.php?error=ER_DT_4";</script>';
    exit();
}
// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');
$consecutivo = '';

if ($_POST["tipo"] == 1) {
    try{
        $sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas 
                WHERE fcu_tipo=1 AND institucion=? AND year=? 
                ORDER BY fcu_id DESC LIMIT 1";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $consecutivoActual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (empty($consecutivoActual['fcu_consecutivo'])) {
            $consecutivo = $config['conf_inicio_recibos_ingreso'];
        } else {
            $consecutivo = $consecutivoActual['fcu_consecutivo'] + 1;
        }
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}
if ($_POST["tipo"] == 2) {
    try{
        $sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas 
                WHERE fcu_tipo=2 AND institucion=? AND year=? 
                ORDER BY fcu_id DESC LIMIT 1";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $consecutivoActual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (empty($consecutivoActual['fcu_consecutivo'])) {
            $consecutivo = $config['conf_inicio_recibos_egreso'];
        } else {
            $consecutivo = $consecutivoActual['fcu_consecutivo'] + 1;
        }
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

$idInsercion=Utilidades::generateCode("FCU");
try{
    $sql = "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(
        fcu_id, fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, 
        fcu_usuario, fcu_anulado, fcu_forma_pago, fcu_cerrado, fcu_consecutivo, institucion, year
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, 0, ?, ?, ?)";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idInsercion, PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["fecha"], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST["detalle"], PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["valor"], PDO::PARAM_STR);
    $stmt->bindParam(5, $_POST["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(6, $_POST["obs"], PDO::PARAM_STR);
    $stmt->bindParam(7, $_POST["usuario"], PDO::PARAM_STR);
    $stmt->bindParam(8, $_POST["forma"], PDO::PARAM_STR);
    $stmt->bindParam(9, $consecutivo, PDO::PARAM_STR);
    $stmt->bindParam(10, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(11, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

try{
    $sql = "UPDATE ".BD_FINANCIERA.".transaction_items SET id_transaction=? 
            WHERE id_transaction=? AND institucion=? AND year=?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $idInsercion, PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST["idU"], PDO::PARAM_STR);
    $stmt->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

if (!empty($_POST["abonoAutomatico"]) && $_POST["abonoAutomatico"] == 1) {
    $totalNeto    = Movimientos::calcularTotalNeto($conexion, $config, $idInsercion, $_POST["valor"]);

    if ($totalNeto > 0) {
        switch ($_POST["forma"]) {
            case 1:
                $formaPago = 'EFECTIVO';
            break;
            case 2:
                $formaPago = 'CHEQUE';
            break;
            case 3:
                $formaPago = 'T_DEBITO';
            break;
            case 4:
                $formaPago = 'T_CREDITO';
            break;
            case 5:
                $formaPago = 'TRANSFERENCIA';
            break;
            case 6:
                $formaPago = 'OTROS';
            break;
        }

        $codigoUnico=Utilidades::generateCode("ABO");

        // Migrado a PDO - Consultas preparadas para abono automático
        try {
            $sqlPayment = "INSERT INTO ".BD_FINANCIERA.".payments (
                responsible_user, invoiced, cod_payment, type_payments, payment_method, observation, institucion, year
            ) VALUES (?, ?, ?, ?, ?, 'Abono automatico', ?, ?)";
            $stmtPayment = $conexionPDO->prepare($sqlPayment);
            $tipoInvoice = INVOICE;
            $stmtPayment->bindParam(1, $_SESSION["id"], PDO::PARAM_STR);
            $stmtPayment->bindParam(2, $_POST["usuario"], PDO::PARAM_STR);
            $stmtPayment->bindParam(3, $codigoUnico, PDO::PARAM_STR);
            $stmtPayment->bindParam(4, $tipoInvoice, PDO::PARAM_STR);
            $stmtPayment->bindParam(5, $formaPago, PDO::PARAM_STR);
            $stmtPayment->bindParam(6, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtPayment->bindParam(7, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtPayment->execute();
            
            $sqlInvoiced = "INSERT INTO ".BD_FINANCIERA.".payments_invoiced (
                invoiced, payments, payment, institucion, year
            ) VALUES (?, ?, ?, ?, ?)";
            $stmtInvoiced = $conexionPDO->prepare($sqlInvoiced);
            $stmtInvoiced->bindParam(1, $idInsercion, PDO::PARAM_STR);
            $stmtInvoiced->bindParam(2, $codigoUnico, PDO::PARAM_STR);
            $stmtInvoiced->bindParam(3, $totalNeto, PDO::PARAM_STR);
            $stmtInvoiced->bindParam(4, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtInvoiced->bindParam(5, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtInvoiced->execute();
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }

        try{
            $sqlUpdateStatus = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas SET fcu_status=? WHERE fcu_id=? AND institucion=? AND year=?";
            $stmtUpdateStatus = $conexionPDO->prepare($sqlUpdateStatus);
            $estadoCobrada = COBRADA;
            $stmtUpdateStatus->bindParam(1, $estadoCobrada, PDO::PARAM_STR);
            $stmtUpdateStatus->bindParam(2, $idInsercion, PDO::PARAM_STR);
            $stmtUpdateStatus->bindParam(3, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtUpdateStatus->bindParam(4, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtUpdateStatus->execute();
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
    }
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

// Verificar que el registro se insertó correctamente
try {
    $sqlVerificar = "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_id=? AND institucion=? AND year=?";
    $stmtVerificar = $conexionPDO->prepare($sqlVerificar);
    $stmtVerificar->bindParam(1, $idInsercion, PDO::PARAM_STR);
    $stmtVerificar->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtVerificar->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmtVerificar->execute();
    $verificacion = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
    
    if ($verificacion && !empty($verificacion['fcu_id'])) {
        // Limpiar cualquier output buffer antes de redirigir
        ob_clean();
        // Redirigir directamente a la página de edición
        header("Location: movimientos-editar.php?success=SC_DT_1&id=".urlencode(base64_encode($idInsercion)));
        exit();
    } else {
        throw new Exception("No se pudo verificar la creación de la transacción");
    }
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    ob_clean();
    header("Location: movimientos.php?error=ER_DT_CREATE");
    exit();
}