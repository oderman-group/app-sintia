<?php
/**
 * Script procesador de cola de correos
 * 
 * Este script procesa correos pendientes de la cola y los envía mediante SMTP.
 * Debe ejecutarse periódicamente mediante cron job o tarea programada.
 * 
 * Uso:
 *   php process-mail-queue.php [--batch=N] [--env=ENV]
 * 
 * Parámetros:
 *   --batch=N    : Cantidad de correos a procesar por ejecución (default: 200)
 *   --env=ENV    : Entorno (PROD, TEST, LOCAL) - default: PROD
 */

// Configurar para ejecución CLI
if (php_sapi_name() !== 'cli') {
    die("Este script solo puede ejecutarse desde línea de comandos.\n");
}

// Establecer variables $_SERVER necesarias para CLI
if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) {
    // DOCUMENT_ROOT debe apuntar al directorio padre de app-sintia (htdocs)
    // Si estamos en C:\xampp\htdocs\app-sintia, DOCUMENT_ROOT = C:\xampp\htdocs
    $scriptDir = __DIR__;
    // Normalizar separadores de ruta
    $scriptDir = str_replace('\\', '/', $scriptDir);
    // Si termina en /app-sintia, subir un nivel
    if (basename($scriptDir) === 'app-sintia') {
        $_SERVER['DOCUMENT_ROOT'] = dirname($scriptDir);
    } else {
        $_SERVER['DOCUMENT_ROOT'] = $scriptDir;
    }
    // Asegurar formato Windows si es necesario
    if (DIRECTORY_SEPARATOR === '\\') {
        $_SERVER['DOCUMENT_ROOT'] = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
    }
}

// Cambiar al directorio del script para asegurar rutas relativas correctas
chdir(__DIR__);

// Crear carpeta de logs si no existe
$logsDir = __DIR__ . '/config-general/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = 80;
}
if (!isset($_SERVER['HTTPS'])) {
    $_SERVER['HTTPS'] = 'off';
}

// Parsear argumentos
$batchSize = 200;
$entorno = 'PROD';

// Primero buscar --env para establecerlo antes de incluir constantes
foreach ($argv as $arg) {
    if (strpos($arg, '--env=') === 0) {
        $entorno = strtoupper(trim(substr($arg, 6)));
        break;
    }
}

// Validar que el entorno sea válido
if (!in_array($entorno, ['PROD', 'TEST', 'LOCAL'])) {
    $entorno = 'PROD';
}

// Establecer entorno como primer argumento para que constantes.php lo detecte
// IMPORTANTE: Establecer tanto en $argv como en $_SERVER['argv'] y también como variable global
$argv[1] = $entorno;
if (isset($_SERVER['argv'])) {
    $_SERVER['argv'][1] = $entorno;
}
// Establecer también como variable de entorno para asegurar que se lea correctamente
putenv("ENV={$entorno}");

// Incluir constantes y clases (debe ir después de establecer $argv[1])
require_once(__DIR__ . "/config-general/constantes.php");

// Verificar que el entorno se estableció correctamente
if (defined('ENVIROMENT')) {
    echo "Entorno detectado: " . ENVIROMENT . "\n";
    echo "Método de email: " . (defined('EMAIL_METHOD') ? EMAIL_METHOD : 'NO DEFINIDO') . "\n";
} else {
    echo "ADVERTENCIA: ENVIROMENT no está definido. Forzando PROD.\n";
    define('ENVIROMENT', 'PROD');
    define('EMAIL_METHOD', 'NORMAL');
}

