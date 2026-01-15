-- ================================================
-- CORRECCIÓN DE COLLATION: instituciones_vinculadas
-- Base de datos: mobiliar_sintia_admin
-- Problema reportado: collation_connection utf8mb4_general_ci vs utf8mb4_unicode_ci
-- Nota: Esta función solo compara enteros (is_sede1/is_sede2), pero la recreamos
-- para que no herede collation_connection incorrecta.
-- ================================================

-- Objetivo: Recrear la FUNCTION para fijar `collation_connection=utf8mb4_unicode_ci` y mantener consistencia en `mobiliar_sintia_admin`.
-- Ejecución (DBeaver): Ejecuta el archivo completo como script (no por selección parcial). Requiere permisos DROP/CREATE FUNCTION.

-- Qué hace:
-- - Devuelve 1 si dos instituciones están vinculadas en `institucion_sedes` (en cualquiera de los dos sentidos), si no devuelve 0.
-- Parámetros:
-- - id1 (INT): Institución 1.
-- - id2 (INT): Institución 2.
-- Retorna:
-- - TINYINT(1): 1/0.

-- Establecer collation correcta antes de crear la función
SET collation_connection = 'utf8mb4_unicode_ci';

DELIMITER $$

-- Eliminar función existente
DROP FUNCTION IF EXISTS `mobiliar_sintia_admin`.`instituciones_vinculadas`$$

-- Crear función con collation correcta
CREATE DEFINER=`mobiliar`@`localhost` FUNCTION `mobiliar_sintia_admin`.`instituciones_vinculadas`(
    id1 INT,
    id2 INT
) RETURNS TINYINT(1)
BEGIN
    DECLARE vinculado_count INT;

    SELECT COUNT(*) INTO vinculado_count
    FROM mobiliar_sintia_admin.institucion_sedes is2
    WHERE (is2.is_sede1 = id1 AND is2.is_sede2 = id2)
       OR (is2.is_sede1 = id2 AND is2.is_sede2 = id1);

    RETURN vinculado_count > 0;
END$$

DELIMITER ;

-- ================================================
-- NOTAS:
-- 1. Se estableció collation_connection a utf8mb4_unicode_ci al inicio
-- 2. Se mantiene el DEFINER original: mobiliar@localhost
-- 3. Esta función se usa desde PHP (ver main-app/class/Instituciones.php)
-- ================================================

