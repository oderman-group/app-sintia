-- ================================================
-- CORRECCIÓN DE COLLATION: recalcular_calificacion_definitiva_por_carga
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Recorre las actividades activas de una carga y período.
-- - Para cada actividad, invoca `recalcular_calificacion_definitiva_actividades(...)` para recalcular `cal_nota_equivalente_cien`.
-- Parámetros:
-- - carga (VARCHAR(45)): ID de la carga.
-- - periodo (INT): Período.
-- - id_institucion (INT): Institución.
-- - id_year (CHAR(4)): Año académico.

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_por_carga`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` PROCEDURE `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_por_carga`(
    IN carga VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN periodo INT,
    IN id_institucion INT,
    IN id_year CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE actividad VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    DECLARE porcentaje DECIMAL(5,2);
    DECLARE resultado TEXT DEFAULT '';
    DECLARE done INT DEFAULT 0;
    
    DECLARE cur CURSOR FOR
        SELECT
            act_id,
            act_valor
        FROM mobiliar_academic_prod.academico_actividades act
        WHERE
            year COLLATE utf8mb4_unicode_ci = id_year
            AND institucion = id_institucion
            AND act_id_carga COLLATE utf8mb4_unicode_ci = carga
            AND act_periodo = periodo
            AND act.act_estado = 1;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO actividad, porcentaje;
        IF done THEN
            LEAVE read_loop;
        END IF;
        -- Acumular mensajes en la variable resultado
        -- Aquí iría la lógica de actualización o procesamiento
        CALL mobiliar_academic_prod.recalcular_calificacion_definitiva_actividades(actividad, porcentaje, 1);
    END LOOP;
    CLOSE cur;
    -- Mostrar el resultado acumulado
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Los parámetros VARCHAR/CHAR tienen collation explícita (carga, id_year)
-- 3. La variable actividad tiene collation explícita
-- 4. Las comparaciones WHERE usan COLLATE utf8mb4_unicode_ci explícito
-- 5. Se mantiene el DEFINER original: mobiliar@localhost
-- 6. Este procedimiento llama a recalcular_calificacion_definitiva_actividades (ya corregido)
-- ================================================
