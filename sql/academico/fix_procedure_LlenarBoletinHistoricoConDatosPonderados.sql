-- ================================================
-- CORRECCIÓN DE COLLATION: LlenarBoletinHistoricoConDatosPonderados
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations”.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Actualiza registros históricos en `academico_boletin` calculando/llenando:
--   - `bol_area` (área asociada a la materia),
--   - `bol_valor_asignatura` (peso/valor de la materia),
--   - `bol_nota_equivalente` (nota ponderada por el valor de la materia).
-- - Solo afecta la institución, año y período recibidos.
-- Parámetros:
-- - p_institucion (INT): Institución.
-- - p_year (INT): Año académico.
-- - p_periodo (INT): Período.

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_academic_prod`.`LlenarBoletinHistoricoConDatosPonderados`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`root`@`localhost` PROCEDURE `mobiliar_academic_prod`.`LlenarBoletinHistoricoConDatosPonderados`(
    IN p_institucion INT,
    IN p_year INT,
    IN p_periodo INT
)
BEGIN
    -- Actualiza las columnas bol_area, bol_valor_asignatura y bol_nota_equivalente
    -- en la tabla academico_boletin para los registros históricos que coincidan con los parámetros.
    UPDATE mobiliar_academic_prod.academico_boletin AS ab
    INNER JOIN mobiliar_academic_prod.academico_cargas AS ac
        ON ab.bol_carga = ac.car_id
        AND ac.institucion = p_institucion -- ¡Filtro añadido para institución!
        AND ac.year COLLATE utf8mb4_unicode_ci = p_year -- ¡Filtro añadido para año!
    INNER JOIN mobiliar_academic_prod.academico_materias AS am
        ON ac.car_materia = am.mat_id
        AND am.institucion = p_institucion -- ¡Filtro añadido para institución!
        AND am.year COLLATE utf8mb4_unicode_ci = p_year -- ¡Filtro añadido para año!
    SET
        -- Asigna el ID del área a la que pertenece la materia (o el ID de la materia si es principal).
        -- COALESCE(am.mat_area, am.mat_id) se usa para manejar materias que son "áreas" por sí mismas.
        ab.bol_area = COALESCE(am.mat_area, am.mat_id),
        -- Almacena el valor del peso de la asignatura (mat_valor).
        -- Se usa NULLIF para convertir cadenas vacías a NULL, y luego COALESCE para convertir NULLs a 0.
        ab.bol_valor_asignatura = COALESCE(NULLIF(am.mat_valor, ''), 0),
        -- Calcula la nota equivalente: bol_nota * (mat_valor / 100).
        -- Se usa NULLIF para convertir cadenas vacías a NULL, y luego COALESCE para convertir NULLs a 0.
        -- Se usa 100.0 para asegurar una división de punto flotante.
        ab.bol_nota_equivalente = ab.bol_nota * (COALESCE(NULLIF(am.mat_valor, ''), 0) / 100.0)
    WHERE
        ab.institucion = p_institucion
        AND ab.year COLLATE utf8mb4_unicode_ci = p_year
        AND ab.bol_periodo = p_periodo;

    -- Puedes añadir un mensaje de confirmación o log aquí si lo deseas
    -- SELECT CONCAT('Actualización completada para Institución: ', p_institucion, ', Año: ', p_year, ', Periodo: ', p_periodo);

END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Las comparaciones con campos 'year' (que son VARCHAR/CHAR) usan COLLATE explícito
-- 3. Las comparaciones con campos 'year' (VARCHAR/CHAR) usan COLLATE explícito (MySQL convierte INT automáticamente)
-- 4. Se mantiene el DEFINER original: root@localhost
-- 5. Los campos INT (institucion, bol_periodo) no requieren collation
-- ================================================
