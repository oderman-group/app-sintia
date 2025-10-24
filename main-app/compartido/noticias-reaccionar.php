<?php
/**
 * ==========================================
 * ENDPOINT OPTIMIZADO PARA REACCIONES
 * Maneja reacciones en publicaciones
 * ==========================================
 */

include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/SocialReacciones.php");

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $postId = isset($input['id']) ? intval($input['id']) : 0;
    $reaccionTipo = isset($input['reaccion']) ? intval($input['reaccion']) : 1;

    // Validaciones
    if ($postId <= 0) {
        throw new Exception('ID de publicación inválido');
    }

    if ($reaccionTipo < 1 || $reaccionTipo > 4) {
        throw new Exception('Tipo de reacción inválido');
    }

    // Obtener información de la publicación
    $consultaPost = mysqli_query($conexion, "SELECT not_usuario, not_titulo 
                                              FROM " . $baseDatosServicios . ".social_noticias 
                                              WHERE not_id = '{$postId}'");
    
    if (!$consultaPost || mysqli_num_rows($consultaPost) == 0) {
        throw new Exception('Publicación no encontrada');
    }

    $post = mysqli_fetch_array($consultaPost, MYSQLI_ASSOC);
    $postOwner = $post['not_usuario'];
    $postTitle = $post['not_titulo'];

    // Verificar si el usuario ya reaccionó
    $parametros = [
        "npr_noticia" => $postId,
        "npr_usuario" => $_SESSION["id"]
    ];
    
    $reaccionExistente = SocialReacciones::consultar($parametros);
    
    $accion = 'crear';
    $mensaje = 'Reacción agregada';

    if ($reaccionExistente && isset($reaccionExistente['npr_id'])) {
        // Ya existe una reacción
        if ($reaccionExistente['npr_reaccion'] == $reaccionTipo) {
            // Misma reacción - eliminar
            mysqli_query($conexion, "DELETE FROM " . $baseDatosServicios . ".social_noticias_reacciones WHERE npr_id='" . $reaccionExistente['npr_id'] . "'");
            $accion = 'eliminar';
            $mensaje = 'Reacción eliminada';
        } else {
            // Diferente reacción - actualizar
            mysqli_query($conexion, "UPDATE " . $baseDatosServicios . ".social_noticias_reacciones SET npr_reaccion='" . $reaccionTipo . "', npr_fecha=now() WHERE npr_id='" . $reaccionExistente['npr_id'] . "'");
            $accion = 'actualizar';
            $mensaje = 'Reacción actualizada';
        }
    } else {
        // Nueva reacción - crear
        mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".social_noticias_reacciones(npr_usuario, npr_noticia, npr_reaccion, npr_fecha, npr_estado, npr_institucion, npr_year) VALUES('" . $_SESSION["id"] . "', '" . $postId . "', '" . $reaccionTipo . "', now(), 1, '" . $config['conf_id_institucion'] . "', '" . $_SESSION["bd"] . "')");
        $accion = 'crear';
        
        // Enviar notificación al dueño del post (si no es el mismo usuario)
        if ($postOwner != $_SESSION["id"]) {
            try {
                $tiposReaccion = [
                    1 => 'le gusta',
                    2 => 'le encanta',
                    3 => 'le divierte',
                    4 => 'le entristece'
                ];
                
                $textoReaccion = $tiposReaccion[$reaccionTipo] ?? 'reaccionó a';
                
                $notificacion = [
                    'not_usuario_origen' => $_SESSION["id"],
                    'not_usuario_destino' => $postOwner,
                    'not_tipo' => 'reaccion_post',
                    'not_mensaje' => "A {$datosUsuarioActual['uss_nombre']} {$textoReaccion} tu publicación",
                    'not_enlace' => 'noticias.php#post-' . $postId,
                    'not_fecha' => date('Y-m-d H:i:s'),
                    'not_leido' => 0
                ];
                
                // Si tienes sistema de notificaciones, descomentar:
                // Notificacion::crear($notificacion);
            } catch (Exception $e) {
                // Ignorar errores de notificación
                error_log("Error al enviar notificación de reacción: " . $e->getMessage());
            }
        }
    }

    // Contar reacciones actualizadas
    $parametrosCuenta = ["npr_noticia" => $postId];
    $totalReacciones = intval(SocialReacciones::contar($parametrosCuenta));

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'accion' => $accion,
        'id' => $postId,
        'reaccion' => $reaccionTipo,
        'cantidad' => $totalReacciones
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-reaccionar.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
