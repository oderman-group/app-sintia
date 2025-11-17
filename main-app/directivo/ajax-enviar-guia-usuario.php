<?php
/**
 * ENVIAR GUÍA INDIVIDUAL A UN USUARIO
 * Endpoint para enviar la guía correspondiente a un usuario específico
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

// Validar que el JSON se haya decodificado correctamente
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar los datos recibidos'
    ]);
    exit();
}

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

// Extraer usuario_id - puede ser string o número, pero nunca array
$usuarioId = '';
if (isset($data['usuario_id'])) {
    if (is_string($data['usuario_id'])) {
        $usuarioId = trim($data['usuario_id']);
    } elseif (is_numeric($data['usuario_id'])) {
        $usuarioId = (string)$data['usuario_id'];
    } elseif (is_array($data['usuario_id']) && count($data['usuario_id']) > 0) {
        // Si por alguna razón viene como array, tomar el primer elemento
        $usuarioId = is_string($data['usuario_id'][0]) ? trim($data['usuario_id'][0]) : (string)$data['usuario_id'][0];
    }
}

$tipoUsuario = isset($data['tipo_usuario']) ? (int)$data['tipo_usuario'] : 0;

// Log inicial
error_log("========================================");
error_log("ENVIAR GUÍA INDIVIDUAL");
error_log("========================================");
error_log("Usuario ID: " . $usuarioId);
error_log("Tipo Usuario: " . $tipoUsuario);

// Validar datos - los IDs son alfanuméricos
if (empty($usuarioId)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de usuario inválido'
    ]);
    exit();
}

try {
    // Obtener datos del usuario usando PDO
    require_once(ROOT_PATH."/main-app/class/Conexion.php");
    $conexionPDO = Conexion::newConnection('PDO');
    $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT uss_id, uss_usuario, uss_nombre, uss_apellido1, uss_apellido2, uss_email, uss_tipo 
            FROM " . BD_GENERAL . ".usuarios 
            WHERE uss_id = ? AND institucion = ? AND year = ?";
    $stmt = $conexionPDO->prepare($sql);
    $stmt->bindParam(1, $usuarioId, PDO::PARAM_STR);
    $stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
    $stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception('Usuario no encontrado');
    }
    
    // Verificar que tenga email válido
    if (empty($usuario['uss_email']) || !EnviarEmail::validarEmail($usuario['uss_email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'El usuario no tiene un email válido registrado'
        ]);
        exit();
    }
    
    // Determinar qué guía enviar según el tipo de usuario
    $nombreGuia = '';
    $rutaGuia = '';
    
    switch ($tipoUsuario) {
        case TIPO_DIRECTIVO:
            $nombreGuia = 'Directivos';
            $rutaGuia = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-directivos.php';
            break;
        case TIPO_DOCENTE:
            $nombreGuia = 'Docentes';
            $rutaGuia = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-docentes.php';
            break;
        case TIPO_ACUDIENTE:
            $nombreGuia = 'Acudientes';
            // TODO: Crear guia-acudientes.php cuando esté disponible
            $rutaGuia = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-acudientes.php';
            break;
        case TIPO_ESTUDIANTE:
            $nombreGuia = 'Estudiantes';
            // TODO: Crear guia-estudiantes.php cuando esté disponible
            $rutaGuia = REDIRECT_ROUTE . '/guias-iniciales-sintia/guia-estudiantes.php';
            break;
        default:
            throw new Exception('Tipo de usuario no válido');
    }
    
    // Preparar datos para el correo
    $nombreCompleto = UsuariosPadre::nombreCompletoDelUsuario($usuario);
    
    $dataEmail = [
        'institucion_id' => $config['conf_id_institucion'],
        'usuario_id' => $usuarioId,
        'usuario_email' => $usuario['uss_email'],
        'usuario_nombre' => $nombreCompleto,
        'guia_nombre' => $nombreGuia,
        'guia_url' => $rutaGuia
    ];
    
    $asunto = 'Guía de Inicio - ' . $nombreGuia . ' | SINTIA';
    $bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-guia-inicial.php';
    
    error_log("Preparando envío de correo...");
    error_log("Destinatario: " . $usuario['uss_email']);
    error_log("Guía: " . $nombreGuia);
    error_log("URL: " . $rutaGuia);
    
    // Enviar correo
    EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, null);
    
    error_log("✅ Correo enviado exitosamente");
    error_log("========================================");
    
    echo json_encode([
        'success' => true,
        'message' => '✉️ Guía enviada exitosamente a ' . $usuario['uss_email']
    ]);
    
} catch (Exception $e) {
    error_log("❌ Error al enviar guía: " . $e->getMessage());
    error_log("========================================");
    
    echo json_encode([
        'success' => false,
        'message' => '⚠️ No se pudo enviar la guía: ' . $e->getMessage()
    ]);
}

exit();

