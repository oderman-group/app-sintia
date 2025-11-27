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

require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

try {
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT 
                fc.fcu_id,
                fc.id_nuevo,
                fc.fcu_usuario,
                fc.fcu_valor,
                fc.fcu_tipo,
                fc.fcu_status
            FROM ".BD_FINANCIERA.".finanzas_cuentas fc
            WHERE fc.fcu_tipo = 1
              AND fc.fcu_anulado = 0
              AND fc.institucion = :institucion
              AND fc.year = :year";

    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindValue(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindValue(':year', $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();

    $bloqueados = [];
    $totalesEvaluados = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $totalesEvaluados++;
        $totalNeto = Movimientos::calcularTotalNeto($conexion, $config, $row['fcu_id'], $row['fcu_valor']);
        $totalAbonos = Movimientos::calcularTotalAbonado($conexion, $config, $row['fcu_id']);
        $saldoPendiente = $totalNeto - $totalAbonos;

        if ($saldoPendiente > 0 && !empty($row['fcu_usuario'])) {
            UsuariosPadre::bloquearUsuario(
                $config,
                $row['fcu_usuario'],
                1,
                "Bloqueo automático por saldo pendiente en factura {$row['id_nuevo']}"
            );
            $bloqueados[] = $row['fcu_usuario'];
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Proceso completado.',
        'totalEvaluados' => $totalesEvaluados,
        'usuariosBloqueados' => array_unique($bloqueados)
    ]);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo json_encode([
        'success' => false,
        'message' => 'Error al bloquear usuarios con saldos pendientes.'
    ]);
}

