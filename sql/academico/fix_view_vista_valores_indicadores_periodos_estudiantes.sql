-- ================================================
-- CORRECCIÓN DE COLLATION: vista_valores_indicadores_periodos_estudiantes
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- NOTA: Esta vista es usada por otras vistas, debe corregirse antes que ellas
-- ================================================

-- Objetivo: Recrear la VIEW base para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” (dependida por otras vistas).
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Agrega (SUM) el porcentaje total del indicador y la nota equivalente (en escala 0–100) por estudiante/carga/período/tipo de indicador.
-- - Calcula `valor_indicador` como: (SUM(cal_nota_equivalente_cien) / SUM(act_valor)) * 100.

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_valores_indicadores_periodos_estudiantes`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED
DEFINER=`mobiliar`@`localhost`
SQL SECURITY DEFINER
VIEW `mobiliar_academic_prod`.`vista_valores_indicadores_periodos_estudiantes` AS
SELECT
    `cal`.`cal_id_estudiante` AS `cal_id_estudiante`,
    `act`.`act_id_carga` AS `act_id_carga`,
    `act`.`act_id_tipo` AS `act_id_tipo`,
    `act`.`act_id` AS `act_id`,
    `act`.`act_descripcion` AS `act_descripcion`,
    `act`.`act_periodo` AS `act_periodo`,
    SUM(`act`.`act_valor`) AS `valor_porcentaje_indicador`,
    SUM(`cal`.`cal_nota_equivalente_cien`) AS `indicador_porcentual`,
    SUM(`cal`.`cal_nota_equivalente_cien`) / SUM(`act`.`act_valor`) * 100 AS `valor_indicador`,
    `cal`.`institucion` AS `institucion`,
    `cal`.`year` AS `year`
FROM `mobiliar_academic_prod`.`academico_calificaciones` `cal`
JOIN `mobiliar_academic_prod`.`academico_actividades` `act`
    ON `act`.`act_id` COLLATE utf8mb4_unicode_ci = `cal`.`cal_id_actividad` COLLATE utf8mb4_unicode_ci
    AND `cal`.`institucion` = `act`.`institucion`
    AND `cal`.`year` COLLATE utf8mb4_unicode_ci = `act`.`year` COLLATE utf8mb4_unicode_ci
    AND `act`.`act_estado` = 1
WHERE `cal`.`cal_nota` IS NOT NULL
GROUP BY
    `cal`.`cal_id_estudiante`,
    `act`.`act_periodo`,
    `act`.`act_id_carga`,
    `act`.`act_id_tipo`,
    `cal`.`institucion`,
    `cal`.`year`
ORDER BY
    `cal`.`institucion`,
    `cal`.`year`,
    `cal`.`cal_id_estudiante`,
    `act`.`act_id_carga`,
    `act`.`act_periodo`,
    `act`.`act_id_tipo`;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Las comparaciones de campos VARCHAR/CHAR en JOIN usan COLLATE utf8mb4_unicode_ci explícito
-- 3. Se mantiene el DEFINER original: mobiliar@localhost
-- 4. Esta vista es usada por otras vistas (ej: vista_datos_boletin_indicadores, vista_matriculas_cursos_individual_indicadores2)
-- ================================================

