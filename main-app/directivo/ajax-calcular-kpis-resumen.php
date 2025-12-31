<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Movimientos.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

Modulos::validarAccesoDirectoPaginas();

header('Content-Type: application/json');

try {
    // Obtener filtros de los parámetros GET
    $filtros = [
        'mostrarAnuladas' => (!empty($_GET['mostrarAnuladas']) && $_GET['mostrarAnuladas'] == '1'),
        'excluirEnProceso' => true,
        'tipo' => !empty($_GET['tipo']) ? intval(base64_decode($_GET['tipo'])) : null,
        'usuario' => !empty($_GET['usuario']) ? base64_decode($_GET['usuario']) : null,
        'desde' => !empty($_GET['desde']) ? $_GET['desde'] : null,
        'hasta' => !empty($_GET['hasta']) ? $_GET['hasta'] : null
    ];

    // Calcular KPIs usando el método centralizado
    $kpis = Movimientos::calcularKPIsResumen($conexion, $config, $filtros);

    echo json_encode([
        'success' => true,
        'kpis' => $kpis
    ]);
} catch (Exception $e) {
    include("../compartido/error-catch-to-report.php");
    echo json_encode([
        'success' => false,
        'message' => 'Error al calcular KPIs: ' . $e->getMessage(),
        'kpis' => [
            'totalVentas' => 0,
            'totalCompras' => 0,
            'totalPorCobrar' => 0,
            'totalCobrado' => 0
        ]
    ]);
}

