<?php
include("session.php");

// Asegurar que las constantes de BD estén disponibles
if (!defined('BD_GENERAL')) {
    require_once(ROOT_PATH."/conexion.php");
}

header('Content-Type: application/json');

Modulos::verificarPermisoDev();

$response = [
    'exists' => false,
    'message' => ''
];

try {
    $documento = trim($_POST['documento'] ?? '');
    
    if (empty($documento)) {
        $response['message'] = 'Documento no especificado';
        echo json_encode($response);
        exit;
    }
    
    // Buscar en la tabla de usuarios de la BD general
    $consulta = mysqli_query($conexion, "SELECT uss_id, uss_nombre, uss_documento, institucion 
        FROM ".BD_GENERAL.".usuarios 
        WHERE uss_documento = '".$documento."' 
        AND uss_tipo = ".TIPO_DIRECTIVO."
        LIMIT 1");
    
    $existe = mysqli_num_rows($consulta);
    
    if ($existe > 0) {
        $datos = mysqli_fetch_assoc($consulta);
        $response['exists'] = true;
        $response['message'] = 'Este documento ya está registrado para: ' . $datos['uss_nombre'];
        $response['institucion'] = $datos['institucion'];
    } else {
        $response['exists'] = false;
        $response['message'] = 'Documento disponible';
    }
    
} catch (Exception $e) {
    $response['exists'] = false;
    $response['message'] = 'Error al validar: ' . $e->getMessage();
}

echo json_encode($response);

