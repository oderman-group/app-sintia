-- ================================================
-- CORRECCIÓN DE COLLATION: calcular_valor_peridos_institucion
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Recorre los cursos/grados distintos presentes en `academico_cargas` para una institución y año.
-- - Para cada curso, invoca `calcular_valor_peridos_cruso(...)` para asegurar/crear la distribución de períodos.
-- Parámetros:
-- - id_institucion (INT): Institución.
-- - id_year (CHAR(4)): Año académico.

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`calcular_valor_peridos_institucion`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`mobiliar_production`@`%` PROCEDURE `mobiliar_academic_prod`.`calcular_valor_peridos_institucion`(
    IN id_institucion INT,
    IN id_year CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE curso VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    DECLARE done INT DEFAULT 0;

    DECLARE cur CURSOR FOR
       SELECT DISTINCT car_curso 
       FROM mobiliar_academic_prod.academico_cargas car
       WHERE institucion = id_institucion
       AND year COLLATE utf8mb4_unicode_ci = id_year
       ORDER BY car_curso;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO curso;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Crear valores de periodos si no existen
        CALL calcular_valor_peridos_cruso(id_institucion, id_year, curso);

    END LOOP;
    CLOSE cur;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. El parámetro id_year tiene collation explícita
-- 3. La variable curso tiene collation explícita
-- 4. La comparación WHERE usa COLLATE utf8mb4_unicode_ci explícito
-- 5. Se mantiene el DEFINER original: mobiliar_production@%
-- 6. Este procedimiento llama a calcular_valor_peridos_cruso (ya corregido)
-- ================================================
