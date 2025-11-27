<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

header('Content-Type: application/json');

try {
    if (!isset($_POST['usuario'])) {
        echo json_encode(['existe' => false, 'error' => 'Usuario no proporcionado']);
        exit();
    }

    $usuario = trim($_POST['usuario']);

    // Validar que el usuario no esté vacío
    if (empty($usuario)) {
        echo json_encode(['existe' => false, 'error' => 'Usuario vacío']);
        exit();
    }
    
    // Consultar si el usuario ya existe
    $sql = "SELECT uss_id FROM ".BD_GENERAL.".usuarios 
            WHERE uss_usuario = ? AND institucion = ? AND year = ?";
    $parametros = [$usuario, $config['conf_id_institucion'], $_SESSION["bd"]];
    
    $consulta = BindSQL::prepararSQL($sql, $parametros);
    
    if (!$consulta) {
        echo json_encode(['existe' => false, 'error' => 'Error en la consulta', 'debug' => mysqli_error($conexion)]);
        exit();
    }
    
    $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    
    if ($resultado) {
        echo json_encode(['existe' => true, 'mensaje' => 'Este usuario ya existe']);
    } else {
        echo json_encode(['existe' => false, 'mensaje' => 'Usuario disponible']);
    }
} catch (Exception $e) {
    echo json_encode(['existe' => false, 'error' => 'Error en la validación: ' . $e->getMessage()]);
}

