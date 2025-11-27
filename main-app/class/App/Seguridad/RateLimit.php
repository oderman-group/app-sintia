<?php
/**
 * RATE LIMITING - CONTROL DE INTENTOS DE LOGIN
 * Previene ataques de fuerza bruta limitando intentos por IP y usuario
 */

class RateLimit {
    
    // Configuración
    const MAX_INTENTOS_IP = 10;           // Máximo intentos por IP en ventana de tiempo
    const MAX_INTENTOS_USUARIO = 5;       // Máximo intentos por usuario
    const TIEMPO_BLOQUEO_IP = 900;        // 15 minutos en segundos
    const TIEMPO_BLOQUEO_USUARIO = 1800;  // 30 minutos en segundos
    const VENTANA_TIEMPO = 3600;          // 1 hora - ventana para contar intentos
    
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
     * Verificar si una IP está bloqueada
     * @param string $ip Dirección IP
     * @return array ['bloqueado' => bool, 'tiempo_restante' => int, 'intentos' => int]
     */
    public static function verificarBloqueoIP($ip) {
        $conexion = self::getConexion();
        $ip = mysqli_real_escape_string($conexion, $ip);
        $tiempoLimite = date('Y-m-d H:i:s', time() - self::TIEMPO_BLOQUEO_IP);
        
        // Contar intentos recientes de esta IP
        $query = "SELECT COUNT(*) as intentos, MAX(uif_fecha) as ultimo_intento
                  FROM " . BD_ADMIN . ".usuarios_intentos_fallidos 
                  WHERE uif_ip = '$ip' 
                  AND uif_fecha > '$tiempoLimite'";
        
        $consulta = mysqli_query($conexion, $query);
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        
        $intentos = (int)$resultado['intentos'];
        $bloqueado = $intentos >= self::MAX_INTENTOS_IP;
        
        $tiempoRestante = 0;
        if ($bloqueado && $resultado['ultimo_intento']) {
            $tiempoUltimoIntento = strtotime($resultado['ultimo_intento']);
            $tiempoRestante = max(0, self::TIEMPO_BLOQUEO_IP - (time() - $tiempoUltimoIntento));
        }
        
        return [
            'bloqueado' => $bloqueado,
            'tiempo_restante' => $tiempoRestante,
            'intentos' => $intentos
        ];
    }
    
    /**
     * Verificar si un usuario está bloqueado
     * @param string $usuario Nombre de usuario
     * @return array ['bloqueado' => bool, 'tiempo_restante' => int, 'intentos' => int]
     */
    public static function verificarBloqueoUsuario($usuario) {
        $conexion = self::getConexion();
        $usuario = mysqli_real_escape_string($conexion, $usuario);
        $tiempoLimite = date('Y-m-d H:i:s', time() - self::TIEMPO_BLOQUEO_USUARIO);
        
        error_log("🔍 RATE LIMIT - Verificando usuario: {$usuario}");
        error_log("🔍 Tiempo límite: {$tiempoLimite}");
        
        // Buscar el uss_id del usuario más reciente
        $queryUsuario = "SELECT uss_id, institucion, year 
                        FROM " . BD_GENERAL . ".usuarios 
                        WHERE uss_usuario = '$usuario' 
                        ORDER BY uss_id DESC LIMIT 1";
        
        $consultaUsuario = mysqli_query($conexion, $queryUsuario);
        
        if (mysqli_num_rows($consultaUsuario) == 0) {
            error_log("🔍 Usuario NO encontrado en BD");
            // Usuario no existe, no está bloqueado pero registramos el intento
            return [
                'bloqueado' => false,
                'tiempo_restante' => 0,
                'intentos' => 0,
                'usuario_existe' => false
            ];
        }
        
        $datosUsuario = mysqli_fetch_array($consultaUsuario, MYSQLI_BOTH);
        $ussId = $datosUsuario['uss_id'];
        
        error_log("🔍 Usuario encontrado - uss_id: {$ussId}");
        
        // Contar intentos recientes de este usuario
        $query = "SELECT COUNT(*) as intentos, MAX(uif_fecha) as ultimo_intento
                  FROM " . BD_ADMIN . ".usuarios_intentos_fallidos 
                  WHERE uif_usuarios = '$ussId' 
                  AND uif_fecha > '$tiempoLimite'";
        
        error_log("🔍 Query intentos: {$query}");
        
        $consulta = mysqli_query($conexion, $query);
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        
        $intentos = (int)$resultado['intentos'];
        $bloqueado = $intentos >= self::MAX_INTENTOS_USUARIO;
        
        error_log("🔍 Intentos encontrados: {$intentos}");
        error_log("🔍 Bloqueado: " . ($bloqueado ? 'SÍ' : 'NO'));
        
        $tiempoRestante = 0;
        if ($bloqueado && $resultado['ultimo_intento']) {
            $tiempoUltimoIntento = strtotime($resultado['ultimo_intento']);
            $tiempoRestante = max(0, self::TIEMPO_BLOQUEO_USUARIO - (time() - $tiempoUltimoIntento));
            error_log("🔍 Tiempo restante de bloqueo: {$tiempoRestante} segundos");
        }
        
        return [
            'bloqueado' => $bloqueado,
            'tiempo_restante' => $tiempoRestante,
            'intentos' => $intentos,
            'usuario_existe' => true,
            'uss_id' => $ussId,
            'institucion' => $datosUsuario['institucion'],
            'year' => $datosUsuario['year']
        ];
    }
    
