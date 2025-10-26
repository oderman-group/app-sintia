<?php
header('Content-Type: application/json');

try {
    require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
    require_once(ROOT_PATH . "/main-app/class/Usuarios.php");
    require_once(ROOT_PATH . "/main-app/class/Notificacion.php");

    if (empty($_REQUEST['usuarioId'])) {
        echo json_encode([
            "success" => false, 
            "message" => "ID de usuario no proporcionado"
        ]);
        exit;
    }

    $notificacion = new Notificacion();
    $datosUsuario = Usuarios::buscarUsuarioIdNuevo($_REQUEST['usuarioId']);
    
    if (empty($datosUsuario)) {
        echo json_encode([
            "success" => false, 
            "message" => "Usuario no encontrado"
        ]);
        exit;
    }

    $numeroCelular = !empty($datosUsuario['uss_celular']) ? preg_replace('/[()\s-]/', '', $datosUsuario['uss_celular']) : '';

    $data = [
        'usuario_nombre'      => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
        'institucion_id'      => $datosUsuario['institucion'],
        'usuario_id'          => $datosUsuario['uss_id'],
        'year'                => $datosUsuario['year'],
        'asunto'              => 'Código de Confirmación: ',
        'body_template_route' => ROOT_PATH .'/config-general/template-email-recuperar-clave-codigo.php',
        'usuario_email'       => $datosUsuario['uss_email'],
        'telefono'            => $numeroCelular,
        'id_nuevo'            => $datosUsuario['id_nuevo'],
        'datos_codigo'        => [],
    ];

    $canal = Notificacion::CANAL_SMS;
    $datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_RECUPERAR_CLAVE);
    
    echo json_encode([
        "success" => true,
        "message" => "Código enviado exitosamente por SMS",
        "telefono" => $numeroCelular,
        "code" => $datosCodigo,
        "datosCodigo" => $datosCodigo
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false, 
        "message" => "Error al enviar SMS: " . $e->getMessage()
    ]);
}
exit;