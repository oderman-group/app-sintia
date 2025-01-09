<?php
require_once(ROOT_PATH."/main-app/class/Conexion.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_interface.php");

abstract class BDT_Tablas implements BDT_Interface{

    public const INNER = 'INNER';
    public const OTHER_PREDICATE = 'OTHER_PREDICATE';
    public const LEFT = 'LEFT';
    public static $schema;
    public static $tableName;
    public static  $tableAs;
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
                if ($clave === self::OTHER_PREDICATE) {
                    $where.= " {$valor} AND ";
                }else{
                    $where .= $clave ." = ".self::formatValor($valor)." AND ";
                }
                
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
            if ($clave === self::OTHER_PREDICATE) {
                $where.= " {$valor} AND ";
            }else{
                $where .= $clave ." = ".self::formatValor($valor)." AND ";
            }
        }

        $where = substr($where, 0, -5);
        $consulta = " UPDATE ".static::$schema.".".static::$tableName." SET {$sets} WHERE {$where}";

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
            if ($clave === OTHER_PREDICATE) {
                $where.= $clave."  {$valor} ";
            }else{
                $where.= $clave." = " .self::formatValor($valor)." AND ";
            }
            
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
 * Ejecuta una consulta SQL utilizando una conexión PDO y retorna los resultados.
 *
 * @param string $sql Consulta SQL a ejecutar.
 *
 * @return array|null Devuelve un array asociativo con los resultados de la consulta en caso de éxito. 
 *                    Retorna `null` en caso de una excepción.
 *
 * @throws PDOException Si ocurre algún error durante la preparación o ejecución de la consulta.
 */
    public static function ejecutarSQL(String $sql){

        $conexionPDO = Conexion::newConnection('PDO');
        try {
            $stmt = $conexionPDO->prepare($sql);            
            $stmt->execute();              
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            return $result;
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return null;
        }
    }

    /**
 * Construye y ejecuta dinámicamente una consulta SQL con múltiples `JOIN`, predicados y opciones de ordenamiento.
 *
 * @param array  $predicado    Array de condiciones para el filtro `WHERE`. Las claves son los campos o operadores (`AND`, `OR`), y los valores son los valores de comparación.
 * @param string $campos       Campos a seleccionar en la consulta. Por defecto, selecciona todos los campos (`'*'`).
 * @param string $ClasePrincipal Clase principal que define la tabla base. Debe extender de `BDT_Tablas`.
 * @param array  $clasesJoin   Array de clases que representan las tablas para realizar `JOIN`. Deben implementar `BDT_JoinImplements`.
 * @param string $joinString   Cláusula adicional para los `JOIN` (opcional).
 * @param string $orderBy      Cláusula de ordenamiento `ORDER BY` (opcional).
 *
 * @return array|null Devuelve el resultado de la consulta SQL como un array. Retorna `null` en caso de excepción.
 *
 * @throws Exception Si la clase principal no extiende `BDT_Tablas` o las clases `JOIN` no implementan `BDT_JoinImplements`.
 */
    public static function SelectJoin(Array $predicado, $campos,$ClasePrincipal,array $clasesJoin,String $joinString = '',String $orderBy = ''): array|null{
      
        try {
            $result ='';
            $campos ??= '*';
            $predicado ??= [];
            $orderBy =  !empty($orderBy)? "ORDER BY ".$orderBy: ""; 

            if (!is_subclass_of($ClasePrincipal, BDT_Tablas::class)) {
                throw new Exception("la clase  \$clasePrincipal deben extender de BDT_Tablas.");
            }
            // extructura de la clase principal
            $schema = $ClasePrincipal::$schema;
            $table  = $ClasePrincipal::$tableName;
            $as     = $ClasePrincipal::$tableAs;

            // Construir JOIN dinámico
            $joinClauses = '';
            foreach ($clasesJoin as $clase) {               
                if (in_array($clase, class_implements(BDT_JoinImplements::class))) {
                    throw new Exception("Todas las clases Join en \$clasesJoin deben implmentar de BDT_JoinImplements.");
                } 
                $joinSchema = $clase::$schema;
                $joinTable  = $clase::$tableName;
                $joinAs     = $clase::$tableAs;
                $joinKey    = $clase::getForeignKey(); 
                $tipoJoin   = $clase::getTypeJoin();

                if( !empty($joinKey) ) {
                    $conditionsJoin = [];
                    foreach( $joinKey as $onclave => $onvalor ) {                       
                        if ($onclave === 'AND' || $onvalor === 'OR') {
                            $conditionsJoin[] = "($onvalor)";
                        }else{
                            $asociacion = explode(" ",$onclave);
                            if(empty($asociacion[1])){
                                $conditionsJoin[] = $joinAs.'.'.$onclave ." = ".$onvalor;
                            }else{
                                $conditionsJoin[] = $joinAs.'.'.$onclave ." ".$onvalor;
                            }                            
                        }                        
                    }
                    $On = "ON " . implode("\n AND ", $conditionsJoin);
                    $joinClauses .= "\n {$tipoJoin} JOIN {$joinSchema}.{$joinTable} AS {$joinAs}  {$On} \n";
                } 
            }

             // Construir WHERE dinámico
             if( !empty($predicado) ) {               
                $conditions = [];
                foreach( $predicado as $clave => $valor ) {

                    if ($clave === 'AND' || $clave === 'OR') {
                        $conditions[] = "($valor)";
                    }else{
                        $asociacion = explode(" ",$clave);
                        if(empty($asociacion[1])){
                            $conditions[] = $clave ." = ".$valor;
                        }else{
                            $conditions[] = $clave ." ".$valor;
                        }
                        
                    }
                    
                }
                $where = "\n WHERE " . implode("\n AND ", $conditions);
            } 
            

            $consulta = "SELECT $campos FROM {$schema}.{$table} AS {$as}  \n            
            {$joinClauses}
            \n
            {$joinString}
             \n
            {$where}
            \n
            {$orderBy} \n";
            return self::ejecutarSQL($consulta); ;
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return null;
        }
    }

    /**
     * Ejecuta una consulta SELECT con opciones de ordenación y límite.
     * 
     * @param array $predicado  Condiciones para filtrar los resultados (clave => valor).
     * @param string $campos    Campos que se deben seleccionar, separados por comas (por defecto '*').
     * @param string $orderBy   Cláusula ORDER BY para ordenar los resultados.
     * @param string $limit     Cláusula LIMIT para limitar la cantidad de resultados.
     * 
     * @return PDOStatement|null Retorna un objeto PDOStatement con los resultados de la consulta si tiene éxito,
     *                           o null si ocurre un error.
     * 
     * @throws Exception Si ocurre un error al preparar la consulta.
     */
    public static function SelectOrderLimit(
        Array $predicado    = [],
        string $campos      = '*', 
        string $orderBy     = '', 
        string $limit       = ''
    ) {
        $schema = BD_ACADEMICA;

        if(property_exists(self::class, 'schema') && !empty(static::$schema)) {
            $schema = static::$schema;
        }
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
            $consulta = "SELECT $campos FROM {$schema}.".static::$tableName." {$where} {$orderBy} {$limit}";
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
    /**
     * Valida el tipo de un valor dado y ajusta su formato.
     *
     * - Si el valor es numérico o booleano, lo convierte a su formato numérico correspondiente.
     * - Si el valor no es numérico ni booleano, lo retorna como está.
     *
     * @param mixed $valor El valor a validar y ajustar.
     *
     * @return string Devuelve el valor convertido como cadena.
     */
    public static function formatValor($valor): string  {
            if ( is_numeric($valor) || is_bool($valor)) {
                $result = $valor+0;;
            } else{
                $result = "'".$valor."'";
            }
        return $result;
    }
}