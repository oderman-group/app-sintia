<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class Areas {

    /**
    *  Esta función ejecuta una consulta para obtener los datos de un área específica
    * utilizando el ID del área, la institución y el año proporcionados.
    *
    * @param array  $config    Configuración de la aplicación.
    * @param string $idArea    ID del área que se desea consultar.
    * @param string $yearBd    Año de la base de datos (opcional). Si no se proporciona, se utiliza el año de sesión.
    * @return array|false      Devuelve un array asociativo con los datos del área si se encuentra, o false si no se encuentra.
    **/
    public static function traerDatosArea (
        array   $config,
        string  $idArea,  
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_areas WHERE ar_id=? AND institucion=? AND year=?";

        $parametros = [$idArea, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta para obtener todas las áreas de una institución,
    * excluyendo opcionalmente un área específica si se proporciona su ID.
    *
    * @param array  $config    Configuración de la aplicación.
    * @param string $idArea    (Opcional) ID del área que se desea excluir de los resultados.
    * @param string $yearBd    Año de la base de datos (opcional). Si no se proporciona, se utiliza el año de sesión.
    * @return mysqli_result    Objeto `mysqli_result` que contiene los resultados de la consulta.
    **/
    public static function traerAreasInstitucion (
        array   $config,
        string  $idArea     =   "",  
        string  $yearBd     =   ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $filtroArea= !empty($idArea) ? "AND ar_id NOT IN ('".$idArea."')" : "";

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_areas WHERE institucion=? AND year=? {$filtroArea}  ORDER BY ar_posicion";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta preparada para insertar un nuevo registro de área en la tabla 'academico_areas'.
    *
    * @param PDO    $conexionPDO  Conexión PDO a la base de datos.
    * @param string $insert       Lista de campos separados por coma para la inserción.
    * @param array  $parametros   Array de parámetros para la consulta preparada.
    * @return string              Código único generado para el nuevo registro de área.
    **/
    public static function guardarArea (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_areas');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_areas ({$insert}) VALUES ({$signosPreguntas})";
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
    * Esta función ejecuta una consulta preparada para actualizar un registro de área en la tabla 'academico_areas'.
    *
    * @param array  $config     Configuración del sistema.
    * @param string $idArea     Identificador del área a actualizar.
    * @param string $update     Lista de campos y valores a actualizar en formato de cadena.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function actualizarAreas (
        array   $config,
        string  $idArea,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_areas SET {$updateSql} WHERE ar_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idArea, $config['conf_id_institucion'], $year]);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar un registro de área de la tabla 'academico_areas'
    * utilizando el ID del área, la institución y el año especificados.
    *
    * @param array  $config     Configuración del sistema.
    * @param string $idArea     Identificador del área a eliminar.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function eliminarAreasID (
        array   $config,
        string  $idArea,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_areas WHERE ar_id=? AND institucion=? AND year=?";

        $parametros = [$idArea, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar todos los registros de áreas
    * pertenecientes a una institución para un año específico de la base de datos.
    *
    * @param int    $idInstitucion Identificador de la institución cuyas áreas se eliminarán.
    * @param string $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    * @return void
    **/
    public static function eliminarTodasAreas (
        int     $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_areas WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
}