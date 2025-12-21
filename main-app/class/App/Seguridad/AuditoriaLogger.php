<?php
/**
 * AUDITORÍA Y LOGGING DE SEGURIDAD
 * Registra acciones sensibles para auditoría y análisis forense
 */

class AuditoriaLogger {
    
    // Tipos de acciones
    const ACCION_LOGIN = 'LOGIN';
    const ACCION_LOGOUT = 'LOGOUT';
    const ACCION_CREAR = 'CREAR';
    const ACCION_EDITAR = 'EDITAR';
    const ACCION_ELIMINAR = 'ELIMINAR';
    const ACCION_PERMISOS = 'PERMISOS';
    const ACCION_CONFIGURACION = 'CONFIGURACION';
    const ACCION_ACCESO_ADMIN = 'ACCESO_ADMIN';
    const ACCION_EXPORTAR = 'EXPORTAR';
    const ACCION_IMPORTAR = 'IMPORTAR';
    
    // Niveles de severidad
    const NIVEL_INFO = 'INFO';
    const NIVEL_WARNING = 'WARNING';
    const NIVEL_CRITICAL = 'CRITICAL';
    
    private static $conexion;
    
    /**
     * Inicializar conexión
     */
    private static function getConexion() {
        if (self::$conexion === null) {
            global $servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios;
            self::$conexion = mysqli_connect($servidorConexion, $usuarioConexion, $claveConexion, $baseDatosServicios);
            
            if (!self::$conexion) {
                throw new Exception('Error de conexión a la base de datos');
            }
        }
        return self::$conexion;
    }
    
