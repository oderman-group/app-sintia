-- ================================================
-- CORRECCIÓN DE COLLATION: recalcular_calificacion_definitiva_por_curso
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Busca las actividades activas asociadas a las cargas del curso+grupo (y período).
-- - Para cada actividad, invoca `recalcular_calificacion_definitiva_actividades(...)` para recalcular `cal_nota_equivalente_cien`.
-- Parámetros:
-- - curso (VARCHAR(45)): Código del curso/grado.
-- - grupo (VARCHAR(45)): Código del grupo.
-- - periodo (INT): Período.
-- - id_institucion (INT): Institución.
-- - id_year (CHAR(4)): Año académico.

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_por_curso`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` PROCEDURE `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_por_curso`(
    IN curso VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN grupo VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN periodo INT,
    IN id_institucion INT,
    IN id_year CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE actividad VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    DECLARE porcentaje DECIMAL(5,2);
    DECLARE done INT DEFAULT 0;
    
    DECLARE cur CURSOR FOR
        SELECT
            act_id,
            act_valor
        FROM mobiliar_academic_prod.academico_actividades act
        INNER JOIN mobiliar_academic_prod.academico_cargas car
            ON car.car_id COLLATE utf8mb4_unicode_ci = act.act_id_carga COLLATE utf8mb4_unicode_ci
            AND car.institucion = act.institucion
            AND car.year COLLATE utf8mb4_unicode_ci = act.year COLLATE utf8mb4_unicode_ci
            AND car.car_activa = 1
        WHERE
            car.year COLLATE utf8mb4_unicode_ci = id_year
            AND car.institucion = id_institucion
            AND car.car_curso COLLATE utf8mb4_unicode_ci = curso
            AND car.car_grupo COLLATE utf8mb4_unicode_ci = grupo
            AND act.act_periodo = periodo
            AND act.act_estado = 1;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO actividad, porcentaje;
        IF done THEN
            LEAVE read_loop;
        END IF;
        -- Acumular mensajes en la variable resultado
        -- SET resultado = CONCAT(resultado, 'Actividad:', actividad, ' con porcentaje:', porcentaje, '%; ');
        -- Aquí iría la lógica de actualización o procesamiento
        CALL mobiliar_academic_prod.recalcular_calificacion_definitiva_actividades(actividad, porcentaje, 1);
    END LOOP;
    CLOSE cur;
    -- Mostrar el resultado acumulado
    -- SELECT resultado;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Los parámetros VARCHAR/CHAR tienen collation explícita (curso, grupo, id_year)
-- 3. La variable actividad tiene collation explícita
-- 4. Las comparaciones en JOIN y WHERE usan COLLATE utf8mb4_unicode_ci explícito
-- 5. Se mantiene el DEFINER original: mobiliar@localhost
-- 6. Este procedimiento llama a recalcular_calificacion_definitiva_actividades (ya corregido)
-- ================================================
