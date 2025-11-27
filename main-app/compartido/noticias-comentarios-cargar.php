<?php
/**
 * ==========================================
 * ENDPOINT OPTIMIZADO PARA CARGAR COMENTARIOS
 * Retorna JSON con comentarios de una publicación
 * ==========================================
 */

include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");

// Instanciar clase de usuarios
$usuariosClase = new UsuariosFunciones();

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $postId = isset($input['postId']) ? intval($input['postId']) : 0;

    // Validaciones
    if ($postId <= 0) {
        throw new Exception('ID de publicación inválido');
    }

    // Obtener comentarios principales (sin padre)
    $parametros = ["ncm_noticia" => $postId, "ncm_padre" => 0];
    $comentariosData = SocialComentarios::listar($parametros);

    $comments = [];

    if ($comentariosData) {
        foreach ($comentariosData as $comentario) {
            // Obtener información del usuario
            $consultaUsuario = mysqli_query($conexion, "SELECT uss_nombre, uss_foto 
                                                        FROM " . BD_GENERAL . ".usuarios 
                                                        WHERE uss_id = '{$comentario['ncm_usuario']}' 
                                                        AND institucion = {$config['conf_id_institucion']}
                                                        AND year = {$_SESSION["bd"]}");
            
            if ($consultaUsuario && mysqli_num_rows($consultaUsuario) > 0) {
                $usuario = mysqli_fetch_array($consultaUsuario, MYSQLI_ASSOC);
                $fotoUsuario = $usuariosClase->verificarFoto($usuario['uss_foto']);
                $nombreUsuario = $usuario['uss_nombre'];
            } else {
                $fotoUsuario = $usuariosClase->verificarFoto('');
                $nombreUsuario = 'Usuario';
            }

            // Formatear fecha
            $fecha = $comentario['ncm_fecha'];
            $fechaFormateada = formatearFecha($fecha);

            // Obtener respuestas de este comentario
            $parametrosRespuestas = ["ncm_noticia" => $postId, "ncm_padre" => $comentario['ncm_id']];
            $respuestasData = SocialComentarios::listar($parametrosRespuestas);

            $respuestas = [];
            if ($respuestasData) {
                foreach ($respuestasData as $respuesta) {
                    // Obtener información del usuario de la respuesta
                    $consultaUsuarioResp = mysqli_query($conexion, "SELECT uss_nombre, uss_foto 
                                                                     FROM " . BD_GENERAL . ".usuarios 
                                                                     WHERE uss_id = '{$respuesta['ncm_usuario']}'
                                                                     AND institucion = {$config['conf_id_institucion']}
                                                                     AND year = {$_SESSION["bd"]}");
                    
                    if ($consultaUsuarioResp && mysqli_num_rows($consultaUsuarioResp) > 0) {
                        $usuarioResp = mysqli_fetch_array($consultaUsuarioResp, MYSQLI_ASSOC);
                        $fotoUsuarioResp = $usuariosClase->verificarFoto($usuarioResp['uss_foto']);
                        $nombreUsuarioResp = $usuarioResp['uss_nombre'];
                    } else {
                        $fotoUsuarioResp = $usuariosClase->verificarFoto('');
                        $nombreUsuarioResp = 'Usuario';
                    }

                    $fechaResp = $respuesta['ncm_fecha'];
                    $fechaRespFormateada = formatearFecha($fechaResp);

                    $respuestas[] = [
                        'id' => $respuesta['ncm_id'],
                        'usuarioId' => $respuesta['ncm_usuario'],
                        'nombreUsuario' => $nombreUsuarioResp,
                        'foto' => $fotoUsuarioResp,
                        'texto' => $respuesta['ncm_comentario'],
                        'fecha' => $fechaRespFormateada
                    ];
                }
            }

            $comments[] = [
                'id' => $comentario['ncm_id'],
                'usuarioId' => $comentario['ncm_usuario'],
                'nombreUsuario' => $nombreUsuario,
                'foto' => $fotoUsuario,
                'texto' => $comentario['ncm_comentario'],
                'fecha' => $fechaFormateada,
                'respuestas' => $respuestas
            ];
        }
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'comments' => $comments,
        'count' => count($comments)
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-comentarios-cargar.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar comentarios',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Formatear fecha de forma amigable
 */
function formatearFecha($fecha) {
    $timestamp = strtotime($fecha);
    $diferencia = time() - $timestamp;
    
    if ($diferencia < 60) {
        return 'Ahora';
    } elseif ($diferencia < 3600) {
        $minutos = floor($diferencia / 60);
        return $minutos . ' min';
    } elseif ($diferencia < 86400) {
        $horas = floor($diferencia / 3600);
        return $horas . ' h';
    } elseif ($diferencia < 604800) {
        $dias = floor($diferencia / 86400);
        return $dias . ' d';
    } else {
        return date('d/m/Y', $timestamp);
    }
}

