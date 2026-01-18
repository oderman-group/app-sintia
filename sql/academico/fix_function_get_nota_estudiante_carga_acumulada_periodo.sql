-- ================================================
-- CORRECCIÓN DE COLLATION: get_nota_estudiante_carga_acumulada_periodo
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la FUNCTION para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en comparaciones de texto.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE FUNCTION.

-- Qué hace:
-- - Retorna el promedio (AVG) de `bol_nota` de un estudiante en una carga, acumulado hasta un período (<= periodo_select).
-- - La data proviene de la vista `vista_carga_acumulada_estudiante`.
-- Parámetros:
-- - carga (VARCHAR(45)): ID de la carga.
-- - id_estudiante (VARCHAR(45)): Código/ID del estudiante.
-- - periodo_select (INT): Período máximo para acumular.
-- - id_institucion (INT): Institución.
-- - id_year (CHAR(4)): Año académico.
-- Retorna:
-- - DECIMAL(5,4): Promedio acumulado (puede ser NULL si no hay datos).

-- Establecer collation correcta antes de crear la función
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar función existente
DROP FUNCTION IF EXISTS `mobiliar_academic_prod`.`get_nota_estudiante_carga_acumulada_periodo`$$

-- Crear función con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` FUNCTION `mobiliar_academic_prod`.`get_nota_estudiante_carga_acumulada_periodo`(
    carga VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    id_estudiante VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    periodo_select INT,
    id_institucion INT,
    id_year CHAR(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) RETURNS DECIMAL(5,4)
BEGIN
    DECLARE nota_acumulada DECIMAL(5,4);
    
    SELECT AVG(bol_nota)
    INTO nota_acumulada
    FROM mobiliar_academic_prod.vista_carga_acumulada_estudiante
    WHERE institucion = id_institucion
    AND year COLLATE utf8mb4_unicode_ci = id_year
    AND periodo <= periodo_select
    AND est COLLATE utf8mb4_unicode_ci = id_estudiante
    AND bol_carga COLLATE utf8mb4_unicode_ci = carga
    GROUP BY institucion, est, mat_grado, mat_grupo, bol_carga;
    
    RETURN nota_acumulada;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Los parámetros VARCHAR/CHAR tienen collation explícita (carga, id_estudiante, id_year)
-- 3. Las comparaciones WHERE usan COLLATE utf8mb4_unicode_ci explícito
-- 4. Se mantiene el DEFINER original: mobiliar@localhost
-- 5. La función retorna DECIMAL(5,4) - no requiere collation
-- ================================================
