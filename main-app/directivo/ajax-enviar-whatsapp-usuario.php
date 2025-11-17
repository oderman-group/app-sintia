<?php
header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");

// Verificar permisos
Modulos::validarAccesoDirectoPaginas();

try {
    require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
    require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
    require_once(ROOT_PATH . "/main-app/class/Sms.php");

    // Debug: Ver qué está llegando
    error_log("POST recibido: " . print_r($_POST, true));
    
    if (empty($_POST['usuario_id']) || $_POST['usuario_id'] == '0' || $_POST['usuario_id'] == 0) {
        throw new Exception('ID de usuario no proporcionado o inválido. Valor recibido: ' . ($_POST['usuario_id'] ?? 'NULL'));
    }

    // El ID puede ser numérico (uss_id) o alfanumérico (uss_usuario como "USU211")
    // NO convertir a entero porque puede ser alfanumérico según la memoria del usuario
    $usuarioId = trim($_POST['usuario_id']);
    
    if (empty($usuarioId) || $usuarioId === '0') {
        throw new Exception('ID de usuario inválido: ' . ($_POST['usuario_id'] ?? 'NULL'));
    }
    
    $mensajePersonalizado = !empty($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

    // Buscar usuario por uss_id o uss_usuario (obtenerDatosUsuario busca por ambos)
    // El método obtiene los datos usando: WHERE (uss_id=? || uss_usuario=?)
    // Por lo que acepta tanto números como alfanuméricos
    $datosUsuario = Usuarios::obtenerDatosUsuario($usuarioId);
    
    if (empty($datosUsuario)) {
        throw new Exception('Usuario no encontrado con ID: ' . $usuarioId);
    }

    // Validar que tenga número de celular
    $celularRaw = !empty($datosUsuario['uss_celular']) ? trim($datosUsuario['uss_celular']) : '';
    
    if (empty($celularRaw)) {
        throw new Exception('El usuario no tiene número de celular registrado');
    }
    
    // Procesar el número: eliminar espacios, guiones, paréntesis
    $numeroCelular = preg_replace('/[()\s-]/', '', $celularRaw);
    
    // Si el número ya tiene el prefijo +57, removerlo para que no se duplique
    // El método enviarWhatsApp ya agrega el prefijo +57 automáticamente
    if (strpos($numeroCelular, '+57') === 0) {
        $numeroCelular = substr($numeroCelular, 3); // Remover +57
    } elseif (strpos($numeroCelular, '57') === 0 && strlen($numeroCelular) > 10) {
        $numeroCelular = substr($numeroCelular, 2); // Remover 57 si está al inicio
    }
    
    // Validar que el número tenga al menos 10 dígitos (número colombiano)
    if (strlen($numeroCelular) < 10) {
        throw new Exception('El número de celular no tiene el formato correcto. Número procesado: ' . $numeroCelular);
    }

    // Preparar mensaje
    if (empty($mensajePersonalizado)) {
        $mensaje = 'Hola ' . $datosUsuario['uss_nombre'] . ', este es un mensaje de prueba desde SINTIA. ¡Gracias por ser parte de nuestra plataforma!';
    } else {
        $mensaje = $mensajePersonalizado;
    }

    // Enviar WhatsApp
    $sms = new Sms();
    $sms->enviarWhatsApp([
        'telefono' => $numeroCelular,
        'mensaje' => $mensaje
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'WhatsApp enviado exitosamente a ' . UsuariosPadre::nombreCompletoDelUsuario($datosUsuario),
        'telefono' => $numeroCelular
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al enviar WhatsApp: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

