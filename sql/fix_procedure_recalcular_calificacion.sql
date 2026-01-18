-- Script para recrear el procedimiento almacenado con la collation correcta
-- Este script debe ejecutarse en producción después de establecer la collation de conexión
--
-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos para DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Recalcula `cal_nota_equivalente_cien` para todas las calificaciones asociadas a una actividad.
-- - Si la actividad está activa (p_estado=1), calcula: cal_nota_equivalente_cien = cal_nota * (p_valor/100).
-- - Si está inactiva, pone cal_nota_equivalente_cien = 0.
-- Parámetros:
-- - p_id_actividad (VARCHAR): ID de la actividad.
-- - p_valor (DECIMAL): Porcentaje/valor de la actividad.
-- - p_estado (INT): Estado (1 activo, 0 inactivo).

-- Paso 1: Eliminar el procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_actividades`;

-- Paso 2: Establecer la collation de conexión a utf8mb4_unicode_ci
SET collation_connection = 'utf8mb4_unicode_ci';

-- Paso 3: Crear el procedimiento con la collation correcta
DELIMITER $$

CREATE DEFINER=`mobiliar`@`localhost` PROCEDURE `mobiliar_academic_prod`.`recalcular_calificacion_definitiva_actividades`(
    IN p_id_actividad VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
		IN p_valor DECIMAL(5,2),
		IN p_estado INT
)
BEGIN	
    DECLARE registro  INT;
		DECLARE nota DECIMAL(5,2);		
		DECLARE done INT DEFAULT 0;
		DECLARE cur CURSOR FOR
			SELECT id_nuevo, cal_nota 
			FROM mobiliar_academic_prod.academico_calificaciones cal
			WHERE cal.cal_id_actividad COLLATE utf8mb4_unicode_ci = p_id_actividad;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
		OPEN cur;		
		read_loop: LOOP
        FETCH cur INTO registro, nota;		
        IF done THEN
            LEAVE read_loop;
        END IF;
				-- actualizamos los registros 
        IF p_estado = 1 THEN 
					UPDATE mobiliar_academic_prod.academico_calificaciones 
					SET cal_nota_equivalente_cien = nota * (p_valor / 100)
					WHERE id_nuevo = registro;
				ELSE
				   UPDATE mobiliar_academic_prod.academico_calificaciones 
					SET cal_nota_equivalente_cien = 0
					WHERE id_nuevo = registro;
				END IF;
		END LOOP;
    CLOSE cur;
END$$

DELIMITER ;
