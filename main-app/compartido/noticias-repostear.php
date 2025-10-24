<?php
/**
 * ENDPOINT PARA REPOSTEAR PUBLICACIONES
 * Comparte una publicación en el perfil del usuario
 */

include_once("session-compartida.php");

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $postId = isset($input['postId']) ? intval($input['postId']) : 0;

    // Validaciones
    if ($postId <= 0) {
        throw new Exception('ID de publicación inválido');
    }

    // Obtener información de la publicación original
    $consultaPost = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".social_noticias WHERE not_id = '{$postId}'");
    
    if (!$consultaPost || mysqli_num_rows($consultaPost) == 0) {
        throw new Exception('Publicación no encontrada');
    }

    $postOriginal = mysqli_fetch_array($consultaPost, MYSQLI_ASSOC);

    // Crear el repost (nueva publicación haciendo referencia a la original)
    $estado = ($datosUsuarioActual['uss_tipo'] == 4) ? 0 : 1;
    $destinatarios = "1,2,3,4,5";

    $descripcionRepost = "📢 Publicación compartida:\n\n---\n\n" . $postOriginal['not_descripcion'];
    $tituloRepost = "🔁 " . $postOriginal['not_titulo'];

    mysqli_query($conexion, "INSERT INTO ".$baseDatosServicios.".social_noticias(
        not_usuario, 
        not_titulo,
        not_descripcion, 
        not_fecha, 
        not_estado, 
        not_para, 
        not_institucion, 
        not_year,
        not_imagen
    ) VALUES(
        '" . $_SESSION["id"] . "',
        '" . mysqli_real_escape_string($conexion, $tituloRepost) . "',
        '" . mysqli_real_escape_string($conexion, $descripcionRepost) . "',
        now(), 
        '" . $estado . "', 
        '" . $destinatarios . "',
        '" . $config['conf_id_institucion'] . "',
        '" . $_SESSION["bd"] . "',
        ''
    )");

    $idRepost = mysqli_insert_id($conexion);

    if (!$idRepost) {
        throw new Exception('Error al crear el repost');
    }

    // Notificar al dueño del post original
    if ($postOriginal['not_usuario'] != $_SESSION["id"]) {
        mysqli_query($conexion, "INSERT INTO " . $baseDatosServicios . ".general_alertas (
            alr_nombre, 
            alr_descripcion, 
            alr_tipo, 
            alr_usuario, 
            alr_fecha_envio, 
            alr_categoria, 
            alr_importancia, 
            alr_vista, 
            alr_institucion, 
            alr_year
        ) VALUES(
            '<b>" . $datosUsuarioActual['uss_nombre'] . "</b> compartió tu publicación', 
            '<b>" . $datosUsuarioActual['uss_nombre'] . "</b> compartió tu publicación \"" . $postOriginal['not_titulo'] . "\".', 
            2, 
            '" . $postOriginal['not_usuario'] . "', 
            now(), 
            3, 
            2, 
            0,
            '" . $config['conf_id_institucion'] . "',
            '" . $_SESSION["bd"] . "'
        )");
    }

    // Guardar historial
    $idPaginaInterna = 'CM0005';
    include("../compartido/guardar-historial-acciones.php");

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Publicación compartida correctamente',
        'repostId' => $idRepost
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-repostear.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

