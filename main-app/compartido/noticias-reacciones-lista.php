<?php
include("session-compartida.php");

try {
    $postId = isset($_POST['postId']) ? intval($_POST['postId']) : 0;
    
    if ($postId <= 0) {
        throw new Exception("ID de publicación inválido");
    }
    
    // Consulta para obtener las reacciones con información del usuario
    // La tabla usuarios está en BD_GENERAL
    $sql = "SELECT 
                npr.npr_reaccion,
                npr.npr_fecha,
                u.uss_id,
                u.uss_nombre,
                u.uss_nombre2,
                u.uss_apellido1,
                u.uss_apellido2,
                u.uss_foto
            FROM social_noticias_reacciones npr
            INNER JOIN ".BD_GENERAL.".usuarios u ON u.uss_id = npr.npr_usuario 
                AND u.institucion = npr.npr_institucion 
                AND u.year = npr.npr_year
            WHERE npr.npr_noticia = ?
            AND npr.npr_institucion = ?
            AND npr.npr_year = ?
            ORDER BY npr.npr_fecha DESC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $postId, $config['conf_id_institucion'], $_SESSION["bd"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $reacciones = [];
    while ($row = $resultado->fetch_assoc()) {
        // Construir nombre completo
        $nombreCompleto = trim($row['uss_nombre'] . ' ' . ($row['uss_nombre2'] ?? '') . ' ' . 
                              $row['uss_apellido1'] . ' ' . ($row['uss_apellido2'] ?? ''));
        
        // Foto del usuario
        $fotoUsuario = '../files/fotos/default.png';
        if (!empty($row['uss_foto']) && file_exists(ROOT_PATH.'/main-app/files/fotos/'.$row['uss_foto'])) {
            $fotoUsuario = '../files/fotos/'.$row['uss_foto'];
        }
        
        // Tipo de reacción
        $tipoReaccion = intval($row['npr_reaccion']);
        $reaccionEmoji = '👍';
        $reaccionTexto = 'Me gusta';
        
        switch ($tipoReaccion) {
            case 1:
                $reaccionEmoji = '👍';
                $reaccionTexto = 'Me gusta';
                break;
            case 2:
                $reaccionEmoji = '❤️';
                $reaccionTexto = 'Me encanta';
                break;
            case 3:
                $reaccionEmoji = '😄';
                $reaccionTexto = 'Me divierte';
                break;
            case 4:
                $reaccionEmoji = '😢';
                $reaccionTexto = 'Me entristece';
                break;
        }
        
        // Fecha formateada
        $fecha = formatearFecha($row['npr_fecha']);
        
        $reacciones[] = [
            'usuarioId' => $row['uss_id'],
            'nombreCompleto' => $nombreCompleto,
            'foto' => $fotoUsuario,
            'tipoReaccion' => $tipoReaccion,
            'reaccionEmoji' => $reaccionEmoji,
            'reaccionTexto' => $reaccionTexto,
            'fecha' => $fecha
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reacciones' => $reacciones,
        'total' => count($reacciones)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar las reacciones',
        'error' => $e->getMessage()
    ]);
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

