-- ================================================
-- CORRECCIÓN DE COLLATION: recalcular_calificacion_definitiva_por_institucion_periodo
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Recorre curso/grupo distintos presentes en `academico_cargas` para una institución y año.
-- - Para cada curso/grupo, invoca `recalcular_calificacion_definitiva_por_curso(...)` en el período indicado.
-- Parámetros:
-- - periodo (INT): Período.
-- - id_institucion (INT): Institución.
-- - id_year (CHAR(4)): Año académico.

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_por_institucion_periodo`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` PROCEDURE `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_por_institucion_periodo`(
    IN periodo INT,
    IN id_institucion INT,
    IN id_year CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE curso VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    DECLARE grupo VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    DECLARE done INT DEFAULT 0;
    
    DECLARE cur CURSOR FOR
       SELECT DISTINCT car_curso, car_grupo 
       FROM mobiliar_academic_prod.academico_cargas car
       WHERE institucion = id_institucion
       AND year COLLATE utf8mb4_unicode_ci = id_year
       ORDER BY car_curso;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO curso, grupo;
        IF done THEN
            LEAVE read_loop;
        END IF;
        -- Acumular mensajes en la variable resultado
        -- Aquí iría la lógica de actualización o procesamiento
        CALL mobiliar_academic_prod.recalcular_calificacion_definitiva_por_curso(curso, grupo, periodo, id_institucion, id_year);
    END LOOP;
    CLOSE cur;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. El parámetro id_year tiene collation explícita
-- 3. Las variables curso y grupo tienen collation explícita
-- 4. La comparación WHERE usa COLLATE utf8mb4_unicode_ci explícito
-- 5. Se mantiene el DEFINER original: mobiliar@localhost
-- 6. Este procedimiento llama a recalcular_calificacion_definitiva_por_curso (ya corregido)
-- ================================================
