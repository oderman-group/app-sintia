<?php
class Servicios
{
    /**
     * Ejecuta una consulta SQL y devuelve un solo resultado.
     *
     * @param string $sql Consulta SQL a ejecutar.
     *
     * @return array|false Arreglo de datos del resultado o false si hay un error.
     */
    public static function getSql($sql)// funcion para obtener un dato especifico de una consulta
    {
        global $conexion;
        try {
            $resulsConsulta = mysqli_query($conexion, $sql." limit 1");
            
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage();
            exit();
        }
        return mysqli_fetch_array($resulsConsulta, MYSQLI_BOTH);
    }

    /**
     * Ejecuta una consulta SQL y devuelve los resultados en un array.
     *
     * @param string $sql Consulta SQL a ejecutar.
     * @param string $limite Límite de resultados a recuperar (opcional).
     * @param bool $esArreglo Indica si los resultados deben ser devueltos como un arreglo (predeterminado) o como un objeto mysqli_result.
     *
     * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
     */
    public static function selectSql($sql,$limite ='LIMIT 20',$esArreglo=true) // funcion para obtener datos en un array de una consulta
    {
        global $conexion;
        try {
            $resulsConsulta = mysqli_query($conexion, $sql.' '.$limite);
            if($resulsConsulta->num_rows>0){
                $index=0;
                if(is_null($esArreglo) || $esArreglo){
                    while($fila=$resulsConsulta->fetch_assoc()){
                        $arraysDatos[$index]=$fila;
                        $index++;
                    } 
                    return $arraysDatos;
                }else{
                    return $resulsConsulta;
                }               
             }
            
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Ejecuta una consulta SQL de inserción y devuelve el ID del último registro insertado.
     *
     * @param string $sql Consulta SQL de inserción.
     *
     * @return int|null ID del último registro insertado o false si hay un error.
     */
    public static function insertSql($sql) { // funcion para insertar en una tabla 
        global $conexion;
        try {
            mysqli_query($conexion, $sql);
            return mysqli_insert_id($conexion);
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage(); 
            return null;
        }        
    }

    /**
     * Ejecuta una consulta SQL de actualización.
     *
     * @param string $sql Consulta SQL de actualización.
     *
     * @return void
     */
    public static function updateSql($sql) // funcion para actualizar Insert en una tabla 
    {
        global $conexion;
        try {
            mysqli_query($conexion, $sql);
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Construye y ejecuta una consulta SQL teniendo en cuenta los parámetros proporcionados.
     *
     * @param array|null $parametrosArray Arreglo de parámetros para construir la consulta.
     *
     * @return array|mysqli_result|false Arreglo de datos del resultado, objeto mysqli_result o false si hay un error.
     */
    public static function buildSelectSql($parametrosArray=null)// funcion para construir y enviar un Sql teniendo en cuenta los parametros
    {
      $sqlInicial=$parametrosArray["sqlInicial"];
      $parametrosValidos=$parametrosArray["parametrosValidos"];
      if($parametrosArray && count($parametrosArray)>0){
        $sqlInicial=Servicios::concatenarWhereAnd($sqlInicial,$parametrosValidos,$parametrosArray);
        $andPersonalizado=$parametrosArray['and'];
      };
      $sqlFinal =$parametrosArray["sqlFinal"];;     
      $sql=$sqlInicial." ".$andPersonalizado." ".$sqlFinal;
      $limite=$parametrosArray["limite"];
      $esArreglo=$parametrosArray["arreglo"];
      return Servicios::selectSql($sql,$limite,$esArreglo);
    }

    /**
     * Concatena los parámetros WHERE o AND en una consulta SQL.
     *
     * @param string $sqlInicial Consulta SQL inicial.
     * @param array $parametrosValidos Arreglo de nombres de parámetros válidos.
     * @param array $parametrosArray Arreglo de parámetros y valores a concatenar en la consulta.
     *
     * @return string Consulta SQL con los parámetros WHERE o AND concatenados.
     */
    public static function concatenarWhereAnd($sqlInicial,$parametrosValidos,$parametrosArray)// funcion para concatenar los parametros WHERE o AND en una Consulta
    {
        $contador=0;//contará cuantos parametros validos existen
        foreach($parametrosArray as $parametro => $valor){
            if (in_array($parametro, $parametrosValidos)) {
                if(!is_null($valor)){
                    $contador++;
                    if(is_numeric($valor)) 
                         $condicion=$parametro." = ".$valor;
                     else
                         $condicion=$parametro." = '".$valor."'";
     
                     if ($contador == 1) {
                         $sqlInicial = $sqlInicial . " WHERE ";
                     } else {
                         $sqlInicial = $sqlInicial . " AND ";
                     }
                     $sqlInicial = $sqlInicial . $condicion;
                 }
            }
        }
        return $sqlInicial." ";
    }
}
