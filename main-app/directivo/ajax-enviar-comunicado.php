<?php
header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

Modulos::validarAccesoDirectoPaginas();

// Validar que la institución tenga el módulo de comunicados activo
if (!Modulos::verificarModulosDeInstitucion(Modulos::MODULO_COMUNICADOS)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'El módulo de Comunicados no está activo para esta institución.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
    require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
    require_once(ROOT_PATH . "/main-app/class/Sms.php");
    require_once(ROOT_PATH . "/main-app/class/EnviarEmail.php");
    
    // Validar CSRF
    if (!Csrf::validarToken($_POST['csrf_token'] ?? '')) {
        throw new Exception('Token CSRF inválido');
    }
    
    // Validar datos recibidos
    if (empty($_POST['usuario_id']) || $_POST['usuario_id'] == '0' || $_POST['usuario_id'] == 0) {
        throw new Exception('ID de usuario no proporcionado o inválido');
    }
    
    $usuarioId = trim($_POST['usuario_id']);
    $mensaje = !empty($_POST['mensaje']) ? trim($_POST['mensaje']) : '';
    $canales = !empty($_POST['canales']) ? $_POST['canales'] : [];
    
    if (empty($mensaje)) {
        throw new Exception('El mensaje no puede estar vacío');
    }
    
    if (empty($canales) || !is_array($canales)) {
        throw new Exception('Debes seleccionar al menos un canal de envío');
    }
    
    // Obtener datos del usuario
    $datosUsuario = Usuarios::obtenerDatosUsuario($usuarioId);
    
    if (empty($datosUsuario)) {
        throw new Exception('Usuario no encontrado con ID: ' . $usuarioId);
    }
    
    $nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($datosUsuario);
    $email = !empty($datosUsuario['uss_email']) ? trim($datosUsuario['uss_email']) : '';
    $celularRaw = !empty($datosUsuario['uss_celular']) ? trim($datosUsuario['uss_celular']) : '';
    
    // Procesar número de celular - solo limpiar formato, mantener 10 dígitos
    // El código de país (+57) se agregará automáticamente en Sms.php
    $numeroCelular = '';
    if (!empty($celularRaw)) {
        // Limpiar caracteres especiales (igual que en ajax-enviar-whatsapp-usuario.php)
        $numeroCelular = preg_replace('/[()\s-]/', '', $celularRaw);
        // Si preg_replace falla, $numeroCelular puede ser null, así que validamos
        if ($numeroCelular === null) {
            $numeroCelular = '';
        }
        // Si el número ya tiene el prefijo +57, removerlo para que no se duplique
        // El método enviarWhatsApp ya agrega el prefijo +57 automáticamente
        if (!empty($numeroCelular) && strpos($numeroCelular, '+57') === 0) {
            $numeroCelular = substr($numeroCelular, 3); // Remover +57
        } elseif (!empty($numeroCelular) && strpos($numeroCelular, '57') === 0 && strlen($numeroCelular) > 10) {
            $numeroCelular = substr($numeroCelular, 2); // Remover 57 si está al inicio
        }
        // Asegurar que tenga exactamente 10 dígitos (tomar los últimos 10 si tiene más)
        if (!empty($numeroCelular) && strlen($numeroCelular) > 10) {
            $numeroCelular = substr($numeroCelular, -10);
        }
    }
    
    $resultados = [];
    $errores = [];
    $exitosos = [];
    
    // Obtener datos del usuario que envía
    global $datosUsuarioActual;
    $usuarioEnvio = $datosUsuarioActual['uss_id'] ?? null;
    $institucion = $datosUsuario['institucion'] ?? 0;
    $year = $_SESSION["bd"] ?? date('Y');
    
    // Función auxiliar para guardar registro en BD
    // Nota: $destinatario debe ser el número/email REAL del usuario, no el de prueba
    $guardarRegistro = function($canal, $destinatario, $estado, $error = null, $codigoError = null, $twilioSid = null, $destinatarioReal = null) use ($usuarioId, $nombreUsuario, $mensaje, $institucion, $year, $usuarioEnvio) {
        global $conexion, $baseDatosServicios;
        
        $canalUpper = strtoupper($canal);
        if ($canalUpper === 'EMAIL') {
            $canalBD = 'EMAIL';
        } elseif ($canalUpper === 'SMS') {
            $canalBD = 'SMS';
        } elseif ($canalUpper === 'WHATSAPP') {
            $canalBD = 'WHATSAPP';
        } else {
            return false;
        }
        
        $estadoBD = $estado === 'exito' ? 'ENVIADO' : 'ERROR';
        
        // Usar el destinatario real si está disponible, sino usar el destinatario proporcionado
        $destinatarioFinal = $destinatarioReal !== null ? $destinatarioReal : $destinatario;
        
        $sql = "INSERT INTO " . $baseDatosServicios . ".comunicaciones_enviadas (
            com_usuario_id, com_usuario_nombre, com_canal, com_destinatario, com_mensaje,
            com_estado, com_error, com_codigo_error, com_institucion, com_year, com_usuario_envio, com_twilio_sid
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            require_once(ROOT_PATH . "/main-app/class/BindSQL.php");
            $stmt = BindSQL::prepararSQL($sql, [
                $usuarioId,
                $nombreUsuario,
                $canalBD,
                $destinatarioFinal, // Guardar el número real del usuario
                $mensaje,
                $estadoBD,
                $error,
                $codigoError,
                $institucion,
                $year,
                $usuarioEnvio,
                $twilioSid
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error al guardar registro de comunicación: " . $e->getMessage());
            return false;
        }
    };
    
    // Enviar por cada canal seleccionado
    foreach ($canales as $canal) {
        try {
            switch ($canal) {
                case 'email':
                    if (empty($email) || !EnviarEmail::validarEmail($email)) {
                        $errorMsg = 'No hay email válido registrado';
                        $errores[] = 'Email: ' . $errorMsg;
                        $guardarRegistro('email', $email ?: 'N/A', 'error', $errorMsg);
                        break;
                    }
                    
                    $dataEmail = [
                        'usuario_email' => $email,
                        'usuario_nombre' => $nombreUsuario,
                        'institucion_id' => $institucion,
                        'usuario_id' => $usuarioId,
                        'mensaje' => $mensaje,
                        'asunto' => 'Comunicado de SINTIA'
                    ];
                    
                    EnviarEmail::enviar(
                        $dataEmail,
                        'Comunicado de SINTIA',
                        ROOT_PATH . '/config-general/template-email-comunicado.php',
                        '',
                        []
                    );
                    $exitosos[] = 'Email enviado a ' . $email;
                    $guardarRegistro('email', $email, 'exito');
                    break;
                    
                case 'sms':
                    // Validar que la institución tenga el módulo SMS activo
                    if (!Modulos::verificarModulosDeInstitucion(Modulos::MODULO_SMS)) {
                        $errorMsg = 'El módulo SMS no está activo para esta institución';
                        $errores[] = 'SMS: ' . $errorMsg;
                        $guardarRegistro('sms', $celularRaw ?: 'N/A', 'error', $errorMsg);
                        break;
                    }
                    
                    if (empty($numeroCelular) || strlen($numeroCelular) < 10) {
                        $errorMsg = 'No hay número de teléfono válido registrado';
                        $errores[] = 'SMS: ' . $errorMsg;
                        $guardarRegistro('sms', $celularRaw ?: 'N/A', 'error', $errorMsg);
                        break;
                    }
                    
                    $sms = new Sms();
                    $resultadoSms = $sms->enviarSms([
                        'telefono' => $numeroCelular,
                        'mensaje' => $mensaje
                    ]);
                    
                    $twilioSid = null;
                    if (is_object($resultadoSms) && isset($resultadoSms->sid)) {
                        $twilioSid = $resultadoSms->sid;
                    }
                    
                    // Determinar mensaje de éxito según si se redirigió o no
                    $mensajeExito = 'SMS enviado a ' . $celularRaw;
                    if (defined('ENVIROMENT') && ENVIROMENT !== 'PROD' && defined('TWILIO_SMS_TEST_DESTINATION') && !empty(TWILIO_SMS_TEST_DESTINATION)) {
                        $mensajeExito = 'SMS enviado a ' . $celularRaw . ' (redirigido a número de prueba: ' . TWILIO_SMS_TEST_DESTINATION . ')';
                    }
                    
                    $exitosos[] = $mensajeExito;
                    // Guardar con el número real del usuario, no el de prueba
                    $guardarRegistro('sms', $celularRaw, 'exito', null, null, $twilioSid, $celularRaw);
                    break;
                    
                case 'whatsapp':
                    if (empty($numeroCelular) || strlen($numeroCelular) < 10) {
                        $errorMsg = 'No hay número de teléfono válido registrado. Número procesado: ' . ($numeroCelular ?: 'vacío');
                        $errores[] = 'WhatsApp: ' . $errorMsg;
                        $guardarRegistro('whatsapp', $celularRaw ?: 'N/A', 'error', $errorMsg);
                        break;
                    }
                    
                    if (strlen($numeroCelular) < 10) {
                        $errorMsg = 'El número de celular no tiene el formato correcto. Número procesado: ' . $numeroCelular;
                        $errores[] = 'WhatsApp: ' . $errorMsg;
                        $guardarRegistro('whatsapp', $celularRaw, 'error', $errorMsg);
                        break;
                    }
                    
                    if (strlen($numeroCelular) > 10) {
                        $numeroCelular = substr($numeroCelular, -10);
                    }
                    
                    $sms = new Sms();
                    $resultadoWhatsApp = $sms->enviarWhatsApp([
                        'telefono' => $numeroCelular,
                        'mensaje' => $mensaje
                    ]);
                    
                    $twilioSid = null;
                    if (is_object($resultadoWhatsApp) && isset($resultadoWhatsApp->sid)) {
                        $twilioSid = $resultadoWhatsApp->sid;
                    }
                    
                    $exitosos[] = 'WhatsApp enviado a ' . $celularRaw;
                    $guardarRegistro('whatsapp', $celularRaw, 'exito', null, null, $twilioSid);
                    break;
                    
                default:
                    $errorMsg = 'Canal desconocido: ' . $canal;
                    $errores[] = $errorMsg;
                    $guardarRegistro($canal, 'N/A', 'error', $errorMsg);
                    break;
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $errores[] = ucfirst($canal) . ': ' . $errorMsg;
            
            // Extraer código de error si existe
            $codigoError = null;
            if (preg_match('/Código (\d+):/', $errorMsg, $matches)) {
                $codigoError = $matches[1];
            } elseif (preg_match('/\[HTTP (\d+)\]/', $errorMsg, $matches)) {
                $codigoError = $matches[1];
            }
            
            $destinatario = '';
            if ($canal === 'email') {
                $destinatario = $email ?: 'N/A';
            } else {
                $destinatario = $celularRaw ?: 'N/A';
            }
            
            $guardarRegistro($canal, $destinatario, 'error', $errorMsg, $codigoError);
        }
    }
    
    // Preparar respuesta
    $mensajeRespuesta = '';
    if (count($exitosos) > 0) {
        $mensajeRespuesta .= '<strong>Enviados exitosamente:</strong><ul>';
        foreach ($exitosos as $exito) {
            $mensajeRespuesta .= '<li>' . htmlspecialchars($exito) . '</li>';
        }
        $mensajeRespuesta .= '</ul>';
    }
    
    if (count($errores) > 0) {
        $mensajeRespuesta .= '<strong>Errores:</strong><ul>';
        foreach ($errores as $error) {
            $mensajeRespuesta .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $mensajeRespuesta .= '</ul>';
    }
    
    // Si hubo al menos un envío exitoso, considerar éxito
    $success = count($exitosos) > 0;
    
    if (!$success && count($errores) > 0) {
        $mensajeRespuesta = 'No se pudo enviar el comunicado por ningún canal. ' . $mensajeRespuesta;
    }
    
    echo json_encode([
        'success' => $success,
        'message' => $mensajeRespuesta ?: 'Comunicado enviado correctamente',
        'exitosos' => $exitosos,
        'errores' => $errores
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar comunicado: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

