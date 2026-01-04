-- ================================================
-- TRIGGERS DE AUDITORÍA PARA: items (PRIORIDAD MEDIA)
-- ================================================

DELIMITER $$

-- Trigger BEFORE UPDATE: Captura el estado anterior antes de actualizar
DROP TRIGGER IF EXISTS `trg_items_before_update`$$

CREATE TRIGGER `trg_items_before_update`
BEFORE UPDATE ON `items`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    DECLARE v_valor_nuevo JSON;
    DECLARE v_cambios_detectados JSON;
    DECLARE v_cambios JSON;
    
    -- Construir JSON con valores anteriores (campos críticos)
    SET v_valor_anterior = JSON_OBJECT(
        'item_id', OLD.item_id,
        'name', OLD.name,
        'price', OLD.price,
        'description', OLD.description,
        'item_type', OLD.item_type,
        'application_time', OLD.application_time,
        'status', OLD.status,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Construir JSON con valores nuevos
    SET v_valor_nuevo = JSON_OBJECT(
        'item_id', NEW.item_id,
        'name', NEW.name,
        'price', NEW.price,
        'description', NEW.description,
        'item_type', NEW.item_type,
        'application_time', NEW.application_time,
        'status', NEW.status,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Detectar cambios (solo campos que realmente cambiaron)
    SET v_cambios = JSON_OBJECT();
    
    IF COALESCE(OLD.name, '') != COALESCE(NEW.name, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.name', JSON_OBJECT('anterior', OLD.name, 'nuevo', NEW.name));
    END IF;
    
    IF COALESCE(OLD.price, 0) != COALESCE(NEW.price, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.price', JSON_OBJECT('anterior', OLD.price, 'nuevo', NEW.price));
    END IF;
    
    IF COALESCE(OLD.description, '') != COALESCE(NEW.description, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.description', JSON_OBJECT('anterior', OLD.description, 'nuevo', NEW.description));
    END IF;
    
    IF COALESCE(OLD.item_type, '') != COALESCE(NEW.item_type, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.item_type', JSON_OBJECT('anterior', OLD.item_type, 'nuevo', NEW.item_type));
    END IF;
    
    IF COALESCE(OLD.application_time, '') != COALESCE(NEW.application_time, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.application_time', JSON_OBJECT('anterior', OLD.application_time, 'nuevo', NEW.application_time));
    END IF;
    
    IF COALESCE(OLD.status, '') != COALESCE(NEW.status, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.status', JSON_OBJECT('anterior', OLD.status, 'nuevo', NEW.status));
    END IF;
    
    -- Usar la función helper para registrar el cambio
    SET v_cambios_detectados = CASE 
        WHEN JSON_LENGTH(v_cambios) > 0 THEN v_cambios 
        ELSE NULL 
    END;
    
    SELECT auditar_cambio_financiero(
        'items',
        CAST(OLD.item_id AS CHAR),
        'UPDATE',
        v_valor_anterior,
        v_valor_nuevo,
        v_cambios_detectados
    ) INTO @dummy;
END$$

-- Trigger BEFORE DELETE: Captura el registro completo antes de eliminarlo
DROP TRIGGER IF EXISTS `trg_items_before_delete`$$

CREATE TRIGGER `trg_items_before_delete`
BEFORE DELETE ON `items`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    
    -- Construir JSON con todos los datos del registro eliminado
    SET v_valor_anterior = JSON_OBJECT(
        'item_id', OLD.item_id,
        'name', OLD.name,
        'price', OLD.price,
        'description', OLD.description,
        'item_type', OLD.item_type,
        'application_time', OLD.application_time,
        'status', OLD.status,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Registrar el DELETE
    SELECT auditar_cambio_financiero(
        'items',
        CAST(OLD.item_id AS CHAR),
        'DELETE',
        v_valor_anterior,
        NULL,
        NULL
    ) INTO @dummy;
END$$

-- Trigger AFTER INSERT: Captura el nuevo registro después de insertarlo
DROP TRIGGER IF EXISTS `trg_items_after_insert`$$

CREATE TRIGGER `trg_items_after_insert`
AFTER INSERT ON `items`
FOR EACH ROW
BEGIN
    DECLARE v_valor_nuevo JSON;
    
    -- Construir JSON con todos los datos del nuevo registro
    SET v_valor_nuevo = JSON_OBJECT(
        'item_id', NEW.item_id,
        'name', NEW.name,
        'price', NEW.price,
        'description', NEW.description,
        'item_type', NEW.item_type,
        'application_time', NEW.application_time,
        'status', NEW.status,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Registrar el INSERT
    SELECT auditar_cambio_financiero(
        'items',
        CAST(NEW.item_id AS CHAR),
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
-- Estos triggers capturan todos los cambios en items (maestro de items)
-- Incluye manejo del campo status (soft delete específico)
--
-- IMPORTANTE: Los DELETE físicos se capturan, pero en producción deberían
-- ser raros ya que se usa soft delete (status)
--
-- ================================================

