-- ================================================
-- TRIGGERS DE AUDITORÍA PARA: finanzas_cuentas
-- ================================================

DELIMITER $$

-- Trigger BEFORE UPDATE: Captura el estado anterior antes de actualizar
DROP TRIGGER IF EXISTS `trg_finanzas_cuentas_before_update`$$

CREATE TRIGGER `trg_finanzas_cuentas_before_update`
BEFORE UPDATE ON `finanzas_cuentas`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    DECLARE v_valor_nuevo JSON;
    DECLARE v_cambios_detectados JSON;
    DECLARE v_cambios JSON;
    
    -- Construir JSON con valores anteriores (solo campos críticos)
    SET v_valor_anterior = JSON_OBJECT(
        'fcu_id', OLD.fcu_id,
        'fcu_fecha', OLD.fcu_fecha,
        'fcu_detalle', OLD.fcu_detalle,
        'fcu_valor', OLD.fcu_valor,
        'fcu_tipo', OLD.fcu_tipo,
        'fcu_anulado', OLD.fcu_anulado,
        'fcu_status', OLD.fcu_status,
        'fcu_consecutivo', OLD.fcu_consecutivo,
        'fcu_usuario', OLD.fcu_usuario,
        'fcu_cerrado', OLD.fcu_cerrado,
        'fcu_fecha_cerrado', OLD.fcu_fecha_cerrado,
        'fcu_cerrado_usuario', OLD.fcu_cerrado_usuario,
        'fcu_observaciones', OLD.fcu_observaciones,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Construir JSON con valores nuevos
    SET v_valor_nuevo = JSON_OBJECT(
        'fcu_id', NEW.fcu_id,
        'fcu_fecha', NEW.fcu_fecha,
        'fcu_detalle', NEW.fcu_detalle,
        'fcu_valor', NEW.fcu_valor,
        'fcu_tipo', NEW.fcu_tipo,
        'fcu_anulado', NEW.fcu_anulado,
        'fcu_status', NEW.fcu_status,
        'fcu_consecutivo', NEW.fcu_consecutivo,
        'fcu_usuario', NEW.fcu_usuario,
        'fcu_cerrado', NEW.fcu_cerrado,
        'fcu_fecha_cerrado', NEW.fcu_fecha_cerrado,
        'fcu_cerrado_usuario', NEW.fcu_cerrado_usuario,
        'fcu_observaciones', NEW.fcu_observaciones,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Detectar cambios (solo campos que realmente cambiaron)
    SET v_cambios = JSON_OBJECT();
    
    IF OLD.fcu_fecha != NEW.fcu_fecha THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_fecha', JSON_OBJECT('anterior', OLD.fcu_fecha, 'nuevo', NEW.fcu_fecha));
    END IF;
    
    IF COALESCE(OLD.fcu_detalle, '') != COALESCE(NEW.fcu_detalle, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_detalle', JSON_OBJECT('anterior', OLD.fcu_detalle, 'nuevo', NEW.fcu_detalle));
    END IF;
    
    IF COALESCE(OLD.fcu_valor, '') != COALESCE(NEW.fcu_valor, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_valor', JSON_OBJECT('anterior', OLD.fcu_valor, 'nuevo', NEW.fcu_valor));
    END IF;
    
    IF COALESCE(OLD.fcu_tipo, '') != COALESCE(NEW.fcu_tipo, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_tipo', JSON_OBJECT('anterior', OLD.fcu_tipo, 'nuevo', NEW.fcu_tipo));
    END IF;
    
    IF COALESCE(OLD.fcu_anulado, 0) != COALESCE(NEW.fcu_anulado, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_anulado', JSON_OBJECT('anterior', OLD.fcu_anulado, 'nuevo', NEW.fcu_anulado));
    END IF;
    
    IF COALESCE(OLD.fcu_status, '') != COALESCE(NEW.fcu_status, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_status', JSON_OBJECT('anterior', OLD.fcu_status, 'nuevo', NEW.fcu_status));
    END IF;
    
    IF COALESCE(OLD.fcu_consecutivo, '') != COALESCE(NEW.fcu_consecutivo, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_consecutivo', JSON_OBJECT('anterior', OLD.fcu_consecutivo, 'nuevo', NEW.fcu_consecutivo));
    END IF;
    
    IF COALESCE(OLD.fcu_usuario, '') != COALESCE(NEW.fcu_usuario, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_usuario', JSON_OBJECT('anterior', OLD.fcu_usuario, 'nuevo', NEW.fcu_usuario));
    END IF;
    
    IF COALESCE(OLD.fcu_cerrado, 0) != COALESCE(NEW.fcu_cerrado, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_cerrado', JSON_OBJECT('anterior', OLD.fcu_cerrado, 'nuevo', NEW.fcu_cerrado));
    END IF;
    
    IF OLD.fcu_fecha_cerrado != NEW.fcu_fecha_cerrado THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_fecha_cerrado', JSON_OBJECT('anterior', OLD.fcu_fecha_cerrado, 'nuevo', NEW.fcu_fecha_cerrado));
    END IF;
    
    IF COALESCE(OLD.fcu_cerrado_usuario, '') != COALESCE(NEW.fcu_cerrado_usuario, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_cerrado_usuario', JSON_OBJECT('anterior', OLD.fcu_cerrado_usuario, 'nuevo', NEW.fcu_cerrado_usuario));
    END IF;
    
    IF COALESCE(OLD.fcu_observaciones, '') != COALESCE(NEW.fcu_observaciones, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fcu_observaciones', JSON_OBJECT('anterior', OLD.fcu_observaciones, 'nuevo', NEW.fcu_observaciones));
    END IF;
    
    -- Usar la función helper para registrar el cambio
    SET v_cambios_detectados = CASE 
        WHEN JSON_LENGTH(v_cambios) > 0 THEN v_cambios 
        ELSE NULL 
    END;
    
    SELECT auditar_cambio_financiero(
        'finanzas_cuentas',
        CAST(OLD.fcu_id AS CHAR),
        'UPDATE',
        v_valor_anterior,
        v_valor_nuevo,
        v_cambios_detectados
    ) INTO @dummy;
END$$

-- Trigger BEFORE DELETE: Captura el registro completo antes de eliminarlo
DROP TRIGGER IF EXISTS `trg_finanzas_cuentas_before_delete`$$

CREATE TRIGGER `trg_finanzas_cuentas_before_delete`
BEFORE DELETE ON `finanzas_cuentas`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    
    -- Construir JSON con todos los datos del registro eliminado
    SET v_valor_anterior = JSON_OBJECT(
        'fcu_id', OLD.fcu_id,
        'fcu_fecha', OLD.fcu_fecha,
        'fcu_detalle', OLD.fcu_detalle,
        'fcu_valor', OLD.fcu_valor,
        'fcu_tipo', OLD.fcu_tipo,
        'fcu_anulado', OLD.fcu_anulado,
        'fcu_status', OLD.fcu_status,
        'fcu_consecutivo', OLD.fcu_consecutivo,
        'fcu_usuario', OLD.fcu_usuario,
        'fcu_cerrado', OLD.fcu_cerrado,
        'fcu_fecha_cerrado', OLD.fcu_fecha_cerrado,
        'fcu_cerrado_usuario', OLD.fcu_cerrado_usuario,
        'fcu_observaciones', OLD.fcu_observaciones,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Registrar el DELETE
    SELECT auditar_cambio_financiero(
        'finanzas_cuentas',
        CAST(OLD.fcu_id AS CHAR),
        'DELETE',
        v_valor_anterior,
        NULL,
        NULL
    ) INTO @dummy;
END$$

-- Trigger AFTER INSERT: Captura el nuevo registro después de insertarlo
DROP TRIGGER IF EXISTS `trg_finanzas_cuentas_after_insert`$$

CREATE TRIGGER `trg_finanzas_cuentas_after_insert`
AFTER INSERT ON `finanzas_cuentas`
FOR EACH ROW
BEGIN
    DECLARE v_valor_nuevo JSON;
    
    -- Construir JSON con todos los datos del nuevo registro
    SET v_valor_nuevo = JSON_OBJECT(
        'fcu_id', NEW.fcu_id,
        'fcu_fecha', NEW.fcu_fecha,
        'fcu_detalle', NEW.fcu_detalle,
        'fcu_valor', NEW.fcu_valor,
        'fcu_tipo', NEW.fcu_tipo,
        'fcu_anulado', NEW.fcu_anulado,
        'fcu_status', NEW.fcu_status,
        'fcu_consecutivo', NEW.fcu_consecutivo,
        'fcu_usuario', NEW.fcu_usuario,
        'fcu_cerrado', NEW.fcu_cerrado,
        'fcu_fecha_cerrado', NEW.fcu_fecha_cerrado,
        'fcu_cerrado_usuario', NEW.fcu_cerrado_usuario,
        'fcu_observaciones', NEW.fcu_observaciones,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Registrar el INSERT
    SELECT auditar_cambio_financiero(
        'finanzas_cuentas',
        CAST(NEW.fcu_id AS CHAR),
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
-- Estos triggers capturan todos los cambios en finanzas_cuentas
-- Incluye manejo del campo fcu_anulado (soft delete específico de esta tabla)
--
-- IMPORTANTE: Los DELETE físicos se capturan, pero en producción deberían
-- ser raros ya que se usa soft delete (fcu_anulado = 1)
--
-- ================================================

