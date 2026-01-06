<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");

header('Content-Type: application/json');

$response = ['success' => false, 'cuentas' => []];

try {
    // Cargar todas las cuentas bancarias activas, sin filtrar por método de pago
    // Una misma cuenta bancaria puede registrar ingresos o egresos de diferentes tipos de pago
    $consulta = Movimientos::listarCuentasBancarias($conexion, $config, null, true);
    
    $cuentas = [];
    if ($consulta) {
        while ($cuenta = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $cuentas[] = [
                'id' => $cuenta['cba_id'],
                'nombre' => $cuenta['cba_nombre'] . (!empty($cuenta['cba_banco']) ? ' - ' . $cuenta['cba_banco'] : '')
            ];
        }
    }
    
    $response['success'] = true;
    $response['cuentas'] = $cuentas;
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>


