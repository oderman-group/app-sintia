-- ================================================
-- CORRECCIÓN DE COLLATION: vista_datos_boletin
-- Base de datos: mobiliar_academic_prod
-- Problema: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- ================================================

-- Objetivo: Recrear la VIEW para fijar `collation_connection=utf8mb4_unicode_ci` y evitar “Illegal mix of collations” en JOINs (incluye cruces con otras BDs).
-- Ejecución (DBeaver): Ejecuta el archivo completo. Requiere permisos DROP/CREATE VIEW.

-- Qué hace:
-- - Vista “consolidada” para informes/boletines: combina matrícula, cargas, materias, áreas, boletín, períodos, clases/ausencias y otros datos asociados.
-- - Expone campos listos para reportes de boletín (nombres, curso/grupo, materia, nota, ausencias, etc.).

-- Establecer collation correcta antes de crear la vista
SET collation_connection = 'utf8mb4_unicode_ci';

-- Eliminar vista existente
DROP VIEW IF EXISTS `mobiliar_academic_prod`.`vista_datos_boletin`;

-- Crear vista con collation correcta
CREATE ALGORITHM=UNDEFINED 
DEFINER=`mobiliar`@`localhost` 
SQL SECURITY DEFINER 
VIEW `mobiliar_academic_prod`.`vista_datos_boletin` AS 
SELECT 
    `mat`.`id_nuevo` AS `id_nuevo`,
    `mat`.`mat_id` AS `mat_id`,
    `mat`.`mat_matricula` AS `mat_matricula`,
    `mat`.`mat_fecha` AS `mat_fecha`,
    `mat`.`mat_primer_apellido` AS `mat_primer_apellido`,
    `mat`.`mat_segundo_apellido` AS `mat_segundo_apellido`,
    `mat`.`mat_nombres` AS `mat_nombres`,
    `mat`.`mat_grado` AS `mat_grado`,
    `mat`.`mat_grupo` AS `mat_grupo`,
    `mat`.`mat_genero` AS `mat_genero`,
    `mat`.`mat_fecha_nacimiento` AS `mat_fecha_nacimiento`,
    `mat`.`mat_lugar_nacimiento` AS `mat_lugar_nacimiento`,
    `mat`.`mat_tipo_documento` AS `mat_tipo_documento`,
    `mat`.`mat_documento` AS `mat_documento`,
    `mat`.`mat_lugar_expedicion` AS `mat_lugar_expedicion`,
    `mat`.`mat_religion` AS `mat_religion`,
    `mat`.`mat_direccion` AS `mat_direccion`,
    `mat`.`mat_barrio` AS `mat_barrio`,
    `mat`.`mat_telefono` AS `mat_telefono`,
    `mat`.`mat_celular` AS `mat_celular`,
    `mat`.`mat_estrato` AS `mat_estrato`,
    `mat`.`mat_foto` AS `mat_foto`,
    `mat`.`mat_tipo` AS `mat_tipo`,
    `mat`.`mat_estado_matricula` AS `mat_estado_matricula`,
    `mat`.`mat_id_usuario` AS `mat_id_usuario`,
    `mat`.`mat_eliminado` AS `mat_eliminado`,
    `mat`.`mat_email` AS `mat_email`,
    `mat`.`mat_acudiente` AS `mat_acudiente`,
    `mat`.`mat_privilegio1` AS `mat_privilegio1`,
    `mat`.`mat_privilegio2` AS `mat_privilegio2`,
    `mat`.`mat_privilegio3` AS `mat_privilegio3`,
    `mat`.`mat_uso_sintia` AS `mat_uso_sintia`,
    `mat`.`mat_inicio` AS `mat_inicio`,
    `mat`.`mat_meses` AS `mat_meses`,
    `mat`.`mat_fin` AS `mat_fin`,
    `mat`.`mat_folio` AS `mat_folio`,
    `mat`.`mat_codigo_tesoreria` AS `mat_codigo_tesoreria`,
    `mat`.`mat_valor_matricula` AS `mat_valor_matricula`,
    `mat`.`mat_inclusion` AS `mat_inclusion`,
    `mat`.`mat_promocionado` AS `mat_promocionado`,
    `mat`.`mat_extranjero` AS `mat_extranjero`,
    `mat`.`mat_numero_matricula` AS `mat_numero_matricula`,
    `mat`.`mat_compromiso` AS `mat_compromiso`,
    `mat`.`mat_acudiente2` AS `mat_acudiente2`,
    `mat`.`mat_institucion_procedencia` AS `mat_institucion_procedencia`,
    `mat`.`mat_estado_agno` AS `mat_estado_agno`,
    `mat`.`mat_salon` AS `mat_salon`,
    `mat`.`mat_notificacion1` AS `mat_notificacion1`,
    `mat`.`mat_acudiente_principal` AS `mat_acudiente_principal`,
    `mat`.`mat_padre` AS `mat_padre`,
    `mat`.`mat_madre` AS `mat_madre`,
    `mat`.`mat_lugar_colegio_procedencia` AS `mat_lugar_colegio_procedencia`,
    `mat`.`mat_razon_ingreso_plantel` AS `mat_razon_ingreso_plantel`,
    `mat`.`mat_motivo_retiro_anterior` AS `mat_motivo_retiro_anterior`,
    `mat`.`mat_ciudad_actual` AS `mat_ciudad_actual`,
    `mat`.`mat_solicitud_inscripcion` AS `mat_solicitud_inscripcion`,
    `mat`.`mat_tipo_sangre` AS `mat_tipo_sangre`,
    `mat`.`mat_con_quien_vive` AS `mat_con_quien_vive`,
    `mat`.`mat_quien_otro` AS `mat_quien_otro`,
    `mat`.`mat_iniciar_proceso` AS `mat_iniciar_proceso`,
    `mat`.`mat_actualizar_datos` AS `mat_actualizar_datos`,
    `mat`.`mat_pago_matricula` AS `mat_pago_matricula`,
    `mat`.`mat_contrato` AS `mat_contrato`,
    `mat`.`mat_compromiso_academico` AS `mat_compromiso_academico`,
    `mat`.`mat_manual` AS `mat_manual`,
    `mat`.`mat_mayores14` AS `mat_mayores14`,
    `mat`.`mat_hoja_firma` AS `mat_hoja_firma`,
    `mat`.`mat_soporte_pago` AS `mat_soporte_pago`,
    `mat`.`mat_firma_adjunta` AS `mat_firma_adjunta`,
    `mat`.`mat_compromiso_convivencia` AS `mat_compromiso_convivencia`,
    `mat`.`mat_compromiso_convivencia_opcion` AS `mat_compromiso_convivencia_opcion`,
    `mat`.`mat_pagare` AS `mat_pagare`,
    `mat`.`mat_modalidad_estudio` AS `mat_modalidad_estudio`,
    `mat`.`mat_informe_parcial` AS `mat_informe_parcial`,
    `mat`.`mat_informe_parcial_fecha` AS `mat_informe_parcial_fecha`,
    `mat`.`mat_eps` AS `mat_eps`,
    `mat`.`mat_celular2` AS `mat_celular2`,
    `mat`.`mat_ciudad_residencia` AS `mat_ciudad_residencia`,
    `mat`.`mat_nombre2` AS `mat_nombre2`,
    `mat`.`mat_ciudad_recidencia` AS `mat_ciudad_recidencia`,
    `mat`.`mat_tipo_matricula` AS `mat_tipo_matricula`,
    `mat`.`institucion` AS `institucion`,
    `mat`.`year` AS `year`,
    `mat`.`mat_etnia` AS `mat_etnia`,
    `mat`.`mat_tiene_discapacidad` AS `mat_tiene_discapacidad`,
    `mat`.`mat_tipo_situacion` AS `mat_tipo_situacion`,
    `mat`.`mat_fecha_creacion` AS `mat_fecha_creacion`,
    `mat`.`mat_forma_creacion` AS `mat_forma_creacion`,
    `are`.`ar_id` AS `ar_id`,
    `car`.`car_id` AS `car_id`,
    `gra`.`gra_nombre` AS `gra_nombre`,
    `gru`.`gru_nombre` AS `gru_nombre`,
    `are`.`ar_nombre` AS `ar_nombre`,
    `mate`.`mat_id` AS `id_materia`,
    `mate`.`mat_nombre` AS `mat_nombre`,
    `car`.`car_ih` AS `car_ih`,
    `car`.`car_director_grupo` AS `car_director_grupo`,
    `mate`.`mat_valor` AS `mat_valor`,
    `per`.`gvp_valor` AS `periodo_valor`,
    `aus`.`aus_ausencias` AS `aus_ausencias`,
    `bol`.`bol_id` AS `bol_id`,
    `bol`.`bol_carga` AS `bol_carga`,
    `bol`.`bol_estudiante` AS `bol_estudiante`,
    `bol`.`bol_periodo` AS `bol_periodo`,
    `bol`.`bol_nota` AS `bol_nota`,
    `bol`.`bol_tipo` AS `bol_tipo`,
    `bol`.`bol_observaciones` AS `bol_observaciones`,
    `bol`.`bol_observaciones_boletin` AS `bol_observaciones_boletin`,
    `bol`.`bol_actualizaciones` AS `bol_actualizaciones`,
    `bol`.`bol_fecha_registro` AS `bol_fecha_registro`,
    `bol`.`bol_ultima_actualizacion` AS `bol_ultima_actualizacion`,
    `bol`.`bol_nota_anterior` AS `bol_nota_anterior`,
    `bol`.`bol_nota_indicadores` AS `bol_nota_indicadores`,
    `bol`.`bol_porcentaje` AS `bol_porcentaje`,
    `bol`.`bol_historial_actualizacion` AS `bol_historial_actualizacion`,
    `disi`.`dn_id` AS `dn_id`,
    `disi`.`dn_cod_estudiante` AS `dn_cod_estudiante`,
    `disi`.`dn_observacion` AS `dn_observacion`,
    `disi`.`dn_nota` AS `dn_nota`,
    `disi`.`dn_fecha` AS `dn_fecha`,
    `disi`.`dn_periodo` AS `dn_periodo`,
    `disi`.`dn_aspecto_academico` AS `dn_aspecto_academico`,
    `disi`.`dn_aspecto_convivencial` AS `dn_aspecto_convivencial`,
    `disi`.`dn_fecha_aspecto` AS `dn_fecha_aspecto`,
    `disi`.`dn_ultima_lectura` AS `dn_ultima_lectura`,
    `niv`.`niv_id` AS `niv_id`,
    `niv`.`niv_id_asg` AS `niv_id_asg`,
    `niv`.`niv_cod_estudiante` AS `niv_cod_estudiante`,
    `niv`.`niv_definitiva` AS `niv_definitiva`,
    `niv`.`niv_acta` AS `niv_acta`,
    `car`.`car_docente` AS `car_docente`
