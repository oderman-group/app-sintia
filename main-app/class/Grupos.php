<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
class Grupos {

    /**
     * Obtiene los datos de un grupo académico específico.
     * 
     * @param string $grupo - El identificador único del grupo académico a obtener.
     *
     * @return mysqli_result - Devuelve un conjunto de resultados (fila) obtenido de la base de datos con la información del grupo académico correspondiente al identificador proporcionado.
     *
     * @throws Exception - Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso para obtener los datos de un grupo específico
     * $idGrupo = "ID_EJEMPLO";
     * $datosGrupo = obtenerDatosGrupos($idGrupo);
     * print_r($datosGrupo);
     * ```
     */
    public static function obtenerDatosGrupos($grupo = ''){
        global $config;

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grupos WHERE gru_id=? AND institucion=? AND year=?";

        $parametros = [$grupo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene un grupo académico específico a partir de su identificador único.
     *
     * @param int $grupo - El identificador único del grupo académico a obtener.
     *
     * @return array - Devuelve un array asociativo con la información del grupo académico correspondiente al identificador proporcionado.
     *
     * @throws Exception - Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso para obtener un grupo académico por su identificador
     * $idGrupo = 123; // Reemplazar con el identificador real del grupo
     * $grupoObtenido = obtenerGrupo($idGrupo);
     * print_r($grupoObtenido);
     * ```
     */
    public static function obtenerGrupo($grupo = ''){
            $datos=Grupos::obtenerDatosGrupos($grupo);
            $resultado = mysqli_fetch_array($datos);

        return $resultado;
    }

    /**
     * Lista todos los grupos académicos existentes en la institución.
     *
     * @return mysqli_result - Un conjunto de resultados (`mysqli_result`)
     *
     * @throws Exception - Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso para listar todos los grupos académicos
     * $grupos = listarGrupos();
     * print_r($grupos);
     * ```
     */
    public static function listarGrupos(){
        global $config;

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_grupos WHERE institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me guarda un grupo
     * @param mysqli $conexion
     * @param PDO $conexionPDO
     * @param array $config
     * @param array $POST
     */
    public static function guardarGrupos(mysqli $conexion, PDO $conexionPDO, array $config, array $POST){
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_grupos');
        
        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_grupos (gru_id, gru_codigo, gru_nombre, institucion, year) VALUES (?, ?, ?, ?, ?)";

        $parametros = [$codigo, $POST["codigoG"], $POST["nombreG"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }
    
    /**
     * Este metodo me actualiza un grupo
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     */
    public static function actualizarGrupos(mysqli $conexion, array $config, array $POST){
        $sql = "UPDATE ".BD_ACADEMICA.".academico_grupos SET gru_codigo=?, gru_nombre=? WHERE gru_id=? AND institucion=? AND year=?";

        $parametros = [$POST['codigoG'], $POST['nombreG'], $POST["id"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina todos los grupos de una institucion
     * @param string $idInstitucion
     * @param string $yearBd
    **/
    public static function eliminarTodosGrupos (
        string  $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_grupos WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

}