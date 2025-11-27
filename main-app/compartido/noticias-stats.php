<?php
/**
 * ==========================================
 * ENDPOINT PARA OBTENER ESTADÍSTICAS DE POST
 * Retorna contadores de reacciones y comentarios
 * ==========================================
 */

include_once("session-compartida.php");
require_once(ROOT_PATH . "/main-app/class/SocialComentarios.php");
require_once(ROOT_PATH . "/main-app/class/SocialReacciones.php");

header('Content-Type: application/json');

try {
    // Obtener datos del request
    $input = json_decode(file_get_contents("php://input"), true);
    $postId = isset($input['postId']) ? intval($input['postId']) : 0;

    // Validaciones
    if ($postId <= 0) {
        throw new Exception('ID de publicación inválido');
    }

    // Contar reacciones
    $parametrosReacciones = ["npr_noticia" => $postId];
    $numReacciones = intval(SocialReacciones::contar($parametrosReacciones));

    // Contar comentarios
    $parametrosComentarios = ["ncm_noticia" => $postId, "ncm_padre" => 0];
    $numComentarios = intval(SocialComentarios::contar($parametrosComentarios));

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'reacciones' => $numReacciones,
        'comentarios' => $numComentarios
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Registrar error
    error_log("Error en noticias-stats.php: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

