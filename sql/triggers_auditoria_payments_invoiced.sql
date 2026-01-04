-- ================================================
-- TRIGGERS DE AUDITORÍA PARA: payments_invoiced
-- ================================================

DELIMITER $$

-- Trigger BEFORE UPDATE: Captura el estado anterior antes de actualizar
DROP TRIGGER IF EXISTS `trg_payments_invoiced_before_update`$$

CREATE TRIGGER `trg_payments_invoiced_before_update`
BEFORE UPDATE ON `payments_invoiced`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    DECLARE v_valor_nuevo JSON;
    DECLARE v_cambios_detectados JSON;
    DECLARE v_cambios JSON;
    
    -- Construir JSON con valores anteriores (campos críticos)
    SET v_valor_anterior = JSON_OBJECT(
        'id', OLD.id,
        'invoiced', OLD.invoiced,
        'payment', OLD.payment,
        'payment_tipo', OLD.payment_tipo,
        'payment_method', OLD.payment_method,
        'type_payments', OLD.type_payments,
        'payment_cuenta_bancaria_id', OLD.payment_cuenta_bancaria_id,
        'observation', OLD.observation,
        'note', OLD.note,
        'fecha_documento', OLD.fecha_documento,
        'attachment', OLD.attachment,
        'is_deleted', OLD.is_deleted,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Construir JSON con valores nuevos
    SET v_valor_nuevo = JSON_OBJECT(
        'id', NEW.id,
        'invoiced', NEW.invoiced,
        'payment', NEW.payment,
        'payment_tipo', NEW.payment_tipo,
        'payment_method', NEW.payment_method,
        'type_payments', NEW.type_payments,
        'payment_cuenta_bancaria_id', NEW.payment_cuenta_bancaria_id,
        'observation', NEW.observation,
        'note', NEW.note,
        'fecha_documento', NEW.fecha_documento,
        'attachment', NEW.attachment,
        'is_deleted', NEW.is_deleted,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Detectar cambios (solo campos que realmente cambiaron)
    SET v_cambios = JSON_OBJECT();
    
    IF COALESCE(OLD.invoiced, '') != COALESCE(NEW.invoiced, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.invoiced', JSON_OBJECT('anterior', OLD.invoiced, 'nuevo', NEW.invoiced));
    END IF;
    
    IF (OLD.payment IS NULL AND NEW.payment IS NOT NULL) OR 
       (OLD.payment IS NOT NULL AND NEW.payment IS NULL) OR
       (OLD.payment IS NOT NULL AND NEW.payment IS NOT NULL AND TRIM(OLD.payment) != TRIM(NEW.payment)) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.payment', JSON_OBJECT('anterior', OLD.payment, 'nuevo', NEW.payment));
    END IF;
    
    IF COALESCE(OLD.payment_tipo, '') != COALESCE(NEW.payment_tipo, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.payment_tipo', JSON_OBJECT('anterior', OLD.payment_tipo, 'nuevo', NEW.payment_tipo));
    END IF;
    
    IF COALESCE(OLD.payment_method, '') != COALESCE(NEW.payment_method, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.payment_method', JSON_OBJECT('anterior', OLD.payment_method, 'nuevo', NEW.payment_method));
    END IF;
    
    IF COALESCE(OLD.type_payments, '') != COALESCE(NEW.type_payments, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.type_payments', JSON_OBJECT('anterior', OLD.type_payments, 'nuevo', NEW.type_payments));
    END IF;
    
    IF COALESCE(OLD.payment_cuenta_bancaria_id, '') != COALESCE(NEW.payment_cuenta_bancaria_id, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.payment_cuenta_bancaria_id', JSON_OBJECT('anterior', OLD.payment_cuenta_bancaria_id, 'nuevo', NEW.payment_cuenta_bancaria_id));
    END IF;
    
    IF COALESCE(OLD.observation, '') != COALESCE(NEW.observation, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.observation', JSON_OBJECT('anterior', OLD.observation, 'nuevo', NEW.observation));
    END IF;
    
    IF COALESCE(OLD.note, '') != COALESCE(NEW.note, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.note', JSON_OBJECT('anterior', OLD.note, 'nuevo', NEW.note));
    END IF;
    
    IF OLD.fecha_documento != NEW.fecha_documento THEN
        SET v_cambios = JSON_SET(v_cambios, '$.fecha_documento', JSON_OBJECT('anterior', OLD.fecha_documento, 'nuevo', NEW.fecha_documento));
    END IF;
    
    IF COALESCE(OLD.attachment, '') != COALESCE(NEW.attachment, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.attachment', JSON_OBJECT('anterior', OLD.attachment, 'nuevo', NEW.attachment));
    END IF;
    
    IF COALESCE(OLD.is_deleted, 0) != COALESCE(NEW.is_deleted, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.is_deleted', JSON_OBJECT('anterior', OLD.is_deleted, 'nuevo', NEW.is_deleted));
    END IF;
    
    -- Usar la función helper para registrar el cambio
    SET v_cambios_detectados = CASE 
        WHEN JSON_LENGTH(v_cambios) > 0 THEN v_cambios 
        ELSE NULL 
    END;
    
    SELECT auditar_cambio_financiero(
        'payments_invoiced',
        CAST(OLD.id AS CHAR),
        'UPDATE',
        v_valor_anterior,
        v_valor_nuevo,
        v_cambios_detectados
    ) INTO @dummy;
END$$

-- Trigger BEFORE DELETE: Captura el registro completo antes de eliminarlo
DROP TRIGGER IF EXISTS `trg_payments_invoiced_before_delete`$$

CREATE TRIGGER `trg_payments_invoiced_before_delete`
BEFORE DELETE ON `payments_invoiced`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    
    -- Construir JSON con todos los datos del registro eliminado
    SET v_valor_anterior = JSON_OBJECT(
        'id', OLD.id,
        'invoiced', OLD.invoiced,
        'payment', OLD.payment,
        'payment_tipo', OLD.payment_tipo,
        'payment_method', OLD.payment_method,
        'type_payments', OLD.type_payments,
        'payment_cuenta_bancaria_id', OLD.payment_cuenta_bancaria_id,
        'observation', OLD.observation,
        'note', OLD.note,
        'fecha_documento', OLD.fecha_documento,
        'attachment', OLD.attachment,
        'is_deleted', OLD.is_deleted,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Registrar el DELETE
    SELECT auditar_cambio_financiero(
        'payments_invoiced',
        CAST(OLD.id AS CHAR),
        'DELETE',
        v_valor_anterior,
        NULL,
        NULL
    ) INTO @dummy;
END$$

-- Trigger AFTER INSERT: Captura el nuevo registro después de insertarlo
DROP TRIGGER IF EXISTS `trg_payments_invoiced_after_insert`$$

CREATE TRIGGER `trg_payments_invoiced_after_insert`
AFTER INSERT ON `payments_invoiced`
FOR EACH ROW
BEGIN
    DECLARE v_valor_nuevo JSON;
    
    -- Construir JSON con todos los datos del nuevo registro
    SET v_valor_nuevo = JSON_OBJECT(
        'id', NEW.id,
        'invoiced', NEW.invoiced,
        'payment', NEW.payment,
        'payment_tipo', NEW.payment_tipo,
        'payment_method', NEW.payment_method,
        'type_payments', NEW.type_payments,
        'payment_cuenta_bancaria_id', NEW.payment_cuenta_bancaria_id,
        'observation', NEW.observation,
        'note', NEW.note,
        'fecha_documento', NEW.fecha_documento,
        'attachment', NEW.attachment,
        'is_deleted', NEW.is_deleted,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Registrar el INSERT
    SELECT auditar_cambio_financiero(
        'payments_invoiced',
        CAST(NEW.id AS CHAR),
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
-- Estos triggers capturan todos los cambios en payments_invoiced (abonos/pagos)
-- Incluye manejo del campo is_deleted (soft delete)
--
-- IMPORTANTE: Los DELETE físicos se capturan, pero en producción deberían
-- ser raros ya que se usa soft delete (is_deleted = 1)
--
-- ================================================

