-- ================================================
-- CORRECCIÓN DE COLLATION: vista_carga_acumulada_estudiante
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la VIEW para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en JOINs.
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Construye un dataset base de boletín por estudiante/carga/período.
-- - Une `academico_boletin` con `academico_matriculas` para exponer grado/grupo del estudiante.

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_carga_acumulada_estudiante`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED 
DEFINER=`mobiliar`@`localhost` 
SQL SECURITY DEFINER 
VIEW `mobiliar_academic_prod`.`vista_carga_acumulada_estudiante` AS 
SELECT 
    `bol2`.`institucion` AS `institucion`,
    `bol2`.`year` AS `year`,
    `bol2`.`bol_estudiante` AS `est`,
    `mat1`.`mat_grado` AS `mat_grado`,
    `mat1`.`mat_grupo` AS `mat_grupo`,
    `bol2`.`bol_carga` AS `bol_carga`,
    `bol2`.`bol_nota` AS `bol_nota`,
    `bol2`.`bol_periodo` AS `periodo`
FROM `mobiliar_academic_prod`.`academico_boletin` `bol2`
INNER JOIN `mobiliar_academic_prod`.`academico_matriculas` `mat1` 
    ON `mat1`.`mat_id` COLLATE utf8mb4_unicode_ci = `bol2`.`bol_estudiante` COLLATE utf8mb4_unicode_ci
    AND `mat1`.`institucion` = `bol2`.`institucion`
    AND `mat1`.`year` COLLATE utf8mb4_unicode_ci = `bol2`.`year` COLLATE utf8mb4_unicode_ci
GROUP BY 
    `bol2`.`institucion`,
    `bol2`.`year`,
    `bol2`.`bol_estudiante`,
    `mat1`.`mat_grado`,
    `mat1`.`mat_grupo`,
    `bol2`.`bol_periodo`,
    `bol2`.`bol_carga`;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Las comparaciones en el JOIN usan COLLATE utf8mb4_unicode_ci explícito
-- 3. Se mantiene el DEFINER original: mobiliar@localhost
-- 4. Esta vista es usada por la función get_nota_estudiante_carga_acumulada_periodo
-- 5. Los campos INT (institucion) no requieren collation
-- ================================================
