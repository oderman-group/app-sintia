<?php
/**
 * ENVIAR GUÍA MASIVA A USUARIOS
 * Endpoint para enviar guías a múltiples usuarios según tipo o lista de IDs
 */

// Configurar zona horaria de Colombia
date_default_timezone_set('America/Bogota');

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH."/main-app/class/App/Seguridad/Csrf.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");

// Verificar permisos
if (!Modulos::validarPermisoEdicion()) {
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para realizar esta acción'
    ]);
    exit();
}

// Obtener datos JSON
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Validar token CSRF
if (empty($data['csrf_token']) || !Csrf::validarToken($data['csrf_token'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Token de seguridad inválido. Por favor recarga la página.',
        'code' => 'CSRF_INVALID',
        'reload' => true
    ]);
    exit();
}

$tipoUsuario = isset($data['tipo_usuario']) ? (int)$data['tipo_usuario'] : null;
$usuariosIds = isset($data['usuarios_ids']) && is_array($data['usuarios_ids']) ? $data['usuarios_ids'] : null;

// Log inicial
error_log("========================================");
error_log("ENVIAR GUÍA MASIVA");
error_log("========================================");
error_log("Tipo Usuario: " . ($tipoUsuario ?? 'N/A'));
error_log("Usuarios IDs: " . ($usuariosIds ? implode(', ', $usuariosIds) : 'N/A'));

// Validar que se haya proporcionado tipo o lista de usuarios
if (is_null($tipoUsuario) && (is_null($usuariosIds) || empty($usuariosIds))) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes especificar un tipo de usuario o una lista de usuarios'
    ]);
    exit();
}

