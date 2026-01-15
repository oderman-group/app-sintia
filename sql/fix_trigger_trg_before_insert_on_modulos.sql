-- ================================================
-- CORRECCIÓN DE COLLATION: trg_before_insert_on_modulos
-- Base de datos: mobiliar_sintia_admin
-- Problema reportado: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- Nota: Este trigger asigna NEW.created_by = CURRENT_USER() (cadena).
-- ================================================

-- Objetivo: Recrear el TRIGGER para fijar `collation_connection=utf8mb4_unicode_ci` (asigna texto) y evitar herencias inconsistentes.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - BEFORE INSERT en `modulos`.
-- - Asigna `created_by` con el usuario de BD actual (`CURRENT_USER()`).

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_sintia_admin`.`trg_before_insert_on_modulos`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar_production`@`%` TRIGGER `mobiliar_sintia_admin`.`trg_before_insert_on_modulos`
BEFORE INSERT ON `mobiliar_sintia_admin`.`modulos`
FOR EACH ROW
BEGIN
    SET NEW.created_by = CURRENT_USER();
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Se mantiene el DEFINER original: mobiliar_production@%
-- 3. Trigger BEFORE INSERT sobre mobiliar_sintia_admin.modulos
-- ================================================

