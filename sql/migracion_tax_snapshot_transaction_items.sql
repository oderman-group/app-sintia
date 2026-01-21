-- Migración: Agregar columnas de snapshot para impuestos en transaction_items
-- Fecha: 2024
-- Descripción: Agrega tax_name y tax_fee para guardar snapshot del impuesto al momento de crear la factura
--              Mantiene la columna tax (FK) para integridad referencial

-- Paso 1: Agregar columnas de snapshot
ALTER TABLE `transaction_items` 
ADD COLUMN `tax_name` VARCHAR(45) DEFAULT NULL COMMENT 'Snapshot del type_tax del impuesto' AFTER `tax`,
ADD COLUMN `tax_fee` FLOAT DEFAULT NULL COMMENT 'Snapshot del fee del impuesto' AFTER `tax_name`;

-- Paso 2: Migrar datos existentes desde la tabla taxes
-- Actualiza los registros que tienen un tax asignado con los valores actuales del impuesto
UPDATE `transaction_items` ti
LEFT JOIN `taxes` t ON t.id = ti.tax AND t.institucion = ti.institucion AND t.year = ti.year
SET ti.tax_name = t.type_tax, ti.tax_fee = t.fee
WHERE ti.tax IS NOT NULL AND ti.tax != 0;

-- Verificación: Contar registros actualizados
-- SELECT COUNT(*) as registros_actualizados FROM transaction_items WHERE tax_name IS NOT NULL;
