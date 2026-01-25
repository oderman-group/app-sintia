-- Script de migración: Agregar campo car_indicadores_directivo en academico_cargas
-- Fecha: 2026-01-23
-- Descripción: Agrega campo para configurar si el directivo define los indicadores o el docente puede crearlos

-- Versión simplificada y compatible con MySQL/MariaDB
-- Si la columna ya existe, se mostrará un error pero no afectará los datos

-- Paso 1: Agregar campo de configuración en cargas
-- Si la columna ya existe, se mostrará un error 1060 (Duplicate column name) que se puede ignorar
ALTER TABLE academico_cargas 
ADD COLUMN car_indicadores_directivo TINYINT(1) DEFAULT 0 
COMMENT '0=Docente puede crear indicadores, 1=Solo directivo define indicadores' 
AFTER car_indicador_automatico;

-- Paso 2: Crear índice para optimizar consultas
-- Si el índice ya existe, se mostrará un error 1061 (Duplicate key name) que se puede ignorar
CREATE INDEX idx_car_indicadores_directivo 
ON academico_cargas(car_indicadores_directivo, institucion, year);
