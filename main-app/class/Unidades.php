<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
class Unidades{
    /**
     * Este metodo me trae las unidades de una carga
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCarga
     * @param int $periodo
     * 
     * @return mysqli_result|false $consulta
     */
    public static function consultarUnidades(mysqli $conexion, array $config, string $idCarga, int $periodo)
    {
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_unidades WHERE uni_id_carga=? AND uni_periodo=? AND uni_eliminado!=1 AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Este metodo me trae las unidades de una carga exceptando la unidad actual
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCarga
     * @param int $periodo
     * @param int $idR
     * 
     * @return mysqli_result|false $consulta
     */
    public static function consultarUnidadesDiferentes(mysqli $conexion, array $config, string $idCarga, int $periodo, int $idR)
    {
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_unidades WHERE uni_id_carga=? AND uni_periodo=? AND uni_eliminado!=1 AND id_nuevo!=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $idR, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae los datos de una unidad por su ID
     * @param mysqli $conexion
     * @param int $idR
     * 
     * @return array $resultado
     */
    public static function consultarUnidadesPorID(mysqli $conexion, int $idR)
    {
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_unidades WHERE id_nuevo=?";

        $parametros = [$idR];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Obtener la fila de resultados como un array asociativo
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo guarda una unidad
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCarga
     * @param int $periodo
     * @param array $POST
     */
    public static function guardarUnidades(mysqli $conexion, PDO $conexionPDO, array $config, string $idCarga, int $periodo, array $POST)
    {
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_unidades');
        
        $sql = "INSERT INTO " . BD_ACADEMICA . ".academico_unidades (uni_id, uni_nombre, uni_id_carga, uni_periodo, uni_descripcion, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $POST["nombre"], $idCarga, $periodo, $POST["contenido"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo actualiza una unidad
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCarga
     * @param int $pertiodo
     * @param array $POST
     */
    public static function actualizarUnidades(mysqli $conexion, array $config, string $idCarga, int $pertiodo, array $POST)
    {
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_unidades SET uni_nombre=?, uni_id_carga=?, uni_periodo=?, uni_descripcion=? WHERE id_nuevo=? AND institucion=? AND year=?";

        $parametros = [$POST["nombre"], $idCarga, $pertiodo, $POST["contenido"], $POST["idR"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo elimina una unidad
     * @param mysqli $conexion
     * @param array $config
     * @param array $GET
     */
    public static function eliminarUnidades(mysqli $conexion, array $config, array $GET)
    {
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_unidades SET uni_eliminado=1 WHERE id_nuevo=? AND institucion=? AND year=?";

        $parametros = [base64_decode($GET["idR"]), $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
}