    /**
     * Registrar intento fallido
     * @param string $usuario Nombre de usuario
     * @param string $ip Dirección IP
     * @param string $clave Clave intentada (para análisis)
     */
    public static function registrarIntentoFallido($usuario, $ip, $clave = '') {
        $conexion = self::getConexion();
        
        $usuario = mysqli_real_escape_string($conexion, $usuario);
        $ip = mysqli_real_escape_string($conexion, $ip);
        $clave = mysqli_real_escape_string($conexion, substr($clave, 0, 50)); // Limitar longitud
        
        error_log("📝 REGISTRAR INTENTO FALLIDO - Usuario: {$usuario} | IP: {$ip}");
        
        // Buscar datos del usuario
        $queryUsuario = "SELECT uss_id, institucion, year 
                        FROM " . BD_GENERAL . ".usuarios 
                        WHERE uss_usuario = '$usuario' 
                        ORDER BY uss_id DESC LIMIT 1";
        
        $consultaUsuario = mysqli_query($conexion, $queryUsuario);
        
        $ussId = null;
        $institucion = null;
        $year = null;
        
        if (mysqli_num_rows($consultaUsuario) > 0) {
            $datosUsuario = mysqli_fetch_array($consultaUsuario, MYSQLI_BOTH);
            $ussId = $datosUsuario['uss_id'];
            $institucion = $datosUsuario['institucion'];
            $year = $datosUsuario['year'];
            
            error_log("📝 Usuario encontrado - uss_id: {$ussId} | institucion: {$institucion} | year: {$year}");
            
            // Actualizar contador en tabla usuarios
            $queryUpdate = "UPDATE " . BD_GENERAL . ".usuarios 
                           SET uss_intentos_fallidos = uss_intentos_fallidos + 1 
                           WHERE uss_id = '$ussId' 
                           AND institucion = '$institucion' 
                           AND year = '$year'";
            
            mysqli_query($conexion, $queryUpdate);
            error_log("📝 Contador uss_intentos_fallidos incrementado");
        } else {
            error_log("📝 Usuario NO encontrado en BD");
        }
        
        // Registrar en tabla de intentos fallidos
        $queryIntentos = "INSERT INTO " . BD_ADMIN . ".usuarios_intentos_fallidos 
                         (uif_usuarios, uif_ip, uif_clave, uif_institucion, uif_year, uif_fecha) 
                         VALUES (
                             " . ($ussId ? "'$ussId'" : "NULL") . ", 
                             '$ip', 
                             '$clave', 
                             " . ($institucion ? "'$institucion'" : "NULL") . ", 
                             " . ($year ? "'$year'" : "NULL") . ",
                             NOW()
                         )";
        
        error_log("📝 Query INSERT: {$queryIntentos}");
        
        $resultado = mysqli_query($conexion, $queryIntentos);
        
        if ($resultado) {
            $insertedId = mysqli_insert_id($conexion);
            error_log("📝 ✅ Intento registrado exitosamente - ID: {$insertedId}");
        } else {
            error_log("📝 ❌ ERROR al insertar intento: " . mysqli_error($conexion));
        }
        
        // Log de seguridad
        self::logIntentoFallido($usuario, $ip, $ussId);
    }
    
