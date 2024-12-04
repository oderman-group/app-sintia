<?php
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_interface.php");

abstract class BDT_Tablas implements BDT_Interface{

    public static $schema;
    public static $tableName;

    /**
     * Obtiene el nombre de la tabla asociada a la clase.
     *
     * @return string - Nombre de la tabla asociada a la clase.
     *
     * @example
     * ```php
     * // Ejemplo de uso para obtener el nombre de la tabla asociada a la clase
     * $tableName = MiClase::getTableName();
     * // $tableName contendrá el nombre de la tabla asociada a la clase MiClase.
     * ```
     */
    public static function getTableName() {
        return static::$tableName;
    }

    /**
     * Realiza una consulta en la base de datos utilizando una tabla específica y predicados opcionales.
     *
     * @param Array $predicado Un arreglo opcional que contiene los predicados para filtrar los resultados.
     *
     * @return PDOStatement|false Un objeto PDOStatement que contiene los resultados de la consulta o false en caso de error.
     * @throws Exception Si ocurre un error al preparar la consulta.
     */
    public static function Select(Array $predicado = [], $campos = '*', $bd = BD_ACADEMICA) {
        $conexionPDO = Conexion::newConnection('PDO');
        $where = '';

        $campos ??= '*';

        if( !empty($predicado) ) {
            $where = "WHERE ";
            foreach( $predicado as $clave => $valor ) {
                $where .= $clave ."='".$valor."' AND ";
            }
            $where = substr($where, 0, -5);
        }
        
        try {
            $consulta = "SELECT $campos FROM {$bd}.".static::$tableName." {$where}";
            $stmt = $conexionPDO->prepare($consulta);

            if ($stmt) {

                $stmt->execute();

                return $stmt;

            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        } catch (PDOException  $e) {
            echo "Excepción capturada: " . $e->getMessage();
            return null;
        }

    }

    public static function Insert(array $datos, $bd = BD_ACADEMICA): ?string
    {
        global $conexionPDO;

        if (is_null($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }

        $campos   = implode(", ", array_keys($datos));
        $valores  = implode("', '", array_values($datos));
        $consulta = "INSERT INTO {$bd}.".static::$tableName." ({$campos}) VALUES ('{$valores}')";

        try {
            $stmt = $conexionPDO->prepare($consulta);
            $stmt->execute();
            return $conexionPDO->lastInsertId();
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return null;
        }
    }

    public static function Update(array $datos, array $predicado, $bd = BD_ACADEMICA): bool {
        global $conexionPDO;

        if (is_null($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }

        $sets = '';

        foreach( $datos as $clave => $valor ) {
            $sets.= $clave."='{$valor}', ";
        }

        $sets = substr($sets, 0, -2);
        $where = '';

        foreach( $predicado as $clave => $valor ) {
            $where.= $clave."='{$valor}' AND ";
        }

        $where = substr($where, 0, -5);
        $consulta = "UPDATE {$bd}.".static::$tableName." SET {$sets} WHERE {$where}";

        try {
            $stmt = $conexionPDO->prepare($consulta);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return false;
        }
    }

    public static function Delete(array $predicado, $bd = BD_ACADEMICA): bool {
        global $conexionPDO;

        if (is_null($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }

        $where = '';
        
        foreach( $predicado as $clave => $valor ) {
            $where.= $clave."='{$valor}' AND ";
        }
        
        $where = substr($where, 0, -5);
        $consulta = "DELETE FROM {$bd}.".static::$tableName." WHERE {$where}";

        try {
            $stmt = $conexionPDO->prepare($consulta);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return false;
        }
    }

    public static function InsertOrUpdate(array $datos, array $predicado, $bd = BD_ACADEMICA): bool {
        global $conexionPDO;

        if (is_null($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }

        $campos   = implode(", ", array_keys($datos));
        $valores  = implode("', '", array_values($datos));
        $consulta = "INSERT INTO {$bd}.".static::$tableName." ({$campos}) VALUES ('{$valores}') ON DUPLICATE KEY UPDATE ";
        
        foreach( $datos as $clave => $valor ) {
            $consulta.= $clave."='{$valor}', ";
        }
        
        $consulta = substr($consulta, 0, -2);
        $where = '';
        
        foreach( $predicado as $clave => $valor ) {
            $where.= $clave."='{$valor}' AND ";
        }
        
        $where = substr($where, 0, -5);
        $consulta.= " WHERE {$where}";

        try {
            $stmt = $conexionPDO->prepare($consulta);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return false;
        }
    }

    public static function deleteBeforeInsert(array $datos, array $predicado, $bd = BD_ACADEMICA): bool {
        global $conexionPDO;

        if (is_null($conexionPDO)) {
            $conexionPDO = Conexion::newConnection('PDO');
        }

        $where = '';
        
        foreach( $predicado as $clave => $valor ) {
            $where.= $clave."='{$valor}' AND ";
        }

        $where = substr($where, 0, -5);
        $consulta = "DELETE FROM {$bd}.".static::$tableName." WHERE {$where}";

        try {
            $stmt = $conexionPDO->prepare($consulta);
            $stmt->execute();
            return self::Insert($datos, $bd);
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return false;
        }
    }

    /**
     * Obtiene el número de filas resultantes de una consulta en la base de datos.
     *
     * @param Array $predicado Un arreglo opcional de predicados para filtrar los resultados.
     *
     * @return int El número de filas resultantes de la consulta.
     */
    public static function numRows(array $predicado = []) {
        $schema = BD_ACADEMICA;

        if(property_exists(self::class, 'schema') && !empty(static::$schema)) {
            $schema = static::$schema;
        }

        $consulta   = self::Select($predicado, '*', $schema);
        $numRecords = $consulta->rowCount();

        return $numRecords;
    }
}