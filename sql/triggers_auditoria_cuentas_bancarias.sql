-- ================================================
-- TRIGGERS DE AUDITORÍA PARA: finanzas_cuentas_bancarias (PRIORIDAD MEDIA)
-- ================================================

DELIMITER $$

-- Trigger BEFORE UPDATE: Captura el estado anterior antes de actualizar
DROP TRIGGER IF EXISTS `trg_finanzas_cuentas_bancarias_before_update`$$

CREATE TRIGGER `trg_finanzas_cuentas_bancarias_before_update`
BEFORE UPDATE ON `finanzas_cuentas_bancarias`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    DECLARE v_valor_nuevo JSON;
    DECLARE v_cambios_detectados JSON;
    DECLARE v_cambios JSON;
    
    -- Construir JSON con valores anteriores (campos críticos)
    SET v_valor_anterior = JSON_OBJECT(
        'cba_id', OLD.cba_id,
        'cba_nombre', OLD.cba_nombre,
        'cba_tipo', OLD.cba_tipo,
        'cba_metodo_pago_asociado', OLD.cba_metodo_pago_asociado,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Construir JSON con valores nuevos
    SET v_valor_nuevo = JSON_OBJECT(
        'cba_id', NEW.cba_id,
        'cba_nombre', NEW.cba_nombre,
        'cba_tipo', NEW.cba_tipo,
        'cba_metodo_pago_asociado', NEW.cba_metodo_pago_asociado,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Detectar cambios (solo campos que realmente cambiaron)
    SET v_cambios = JSON_OBJECT();
    
    IF COALESCE(OLD.cba_nombre, '') != COALESCE(NEW.cba_nombre, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.cba_nombre', JSON_OBJECT('anterior', OLD.cba_nombre, 'nuevo', NEW.cba_nombre));
    END IF;
    
    IF COALESCE(OLD.cba_tipo, '') != COALESCE(NEW.cba_tipo, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.cba_tipo', JSON_OBJECT('anterior', OLD.cba_tipo, 'nuevo', NEW.cba_tipo));
    END IF;
    
    IF COALESCE(OLD.cba_metodo_pago_asociado, '') != COALESCE(NEW.cba_metodo_pago_asociado, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.cba_metodo_pago_asociado', JSON_OBJECT('anterior', OLD.cba_metodo_pago_asociado, 'nuevo', NEW.cba_metodo_pago_asociado));
    END IF;
    
    -- Usar la función helper para registrar el cambio
    SET v_cambios_detectados = CASE 
        WHEN JSON_LENGTH(v_cambios) > 0 THEN v_cambios 
        ELSE NULL 
    END;
    
    SELECT auditar_cambio_financiero(
        'finanzas_cuentas_bancarias',
        CAST(OLD.cba_id AS CHAR),
        'UPDATE',
        v_valor_anterior,
        v_valor_nuevo,
        v_cambios_detectados
    ) INTO @dummy;
END$$

-- Trigger BEFORE DELETE: Captura el registro completo antes de eliminarlo
DROP TRIGGER IF EXISTS `trg_finanzas_cuentas_bancarias_before_delete`$$

CREATE TRIGGER `trg_finanzas_cuentas_bancarias_before_delete`
BEFORE DELETE ON `finanzas_cuentas_bancarias`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    
    -- Construir JSON con todos los datos del registro eliminado
    SET v_valor_anterior = JSON_OBJECT(
        'cba_id', OLD.cba_id,
        'cba_nombre', OLD.cba_nombre,
        'cba_tipo', OLD.cba_tipo,
        'cba_metodo_pago_asociado', OLD.cba_metodo_pago_asociado,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Registrar el DELETE
    SELECT auditar_cambio_financiero(
        'finanzas_cuentas_bancarias',
        CAST(OLD.cba_id AS CHAR),
        'DELETE',
        v_valor_anterior,
        NULL,
        NULL
    ) INTO @dummy;
END$$

-- Trigger AFTER INSERT: Captura el nuevo registro después de insertarlo
DROP TRIGGER IF EXISTS `trg_finanzas_cuentas_bancarias_after_insert`$$

CREATE TRIGGER `trg_finanzas_cuentas_bancarias_after_insert`
AFTER INSERT ON `finanzas_cuentas_bancarias`
FOR EACH ROW
BEGIN
    DECLARE v_valor_nuevo JSON;
    
    -- Construir JSON con todos los datos del nuevo registro
    SET v_valor_nuevo = JSON_OBJECT(
        'cba_id', NEW.cba_id,
        'cba_nombre', NEW.cba_nombre,
        'cba_tipo', NEW.cba_tipo,
        'cba_metodo_pago_asociado', NEW.cba_metodo_pago_asociado,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Registrar el INSERT
    SELECT auditar_cambio_financiero(
        'finanzas_cuentas_bancarias',
        CAST(NEW.cba_id AS CHAR),
        'INSERT',
        NULL,
        v_valor_nuevo,
        NULL
    ) INTO @dummy;
END$$

DELIMITER ;

-- ================================================
-- NOTAS
-- ================================================
-- 
-- Estos triggers capturan todos los cambios en finanzas_cuentas_bancarias
-- Esta tabla no tiene soft delete, solo DELETE físicos
--
-- ================================================

