<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";

class Indicadores {

    public const CONFIG_MANUAL_INDICADOR      = 1;
    public const CONFIG_AUTOMATICO_INDICADOR  = 0;

    /**
     * Este metodo me consulta la suma de los indicadores de la carga actual
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idcarga
     * @param int       $periodo
     * 
     * @return array    $resultado
    **/
    public static function consultarSumaIndicadores (
        mysqli  $conexion, 
        array   $config, 
        string  $idcarga, 
        int     $periodo
    )
    {
        $sql = "SELECT
        (SELECT sum(ipc_valor) FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga=? AND ipc_periodo=? AND ipc_creado=0 AND institucion=? AND year=?),
        (SELECT sum(ipc_valor) FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga=? AND ipc_periodo=? AND ipc_creado=1 AND institucion=? AND year=?),
        (SELECT count(*) FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga=? AND ipc_periodo=? AND ipc_creado=1 AND institucion=? AND year=?)";

        $parametros = [$idcarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"], $idcarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"], $idcarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo guarda la relación de un indicador con su carga
     * @param mysqli    $conexion
     * @param PDO       $conexionPDO
     * @param array     $config
     * @param string    $idcarga
     * @param string    $idIndicador
     * @param int       $periodo
     * @param array     $POST
     * @param array     $indicadorCopiado
     * @param float     $creado
     * @param string    $valor
    **/
    public static function guardarIndicadorCarga (
        mysqli  $conexion, 
        PDO     $conexionPDO, 
        array   $config, 
        string  $idcarga, 
        string  $idIndicador,  
        int     $periodo,  
        array   $POST               = NULL,  
        array   $indicadorCopiado   = NULL, 
        float   $creado, 
        string  $valor = ""
    )
    {
        // Asegurar que ipc_creado tenga un valor válido (0 o 1, no NULL)
        $creado = ($creado === null || $creado === '') ? 0 : (int)$creado;
        
        $codigo             = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores_carga');
        $copiado            = NULL;
        $evaluacion         = NULL;
        $valorIndicador     = NULL;

        if($POST != NULL){
            $evaluacion     = !empty($POST["saberes"]) ? $POST["saberes"] : NULL;
            $valorIndicador = !empty($valor) ? $valor : (!empty($POST["valor"]) ? $POST["valor"] : NULL);
        } else {
            // Si no hay POST, usar el valor pasado como parámetro
            $valorIndicador = !empty($valor) ? $valor : NULL;
        }
        
        if($indicadorCopiado != NULL){
            $evaluacion         = !empty($indicadorCopiado['ipc_evaluacion']) ? $indicadorCopiado['ipc_evaluacion'] : NULL;
            $copiado            = !empty($indicadorCopiado['ind_id']) ? $indicadorCopiado['ind_id'] : NULL;
            $valorIndicador     = !empty($indicadorCopiado['ipc_valor']) ? $indicadorCopiado['ipc_valor'] : NULL;
        }

        // Usar PDO directamente en lugar de BindSQL
        try {
            $conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_carga(ipc_id, ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, ipc_evaluacion, institucion, year) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conexionPDO->prepare($sql);
            
            // Bind de parámetros con tipos correctos
            $stmt->bindParam(1, $codigo, PDO::PARAM_STR);
            $stmt->bindParam(2, $idcarga, PDO::PARAM_STR);
            $stmt->bindParam(3, $idIndicador, PDO::PARAM_STR);
            $stmt->bindValue(4, $valorIndicador, is_null($valorIndicador) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(5, $periodo, PDO::PARAM_INT);
            $stmt->bindParam(6, $creado, PDO::PARAM_INT);
            $stmt->bindValue(7, $copiado, is_null($copiado) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(8, $evaluacion, is_null($evaluacion) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(9, $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(10, $_SESSION["bd"], PDO::PARAM_INT);
            
            $stmt->execute();
        } catch (PDOException $e) {
            Utilidades::writeLog('LOG desde el método guardarIndicadorCarga: '.$e->getMessage());
            include(ROOT_PATH . "/main-app/compartido/error-catch-to-report.php");
            throw $e;
        }
    }

    /**
     * Este metodo me re-calcula los valores de los indicadores
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idcarga
     * @param int       $periodo
     * @param float     $valorIgualIndicador
    **/
    public static function actualizarValorIndicadores (
        mysqli  $conexion, 
        array   $config, 
        string  $idcarga, 
        int     $periodo, 
        float   $valorIndicadores
    )
    {
        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_carga SET ipc_valor=? WHERE ipc_carga=? AND ipc_periodo=? AND ipc_creado=1 AND institucion=? AND year=?";

        $parametros = [$valorIndicadores, $idcarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me trae la relación de una indicador con una carga
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idcarga
     * @param string    $idIndicador
     * 
     * @return array    $resultado
    **/
    public static function traerRelacionCargaIndicador (
        mysqli  $conexion, 
        array   $config, 
        string  $idCarga, 
        string  $idIndicador
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga=? AND ipc_indicador=? AND ipc_creado=0 AND institucion=? AND year=?";

        $parametros = [$idCarga, $idIndicador, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me elimina la relación entre indicador y una carga
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idcarga
     * @param string    $idIndicador
    **/
    public static function eliminarRelacionCargaIndicador (
        mysqli  $conexion, 
        array   $config, 
        string  $idCarga, 
        string  $idIndicador
    )
    {
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga=? AND ipc_indicador=? AND ipc_creado=0 AND institucion=? AND year=?";

        $parametros = [$idCarga, $idIndicador, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me trae los indicador de un periodo de una carga
     * @param mysqli            $conexion
     * @param array             $config
     * @param string            $idcarga
     * @param int               $periodo
     * @param string            $year
     * 
     * @return mysqli_result    $consulta
    **/
    public static function traerCargaIndicadorPorPeriodo (
        mysqli  $conexion, 
        array   $config, 
        string  $idCarga, 
        int     $periodo, 
        string  $year = ""
    )
    {
        $yearConsulta = $_SESSION['bd'];
        if (!empty($year)) {
            $yearConsulta = $year;
        }
        $sql = "SELECT 
            aipc.id_nuevo AS aipc_id_nuevo, 
            aipc.ipc_id, 
            aipc.ipc_carga, 
            aipc.ipc_indicador, 
            aipc.ipc_valor, 
            aipc.ipc_periodo, 
            aipc.ipc_creado, 
            aipc.institucion, 
            aipc.year, 
            ai.id_nuevo, 
            ai.ind_nombre, 
            ai.ind_tematica, 
            ai.ind_definitivo, 
            ai.ind_id AS ai_ind_id
        FROM ".BD_ACADEMICA.".academico_indicadores_carga aipc
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai 
            ON ai.ind_id=aipc.ipc_indicador 
            AND ai.institucion=aipc.institucion 
            AND ai.year=aipc.year
        WHERE 
            aipc.ipc_carga=? 
        AND aipc.ipc_periodo=? 
        AND aipc.institucion=? 
        AND aipc.year=?
        ";

        $parametros = [$idCarga, $periodo, $config['conf_id_institucion'], $yearConsulta];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae los datos de un indicador
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idIndicador
     * 
     * @return array    $resultado
    **/
    public static function traerDatosIndicador (
        mysqli  $conexion, 
        array   $config,  
        string  $idIndicador
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga aic
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=aic.ipc_indicador AND ai.institucion=aic.institucion AND ai.year=aic.year
        WHERE aic.ipc_id=? AND aic.institucion=? AND aic.year=?";

        $parametros = [$idIndicador, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me elimina indicador
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idIndicador
    **/
    public static function eliminarIndicador (
        mysqli  $conexion, 
        array   $config, 
        string  $idIndicador
    )
    {
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_id=? AND institucion=? AND year=?";

        $parametros = [$idIndicador, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me trae los indicador generados
     * @param mysqli            $conexion
     * @param array             $config
     * 
     * @return mysqli_result    $consulta
    **/
    public static function consultarIndicadorGenerados (
        mysqli  $conexion, 
        array   $config
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_creado=0 AND institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae todos los indicadorde una carga
     * @param mysqli            $conexion
     * @param array             $config
     * @param string            $idCarga
     * @param string            $filtro
     * 
     * @return mysqli_result    $consulta
    **/
    public static function consultarIndicador (
        mysqli  $conexion, 
        array   $config,
        string  $idCarga,
        string  $filtro = "",
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga ipc
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=ipc.ipc_indicador AND ai.institucion=ipc.institucion AND ai.year=ipc.year
        WHERE ipc.ipc_carga=? AND ipc.institucion=? AND ipc.year=? {$filtro}
        ORDER BY ipc.ipc_periodo";

        $parametros = [$idCarga, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me consulta indicador en un periodo
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idIndicador
     * @param string    $idCarga
     * @param int       $periodo
     * 
     * @return array    $resultado
    **/
    public static function consultaIndicadorPeriodo (
        mysqli  $conexion, 
        array   $config,  
        string  $idIndicador,  
        string  $idCarga,  
        int     $periodo
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_indicador=? AND ipc_carga=? AND ipc_periodo=? AND institucion=? AND year=?";

        $parametros = [$idIndicador, $idCarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me elimina los indicadores de una carga en un periodo
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idcarga
     * @param int       $periodo
    **/
    public static function eliminarCargaIndicadorPeriodo (
        mysqli  $conexion, 
        array   $config, 
        string  $idCarga, 
        int     $periodo
    )
    {
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga=? AND ipc_periodo=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo guarda la relación de un indicador con su carga
     * @param mysqli    $conexion
     * @param string    $idcarga
    **/
    public static function guardarIndicadorCargaMaxivo (
        mysqli  $conexion,
        string  $datosInsert
    )
    {
		try{
			mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_carga(ipc_id, ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, institucion, year) VALUES $datosInsert");
		} catch (Exception $e) {
			include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
		}
    }
    
    /**
     * Me trae los datos de un indicador.
     *
     * @param string $idIndicador
     *
     */
    public static function traerIndicadoresDatos(
        string $idIndicador
    ){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_id=? AND institucion=? AND year=?";

        $parametros = [$idIndicador, $_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
    
    /**
     * Me trae los datos de un indicador y su relacion.
     *
     * @param string $idIndicador
     *
     */
    public static function traerIndicadoresDatosRelacion(
        string $idIndicador
    ){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores ai 
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga ipc ON ipc.ipc_indicador=ai.ind_id AND ipc.institucion=? AND ipc.year=?
        WHERE ai.ind_id=? AND ai.institucion=? AND ai.year=?";

        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"], $idIndicador, $_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
    
    /**
     * Me trae los indicadores de una carga en un periodo.
     *
     * @param string $idCarga
     * @param int    $periodo
     *
     */
    public static function traerIndicadoresCargaPeriodo(
        string $idCarga,
        int    $periodo
    ){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores ai, ".BD_ACADEMICA.".academico_indicadores_carga aic WHERE ai.ind_id=aic.ipc_indicador AND aic.ipc_periodo=? AND aic.ipc_carga=? AND aic.institucion=? AND aic.year=? AND ai.institucion=? AND ai.year=?";

        $parametros = [$periodo, $idCarga, $_SESSION["idInstitucion"], $_SESSION["bd"], $_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Me trae los datos de un indicadores y su relación.
     *
     * @param string $idIndicador
     *
     */
    public static function traerDatosIndicadorRelacion(
        string $idIndicador
    ){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores ai
		INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga ipc ON ipc.ipc_indicador=ai.ind_id AND ipc.institucion=? AND ipc.year=?
		WHERE ai.ind_id=? AND ai.institucion=? AND ai.year=?";

        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"], $idIndicador, $_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
    
    /**
     * Me trae la tematica de una carga
     *
     * @param string $idCarga
     * @param int    $periodo
     *
     */
    public static function consultaTematica(
        string $idCarga,
        int    $periodo
    ){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_carga=? AND ind_periodo=? AND ind_tematica=1 AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me actualiza la informacion de un usuario
    **/
    public static function actualizarIndicador (
        array   $config,
        string  $idIndicador,
        array   $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdateConArray($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores SET {$updateSql}, ind_fecha_modificacion=now() WHERE ind_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idIndicador, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me actualiza la informacion de un usuario
    **/
    public static function actualizarIndicadorCargaPeriodo (
        array   $config,
        string  $idCarga,
        int     $periodo,
        array   $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdateConArray($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores SET {$updateSql}, ind_fecha_modificacion=now() WHERE ind_periodo=? AND ind_carga=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$periodo, $idCarga, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me guarda la informacion de un indicador
    **/
    public static function guardarIndicador (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_indicadores({$insert}) VALUES ({$signosPreguntas})";

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }
    
    /**
     * Me trae los indicadores obligatorios.
     */
    public static function consultarIndicadoresObligatorios()
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_obligatorio=1 AND institucion=? AND year=?";

        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Me trae el valor de los indicadores obligatorios.
     *
     * @param string $idIndicador
     *
     */
    public static function consultarValorIndicadoresObligatorios(
        string $idIndicador = ""
    ){
        $filtro = !empty($idIndicador) ? "AND ind_id!='".$idIndicador."'" : "";
        $sql = "SELECT sum(ind_valor) FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_obligatorio=1 AND institucion=? AND year=? {$filtro}";

        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
    
    /**
     * Me trae los datos de un indicador.
     *
     * @param string $idIndicador
     *
     */
    public static function eliminarIndicadores(
        string $idIndicador
    ){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_id=? AND institucion=? AND year=?";

        $parametros = [$idIndicador, $_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Me trae el valor de los indicadores obligatorios.
     *
     * @param string $idIndicador
     *
     */
    public static function consultarIndicadoresDefinitivos()
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores WHERE ind_definitivo=1 AND institucion=? AND year=?";

        $parametros = [$_SESSION["idInstitucion"], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me trae la recuperacion del indicador de una carga en un periodo para un estudiante
     * @param array             $config
     * @param string            $idIndicador
     * @param string            $estudiante
     * @param string            $idCarga
     * @param int               $periodo
     * @param string            $yearBd
     * 
     * @return mysqli_result    $resultado
    **/
    public static function consultaRecuperacionIndicadorPeriodo ( 
        array   $config,  
        string  $idIndicador,  
        string  $estudiante,  
        string  $idCarga,  
        int     $periodo,  
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "
        SELECT * 
        FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion 
        WHERE 
            rind_carga=? 
        AND rind_estudiante=? 
        AND rind_periodo=? 
        AND rind_indicador=? 
        AND institucion=? 
        AND year=?
        ";

        $parametros = [$idCarga, $estudiante, $periodo, $idIndicador, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae todas las recuperaciones de un indicador para una carga y periodo,
     * organizadas en un mapa [idEstudiante] => fila.
     *
     * Se usa para optimizar pantallas donde se muestran/usan recuperaciones
     * de muchos estudiantes a la vez (evitar una consulta por estudiante).
     */
    public static function traerRecuperacionesIndicadorCargaPeriodoMapa ( 
        array   $config,  
        string  $idIndicador,  
        string  $idCarga,  
        int     $periodo,  
        string  $yearBd = ""
    ): array
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * 
                FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion 
                WHERE rind_carga = ? 
                  AND rind_periodo = ? 
                  AND rind_indicador = ? 
                  AND institucion = ? 
                  AND year = ?";

        $parametros = [$idCarga, $periodo, $idIndicador, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $mapa = [];
        while ($fila = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
            $mapa[$fila['rind_estudiante']] = $fila;
        }

        return $mapa;
    }

    /**
     * Este metodo me trae la recuperacion del indicador de una carga en un periodo para un estudiante
     * @param array             $config
     * @param string            $estudiante
     * @param string            $idCarga
     * @param string            $yearBd
     * 
     * @return mysqli_result    $resultado
    **/
    public static function traerDatosIndicadorPerdidos ( 
        array   $config,  
        string  $estudiante,  
        string  $idCarga,  
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion rind
		INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=rind.rind_indicador AND ai.institucion=? AND ai.year=?
		WHERE rind.rind_carga=? AND rind.rind_estudiante=? AND rind.rind_nota>rind.rind_nota_original AND rind.institucion=? AND rind.year=?";

        $parametros = [$config['conf_id_institucion'], $year, $idCarga, $estudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me elimina la recuperacion del indicador de una carga en un periodo para un estudiante
     * @param array             $config
     * @param string            $idIndicador
     * @param string            $estudiante
     * @param string            $idCarga
     * @param int               $periodo
     * @param string            $yearBd
    **/
    public static function eliminarRecuperacionIndicadorPeriodo ( 
        array   $config,  
        string  $idIndicador,  
        string  $estudiante,  
        string  $idCarga,  
        int     $periodo,  
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_indicador=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $estudiante, $periodo, $idIndicador, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me guarda la recuperación de un indicador
     * @param PDO       $conexionPDO
     * @param array     $config
    **/
    public static function guardarRecuperacionIndicador (
        PDO     $conexionPDO, 
        array   $config,
        string  $estudiante,  
        string  $carga,
        string  $nota,
        string  $idIndicador,
        int     $periodo,
        string  $valor,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores_recuperacion');

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_recuperacion(rind_id, rind_fecha_registro, rind_estudiante, rind_carga, rind_nota, rind_indicador, rind_periodo, rind_actualizaciones, rind_nota_original, rind_nota_actual, rind_valor_indicador_registro, institucion, year)VALUES(?, now(), ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $estudiante, $carga, $nota, $idIndicador, $periodo, $nota, $nota, $valor, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me actualiza la recuperación de un indicador
     * @param array     $config
    **/
    public static function actualizarRecuperacionIndicador (
        array   $config,
        string  $estudiante,  
        string  $carga,
        string  $nota,
        string  $idIndicador,
        int     $periodo,
        string  $valor,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_recuperacion SET rind_nota_anterior=rind_nota, rind_nota=?, rind_actualizaciones=rind_actualizaciones+1, rind_ultima_actualizacion=now(), rind_nota_actual=?, rind_tipo_ultima_actualizacion=1, rind_valor_indicador_actualizacion=? WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_indicador=? AND institucion=? AND year=?";

        $parametros = [$nota, $nota, $valor, $carga, $estudiante, $periodo, $idIndicador, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me guarda la relacion de un indicador y una carga
    **/
    public static function guardarRelacionIndicadorCarga (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores_carga');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_carga({$insert}) VALUES ({$signosPreguntas})";

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
     * Este metodo me actualiza la relación de una carga y un periodo
    **/
    public static function actualizarRelacionIndicadorCargas (
        array   $config,
        string  $idIndicador,
        array   $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdateConArray($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_carga SET {$updateSql} WHERE ipc_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idIndicador, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Valida si el docente tiene permiso para crear/editar/eliminar indicadores en una carga
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idCarga
     * @param string    $idIndicador (opcional, para validar si es indicador del directivo)
     * 
     * @return array    ['permiso' => bool, 'mensaje' => string]
    **/
    public static function validarPermisoEditarIndicador (
        mysqli  $conexion, 
        array   $config, 
        string  $idCarga,
        string  $idIndicador = ""
    )
    {
        // Consultar datos de la carga
        $sql = "SELECT car_indicadores_directivo FROM ".BD_ACADEMICA.".academico_cargas WHERE car_id=? AND institucion=? AND year=?";
        $parametros = [$idCarga, $config['conf_id_institucion'], $_SESSION["bd"]];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $datosCarga = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        // Si la carga no existe
        if (empty($datosCarga)) {
            return [
                'permiso' => false,
                'mensaje' => 'La carga académica no existe.'
            ];
        }

        // Si car_indicadores_directivo = 1, el docente NO puede crear/editar/eliminar indicadores
        if (!empty($datosCarga['car_indicadores_directivo']) && $datosCarga['car_indicadores_directivo'] == 1) {
            // Si se está intentando editar/eliminar un indicador específico, verificar si es del directivo
            if (!empty($idIndicador)) {
                $sqlIndicador = "SELECT ipc_creado FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_id=? AND ipc_carga=? AND institucion=? AND year=?";
                $parametrosIndicador = [$idIndicador, $idCarga, $config['conf_id_institucion'], $_SESSION["bd"]];
                $resultadoIndicador = BindSQL::prepararSQL($sqlIndicador, $parametrosIndicador);
                $datosIndicador = mysqli_fetch_array($resultadoIndicador, MYSQLI_BOTH);

                // Si el indicador fue creado por el directivo (ipc_creado=0), el docente no puede editarlo
                if (!empty($datosIndicador) && $datosIndicador['ipc_creado'] == 0) {
                    return [
                        'permiso' => false,
                        'mensaje' => 'No tiene permiso para modificar este indicador. Solo el directivo puede gestionar los indicadores en esta carga.'
                    ];
                }
            } else {
                // Si está intentando crear un nuevo indicador
                return [
                    'permiso' => false,
                    'mensaje' => 'No tiene permiso para crear indicadores en esta carga. Solo el directivo puede definir los indicadores.'
                ];
            }
        }

        return [
            'permiso' => true,
            'mensaje' => ''
        ];
    }

    /**
     * Verifica si un indicador está en uso (tiene actividades registradas)
     * @param array     $config
     * @param string    $idIndicador - ID del indicador en academico_indicadores
     * @param string    $idCarga (opcional) - Si se especifica, verifica solo en esa carga
     * @param int       $periodo (opcional) - Si se especifica, verifica solo en ese período
     * 
     * @return array    ['enUso' => bool, 'mensaje' => string, 'cargas' => array]
    **/
    public static function verificarIndicadorEnUso (
        array   $config,
        string  $idIndicador,
        string  $idCarga = null,
        int     $periodo = null,
        string  $yearBd = ""
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        require_once(ROOT_PATH."/main-app/class/Actividades.php");

        // Verificar actividades registradas (act_registrada=1)
        $sql = "SELECT COUNT(*) as total, 
                       GROUP_CONCAT(DISTINCT CONCAT('Carga: ', act_id_carga, ', Período: ', act_periodo) SEPARATOR '; ') as cargas_periodos
                FROM ".BD_ACADEMICA.".academico_actividades 
                WHERE act_id_tipo=? AND act_estado=1 AND act_registrada=1 AND institucion=? AND year=?";

        $parametros = [$idIndicador, $config['conf_id_institucion'], $year];

        // Si se especifica carga, agregar filtro
        if (!empty($idCarga)) {
            $sql .= " AND act_id_carga=?";
            $parametros[] = $idCarga;
        }

        // Si se especifica período, agregar filtro
        if (!empty($periodo)) {
            $sql .= " AND act_periodo=?";
            $parametros[] = $periodo;
        }

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $datos = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        $total = !empty($datos['total']) ? (int)$datos['total'] : 0;
        
        // También verificar si hay actividades no registradas pero existentes
        $sqlTodas = "SELECT COUNT(*) as total_todas 
                     FROM ".BD_ACADEMICA.".academico_actividades 
                     WHERE act_id_tipo=? AND act_estado=1 AND institucion=? AND year=?";
        $parametrosTodas = [$idIndicador, $config['conf_id_institucion'], $year];
        
        if (!empty($idCarga)) {
            $sqlTodas .= " AND act_id_carga=?";
            $parametrosTodas[] = $idCarga;
        }
        
        if (!empty($periodo)) {
            $sqlTodas .= " AND act_periodo=?";
            $parametrosTodas[] = $periodo;
        }
        
        $resultadoTodas = BindSQL::prepararSQL($sqlTodas, $parametrosTodas);
        $datosTodas = mysqli_fetch_array($resultadoTodas, MYSQLI_BOTH);
        $totalTodas = !empty($datosTodas['total_todas']) ? (int)$datosTodas['total_todas'] : 0;
        
        // Verificar calificaciones asociadas
        $sqlCalificaciones = "SELECT COUNT(DISTINCT aac.cal_id) as total_calificaciones 
                              FROM ".BD_ACADEMICA.".academico_actividades aa
                              INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad = aa.act_id AND aac.institucion = aa.institucion AND aac.year = aa.year
                              WHERE aa.act_id_tipo=? AND aa.act_estado=1 AND aa.institucion=? AND aa.year=?";
        $parametrosCalificaciones = [$idIndicador, $config['conf_id_institucion'], $year];
        
        if (!empty($idCarga)) {
            $sqlCalificaciones .= " AND aa.act_id_carga=?";
            $parametrosCalificaciones[] = $idCarga;
        }
        
        if (!empty($periodo)) {
            $sqlCalificaciones .= " AND aa.act_periodo=?";
            $parametrosCalificaciones[] = $periodo;
        }
        
        $resultadoCalificaciones = BindSQL::prepararSQL($sqlCalificaciones, $parametrosCalificaciones);
        $datosCalificaciones = mysqli_fetch_array($resultadoCalificaciones, MYSQLI_BOTH);
        $totalCalificaciones = !empty($datosCalificaciones['total_calificaciones']) ? (int)$datosCalificaciones['total_calificaciones'] : 0;
        
        // El indicador está en uso si tiene actividades o calificaciones
        $enUso = $total > 0 || $totalTodas > 0 || $totalCalificaciones > 0;

        $mensaje = '';
        if ($enUso) {
            if (!empty($idCarga) && !empty($periodo)) {
                $mensaje = "Este indicador está en uso en esta carga y período. ";
                if ($total > 0) {
                    $mensaje .= "Tiene {$total} actividad(es) registrada(s). ";
                }
                if ($totalTodas > $total) {
                    $mensaje .= "Tiene " . ($totalTodas - $total) . " actividad(es) adicional(es). ";
                }
                if ($totalCalificaciones > 0) {
                    $mensaje .= "Tiene {$totalCalificaciones} calificación(es) asociada(s). ";
                }
                $mensaje .= "No puede ser modificado o eliminado.";
            } else {
                $mensaje = "Este indicador está en uso. ";
                if ($total > 0) {
                    $mensaje .= "Tiene {$total} actividad(es) registrada(s) en: " . ($datos['cargas_periodos'] ?? 'varias cargas') . ". ";
                }
                if ($totalTodas > $total) {
                    $mensaje .= "Tiene " . ($totalTodas - $total) . " actividad(es) adicional(es). ";
                }
                if ($totalCalificaciones > 0) {
                    $mensaje .= "Tiene {$totalCalificaciones} calificación(es) asociada(s). ";
                }
                $mensaje .= "No puede ser modificado o eliminado.";
            }
        }

        return [
            'enUso' => $enUso,
            'mensaje' => $mensaje,
            'totalActividades' => $total,
            'totalActividadesTodas' => $totalTodas,
            'totalCalificaciones' => $totalCalificaciones,
            'cargasPeriodos' => !empty($datos['cargas_periodos']) ? $datos['cargas_periodos'] : ''
        ];
    }

    /**
     * Verifica si ya existe un indicador asignado a una carga y período específicos
     * @param array     $config
     * @param string    $idIndicador
     * @param string    $idCarga
     * @param int       $periodo
     * @param string    $yearBd
     * 
     * @return bool
    **/
    public static function verificarIndicadorCargaPeriodo (
        array   $config,
        string  $idIndicador,
        string  $idCarga,
        int     $periodo,
        string  $yearBd = ""
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_indicadores_carga 
                WHERE ipc_indicador=? AND ipc_carga=? AND ipc_periodo=? AND institucion=? AND year=?";

        $parametros = [$idIndicador, $idCarga, $periodo, $config['conf_id_institucion'], $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $datos = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return !empty($datos['total']) && (int)$datos['total'] > 0;
    }

    /**
     * Verifica si el docente ya tiene indicadores creados en una carga y período específicos
     * @param array     $config
     * @param string    $idCarga
     * @param int       $periodo
     * @param string    $yearBd
     * 
     * @return bool
    **/
    public static function verificarIndicadoresDocenteCargaPeriodo (
        array   $config,
        string  $idCarga,
        int     $periodo,
        string  $yearBd = ""
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT COUNT(*) as total FROM ".BD_ACADEMICA.".academico_indicadores_carga 
                WHERE ipc_carga=? AND ipc_periodo=? AND ipc_creado=1 AND institucion=? AND year=?";

        $parametros = [$idCarga, $periodo, $config['conf_id_institucion'], $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $datos = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return !empty($datos['total']) && (int)$datos['total'] > 0;
    }

    /**
     * Asigna un indicador a múltiples cargas y períodos
     * @param mysqli    $conexion
     * @param PDO       $conexionPDO
     * @param array     $config
     * @param string    $idIndicador
     * @param array     $cargas - Array de IDs de cargas (vacío = todas las cargas activas)
     * @param array     $periodos - Array de números de períodos
     * @param float     $valor - Valor del indicador
     * @param string    $yearBd
     * 
     * @return array    ['asignadas' => int, 'duplicadas' => int, 'errores' => array]
    **/
    public static function asignarIndicadorACargas (
        mysqli  $conexion,
        PDO     $conexionPDO,
        array   $config,
        string  $idIndicador,
        array   $cargas,
        array   $periodos,
        float   $valor,
        string  $yearBd = ""
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        require_once(ROOT_PATH."/main-app/class/CargaAcademicaOptimizada.php");

        $asignadas = 0;
        $duplicadas = 0;
        $errores = [];

        // Si no se especificaron cargas, obtener todas las cargas activas que tienen car_indicadores_directivo=1
        if (empty($cargas)) {
            $selectSql = ["car.car_id"];
            $cargasConsulta = CargaAcademicaOptimizada::listarCargasOptimizado($conexion, $config, "", "AND car.car_activa=1 AND car.car_indicadores_directivo=1", "car.car_id", "", "", array(), $selectSql);
            $cargas = [];
            while ($carga = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)) {
                $cargas[] = $carga['car_id'];
            }
        }

        // Validar que haya cargas y períodos
        if (empty($cargas)) {
            $errores[] = "No hay cargas académicas disponibles para asignar el indicador.";
            return ['asignadas' => 0, 'duplicadas' => 0, 'errores' => $errores];
        }

        if (empty($periodos)) {
            $errores[] = "Debe seleccionar al menos un período.";
            return ['asignadas' => 0, 'duplicadas' => 0, 'errores' => $errores];
        }

        $omitidas = 0; // Contador de asignaciones omitidas por tener indicadores del docente

        // Asignar a cada combinación de carga y período
        foreach ($cargas as $idCarga) {
            foreach ($periodos as $periodo) {
                // Verificar si ya existe este indicador específico
                if (Indicadores::verificarIndicadorCargaPeriodo($config, $idIndicador, $idCarga, (int)$periodo, $year)) {
                    $duplicadas++;
                    continue;
                }

                // Validar si el docente ya tiene indicadores creados en este período
                // Si ya tiene indicadores del docente, omitir la asignación del indicador obligatorio
                if (Indicadores::verificarIndicadoresDocenteCargaPeriodo($config, $idCarga, (int)$periodo, $year)) {
                    $omitidas++;
                    continue;
                }

                // Crear la relación
                try {
                    Indicadores::guardarIndicadorCarga($conexion, $conexionPDO, $config, $idCarga, $idIndicador, (int)$periodo, NULL, NULL, 0, $valor);
                    $asignadas++;
                } catch (Exception $e) {
                    $errores[] = "Error al asignar indicador a carga {$idCarga}, período {$periodo}: " . $e->getMessage();
                }
            }
        }

        return [
            'asignadas' => $asignadas,
            'duplicadas' => $duplicadas,
            'omitidas' => $omitidas,
            'errores' => $errores
        ];
    }

}