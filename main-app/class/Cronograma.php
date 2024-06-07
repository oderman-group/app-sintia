<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
class Cronograma {
    
    /**
     * Buscar cronograma por el id.
     *
     * @param mysqli    $conexion Objeto de conexión a la base de datos.
     * @param array     $config Configuraciones de la aplicación.
     * @param string    $idCronograma Identificador del cronograma.
     *
     */
    public static function buscarCronograma(
        mysqli $conexion, 
        array $config,
        string $idCronograma
    ){
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_cronograma WHERE cro_id=? AND institucion=? AND year=?";

        $parametros = [$idCronograma, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Obtener la fila de resultados como un array asociativo
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
    
    /**
     * Traer todos los datos de un cronograma.
     *
     * @param mysqli    $conexion Objeto de conexión a la base de datos.
     * @param array     $config Configuraciones de la aplicación.
     * @param string    $idCarga Identificador de la carga académica.
     * @param int       $periodo Identificador del periodo.
     *
     */
    public static function traerDatosCompletosCronograma(
        mysqli $conexion, 
        array $config,
        string $idCarga,
        int $periodo
    ){
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_cronograma 
        WHERE cro_id_carga=? AND cro_periodo=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Traer algunos datos de un cronograma.
     *
     * @param mysqli    $conexion Objeto de conexión a la base de datos.
     * @param array     $config Configuraciones de la aplicación.
     * @param string    $idCarga Identificador de la carga académica.
     * @param int       $periodo Identificador del periodo.
     *
     */
    public static function traerDatosCronograma(
        mysqli $conexion, 
        array $config,
        string $idCarga,
        int $periodo
    ){
        $sql = "SELECT cro_id, cro_tema, cro_fecha, cro_id_carga, cro_recursos, cro_periodo, cro_color, DAY(cro_fecha) as dia, MONTH(cro_fecha) as mes, YEAR(cro_fecha) as agno FROM " . BD_ACADEMICA . ".academico_cronograma 
        WHERE cro_id_carga=? AND cro_periodo=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Guardar cronograma.
     *
     * @param mysqli    $conexion Objeto de conexión a la base de datos.
     * @param array     $config Configuraciones de la aplicación.
     * @param array     $POST Identificador del cronograma.
     * @param string    $idCarga Identificador de la carga.
     * @param int       $periodo Identificador del periodo.
     *
     */
    public static function guardarCronograma(
        mysqli $conexion, 
        PDO $conexionPDO, 
        array $config,
        array $POST,
        string $idCarga,
        int $periodo
    ){
        $date = date('Y-m-d', strtotime(str_replace('-', '/', $POST["fecha"])));
        $idInsercion = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_cronograma');

        $sql = "INSERT INTO " . BD_ACADEMICA . ".academico_cronograma(cro_id, cro_tema, cro_fecha, cro_id_carga, cro_recursos, cro_periodo, cro_color, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $parametros = [$idInsercion, $POST["contenido"], $date, $idCarga, $POST["recursos"], $periodo, $POST["colorFondo"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        return $idInsercion;
    }
    
    /**
     * Actualizar cronograma.
     *
     * @param mysqli    $conexion Objeto de conexión a la base de datos.
     * @param array     $config Configuraciones de la aplicación.
     * @param array     $POST Identificador del cronograma.
     *
     */
    public static function actualizarCronograma(
        mysqli $conexion, 
        array $config,
        array $POST
    ){
        $date = date('Y-m-d', strtotime(str_replace('-', '/', $POST["fecha"])));

        $sql = "UPDATE " . BD_ACADEMICA . ".academico_cronograma SET cro_tema=?, cro_fecha=?, cro_recursos=?, cro_color=? WHERE cro_id=? AND institucion=? AND year=?";

        $parametros = [$POST["contenido"], $date, $POST["recursos"], $POST["colorFondo"], $POST["idR"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Eliminar cronograma.
     *
     * @param mysqli    $conexion Objeto de conexión a la base de datos.
     * @param array     $config Configuraciones de la aplicación.
     * @param string    $idCronograma Identificador del cronograma.
     *
     */
    public static function eliminarCronograma(
        mysqli $conexion, 
        array $config,
        string $idCronograma
    ){
        $sql = "DELETE FROM " . BD_ACADEMICA . ".academico_cronograma WHERE cro_id=? AND institucion=? AND year=?";

        $parametros = [$idCronograma, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
}