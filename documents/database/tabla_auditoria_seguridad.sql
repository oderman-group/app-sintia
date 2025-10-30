-- ================================================
-- TABLA DE AUDITORÍA DE SEGURIDAD
-- Registra todas las acciones sensibles del sistema
-- ================================================

CREATE TABLE IF NOT EXISTS `auditoria_seguridad` (
  `aud_id` int(11) NOT NULL AUTO_INCREMENT,
  `aud_usuario_id` varchar(10) DEFAULT NULL COMMENT 'ID del usuario que realiza la acción',
  `aud_accion` varchar(50) NOT NULL COMMENT 'Tipo de acción: LOGIN, LOGOUT, CREAR, EDITAR, ELIMINAR, etc.',
  `aud_modulo` varchar(100) NOT NULL COMMENT 'Módulo afectado: Usuarios, Estudiantes, Calificaciones, etc.',
  `aud_descripcion` varchar(500) NOT NULL COMMENT 'Descripción detallada de la acción',
  `aud_nivel` enum('INFO','WARNING','CRITICAL') DEFAULT 'INFO' COMMENT 'Nivel de severidad',
  `aud_ip` varchar(45) NOT NULL COMMENT 'Dirección IP del usuario',
  `aud_user_agent` varchar(255) DEFAULT NULL COMMENT 'User Agent del navegador',
  `aud_url` varchar(255) DEFAULT NULL COMMENT 'URL donde se ejecutó la acción',
  `aud_metodo` varchar(10) DEFAULT NULL COMMENT 'Método HTTP: GET, POST, etc.',
  `aud_datos_adicionales` text DEFAULT NULL COMMENT 'Datos adicionales en formato JSON',
  `aud_institucion` int(11) DEFAULT NULL COMMENT 'ID de la institución',
  `aud_year` int(4) DEFAULT NULL COMMENT 'Año académico',
  `aud_fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la acción',
  PRIMARY KEY (`aud_id`),
  KEY `idx_usuario` (`aud_usuario_id`),
  KEY `idx_accion` (`aud_accion`),
  KEY `idx_nivel` (`aud_nivel`),
  KEY `idx_fecha` (`aud_fecha`),
  KEY `idx_modulo` (`aud_modulo`),
  KEY `idx_institucion` (`aud_institucion`),
  KEY `idx_usuario_fecha` (`aud_usuario_id`, `aud_fecha`),
  KEY `idx_accion_fecha` (`aud_accion`, `aud_fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de auditoría de seguridad';

-- ================================================
-- ÍNDICES ADICIONALES PARA RENDIMIENTO
-- ================================================

-- Índice compuesto para búsquedas por institución y año
CREATE INDEX idx_institucion_year ON `auditoria_seguridad` (`aud_institucion`, `aud_year`, `aud_fecha`);

-- Índice para búsquedas por IP
CREATE INDEX idx_ip_fecha ON `auditoria_seguridad` (`aud_ip`, `aud_fecha`);

-- ================================================
-- NOTAS DE IMPLEMENTACIÓN
-- ================================================
-- 
-- Esta tabla debe crearse en: BD_ADMIN (mobiliar_sintia_admin_local)
--
-- Uso de espacio estimado:
-- - ~1KB por registro
-- - 1000 acciones/día = ~30 MB/mes
-- - Con limpieza de 90 días = ~90 MB
--
-- Mantenimiento recomendado:
-- - Ejecutar limpieza cada mes
-- - Mantener logs CRITICAL indefinidamente
-- - Eliminar INFO/WARNING > 90 días
--
-- ================================================

