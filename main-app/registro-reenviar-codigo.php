<?php
/**
 * REENVIAR CÓDIGO DE VERIFICACIÓN PARA REGISTRO
 * Similar a recuperación de clave pero con proceso ACTIVAR_CUENTA
 */

header('Content-Type: application/json; charset=UTF-8');
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/Notificacion.php");

$notificacion = new Notificacion();
$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);

if (!$conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit();
}

mysqli_set_charset($conexion, "utf8mb4");

// Obtener usuario ID
$usuarioId = isset($_POST['usuarioId']) ? (int)$_POST['usuarioId'] : 0;

if ($usuarioId == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de usuario inválido'
    ]);
    exit();
}

try {
    // Buscar datos del usuario por uss_id (el generado manualmente)
    error_log("Reenvío - Buscando usuario con uss_id: " . $usuarioId);
    
    $sql = "SELECT u.*, i.ins_id, i.ins_nombre 
        FROM " . BD_GENERAL . ".usuarios u
        INNER JOIN " . BD_ADMIN . ".instituciones i ON u.institucion = i.ins_id
        WHERE u.uss_id = {$usuarioId}";
    
    $consulta = mysqli_query($conexion, $sql);
    
    if (mysqli_num_rows($consulta) == 0) {
        error_log("ERROR: Usuario no encontrado en reenvío");
        echo json_encode([
            'success' => false,
            'message' => 'Usuario no encontrado'
        ]);
        exit();
    }
    
    $datosUsuario = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    error_log("Usuario encontrado para reenvío: " . $datosUsuario['uss_email']);
    
    // Preparar datos para enviar código
    $data = [
        'usuario_nombre' => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
        'institucion_id' => $datosUsuario['institucion'],
        'usuario_id' => $datosUsuario['uss_id'],  // uss_id generado
        'year' => $datosUsuario['year'],
        'asunto' => 'Código de Verificación: ',  // El código se agrega automáticamente
        'body_template_route' => ROOT_PATH . '/config-general/template-email-codigo-verificacion.php',
        'usuario_email' => $datosUsuario['uss_email'],
        'telefono' => $datosUsuario['uss_celular'],
        'datos_codigo' => [],
    ];
    
    // IMPORTANTE: Usar PROCESO_ACTIVAR_CUENTA, no RECUPERAR_CLAVE
    $canal = Notificacion::CANAL_EMAIL;
    $datosCodigo = $notificacion->enviarCodigoNotificacion($data, $canal, Notificacion::PROCESO_ACTIVAR_CUENTA);
    
    error_log("Código reenviado: " . $datosCodigo['codigo']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Código reenviado exitosamente',
        'usuarioEmail' => $datosUsuario['uss_email'],
        'usuarioNombre' => $datosUsuario['uss_nombre'] . ' ' . $datosUsuario['uss_apellido1'],
        'institucionId' => $datosUsuario['institucion'],
        'usuarioId' => $datosUsuario['uss_id'],  // uss_id generado
        'year' => $datosUsuario['year'],
        'telefono' => $datosUsuario['uss_celular'],
        'datosCodigo' => $datosCodigo
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al reenviar código: ' . $e->getMessage()
    ]);
}

exit();