// Ahora parsear --batch
foreach ($argv as $arg) {
    if (strpos($arg, '--batch=') === 0) {
        $batchSize = (int)substr($arg, 8);
        if ($batchSize <= 0) {
            $batchSize = 200;
        }
    }
}
require_once(ROOT_PATH . "/main-app/class/EnviarEmail.php");
require_once(ROOT_PATH . "/main-app/class/App/Comunicativo/MailQueue.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configurar timezone
date_default_timezone_set("America/New_York");

echo "[" . date('Y-m-d H:i:s') . "] Iniciando procesamiento de cola de correos...\n";
echo "Lote: {$batchSize} correos\n";
echo "Entorno solicitado: {$entorno}\n";
if (defined('ENVIROMENT')) {
    echo "Entorno detectado: " . ENVIROMENT . "\n";
}
if (defined('EMAIL_METHOD')) {
    echo "Método de email: " . EMAIL_METHOD . "\n";
}
echo "\n";

try {
    // Reclamar correos pendientes
    $correos = MailQueue::reclamarPendientes($batchSize);
    
    if (empty($correos)) {
        echo "No hay correos pendientes en la cola.\n";
        exit(0);
    }
    
    $enviados = 0;
    $errores = 0;
    $reagendados = 0;
    
    foreach ($correos as $correo) {
        $id = (int)$correo['mq_id'];
        $destinatario = $correo['mq_destinatario'];
        $asunto = $correo['mq_asunto'];
        
        echo "[ID: {$id}] Procesando: {$destinatario} - {$asunto}\n";
        
        try {
            $mail = new PHPMailer(true);
            
            // Configuración SMTP
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            
            // Verificar método de email (solo en el primer correo)
            if ($enviados === 0 && $errores === 0) {
                echo "  [DEBUG] EMAIL_METHOD definido: " . (defined('EMAIL_METHOD') ? EMAIL_METHOD : 'NO DEFINIDO') . "\n";
                echo "  [DEBUG] ENVIROMENT definido: " . (defined('ENVIROMENT') ? ENVIROMENT : 'NO DEFINIDO') . "\n";
            }
            
            if (EMAIL_METHOD == 'MAILPIT') {
                $mail->Host = EMAIL_SERVER_MAILPIT;
                $mail->SMTPAuth = SMTPAUTH_MAILPIT;
                $mail->Username = EMAIL_USER_MAILPIT;
                $mail->Password = EMAIL_PASSWORD_MAILPIT;
                $mail->SMTPSecure = SMTPSECURE_MAILPIT;
                $mail->Port = PORT_SEND_EMAIL_MAILPIT;
            } else {
                $mail->Host = EMAIL_SERVER;
                $mail->SMTPAuth = SMTPAUTH;
                $mail->Username = EMAIL_USER;
                $mail->Password = EMAIL_PASSWORD;
                $mail->SMTPSecure = SMTPSECURE;
                $mail->Port = PORT_SEND_EMAIL;
            }
            
            // Remitente
            $remitente = $correo['mq_remitente'] ?? EMAIL_SENDER;
            $mail->setFrom($remitente, NAME_SENDER);
            
            // Destinatario principal
            $mail->addAddress($destinatario);
            
            // CC si existe
            if (!empty($correo['mq_destinatario_cc'])) {
                $ccList = explode(',', $correo['mq_destinatario_cc']);
                foreach ($ccList as $cc) {
                    $cc = trim($cc);
                    if (!empty($cc) && EnviarEmail::validarEmail($cc)) {
                        $mail->addCC($cc);
                    }
                }
            }
            
            // BCC si existe
            if (!empty($correo['mq_destinatario_bcc'])) {
                $bccList = explode(',', $correo['mq_destinatario_bcc']);
                foreach ($bccList as $bcc) {
                    $bcc = trim($bcc);
                    if (!empty($bcc) && EnviarEmail::validarEmail($bcc)) {
                        $mail->addBCC($bcc);
                    }
                }
            }
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $correo['mq_contenido_html'];
            $mail->CharSet = 'UTF-8';
            
            if (!empty($correo['mq_contenido_texto'])) {
                $mail->AltBody = $correo['mq_contenido_texto'];
            }
            
            // Adjuntos si existen
            if (!empty($correo['mq_adjuntos'])) {
                $adjuntos = json_decode($correo['mq_adjuntos'], true);
                if (is_array($adjuntos)) {
                    foreach ($adjuntos as $adjunto) {
                        if (is_string($adjunto) && file_exists($adjunto)) {
                            $mail->addAttachment($adjunto);
                        }
                    }
                }
            }
            
            // Intentar envío
            $mail->send();
            
            // Marcar como enviado
            MailQueue::marcarEnviado($id, "Enviado exitosamente");
            
            // Registrar en historial
            $dataHistorial = [
                'institucion_id' => $correo['mq_institucion_id'],
                'usuario_id' => $correo['mq_usuario_id']
            ];
            EnviarEmail::enviarReporte(
                $correo['mq_institucion_id'],
                $mail,
                $remitente,
                $destinatario,
                $asunto,
                $correo['mq_contenido_html'],
                EnviarEmail::ESTADO_EMAIL_ENVIADO,
                ''
            );
            
            $enviados++;
            echo "  ✓ Enviado exitosamente\n";
            
        } catch (Exception $e) {
            $intentos = (int)$correo['mq_intentos'];
            $maxIntentos = (int)$correo['mq_max_intentos'];
            $mensajeError = $e->getMessage();
            
            echo "  ✗ Error: {$mensajeError}\n";
            
            // Verificar si es un error temporal (SMTP temporal)
            $esErrorTemporal = (
                stripos($mensajeError, 'temporary') !== false ||
                stripos($mensajeError, 'try later') !== false ||
                stripos($mensajeError, 'timeout') !== false ||
                stripos($mensajeError, 'connection') !== false
            );
            
            if ($esErrorTemporal && ($intentos + 1) < $maxIntentos) {
                // Reagendar con backoff exponencial (minutos)
                $minutosEspera = min(60, pow(2, $intentos) * 5); // 5, 10, 20, 40, 60 minutos
                $fechaReagendar = date('Y-m-d H:i:s', strtotime("+{$minutosEspera} minutes"));
                
                MailQueue::reagendar($id, $fechaReagendar, "Error temporal: {$mensajeError}. Reagendado para {$fechaReagendar}");
                $reagendados++;
                echo "  ↻ Reagendado para {$fechaReagendar}\n";
            } else {
                // Error definitivo o máximo de intentos alcanzado
                MailQueue::marcarError($id, $mensajeError);
                
                // Registrar en historial
                EnviarEmail::enviarReporte(
                    $correo['mq_institucion_id'],
                    $mail ?? null,
                    $remitente ?? EMAIL_SENDER,
                    $destinatario,
                    $asunto,
                    $correo['mq_contenido_html'] ?? '',
                    EnviarEmail::ESTADO_EMAIL_ERROR,
                    $mensajeError
                );
                
                $errores++;
                echo "  ✗ Marcado como error definitivo\n";
            }
        }
        
        // Pequeña pausa para evitar saturación SMTP
        usleep(100000); // 0.1 segundos
    }
    
    echo "\n";
    echo "========================================\n";
    echo "Resumen:\n";
    echo "  Enviados: {$enviados}\n";
    echo "  Reagendados: {$reagendados}\n";
    echo "  Errores: {$errores}\n";
    echo "  Total procesados: " . count($correos) . "\n";
    echo "========================================\n";
    
} catch (Exception $e) {
    echo "\nERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

exit(0);

