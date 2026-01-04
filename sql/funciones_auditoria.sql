-- ================================================
-- FUNCIÓN HELPER PARA AUDITORÍA FINANCIERA
-- Facilita el registro desde triggers
-- ================================================

DELIMITER $$

-- Función para registrar cambios en la tabla de auditoría
-- Esta función es llamada desde los triggers para simplificar el código
DROP FUNCTION IF EXISTS `auditar_cambio_financiero`$$

CREATE FUNCTION `auditar_cambio_financiero`(
    p_tabla VARCHAR(100),
    p_registro_id VARCHAR(50),
    p_accion ENUM('INSERT', 'UPDATE', 'DELETE'),
    p_valor_anterior JSON,
    p_valor_nuevo JSON,
    p_cambios_detectados JSON
) RETURNS BIGINT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_id BIGINT;
    DECLARE v_institucion INT DEFAULT NULL;
    DECLARE v_year INT DEFAULT NULL;
    DECLARE v_usuario_db VARCHAR(100);
    
    -- Obtener usuario de MySQL
    SET v_usuario_db = USER();
    
    -- Intentar extraer institucion y year del JSON (si están presentes)
    -- Para INSERT: buscar en valor_nuevo
    -- Para UPDATE/DELETE: buscar en valor_anterior
    IF p_accion = 'INSERT' AND p_valor_nuevo IS NOT NULL THEN
        SET v_institucion = JSON_UNQUOTE(JSON_EXTRACT(p_valor_nuevo, '$.institucion'));
        SET v_year = JSON_UNQUOTE(JSON_EXTRACT(p_valor_nuevo, '$.year'));
    ELSEIF (p_accion = 'UPDATE' OR p_accion = 'DELETE') AND p_valor_anterior IS NOT NULL THEN
        SET v_institucion = JSON_UNQUOTE(JSON_EXTRACT(p_valor_anterior, '$.institucion'));
        SET v_year = JSON_UNQUOTE(JSON_EXTRACT(p_valor_anterior, '$.year'));
    END IF;
    
    -- Convertir a números si son strings
    IF v_institucion IS NOT NULL AND v_institucion != 'null' THEN
        SET v_institucion = CAST(v_institucion AS UNSIGNED);
    ELSE
        SET v_institucion = NULL;
    END IF;
    
    IF v_year IS NOT NULL AND v_year != 'null' THEN
        SET v_year = CAST(v_year AS UNSIGNED);
    ELSE
        SET v_year = NULL;
    END IF;
    
    -- Insertar registro de auditoría
    INSERT INTO `auditoria_financiera` (
        `tabla_afectada`,
        `registro_id`,
        `accion`,
        `valor_anterior`,
        `valor_nuevo`,
        `cambios_detectados`,
        `usuario_db`,
        `contexto`,
        `institucion`,
        `year`,
        `fecha`
    ) VALUES (
        p_tabla,
        p_registro_id,
        p_accion,
        p_valor_anterior,
        p_valor_nuevo,
        p_cambios_detectados,
        v_usuario_db,
        'BD_DIRECTA',
        v_institucion,
        v_year,
        NOW()
    );
    
    -- Retornar el ID insertado
    SET v_id = LAST_INSERT_ID();
    
    RETURN v_id;
END$$

DELIMITER ;

-- ================================================
-- NOTAS
-- ================================================
-- 
-- Esta función es utilizada por los triggers para registrar cambios
-- Los campos usuario_app e ip_address se llenan desde código PHP cuando
-- el cambio viene desde la aplicación (contexto = 'APP')
--
-- ================================================

