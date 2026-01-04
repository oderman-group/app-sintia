-- ================================================
-- TRIGGERS DE AUDITORÍA PARA: transaction_items
-- ================================================

DELIMITER $$

-- Trigger BEFORE UPDATE: Captura el estado anterior antes de actualizar
DROP TRIGGER IF EXISTS `trg_transaction_items_before_update`$$

CREATE TRIGGER `trg_transaction_items_before_update`
BEFORE UPDATE ON `transaction_items`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    DECLARE v_valor_nuevo JSON;
    DECLARE v_cambios_detectados JSON;
    DECLARE v_cambios JSON;
    
    -- Construir JSON con valores anteriores (campos críticos)
    SET v_valor_anterior = JSON_OBJECT(
        'id_autoincremental', OLD.id_autoincremental,
        'id_transaction', OLD.id_transaction,
        'id_item', OLD.id_item,
        'item_name', OLD.item_name,
        'price', OLD.price,
        'cantity', OLD.cantity,
        'subtotal', OLD.subtotal,
        'discount', OLD.discount,
        'tax', OLD.tax,
        'item_type', OLD.item_type,
        'application_time', OLD.application_time,
        'type_transaction', OLD.type_transaction,
        'factura_recurrente_id', OLD.factura_recurrente_id,
        'description', OLD.description,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Construir JSON con valores nuevos
    SET v_valor_nuevo = JSON_OBJECT(
        'id_autoincremental', NEW.id_autoincremental,
        'id_transaction', NEW.id_transaction,
        'id_item', NEW.id_item,
        'item_name', NEW.item_name,
        'price', NEW.price,
        'cantity', NEW.cantity,
        'subtotal', NEW.subtotal,
        'discount', NEW.discount,
        'tax', NEW.tax,
        'item_type', NEW.item_type,
        'application_time', NEW.application_time,
        'type_transaction', NEW.type_transaction,
        'factura_recurrente_id', NEW.factura_recurrente_id,
        'description', NEW.description,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Detectar cambios (solo campos que realmente cambiaron)
    SET v_cambios = JSON_OBJECT();
    
    IF COALESCE(OLD.id_transaction, '') != COALESCE(NEW.id_transaction, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.id_transaction', JSON_OBJECT('anterior', OLD.id_transaction, 'nuevo', NEW.id_transaction));
    END IF;
    
    IF COALESCE(OLD.id_item, '') != COALESCE(NEW.id_item, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.id_item', JSON_OBJECT('anterior', OLD.id_item, 'nuevo', NEW.id_item));
    END IF;
    
    IF COALESCE(OLD.item_name, '') != COALESCE(NEW.item_name, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.item_name', JSON_OBJECT('anterior', OLD.item_name, 'nuevo', NEW.item_name));
    END IF;
    
    IF COALESCE(OLD.price, 0) != COALESCE(NEW.price, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.price', JSON_OBJECT('anterior', OLD.price, 'nuevo', NEW.price));
    END IF;
    
    IF COALESCE(OLD.cantity, 0) != COALESCE(NEW.cantity, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.cantity', JSON_OBJECT('anterior', OLD.cantity, 'nuevo', NEW.cantity));
    END IF;
    
    IF COALESCE(OLD.subtotal, 0) != COALESCE(NEW.subtotal, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.subtotal', JSON_OBJECT('anterior', OLD.subtotal, 'nuevo', NEW.subtotal));
    END IF;
    
    IF COALESCE(OLD.discount, 0) != COALESCE(NEW.discount, 0) THEN
        SET v_cambios = JSON_SET(v_cambios, '$.discount', JSON_OBJECT('anterior', OLD.discount, 'nuevo', NEW.discount));
    END IF;
    
    IF COALESCE(OLD.tax, '') != COALESCE(NEW.tax, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.tax', JSON_OBJECT('anterior', OLD.tax, 'nuevo', NEW.tax));
    END IF;
    
    IF COALESCE(OLD.item_type, '') != COALESCE(NEW.item_type, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.item_type', JSON_OBJECT('anterior', OLD.item_type, 'nuevo', NEW.item_type));
    END IF;
    
    IF COALESCE(OLD.application_time, '') != COALESCE(NEW.application_time, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.application_time', JSON_OBJECT('anterior', OLD.application_time, 'nuevo', NEW.application_time));
    END IF;
    
    IF COALESCE(OLD.type_transaction, '') != COALESCE(NEW.type_transaction, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.type_transaction', JSON_OBJECT('anterior', OLD.type_transaction, 'nuevo', NEW.type_transaction));
    END IF;
    
    IF COALESCE(OLD.factura_recurrente_id, '') != COALESCE(NEW.factura_recurrente_id, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.factura_recurrente_id', JSON_OBJECT('anterior', OLD.factura_recurrente_id, 'nuevo', NEW.factura_recurrente_id));
    END IF;
    
    IF COALESCE(OLD.description, '') != COALESCE(NEW.description, '') THEN
        SET v_cambios = JSON_SET(v_cambios, '$.description', JSON_OBJECT('anterior', OLD.description, 'nuevo', NEW.description));
    END IF;
    
    -- Usar la función helper para registrar el cambio
    SET v_cambios_detectados = CASE 
        WHEN JSON_LENGTH(v_cambios) > 0 THEN v_cambios 
        ELSE NULL 
    END;
    
    SELECT auditar_cambio_financiero(
        'transaction_items',
        CAST(OLD.id_autoincremental AS CHAR),
        'UPDATE',
        v_valor_anterior,
        v_valor_nuevo,
        v_cambios_detectados
    ) INTO @dummy;
END$$

-- Trigger BEFORE DELETE: Captura el registro completo antes de eliminarlo
DROP TRIGGER IF EXISTS `trg_transaction_items_before_delete`$$

CREATE TRIGGER `trg_transaction_items_before_delete`
BEFORE DELETE ON `transaction_items`
FOR EACH ROW
BEGIN
    DECLARE v_valor_anterior JSON;
    
    -- Construir JSON con todos los datos del registro eliminado
    SET v_valor_anterior = JSON_OBJECT(
        'id_autoincremental', OLD.id_autoincremental,
        'id_transaction', OLD.id_transaction,
        'id_item', OLD.id_item,
        'item_name', OLD.item_name,
        'price', OLD.price,
        'cantity', OLD.cantity,
        'subtotal', OLD.subtotal,
        'discount', OLD.discount,
        'tax', OLD.tax,
        'item_type', OLD.item_type,
        'application_time', OLD.application_time,
        'type_transaction', OLD.type_transaction,
        'factura_recurrente_id', OLD.factura_recurrente_id,
        'description', OLD.description,
        'institucion', OLD.institucion,
        'year', OLD.year
    );
    
    -- Registrar el DELETE
    SELECT auditar_cambio_financiero(
        'transaction_items',
        CAST(OLD.id_autoincremental AS CHAR),
        'DELETE',
        v_valor_anterior,
        NULL,
        NULL
    ) INTO @dummy;
END$$

-- Trigger AFTER INSERT: Captura el nuevo registro después de insertarlo
DROP TRIGGER IF EXISTS `trg_transaction_items_after_insert`$$

CREATE TRIGGER `trg_transaction_items_after_insert`
AFTER INSERT ON `transaction_items`
FOR EACH ROW
BEGIN
    DECLARE v_valor_nuevo JSON;
    
    -- Construir JSON con todos los datos del nuevo registro
    SET v_valor_nuevo = JSON_OBJECT(
        'id_autoincremental', NEW.id_autoincremental,
        'id_transaction', NEW.id_transaction,
        'id_item', NEW.id_item,
        'item_name', NEW.item_name,
        'price', NEW.price,
        'cantity', NEW.cantity,
        'subtotal', NEW.subtotal,
        'discount', NEW.discount,
        'tax', NEW.tax,
        'item_type', NEW.item_type,
        'application_time', NEW.application_time,
        'type_transaction', NEW.type_transaction,
        'factura_recurrente_id', NEW.factura_recurrente_id,
        'description', NEW.description,
        'institucion', NEW.institucion,
        'year', NEW.year
    );
    
    -- Registrar el INSERT
    SELECT auditar_cambio_financiero(
        'transaction_items',
        CAST(NEW.id_autoincremental AS CHAR),
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
-- Estos triggers capturan todos los cambios en transaction_items
-- Esta tabla es crítica porque contiene los items de cada transacción
--
-- ================================================

