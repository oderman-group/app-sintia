<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0177';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

// Validaciones básicas (ya no se requiere "forma" - fcu_forma_pago, ni "valor")
if (empty($_POST["fecha"]) or empty($_POST["detalle"]) or empty($_POST["tipo"])) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-editar.php?error=ER_DT_4&id='.urlencode(base64_encode($_POST['idU'])).'";</script>';
    exit();
}

// Validar fecha_documento: no futura, no mayor a 1 año en el pasado
$fechaDocumento = $_POST["fecha"];
$fechaActual = new DateTime();
$fechaDoc = DateTime::createFromFormat('Y-m-d', $fechaDocumento);
if ($fechaDoc === false) {
    $fechaDoc = DateTime::createFromFormat('Y-m-d H:i:s', $fechaDocumento);
}

if ($fechaDoc === false) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-editar.php?error=ER_DT_FECHA_INVALIDA&id='.urlencode(base64_encode($_POST['idU'])).'";</script>';
    exit();
}

$fechaLimite = (clone $fechaActual)->modify('-1 year');

if ($fechaDoc > $fechaActual) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-editar.php?error=ER_DT_FECHA_FUTURA&id='.urlencode(base64_encode($_POST['idU'])).'";</script>';
    exit();
}

if ($fechaDoc < $fechaLimite) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="movimientos-editar.php?error=ER_DT_FECHA_ANTIGUA&id='.urlencode(base64_encode($_POST['idU'])).'";</script>';
    exit();
}

// Verificar estado actual de la factura y si tiene abonos
$idFactura = $_POST['idU'];
// Buscar fcu_id y estado de la factura (fcu_id es ahora el identificador principal)
require_once(ROOT_PATH."/main-app/class/Conexion.php");
$conexionPDO = Conexion::newConnection('PDO');
$sql = "SELECT fcu_id, fcu_status FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_id=? AND institucion=? AND year=? LIMIT 1";
$stmt = $conexionPDO->prepare($sql);
$stmt->bindParam(1, $idFactura, PDO::PARAM_INT);
$stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
$stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
$stmt->execute();
$facturaData = $stmt->fetch(PDO::FETCH_ASSOC);

$fcuIdFactura = $facturaData['fcu_id'] ?? $idFactura;
$estadoActual = $facturaData['fcu_status'] ?? '';
$totalAbonado = Movimientos::calcularTotalAbonado($conexion, $config, $fcuIdFactura);
$tieneAbonos = ($totalAbonado > 0);

// Solo se puede editar si está en EN_PROCESO y no tiene abonos
if ($estadoActual != EN_PROCESO) {
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">alert("Esta factura ya está confirmada. No se permite editar ningún dato."); window.location.href="movimientos-editar.php?error=ER_DT_FACTURA_CONFIRMADA&id='.urlencode(base64_encode($idFactura)).'";</script>';
    exit();
}

if ($tieneAbonos) {
    include("../compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">alert("Esta factura tiene abonos asociados. No se permite editar ningún dato."); window.location.href="movimientos-editar.php?error=ER_DT_ABONOS_EXISTENTES&id='.urlencode(base64_encode($idFactura)).'";</script>';
    exit();
}

// Determinar acción: "guardar" mantiene EN_PROCESO, "confirmar" cambia a POR_COBRAR
$accion = $_POST['accion'] ?? 'guardar';
$nuevoEstado = ($accion == 'confirmar') ? POR_COBRAR : EN_PROCESO;

$fecha = $fechaDocumento;

if ($_POST["tipo"] == 1) {
    try{
        $consultaConsecutivoActual=mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_tipo=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}
        ORDER BY fcu_id DESC");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }

    $consecutivoActual = mysqli_fetch_array($consultaConsecutivoActual, MYSQLI_BOTH);
    if ($consecutivoActual['fcu_consecutivo'] == "") {
        $consecutivo = $config['conf_inicio_recibos_ingreso'];
    } else {
        $consecutivo = $consecutivoActual['fcu_consecutivo'] + 1;
    }
}

if ($_POST["tipo"] == 2) {
    try{
        $consultaConsecutivoActual=mysqli_query($conexion, "SELECT * FROM ".BD_FINANCIERA.".finanzas_cuentas WHERE fcu_tipo=2 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}
        ORDER BY fcu_id DESC");
    } catch (Exception $e) {
        include("../compartido/error-catch-to-report.php");
    }
    $consecutivoActual = mysqli_fetch_array($consultaConsecutivoActual, MYSQLI_BOTH);
    if ($consecutivoActual['fcu_consecutivo'] == "") {
        $consecutivo = $config['conf_inicio_recibos_egreso'];
    } else {
        $consecutivo = $consecutivoActual['fcu_consecutivo'] + 1;
    }
}

// Solo se ejecuta si está en EN_PROCESO y NO tiene abonos (ya se manejó arriba)
try{
    // Asegurar que todos los valores sean strings válidos para mysqli_real_escape_string
    $fechaEscapada = mysqli_real_escape_string($conexion, (string)($fecha ?? ''));
    $detalleEscapado = mysqli_real_escape_string($conexion, (string)($_POST["detalle"] ?? ''));
    $tipoEscapado = mysqli_real_escape_string($conexion, (string)($_POST["tipo"] ?? ''));
    $obsEscapada = mysqli_real_escape_string($conexion, (string)($_POST["obs"] ?? ''));
    $usuarioEscapado = mysqli_real_escape_string($conexion, (string)($_POST["usuario"] ?? ''));
    $anuladoEscapado = mysqli_real_escape_string($conexion, (string)($_POST["anulado"] ?? '0'));
    $cerradoEscapado = mysqli_real_escape_string($conexion, (string)($_POST["cerrado"] ?? '0'));
    $consecutivoEscapado = mysqli_real_escape_string($conexion, (string)($consecutivo ?? ''));
    $estadoEscapado = mysqli_real_escape_string($conexion, (string)($nuevoEstado ?? ''));
    $idUEscapado = mysqli_real_escape_string($conexion, (string)($_POST['idU'] ?? ''));
    
    // Actualizar factura incluyendo el nuevo estado
    // El campo fcu_valor (valor) ya no es editable, no se actualiza
    mysqli_query($conexion, "UPDATE ".BD_FINANCIERA.".finanzas_cuentas SET
    fcu_fecha='{$fechaEscapada}',
    fcu_detalle='{$detalleEscapado}',
    fcu_tipo='{$tipoEscapado}',
    fcu_observaciones='{$obsEscapada}',
    fcu_usuario='{$usuarioEscapado}',
    fcu_anulado='{$anuladoEscapado}',
    fcu_cerrado='{$cerradoEscapado}',
    fcu_consecutivo='{$consecutivoEscapado}',
    fcu_status='{$estadoEscapado}'
    WHERE fcu_id='{$idUEscapado}' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
} catch (Exception $e) {
	include("../compartido/error-catch-to-report.php");
}
	include("../compartido/guardar-historial-acciones.php");

// Redirigir según la acción
if ($accion == 'confirmar') {
    echo '<script type="text/javascript">alert("Factura confirmada exitosamente. Ya no se permiten más modificaciones."); window.location.href="movimientos.php?success=SC_DT_CONFIRMADA";</script>';
} else {
    echo '<script type="text/javascript">window.location.href="movimientos-editar.php?success=SC_DT_2&id='.base64_encode($_POST['idU']).'";</script>';
}
exit();