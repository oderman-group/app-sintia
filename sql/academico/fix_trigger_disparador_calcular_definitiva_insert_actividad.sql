-- ================================================
-- CORRECCIÓN DE COLLATION: disparador_calcular_definitiva_insert_actividad
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- NOTA: Este es el trigger que causa el error de collation al insertar actividades
-- ================================================

-- Objetivo: Recrear el TRIGGER que llama el procedimiento de recálculo, para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - AFTER INSERT en `academico_actividades`.
-- - Llama `recalcular_calificacion_definitiva_actividades(NEW.act_id, NEW.act_valor, NEW.act_estado)` para recalcular equivalencias.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_academic_prod`.`disparador_calcular_definitiva_insert_actividad`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_academic_prod`.`disparador_calcular_definitiva_insert_actividad` 
AFTER INSERT ON `mobiliar_academic_prod`.`academico_actividades` 
FOR EACH ROW 
BEGIN
    CALL mobiliar_academic_prod.recalcular_calificacion_definitiva_actividades(
        NEW.act_id, 
        NEW.act_valor,
        NEW.act_estado
    );
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Este trigger llama al procedimiento recalcular_calificacion_definitiva_actividades
-- 3. El procedimiento ya fue corregido con collation explícita
-- 4. Se mantiene el DEFINER original: mobiliar@localhost
-- 5. Este trigger se ejecuta AFTER INSERT en academico_actividades
-- 6. IMPORTANTE: Este es el trigger que estaba causando el error de collation
-- ================================================
