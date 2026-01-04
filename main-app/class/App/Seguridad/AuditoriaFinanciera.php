<?php
/**
 * Clase para registrar cambios en el módulo financiero
 * Complementa los triggers de base de datos agregando información
 * de contexto de la aplicación (usuario, IP, etc.)
 * 
 * @package App\Seguridad
 */

require_once(ROOT_PATH."/main-app/class/Conexion.php");

class AuditoriaFinanciera {
    
    /**
     * Registra un cambio en la tabla de auditoría financiera
     * 
     * @param string $tabla Nombre de la tabla afectada
     * @param string $registroId ID del registro que cambió
     * @param string $accion Acción realizada (INSERT, UPDATE, DELETE)
     * @param array|null $valorAnterior Datos antes del cambio
     * @param array|null $valorNuevo Datos después del cambio
     * @param array|null $camposModificados Solo campos modificados (para UPDATE)
     * @return bool true si se registró correctamente, false en caso de error
     */
    public static function registrarCambio(
        string $tabla,
        string $registroId,
        string $accion,
        ?array $valorAnterior = null,
        ?array $valorNuevo = null,
        ?array $camposModificados = null
    ): bool {
        try {
            $conexionPDO = Conexion::newConnection('PDO');
            $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Obtener contexto de la aplicación
            $usuarioApp = isset($_SESSION['id']) ? $_SESSION['id'] : null;
            $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
            $institucion = isset($GLOBALS['config']['conf_id_institucion']) ? $GLOBALS['config']['conf_id_institucion'] : null;
            $year = isset($_SESSION['bd']) ? $_SESSION['bd'] : null;
            
            // Extraer institucion y year de los datos si no están disponibles en contexto
            if ($institucion === null && $valorNuevo !== null && isset($valorNuevo['institucion'])) {
                $institucion = $valorNuevo['institucion'];
            } elseif ($institucion === null && $valorAnterior !== null && isset($valorAnterior['institucion'])) {
                $institucion = $valorAnterior['institucion'];
            }
            
            if ($year === null && $valorNuevo !== null && isset($valorNuevo['year'])) {
                $year = $valorNuevo['year'];
            } elseif ($year === null && $valorAnterior !== null && isset($valorAnterior['year'])) {
                $year = $valorAnterior['year'];
            }
            
            // Convertir arrays a JSON
            $valorAnteriorJson = $valorAnterior !== null ? json_encode($valorAnterior, JSON_UNESCAPED_UNICODE) : null;
            $valorNuevoJson = $valorNuevo !== null ? json_encode($valorNuevo, JSON_UNESCAPED_UNICODE) : null;
            $cambiosDetectadosJson = $camposModificados !== null && !empty($camposModificados) 
                ? json_encode($camposModificados, JSON_UNESCAPED_UNICODE) 
                : null;
            
            // Preparar consulta INSERT
            // NOTA: usuario_db se llenará desde los triggers, pero aquí podemos sobreescribir si es necesario
            // Para cambios desde la app, contexto = 'APP'
            $sql = "INSERT INTO ".BD_FINANCIERA.".auditoria_financiera (
                tabla_afectada,
                registro_id,
                accion,
                valor_anterior,
                valor_nuevo,
                cambios_detectados,
                usuario_app,
                contexto,
                ip_address,
                institucion,
                year
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'APP', ?, ?, ?)";
            
            $stmt = $conexionPDO->prepare($sql);
            $stmt->bindParam(1, $tabla, PDO::PARAM_STR);
            $stmt->bindParam(2, $registroId, PDO::PARAM_STR);
            $stmt->bindParam(3, $accion, PDO::PARAM_STR);
            $stmt->bindParam(4, $valorAnteriorJson, PDO::PARAM_STR);
            $stmt->bindParam(5, $valorNuevoJson, PDO::PARAM_STR);
            $stmt->bindParam(6, $cambiosDetectadosJson, PDO::PARAM_STR);
            $stmt->bindParam(7, $usuarioApp, PDO::PARAM_STR);
            $stmt->bindParam(8, $ipAddress, PDO::PARAM_STR);
            $stmt->bindParam(9, $institucion, PDO::PARAM_INT);
            $stmt->bindParam(10, $year, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return true;
            
        } catch (Exception $e) {
            // Log error pero no interrumpir la operación principal
            error_log("Error en AuditoriaFinanciera::registrarCambio: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Registra una inserción
     * 
     * @param string $tabla Nombre de la tabla
     * @param string $registroId ID del nuevo registro
     * @param array $valorNuevo Datos del nuevo registro
     * @return bool
     */
    public static function registrarInsercion(
        string $tabla,
        string $registroId,
        array $valorNuevo
    ): bool {
        // Los INSERTs se capturan automáticamente con triggers AFTER INSERT
        // Este método es opcional, se puede usar si se necesita agregar contexto adicional
        // antes de que se ejecute el INSERT
        return true; // Los triggers ya lo capturan
    }
    
    /**
     * Registra una actualización
     * 
     * @param string $tabla Nombre de la tabla
     * @param string $registroId ID del registro actualizado
     * @param array $valorAnterior Datos antes del cambio
     * @param array $valorNuevo Datos después del cambio
     * @param array|null $camposModificados Solo campos modificados (opcional)
     * @return bool
     */
    public static function registrarActualizacion(
        string $tabla,
        string $registroId,
        array $valorAnterior,
        array $valorNuevo,
        ?array $camposModificados = null
    ): bool {
        // Los UPDATEs se capturan automáticamente con triggers BEFORE UPDATE
        // Este método complementa agregando contexto de aplicación (usuario_app, ip_address)
        return self::registrarCambio($tabla, $registroId, 'UPDATE', $valorAnterior, $valorNuevo, $camposModificados);
    }
    
    /**
     * Registra una eliminación (soft delete o física)
     * 
     * @param string $tabla Nombre de la tabla
     * @param string $registroId ID del registro eliminado
     * @param array $valorAnterior Datos del registro antes de eliminar
     * @return bool
     */
    public static function registrarEliminacion(
        string $tabla,
        string $registroId,
        array $valorAnterior
    ): bool {
        // Los DELETEs se capturan automáticamente con triggers BEFORE DELETE
        // Este método complementa agregando contexto de aplicación
        // Para soft deletes (UPDATE que cambia is_deleted/fcu_anulado), usar registrarActualizacion
        return self::registrarCambio($tabla, $registroId, 'DELETE', $valorAnterior, null, null);
    }
}

