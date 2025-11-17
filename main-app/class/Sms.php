<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");

require_once ROOT_PATH . "/vendor/twilio/sdk/src/Twilio/autoload.php";

use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

class Sms {

    private const TWILIO_ACCOUNT_SID = TWILIO_ACCOUNT_SID;
    private const TWILIO_AUTH_TOKEN  = TWILIO_AUTH_TOKEN;

    public const PREFIX_COL = "+57";

    /**
     * Sends a WhatsApp message using the Twilio API.
     *
     * @param array $data An associative array containing the following keys:
     *                    - 'telefono': The recipient's phone number (without whatsapp: prefix, will be added automatically).
     *                    - 'mensaje': The message to be sent.
     *
     * @throws Exception Si hay un error al enviar el WhatsApp
     * @return object|null Retorna el objeto message de Twilio o null en caso de error
     */
    public function enviarWhatsApp(array $data) {
        try {
            // Validar que las credenciales estén definidas
            if (empty(self::TWILIO_ACCOUNT_SID) || empty(self::TWILIO_AUTH_TOKEN)) {
                throw new Exception("Las credenciales de Twilio no están configuradas correctamente.");
            }

            // Validar que el número de WhatsApp esté configurado
            if (empty(TWILIO_WHATSAPP_FROM_NUMBER)) {
                throw new Exception("El número de WhatsApp de Twilio no está configurado. Verifica TWILIO_WHATSAPP_FROM_NUMBER en sensitive.php");
            }

            $twilio = new Client(self::TWILIO_ACCOUNT_SID, self::TWILIO_AUTH_TOKEN);

            // Formatear el número de destino: agregar prefijo whatsapp: y código de país
            $numeroDestino = 'whatsapp:' . self::PREFIX_COL . $data['telefono'];

            // Preparar opciones del mensaje
            $opcionesMensaje = [
                "from" => TWILIO_WHATSAPP_FROM_NUMBER,
            ];

            // Si hay un template configurado, usarlo (para mensajes iniciados por el negocio en producción)
            if (!empty(TWILIO_WHATSAPP_TEMPLATE_SID)) {
                // Usar template con el mensaje como variable
                $opcionesMensaje["contentSid"] = TWILIO_WHATSAPP_TEMPLATE_SID;
                // Si el template tiene variables ({{1}}, {{2}}, etc.), puedes pasarlas aquí
                // Ejemplo: $opcionesMensaje["contentVariables"] = json_encode(["1" => $data['mensaje']]);
                // Por ahora, usamos body como fallback si el template falla
                $opcionesMensaje["body"] = $data['mensaje'];
            } else {
                // Intentar mensaje libre (funciona en Sandbox o ventana de 24h)
                $opcionesMensaje["body"] = $data['mensaje'];
            }

            // Enviar mensaje por WhatsApp
            $message = $twilio->messages->create(
                $numeroDestino,
                $opcionesMensaje
            );

            return $message;
        } catch (RestException $e) {
            // Error específico de Twilio API
            $mensajeError = "Error de Twilio API (WhatsApp): " . $e->getMessage();
            
            // Agregar información adicional para errores comunes
            $errorCode = $e->getCode();
            if ($errorCode == 401) {
                $mensajeError .= " (Código 401: Credenciales incorrectas. Verifica TWILIO_ACCOUNT_SID y TWILIO_AUTH_TOKEN en sensitive.php)";
            } elseif ($errorCode == 21211) {
                $mensajeError .= " (Código 21211: Número de destino inválido para WhatsApp. Verifica que el número tenga formato correcto)";
            } elseif ($errorCode == 21608) {
                $mensajeError .= " (Código 21608: Número no autorizado para WhatsApp. El destinatario debe unirse al Sandbox de Twilio primero)";
            } elseif ($errorCode == 21212) {
                $mensajeError .= " (Código 21212: El número remitente no está configurado para WhatsApp. Verifica TWILIO_WHATSAPP_FROM_NUMBER)";
            } elseif ($errorCode == 63016) {
                $mensajeError .= " (Código 63016: Fuera de la ventana permitida. En producción, los mensajes iniciados por el negocio requieren Message Templates aprobados por WhatsApp. Necesitas crear un template en Twilio Console → Content Templates)";
            }
            
            // También verificar el mensaje de error para detectar el código 63016
            if (strpos($e->getMessage(), '63016') !== false || strpos($e->getMessage(), 'outside the allowed window') !== false) {
                $mensajeError = "Error 63016: WhatsApp requiere Message Templates para mensajes iniciados por el negocio en producción. " .
                              "Ve a Twilio Console → Messaging → Content Templates para crear un template aprobado. " .
                              "Mensaje original: " . $e->getMessage();
            }
            
            throw new Exception($mensajeError, $e->getCode(), $e);
        } catch (Exception $e) {
            // Cualquier otro error
            throw new Exception("Error al enviar WhatsApp: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Sends an SMS message using the Twilio API.
     *
     * @param array $data An associative array containing the following keys:
     *                    - 'telefono': The recipient's phone number.
     *                    - 'mensaje': The message to be sent.
     *
     * @throws Exception Si hay un error al enviar el SMS
     * @return void
     */
    public function enviarSms(array $data) {
        try {
            // Validar que las credenciales estén definidas
            if (empty(self::TWILIO_ACCOUNT_SID) || empty(self::TWILIO_AUTH_TOKEN)) {
                throw new Exception("Las credenciales de Twilio no están configuradas correctamente.");
            }

            $twilio = new Client(self::TWILIO_ACCOUNT_SID, self::TWILIO_AUTH_TOKEN);

            $message = $twilio->messages->create(
                self::PREFIX_COL.$data['telefono'],
                [
                    "body" => $data['mensaje'],
                    "from" => TWILIO_FROM_PHONE_NUMBER,
                ]
            );

            // Retornar el objeto message para obtener el SID
            return $message;
        } catch (RestException $e) {
            // Error específico de Twilio API
            $mensajeError = "Error de Twilio API: " . $e->getMessage();
            
            // Agregar información adicional para errores 401
            if ($e->getStatusCode() == 401) {
                $mensajeError .= " (Código 401: Credenciales incorrectas. Verifica TWILIO_ACCOUNT_SID y TWILIO_AUTH_TOKEN en sensitive.php)";
            }
            
            throw new Exception($mensajeError, $e->getCode(), $e);
        } catch (Exception $e) {
            // Cualquier otro error
            throw new Exception("Error al enviar SMS: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    // TODO: Implementar métodos para listar todos los mensajes enviados
    public function listarMensajes() {

        $twilio = new Client(self::TWILIO_ACCOUNT_SID, self::TWILIO_AUTH_TOKEN);

        $messages = $twilio->messages->read(
            [
                "dateSent" => new \DateTime("2024-10-07T00:00:00Z"),
            ], 20);

        foreach ($messages as $record) {
            echo $record->body ." - ".$record->to." - ".$record->status."<br>";
        }
    }

}