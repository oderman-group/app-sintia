-- ================================================
-- CORRECCIÓN DE COLLATION: incrementar_actualizaciones_general_resultados
-- Base de datos: mobiliar_sintia_admin
-- Problema reportado: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- Nota: Este trigger solo incrementa un contador numérico, pero lo recreamos
-- para que no herede collation_connection incorrecta.
-- ================================================

-- Objetivo: Recrear el TRIGGER para fijar `collation_connection=utf8mb4_unicode_ci` y mantener consistencia en `mobiliar_sintia_admin`.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - BEFORE UPDATE en `general_resultados`.
-- - Incrementa `resg_actualizaciones` en 1.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_sintia_admin`.`incrementar_actualizaciones_general_resultados`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_sintia_admin`.`incrementar_actualizaciones_general_resultados`
BEFORE UPDATE ON `mobiliar_sintia_admin`.`general_resultados`
FOR EACH ROW
BEGIN
  SET NEW.resg_actualizaciones = OLD.resg_actualizaciones + 1;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Se mantiene el DEFINER original: mobiliar@localhost
-- 3. Trigger BEFORE UPDATE sobre mobiliar_sintia_admin.general_resultados
-- ================================================

