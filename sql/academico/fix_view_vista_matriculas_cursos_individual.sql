-- ================================================
-- CORRECCIÓN DE COLLATION: vista_matriculas_cursos_individual
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la VIEW para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en JOINs/WHERE.
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Lista matrículas “individuales” (media técnica) con sus cargas/materias/áreas y notas de boletín asociadas.
-- - Filtra por registros activos en `mediatecnica_matriculas_cursos`.

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_matriculas_cursos_individual`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED 
DEFINER=`mobiliar`@`localhost` 
SQL SECURITY DEFINER 
VIEW `mobiliar_academic_prod`.`vista_matriculas_cursos_individual` AS 
SELECT 
    `mts`.`matcur_years` AS `matcur_years`,
    `mts`.`matcur_id_institucion` AS `matcur_id_institucion`,
    `car`.`car_id` AS `car_id`,
    `mts`.`matcur_id_curso` AS `matcur_id_curso`,
    `mts`.`matcur_id_grupo` AS `matcur_id_grupo`,
    `gra`.`gra_nombre` AS `gra_nombre`,
    `gra`.`gra_tipo` AS `gra_tipo`,
    `mts`.`matcur_id_matricula` AS `matcur_id_matricula`,
    `mate`.`mat_area` AS `mat_area`,
    `are`.`ar_nombre` AS `ar_nombre`,
    `car`.`car_materia` AS `car_materia`,
    `mate`.`mat_nombre` AS `mat_nombre`,
    `bol`.`bol_periodo` AS `bol_periodo`,
    `bol`.`bol_nota` AS `bol_nota`,
    `bol`.`bol_tipo` AS `bol_tipo`,
    `bol`.`bol_nota_anterior` AS `bol_nota_anterior`
FROM `mobiliar_sintia_admin`.`mediatecnica_matriculas_cursos` `mts`
JOIN `mobiliar_academic_prod`.`academico_cargas` `car` 
    ON `car`.`institucion` = `mts`.`matcur_id_institucion`
    AND `car`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_curso` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_curso` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_grupo` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_grupo` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `mts`.`matcur_id_institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_curso` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_materias` `mate` 
    ON `mate`.`institucion` = `car`.`institucion`
    AND `mate`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `mate`.`mat_id` COLLATE utf8mb4_unicode_ci = `car`.`car_materia` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_areas` `are` 
    ON `are`.`institucion` = `mate`.`institucion`
    AND `are`.`year` COLLATE utf8mb4_unicode_ci = `mate`.`year` COLLATE utf8mb4_unicode_ci
    AND `are`.`ar_id` COLLATE utf8mb4_unicode_ci = `mate`.`mat_area` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_boletin` `bol` 
    ON `bol`.`institucion` = `mts`.`matcur_id_institucion`
    AND `bol`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_estudiante` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_matricula` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
WHERE `mts`.`matcur_estado` COLLATE utf8mb4_unicode_ci = 'ACTIVO' COLLATE utf8mb4_unicode_ci
ORDER BY 
    `mts`.`matcur_years`,
    `mts`.`matcur_id_institucion`,
    `car`.`car_id`,
    `mts`.`matcur_id_curso`,
    `mts`.`matcur_id_grupo`,
    `mts`.`matcur_id_matricula`,
    `bol`.`bol_periodo`;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Todas las comparaciones de campos VARCHAR/CHAR en JOINs usan COLLATE utf8mb4_unicode_ci explícito
-- 3. La condición WHERE también usa COLLATE utf8mb4_unicode_ci explícito
-- 4. Los campos INT (institucion, periodos, etc.) no requieren collation
-- 5. Se mantiene el DEFINER original: mobiliar@localhost
-- 6. Incluye JOIN con tabla de otra base de datos: mobiliar_sintia_admin.mediatecnica_matriculas_cursos
-- ================================================
