<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";

class Indicadores {

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
     * @param array     $config
     * @param string    $idcarga
     * @param string    $idIndicador
     * @param int       $periodo
     * @param array     $POST
     * @param array     $indicadorCopiado
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
        $codigo             = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores_carga');
        $copiado            = NULL;
        $evaluacion         = NULL;
        $valorIndicador     = NULL;

        if($POST != NULL){
            $evaluacion     = !empty($POST["saberes"]) ? $POST["saberes"] : NULL;
            $valorIndicador = !empty($valor) ? $valor : (!empty($POST["valor"]) ? $POST["valor"] : NULL);
        }
        
        if($indicadorCopiado != NULL){
            $evaluacion         = !empty($indicadorCopiado['ipc_evaluacion']) ? $indicadorCopiado['ipc_evaluacion'] : NULL;
            $copiado            = !empty($indicadorCopiado['ind_id']) ? $indicadorCopiado['ind_id'] : NULL;
            $valorIndicador     = !empty($indicadorCopiado['ipc_valor']) ? $indicadorCopiado['ipc_valor'] : NULL;
        }

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_carga(ipc_id, ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, ipc_evaluacion, institucion, year) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $idcarga, $idIndicador, $valorIndicador, $periodo, $creado, $copiado, $evaluacion, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
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
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga ipc
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=ipc.ipc_indicador AND ai.institucion=ipc.institucion AND ai.year=ipc.year
        WHERE ipc.ipc_carga=? AND ipc.ipc_periodo=? AND ipc.institucion=? AND ipc.year=?";

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
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

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
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

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

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion 
            WHERE rind_carga=? AND rind_estudiante=? AND rind_periodo=? AND rind_indicador=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $estudiante, $periodo, $idIndicador, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
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
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_indicadores_carga SET {$updateSql} WHERE ipc_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idIndicador, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

}