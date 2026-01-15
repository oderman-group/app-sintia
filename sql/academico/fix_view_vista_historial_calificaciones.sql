-- ================================================
-- CORRECCIÓN DE COLLATION: vista_historial_calificaciones
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la VIEW para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en JOINs (incluye condición OR).
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Vista de historial/consulta de calificaciones: relaciona matrícula, cargas, áreas/materias, indicadores, actividades, calificaciones, boletín y recuperaciones.
-- - Útil para ver notas e indicadores por período/carga/curso/grupo, incluyendo rutas de media técnica (cuando aplica).

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_historial_calificaciones`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED 
DEFINER=`mobiliar_production`@`%` 
SQL SECURITY DEFINER 
VIEW `mobiliar_academic_prod`.`vista_historial_calificaciones` AS 
SELECT 
    `mat`.`institucion` AS `institucion`,
    `mat`.`year` AS `year`,
    `mat`.`mat_id` AS `mat_id`,
    `mat`.`mat_primer_apellido` AS `mat_primer_apellido`,
    `mat`.`mat_segundo_apellido` AS `mat_segundo_apellido`,
    `mat`.`mat_nombres` AS `mat_nombres`,
    `mat`.`mat_grado` AS `mat_grado`,
    `gra_actual`.`gra_nombre` AS `grado_actual`,
    `mat`.`mat_grupo` AS `mat_grupo`,
    `gru_actual`.`gru_nombre` AS `grupo_actual`,
    `mdt`.`matcur_id` AS `matcur_id`,
    `mdt`.`matcur_id_curso` AS `matcur_id_curso`,
    `mdt`.`matcur_id_grupo` AS `matcur_id_grupo`,
    `car`.`car_curso` AS `car_curso`,
    `gra`.`gra_nombre` AS `gra_nombre`,
    `car`.`car_grupo` AS `car_grupo`,
    `gru`.`gru_nombre` AS `gru_nombre`,
    `car`.`car_id` AS `car_id`,
    `car`.`car_activa` AS `car_activa`,
    `are`.`ar_id` AS `ar_id`,
    `are`.`ar_posicion` AS `ar_posicion`,
    `are`.`ar_nombre` AS `ar_nombre`,
    `mate`.`mat_id` AS `id_materia`,
    `mate`.`mat_siglas` AS `mat_siglas`,
    `mate`.`mat_nombre` AS `mat_nombre`,
    `mate`.`mat_valor` AS `mat_valor`,
    `ind`.`ind_id` AS `ind_id`,
    `indc`.`ipc_valor` AS `ipc_valor`,
    `ind`.`ind_nombre` AS `ind_nombre`,
    `indr`.`rind_id` AS `rind_id`,
    `indr`.`rind_nota` AS `rind_nota`,
    `act`.`act_id` AS `act_id`,
    `act`.`act_valor` AS `act_valor`,
    `act`.`act_descripcion` AS `act_descripcion`,
    `act`.`act_periodo` AS `act_periodo`,
    `bol`.`bol_periodo` AS `bol_periodo`,
    `per`.`gvp_periodo` AS `periodo`,
    `per`.`gvp_valor` AS `periodo_valor`,
    `cal`.`cal_id` AS `cal_id`,
    `cal`.`cal_nota` AS `cal_nota`,
    `cal`.`cal_observaciones` AS `cal_observaciones`,
    `cal`.`cal_nota_equivalente_cien` AS `cal_nota_equivalente_cien`,
    `bol`.`bol_id` AS `bol_id`,
    `bol`.`bol_nota` AS `bol_nota`,
    `bol`.`bol_nota_anterior` AS `bol_nota_anterior`,
    `bol`.`bol_tipo` AS `bol_tipo`,
    `bol`.`bol_observaciones_boletin` AS `bol_observaciones_boletin`,
    `mat`.`mat_foto` AS `mat_foto`,
    `mat`.`mat_documento` AS `mat_documento`,
    `mat`.`mat_estado_matricula` AS `mat_estado_matricula`,
    `mat`.`mat_numero_matricula` AS `mat_numero_matricula`,
    `mat`.`mat_folio` AS `mat_folio`,
    `mat`.`mat_matricula` AS `mat_matricula`,
    `car`.`car_ih` AS `car_ih`
FROM `mobiliar_academic_prod`.`academico_matriculas` `mat`
JOIN `mobiliar_academic_prod`.`academico_grados` `gra_actual` 
    ON `gra_actual`.`institucion` = `mat`.`institucion`
    AND `gra_actual`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `gra_actual`.`gra_id` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grupos` `gru_actual` 
    ON `gru_actual`.`institucion` = `mat`.`institucion`
    AND `gru_actual`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `gru_actual`.`gru_id` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grupo` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_sintia_admin`.`mediatecnica_matriculas_cursos` `mdt` 
    ON `mdt`.`matcur_id_institucion` = `mat`.`institucion`
    AND `mdt`.`matcur_years` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `mdt`.`matcur_id_matricula` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_cargas` `car` 
    ON `car`.`institucion` = `mat`.`institucion`
    AND `car`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND (
        (`car`.`car_curso` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
        AND `car`.`car_grupo` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grupo` COLLATE utf8mb4_unicode_ci)
        OR `car`.`car_curso` COLLATE utf8mb4_unicode_ci = `mdt`.`matcur_id_curso` COLLATE utf8mb4_unicode_ci
    )
