<?php

require_once(ROOT_PATH."/main-app/class/Conexion.php");

class MailQueue
{
    public const ESTADO_PENDIENTE   = 'pendiente';
    public const ESTADO_PROCESANDO = 'procesando';
    public const ESTADO_ENVIADO    = 'enviado';
    public const ESTADO_ERROR      = 'error';
    public const ESTADO_DESCARTADO = 'descartado';

    private const TABLA = BD_ADMIN . ".mail_queue";

    /**
     * Inserta un correo en la cola.
     *
     * @param array $payload
     * @return int ID del registro creado
     */
    public static function enqueue(array $payload): int
    {
        $destinatario = self::normalizarDireccion($payload['destinatario'] ?? null);
        $asunto       = trim((string)($payload['asunto'] ?? ''));
        $html         = (string)($payload['contenido_html'] ?? '');

        if (empty($destinatario)) {
            throw new InvalidArgumentException('Correo destinatario requerido para la cola.');
        }

        if ($asunto === '') {
            throw new InvalidArgumentException('Asunto requerido para la cola.');
        }

        if ($html === '') {
            throw new InvalidArgumentException('Contenido HTML requerido para la cola.');
        }

        $pdo  = self::conexion();
        
        // Obtener el entorno actual (PROD, TEST, LOCAL)
        $entorno = defined('ENVIROMENT') ? ENVIROMENT : 'PROD';
        
        // Verificar si la columna mq_entorno existe
        $tieneCampoEntorno = self::tieneCampoEntorno($pdo);
        
        if ($tieneCampoEntorno) {
            $sql  = "INSERT INTO " . self::TABLA . "
                (mq_estado, mq_prioridad, mq_destinatario, mq_destinatario_cc, mq_destinatario_bcc,
                 mq_asunto, mq_contenido_html, mq_contenido_texto, mq_adjuntos, mq_remitente,
                 mq_respuesta_servidor, mq_intentos, mq_max_intentos, mq_fecha_programada,
                 mq_usuario_id, mq_institucion_id, mq_entorno)
                VALUES
                (:estado, :prioridad, :destino, :cc, :bcc, :asunto, :html, :texto, :adjuntos,
                 :remitente, NULL, 0, :maxIntentos, :fechaProgramada, :usuarioId, :institucionId, :entorno)";
        } else {
            $sql  = "INSERT INTO " . self::TABLA . "
                (mq_estado, mq_prioridad, mq_destinatario, mq_destinatario_cc, mq_destinatario_bcc,
                 mq_asunto, mq_contenido_html, mq_contenido_texto, mq_adjuntos, mq_remitente,
                 mq_respuesta_servidor, mq_intentos, mq_max_intentos, mq_fecha_programada,
                 mq_usuario_id, mq_institucion_id)
                VALUES
                (:estado, :prioridad, :destino, :cc, :bcc, :asunto, :html, :texto, :adjuntos,
                 :remitente, NULL, 0, :maxIntentos, :fechaProgramada, :usuarioId, :institucionId)";
        }

        $stmt = $pdo->prepare($sql);
        $estadoCola = $payload['estado'] ?? self::ESTADO_PENDIENTE;
        $stmt->bindValue(':estado', $estadoCola, PDO::PARAM_STR);
        $stmt->bindValue(':prioridad', (int)($payload['prioridad'] ?? 3), PDO::PARAM_INT);
        $stmt->bindValue(':destino', $destinatario, PDO::PARAM_STR);
        self::bindNullable($stmt, ':cc', self::normalizarDireccion($payload['destinatario_cc'] ?? null));
        self::bindNullable($stmt, ':bcc', self::normalizarDireccion($payload['destinatario_bcc'] ?? null));
        $stmt->bindValue(':asunto', $asunto, PDO::PARAM_STR);
        $stmt->bindValue(':html', $html, PDO::PARAM_STR);
        self::bindNullable($stmt, ':texto', $payload['contenido_texto'] ?? null);
        self::bindNullable($stmt, ':adjuntos', self::normalizarAdjuntos($payload['adjuntos'] ?? null));
        $stmt->bindValue(':remitente', $payload['remitente'] ?? EMAIL_SENDER, PDO::PARAM_STR);
        $stmt->bindValue(':maxIntentos', (int)($payload['max_intentos'] ?? 5), PDO::PARAM_INT);
        self::bindNullable($stmt, ':fechaProgramada', $payload['fecha_programada'] ?? null);
        self::bindNullable($stmt, ':usuarioId', $payload['usuario_id'] ?? null);
        self::bindNullable($stmt, ':institucionId', $payload['institucion_id'] ?? null);
        
        // Si el campo entorno existe, agregarlo
        if ($tieneCampoEntorno) {
            $entornoPayload = $payload['entorno'] ?? $entorno;
            $stmt->bindValue(':entorno', $entornoPayload, PDO::PARAM_STR);
        }
        
        $stmt->execute();

        return (int)$pdo->lastInsertId();
    }

