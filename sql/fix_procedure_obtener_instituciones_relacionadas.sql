-- ================================================
-- CORRECCIÓN DE COLLATION: obtener_instituciones_relacionadas
-- Base de datos: mobiliar_sintia_admin
-- Problema reportado: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- Nota: Este procedimiento solo compara enteros (INS_ID / is_sede1 / is_sede2),
-- pero lo recreamos para que no herede collation_connection incorrecta.
-- ================================================

-- Objetivo: Recrear el PROCEDURE para fijar `collation_connection=utf8mb4_unicode_ci` y mantener consistencia en `mobiliar_sintia_admin`.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE PROCEDURE.

-- Qué hace:
-- - Retorna las instituciones “relacionadas/vinculadas” a una institución base, usando `institucion_sedes` (ambos sentidos).
-- Parámetros:
-- - id_institucion (INT): Institución base (se excluye del resultado).

-- Establecer collation correcta antes de crear el procedimiento
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar procedimiento existente
DROP PROCEDURE IF EXISTS `mobiliar_sintia_admin`.`obtener_instituciones_relacionadas`$$

-- Crear procedimiento con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` PROCEDURE `mobiliar_sintia_admin`.`obtener_instituciones_relacionadas`(
    IN id_institucion INT
)
BEGIN
    SELECT DISTINCT i.ins_id, i.ins_nombre, i.ins_siglas, i.ins_bd, i.ins_year_default
    FROM mobiliar_sintia_admin.instituciones i
    WHERE i.ins_id IN (
        SELECT is2.is_sede1
        FROM mobiliar_sintia_admin.institucion_sedes is2
        WHERE is2.is_sede2 = id_institucion
        UNION
        SELECT is2.is_sede2
        FROM mobiliar_sintia_admin.institucion_sedes is2
        WHERE is2.is_sede1 = id_institucion
    ) AND i.ins_id != id_institucion;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Se mantiene el DEFINER original: mobiliar@localhost
-- 3. Este procedimiento es invocado desde PHP (ver main-app/class/Instituciones.php)
-- ================================================