try {
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Construir query según el caso
    if (!is_null($tipoUsuario)) {
        // Enviar a todos los usuarios de un tipo específico
        $sql = "SELECT uss_id, uss_usuario, uss_nombre, uss_apellido1, uss_apellido2, uss_email, uss_tipo 
                FROM " . BD_GENERAL . ".usuarios 
                WHERE uss_tipo = ? AND institucion = ? AND year = ? 
                AND uss_email IS NOT NULL AND uss_email != ''";
        $stmt = $conexionPDO->prepare($sql);
        $stmt->bindParam(1, $tipoUsuario, PDO::PARAM_INT);
        $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $tipoEnvio = $tipoUsuario;
    } else {
        // Enviar a usuarios específicos por IDs
        if (empty($usuariosIds)) {
            throw new Exception('No se proporcionaron usuarios');
        }
        
        // Crear placeholders para la consulta IN
        $placeholders = str_repeat('?,', count($usuariosIds) - 1) . '?';
        
        $sql = "SELECT uss_id, uss_usuario, uss_nombre, uss_apellido1, uss_apellido2, uss_email, uss_tipo 
                FROM " . BD_GENERAL . ".usuarios 
                WHERE uss_id IN ($placeholders) AND institucion = ? AND year = ? 
                AND uss_email IS NOT NULL AND uss_email != ''";
        $stmt = $conexionPDO->prepare($sql);
        
        $paramIndex = 1;
        foreach ($usuariosIds as $id) {
            // Asegurar que el ID sea string (son alfanuméricos)
            $idStr = trim((string)$id);
            $stmt->bindParam($paramIndex++, $idStr, PDO::PARAM_STR);
        }
        $stmt->bindParam($paramIndex++, $config['conf_id_institucion'], PDO::PARAM_INT);
        $stmt->bindParam($paramIndex, $_SESSION["bd"], PDO::PARAM_INT);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Determinar el tipo más común (para el nombre de la guía)
        $tipos = array_column($usuarios, 'uss_tipo');
        $tipoEnvio = !empty($tipos) ? $tipos[0] : null;
    }
    
    if (empty($usuarios)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron usuarios con email válido'
        ]);
        exit();
    }
    
    // Determinar qué guía enviar según el tipo
    $nombreGuia = '';
    $rutaGuia = '';
    
    switch ($tipoEnvio) {
        case TIPO_DIRECTIVO:
            $nombreGuia = 'Directivos';
            $rutaGuia = REDIRECT_ROUTE . '/main-app/guias-iniciales-sintia/guia-directivos.php';
            break;
        case TIPO_DOCENTE:
            $nombreGuia = 'Docentes';
            $rutaGuia = REDIRECT_ROUTE . '/main-app/guias-iniciales-sintia/guia-docentes.php';
            break;
        case TIPO_ACUDIENTE:
            $nombreGuia = 'Acudientes';
            $rutaGuia = REDIRECT_ROUTE . '/main-app/guias-iniciales-sintia/guia-acudientes.php';
            break;
        case TIPO_ESTUDIANTE:
            $nombreGuia = 'Estudiantes';
            $rutaGuia = REDIRECT_ROUTE . '/main-app/guias-iniciales-sintia/guia-estudiantes.php';
            break;
        default:
            $nombreGuia = 'Usuarios';
            $rutaGuia = REDIRECT_ROUTE . '/main-app/guias-iniciales-sintia/guia-directivos.php';
    }
    
    $bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-guia-inicial.php';
    
    $enviados = 0;
    $errores = 0;
    $sinEmail = 0;
    $erroresDetalle = [];
    
    error_log("Procesando " . count($usuarios) . " usuarios...");
    
    // Enviar a cada usuario
    foreach ($usuarios as $usuario) {
        // Validar email
        if (empty($usuario['uss_email']) || !EnviarEmail::validarEmail($usuario['uss_email'])) {
            $sinEmail++;
            continue;
        }
        
        // Determinar tipo de usuario para este usuario específico (si es selección múltiple)
        $tipoUsuarioActual = $usuario['uss_tipo'];
        $nombreGuiaActual = $nombreGuia;
        $rutaGuiaActual = $rutaGuia;
        
        // Si el tipo de usuario es diferente, ajustar la guía
        if ($tipoUsuarioActual != $tipoEnvio && !is_null($usuariosIds)) {
            switch ($tipoUsuarioActual) {
                case TIPO_DIRECTIVO:
                    $nombreGuiaActual = 'Directivos';
                    $rutaGuiaActual = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-directivos.php';
                    break;
                case TIPO_DOCENTE:
                    $nombreGuiaActual = 'Docentes';
                    $rutaGuiaActual = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-docentes.php';
                    break;
                case TIPO_ACUDIENTE:
                    $nombreGuiaActual = 'Acudientes';
                    $rutaGuiaActual = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-acudientes.php';
                    break;
                case TIPO_ESTUDIANTE:
                    $nombreGuiaActual = 'Estudiantes';
                    $rutaGuiaActual = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-estudiantes.php';
                    break;
            }
        }
        
        try {
            $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($usuario);
            
            $dataEmail = [
                'institucion_id' => $config['conf_id_institucion'],
                'usuario_id' => $usuario['uss_id'],
                'usuario_email' => $usuario['uss_email'],
                'usuario_nombre' => $nombreCompleto,
                'guia_nombre' => $nombreGuiaActual,
                'guia_url' => $rutaGuiaActual
            ];
            
            $asunto = 'Guía de Inicio - ' . $nombreGuiaActual . ' | SINTIA';
            
            EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, null);
            $enviados++;
            
        } catch (Exception $e) {
            $errores++;
            $erroresDetalle[] = $usuario['uss_email'] . ': ' . $e->getMessage();
            error_log("Error enviando a " . $usuario['uss_email'] . ": " . $e->getMessage());
        }
    }
    
    error_log("✅ Proceso completado");
    error_log("Enviados: " . $enviados);
    error_log("Errores: " . $errores);
    error_log("Sin email: " . $sinEmail);
    error_log("========================================");
    
    // Construir mensaje de respuesta
    $mensaje = "Proceso completado:<br>";
    $mensaje .= "✅ <strong>" . $enviados . "</strong> guías enviadas exitosamente<br>";
    
    if ($sinEmail > 0) {
        $mensaje .= "⚠️ <strong>" . $sinEmail . "</strong> usuarios sin email válido<br>";
    }
    
    if ($errores > 0) {
        $mensaje .= "❌ <strong>" . $errores . "</strong> errores al enviar<br>";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje,
        'enviados' => $enviados,
        'errores' => $errores,
        'sin_email' => $sinEmail,
        'errores_detalle' => $erroresDetalle
    ]);
    
} catch (Exception $e) {
    error_log("❌ Error en proceso masivo: " . $e->getMessage());
    error_log("========================================");
    
    echo json_encode([
        'success' => false,
        'message' => '⚠️ Error al procesar el envío masivo: ' . $e->getMessage()
    ]);
}

exit();

