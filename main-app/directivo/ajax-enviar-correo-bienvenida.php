<?php
/**
 * ENVIAR CORREO DE BIENVENIDA A INSTITUCIÓN
 * Endpoint para reenviar el correo de bienvenida desde la página de edición
 */

// Configurar zona horaria de Colombia
date_default_timezone_set('America/Bogota');

header('Content-Type: application/json; charset=UTF-8');
require_once("session.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");

// Verificar permisos de desarrollador
Modulos::verificarPermisoDev();

// Obtener datos JSON
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

$institucionId = isset($data['institucionId']) ? (int)$data['institucionId'] : 0;
$year = isset($data['year']) ? mysqli_real_escape_string($conexion, $data['year']) : date('Y');

// Log inicial
error_log("========================================");
error_log("ENVIAR CORREO BIENVENIDA MANUAL");
error_log("========================================");
error_log("Institución ID: " . $institucionId);
error_log("Año: " . $year);

// Validar datos
if ($institucionId == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de institución inválido'
    ]);
    exit();
}

try {
    // Obtener datos de la institución
    $consultaInst = mysqli_query($conexion, "SELECT * FROM " . BD_ADMIN . ".instituciones WHERE ins_id = $institucionId");
    
    if (!$consultaInst || mysqli_num_rows($consultaInst) == 0) {
        throw new Exception('Institución no encontrada');
    }
    
    $datosInsti = mysqli_fetch_array($consultaInst, MYSQLI_BOTH);
    
    // Verificar que tenga email de contacto
    $emailContacto = !empty($datosInsti['ins_email_contacto']) ? $datosInsti['ins_email_contacto'] : null;
    
    if (!$emailContacto) {
        echo json_encode([
            'success' => false,
            'message' => '⚠️ Esta institución no tiene configurado un email de contacto principal.'
        ]);
        exit();
    }
    
    // Obtener nombre del contacto e institución
    $nombreContacto = !empty($datosInsti['ins_contacto_principal']) ? $datosInsti['ins_contacto_principal'] : 'Contacto Principal';
    $nombreInstitucion = !empty($datosInsti['ins_nombre']) ? $datosInsti['ins_nombre'] : 'Institución';
    
    // Buscar el usuario administrador de esta institución (uss_tipo=5, institucion=ins_id)
    $consultaAdmin = mysqli_query($conexion, "SELECT uss_id, uss_usuario, uss_nombre, uss_apellido1, uss_email 
        FROM " . BD_GENERAL . ".usuarios 
        WHERE institucion = $institucionId AND uss_tipo = 5 AND year = '$year'
        ORDER BY uss_id ASC LIMIT 1");
    
    $adminUsuario = 'admin';
    $adminNombre = $nombreContacto;
    
    if ($consultaAdmin && mysqli_num_rows($consultaAdmin) > 0) {
        $datosAdmin = mysqli_fetch_array($consultaAdmin, MYSQLI_BOTH);
        $adminUsuario = $datosAdmin['uss_usuario'];
        $adminNombre = $datosAdmin['uss_nombre'] . ' ' . $datosAdmin['uss_apellido1'];
    }
    
    error_log("Email contacto: " . $emailContacto);
    error_log("Nombre contacto: " . $nombreContacto);
    error_log("Usuario admin: " . $adminUsuario);
    
    // Preparar datos para el correo
    $dataEmail = [
        'institucion_id'   => $institucionId,
        'institucion_agno' => $year,
        'institucion_nombre' => $nombreInstitucion,
        'usuario_email'    => $emailContacto,
        'usuario_nombre'   => $adminNombre,
        'usuario_usuario'  => $adminUsuario,
        'usuario_clave'    => '12345678', // Clave genérica, se recomienda cambiarla
        'url_acceso'       => REDIRECT_ROUTE.'/index.php?inst='.base64_encode($institucionId).'&year='.base64_encode($year)
    ];
    
    $asunto = 'Bienvenido a la Plataforma SINTIA - ' . $nombreInstitucion;
    $bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-bienvenida.php';
    
    error_log("Preparando envío de correo...");
    error_log("Destinatario: " . $emailContacto);
    error_log("Asunto: " . $asunto);
    
    // EnviarEmail::enviar() retorna void, lanza excepción si falla
    EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, null);
    
    error_log("✅ Correo enviado exitosamente");
    error_log("========================================");
    
    echo json_encode([
        'success' => true,
        'message' => '✉️ Correo de bienvenida enviado exitosamente a ' . $emailContacto
    ]);
    
} catch (Exception $e) {
    error_log("❌ Error al enviar correo: " . $e->getMessage());
    error_log("========================================");
    
    echo json_encode([
        'success' => false,
        'message' => '⚠️ No se pudo enviar el correo: ' . $e->getMessage()
    ]);
}

exit();

