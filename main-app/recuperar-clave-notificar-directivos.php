<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/App/Administrativo/Usuario/Usuario.php");
require_once(ROOT_PATH . "/main-app/class/App/Socket/Socket.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH . "/main-app/class/Usuarios.php");

try {
    if (!empty($_REQUEST['usuarioId'])) {
        $datosUsuario = Usuarios::buscarUsuarioIdNuevo($_REQUEST['usuarioId']);
    }

    $nombreEmisor = UsuariosPadre::nombreCompletoDelUsuario($datosUsuario);

    $predicadoD = [
        'uss_tipo'      => TIPO_DIRECTIVO,
        'institucion'   => $datosUsuario['institucion'],
        'year'          => $datosUsuario["year"]
    ];

    $camposD = "uss_id";
    $consultaDirectivos = Administrativo_Usuario_Usuario::Select($predicadoD, $camposD, BD_GENERAL);

    while ($datosDirectivo = $consultaDirectivos->fetch(PDO::FETCH_ASSOC)) {
        $data = [
            "year"          => $datosUsuario["year"],
            "institucion"   => $datosUsuario['institucion'],
            "emisor"        => $datosUsuario['uss_id'],
            "nombreEmisor"  => $nombreEmisor,
            "asunto"        => "REESTABLECER CONTRASEÑA",
            "contenido"     => 'El usuario ' . $nombreEmisor . ' supero el maximo de intentos para recuperar su contraseña, se solicita reestablecer su contraseña.<br> <a href="usuarios-editar.php?id=' . base64_encode($datosUsuario['uss_id']) . '" id="addRow" class="btn deepPink-bgcolor">REESTABLECER CONTRASEÑA</a>',
            "receptor"      => $datosDirectivo['uss_id']
        ];

        // Enviar datos al WebSocket
        if (!Socket::socket_emit("enviar_mensaje_correo", $data)) {
            error_log("Error al enviar mensaje al WebSocket");
        }
    }

    $arrayIdInsercion = [
        "success" => true,
        "message" => "Haz superado el maximo de intentos para reestablecer su contraseña, hemos notificado a los directivos de la institución para que le generen una contraseña nueva,<br> en segundos sera redirigido al login y le notificaremos por correo electrónico.",
    ];
} catch (Exception $e) {
    $arrayIdInsercion = ["success" => false, "message" => "Error al notificar a los directivos: " . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($arrayIdInsercion);