FROM `mobiliar_academic_prod`.`academico_matriculas` `mat`
LEFT JOIN `mobiliar_academic_prod`.`academico_cargas` `car` 
    ON `car`.`institucion` = `mat`.`institucion`
    AND `car`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_grupo` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grupo` COLLATE utf8mb4_unicode_ci
    AND `car`.`car_curso` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_materias` `mate` 
    ON `mate`.`institucion` = `mat`.`institucion`
    AND `mate`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `mate`.`mat_id` COLLATE utf8mb4_unicode_ci = `car`.`car_materia` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grados` `gra` 
    ON `gra`.`institucion` = `mat`.`institucion`
    AND `gra`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `gra`.`gra_id` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_grupos` `gru` 
    ON `gru`.`institucion` = `mat`.`institucion`
    AND `gru`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `gru`.`gru_id` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grupo` COLLATE utf8mb4_unicode_ci
JOIN `mobiliar_academic_prod`.`academico_areas` `are` 
    ON `are`.`institucion` = `mat`.`institucion`
    AND `are`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `are`.`ar_id` COLLATE utf8mb4_unicode_ci = `mate`.`mat_area` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_boletin` `bol` 
    ON `bol`.`institucion` = `mat`.`institucion`
    AND `bol`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `bol`.`bol_periodo` IN (1,2,3)
LEFT JOIN `mobiliar_academic_prod`.`academico_grados_periodos` `per` 
    ON `per`.`institucion` = `mat`.`institucion`
    AND `per`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `per`.`gvp_grado` COLLATE utf8mb4_unicode_ci = `mat`.`mat_grado` COLLATE utf8mb4_unicode_ci
    AND `per`.`gvp_periodo` = `bol`.`bol_periodo`
LEFT JOIN `mobiliar_academic_prod`.`academico_clases` `cls` 
    ON `cls`.`institucion` = `bol`.`institucion`
    AND `cls`.`year` COLLATE utf8mb4_unicode_ci = `bol`.`year` COLLATE utf8mb4_unicode_ci
    AND `cls`.`cls_id_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
    AND `cls`.`cls_periodo` = `bol`.`bol_periodo`
    AND `cls`.`cls_registrada` = 1
