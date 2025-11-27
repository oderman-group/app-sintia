<?php
include("../directivo/session.php");

header('Content-Type: application/json');

if (!isset($_POST['documento'])) {
    echo json_encode(['existe' => false, 'error' => 'Documento no proporcionado']);
    exit();
}

$documento = trim($_POST['documento']);
$idUsuario = isset($_POST['idUsuario']) ? intval($_POST['idUsuario']) : 0;

try {
    // Validar que el documento no esté vacío
    if (empty($documento)) {
        echo json_encode(['existe' => false, 'mensaje' => 'Documento vacío']);
        exit();
    }
    
    // Consultar si el documento ya existe (excluyendo el usuario actual si se está editando)
    if ($idUsuario > 0) {
        $sql = "SELECT uss_id FROM ".BD_GENERAL.".usuarios 
                WHERE uss_documento = ? AND institucion = ? AND year = ? AND uss_id != ?";
        $parametros = [$documento, $config['conf_id_institucion'], $_SESSION["bd"], $idUsuario];
    } else {
        $sql = "SELECT uss_id FROM ".BD_GENERAL.".usuarios 
                WHERE uss_documento = ? AND institucion = ? AND year = ?";
        $parametros = [$documento, $config['conf_id_institucion'], $_SESSION["bd"]];
    }
    
    $consulta = BindSQL::prepararSQL($sql, $parametros);
    
    $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    
    if ($resultado) {
        echo json_encode(['existe' => true, 'mensaje' => 'Este documento ya existe']);
    } else {
        echo json_encode(['existe' => false, 'mensaje' => 'Documento disponible']);
    }
} catch (Exception $e) {
    echo json_encode(['existe' => false, 'error' => 'Error en la validación: ' . $e->getMessage()]);
}

