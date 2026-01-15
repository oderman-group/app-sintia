-- ================================================
-- CORRECCIÓN DE COLLATION: disparador_calcular_definitiva_update_actividad
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el TRIGGER que llama el procedimiento de recálculo, para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - AFTER UPDATE en `academico_actividades`.
-- - Si cambia `act_valor` o `act_estado`, llama `recalcular_calificacion_definitiva_actividades(...)` para recalcular equivalencias.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_academic_prod`.`disparador_calcular_definitiva_update_actividad`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_academic_prod`.`disparador_calcular_definitiva_update_actividad` 
AFTER UPDATE ON `mobiliar_academic_prod`.`academico_actividades` 
FOR EACH ROW 
BEGIN
    IF OLD.act_valor <> NEW.act_valor OR OLD.act_estado <> NEW.act_estado THEN
        CALL mobiliar_academic_prod.recalcular_calificacion_definitiva_actividades(
            NEW.act_id, 
            NEW.act_valor, 
            NEW.act_estado
        );
    END IF;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Este trigger llama al procedimiento recalcular_calificacion_definitiva_actividades
-- 3. El procedimiento ya fue corregido con collation explícita
-- 4. Se mantiene el DEFINER original: mobiliar@localhost
-- 5. Este trigger se ejecuta AFTER UPDATE en academico_actividades
-- 6. Solo se ejecuta si cambian act_valor o act_estado
-- ================================================
