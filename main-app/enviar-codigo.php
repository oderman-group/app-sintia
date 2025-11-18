<?php
/**
 * ENVÍO DE CÓDIGO DE VERIFICACIÓN
 * Plataforma Educativa SINTIA
 * Versión 2.0 con reCAPTCHA v3
 */

header('Content-Type: application/json');
require_once("../conexion.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");

// Obtener datos del POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$apellidos = isset($data['apellidos']) ? trim($data['apellidos']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$celular = isset($data['celular']) ? trim($data['celular']) : '';
$recaptchaToken = isset($data['recaptchaToken']) ? $data['recaptchaToken'] : '';
$attempt = isset($data['attempt']) ? (int)$data['attempt'] : 1;

// Validar reCAPTCHA v3 (opcional - no bloquea el proceso)
$recaptchaValid = false;
if (!empty($recaptchaToken) && $recaptchaToken !== 'RECAPTCHA_NOT_AVAILABLE' && $recaptchaToken !== 'RECAPTCHA_ERROR') {
    $secretKey = '6LfH9KkqAAAAAI3vc_wWTW0EfV0qGVs2cVXe8gGc'; // Tu clave secreta de reCAPTCHA
    
    try {
        $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaData = [
            'secret' => $secretKey,
            'response' => $recaptchaToken,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($recaptchaData),
                'timeout' => 5 // Timeout de 5 segundos
            ]
        ];
        
        $context = stream_context_create($options);
        $verify = @file_get_contents($recaptchaUrl, false, $context);
        
        if ($verify !== false) {
            $captchaSuccess = json_decode($verify);
            
            // Verificar score de reCAPTCHA (debe ser > 0.3 para ser flexible)
            if ($captchaSuccess->success && $captchaSuccess->score >= 0.3) {
                $recaptchaValid = true;
            } else {
                error_log("reCAPTCHA score bajo: " . ($captchaSuccess->score ?? 0));
            }
        }
    } catch (Exception $e) {
        error_log("Error validando reCAPTCHA: " . $e->getMessage());
    }
}

// Si reCAPTCHA no está disponible o falla, registrar advertencia pero continuar
if (!$recaptchaValid) {
    error_log("Advertencia: reCAPTCHA no validado para envío de código - Email: {$email}, IP: " . $_SERVER['REMOTE_ADDR']);
}

// Validaciones básicas
if (empty($nombre) || empty($apellidos) || empty($email) || empty($celular)) {
    echo json_encode([
        'success' => false,
        'message' => 'Todos los campos son requeridos'
    ]);
    exit();
}

// Validar formato de email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Formato de email inválido'
    ]);
    exit();
}

// Validar formato de celular (10 dígitos)
if (!preg_match('/^[0-9]{10}$/', $celular)) {
    echo json_encode([
        'success' => false,
        'message' => 'Formato de celular inválido'
    ]);
    exit();
}

try {
    // Generar código de 6 dígitos
    $codigo = sprintf("%06d", mt_rand(0, 999999));
    
    // Calcular tiempo de expiración (10 minutos)
    $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    $emailEscaped = mysqli_real_escape_string($conexion, $email);
    
    // Verificar si ya existe un código para este email
    $consultaExistente = mysqli_query($conexion, "SELECT codv_id FROM " . BD_GENERAL . ".usuarios_codigo_verificacion 
        WHERE codv_email='{$emailEscaped}' AND codv_usuario_asociado IS NULL ORDER BY codv_id DESC LIMIT 1");
    
    if (mysqli_num_rows($consultaExistente) > 0) {
        // Actualizar código existente
        $registro = mysqli_fetch_array($consultaExistente, MYSQLI_BOTH);
        $idRegistro = $registro['codv_id'];
        
        mysqli_query($conexion, "UPDATE " . BD_GENERAL . ".usuarios_codigo_verificacion SET 
            codv_codigo='{$codigo}',
            codv_fecha_expiracion='{$fechaExpiracion}',
            codv_intentos=codv_intentos+1,
            codv_fecha_ultimo_envio=NOW()
            WHERE codv_id='{$idRegistro}'");
    } else {
        // Crear nuevo registro
        mysqli_query($conexion, "INSERT INTO " . BD_GENERAL . ".usuarios_codigo_verificacion 
            (codv_email, codv_codigo, codv_fecha_creacion, codv_fecha_expiracion, codv_intentos, codv_ip, codv_fecha_ultimo_envio) 
            VALUES ('{$emailEscaped}', '{$codigo}', NOW(), '{$fechaExpiracion}', 1, '{$_SERVER['REMOTE_ADDR']}', NOW())");
        
        $idRegistro = mysqli_insert_id($conexion);
    }
    
    // Preparar datos para el email
    $dataEmail = [
        'usuario_nombre' => $nombre . ' ' . $apellidos,
        'usuario_email' => $email,
        'codigo' => $codigo,
        'tiempo_expiracion' => '10 minutos'
    ];
    
    $asunto = 'Código de verificación - Plataforma SINTIA';
    $bodyTemplateRoute = ROOT_PATH.'/config-general/template-email-codigo-verificacion.php';
    
    // Enviar email inmediatamente (código expira en 10 minutos)
    EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, null, true);
    
    // Si llegamos aquí, el email se envió exitosamente
    echo json_encode([
        'success' => true,
        'message' => 'Código enviado exitosamente',
        'idRegistro' => $idRegistro,
        'attempt' => $attempt
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud',
        'error' => $e->getMessage()
    ]);
}
exit();
