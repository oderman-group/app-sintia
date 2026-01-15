-- ================================================
-- CORRECCIÓN DE COLLATION: disparador_calcular_definitiva_insert
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el TRIGGER para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en SELECT/WHERE internos.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE TRIGGER.

-- Qué hace:
-- - BEFORE INSERT en `academico_calificaciones`.
-- - Si `NEW.cal_nota` NO es NULL, consulta el valor (%) de la actividad y calcula `NEW.cal_nota_equivalente_cien`.
-- - Si `NEW.cal_nota` es NULL, asigna `NEW.cal_nota_equivalente_cien = 0`.

-- Establecer collation correcta antes de crear el trigger
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar trigger existente
DROP TRIGGER IF EXISTS `mobiliar_academic_prod`.`disparador_calcular_definitiva_insert`$$

-- Crear trigger con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` TRIGGER `mobiliar_academic_prod`.`disparador_calcular_definitiva_insert` 
BEFORE INSERT ON `mobiliar_academic_prod`.`academico_calificaciones` 
FOR EACH ROW 
BEGIN
    DECLARE definitiva DECIMAL(5,2);
    DECLARE acumulado DECIMAL(5,2);
    DECLARE suma_porcentaje DECIMAL(5,2);
    DECLARE porcentaje DECIMAL(5,2);
    DECLARE periodo INTEGER;
    DECLARE carga VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    
    -- hacemos operacion solo si la nota es diferente de null
    IF NEW.cal_nota IS NOT NULL THEN
        SELECT
            act_valor, act_periodo, act_id_carga 
        INTO porcentaje, periodo, carga
        FROM mobiliar_academic_prod.academico_actividades act
        WHERE act_id COLLATE utf8mb4_unicode_ci = NEW.cal_id_actividad COLLATE utf8mb4_unicode_ci
        AND act.institucion = NEW.institucion
        AND act.year COLLATE utf8mb4_unicode_ci = NEW.year COLLATE utf8mb4_unicode_ci
        LIMIT 1;
        
        SET NEW.cal_nota_equivalente_cien = NEW.cal_nota * (porcentaje / 100);
    ELSE
        SET NEW.cal_nota_equivalente_cien = 0;
    END IF;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. La variable carga tiene collation explícita
-- 3. Las comparaciones WHERE usan COLLATE utf8mb4_unicode_ci explícito
-- 4. Se mantiene el DEFINER original: mobiliar@localhost
-- 5. Este trigger se ejecuta BEFORE INSERT en academico_calificaciones
-- ================================================
