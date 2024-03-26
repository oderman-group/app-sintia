<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

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
        try{
            $consulta=mysqli_query($conexion, "SELECT
            (SELECT sum(ipc_valor) FROM ".BD_ACADEMICA.".academico_indicadores_carga 
            WHERE ipc_carga='".$idcarga."' AND ipc_periodo='".$periodo."' AND ipc_creado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
            (SELECT sum(ipc_valor) FROM ".BD_ACADEMICA.".academico_indicadores_carga 
            WHERE ipc_carga='".$idcarga."' AND ipc_periodo='".$periodo."' AND ipc_creado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}),
            (SELECT count(*) FROM ".BD_ACADEMICA.".academico_indicadores_carga 
            WHERE ipc_carga='".$idcarga."' AND ipc_periodo='".$periodo."' AND ipc_creado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]})");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

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

		try{
			mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_indicadores_carga(ipc_id, ipc_carga, ipc_indicador, ipc_valor, ipc_periodo, ipc_creado, ipc_copiado, ipc_evaluacion, institucion, year) VALUES('".$codigo."', '".$idcarga."', '".$idIndicador."', '".$valorIndicador."', '".$periodo."', '".$creado."', '".$copiado."', '".$evaluacion."', {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
		} catch (Exception $e) {
			include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
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
		try{
			mysqli_query($conexion, "UPDATE ".BD_ACADEMICA.".academico_indicadores_carga SET ipc_valor='".$valorIndicadores."' WHERE ipc_carga='".$idcarga."' AND ipc_periodo='".$periodo."' AND ipc_creado=1 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
		} catch (Exception $e) {
			include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
		}
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
        try{
            $consulta=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga='".$idCarga."' AND ipc_indicador='".$idIndicador."' AND ipc_creado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

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
        try{
            mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga='".$idCarga."' AND ipc_indicador='".$idIndicador."' AND ipc_creado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
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
        try{
            $consulta=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga ipc
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=ipc.ipc_indicador AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$yearConsulta}
            WHERE ipc.ipc_carga='".$idCarga."' AND ipc.ipc_periodo='".$periodo."' AND ipc.institucion={$config['conf_id_institucion']} AND ipc.year={$yearConsulta}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }

        return $consulta;
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
        try{
            $consulta=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga aic
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=aic.ipc_indicador AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$_SESSION["bd"]}
            WHERE aic.ipc_id='".$idIndicador."' AND aic.institucion={$config['conf_id_institucion']} AND aic.year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

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
        try{
            mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_id='" . $idIndicador . "' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
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
        try{
            $consulta=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_creado=0 AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }

        return $consulta;
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
        try{
            $consulta=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga ipc
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_id=ipc.ipc_indicador AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$_SESSION["bd"]}
            WHERE ipc.ipc_carga='".$idCarga."' AND ipc.institucion={$config['conf_id_institucion']} AND ipc.year={$_SESSION["bd"]} $filtro
            ORDER BY ipc.ipc_periodo");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }

        return $consulta;
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
        try{
            $consulta=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_carga 
            WHERE ipc_indicador='".$idIndicador."' AND ipc_carga='".$idCarga."' AND ipc_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

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
        try{
            mysqli_query($conexion, "DELETE FROM ".BD_ACADEMICA.".academico_indicadores_carga WHERE ipc_carga='".$idCarga."' AND ipc_periodo='".$periodo."' AND institucion={$config['conf_id_institucion']} AND year={$_SESSION["bd"]}");
        } catch (Exception $e) {
            include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
        }
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

}