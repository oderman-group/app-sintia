<?php
/**
 * ==========================================
 * ENDPOINT PARA GESTIONAR PUBLICACIONES
 * Cambiar visibilidad, eliminar, etc.
 * ==========================================
 */

include_once("session-compartida.php");

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $postId = isset($input['id']) ? intval($input['id']) : 0;
    $accion = isset($input['accion']) ? $input['accion'] : '';
    $estado = isset($input['estado']) ? intval($input['estado']) : null;

    // Validaciones
    if ($postId <= 0) {
        throw new Exception('ID de publicación inválido');
    }

    // Obtener información de la publicación
    $consultaPost = mysqli_query($conexion, "SELECT not_usuario 
                                              FROM " . $baseDatosServicios . ".social_noticias 
                                              WHERE not_id = '{$postId}'");
    
    if (!$consultaPost || mysqli_num_rows($consultaPost) == 0) {
        throw new Exception('Publicación no encontrada');
    }

    $post = mysqli_fetch_array($consultaPost, MYSQLI_ASSOC);
    $postOwner = $post['not_usuario'];

    // Verificar permisos
    $esOwner = ($_SESSION["id"] == $postOwner);
    $esAdmin = ($datosUsuarioActual['uss_tipo'] == 1 || $datosUsuarioActual['uss_tipo'] == 5);

    if (!$esOwner && !$esAdmin) {
        throw new Exception('No tienes permisos para realizar esta acción');
    }

    // Ejecutar acción
    if ($accion === 'eliminar') {
        // Eliminar publicación
        if (!$esOwner && !$esAdmin) {
            throw new Exception('Solo el propietario o administradores pueden eliminar');
        }

        $delete = mysqli_query($conexion, "DELETE FROM " . $baseDatosServicios . ".social_noticias 
                                           WHERE not_id = '{$postId}'");
        
        if (!$delete) {
            throw new Exception('Error al eliminar la publicación');
        }

        // Eliminar reacciones asociadas
        mysqli_query($conexion, "DELETE FROM " . $baseDatosServicios . ".social_noticias_reacciones 
                                 WHERE npr_noticia = '{$postId}'");

        // Eliminar comentarios asociados
        mysqli_query($conexion, "DELETE FROM " . $baseDatosServicios . ".social_noticias_comentarios 
                                 WHERE ncm_noticia = '{$postId}'");

        // Guardar en historial
        $idPaginaInterna = 'CM0005';
        include("../compartido/guardar-historial-acciones.php");

        echo json_encode([
            'success' => true,
            'message' => 'Publicación eliminada correctamente'
        ], JSON_UNESCAPED_UNICODE);

    } elseif ($estado !== null) {
        // Cambiar visibilidad
        $nuevoEstado = ($estado == 1) ? 1 : 0;

        $update = mysqli_query($conexion, "UPDATE " . $baseDatosServicios . ".social_noticias 
                                           SET not_estado = '{$nuevoEstado}' 
                                           WHERE not_id = '{$postId}'");
        
        if (!$update) {
            throw new Exception('Error al actualizar la visibilidad');
        }

        $mensaje = ($nuevoEstado == 1) ? 'Publicación visible' : 'Publicación oculta';

        echo json_encode([
            'success' => true,
            'message' => $mensaje,
            'estado' => $nuevoEstado
        ], JSON_UNESCAPED_UNICODE);

    } else {
        throw new Exception('Acción no especificada');
    }

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-gestionar.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
