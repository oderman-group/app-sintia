<?php
header('Content-Type: application/json');
include("session.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "File is accessible";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cargas = $_POST['cargas'];
    $periodo = $_POST['periodo'];

    try {
        foreach ($cargas as $id) {
            CargaAcademica::actualizarCargaPorID($config, $id, ['car_periodo' => $periodo]);
        }
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>