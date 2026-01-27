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

// Validaciones: el campo "valor" ya no es requerido, se usa 0 por defecto
if (empty($_POST["fecha"]) or empty($_POST["detalle"]) or empty($_POST["tipo"])) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-agregar.php?error=ER_DT_4";</script>';
    exit();
}

// Validar fecha_documento: no futura, no mayor a 1 año en el pasado
$fechaDocumento = $_POST["fecha"];
$fechaActual = new DateTime();
$fechaDoc = DateTime::createFromFormat('Y-m-d', $fechaDocumento);
if ($fechaDoc === false) {
    // Intentar otro formato
    $fechaDoc = DateTime::createFromFormat('Y-m-d H:i:s', $fechaDocumento);
}

if ($fechaDoc === false) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-agregar.php?error=ER_DT_FECHA_INVALIDA";</script>';
    exit();
}

$fechaLimite = (clone $fechaActual)->modify('-1 year');

if ($fechaDoc > $fechaActual) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-agregar.php?error=ER_DT_FECHA_FUTURA";</script>';
    exit();
}

if ($fechaDoc < $fechaLimite) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-agregar.php?error=ER_DT_FECHA_ANTIGUA";</script>';
    exit();
}
// Migrado a PDO - Consultas preparadas
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');

// También necesitamos la conexión mysqli para métodos que aún la usan
if (!isset($conexion)) {
    $conexion = Conexion::newConnection('mysqli');
}

$consecutivo = '';

if ($_POST["tipo"] == 1) {
    try{
        $sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas 
                WHERE fcu_tipo=1 AND institucion=? AND year=? 
                ORDER BY fcu_consecutivo DESC LIMIT 1";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $consecutivoActual = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$consecutivoActual || $consecutivoActual['fcu_consecutivo'] === '' || $consecutivoActual['fcu_consecutivo'] === null) {
            $consecutivo = (int)($config['conf_inicio_recibos_ingreso'] ?? 1);
        } else {
            $consecutivo = (int)$consecutivoActual['fcu_consecutivo'] + 1;
        }
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}
if ($_POST["tipo"] == 2) {
    try{
        $sql = "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas 
                WHERE fcu_tipo=2 AND institucion=? AND year=? 
                ORDER BY fcu_consecutivo DESC LIMIT 1";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $consecutivoActual = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$consecutivoActual || $consecutivoActual['fcu_consecutivo'] === '' || $consecutivoActual['fcu_consecutivo'] === null) {
            $consecutivo = (int)($config['conf_inicio_recibos_egreso'] ?? 1);
        } else {
            $consecutivo = (int)$consecutivoActual['fcu_consecutivo'] + 1;
        }
    } catch (Exception $e) {
        include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    }
}

if ($consecutivo === '' || $consecutivo === null) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-agregar.php?error=ER_DT_4";</script>';
    exit();
}

// fcu_id es AUTO_INCREMENT, no necesitamos generarlo manualmente

