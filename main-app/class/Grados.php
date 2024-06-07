<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
class Grados {

    /**
     * Lista los grados académicos según el estado y tipo proporcionados.
     *
     * @param int    $estado El estado de los grados a listar (1 activo, 0 inactivo u otro estado según la aplicación).
     * @param string $tipo   El tipo de grado académico a listar (opcional, puede ser null para listar todos los tipos).
     *
     * @return mysqli_result Devuelve Un conjunto de resultados (`mysqli_result`)
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso para listar todos los grados activos
     * $gradosActivos = listarGrados(1);
     * foreach ($gradosActivos as $grado) {
     *     echo "ID: " . $grado['gra_id'] . ", Nombre: " . $grado['gra_nombre'] . "\n";
     * }
     *
     * // Ejemplo de uso para listar grados activos de un tipo específico
     * $tipoGrado = "TIPO_EJEMPLO";
     * $gradosTipoEjemplo = listarGrados(1, $tipoGrado);
     * foreach ($gradosTipoEjemplo as $grado) {
     *     echo "ID: " . $grado['gra_id'] . ", Nombre: " . $grado['gra_nombre'] . "\n";
     * }
     * ```
     */
    public static function listarGrados($estado = 1, $tipo =null){
        
        global $conexion, $arregloModulos, $config;
        
        $resultado = null;
        $filtro="";
        if(!is_null($tipo)){
            $filtro="AND gra_tipo ='".$tipo."'";
        }

        if( !array_key_exists(10,$arregloModulos) ) { 
            $filtro="AND gra_tipo ='".GRADO_GRUPAL."'";
        }

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados
        WHERE gra_estado IN (1, '".$estado."') AND institucion=? AND year=? {$filtro}
        ORDER BY gra_vocal";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta preparada para insertar un nuevo registro de curso en la tabla 'academico_grados'.
    *
    * @param PDO    $conexionPDO  Conexión PDO a la base de datos.
    * @param string $insert       Lista de campos separados por coma para la inserción.
    * @param array  $parametros   Array de parámetros para la consulta preparada.
    * @return string              Código único generado para el nuevo registro de curso.
    **/
    public static function guardarCurso (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_grados');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_grados ({$insert}) VALUES ({$signosPreguntas})";

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
    * Esta función ejecuta una consulta preparada para actualizar un registro de cursos en la tabla 'academico_grados'.
    *
    * @param array  $config     Configuración del sistema.
    * @param string $idCursos   Identificador del curso a actualizar.
    * @param string $update     Lista de campos y valores a actualizar en formato de cadena.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function actualizarCursos (
        array   $config,
        string  $idCursos,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_grados SET {$updateSql} WHERE gra_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idCursos, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para actualizar todos los cursos en la tabla 'academico_grados'.
    *
    * @param array  $config     Configuración del sistema.
    * @param string $update     Lista de campos y valores a actualizar en formato de cadena.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function actualizarTodosCursos (
        array   $config,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_grados SET {$updateSql} WHERE institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta para obtener todos los cursos de una institución,
    *
    * @param array  $config    Configuración de la aplicación.
    * @param string $yearBd    Año de la base de datos (opcional). Si no se proporciona, se utiliza el año de sesión.
    * @return mysqli_result    Objeto `mysqli_result` que contiene los resultados de la consulta.
    **/
    public static function traerGradosInstitucion (
        array   $config,
        string  $tipo       =   "",
        string  $yearBd     =   ""
    )
    {
        $year       = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $filtroTipo = !empty($tipo) ? "gra_tipo='{$tipo}' AND" : "";

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados WHERE gra_estado=1 AND {$filtroTipo} institucion=? AND year=? ORDER BY gra_vocal";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene los datos de un grado académico específico.
     *
     * @param string $grado El identificador único del grado académico a obtener.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`)
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso para obtener los datos de un grado específico
     * $idGrado = "ID_EJEMPLO";
     * $datosGrado = obtenerDatosGrados($idGrado);
     * print_r($datosGrado);
     * ```
     */
    public static function obtenerDatosGrados($grado = '', $yearBD = ''){
        
        global $config;
        
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados WHERE gra_id=? AND institucion=? AND year=?";

        $parametros = [$grado, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    public static function obtenerGrado($grado = '', $yearBD = ''){        
            return mysqli_fetch_array(Grados::obtenerDatosGrados($grado, $yearBD));
    }

    /**
    * Esta función ejecuta una consulta para obtener los datos de una grado por su nombre
    *
    * @param array  $config    Configuración de la aplicación.
    * @param string $nombreGrado
    * @param string $yearBd    Año de la base de datos (opcional). Si no se proporciona, se utiliza el año de sesión.
    **/
    public static function obtenerGradoPorNombre (
        array   $config,
        string  $nombreGrado,
        string  $yearBd     =   ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados WHERE gra_nombre=? AND institucion=? AND year=?";

        $parametros = [$nombreGrado, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta para obtener todos los cursos y grupos de una institución,
    *
    * @param array  $config    Configuración de la aplicación.
    * @param string $idGrado   Identificador del curso.
    * @param string $idGrupo   Identificador del grupo.
    * @param string $yearBd    Año de la base de datos (opcional). Si no se proporciona, se utiliza el año de sesión.
    **/
    public static function traerGradosGrupos (
        array   $config,
        string  $idGrado     =   "",
        string  $idGrupo     =   "",
        string  $yearBd     =   ""
    )
    {
        $year           = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $filtroGrado    = !empty($idGrado) ? "gra.gra_id='{$idGrado}' AND" : "";
        $filtroGrupo    = !empty($idGrupo) ? "gru.gru_id='{$idGrupo}' AND" : "";

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados gra
        JOIN ".BD_ACADEMICA.".academico_grupos gru ON {$filtroGrupo} gru.institucion=gra.institucion AND gru.year=gra.year
        WHERE {$filtroGrado} gra.institucion=? AND gra.year=?";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar todos los registros de categorias de nota
    * pertenecientes a una institución para un año específico de la base de datos.
    *
    * @param int    $idInstitucion Identificador de la institución cuyas categorias de nota se eliminarán.
    * @param string $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    * @return void
    **/
    public static function eliminarGradosInstitucion (
        int     $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_grados WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Obtiene el porcentaje por periodo de un curso.
     *
     * @param mysqli $conexion
     * @param array $config
     * @param string $grado
     * @param int $periodo
     * 
     * @return array $resultado
     *
     */
    public static function traerPorcentajePorPeriodosGrados(
        mysqli $conexion,
        array $config,
        string $grado,
        int $periodo,
    ){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grados_periodos WHERE gvp_grado=? AND gvp_periodo=? AND institucion=? AND year=?";

        $parametros = [$grado, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);
        
        return $resultado;
    }

    /**
     * Me elimina la intensidad de una materia en un curso
     *
     * @param mysqli $conexion
     * @param array $config
     * @param string $curso
     * @param string $materia
     *
     */
    public static function eliminarIntensidadMateriaCurso(
        mysqli $conexion,
        array $config,
        string $curso,
        string $materia
    ){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_intensidad_curso WHERE ipc_curso=? AND ipc_materia=? AND institucion=? AND year=?";

        $parametros = [$curso, $materia, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Me guarda la intensidad de una materia en un curso
     *
     * @param mysqli $conexion
     * @param array $config
     * @param string $curso
     * @param string $materia
     * @param string $ih
     *
     */
    public static function guardarIntensidadMateriaCurso(
        mysqli $conexion,
        PDO     $conexionPDO,
        array $config,
        string $curso,
        string $materia,
        string $ih
    ){
        $codigo=Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_intensidad_curso');

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_intensidad_curso(ipc_id, ipc_curso, ipc_materia, ipc_intensidad, institucion, year)VALUES(?, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $curso, $materia, $ih, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Me trae la intensidad de una materia en un curso
     *
     * @param mysqli $conexion
     * @param array $config
     * @param string $curso
     * @param string $materia
     * 
     * @return array $resultado
     *
     */
    public static function traerIntensidadMateriaCurso(
        mysqli $conexion,
        array $config,
        string $curso,
        string $materia
    ){

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_intensidad_curso WHERE ipc_curso=? AND ipc_materia=? AND institucion=? AND year=?";

        $parametros = [$curso, $materia, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);
        
        return $resultado;
    }

}