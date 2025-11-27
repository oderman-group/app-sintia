<?php
/**
 * ==========================================
 * ENDPOINT OPTIMIZADO PARA AGREGAR COMENTARIOS
 * Maneja comentarios y respuestas en publicaciones
 * ==========================================
 */

include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");
require_once(ROOT_PATH . "/main-app/class/UsuariosPadre.php");

// Instanciar clase de usuarios
$usuariosClase = new UsuariosFunciones();

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $postId = isset($input['postId']) ? intval($input['postId']) : 0;
    $comentario = isset($input['comentario']) ? trim($input['comentario']) : '';
    $padre = isset($input['padre']) ? intval($input['padre']) : 0;

    // Validaciones
    if ($postId <= 0) {
        throw new Exception('ID de publicación inválido');
    }

    if (empty($comentario)) {
        throw new Exception('El comentario no puede estar vacío');
    }

    if (strlen($comentario) > 1000) {
        throw new Exception('El comentario es demasiado largo (máximo 1000 caracteres)');
    }

    // Sanitizar comentario
    $comentario = mysqli_real_escape_string($conexion, $comentario);

    // Obtener información de la publicación
    $consultaPost = mysqli_query($conexion, "SELECT not_usuario, not_titulo 
                                              FROM " . $baseDatosServicios . ".social_noticias 
                                              WHERE not_id = '{$postId}'");
    
    if (!$consultaPost || mysqli_num_rows($consultaPost) == 0) {
        throw new Exception('Publicación no encontrada');
    }

    $post = mysqli_fetch_array($consultaPost, MYSQLI_ASSOC);
    $postOwner = $post['not_usuario'];

    // Crear comentario (la función espera parámetros individuales)
    $idComentario = SocialComentarios::guardar($postId, $comentario, $padre);

    if (!$idComentario) {
        throw new Exception('Error al guardar el comentario');
    }

    // Obtener información del usuario que comenta
    $fotoUsuario = $usuariosClase->verificarFoto($datosUsuarioActual['uss_foto']);
    $nombreUsuario = UsuariosPadre::nombreCompletoDelUsuario($datosUsuarioActual);

    // Contar comentarios actualizados
    $parametros = ["ncm_noticia" => $postId, "ncm_padre" => 0];
    $totalComentarios = intval(SocialComentarios::contar($parametros));

    // Enviar notificación
    if ($postOwner != $_SESSION["id"]) {
        try {
            $tipoComentario = ($padre > 0) ? 'respuesta' : 'comentario';
            
            $notificacion = [
                'not_usuario_origen' => $_SESSION["id"],
                'not_usuario_destino' => $postOwner,
                'not_tipo' => 'comentario_post',
                'not_mensaje' => "{$nombreUsuario} agregó un {$tipoComentario} en tu publicación",
                'not_enlace' => 'noticias.php#post-' . $postId,
                'not_fecha' => date('Y-m-d H:i:s'),
                'not_leido' => 0
            ];
            
            // Si tienes sistema de notificaciones, descomentar:
            // Notificacion::crear($notificacion);
        } catch (Exception $e) {
            // Ignorar errores de notificación
            error_log("Error al enviar notificación de comentario: " . $e->getMessage());
        }
    }

    // Preparar respuesta con el comentario creado
    $comment = [
        'id' => $idComentario,
        'postId' => $postId,
        'padre' => $padre,
        'usuarioId' => $_SESSION["id"],
        'nombreUsuario' => $nombreUsuario,
        'foto' => $fotoUsuario,
        'texto' => $comentario,
        'fecha' => 'Ahora',
        'respuestas' => []
    ];

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Comentario agregado correctamente',
        'comment' => $comment,
        'totalComentarios' => $totalComentarios
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-comentario-agregar.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