JOIN `mobiliar_academic_prod`.`academico_grados_periodos` `per` 
    ON `per`.`institucion` = `mat`.`institucion`
    AND `per`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `per`.`gvp_grado` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `car`.`institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `car`.`car_curso` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grupos` `gru` 
    ON `gru`.`institucion` = `car`.`institucion`
    AND `gru`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `gru`.`gru_id` COLLATE utf8mb4_unicode_ci = `car`.`car_grupo` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_materias` `mate` 
    ON `mate`.`institucion` = `car`.`institucion`
    AND `mate`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `mate`.`mat_id` COLLATE utf8mb4_unicode_ci = `car`.`car_materia` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_areas` `are` 
    ON `are`.`institucion` = `car`.`institucion`
    AND `are`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `are`.`ar_id` COLLATE utf8mb4_unicode_ci = `mate`.`mat_area` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_indicadores_carga` `indc` 
    ON `indc`.`institucion` = `car`.`institucion`
    AND `indc`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `indc`.`ipc_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `indc`.`ipc_periodo` = `per`.`gvp_periodo`
LEFT JOIN `mobiliar_academic_prod`.`academico_indicadores` `ind` 
    ON `ind`.`institucion` = `indc`.`institucion`
    AND `ind`.`year` COLLATE utf8mb4_unicode_ci = `indc`.`year` COLLATE utf8mb4_unicode_ci
    AND `ind`.`ind_id` COLLATE utf8mb4_unicode_ci = `indc`.`ipc_indicador` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_actividades` `act` 
    ON `act`.`institucion` = `car`.`institucion`
    AND `act`.`year` COLLATE utf8mb4_unicode_ci = `car`.`year` COLLATE utf8mb4_unicode_ci
    AND `act`.`act_id_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `act`.`act_id_tipo` COLLATE utf8mb4_unicode_ci = `ind`.`ind_id` COLLATE utf8mb4_unicode_ci
    AND `act`.`act_periodo` = `per`.`gvp_periodo`
LEFT JOIN `mobiliar_academic_prod`.`academico_calificaciones` `cal` 
    ON `cal`.`institucion` = `act`.`institucion`
    AND `cal`.`year` COLLATE utf8mb4_unicode_ci = `act`.`year` COLLATE utf8mb4_unicode_ci
    AND `cal`.`cal_id_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
    AND `cal`.`cal_id_actividad` COLLATE utf8mb4_unicode_ci = `act`.`act_id` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_boletin` `bol` 
    ON `bol`.`institucion` = `mat`.`institucion`
    AND `bol`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_periodo` = `per`.`gvp_periodo`
LEFT JOIN `mobiliar_academic_prod`.`academico_indicadores_recuperacion` `indr` 
    ON `indr`.`institucion` = `mat`.`institucion`
    AND `indr`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `indr`.`rind_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
    AND `indr`.`rind_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `indr`.`rind_nota` > `indr`.`rind_nota_original`
    AND `indr`.`rind_indicador` COLLATE utf8mb4_unicode_ci = `indc`.`ipc_indicador` COLLATE utf8mb4_unicode_ci
    AND `indr`.`rind_periodo` = `indc`.`ipc_periodo`
ORDER BY 
    `mat`.`mat_id`,
    `mat`.`mat_primer_apellido`,
    `mat`.`mat_segundo_apellido`,
    `mat`.`mat_nombres`,
    `mat`.`mat_nombre2`,
    `are`.`ar_posicion`,
    `car`.`car_id`,
    `car`.`car_curso`,
    `car`.`car_grupo`,
    `per`.`gvp_periodo`;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Todas las comparaciones de campos VARCHAR/CHAR en JOINs usan COLLATE utf8mb4_unicode_ci explícito
-- 3. Los campos INT (institucion, periodos, etc.) no requieren collation
-- 4. Se mantiene el DEFINER original: mobiliar_production@%
-- 5. Esta vista tiene múltiples JOINs incluyendo indicadores y recuperaciones
-- 6. Incluye JOIN con tabla de otra base de datos: mobiliar_sintia_admin.mediatecnica_matriculas_cursos
-- 7. El JOIN con academico_cargas tiene una condición OR que también requiere collation explícita
-- ================================================
