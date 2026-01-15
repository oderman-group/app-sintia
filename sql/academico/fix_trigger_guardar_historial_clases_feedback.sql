-- ================================================
-- CORRECCIÓN DE COLLATION: guardar_historial_clases_feedback
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el TRIGGER para fijar `collation_connection=utf8mb4_unicode_ci` (aunque solo manipula JSON) y evitar herencias inconsistentes.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - BEFORE UPDATE en `clases_feedback`.
-- - Anexa un snapshot del registro anterior (OLD.*) al JSON `NEW.fcls_historial` para mantener historial de cambios.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_academic_prod`.`guardar_historial_clases_feedback`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_academic_prod`.`guardar_historial_clases_feedback` 
BEFORE UPDATE ON `mobiliar_academic_prod`.`clases_feedback` 
FOR EACH ROW 
BEGIN
    DECLARE historial_json JSON;
    
    SET historial_json = JSON_ARRAY();
    
    IF NEW.fcls_historial IS NOT NULL THEN
        SET historial_json = JSON_ARRAY_APPEND(historial_json, '$', NEW.fcls_historial);
    END IF;
    
    SET NEW.fcls_historial = JSON_ARRAY_APPEND(historial_json, '$', JSON_OBJECT(
        'fcls_id_clase', JSON_QUOTE(OLD.fcls_id_clase),
        'fcls_id_institucion', JSON_QUOTE(OLD.fcls_id_institucion),
        'fcls_usuario', JSON_QUOTE(OLD.fcls_usuario),
        'fcls_comentario', JSON_QUOTE(OLD.fcls_comentario),
        'fcls_star', JSON_QUOTE(OLD.fcls_star),
        'fcls_fecha_actualizacion', JSON_QUOTE(OLD.fcls_fecha_actualizacion)
    ));
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Este trigger trabaja con JSON, no requiere comparaciones con COLLATE explícito
-- 3. Se mantiene el DEFINER original: mobiliar@localhost
-- 4. Este trigger se ejecuta BEFORE UPDATE en clases_feedback
-- 5. Guarda el historial de cambios en formato JSON
-- ================================================
