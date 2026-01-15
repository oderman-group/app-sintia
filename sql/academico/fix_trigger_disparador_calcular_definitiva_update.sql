-- ================================================
-- CORRECCIÓN DE COLLATION: disparador_calcular_definitiva_update
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el TRIGGER para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en SELECT/WHERE internos.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - BEFORE UPDATE en `academico_calificaciones`.
-- - Si `NEW.cal_nota` es NULL => `NEW.cal_nota_equivalente_cien = 0`.
-- - Si la nota/equivalente cambia, consulta la actividad; si está inactiva => 0, si está activa => recalcula `NEW.cal_nota_equivalente_cien`.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_academic_prod`.`disparador_calcular_definitiva_update`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_academic_prod`.`disparador_calcular_definitiva_update` 
BEFORE UPDATE ON `mobiliar_academic_prod`.`academico_calificaciones` 
FOR EACH ROW 
BEGIN
    DECLARE porcentaje DECIMAL(5,2);
    DECLARE periodo INTEGER;
    DECLARE estado INTEGER;
    DECLARE carga VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    
    -- validamos que la nota no este null
    IF NEW.cal_nota IS NULL THEN
        SET NEW.cal_nota_equivalente_cien = 0;
    ELSE
        -- hacemos operacion solo si la nota cambia de valor o viene de un vano viene null a uno difernete o se modifique el calculo de nota equivalente
        IF (OLD.cal_nota <> NEW.cal_nota) OR (OLD.cal_nota IS NULL AND NEW.cal_nota IS NOT NULL) OR (OLD.cal_nota_equivalente_cien <> NEW.cal_nota_equivalente_cien) THEN
            SELECT
                act_valor, act_periodo, act_id_carga, act_estado 
            INTO porcentaje, periodo, carga, estado
            FROM mobiliar_academic_prod.academico_actividades act
            WHERE act_id COLLATE utf8mb4_unicode_ci = NEW.cal_id_actividad COLLATE utf8mb4_unicode_ci
            AND act.institucion = NEW.institucion
            AND act.year COLLATE utf8mb4_unicode_ci = NEW.year COLLATE utf8mb4_unicode_ci
            LIMIT 1;
            
            IF estado = 0 THEN
                SET NEW.cal_nota_equivalente_cien = 0;
            ELSE
                SET NEW.cal_nota_equivalente_cien = NEW.cal_nota * (porcentaje / 100);
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. La variable carga tiene collation explícita
-- 3. Las comparaciones WHERE usan COLLATE utf8mb4_unicode_ci explícito
-- 4. Se mantiene el DEFINER original: mobiliar@localhost
-- 5. Este trigger se ejecuta BEFORE UPDATE en academico_calificaciones
-- ================================================
