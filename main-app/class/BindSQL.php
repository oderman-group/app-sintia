<?php

use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");

class BindSQL
{
    /**
     * Prepara y ejecuta una consulta SQL con parámetros.
     *
     * @param string $sql La consulta SQL con marcadores de posición.
     * @param array $parametros Un array de valores para reemplazar los marcadores de posición en la consulta.
     * @return mixed|false El resultado de la consulta o false en caso de error.
     */
    public static function prepararSQL(
        string $sql,
        array $parametros,
        $finalizartransacion = true
    ) {
        global $conexion;
        self::iniciarTransacion();
        try {
            $consulta = mysqli_prepare($conexion, $sql);

            if ($consulta) {
                $tipoParametro = self::validartipoValor($parametros);                
                // aplanamos los array por si hay un elemento tipo aarray (unificamos los parametros con los array)
                $arrayParametrosPreparadosUnificados = [];
                array_walk_recursive($parametros, function ($item) use (&$arrayParametrosPreparadosUnificados) {
                    $arrayParametrosPreparadosUnificados[] = $item;
                });
                // Aplicar trim a cada valor en $parametros para eliminar comillas innecesarias
                $arrayParametrosPreparadosUnificados = array_map(function ($value) {

                    if ($value != null) {
                        if (is_string($value)) {
                            return trim($value, "'");
                        }else{
                            return $value;
                        }
                    } else {
                        return null;
                    }
                }, $arrayParametrosPreparadosUnificados);

                mysqli_stmt_bind_param($consulta, $tipoParametro, ...$arrayParametrosPreparadosUnificados);

                mysqli_stmt_execute($consulta);

                $resultado = mysqli_stmt_get_result($consulta);

                if ($finalizartransacion) {
                    self::finalizarTransacion();
                }

                return $resultado;
            } else {
                self::revertirTransacion();
                echo "Error en la preparación de la consulta.";
                exit();
            }
        } catch (Exception $e) {
            self::revertirTransacion();
            Utilidades::writeLog('Se jodió esta vaina porque '.$e->getMessage());
            include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
        }
    }

    private static function validartipoValor(array $valoresArray){
        $tipoParametro = '';
        foreach ($valoresArray as $valor) {
            if (is_int($valor)) {
                $tipoParametro .= 'i';
            } elseif (is_float($valor)) {
                $tipoParametro .= 'd';
            } elseif (is_string($valor)) {
                $tipoParametro .= 's';
            } elseif (is_array($valor)) { 
               $tipoParametro .= self::validartipoValor($valor);
            } elseif (is_bool($valor)) {
                $tipoParametro .= 'i';
                $valor = $valor ? 1 : 0;
            } else {
                $tipoParametro .= 's';
            }
        }
        return $tipoParametro;
    }

    // funcion para Iniciar la transacio
    public static function iniciarTransacion() // funcion para realizar transaciones multiples
    {
        global $conexion;
        mysqli_query($conexion, "START TRANSACTION");
    }

    // funcion para finalizar la transacion
    public static function finalizarTransacion()
    {
        global $conexion;
        mysqli_query($conexion, "COMMIT");
    }

    // funcion para revertir la transacion
    public static function revertirTransacion()
    {
        global $conexion;
        mysqli_query($conexion, "ROLLBACK");
    }

    /**
     * Prepara la parte de la actualización de una consulta SQL a partir de un array asociativo.
     *
     * Esta función toma un array asociativo donde las claves son los nombres de columnas y los valores son los valores a actualizar.
     * La función devuelve un array con dos elementos:
     * 1. Una cadena con las partes de la actualización preparadas para ser utilizadas en una consulta SQL, separadas por comas.
     * 2. Un array con los valores correspondientes a las partes preparadas.
     *
     * @param array $update Un array asociativo con los nombres de columnas y los valores a actualizar.
     * @return array Un array con dos elementos: la cadena de actualización preparada y el array de valores.
     */
    public static function prepararUpdateConArray(array $update)
    {

        // Array para almacenar las partes preparadas
        $preparedParts = [];
        // Array para almacenar los valores
        $values = [];

        // Iterar sobre cada parte
        foreach ($update as $key => $value) {
            // Añadir la parte preparada al array
            $preparedParts[] = "{$key}=?";
            $values[] = $value;
        }

        // Unir las partes preparadas con comas y retornar
        return [implode(",", $preparedParts), $values];
    }
    /**
     * resive un resultado de una consulta la devuelebe en un array.
     *
     * @param mysqli_result $resulsConsulta resultado de una consulta SQL ya ejecutada.
     *
     * @return array Arreglo de datos del resultado.
     */
    public static function resultadoArray(mysqli_result $resulsConsulta) // funcion para obtener datos en un array de una consulta
    {

        if ($resulsConsulta) {
            return $resulsConsulta->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}
