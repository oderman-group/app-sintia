<?php
/**
 * VALIDACIÓN DE CÓDIGO DE VERIFICACIÓN
 * Plataforma Educativa SINTIA
 * Versión 2.0
 */

header('Content-Type: application/json');
require_once("../conexion.php");

// Obtener datos del GET o POST
$codigo = isset($_GET['code']) ? trim($_GET['code']) : (isset($_POST['code']) ? trim($_POST['code']) : '');
$idRegistro = isset($_GET['idRegistro']) ? (int)$_GET['idRegistro'] : (isset($_POST['idRegistro']) ? (int)$_POST['idRegistro'] : 0);

// Validaciones básicas
if (empty($codigo) || $idRegistro == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Código o ID de registro inválido'
    ]);
    exit();
}

// Validar que el código tenga 6 dígitos
if (!preg_match('/^[0-9]{6}$/', $codigo)) {
    echo json_encode([
        'success' => false,
        'message' => 'El código debe tener 6 dígitos'
    ]);
    exit();
}

try {
    $codigoEscaped = mysqli_real_escape_string($conexion, $codigo);
    
    // Buscar el código en la base de datos (tabla está en BD_ADMIN)
    $consulta = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".codigos_verificacion 
        WHERE codv_id='{$idRegistro}' AND codv_usuario_asociado IS NOT NULL AND codv_activo=1 AND codv_codigo_verificacion='{$codigoEscaped}' 
        ORDER BY codv_fecha_registro DESC LIMIT 1");
    
    if (mysqli_num_rows($consulta) == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Código no encontrado o ya fue utilizado'
        ]);
        exit();
    }
    
    $registro = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    
    // Verificar si el código ha expirado (10 minutos desde la creación)
    $fechaRegistro = strtotime($registro['codv_fecha_registro']);
    $fechaActual = time();
    $tiempoTranscurrido = $fechaActual - $fechaRegistro;
    $tiempoMaximo = 10 * 60; // 10 minutos en segundos
    
    if ($tiempoTranscurrido > $tiempoMaximo) {
        $minutosExpirados = round($tiempoTranscurrido / 60);
        echo json_encode([
            'success' => false,
            'message' => "El código ha expirado (pasaron {$minutosExpirados} minutos). Por favor solicita uno nuevo.",
            'expired' => true
        ]);
        exit();
    }
    
    // Verificar el código
    if ($registro['codv_codigo_verificacion'] !== $codigo) {
        // Incrementar intentos fallidos
        mysqli_query($conexion, "UPDATE " . BD_ADMIN . ".codigos_verificacion SET 
            codv_intentos_fallidos=codv_intentos_fallidos+1 
            WHERE codv_id='{$idRegistro}'");
        
        $intentosFallidos = (int)$registro['codv_intentos_fallidos'] + 1;
        
        // Si hay más de 5 intentos fallidos, invalidar el código
        if ($intentosFallidos >= 5) {
            mysqli_query($conexion, "UPDATE " . BD_ADMIN . ".codigos_verificacion SET 
                codv_activo=0 
                WHERE codv_id='{$idRegistro}'");
            
            echo json_encode([
                'success' => false,
                'message' => 'Has excedido el número máximo de intentos. Por favor solicita un nuevo código.',
                'max_attempts' => true
            ]);
            exit();
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Código incorrecto. Intentos restantes: ' . (5 - $intentosFallidos),
            'attempts_left' => 5 - $intentosFallidos
        ]);
        exit();
    }
    
    // Código correcto - marcar como verificado
    mysqli_query($conexion, "UPDATE " . BD_ADMIN . ".codigos_verificacion SET 
        codv_activo=0,
        codv_fecha_uso=NOW()
        WHERE codv_id='{$idRegistro}'");
    
    echo json_encode([
        'success' => true,
        'message' => '¡Código verificado exitosamente!',
        'verified' => true
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al validar el código',
        'error' => $e->getMessage()
    ]);
}
exit();
