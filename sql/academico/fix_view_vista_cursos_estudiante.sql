-- ================================================
-- CORRECCIÓN DE COLLATION: vista_cursos_estudiante
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la VIEW para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en JOIN/UNION.
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Devuelve cursos/grupos asociados a un estudiante.
-- - Integra fuentes (calificaciones+actividades, boletín, y matrícula) mediante UNION para cubrir distintos escenarios.

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_cursos_estudiante`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED 
DEFINER=`mobiliar_production`@`%` 
SQL SECURITY DEFINER 
VIEW `mobiliar_academic_prod`.`vista_cursos_estudiante` AS 
-- Primera parte: UNION con calificaciones y actividades
SELECT 
    `mat`.`mat_id` AS `mat_id`,
    `gra`.`gra_tipo` AS `gra_tipo`,
    `car`.`car_curso` AS `curso`,
    `gra`.`gra_nombre` AS `gra_nombre`,
    `car`.`car_grupo` AS `grupo`,
    `gru`.`gru_nombre` AS `gru_nombre`,
    `car`.`year` AS `year`,
    `car`.`institucion` AS `institucion`
FROM `mobiliar_academic_prod`.`academico_matriculas` `mat`
LEFT JOIN `mobiliar_academic_prod`.`academico_calificaciones` `cal` 
    ON `cal`.`institucion` = `mat`.`institucion`
    AND `cal`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `cal`.`cal_id_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_actividades` `act` 
    ON `act`.`institucion` = `mat`.`institucion`
    AND `act`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `act`.`act_id` COLLATE utf8mb4_unicode_ci = `cal`.`cal_id_actividad` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_cargas` `car` 
    ON `car`.`institucion` = `mat`.`institucion`
    AND `car`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_id` COLLATE utf8mb4_unicode_ci = `act`.`act_id_carga` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `car`.`institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `car`.`car_curso` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grupos` `gru` 
    ON `gru`.`institucion` = `car`.`institucion`
    AND `gru`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `gru`.`gru_id` COLLATE utf8mb4_unicode_ci = `car`.`car_grupo` COLLATE utf8mb4_unicode_ci
GROUP BY `mat`.`mat_id`, `car`.`car_curso`, `car`.`car_grupo`

UNION

-- Segunda parte: UNION con boletín
SELECT 
    `mat`.`mat_id` AS `mat_id`,
    `gra`.`gra_tipo` AS `gra_tipo`,
    `car`.`car_curso` AS `curso`,
    `gra`.`gra_nombre` AS `gra_nombre`,
    `car`.`car_grupo` AS `grupo`,
    `gru`.`gru_nombre` AS `gru_nombre`,
    `car`.`year` AS `year`,
    `car`.`institucion` AS `institucion`
FROM `mobiliar_academic_prod`.`academico_matriculas` `mat`
LEFT JOIN `mobiliar_academic_prod`.`academico_boletin` `bol` 
    ON `bol`.`institucion` = `mat`.`institucion`
    AND `bol`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_cargas` `car` 
    ON `car`.`institucion` = `mat`.`institucion`
    AND `car`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_id` COLLATE utf8mb4_unicode_ci = `bol`.`bol_carga` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `car`.`institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `car`.`car_curso` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grupos` `gru` 
    ON `gru`.`institucion` = `car`.`institucion`
    AND `gru`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `gru`.`gru_id` COLLATE utf8mb4_unicode_ci = `car`.`car_grupo` COLLATE utf8mb4_unicode_ci
GROUP BY `mat`.`mat_id`, `car`.`car_curso`, `car`.`car_grupo`

UNION

-- Tercera parte: UNION directo con matrículas
SELECT 
    `mat`.`mat_id` AS `mat_id`,
    `gra`.`gra_tipo` AS `gra_tipo`,
    `mat`.`mat_grado` AS `curso`,
    `gra`.`gra_nombre` AS `gra_nombre`,
    `mat`.`mat_grupo` AS `grupo`,
    `gru`.`gru_nombre` AS `gru_nombre`,
    `mat`.`year` AS `year`,
    `mat`.`institucion` AS `institucion`
FROM `mobiliar_academic_prod`.`academico_matriculas` `mat`
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `mat`.`institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grupos` `gru` 
    ON `gru`.`institucion` = `mat`.`institucion`
    AND `gru`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `gru`.`gru_id` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grupo` COLLATE utf8mb4_unicode_ci;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Todas las comparaciones de campos VARCHAR/CHAR en JOINs usan COLLATE utf8mb4_unicode_ci explícito
-- 3. Los campos INT (institucion) no requieren collation
-- 4. Se mantiene el DEFINER original: mobiliar_production@%
-- 5. Esta vista tiene 3 partes unidas con UNION
-- ================================================
