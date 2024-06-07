<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class categoriasNota {

    /**
    * Esta función ejecuta una consulta para obtener todas las categorias de nota de una institución,
    *
    * @param array  $config    Configuración de la aplicación.
    * @param string $yearBd    Año de la base de datos (opcional). Si no se proporciona, se utiliza el año de sesión.
    * @return mysqli_result    Objeto `mysqli_result` que contiene los resultados de la consulta.
    **/
    public static function traerCategoriasNotasInstitucion (
        array   $config,
        string  $yearBd     =   ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_categorias_notas WHERE institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta preparada para insertar un nuevo registro de categoria de nota en la tabla 'academico_categorias_notas'.
    *
    * @param PDO    $conexionPDO  Conexión PDO a la base de datos.
    * @param string $insert       Lista de campos separados por coma para la inserción.
    * @param array  $parametros   Array de parámetros para la consulta preparada.
    * @return string              Código único generado para el nuevo registro de área.
    **/
    public static function guardarCategoriaNota (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_categorias_notas');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_categorias_notas ({$insert}) VALUES ({$signosPreguntas})";

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar un registro de categoria de nota de la tabla 'academico_categorias_notas'
    * utilizando el ID del categoria de nota, la institución y el año especificados.
    *
    * @param array  $config     Configuración del sistema.
    * @param string $idCategoriaNota     Identificador del categoria de nota a eliminar.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function eliminarCategoriaNotaID (
        array   $config,
        string  $idCategoriaNota,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_categorias_notas WHERE catn_id=? AND institucion=? AND year=?";

        $parametros = [$idCategoriaNota, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar todos los registros de categorias de nota
    * pertenecientes a una institución para un año específico de la base de datos.
    *
    * @param int    $idInstitucion Identificador de la institución cuyas categorias de nota se eliminarán.
    * @param string $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    * @return void
    **/
    public static function eliminarTodasCategoriasNotas (
        int     $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_categorias_notas WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
}