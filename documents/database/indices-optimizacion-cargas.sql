-- Índices para optimización de la consulta de cargas académicas
-- Estos índices mejoran significativamente el rendimiento al filtrar y hacer JOINs

-- Tabla: academico_cargas
-- Índice compuesto para filtrar por institución, year y activa
CREATE INDEX IF NOT EXISTS idx_cargas_institucion_year_activa 
ON academico_cargas(institucion, year, car_activa);

-- Índice para curso y grupo (usado en JOINs y agrupaciones)
CREATE INDEX IF NOT EXISTS idx_cargas_curso_grupo 
ON academico_cargas(car_curso, car_grupo, institucion, year);

-- Índice para docente (usado en JOINs)
CREATE INDEX IF NOT EXISTS idx_cargas_docente 
ON academico_cargas(car_docente, institucion, year);

-- Índice para materia (usado en JOINs y filtros)
CREATE INDEX IF NOT EXISTS idx_cargas_materia 
ON academico_cargas(car_materia, institucion, year);

-- Índice para periodo (usado frecuentemente en filtros)
CREATE INDEX IF NOT EXISTS idx_cargas_periodo 
ON academico_cargas(car_periodo, institucion, year);

-- Tabla: academico_matriculas
-- Índice compuesto para filtrar estudiantes activos por grado/grupo
CREATE INDEX IF NOT EXISTS idx_matriculas_grado_grupo_estado 
ON academico_matriculas(mat_grado, mat_grupo, mat_estado_matricula, mat_eliminado, institucion, year);

-- Tabla: academico_actividades
-- Índice para consultas de actividades por carga y periodo
CREATE INDEX IF NOT EXISTS idx_actividades_carga_periodo 
ON academico_actividades(act_id_carga, act_periodo, act_estado, institucion, year);

-- Índice para actividades registradas
CREATE INDEX IF NOT EXISTS idx_actividades_registrada 
ON academico_actividades(act_registrada, act_estado, institucion, year);

-- Tabla: academico_calificaciones
-- Índice para calificaciones por estudiante y actividad
CREATE INDEX IF NOT EXISTS idx_calificaciones_estudiante_actividad 
ON academico_calificaciones(cal_id_estudiante, cal_id_actividad, institucion, year);

-- Tabla: academico_grados
-- Índice compuesto para JOINs frecuentes
CREATE INDEX IF NOT EXISTS idx_grados_institucion_year 
ON academico_grados(gra_id, institucion, year);

-- Tabla: academico_grupos
-- Índice compuesto para JOINs frecuentes
CREATE INDEX IF NOT EXISTS idx_grupos_institucion_year 
ON academico_grupos(gru_id, institucion, year);

-- Tabla: usuarios
-- Índice compuesto para JOINs frecuentes
CREATE INDEX IF NOT EXISTS idx_usuarios_institucion_year 
ON usuarios(uss_id, institucion, year);

-- Verificar índices existentes (comentado, solo para referencia)
-- SHOW INDEX FROM academico_cargas;
-- SHOW INDEX FROM academico_matriculas;
-- SHOW INDEX FROM academico_actividades;


