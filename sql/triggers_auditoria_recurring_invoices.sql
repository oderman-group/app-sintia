-- ================================================
-- TRIGGERS DE AUDITORÍA PARA: recurring_invoices
-- ================================================

DELIMITER $$

-- Trigger BEFORE UPDATE: Captura el estado anterior antes de actualizar
DROP TRIGGER IF EXISTS `trg_recurring_invoices_before_update`$$

CREATE TRIGGER `trg_recurring_invoices_before_update`
BEFORE UPDATE ON `recurring_invoices`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    DECLARE v_valor_nuevo JSON;
    DECLARE v_cambios_detectados JSON;
    DECLARE v_cambios JSON;
    
    -- Construir JSON con valores anteriores (campos críticos)
    SET v_valor_anterior = JSON_OBJECT(
        'id', OLD.id,
        'user', OLD.user,
        'date_start', OLD.date_start,
        'date_finish', OLD.date_finish,
        'frequency', OLD.frequency,
        'days_in_month', OLD.days_in_month,
        'detail', OLD.detail,
        'additional_value', OLD.additional_value,
        'invoice_type', OLD.invoice_type,
        'observation', OLD.observation,
        'is_deleted', OLD.is_deleted,
        'responsible_user', OLD.responsible_user,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Construir JSON con valores nuevos
    SET v_valor_nuevo = JSON_OBJECT(
        'id', NEW.id,
        'user', NEW.user,
        'date_start', NEW.date_start,
        'date_finish', NEW.date_finish,
        'frequency', NEW.frequency,
        'days_in_month', NEW.days_in_month,
        'detail', NEW.detail,
        'additional_value', NEW.additional_value,
        'invoice_type', NEW.invoice_type,
        'observation', NEW.observation,
        'is_deleted', NEW.is_deleted,
        'responsible_user', NEW.responsible_user,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Detectar cambios (solo campos que realmente cambiaron)
    SET v_cambios = JSON_OBJECT();
    
    IF COALESCE(OLD.user, '') != COALESCE(NEW.user, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.user', JSON_OBJECT('anterior', OLD.user, 'nuevo', NEW.user));
    END IF;
    
    IF OLD.date_start != NEW.date_start THEN
        SET v_cambios = JSON_SET(v_cambios, '$.date_start', JSON_OBJECT('anterior', OLD.date_start, 'nuevo', NEW.date_start));
    END IF;
    
    IF OLD.date_finish != NEW.date_finish THEN
        SET v_cambios = JSON_SET(v_cambios, '$.date_finish', JSON_OBJECT('anterior', OLD.date_finish, 'nuevo', NEW.date_finish));
    END IF;
    
    IF COALESCE(OLD.frequency, '') != COALESCE(NEW.frequency, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.frequency', JSON_OBJECT('anterior', OLD.frequency, 'nuevo', NEW.frequency));
    END IF;
    
    IF COALESCE(OLD.days_in_month, '') != COALESCE(NEW.days_in_month, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.days_in_month', JSON_OBJECT('anterior', OLD.days_in_month, 'nuevo', NEW.days_in_month));
    END IF;
    
    IF COALESCE(OLD.detail, '') != COALESCE(NEW.detail, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.detail', JSON_OBJECT('anterior', OLD.detail, 'nuevo', NEW.detail));
    END IF;
    
    IF COALESCE(OLD.additional_value, 0) != COALESCE(NEW.additional_value, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.additional_value', JSON_OBJECT('anterior', OLD.additional_value, 'nuevo', NEW.additional_value));
    END IF;
    
    IF COALESCE(OLD.invoice_type, '') != COALESCE(NEW.invoice_type, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.invoice_type', JSON_OBJECT('anterior', OLD.invoice_type, 'nuevo', NEW.invoice_type));
    END IF;
    
    IF COALESCE(OLD.observation, '') != COALESCE(NEW.observation, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.observation', JSON_OBJECT('anterior', OLD.observation, 'nuevo', NEW.observation));
    END IF;
    
    IF COALESCE(OLD.is_deleted, 0) != COALESCE(NEW.is_deleted, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.is_deleted', JSON_OBJECT('anterior', OLD.is_deleted, 'nuevo', NEW.is_deleted));
    END IF;
    
    IF COALESCE(OLD.responsible_user, '') != COALESCE(NEW.responsible_user, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.responsible_user', JSON_OBJECT('anterior', OLD.responsible_user, 'nuevo', NEW.responsible_user));
    END IF;
    
    -- Usar la función helper para registrar el cambio
    SET v_cambios_detectados = CASE 
        WHEN JSON_LENGTH(v_cambios) > 0 THEN v_cambios 
        ELSE NULL 
    END;
    
    SELECT auditar_cambio_financiero(
        'recurring_invoices',
        CAST(OLD.id AS CHAR),
        'UPDATE',
        v_valor_anterior,
        v_valor_nuevo,
        v_cambios_detectados
    ) INTO @dummy;
END$$

-- Trigger BEFORE DELETE: Captura el registro completo antes de eliminarlo
DROP TRIGGER IF EXISTS `trg_recurring_invoices_before_delete`$$

CREATE TRIGGER `trg_recurring_invoices_before_delete`
BEFORE DELETE ON `recurring_invoices`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    
    -- Construir JSON con todos los datos del registro eliminado
    SET v_valor_anterior = JSON_OBJECT(
        'id', OLD.id,
        'user', OLD.user,
        'date_start', OLD.date_start,
        'date_finish', OLD.date_finish,
        'frequency', OLD.frequency,
        'days_in_month', OLD.days_in_month,
        'detail', OLD.detail,
        'additional_value', OLD.additional_value,
        'invoice_type', OLD.invoice_type,
        'observation', OLD.observation,
        'is_deleted', OLD.is_deleted,
        'responsible_user', OLD.responsible_user,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Registrar el DELETE
    SELECT auditar_cambio_financiero(
        'recurring_invoices',
        CAST(OLD.id AS CHAR),
        'DELETE',
        v_valor_anterior,
        NULL,
        NULL
    ) INTO @dummy;
END$$

-- Trigger AFTER INSERT: Captura el nuevo registro después de insertarlo
DROP TRIGGER IF EXISTS `trg_recurring_invoices_after_insert`$$

CREATE TRIGGER `trg_recurring_invoices_after_insert`
AFTER INSERT ON `recurring_invoices`
FOR EACH ROW
BEGIN
    DECLARE v_valor_nuevo JSON;
    
    -- Construir JSON con todos los datos del nuevo registro
    SET v_valor_nuevo = JSON_OBJECT(
        'id', NEW.id,
        'user', NEW.user,
        'date_start', NEW.date_start,
        'date_finish', NEW.date_finish,
        'frequency', NEW.frequency,
        'days_in_month', NEW.days_in_month,
        'detail', NEW.detail,
        'additional_value', NEW.additional_value,
        'invoice_type', NEW.invoice_type,
        'observation', NEW.observation,
        'is_deleted', NEW.is_deleted,
        'responsible_user', NEW.responsible_user,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Registrar el INSERT
    SELECT auditar_cambio_financiero(
        'recurring_invoices',
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
-- Estos triggers capturan todos los cambios en recurring_invoices (facturas recurrentes)
-- Incluye manejo del campo is_deleted (soft delete)
--
-- IMPORTANTE: Los DELETE físicos se capturan, pero en producción deberían
-- ser raros ya que se usa soft delete (is_deleted = 1)
--
-- ================================================