    /**
     * Reclama correos pendientes marcándolos como procesando.
     *
     * @param int $limite
     * @param string|null $entorno Filtrar por entorno (PROD, TEST, LOCAL). Si es null, no filtra.
     * @return array
     */
    public static function reclamarPendientes(int $limite = 100, ?string $entorno = null): array
    {
        if ($limite <= 0) {
            return [];
        }

        $pdo   = self::conexion();
        $tabla = self::TABLA;
        
        // Verificar si la columna mq_entorno existe
        $tieneCampoEntorno = self::tieneCampoEntorno($pdo);
        
        // Construir condición WHERE
        $whereConditions = [
            "mq_estado = :estadoPendiente",
            "(mq_fecha_programada IS NULL OR mq_fecha_programada <= NOW())",
            "mq_intentos < mq_max_intentos"
        ];
        
        // Si se especifica un entorno y el campo existe, filtrar por entorno
        if ($entorno !== null && $tieneCampoEntorno) {
            $whereConditions[] = "mq_entorno = :entorno";
        }

        $whereClause = implode(" AND ", $whereConditions);

        $stmtSeleccion = $pdo->prepare("
            SELECT mq_id FROM {$tabla}
            WHERE {$whereClause}
            ORDER BY mq_prioridad ASC, mq_fecha_creacion ASC
            LIMIT :limite
        ");
        $stmtSeleccion->bindValue(':estadoPendiente', self::ESTADO_PENDIENTE, PDO::PARAM_STR);
        if ($entorno !== null && $tieneCampoEntorno) {
            $stmtSeleccion->bindValue(':entorno', $entorno, PDO::PARAM_STR);
        }
        $stmtSeleccion->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmtSeleccion->execute();

        $ids = $stmtSeleccion->fetchAll(PDO::FETCH_COLUMN);

        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmtUpdate = $pdo->prepare("
            UPDATE {$tabla}
            SET mq_estado = ?, mq_fecha_actualizacion = NOW()
            WHERE mq_id IN ({$placeholders})
        ");

        $paramsUpdate = array_merge([self::ESTADO_PROCESANDO], $ids);
        $stmtUpdate->execute($paramsUpdate);

        $stmtFetch = $pdo->prepare("
            SELECT * FROM {$tabla}
            WHERE mq_id IN ({$placeholders})
            ORDER BY mq_prioridad ASC, mq_fecha_actualizacion ASC
        ");
        $stmtFetch->execute($ids);

        return $stmtFetch->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marca un correo como enviado.
     */
    public static function marcarEnviado(int $id, ?string $respuestaServidor = null): void
    {
        $pdo = self::conexion();
        $sql = "UPDATE " . self::TABLA . "
                SET mq_estado = :estado, mq_respuesta_servidor = :respuesta,
                    mq_fecha_envio = NOW(), mq_fecha_actualizacion = NOW()
                WHERE mq_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':estado', self::ESTADO_ENVIADO, PDO::PARAM_STR);
        self::bindNullable($stmt, ':respuesta', $respuestaServidor);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Marca un correo como pendiente nuevamente (liberar) después de un error temporal.
     */
    public static function reagendar(int $id, ?string $fechaProgramada, ?string $motivo = null): void
    {
        $pdo = self::conexion();
        $sql = "UPDATE " . self::TABLA . "
                SET mq_estado = :estado, mq_respuesta_servidor = :motivo,
                    mq_fecha_programada = :fechaProgramada,
                    mq_fecha_actualizacion = NOW(),
                    mq_intentos = mq_intentos + 1
                WHERE mq_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':estado', self::ESTADO_PENDIENTE, PDO::PARAM_STR);
        self::bindNullable($stmt, ':motivo', $motivo);
        self::bindNullable($stmt, ':fechaProgramada', $fechaProgramada);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Marca definitivamente como error.
     */
    public static function marcarError(int $id, string $mensajeError): void
    {
        $pdo = self::conexion();
        $sql = "UPDATE " . self::TABLA . "
                SET mq_estado = :estado, mq_respuesta_servidor = :mensaje,
                    mq_fecha_actualizacion = NOW(),
                    mq_intentos = mq_intentos + 1
                WHERE mq_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':estado', self::ESTADO_ERROR, PDO::PARAM_STR);
        $stmt->bindValue(':mensaje', $mensajeError, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Descarta definitivamente un correo (por ejemplo, email inválido).
     */
    public static function descartar(int $id, string $motivo): void
    {
        $pdo = self::conexion();
        $sql = "UPDATE " . self::TABLA . "
                SET mq_estado = :estado, mq_respuesta_servidor = :motivo,
                    mq_fecha_actualizacion = NOW()
                WHERE mq_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':estado', self::ESTADO_DESCARTADO, PDO::PARAM_STR);
        $stmt->bindValue(':motivo', $motivo, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Convierte arrays a cadenas separadas por coma para almacenar direcciones.
     */
    private static function normalizarDireccion($valor): ?string
    {
        if (is_array($valor)) {
            $valor = array_filter(array_map('trim', $valor));
            $valor = array_unique($valor);
            $valor = implode(',', $valor);
        }

        if (is_string($valor)) {
            $valor = trim($valor);
        }

        return $valor !== '' ? $valor : null;
    }

    /**
     * Convierte adjuntos a JSON.
     */
    private static function normalizarAdjuntos($adjuntos): ?string
    {
        if (empty($adjuntos)) {
            return null;
        }

        if (is_string($adjuntos)) {
            return $adjuntos;
        }

        $adjuntos = array_values(array_filter($adjuntos, function ($valor) {
            return !empty($valor);
        }));

        return json_encode($adjuntos, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Helper para bindear valores nulos.
     */
    private static function bindNullable(PDOStatement $stmt, string $param, $valor, int $type = PDO::PARAM_STR): void
    {
        if ($valor === null || $valor === '') {
            $stmt->bindValue($param, null, PDO::PARAM_NULL);
            return;
        }

        $stmt->bindValue($param, $valor, $type);
    }

    /**
     * Obtiene estadísticas de correos para una institución.
     * 
     * @param string|null $institucionId ID de la institución (null para todas)
     * @return array Estadísticas de correos
     */
    public static function obtenerEstadisticas(?string $institucionId = null): array
    {
        global $baseDatosServicios;
        
        $pdo = self::conexion();
        $tablaCola = self::TABLA;
        $tablaHistorial = $baseDatosServicios . ".historial_correos_enviados";
        
        $whereInstitucion = $institucionId !== null ? "WHERE mq_institucion_id = :institucionId" : "";
        $whereInstitucionHistorial = $institucionId !== null ? "WHERE hisco_id_institucion = :institucionId" : "";
        
        $params = [];
        if ($institucionId !== null) {
            $params[':institucionId'] = $institucionId;
        }
        
        // Estadísticas de la cola
        $sqlCola = "
            SELECT 
                mq_estado,
                COUNT(*) as cantidad
            FROM {$tablaCola}
            {$whereInstitucion}
            GROUP BY mq_estado
        ";
        
        $stmtCola = $pdo->prepare($sqlCola);
        if ($institucionId !== null) {
            $stmtCola->bindValue(':institucionId', $institucionId, PDO::PARAM_STR);
        }
        $stmtCola->execute();
        $estadosCola = $stmtCola->fetchAll(PDO::FETCH_ASSOC);
        
        // Inicializar contadores
        $estadisticas = [
            // Estados de la cola (aún no procesados completamente)
            'cola_pendiente' => 0,
            'cola_procesando' => 0,
            'cola_enviado' => 0,  // Temporal: correos enviados pero aún en cola
            'cola_error' => 0,    // Errores definitivos en cola
            'cola_descartado' => 0,
            'cola_total' => 0,
            
            // Estados del historial (intentos de envío registrados)
            'historial_enviado' => 0,  // Única fuente de verdad para enviados
            'historial_error' => 0,    // Errores registrados en historial
            'historial_total' => 0,    // Total de intentos registrados
            
            // Totales calculados
            'total_enviados' => 0,     // Solo de historial (fuente única)
            'total_intentados' => 0,   // Total de intentos (historial completo)
            'total_fallidos' => 0      // Errores de historial + errores de cola
        ];
        
        // Procesar estados de la cola
        foreach ($estadosCola as $estado) {
            $estadoNombre = $estado['mq_estado'];
            $cantidad = (int)$estado['cantidad'];
            
            switch ($estadoNombre) {
                case self::ESTADO_PENDIENTE:
                    $estadisticas['cola_pendiente'] = $cantidad;
                    break;
                case self::ESTADO_PROCESANDO:
                    $estadisticas['cola_procesando'] = $cantidad;
                    break;
                case self::ESTADO_ENVIADO:
                    $estadisticas['cola_enviado'] = $cantidad;
                    break;
                case self::ESTADO_ERROR:
                    $estadisticas['cola_error'] = $cantidad;
                    break;
                case self::ESTADO_DESCARTADO:
                    $estadisticas['cola_descartado'] = $cantidad;
                    break;
            }
            $estadisticas['cola_total'] += $cantidad;
        }
        
        // Estadísticas del historial (fuente única de verdad para enviados)
        $sqlHistorial = "
            SELECT 
                hisco_estado,
                COUNT(*) as cantidad
            FROM {$tablaHistorial}
            {$whereInstitucionHistorial}
            GROUP BY hisco_estado
        ";
        
        $stmtHistorial = $pdo->prepare($sqlHistorial);
        if ($institucionId !== null) {
            $stmtHistorial->bindValue(':institucionId', $institucionId, PDO::PARAM_STR);
        }
        $stmtHistorial->execute();
        $estadosHistorial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);
        
        // Procesar estados del historial
        foreach ($estadosHistorial as $estado) {
            $estadoNombre = $estado['hisco_estado'];
            $cantidad = (int)$estado['cantidad'];
            
            if ($estadoNombre === 'enviado') {
                $estadisticas['historial_enviado'] = $cantidad;
            } elseif ($estadoNombre === 'error') {
                $estadisticas['historial_error'] = $cantidad;
            }
            $estadisticas['historial_total'] += $cantidad;
        }
        
        // Totales calculados
        // Total enviados: SOLO del historial (fuente única de verdad)
        $estadisticas['total_enviados'] = $estadisticas['historial_enviado'];
        
        // Total intentados: todos los registros del historial (todos los intentos)
        $estadisticas['total_intentados'] = $estadisticas['historial_total'];
        
        // Total fallidos: errores del historial + errores definitivos de la cola
        $estadisticas['total_fallidos'] = $estadisticas['historial_error'] + $estadisticas['cola_error'];
        
        return $estadisticas;
    }

    /**
     * Verifica si la tabla tiene el campo mq_entorno.
     */
    private static function tieneCampoEntorno(PDO $pdo): bool
    {
        static $cache = null;
        
        if ($cache !== null) {
            return $cache;
        }
        
        try {
            $tabla = self::TABLA;
            $stmt = $pdo->query("SHOW COLUMNS FROM {$tabla} LIKE 'mq_entorno'");
            $cache = $stmt->rowCount() > 0;
        } catch (Exception $e) {
            // Si hay error, asumir que no existe
            $cache = false;
        }
        
        return $cache;
    }

    /**
     * Obtiene conexión PDO.
     */
    private static function conexion(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $pdo = Conexion::newConnection('PDO');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES utf8mb4");

        return $pdo;
    }
}