LEFT JOIN `mobiliar_academic_prod`.`academico_nivelaciones` `niv` 
    ON `niv`.`institucion` = `mat`.`institucion`
    AND `niv`.`year` COLLATE utf8mb4_unicode_ci = `mat`.`year` COLLATE utf8mb4_unicode_ci
    AND `niv`.`niv_cod_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
    AND `niv`.`niv_id_asg` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_academic_prod`.`academico_ausencias` `aus` 
    ON `aus`.`institucion` = `bol`.`institucion`
    AND `aus`.`year` COLLATE utf8mb4_unicode_ci = `bol`.`year` COLLATE utf8mb4_unicode_ci
    AND `aus`.`aus_id_clase` = `cls`.`cls_id`
    AND `aus`.`aus_id_estudiante` COLLATE utf8mb4_unicode_ci = `mat`.`mat_id` COLLATE utf8mb4_unicode_ci
LEFT JOIN `mobiliar_discipline_prod`.`disiplina_nota` `disi` 
    ON `disi`.`institucion` = `bol`.`institucion`
    AND `disi`.`year` COLLATE utf8mb4_unicode_ci = `bol`.`year` COLLATE utf8mb4_unicode_ci
    AND `disi`.`dn_cod_estudiante` COLLATE utf8mb4_unicode_ci = `bol`.`bol_estudiante` COLLATE utf8mb4_unicode_ci
    AND `disi`.`dn_periodo` = `bol`.`bol_periodo`
    AND `disi`.`dn_id_carga` COLLATE utf8mb4_unicode_ci = `car`.`car_id` COLLATE utf8mb4_unicode_ci
ORDER BY 
    `mat`.`mat_primer_apellido`,
    `mat`.`mat_segundo_apellido`,
    `mat`.`mat_nombres`,
    `mat`.`mat_nombre2`,
    `mat`.`mat_id`,
    `are`.`ar_posicion`,
    `car`.`car_id`,
    `bol`.`bol_periodo`;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Todas las comparaciones de campos VARCHAR/CHAR en JOINs usan COLLATE utf8mb4_unicode_ci explícito
-- 3. Los campos INT (institucion, periodos, etc.) no requieren collation
-- 4. Se mantiene el DEFINER original: mobiliar@localhost
-- 5. Esta vista tiene múltiples JOINs con varias tablas
-- 6. Incluye JOIN con tabla de otra base de datos: mobiliar_discipline_prod.disiplina_nota
-- ================================================
