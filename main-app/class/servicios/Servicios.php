<?php
class Servicios
{
    public static function getSql($sql)// funcion para obtener un dato especifico de una consulta
    {
        global $conexion;
        try {
            $resulsConsulta = mysqli_query($conexion, $sql." limit 1");
            
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage();
            throw $e; 
        }
        return mysqli_fetch_array($resulsConsulta, MYSQLI_BOTH);
    }

    public static function selectSql($sql,$limite ='LIMIT 20') // funcion para obtener datos en un array de una consulta
    {
        global $conexion;
        try {
            $resulsConsulta = mysqli_query($conexion, $sql.' '.$limite);
            if($resulsConsulta->num_rows>0){
                $index=0;
                while($fila=$resulsConsulta->fetch_assoc()){
                      $arraysDatos[$index]=$fila;
                      $index++;
                }                
             }
            
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage();
            throw $e;  
        }
        return $arraysDatos;
    }

    public static function insertSql($sql,$transacion=false) { // funcion para insertar en una tabla 
        
            global $conexion;
            try {
                mysqli_query($conexion, $sql);
                return mysqli_insert_id($conexion);
            } catch (Exception $e) {                
                if($transacion){                   
                    Servicios::revertirTransacion();
                 }
                echo "Excepción catpurada: " . $e->getMessage();
                throw $e;                 
            }
             
    }

    public static function updateSql($sql,$transacion=false) // funcion para actualizar Insert en una tabla 
    {
        if(!$transacion){
            global $conexion;
            try {
                mysqli_query($conexion, $sql);
            } catch (Exception $e) {
                if($transacion){                   
                    Servicios::revertirTransacion();
                 }
                echo "Excepción catpurada: " . $e->getMessage();
                throw $e;  
            }
        }
    }

    public static function concatenarWhereAnd($sqlInicial,$parametrosValidos,$parametrosArray)// funcion para concatenar los parametros WHERE o AND en una Consulta
    {
        $contador=0;//contará cuantos parametros validos existen
        foreach($parametrosValidos as $clave => $parametro){
            $valor=$parametrosArray[$parametro];
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
        return $sqlInicial;
    }

    public static function iniciarTransacion()// funcion para realizar transaciones multiples
    {
        global $conexion;
        mysqli_query($conexion, "START TRANSACTION");
    }

    public static function finalizarTransacion()// funcion para realizar transaciones multiples
    {
        global $conexion;
        mysqli_query($conexion, "COMMIT");
    }

    public static function revertirTransacion()// funcion para realizar transaciones multiples
    {
        global $conexion;
        mysqli_query($conexion, "ROLLBACK");
    }
    public static function transacion($arreglosSQL)// funcion para realizar transaciones multiples
    {
            global $conexion;
            mysqli_query($conexion, "START TRANSACTION");
            foreach($arreglosSQL as $sqlQuery ){                
                try {
                    mysqli_query($conexion, $sqlQuery);
                } catch (Exception $e) {
                    mysqli_query($conexion, "ROLLBACK");                    
                    echo "Excepción catpurada: " . $e->getMessage();
                    throw $e;  
                }                
            }
            return  mysqli_query($conexion, "COMMIT");   
             
            }
}
