-- ================================================
-- CORRECCIÓN DE COLLATION: calcular_valor_peridos_cruso
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Garantiza que existan registros en `academico_grados_periodos` para cada período de un curso/grado.
-- - Si faltan períodos, distribuye el porcentaje restante (100 - suma actual) entre los períodos no registrados.
-- Parámetros:
-- - p_institucion (INT): Institución.
-- - p_year (CHAR(4)): Año académico.
-- - p_curso (VARCHAR(45)): Código del curso/grado.

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`calcular_valor_peridos_cruso`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`mobiliar_production`@`%` PROCEDURE `mobiliar_academic_prod`.`calcular_valor_peridos_cruso`(
    p_institucion INT,
    p_year CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    p_curso VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE max_periodos INT;                 -- numero de periodos que tiene la intitucion
    DECLARE valor_porcentaje INT DEFAULT 100; -- procetaje faltante para registrar incialmente estara en 100%
    DECLARE valor_periodo INT;                -- valor que tendra el periodo a consultar
    DECLARE max_gvp_id INT;                   -- valor del consecutivo a insertar
    DECLARE valor_porcentaje_registrado INT;
    DECLARE valor_porcentaje_acumulado INT DEFAULT 0;
    DECLARE contador INT DEFAULT 1;           -- contador de los periodos registridos
    DECLARE es_null INT DEFAULT 0;            -- valida si ya un periodos esta regstrado
    DECLARE periodos_registrados INT DEFAULT 0;-- cuenta los periodos registrados

    -- Obtener el número de períodos máximos
    SELECT conf_periodos_maximos INTO max_periodos
    FROM mobiliar_sintia_admin.configuracion
    WHERE conf_id_institucion = p_institucion
    AND conf_agno COLLATE utf8mb4_unicode_ci = p_year;

    -- Verificar si se obtuvo un valor válido
    IF max_periodos IS NULL OR max_periodos <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No hay períodos máximos configurados o el valor es inválido.';
    END IF;

    -- Obtener el último gvp_id numérico
    SELECT COALESCE(MAX(CAST(gvp_id AS UNSIGNED)), 0) + 1 INTO max_gvp_id
    FROM mobiliar_academic_prod.academico_grados_periodos;

    -- Bucle consultar si hay valores registrados
    WHILE contador <= max_periodos DO

        -- Buscar si ya existe el período y su porcentaje registrado
        SELECT COUNT(*), IFNULL(gvp_valor, 0)
        INTO es_null, valor_porcentaje_registrado
        FROM mobiliar_academic_prod.academico_grados_periodos
        WHERE institucion = p_institucion
        AND year COLLATE utf8mb4_unicode_ci = p_year
        AND gvp_grado COLLATE utf8mb4_unicode_ci = p_curso
        AND gvp_periodo = contador
        LIMIT 1;

        IF es_null > 0 THEN
            SET periodos_registrados = periodos_registrados +1;
            SET valor_porcentaje_acumulado = valor_porcentaje_acumulado + valor_porcentaje_registrado;
        END IF;

        SET contador = contador + 1;

    END WHILE;
    
    -- Ajustar el porcentaje restante
    SET valor_porcentaje = valor_porcentaje - valor_porcentaje_acumulado;

    -- Evitar división por cero
    IF (max_periodos - periodos_registrados) > 0 THEN
        -- Calcular valor del período
        SET valor_periodo = valor_porcentaje / (max_periodos - periodos_registrados);
    ELSE
        SET valor_periodo = 0;
    END IF;

    -- Reiniciar contador
    SET contador = 1;

    -- Bucle para insertar los períodos
    WHILE contador <= max_periodos DO

        -- verifica si el periodo ya existe
        SELECT COUNT(*) INTO es_null
        FROM mobiliar_academic_prod.academico_grados_periodos
        WHERE institucion = p_institucion
        AND year COLLATE utf8mb4_unicode_ci = p_year
        AND gvp_grado COLLATE utf8mb4_unicode_ci = p_curso
        AND gvp_periodo = contador
        LIMIT 1;

        -- Si no existe, restar el porcentaje
        IF es_null = 0  THEN
            -- Insertar el período
            INSERT INTO mobiliar_academic_prod.academico_grados_periodos (
                gvp_id,
                gvp_grado,
                gvp_periodo,
                gvp_valor,
                institucion,
                year
            )
            VALUES (
                max_gvp_id,
                p_curso,
                contador,
                valor_periodo,
                p_institucion,
                p_year
            );
            -- Incrementar el ID y el contador
            SET max_gvp_id = max_gvp_id + 1;
        END IF;
        
        -- Incrementar contador
        SET contador = contador + 1;

    END WHILE;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Los parámetros VARCHAR/CHAR ahora tienen collation explícita
-- 3. Las comparaciones WHERE usan COLLATE utf8mb4_unicode_ci explícito
-- 4. Se mantiene el DEFINER original: mobiliar_production@%
-- ================================================
