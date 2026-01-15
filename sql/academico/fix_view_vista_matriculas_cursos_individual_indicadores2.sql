-- ================================================
-- CORRECCIÓN DE COLLATION: vista_matriculas_cursos_individual_indicadores2
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la VIEW para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” (JOIN con otra vista).
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Variante de indicadores para matrículas “individuales” que consume la vista agregada `vista_valores_indicadores_periodos_estudiantes`.
-- - Permite consultar valores de indicador ya calculados/agregados por estudiante/carga/período/tipo.

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_matriculas_cursos_individual_indicadores2`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED 
DEFINER=`mobiliar`@`localhost` 
SQL SECURITY DEFINER 
VIEW `mobiliar_academic_prod`.`vista_matriculas_cursos_individual_indicadores2` AS 
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
    `ipc`.`ipc_periodo` AS `ipc_periodo`,
    `ipc`.`ipc_indicador` AS `ipc_indicador`,
    `ind`.`ind_nombre` AS `ind_nombre`,
    `ipc`.`ipc_valor` AS `ipc_valor`,
    `indv`.`cal_id_estudiante` AS `cal_id_estudiante`,
    `indv`.`act_id_carga` AS `act_id_carga`,
    `indv`.`act_id_tipo` AS `act_id_tipo`,
    `indv`.`act_id` AS `act_id`,
    `indv`.`act_descripcion` AS `act_descripcion`,
    `indv`.`act_periodo` AS `act_periodo`,
    `indv`.`valor_porcentaje_indicador` AS `valor_porcentaje_indicador`,
    `indv`.`indicador_porcentual` AS `indicador_porcentual`,
    `indv`.`valor_indicador` AS `valor_indicador`,
    `indv`.`institucion` AS `institucion`,
    `indv`.`year` AS `year`,
    `bol`.`bol_nota` AS `bol_nota`,
    `bol`.`bol_tipo` AS `bol_tipo`,
    `bol`.`bol_nota_anterior` AS `bol_nota_anterior`
FROM `mobiliar_sintia_admin`.`mediatecnica_matriculas_cursos` `mts`
JOIN `mobiliar_academic_prod`.`academico_matriculas` `mat` 
    ON `mat`.`institucion` = `mts`.`matcur_id_institucion`
    AND `mat`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `mat`.`mat_id` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_matricula` COLLATE utf8mb4_unicode_ci
    AND `mat`.`mat_tipo_matricula` COLLATE utf8mb4_unicode_ci = 'individual' COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `mts`.`matcur_id_institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_curso` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_cargas` `car` 
    ON `car`.`institucion` = `mts`.`matcur_id_institucion`
    AND `car`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_curso` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_curso` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_grupo` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_grupo` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_materias` `mate` 
    ON `mate`.`institucion` = `car`.`institucion`
    AND `mate`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `mate`.`mat_id` COLLATE utf8mb4_unicode_ci = `car`.`car_materia` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_areas` `are` 
    ON `are`.`institucion` = `mate`.`institucion`
    AND `are`.`year` COLLATE utf8mb4_unicode_ci = `mate`.`year` COLLATE utf8mb4_unicode_ci
    AND `are`.`ar_id` COLLATE utf8mb4_unicode_ci = `mate`.`mat_area` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_indicadores_carga` `ipc` 
    ON `ipc`.`institucion` = `mts`.`matcur_id_institucion`
    AND `ipc`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `ipc`.`ipc_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_indicadores` `ind` 
    ON `ind`.`institucion` = `mts`.`matcur_id_institucion`
    AND `ind`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `ind`.`ind_id` COLLATE utf8mb4_unicode_ci = `ipc`.`ipc_indicador` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`vista_valores_indicadores_periodos_estudiantes` `indv` 
    ON `indv`.`institucion` = `mat`.`institucion`
    AND `indv`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `indv`.`act_periodo` = `ipc`.`ipc_periodo`
    AND `indv`.`cal_id_estudiante` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_matricula` COLLATE utf8mb4_unicode_ci
    AND `indv`.`act_id_tipo` COLLATE utf8mb4_unicode_ci = `ipc`.`ipc_indicador` COLLATE utf8mb4_unicode_ci
    AND `indv`.`act_id_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_boletin` `bol` 
    ON `bol`.`institucion` = `mts`.`matcur_id_institucion`
    AND `bol`.`year` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_years` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_estudiante` COLLATE utf8mb4_unicode_ci = `mts`.`matcur_id_matricula` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_periodo` = `ipc`.`ipc_periodo`
WHERE `mts`.`matcur_estado` COLLATE utf8mb4_unicode_ci = 'ACTIVO' COLLATE utf8mb4_unicode_ci
ORDER BY 
    `mts`.`matcur_years`,
    `mts`.`matcur_id_institucion`,
    `car`.`car_id`,
    `mts`.`matcur_id_curso`,
    `mts`.`matcur_id_grupo`,
    `mts`.`matcur_id_matricula`,
    `ipc`.`ipc_periodo`;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Todas las comparaciones de campos VARCHAR/CHAR en JOINs usan COLLATE utf8mb4_unicode_ci explícito
-- 3. La condición WHERE también usa COLLATE utf8mb4_unicode_ci explícito
-- 4. La comparación con literal 'individual' también usa COLLATE explícito
-- 5. Los campos INT (institucion, periodos, etc.) no requieren collation
-- 6. Se mantiene el DEFINER original: mobiliar@localhost
-- 7. Incluye JOIN con tabla de otra base de datos: mobiliar_sintia_admin.mediatecnica_matriculas_cursos
-- 8. Incluye JOIN con otra vista: vista_valores_indicadores_periodos_estudiantes (debe estar corregida primero)
-- ================================================
