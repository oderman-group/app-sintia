-- ================================================
-- SCRIPTS DE MANTENIMIENTO PARA AUDITORÍA FINANCIERA
-- ================================================

-- ================================================
-- 1. REVISAR TAMAÑO DE LA TABLA
-- ================================================

SELECT 
    table_name AS 'Tabla',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Tamaño (MB)',
    table_rows AS 'Registros Aproximados',
    ROUND((data_length / 1024 / 1024), 2) AS 'Datos (MB)',
    ROUND((index_length / 1024 / 1024), 2) AS 'Índices (MB)'
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
  AND table_name = 'auditoria_financiera';

-- ================================================
-- 2. ANÁLISIS DE PATRONES DE CAMBIOS (Últimos 30 días)
-- ================================================

SELECT 
    DATE(af.fecha) as fecha,
    af.tabla_afectada,
    af.accion,
    COUNT(*) as total_cambios,
    COUNT(DISTINCT af.registro_id) as registros_unicos
FROM auditoria_financiera af
WHERE af.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(af.fecha), af.tabla_afectada, af.accion
ORDER BY fecha DESC, total_cambios DESC;

-- ================================================
-- 3. CAMBIOS POR USUARIO (Últimos 7 días)
-- ================================================

SELECT 
    COALESCE(af.usuario_app, af.usuario_db) as usuario,
    af.contexto,
    COUNT(*) as total_cambios,
    COUNT(DISTINCT af.tabla_afectada) as tablas_afectadas,
    MIN(af.fecha) as primera_accion,
    MAX(af.fecha) as ultima_accion
FROM auditoria_financiera af
WHERE af.fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY COALESCE(af.usuario_app, af.usuario_db), af.contexto
ORDER BY total_cambios DESC;

-- ================================================
-- 4. DETECTAR CAMBIOS SOSPECHOSOS
-- ================================================

-- Cambios masivos en un período corto
SELECT 
    DATE_FORMAT(af.fecha, '%Y-%m-%d %H:00:00') as hora,
    COALESCE(af.usuario_app, af.usuario_db) as usuario,
    af.tabla_afectada,
    COUNT(*) as cambios_en_hora
FROM auditoria_financiera af
WHERE af.fecha >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY DATE_FORMAT(af.fecha, '%Y-%m-%d %H:00:00'), COALESCE(af.usuario_app, af.usuario_db), af.tabla_afectada
HAVING cambios_en_hora > 50
ORDER BY cambios_en_hora DESC;

-- DELETE físicos (cambios críticos)
SELECT 
    af.*,
    JSON_PRETTY(af.valor_anterior) as datos_eliminados
FROM auditoria_financiera af
WHERE af.accion = 'DELETE'
  AND af.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY af.fecha DESC;

-- ================================================
-- 5. ESTADÍSTICAS POR INSTITUCIÓN
-- ================================================

SELECT 
    af.institucion,
    af.year,
    COUNT(*) as total_cambios,
    COUNT(DISTINCT af.tabla_afectada) as tablas_afectadas,
    COUNT(DISTINCT af.registro_id) as registros_unicos,
    MIN(af.fecha) as primera_fecha,
    MAX(af.fecha) as ultima_fecha
FROM auditoria_financiera af
WHERE af.institucion IS NOT NULL
GROUP BY af.institucion, af.year
ORDER BY total_cambios DESC;

-- ================================================
-- 6. ANÁLISIS DE CRECIMIENTO (Últimos 12 meses)
-- ================================================

SELECT 
    DATE_FORMAT(af.fecha, '%Y-%m') as mes,
    COUNT(*) as total_cambios,
    COUNT(DISTINCT af.tabla_afectada) as tablas_afectadas,
    COUNT(DISTINCT DATE(af.fecha)) as dias_con_cambios,
    ROUND(COUNT(*) / COUNT(DISTINCT DATE(af.fecha)), 2) as promedio_diario
FROM auditoria_financiera af
WHERE af.fecha >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(af.fecha, '%Y-%m')
ORDER BY mes DESC;

-- ================================================
-- 7. CONSIDERACIONES PARA PARTICIONAMIENTO
-- ================================================

-- Ver distribución de datos por año/mes
SELECT 
    YEAR(af.fecha) as año,
    MONTH(af.fecha) as mes,
    COUNT(*) as registros,
    ROUND(COUNT(*) * 3 / 1024 / 1024, 2) as tamaño_estimado_mb
FROM auditoria_financiera af
GROUP BY YEAR(af.fecha), MONTH(af.fecha)
ORDER BY año DESC, mes DESC;

-- ================================================
-- 8. LIMPIAR LOGS ANTIGUOS (EJEMPLO - NO EJECUTAR SIN REVISIÓN)
-- ================================================

-- IMPORTANTE: Esta consulta es solo un ejemplo.
-- NO ejecutar sin revisar primero y hacer backup.
-- Considerar archivar en lugar de eliminar.

-- Archivar logs más antiguos de 2 años (excepto DELETEs)
/*
CREATE TABLE IF NOT EXISTS auditoria_financiera_archivo LIKE auditoria_financiera;

INSERT INTO auditoria_financiera_archivo
SELECT * FROM auditoria_financiera
WHERE fecha < DATE_SUB(NOW(), INTERVAL 2 YEAR)
  AND accion != 'DELETE';

DELETE FROM auditoria_financiera
WHERE fecha < DATE_SUB(NOW(), INTERVAL 2 YEAR)
  AND accion != 'DELETE';
*/

-- ================================================
-- 9. OPTIMIZAR TABLA (Mantenimiento periódico)
-- ================================================

-- Analizar tabla para optimizar índices
-- ANALYZE TABLE auditoria_financiera;

-- Optimizar tabla (reorganiza índices, limpia fragmentación)
-- OPTIMIZE TABLE auditoria_financiera;

-- ================================================
-- 10. VERIFICAR INTEGRIDAD DE DATOS JSON
-- ================================================

-- Verificar que los JSON sean válidos
SELECT 
    id,
    tabla_afectada,
    registro_id,
    accion,
    fecha
FROM auditoria_financiera
WHERE JSON_VALID(valor_anterior) = 0
   OR JSON_VALID(valor_nuevo) = 0
   OR (cambios_detectados IS NOT NULL AND JSON_VALID(cambios_detectados) = 0)
LIMIT 100;

-- ================================================
-- NOTAS DE MANTENIMIENTO
-- ================================================
-- 
-- 1. Ejecutar análisis de tamaño mensualmente
-- 2. Revisar patrones de cambios semanalmente
-- 3. Monitorear cambios sospechosos diariamente
-- 4. Considerar archivar logs antiguos (> 2 años) trimestralmente
-- 5. Optimizar tabla cuando el tamaño exceda 5GB
-- 6. Considerar particionamiento cuando el volumen sea muy alto
--
-- ================================================

