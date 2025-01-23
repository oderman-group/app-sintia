<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");

require_once ROOT_PATH . "/vendor/twilio/sdk/src/Twilio/autoload.php";

use Twilio\Rest\Client;

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
     * @return void
     */
    public function enviarSms(array $data) {
        $twilio = new Client(self::TWILIO_ACCOUNT_SID, self::TWILIO_AUTH_TOKEN);

        $message = $twilio->messages->create(
            self::PREFIX_COL.$data['telefono'],
            [
                "body" => $data['mensaje'],
                "from" => TWILIO_FROM_PHONE_NUMBER,
            ]
        );

        // print $message->body . " - ". $message->status;
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