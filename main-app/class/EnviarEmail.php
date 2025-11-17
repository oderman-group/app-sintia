<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/App/Comunicativo/MailQueue.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require ROOT_PATH.'/librerias/phpmailer/Exception.php';
require ROOT_PATH.'/librerias/phpmailer/PHPMailer.php';
require ROOT_PATH.'/librerias/phpmailer/SMTP.php';

class EnviarEmail {

    public const ESTADO_EMAIL_ENVIADO = 'enviado';
    public const ESTADO_EMAIL_ERROR   = 'error';

    /**
     * Este función envía un correo electrónico
     * 
     * @param array $data
     * @param string $asunto
     * @param string $bodyTemplateRoute
     * @param string|null $body
     * @param array|null $archivos
     * @param bool $enviarInmediato Si es true, envía inmediatamente sin usar cola (útil para códigos de verificación)
     * 
     * @return void
     */
    public static function enviar($data, $asunto, $bodyTemplateRoute, $body, $archivos, $enviarInmediato = false): void
    {
        try {
            // Procesar template si existe
            if(!is_null($bodyTemplateRoute)){
                ob_start();
                include($bodyTemplateRoute);
                $body = ob_get_clean();
            }

            $correrocopia         = 'soporte@plataformasintia.com';
            $destinatario         = $data['usuario_email'] ?? '';
            $destinatario2        = empty($data['usuario2_email'])?null:$data['usuario2_email'];
            $destinatario3        = empty($data['usuario3_email'])?null:$data['usuario3_email'];
            
            // Validar emails
            $validarRemitente     = self::validarEmail(EMAIL_SENDER);
            $validarDestinatario  = self::validarEmail($destinatario);
            $validarDestinatario2 = is_null($destinatario2) ? true : self::validarEmail($destinatario2);
            $validarDestinatario3 = is_null($destinatario3) ? true : self::validarEmail($destinatario3);
            $validarcopia         = self::validarEmail($correrocopia);

            if (!$validarRemitente || !$validarDestinatario || !$validarDestinatario2 || !$validarDestinatario3 || !$validarcopia) {
                $errorMsg = '';
                if (!$validarRemitente) {
                    $errorMsg = 'Error remitente: ' . EMAIL_SENDER;
                    self::mensajeError(EMAIL_SENDER);
                }
                if (!$validarDestinatario) {
                    $errorMsg = 'Error destinatario: ' . $destinatario;
                    self::mensajeError($destinatario);
                }
                if (!$validarDestinatario2) {
                    $errorMsg = 'Error destinatario 2: ' . $destinatario2;
                    self::mensajeError($destinatario2);
                }
                if (!$validarDestinatario3) {
                    $errorMsg = 'Error destinatario 3: ' . $destinatario3;
                    self::mensajeError($destinatario3);
                }
                if (!$validarcopia) {
                    $errorMsg = 'Error destinatario copia: ' . $correrocopia;
                    self::mensajeError($correrocopia);
                }
                // Registrar error en historial (sin PHPMailer)
                self::enviarReporteSinMail($data['institucion_id'] ?? null, EMAIL_SENDER, $destinatario, $asunto, $body ?? '', $archivos, self::ESTADO_EMAIL_ERROR, $errorMsg);
                return;
            }

            // Si se requiere envío inmediato, enviar directamente
            if ($enviarInmediato) {
                self::enviarDirecto($data, $asunto, $body, $archivos, $destinatario, $destinatario2, $destinatario3, $correrocopia);
                return;
            }

            // Construir lista de destinatarios CC (soporte siempre va)
            $destinatariosCC = [$correrocopia];
            if (!is_null($destinatario2)) {
                $destinatariosCC[] = $destinatario2;
            }
            if (!is_null($destinatario3)) {
                $destinatariosCC[] = $destinatario3;
            }

            // Agregar a la cola (comportamiento por defecto)
            $payload = [
                'destinatario' => $destinatario,
                'destinatario_cc' => $destinatariosCC,
                'asunto' => $asunto,
                'contenido_html' => $body ?? '',
                'adjuntos' => $archivos,
                'remitente' => EMAIL_SENDER,
                'usuario_id' => $data['usuario_id'] ?? null,
                'institucion_id' => $data['institucion_id'] ?? null,
                'prioridad' => 2, // Prioridad media por defecto
            ];

            MailQueue::enqueue($payload);

        } catch (Exception $e) {
            error_log("Error al procesar correo: " . $e->getMessage());
            // Registrar error en historial
            self::enviarReporteSinMail($data['institucion_id'] ?? null, EMAIL_SENDER, $destinatario ?? '', $asunto, $body ?? '', $archivos, self::ESTADO_EMAIL_ERROR, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envía un correo directamente usando PHPMailer (sin cola)
     * 
     * @param array $data
     * @param string $asunto
     * @param string $body
     * @param array|null $archivos
     * @param string $destinatario
     * @param string|null $destinatario2
     * @param string|null $destinatario3
     * @param string $correrocopia
     * 
     * @return void
     */
    private static function enviarDirecto($data, $asunto, $body, $archivos, $destinatario, $destinatario2, $destinatario3, $correrocopia): void
    {
        global $mail;

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            if (EMAIL_METHOD == 'MAILPIT') {
                $mail->Host       = EMAIL_SERVER_MAILPIT;
                $mail->SMTPAuth   = SMTPAUTH_MAILPIT;
                $mail->Username   = EMAIL_USER_MAILPIT;
                $mail->Password   = EMAIL_PASSWORD_MAILPIT;
                $mail->SMTPSecure = SMTPSECURE_MAILPIT;
                $mail->Port       = PORT_SEND_EMAIL_MAILPIT;
            } else {
                $mail->Host       = EMAIL_SERVER;
                $mail->SMTPAuth   = SMTPAUTH;
                $mail->Username   = EMAIL_USER;
                $mail->Password   = EMAIL_PASSWORD;
                $mail->SMTPSecure = SMTPSECURE;
                $mail->Port       = PORT_SEND_EMAIL;
            }

            //Remitente
            $mail->setFrom(EMAIL_SENDER, NAME_SENDER);

            //Destinatarios
            $mail->addAddress($correrocopia, 'Soporte Plataforma SINTIA');
            $nombreDestinatario = $data['usuario_nombre'] ?? $data['nombre_completo'] ?? '';
            $mail->addAddress($destinatario, $nombreDestinatario);

            if (!is_null($destinatario2)) {
                $mail->addAddress($destinatario2, $data['usuario2_nombre'] ?? '');
            }

            if (!is_null($destinatario3)) {
                $mail->addAddress($destinatario3, $data['usuario3_nombre'] ?? '');
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $body;
            $mail->CharSet = 'UTF-8';

            if ($archivos && !empty($archivos)) {
                foreach ($archivos as $valor) {
                    if (is_string($valor) && file_exists($valor)) {
                        $mail->AddAttachment($valor);
                    }
                }
            }

            $mail->send();
            self::enviarReporte($data['institucion_id'] ?? null, $mail, EMAIL_SENDER, $destinatario, $asunto, $body, self::ESTADO_EMAIL_ENVIADO, '');

        } catch (Exception $e) {
            self::enviarReporte($data['institucion_id'] ?? null, $mail, EMAIL_SENDER, $destinatario, $asunto, $body, self::ESTADO_EMAIL_ERROR, $e->getMessage());
            error_log("Error al enviar correo inmediato: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Este función valida un correo electrónico que tenga la estructura correcta emplo@dominio.com
     * 
     * @param string $email
     * 
     * @return boolean
     */
    public static function validarEmail($email) 
    {
        // Validar que el email no sea nulo o vacío
        if (empty($email) || !is_string($email)) {
            return false;
        }
        
        $matches = null;
        // Expresion regular
        $regex = "/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/";
        return (1 === preg_match($regex, $email, $matches));
    }

    /**
     * Este función envia el mensaje de error a la pagina de donde se llamo
     * 
     * @param string $email
     * 
     * @return void
     */
    private static function mensajeError($email) 
    {
        $msj              = 'Correo electrónico inválido. El correo <strong>"'.htmlspecialchars($email).'"</strong> no cumple con la estructura de un correo válido. Por favor, verifique que el formato sea correcto (ejemplo: usuario@dominio.com)';
        $url              = $_SERVER["HTTP_REFERER"];
        $pos              = strpos($url, "?");
        $simbolConcatenar = $pos === false ? "?" : "&";
        $url              = $url.$simbolConcatenar.'error=ER_DT_15&msj='.base64_encode($msj);

        echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';
        exit();
    }

    /**
     * Este función envia el mensaje a la tabla de historial de correos enviados 
     * 
     * @param string|null $institucion
     * @param Object $mail
     * @param string $remitente
     * @param string $destinatario
     * @param string $asunto
     * @param string $body
     * @param string $estado
     * @param string $descripcion
     * 
     * @return void
     */
    public static function enviarReporte($institucion, $mail, $remitente, $destinatario, $asunto, $body, $estado, $descripcion) {
        global $baseDatosServicios;

        // Migrado a PDO - Soporte completo UTF-8 con emojis
        try {
            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            $conexionPDO = Conexion::newConnection('PDO');
            $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Asegurar charset UTF-8 para soporte de emojis
            $conexionPDO->exec("SET NAMES utf8mb4");
            
            $adjunto = ($mail !== null && method_exists($mail, 'attachmentExists')) ? $mail->attachmentExists() : 'NO';
            $referencia = (php_sapi_name() !== 'cli' && isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : '';
            
            $sql = "INSERT INTO ".$baseDatosServicios.".historial_correos_enviados(
                hisco_fecha,
                hisco_remitente,
                hisco_destinatario,
                hisco_asunto,
                hisco_contenido,
                hisco_adjunto,
                hisco_archivo_salida,
                hisco_estado,
                hisco_descripcion_error,
                hisco_id_institucion
            ) VALUES (now(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $remitente, PDO::PARAM_STR);
            $stmt->bindParam(2, $destinatario, PDO::PARAM_STR);
            $stmt->bindParam(3, $asunto, PDO::PARAM_STR);
            $stmt->bindParam(4, $body, PDO::PARAM_STR);
            $stmt->bindParam(5, $adjunto, PDO::PARAM_STR);
            $stmt->bindParam(6, $referencia, PDO::PARAM_STR);
            $stmt->bindParam(7, $estado, PDO::PARAM_STR);
            $stmt->bindParam(8, $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(9, $institucion, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en enviarReporte: " . $e->getMessage());
            if (php_sapi_name() !== 'cli') {
                include("../compartido/error-catch-to-report.php");
            }
        }
    }

    /**
     * Versión de enviarReporte sin objeto PHPMailer (para errores de validación)
     * 
     * @param string|null $institucion
     * @param string $remitente
     * @param string $destinatario
     * @param string $asunto
     * @param string $body
     * @param array|null $archivos
     * @param string $estado
     * @param string $descripcion
     * 
     * @return void
     */
    private static function enviarReporteSinMail($institucion, $remitente, $destinatario, $asunto, $body, $archivos, $estado, $descripcion) {
        global $baseDatosServicios;

        try {
            require_once(ROOT_PATH."/main-app/class/Conexion.php");
            $conexionPDO = Conexion::newConnection('PDO');
            $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexionPDO->exec("SET NAMES utf8mb4");
            
            $adjunto = (is_array($archivos) && !empty($archivos)) ? 'SI' : 'NO';
            $referencia = $_SERVER["HTTP_REFERER"] ?? '';
            
            $sql = "INSERT INTO ".$baseDatosServicios.".historial_correos_enviados(
                hisco_fecha,
                hisco_remitente,
                hisco_destinatario,
                hisco_asunto,
                hisco_contenido,
                hisco_adjunto,
                hisco_archivo_salida,
                hisco_estado,
                hisco_descripcion_error,
                hisco_id_institucion
            ) VALUES (now(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $remitente, PDO::PARAM_STR);
            $stmt->bindParam(2, $destinatario, PDO::PARAM_STR);
            $stmt->bindParam(3, $asunto, PDO::PARAM_STR);
            $stmt->bindParam(4, $body, PDO::PARAM_STR);
            $stmt->bindParam(5, $adjunto, PDO::PARAM_STR);
            $stmt->bindParam(6, $referencia, PDO::PARAM_STR);
            $stmt->bindParam(7, $estado, PDO::PARAM_STR);
            $stmt->bindParam(8, $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(9, $institucion, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en enviarReporteSinMail: " . $e->getMessage());
        }
    }

}