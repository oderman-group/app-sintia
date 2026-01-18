-- ================================================
-- CORRECCIÓN DE COLLATION: incrementar_actualizaciones_clases_feedback
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el TRIGGER para fijar `collation_connection=utf8mb4_unicode_ci` (incremento numérico) y evitar herencias inconsistentes.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - BEFORE UPDATE en `clases_feedback`.
-- - Incrementa `NEW.fcls_actualizaciones` en 1 usando el valor anterior.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_academic_prod`.`incrementar_actualizaciones_clases_feedback`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_academic_prod`.`incrementar_actualizaciones_clases_feedback` 
BEFORE UPDATE ON `mobiliar_academic_prod`.`clases_feedback` 
FOR EACH ROW 
BEGIN
    SET NEW.fcls_actualizaciones = OLD.fcls_actualizaciones + 1;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Este trigger solo incrementa un contador numérico, no requiere comparaciones con COLLATE
-- 3. Se mantiene el DEFINER original: mobiliar@localhost
-- 4. Este trigger se ejecuta BEFORE UPDATE en clases_feedback
-- 5. Incrementa el contador de actualizaciones en 1
-- ================================================
