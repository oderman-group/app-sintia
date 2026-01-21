<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");

// Twilio SDK se carga solo cuando se necesita (lazy loading)
// para evitar errores si no está instalado y solo se usa email

class Sms {

    private static bool $twilioLoaded = false;
    
    /**
     * Carga el SDK de Twilio solo cuando se necesita
     * @throws Exception Si el SDK de Twilio no está instalado
     */
    private static function loadTwilio(): void {
        if (self::$twilioLoaded) {
            return;
        }
        
        $twilioAutoloadPath = ROOT_PATH . "/vendor/twilio/sdk/src/Twilio/autoload.php";
        
        if (!file_exists($twilioAutoloadPath)) {
            throw new Exception(
                "El SDK de Twilio no está instalado. " .
                "Para usar SMS o WhatsApp, ejecuta: composer require twilio/sdk"
            );
        }
        
        require_once $twilioAutoloadPath;
        self::$twilioLoaded = true;
    }
    
    /**
     * Obtiene las credenciales de Twilio de forma segura
     */
    private static function getAccountSid(): string {
        return defined('TWILIO_ACCOUNT_SID') ? TWILIO_ACCOUNT_SID : '';
    }
    
    private static function getAuthToken(): string {
        return defined('TWILIO_AUTH_TOKEN') ? TWILIO_AUTH_TOKEN : '';
    }

    public const PREFIX_COL = "+57";
    
    /**
     * Obtiene el número de teléfono de Twilio según el environment
     * @return string Número de teléfono para SMS
     */
    private static function getTwilioFromNumber(): string {
        // Verificar si ENVIROMENT está definido
        if (defined('ENVIROMENT')) {
            if (ENVIROMENT === 'PROD') {
                return defined('TWILIO_FROM_PHONE_NUMBER_PROD') ? TWILIO_FROM_PHONE_NUMBER_PROD : TWILIO_FROM_PHONE_NUMBER;
            } else {
                // LOCAL o TEST - usar número de prueba
                return defined('TWILIO_FROM_PHONE_NUMBER_TEST') ? TWILIO_FROM_PHONE_NUMBER_TEST : '+15005550006';
            }
        }
        // Si no está definido, usar número de prueba por defecto
        return defined('TWILIO_FROM_PHONE_NUMBER_TEST') ? TWILIO_FROM_PHONE_NUMBER_TEST : '+15005550006';
    }
    
    /**
     * Obtiene el número de WhatsApp de Twilio según el environment
     * @return string Número de WhatsApp
     */
    private static function getTwilioWhatsAppFromNumber(): string {
        // Verificar si ENVIROMENT está definido
        if (defined('ENVIROMENT')) {
            if (ENVIROMENT === 'PROD') {
                return defined('TWILIO_WHATSAPP_FROM_NUMBER_PROD') ? TWILIO_WHATSAPP_FROM_NUMBER_PROD : TWILIO_WHATSAPP_FROM_NUMBER;
            } else {
                // LOCAL o TEST - usar Sandbox
                return defined('TWILIO_WHATSAPP_FROM_NUMBER_TEST') ? TWILIO_WHATSAPP_FROM_NUMBER_TEST : 'whatsapp:+14155238886';
            }
        }
        // Si no está definido, usar Sandbox por defecto
        return defined('TWILIO_WHATSAPP_FROM_NUMBER_TEST') ? TWILIO_WHATSAPP_FROM_NUMBER_TEST : 'whatsapp:+14155238886';
    }

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
            // Cargar Twilio SDK solo cuando se necesita
            self::loadTwilio();
            
            // Validar que las credenciales estén definidas
            $accountSid = self::getAccountSid();
            $authToken = self::getAuthToken();
            
            if (empty($accountSid) || empty($authToken)) {
                throw new Exception("Las credenciales de Twilio no están configuradas correctamente.");
            }

            // Obtener el número de WhatsApp según el environment
            $whatsappFromNumber = self::getTwilioWhatsAppFromNumber();
            
            if (empty($whatsappFromNumber)) {
                throw new Exception("El número de WhatsApp de Twilio no está configurado. Verifica TWILIO_WHATSAPP_FROM_NUMBER en sensitive.php");
            }

            $twilio = new \Twilio\Rest\Client($accountSid, $authToken);

            // Formatear el número de destino: agregar prefijo whatsapp: y código de país
            $numeroDestino = 'whatsapp:' . self::PREFIX_COL . $data['telefono'];

            // Preparar opciones del mensaje
            $opcionesMensaje = [
                "from" => $whatsappFromNumber,
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
        } catch (\Twilio\Exceptions\RestException $e) {
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
     * @return object Retorna el objeto message de Twilio
     */
    public function enviarSms(array $data) {
        try {
            // Cargar Twilio SDK solo cuando se necesita
            self::loadTwilio();
            
            // Validar que las credenciales estén definidas
            $accountSid = self::getAccountSid();
            $authToken = self::getAuthToken();
            
            if (empty($accountSid) || empty($authToken)) {
                throw new Exception("Las credenciales de Twilio no están configuradas correctamente.");
            }

            $twilio = new \Twilio\Rest\Client($accountSid, $authToken);
            
            // Obtener el número de teléfono según el environment
            $smsFromNumber = self::getTwilioFromNumber();
            
            // Determinar el número de destino y el mensaje
            $numeroDestino = $data['telefono'];
            $mensajeOriginal = $data['mensaje'];
            $numeroRealDestino = $numeroDestino; // Guardar el número real para el mensaje
            
            // Si no es PROD, redirigir a número de prueba
            if (defined('ENVIROMENT') && ENVIROMENT !== 'PROD') {
                if (defined('TWILIO_SMS_TEST_DESTINATION') && !empty(TWILIO_SMS_TEST_DESTINATION)) {
                    $numeroDestino = TWILIO_SMS_TEST_DESTINATION;
                    // Agregar nota al mensaje indicando el destinatario original
                    $mensajeOriginal = $data['mensaje'] . "\n\n[PRUEBA - Mensaje desviado. Destinatario original: +57" . $numeroRealDestino . "]";
                }
            }

            $message = $twilio->messages->create(
                self::PREFIX_COL.$numeroDestino,
                [
                    "body" => $mensajeOriginal,
                    "from" => $smsFromNumber,
                ]
            );

            // Retornar el objeto message para obtener el SID
            return $message;
        } catch (\Twilio\Exceptions\RestException $e) {
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
        // Cargar Twilio SDK
        self::loadTwilio();
        
        $twilio = new \Twilio\Rest\Client(self::getAccountSid(), self::getAuthToken());

        $messages = $twilio->messages->read(
            [
                "dateSent" => new \DateTime("2024-10-07T00:00:00Z"),
            ], 20);

        foreach ($messages as $record) {
            echo $record->body ." - ".$record->to." - ".$record->status."<br>";
        }
    }

}