try{
    // fecha_registro se genera automáticamente (CURRENT_TIMESTAMP)
    // fcu_fecha es la fecha del documento (validada arriba)
    // fcu_id es AUTO_INCREMENT, NO se incluye en el INSERT
    
    $sql = "INSERT INTO ".BD_FINANCIERA.".finanzas_cuentas(
        fcu_fecha, fcu_detalle, fcu_valor, fcu_tipo, fcu_observaciones, 
        fcu_usuario, fcu_anulado, fcu_cerrado, fcu_consecutivo, fcu_status, 
        fcu_created_by, fcu_origen, institucion, year
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexionPDO->prepare($sql);
    $fcuAnulado = 0;
    $fcuCerrado = 0;
    // Estado inicial: EN_PROCESO
    $fcuStatus = EN_PROCESO;
    $fcuCreatedBy = $_SESSION["id"];
    $fcuOrigen = 'NORMAL';
    // El campo "valor" ya no es editable, se usa 0 por defecto
    $valorAdicional = 0;
    
    $stmt->bindParam(1, $_POST["fecha"], PDO::PARAM_STR); // fcu_fecha
    $stmt->bindParam(2, $_POST["detalle"], PDO::PARAM_STR);
    $stmt->bindParam(3, $valorAdicional, PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(5, $_POST["obs"], PDO::PARAM_STR);
    $stmt->bindParam(6, $_POST["usuario"], PDO::PARAM_STR);
    $stmt->bindParam(7, $fcuAnulado, PDO::PARAM_INT);
    $stmt->bindParam(8, $fcuCerrado, PDO::PARAM_INT);
    $stmt->bindParam(9, $consecutivo, PDO::PARAM_STR);
    $stmt->bindParam(10, $fcuStatus, PDO::PARAM_STR);
    $stmt->bindParam(11, $fcuCreatedBy, PDO::PARAM_STR);
    $stmt->bindParam(12, $fcuOrigen, PDO::PARAM_STR);
    $stmt->bindParam(13, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(14, $_SESSION["bd"], PDO::PARAM_INT);
    
    // Ejecutar el INSERT y capturar errores específicos
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        // Capturar el error específico de PDO
        $errorInfo = $stmt->errorInfo();
        $errorMsg = "Error al ejecutar INSERT en finanzas_cuentas: " . ($errorInfo[2] ?? $e->getMessage());
        $errorMsg .= " | SQL State: " . ($errorInfo[0] ?? 'N/A');
        $errorMsg .= " | Código: " . ($errorInfo[1] ?? 'N/A');
        throw new Exception($errorMsg);
    }
    
    // Obtener el ID generado (fcu_id es AUTO_INCREMENT y PRIMARY KEY)
    $fcuId = (int)$conexionPDO->lastInsertId();
    
    // Validar que se obtuvo un ID válido
    if ($fcuId <= 0) {
        // Intentar obtener el último ID de otra manera
        try {
            $sqlCheck = "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas 
                       WHERE institucion=? AND year=? 
                       ORDER BY fcu_id DESC LIMIT 1";
            $stmtCheck = $conexionPDO->prepare($sqlCheck);
            $stmtCheck->bindParam(1, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmtCheck->bindParam(2, $_SESSION["bd"], PDO::PARAM_INT);
            $stmtCheck->execute();
            $resultadoCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if ($resultadoCheck && !empty($resultadoCheck['fcu_id'])) {
                $fcuId = (int)$resultadoCheck['fcu_id'];
            } else {
                throw new Exception("No se pudo obtener el ID de la transacción. lastInsertId() devolvió: " . $conexionPDO->lastInsertId() . " y la consulta de verificación no encontró registros.");
            }
        } catch (Exception $e2) {
            throw new Exception("No se pudo obtener el ID de la transacción creada. Error original: " . $e2->getMessage());
        }
    }
} catch (PDOException $e) {
    // Capturar errores específicos de PDO
    $errorMsg = "Error PDO: " . $e->getMessage() . " | Código: " . $e->getCode();
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    ob_clean();
    header("Location: movimientos.php?error=ER_DT_CREATE");
    exit();
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
    ob_clean();
    header("Location: movimientos.php?error=ER_DT_CREATE");
    exit();
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

// Verificar que el registro se insertó correctamente
try {
    $sqlVerificar = "SELECT fcu_id FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_id=? AND institucion=? AND year=?";
    $stmtVerificar = $conexionPDO->prepare($sqlVerificar);
    $stmtVerificar->bindParam(1, $fcuId, PDO::PARAM_INT);
    $stmtVerificar->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmtVerificar->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmtVerificar->execute();
    $verificacion = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
    
    if ($verificacion && !empty($verificacion['fcu_id'])) {
        // Limpiar cualquier output buffer antes de redirigir
        ob_clean();
        // Redirigir directamente a la página de edición (usando fcu_id como identificador)
        header("Location: movimientos-editar.php?success=SC_DT_1&id=".urlencode(base64_encode((string)$fcuId)));
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