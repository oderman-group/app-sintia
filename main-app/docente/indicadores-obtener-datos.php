<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

header('Content-Type: application/json');

try {
    $idR = "";
    if (!empty($_GET["idR"])) {
        $idR = base64_decode($_GET["idR"]);
    }

    if (empty($idR)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de indicador no válido'
        ]);
        exit();
    }

    $indicador = Indicadores::traerDatosIndicador($conexion, $config, $idR);

    if (empty($indicador)) {
        echo json_encode([
            'success' => false,
            'message' => 'Indicador no encontrado'
        ]);
        exit();
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'ipc_id' => $indicador['ipc_id'],
            'ipc_indicador' => $indicador['ipc_indicador'],
            'ipc_valor' => $indicador['ipc_valor'],
            'ind_nombre' => $indicador['ind_nombre'],
            'ipc_evaluacion' => $indicador['ipc_evaluacion']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los datos: ' . $e->getMessage()
    ]);
}
?>