    /**
     * Registrar acción de auditoría
     * @param string $accion Tipo de acción (usar constantes)
     * @param string $modulo Módulo afectado
     * @param string $descripcion Descripción de la acción
     * @param string $nivel Nivel de severidad
     * @param array $datosAdicionales Datos adicionales (JSON)
     * @param string $usuarioId ID del usuario que realiza la acción
     */
    public static function registrar($accion, $modulo, $descripcion, $nivel = self::NIVEL_INFO, $datosAdicionales = [], $usuarioId = null) {
        try {
            $conexion = self::getConexion();
            
            // Obtener usuario actual si no se proporciona
            if ($usuarioId === null) {
                $usuarioId = isset($_SESSION['id']) ? $_SESSION['id'] : null;
            }
            
            // Obtener datos del contexto
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
            $url = $_SERVER['REQUEST_URI'] ?? 'N/A';
            $metodo = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
            $institucion = $_SESSION['idInstitucion'] ?? null;
            $year = $_SESSION['bd'] ?? null;
            
            // Sanitizar datos
            $accion = mysqli_real_escape_string($conexion, $accion);
            $modulo = mysqli_real_escape_string($conexion, substr($modulo, 0, 100));
            $descripcion = mysqli_real_escape_string($conexion, substr($descripcion, 0, 500));
            $nivel = mysqli_real_escape_string($conexion, $nivel);
            $ip = mysqli_real_escape_string($conexion, $ip);
            $userAgent = mysqli_real_escape_string($conexion, substr($userAgent, 0, 255));
            $url = mysqli_real_escape_string($conexion, substr($url, 0, 255));
            $metodo = mysqli_real_escape_string($conexion, $metodo);
            
            // Convertir datos adicionales a JSON
            $datosJson = !empty($datosAdicionales) ? json_encode($datosAdicionales, JSON_UNESCAPED_UNICODE) : '{}';
            $datosJson = mysqli_real_escape_string($conexion, $datosJson);
            
            // Insertar en tabla de auditoría
            $query = "INSERT INTO " . BD_ADMIN . ".auditoria_seguridad (
                aud_usuario_id,
                aud_accion,
                aud_modulo,
                aud_descripcion,
                aud_nivel,
                aud_ip,
                aud_user_agent,
                aud_url,
                aud_metodo,
                aud_datos_adicionales,
                aud_institucion,
                aud_year,
                aud_fecha
            ) VALUES (
                " . ($usuarioId ? "'$usuarioId'" : "NULL") . ",
                '$accion',
                '$modulo',
                '$descripcion',
                '$nivel',
                '$ip',
                '$userAgent',
                '$url',
                '$metodo',
                '$datosJson',
                " . ($institucion ? "'$institucion'" : "NULL") . ",
                " . ($year ? "'$year'" : "NULL") . ",
                NOW()
            )";
            
            $resultado = mysqli_query($conexion, $query);
            
            if (!$resultado) {
                error_log("❌ ERROR AUDITORÍA - No se pudo registrar: " . mysqli_error($conexion));
                return false;
            }
            
            // Log en archivo también
            $emoji = self::getEmojiPorNivel($nivel);
            $logMensaje = "{$emoji} AUDITORÍA [{$nivel}] - Acción: {$accion} | Módulo: {$modulo} | Usuario: {$usuarioId} | IP: {$ip} | Descripción: {$descripcion}";
            error_log($logMensaje);
            
            return true;
            
        } catch (Exception $e) {
            error_log("❌ EXCEPCIÓN EN AUDITORÍA: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registrar login exitoso
     */
    public static function registrarLogin($usuarioId, $usuario, $institucion) {
        self::registrar(
            self::ACCION_LOGIN,
            'Autenticación',
            "Login exitoso - Usuario: {$usuario}",
            self::NIVEL_INFO,
            [
                'usuario' => $usuario,
                'institucion_id' => $institucion
            ],
            $usuarioId
        );
    }
    
    /**
     * Registrar logout
     */
    public static function registrarLogout($usuarioId, $usuario) {
        self::registrar(
            self::ACCION_LOGOUT,
            'Autenticación',
            "Logout - Usuario: {$usuario}",
            self::NIVEL_INFO,
            ['usuario' => $usuario],
            $usuarioId
        );
    }
    
    /**
     * Registrar creación de registro
     */
    public static function registrarCreacion($modulo, $registroId, $descripcion, $datosAdicionales = []) {
        self::registrar(
            self::ACCION_CREAR,
            $modulo,
            $descripcion,
            self::NIVEL_INFO,
            array_merge(['registro_id' => $registroId], $datosAdicionales)
        );
    }
    
    /**
     * Registrar edición de registro
     */
    public static function registrarEdicion($modulo, $registroId, $descripcion, $cambios = []) {
        self::registrar(
            self::ACCION_EDITAR,
            $modulo,
            $descripcion,
            self::NIVEL_WARNING,
            array_merge(['registro_id' => $registroId, 'cambios' => $cambios])
        );
    }
    
    /**
     * Registrar eliminación de registro
     */
    public static function registrarEliminacion($modulo, $registroId, $descripcion, $datosEliminados = []) {
        self::registrar(
            self::ACCION_ELIMINAR,
            $modulo,
            $descripcion,
            self::NIVEL_CRITICAL,
            array_merge(['registro_id' => $registroId, 'datos_eliminados' => $datosEliminados])
        );
    }
    
    /**
     * Registrar cambio de permisos
     */
    public static function registrarCambioPermisos($usuarioAfectado, $descripcion, $permisosAnteriores = [], $permisosNuevos = []) {
        self::registrar(
            self::ACCION_PERMISOS,
            'Usuarios',
            $descripcion,
            self::NIVEL_CRITICAL,
            [
                'usuario_afectado' => $usuarioAfectado,
                'permisos_anteriores' => $permisosAnteriores,
                'permisos_nuevos' => $permisosNuevos
            ]
        );
    }
    
    /**
     * Registrar cambio de configuración
     */
    public static function registrarCambioConfiguracion($parametro, $valorAnterior, $valorNuevo, $descripcion = '') {
        self::registrar(
            self::ACCION_CONFIGURACION,
            'Configuración Sistema',
            $descripcion ?: "Cambio en {$parametro}",
            self::NIVEL_WARNING,
            [
                'parametro' => $parametro,
                'valor_anterior' => $valorAnterior,
                'valor_nuevo' => $valorNuevo
            ]
        );
    }
    
    /**
     * Registrar acceso a módulo administrativo
     */
    public static function registrarAccesoAdmin($modulo, $descripcion = '') {
        self::registrar(
            self::ACCION_ACCESO_ADMIN,
            $modulo,
            $descripcion ?: "Acceso a módulo administrativo: {$modulo}",
            self::NIVEL_INFO
        );
    }
    
    /**
     * Registrar exportación de datos
     */
    public static function registrarExportacion($modulo, $cantidad, $filtros = []) {
        self::registrar(
            self::ACCION_EXPORTAR,
            $modulo,
            "Exportación de {$cantidad} registros",
            self::NIVEL_WARNING,
            [
                'cantidad_registros' => $cantidad,
                'filtros_aplicados' => $filtros
            ]
        );
    }
    
    /**
     * Registrar importación de datos
     */
    public static function registrarImportacion($modulo, $cantidad, $archivo = '') {
        self::registrar(
            self::ACCION_IMPORTAR,
            $modulo,
            "Importación de {$cantidad} registros" . ($archivo ? " desde {$archivo}" : ""),
            self::NIVEL_CRITICAL,
            [
                'cantidad_registros' => $cantidad,
                'archivo' => $archivo
            ]
        );
    }
    
    /**
     * Obtener emoji según nivel
     */
    private static function getEmojiPorNivel($nivel) {
        switch ($nivel) {
            case self::NIVEL_INFO:
                return '📘';
            case self::NIVEL_WARNING:
                return '⚠️';
            case self::NIVEL_CRITICAL:
                return '🚨';
            default:
                return '📝';
        }
    }
    
    /**
     * Obtener logs de auditoría con filtros
     * @param int $limite Cantidad de registros
     * @param string $nivel Filtrar por nivel
     * @param string $accion Filtrar por acción
     * @param int $horas Últimas X horas
     * @return array Registros de auditoría
     */
    public static function obtenerLogs($limite = 100, $nivel = null, $accion = null, $horas = 24) {
        $conexion = self::getConexion();
        
        $filtros = [];
        $tiempoLimite = date('Y-m-d H:i:s', time() - ($horas * 3600));
        $filtros[] = "a.aud_fecha > '$tiempoLimite'";
        
        if ($nivel) {
            $nivel = mysqli_real_escape_string($conexion, $nivel);
            $filtros[] = "a.aud_nivel = '$nivel'";
        }
        
        if ($accion) {
            $accion = mysqli_real_escape_string($conexion, $accion);
            $filtros[] = "a.aud_accion = '$accion'";
        }
        
        $whereClause = count($filtros) > 0 ? "WHERE " . implode(" AND ", $filtros) : "";
        
        // JOIN con condiciones específicas para evitar duplicados
        $query = "SELECT a.*, u.uss_nombre, u.uss_apellido1, u.uss_usuario
                  FROM " . BD_ADMIN . ".auditoria_seguridad a
                  LEFT JOIN " . BD_GENERAL . ".usuarios u ON (
                      u.uss_id = a.aud_usuario_id 
                      AND u.institucion = a.aud_institucion 
                      AND u.year = a.aud_year
                  )
                  $whereClause
                  ORDER BY a.aud_fecha DESC
                  LIMIT $limite";
        
        $consulta = mysqli_query($conexion, $query);
        
        $logs = [];
        while ($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $logs[] = $row;
        }
        
        return $logs;
    }
    
    /**
     * Obtener estadísticas de auditoría
     * @param int $horas Últimas X horas
     * @return array Estadísticas
     */
    public static function obtenerEstadisticas($horas = 24) {
        $conexion = self::getConexion();
        $tiempoLimite = date('Y-m-d H:i:s', time() - ($horas * 3600));
        
        // Total de acciones
        $queryTotal = "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".auditoria_seguridad 
                       WHERE aud_fecha > '$tiempoLimite'";
        $total = mysqli_fetch_array(mysqli_query($conexion, $queryTotal), MYSQLI_BOTH)['total'];
        
        // Por nivel
        $queryNiveles = "SELECT aud_nivel, COUNT(*) as total FROM " . BD_ADMIN . ".auditoria_seguridad 
                        WHERE aud_fecha > '$tiempoLimite' 
                        GROUP BY aud_nivel";
        $consultaNiveles = mysqli_query($conexion, $queryNiveles);
        $porNivel = [
            'INFO' => 0,
            'WARNING' => 0,
            'CRITICAL' => 0
        ];
        while ($row = mysqli_fetch_array($consultaNiveles, MYSQLI_BOTH)) {
            $porNivel[$row['aud_nivel']] = (int)$row['total'];
        }
        
        // Por acción
        $queryAcciones = "SELECT aud_accion, COUNT(*) as total FROM " . BD_ADMIN . ".auditoria_seguridad 
                         WHERE aud_fecha > '$tiempoLimite' 
                         GROUP BY aud_accion 
                         ORDER BY total DESC 
                         LIMIT 10";
        $consultaAcciones = mysqli_query($conexion, $queryAcciones);
        $porAccion = [];
        while ($row = mysqli_fetch_array($consultaAcciones, MYSQLI_BOTH)) {
            $porAccion[] = $row;
        }
        
        // Usuarios más activos
        $queryUsuarios = "SELECT a.aud_usuario_id, u.uss_nombre, u.uss_apellido1, u.uss_usuario, COUNT(*) as acciones
                         FROM " . BD_ADMIN . ".auditoria_seguridad a
                         LEFT JOIN " . BD_GENERAL . ".usuarios u ON (
                             u.uss_id = a.aud_usuario_id 
                             AND u.institucion = a.aud_institucion 
                             AND u.year = a.aud_year
                         )
                         WHERE a.aud_fecha > '$tiempoLimite' AND a.aud_usuario_id IS NOT NULL
                         GROUP BY a.aud_usuario_id, a.aud_institucion, a.aud_year
                         ORDER BY acciones DESC
                         LIMIT 10";
        $consultaUsuarios = mysqli_query($conexion, $queryUsuarios);
        $usuariosActivos = [];
        while ($row = mysqli_fetch_array($consultaUsuarios, MYSQLI_BOTH)) {
            $usuariosActivos[] = $row;
        }
        
        return [
            'total' => $total,
            'por_nivel' => $porNivel,
            'por_accion' => $porAccion,
            'usuarios_activos' => $usuariosActivos,
            'periodo_horas' => $horas
        ];
    }
    
    /**
     * Limpiar logs antiguos (para mantenimiento)
     * @param int $dias Eliminar logs mayores a X días
     * @return int Registros eliminados
     */
    public static function limpiarLogsAntiguos($dias = 90) {
        $conexion = self::getConexion();
        $tiempoLimite = date('Y-m-d H:i:s', time() - ($dias * 24 * 3600));
        
        // Mantener solo logs CRITICAL indefinidamente, eliminar INFO/WARNING antiguos
        $query = "DELETE FROM " . BD_ADMIN . ".auditoria_seguridad 
                  WHERE aud_fecha < '$tiempoLimite' 
                  AND aud_nivel != '" . self::NIVEL_CRITICAL . "'";
        
        mysqli_query($conexion, $query);
        $registrosEliminados = mysqli_affected_rows($conexion);
        
        error_log("🧹 MANTENIMIENTO AUDITORÍA - Eliminados {$registrosEliminados} registros antiguos (>{$dias} días)");
        
        return $registrosEliminados;
    }
    
    /**
     * Buscar logs por usuario
     * @param string $usuarioId ID del usuario
     * @param int $limite Cantidad de registros
     * @return array Logs del usuario
     */
    public static function obtenerLogsPorUsuario($usuarioId, $limite = 50) {
        $conexion = self::getConexion();
        $usuarioId = mysqli_real_escape_string($conexion, $usuarioId);
        
        $query = "SELECT * FROM " . BD_ADMIN . ".auditoria_seguridad 
                  WHERE aud_usuario_id = '$usuarioId' 
                  ORDER BY aud_fecha DESC 
                  LIMIT $limite";
        
        $consulta = mysqli_query($conexion, $query);
        
        $logs = [];
        while ($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $logs[] = $row;
        }
        
        return $logs;
    }
    
    /**
     * Buscar logs por módulo
     * @param string $modulo Nombre del módulo
     * @param int $limite Cantidad de registros
     * @return array Logs del módulo
     */
    public static function obtenerLogsPorModulo($modulo, $limite = 50) {
        $conexion = self::getConexion();
        $modulo = mysqli_real_escape_string($conexion, $modulo);
        
        // JOIN con condiciones específicas para evitar duplicados
        $query = "SELECT a.*, u.uss_nombre, u.uss_apellido1, u.uss_usuario
                  FROM " . BD_ADMIN . ".auditoria_seguridad a
                  LEFT JOIN " . BD_GENERAL . ".usuarios u ON (
                      u.uss_id = a.aud_usuario_id 
                      AND u.institucion = a.aud_institucion 
                      AND u.year = a.aud_year
                  )
                  WHERE a.aud_modulo = '$modulo' 
                  ORDER BY a.aud_fecha DESC 
                  LIMIT $limite";
        
        $consulta = mysqli_query($conexion, $query);
        
        $logs = [];
        while ($row = mysqli_fetch_array($consulta, MYSQLI_BOTH)) {
            $logs[] = $row;
        }
        
        return $logs;
    }
}

