<?php
include("session.php");
require_once("../class/Usuarios.php");

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['id'])) {
    echo json_encode(['esPrimerIngreso' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

try {
    // Obtener datos del usuario actual
    $usuario = Usuarios::obtenerDatosUsuario($_SESSION['id']);
    
    if (!$usuario) {
        echo json_encode(['esPrimerIngreso' => false, 'error' => 'No se pudo obtener datos del usuario']);
        exit();
    }
    
    // Verificar que sea directivo usando los datos de la base de datos
    if ($usuario['uss_tipo'] != TIPO_DIRECTIVO) {
        echo json_encode(['esPrimerIngreso' => false, 'error' => 'Usuario no es directivo', 'tipo_usuario' => $usuario['uss_tipo']]);
        exit();
    }
    
    $esPrimerIngreso = false;
    
    // Verificar si es primer ingreso:
    // 1. uss_ultimo_ingreso debe ser de hoy
    // 2. uss_ultima_salida debe ser NULL o vacío
    if (!empty($usuario['uss_ultimo_ingreso'])) {
        $fechaUltimoIngreso = new DateTime($usuario['uss_ultimo_ingreso']);
        $fechaHoy = new DateTime();
        
        // Verificar si el último ingreso fue hoy
        if ($fechaUltimoIngreso->format('Y-m-d') === $fechaHoy->format('Y-m-d')) {
            // Verificar si no hay fecha de última salida (NULL o vacío)
            if (empty($usuario['uss_ultima_salida']) || $usuario['uss_ultima_salida'] === null) {
                $esPrimerIngreso = true;
            }
        }
    }
    
    // Respuesta JSON
    echo json_encode([
        'esPrimerIngreso' => $esPrimerIngreso,
        'debug' => [
            'ultimo_ingreso' => $usuario['uss_ultimo_ingreso'],
            'ultima_salida' => $usuario['uss_ultima_salida'],
            'fecha_hoy' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    // En caso de error, no mostrar el modal
    echo json_encode(['esPrimerIngreso' => false, 'error' => $e->getMessage()]);
}
?>
