<?php
/**
 * ENVIAR CONTRATO POR CORREO
 * Endpoint para enviar el contrato institucional a un correo específico
 */

// Limpiar cualquier output previo
if (ob_get_level()) ob_end_clean();
ob_start();

// Habilitar errores para debugging pero no mostrarlos
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Establecer header JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    require_once("session.php");
    require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");
    
    // Verificar permisos
    Modulos::verificarPermisoDev();
    
    error_log("=== INICIO ENVIAR CONTRATO ===");
    
    // Obtener datos del POST
    $ins_id = mysqli_real_escape_string($conexion, $_POST['ins_id'] ?? '');
    $correoDestino = trim($_POST['correo_destino'] ?? '');
    $nombreDestinatario = trim($_POST['nombre_destinatario'] ?? '');
    
    if (empty($ins_id)) {
        throw new Exception('ID de institución no proporcionado');
    }
    
    if (empty($nombreDestinatario)) {
        throw new Exception('Nombre del destinatario no proporcionado');
    }
    
    if (empty($correoDestino)) {
        throw new Exception('Correo de destino no proporcionado');
    }
    
    // Validar formato de correo
    if (!filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del correo electrónico no es válido');
    }
    
    // Obtener datos de la institución
    $consulta = mysqli_query($conexion, "SELECT * FROM " . $baseDatosServicios . ".instituciones 
        WHERE ins_id = '$ins_id' AND ins_enviroment = '" . ENVIROMENT . "'");
    
    if (!$consulta || mysqli_num_rows($consulta) === 0) {
        throw new Exception('No se encontró la institución');
    }
    
    $institucion = mysqli_fetch_array($consulta, MYSQLI_BOTH);
    
    // Verificar que existe un contrato
    if (empty($institucion['ins_contrato'])) {
        throw new Exception('Esta institución no tiene un contrato cargado');
    }
    
    $rutaContrato = ROOT_PATH . '/files-general/contratos/' . $institucion['ins_contrato'];
    
    if (!file_exists($rutaContrato)) {
        throw new Exception('El archivo de contrato no existe en el servidor');
    }
    
    error_log("Contrato a enviar: " . $rutaContrato);
    
    // Preparar datos para el correo
    $dataEmail = [
        'institucion_id'   => $institucion['ins_id'],
        'institucion_agno' => $institucion['ins_year_default'] ?? date('Y'),
        'institucion_nombre' => $institucion['ins_nombre'],
        'institucion_siglas' => $institucion['ins_siglas'],
        'usuario_email'    => $correoDestino,
        'usuario_nombre'   => $nombreDestinatario,
        'usuario_id'       => $_SESSION['id'] ?? 1,
        'nombre_archivo_contrato' => $institucion['ins_contrato']
    ];
    
    $asunto = 'Contrato - ' . $institucion['ins_nombre'];
    $bodyTemplateRoute = ROOT_PATH . '/config-general/plantilla-email-envio-contrato.php';
    
    error_log("Preparando envío de correo...");
    error_log("Destinatario: " . $correoDestino);
    error_log("Asunto: " . $asunto);
    
    // Enviar correo con adjunto
    EnviarEmail::enviar($dataEmail, $asunto, $bodyTemplateRoute, null, [$rutaContrato]);
    
    error_log("✅ Contrato enviado exitosamente");
    
    // Registrar en historial
    try {
        $idPaginaInterna = 'DV0011';
        $error_reporting_original = error_reporting();
        error_reporting(0);
        ob_start();
        @include("../compartido/historial-acciones-guardar.php");
        ob_end_clean();
        error_reporting($error_reporting_original);
    } catch (Exception $e) {
        error_log("Error en historial: " . $e->getMessage());
    }
    
    error_log("=== FIN EXITOSO ENVIAR CONTRATO ===");
    
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => true,
        'message' => '✅ Contrato enviado exitosamente a ' . $correoDestino
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
    
} catch (Exception $e) {
    error_log("=== ERROR ENVIAR CONTRATO ===");
    error_log("Mensaje: " . $e->getMessage());
    
    if (ob_get_level()) ob_clean();
    
    $response = json_encode([
        'success' => false,
        'message' => '❌ ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
    echo $response;
    
    if (ob_get_level()) ob_end_flush();
    exit();
}
?>

