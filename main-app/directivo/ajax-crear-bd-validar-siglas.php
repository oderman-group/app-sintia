<?php
include("session.php");

// Asegurar que las constantes de BD estén disponibles
if (!defined('BD_GENERAL')) {
    require_once(ROOT_PATH."/conexion.php");
}

header('Content-Type: application/json');

Modulos::verificarPermisoDev();

$response = [
    'success' => false,
    'message' => ''
];

try {
    $siglasBD = trim($_POST['siglasBD'] ?? '');
    $tipoInsti = $_POST['tipoInsti'] ?? '';
    
    if (empty($siglasBD)) {
        $response['message'] = 'Siglas no especificadas';
        echo json_encode($response);
        exit;
    }
    
    // Validar formato
    if (!preg_match('/^[a-z0-9_]+$/', $siglasBD)) {
        $response['message'] = 'Solo letras minúsculas, números y guión bajo';
        echo json_encode($response);
        exit;
    }
    
    // Construir el nombre de BD según el entorno
    $bdNamePattern = BD_PREFIX . $siglasBD . '%';
    
    // Verificar si existe alguna BD con esas siglas
    $consulta = mysqli_query($conexion, "SELECT ins_id, ins_nombre, ins_bd 
        FROM ".BD_ADMIN.".instituciones 
        WHERE ins_bd = '".BD_PREFIX.$siglasBD."' 
        AND ins_enviroment='".ENVIROMENT."'
        LIMIT 1");
    
    $existe = mysqli_num_rows($consulta);
    
    if ($existe > 0) {
        $datos = mysqli_fetch_assoc($consulta);
        
        // Si es renovación y es la misma institución, está OK
        if ($tipoInsti === '0') {
            $response['success'] = true;
            $response['message'] = 'Institución válida para renovación';
        } else {
            $response['success'] = false;
            $response['message'] = 'Ya existe una institución con estas siglas: ' . $datos['ins_nombre'];
        }
    } else {
        $response['success'] = true;
        $response['message'] = 'Siglas disponibles ✓';
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error al validar: ' . $e->getMessage();
}

echo json_encode($response);

