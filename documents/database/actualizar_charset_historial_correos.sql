-- Script para actualizar charset de la tabla historial_correos_enviados
-- Esto permite almacenar emojis y caracteres UTF-8 especiales

-- Actualizar la tabla completa a utf8mb4
ALTER TABLE mobiliar_sintia_admin.historial_correos_enviados 
CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Asegurar que la columna hisco_contenido use utf8mb4
ALTER TABLE mobiliar_sintia_admin.historial_correos_enviados 
MODIFY COLUMN hisco_contenido LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verificar otras columnas de texto importantes
ALTER TABLE mobiliar_sintia_admin.historial_correos_enviados 
MODIFY COLUMN hisco_remitente VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
MODIFY COLUMN hisco_destinatario VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
MODIFY COLUMN hisco_asunto VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
MODIFY COLUMN hisco_descripcion_error TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

