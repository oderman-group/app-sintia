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

            // print $message->body . " - ". $message->status;
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