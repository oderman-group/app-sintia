<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Limpiar cualquier output buffer
while (ob_get_level()) { ob_end_clean(); }
ob_start();

// Headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Cargar configuración
require_once("../../config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/EnviarEmail.php");

function jsonResponse($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    // Verificar sesión
    if (empty($_SESSION["id"])) {
        jsonResponse(['success' => false, 'message' => 'Sesión no válida. Por favor inicia sesión nuevamente.']);
    }
    
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['success' => false, 'message' => 'Método no permitido.']);
    }
    
    // Obtener configuración
    include(ROOT_PATH."/config-general/config.php");
    
    // Conexión a la base de datos
    $conexionPDO = Conexion::newConnection('PDO');
    
    // Obtener y validar datos
    $paraJson = $_POST['para'] ?? '';
    $asunto = trim($_POST['asunto'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    
    // Validar campos
    if (empty($paraJson)) {
        jsonResponse(['success' => false, 'message' => 'Debe seleccionar al menos un destinatario.']);
    }
    
    if (empty($asunto)) {
        jsonResponse(['success' => false, 'message' => 'El asunto es obligatorio.']);
    }
    
    if (empty($contenido)) {
        jsonResponse(['success' => false, 'message' => 'El mensaje no puede estar vacío.']);
    }
    
    // Decodificar destinatarios
    $destinatarios = json_decode($paraJson, true);
    if (!is_array($destinatarios) || count($destinatarios) === 0) {
        jsonResponse(['success' => false, 'message' => 'Formato de destinatarios inválido.']);
    }
    
    // Preparar datos del emisor
    $de = $_SESSION["id"];
    $institucion = $_SESSION["idInstitucion"];
    $year = $_SESSION["bd"];
    
    // Insertar mensaje para cada destinatario
    $enviados = 0;
    $errores = [];
    
    foreach ($destinatarios as $destinatarioId) {
        try {

            $sql = "INSERT INTO ".$baseDatosServicios.".social_emails
                    (ema_de, ema_para, ema_asunto, ema_contenido, ema_fecha, ema_visto, 
                     ema_eliminado_de, ema_eliminado_para, ema_institucion, ema_year)
                    VALUES (?, ?, ?, ?, NOW(), 0, 0, 0, ?, ?)";
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->execute([
                $de,
                $destinatarioId,
                $asunto,
                $contenido,
                $institucion,
                $year
            ]);
            
            $enviados++;
            
            // Enviar correo real al email del destinatario si tiene email válido
            try {
                $sqlDestinatario = "SELECT * FROM ".BD_GENERAL.".usuarios WHERE uss_id=? AND institucion=? AND year=?";
                $stmtDestinatario = $conexionPDO->prepare($sqlDestinatario);
                $stmtDestinatario->execute([$destinatarioId, $config['conf_id_institucion'], $year]);
                $datosDestinatario = $stmtDestinatario->fetch(PDO::FETCH_ASSOC);
                
                // Enviar correo real solo si el destinatario tiene email
                if (!empty($datosDestinatario['uss_email'])) {
                    $contenidoCorreo = '
                        <p style="color:navy;">
                        Hola ' . strtoupper($datosDestinatario['uss_nombre']) . ', has recibido un mensaje a través de la plataforma SINTIA.<br>
                        <b>Remitente:</b> ' . strtoupper($_SESSION["datosUsuario"]['uss_nombre']) . '.
                        </p>
                        <p>' . $contenido . '</p>
                    ';
                    
                    $data = [
                        'contenido_msj'  => $contenidoCorreo,
                        'usuario_email'  => $datosDestinatario['uss_email'],
                        'usuario_nombre' => $_SESSION["datosUsuario"]['uss_nombre'],
                        'institucion_id' => $config['conf_id_institucion'],
                        'usuario_id'     => $destinatarioId
                    ];
                    
                    $bodyTemplateRoute = ROOT_PATH.'/config-general/plantilla-email-2.php';
                    
                    EnviarEmail::enviar($data, $asunto, $bodyTemplateRoute, null, null);
                }
            } catch (Exception $e) {
                // No detener el flujo si falla el envío de correo real
                error_log("Error al enviar correo real a destinatario {$destinatarioId}: " . $e->getMessage());
            }
            
        } catch (Exception $e) {
            $errores[] = "Error al enviar a destinatario " . $destinatarioId . ": " . $e->getMessage();
            error_log("Error al enviar mensaje: " . $e->getMessage());
        }
    }
    
    // Guardar en historial de acciones
    try {
        include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    } catch (Exception $e) {
        // No detener el flujo si falla el historial
        error_log("Error al guardar historial: " . $e->getMessage());
    }
    
    // Respuesta final
    if ($enviados === count($destinatarios)) {
        jsonResponse([
            'success' => true,
            'message' => 'Mensaje enviado correctamente a ' . $enviados . ' destinatario(s).',
            'enviados' => $enviados
        ]);
    } else if ($enviados > 0) {
        jsonResponse([
            'success' => true,
            'message' => 'Mensaje enviado parcialmente. ' . $enviados . ' de ' . count($destinatarios) . ' destinatarios.',
            'enviados' => $enviados,
            'errores' => $errores
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'No se pudo enviar el mensaje a ningún destinatario.',
            'errores' => $errores
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error crítico en ajax-enviar-mensaje.php: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>

