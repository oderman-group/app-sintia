<?php
class Socket {
    public static function socket_emit($event, $data) {
        // Analizar la URL para obtener host, puerto y esquema
        $parsedUrl = parse_url(URL_API);

        $host = $parsedUrl['host'];
        $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : 80;
        $scheme = $parsedUrl['scheme']; // Puede ser ws o wss

        // Preparar conexión, manejando conexiones seguras (wss://)
        $protocol = $scheme === 'wss' ? 'ssl://' : '';
        $context = null;

        if ($scheme === 'wss') {
            $context = stream_context_create(['ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]]);
        }

        $socket = stream_socket_client(
            $protocol . $host . ':' . $port,
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            error_log("Error al conectar al servidor WebSocket: $errstr ($errno)");
            return false;
        }

        // Encabezados para la negociación WebSocket
        $headers = [
            "GET /socket.io/?EIO=4&transport=websocket HTTP/1.1",
            "Host: $host",
            "Connection: Upgrade",
            "Upgrade: websocket",
            "Sec-WebSocket-Key: " . base64_encode(random_bytes(16)),
            "Sec-WebSocket-Version: 13",
            "\r\n"
        ];

        // Enviar encabezados
        fwrite($socket, implode("\r\n", $headers));

        // Leer la respuesta del servidor
        $response = fread($socket, 1024);
        error_log("Respuesta del servidor: $response");

        if (strpos($response, "101 Switching Protocols") === false) {
            error_log("Error: No se pudo establecer la conexión WebSocket");
            fclose($socket);
            return false;
        }

        // Preparar el payload
        $payload = json_encode([
            'event' => $event,
            'data' => $data
        ]);

        // Enviar el mensaje codificado
        fwrite($socket, self::encode_websocket_message($payload));

        // Cerrar la conexión
        fclose($socket);
        return true;
    }

    // Función para codificar mensajes en formato WebSocket
    public static function encode_websocket_message($payload) {
        $frameHead = [];
        $payloadLength = strlen($payload);

        if ($payloadLength <= 125) {
            $frameHead[0] = 129; // FIN + texto
            $frameHead[1] = $payloadLength;
        } elseif ($payloadLength >= 126 && $payloadLength <= 65535) {
            $frameHead[0] = 129; // FIN + texto
            $frameHead[1] = 126;
            $frameHead[2] = ($payloadLength >> 8) & 255;
            $frameHead[3] = $payloadLength & 255;
        } else {
            $frameHead[0] = 129; // FIN + texto
            $frameHead[1] = 127;
            $frameHead[2] = ($payloadLength >> 56) & 255;
            $frameHead[3] = ($payloadLength >> 48) & 255;
            $frameHead[4] = ($payloadLength >> 40) & 255;
            $frameHead[5] = ($payloadLength >> 32) & 255;
            $frameHead[6] = ($payloadLength >> 24) & 255;
            $frameHead[7] = ($payloadLength >> 16) & 255;
            $frameHead[8] = ($payloadLength >> 8) & 255;
            $frameHead[9] = $payloadLength & 255;
        }

        // Convertir encabezado en string
        $frame = '';
        foreach ($frameHead as $head) {
            $frame .= chr($head);
        }

        // Agregar el payload
        $frame .= $payload;

        return $frame;
    }
}