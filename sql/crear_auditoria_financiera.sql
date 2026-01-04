-- ================================================
-- SISTEMA DE AUDITORÍA FINANCIERA
-- Tabla para registrar todos los cambios en el módulo financiero
-- ================================================

-- Crear tabla de auditoría financiera
CREATE TABLE IF NOT EXISTS `auditoria_financiera` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabla_afectada` VARCHAR(100) NOT NULL COMMENT 'Nombre de la tabla afectada (ej: finanzas_cuentas)',
  `registro_id` VARCHAR(50) NOT NULL COMMENT 'ID del registro que cambió',
  `accion` ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL COMMENT 'Tipo de acción realizada',
  `valor_anterior` JSON DEFAULT NULL COMMENT 'Datos antes del cambio (JSON completo)',
  `valor_nuevo` JSON DEFAULT NULL COMMENT 'Datos después del cambio (JSON completo)',
  `cambios_detectados` JSON DEFAULT NULL COMMENT 'Solo campos modificados para UPDATE (JSON con cambios específicos)',
  `usuario_db` VARCHAR(100) DEFAULT NULL COMMENT 'Usuario de MySQL que ejecutó el cambio (USER())',
  `usuario_app` VARCHAR(50) DEFAULT NULL COMMENT 'Usuario de SINTIA que realizó el cambio (si aplica)',
  `contexto` ENUM('APP', 'BD_DIRECTA') DEFAULT 'BD_DIRECTA' COMMENT 'Contexto: desde aplicación o directamente en BD',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Dirección IP del usuario (si aplica)',
  `institucion` INT DEFAULT NULL COMMENT 'ID de la institución',
  `year` INT DEFAULT NULL COMMENT 'Año académico',
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del cambio',
  PRIMARY KEY (`id`),
  KEY `idx_tabla_registro` (`tabla_afectada`, `registro_id`, `fecha`),
  KEY `idx_usuario_db_fecha` (`usuario_db`, `fecha`),
  KEY `idx_usuario_app_fecha` (`usuario_app`, `fecha`),
  KEY `idx_institucion_year` (`institucion`, `year`, `fecha`),
  KEY `idx_accion_fecha` (`accion`, `fecha`),
  KEY `idx_contexto_fecha` (`contexto`, `fecha`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de auditoría de cambios en el módulo financiero';

-- ================================================
-- NOTAS DE IMPLEMENTACIÓN
-- ================================================
-- 
-- Esta tabla debe crearse en: BD_FINANCIERA
--
-- IMPORTANTE - Permisos:
-- El usuario de la aplicación (app_user) debe tener SOLO permisos INSERT
-- NO debe tener permisos UPDATE ni DELETE sobre esta tabla
--
-- Uso de espacio estimado:
-- - ~2-5KB por registro (dependiendo de tamaño de JSON)
-- - 1000 cambios/día = ~60-150 MB/mes
-- - Considerar particionamiento por fecha si el volumen crece significativamente
--
-- Mantenimiento recomendado:
-- - Monitorear tamaño de tabla regularmente
-- - Considerar archivar logs antiguos (> 2 años) si es necesario
-- - Los logs críticos (DELETE físicos, cambios grandes) deben mantenerse indefinidamente
--
-- ================================================