    /**
     * Limpiar intentos fallidos después de login exitoso
     * @param string $ussId ID del usuario
     * @param string $ip Dirección IP
     */
    public static function limpiarIntentos($ussId, $ip) {
        $conexion = self::getConexion();
        
        $ussId = mysqli_real_escape_string($conexion, $ussId);
        $ip = mysqli_real_escape_string($conexion, $ip);
        
        // Resetear contador en usuarios
        mysqli_query($conexion, "UPDATE " . BD_GENERAL . ".usuarios 
                                SET uss_intentos_fallidos = 0 
                                WHERE uss_id = '$ussId'");
        
        // No eliminamos los registros históricos, solo los ignoramos por fecha
        // Esto permite análisis de seguridad posterior
        
        self::logLoginExitoso($ussId, $ip);
    }
    
    /**
     * Formatear tiempo restante en formato legible
     * @param int $segundos Segundos restantes
     * @return string Tiempo formateado
     */
    public static function formatearTiempoRestante($segundos) {
        if ($segundos < 60) {
            return $segundos . " segundo" . ($segundos != 1 ? "s" : "");
        }
        
        $minutos = ceil($segundos / 60);
        return $minutos . " minuto" . ($minutos != 1 ? "s" : "");
    }
    
    /**
     * Log de intento fallido
     */
    private static function logIntentoFallido($usuario, $ip, $ussId = null) {
        $mensaje = "🔴 INTENTO DE LOGIN FALLIDO - Usuario: {$usuario} | IP: {$ip}" . 
                   ($ussId ? " | uss_id: {$ussId}" : " | Usuario no encontrado");
        error_log($mensaje);
    }
    
    /**
     * Log de login exitoso
     */
    private static function logLoginExitoso($ussId, $ip) {
        error_log("🟢 LOGIN EXITOSO - uss_id: {$ussId} | IP: {$ip}");
    }
    
    /**
     * Log de bloqueo
     */
    public static function logBloqueo($tipo, $identificador, $ip, $tiempoRestante) {
        $mensaje = "🚨 BLOQUEO POR RATE LIMIT - Tipo: {$tipo} | " . 
                   ($tipo === 'IP' ? "IP: {$identificador}" : "Usuario: {$identificador}") . 
                   " | IP: {$ip} | Tiempo restante: " . self::formatearTiempoRestante($tiempoRestante);
        error_log($mensaje);
    }
    
    /**
     * Obtener estadísticas de intentos fallidos
     * @param int $horas Últimas X horas
     * @return array Estadísticas
     */
    public static function obtenerEstadisticas($horas = 24) {
        $conexion = self::getConexion();
        $tiempoLimite = date('Y-m-d H:i:s', time() - ($horas * 3600));
        
        // Total de intentos
        $queryTotal = "SELECT COUNT(*) as total FROM " . BD_ADMIN . ".usuarios_intentos_fallidos 
                       WHERE uif_fecha > '$tiempoLimite'";
        $total = mysqli_fetch_array(mysqli_query($conexion, $queryTotal), MYSQLI_BOTH)['total'];
        
        // IPs únicas
        $queryIPs = "SELECT COUNT(DISTINCT uif_ip) as total FROM " . BD_ADMIN . ".usuarios_intentos_fallidos 
                     WHERE uif_fecha > '$tiempoLimite'";
        $ipsUnicas = mysqli_fetch_array(mysqli_query($conexion, $queryIPs), MYSQLI_BOTH)['total'];
        
        // Top IPs atacantes
        $queryTopIPs = "SELECT uif_ip, COUNT(*) as intentos 
                        FROM " . BD_ADMIN . ".usuarios_intentos_fallidos 
                        WHERE uif_fecha > '$tiempoLimite' 
                        GROUP BY uif_ip 
                        ORDER BY intentos DESC 
                        LIMIT 10";
        $topIPs = [];
        $consultaTopIPs = mysqli_query($conexion, $queryTopIPs);
        while ($row = mysqli_fetch_array($consultaTopIPs, MYSQLI_BOTH)) {
            $topIPs[] = $row;
        }
        
        return [
            'total_intentos' => $total,
            'ips_unicas' => $ipsUnicas,
            'top_ips' => $topIPs,
            'periodo_horas' => $horas
        ];
    }
    
    /**
     * Limpiar intentos antiguos (para mantenimiento)
     * Elimina registros mayores a 30 días
     */
    public static function limpiarIntentosAntiguos() {
        $conexion = self::getConexion();
        $tiempoLimite = date('Y-m-d H:i:s', time() - (30 * 24 * 3600)); // 30 días
        
        $query = "DELETE FROM " . BD_ADMIN . ".usuarios_intentos_fallidos 
                  WHERE uif_fecha < '$tiempoLimite'";
        
        mysqli_query($conexion, $query);
        
        $registrosEliminados = mysqli_affected_rows($conexion);
        error_log("🧹 MANTENIMIENTO RATE LIMIT - Eliminados {$registrosEliminados} registros antiguos");
        
        return $registrosEliminados;
    }
}

