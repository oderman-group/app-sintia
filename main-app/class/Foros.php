<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
class Foros{
    /**
     * Este metodo me consulta los datos de un foro
     * @param mysqli $conexion
     * @param array $config
     * @param string $idForo
     * 
     * @return array $resultado
     */
    public static function consultarDatosForos(mysqli $conexion, array $config, string $idForo){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_foro WHERE foro_id=? AND institucion=? AND year=?";

        $parametros = [$idForo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
    
    /**
     * Este metodo me consulta los datos de un foro
     * @param mysqli $conexion
     * @param array $config
     * @param string $idForo
     * @param string $carga
     * @param int $periodo
     * 
     * @return mysqli_result $consulta
     */
    public static function traerForosDisintos(mysqli $conexion, array $config, string $idForo, string $carga, int $periodo){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_foro 
        WHERE foro_id_carga=? AND foro_periodo=? AND foro_estado=1 AND foro_id!=? AND institucion=? AND year=?
        ORDER BY foro_id DESC";

        $parametros = [$carga, $periodo, $idForo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Este metodo me guarda un foro
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     * @param string $carga
     * @param int $periodo
     */
    public static function guardarForos(mysqli $conexion, array $config, array $POST, string $carga, int $periodo){
        global $conexionPDO;
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_actividad_foro');

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_actividad_foro(foro_id, foro_nombre, foro_descripcion, foro_id_carga, foro_periodo, foro_estado, institucion, year)VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
        
        $parametros = [$codigo, mysqli_real_escape_string($conexion,$POST["titulo"]), mysqli_real_escape_string($conexion,$POST["contenido"]), $carga, $periodo, 1, $config['conf_id_institucion'], $_SESSION["bd"]];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }
    
    /**
     * Este metodo me consulta los datos de un foro
     * @param mysqli $conexion
     * @param array $config
     * @param string $carga
     * @param int $periodo
     * 
     * @return mysqli_result $consulta
     */
    public static function traerForos(mysqli $conexion, array $config, string $carga, int $periodo){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_foro 
        WHERE foro_id_carga=? AND foro_periodo=? AND foro_estado=1 AND institucion=? AND year=?
        ORDER BY foro_id DESC";

        $parametros = [$carga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Este metodo me actualiza un foro
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     */
    public static function actualizarForos(mysqli $conexion, array $config, array $POST){
        $sql = "UPDATE ".BD_ACADEMICA.".academico_actividad_foro SET foro_nombre=?, foro_descripcion=? WHERE foro_id=? AND institucion=? AND year=?";

        $parametros = [mysqli_real_escape_string($conexion,$POST["titulo"]), mysqli_real_escape_string($conexion,$POST["contenido"]), $POST["idR"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina un foro
     * @param mysqli $conexion
     * @param array $config
     * @param string $idForo
     */
    public static function eliminarForos(mysqli $conexion, array $config, string $idForo){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro WHERE foro_id=? AND institucion=? AND year=?";

        $parametros = [$idForo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me consulta los comentarios de un foro
     * @param mysqli $conexion
     * @param array $config
     * @param string $idForo
     * @param string $filtro
     * 
     * @return mysqli_result $consulta
     */
    public static function traerComentariosForos(mysqli $conexion, array $config, string $idForo, string $filtro = ''){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_foro_comentarios com
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=com_id_estudiante AND uss.institucion=com.institucion AND uss.year=com.year
        WHERE com_id_foro=? AND com.institucion=? AND com.year=?
        {$filtro}
        ORDER BY com_id DESC";

        $parametros = [$idForo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Este metodo me elimina un comentario
     * @param mysqli $conexion
     * @param array $config
     * @param string $idComentario
     */
    public static function eliminarComentario(mysqli $conexion, array $config, string $idComentario){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_comentarios WHERE com_id=? AND institucion=? AND year=?";

        $parametros = [$idComentario, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina un comentario
     * @param mysqli $conexion
     * @param array $config
     */
    public static function eliminarTodosComentario(mysqli $conexion, array $config){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_comentarios WHERE institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina un comentario
     * @param mysqli $conexion
     * @param array $config
     * @param string $idEstudiante
     */
    public static function eliminarComentarioEstudiante(mysqli $conexion, array $config, string $idEstudiante){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_comentarios WHERE com_id_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina un comentario
     * @param mysqli $conexion
     * @param array $config
     * @param string $idForo
     */
    public static function eliminarComentarioForo(mysqli $conexion, array $config, string $idForo){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_comentarios WHERE com_id_foro=? AND institucion=? AND year=?";

        $parametros = [$idForo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me guarda un comentario
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     */
    public static function guardarComentario(mysqli $conexion, array $config, array $POST){
        global $conexionPDO;
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_actividad_foro_comentarios');

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_actividad_foro_comentarios(com_id, com_id_foro, com_descripcion, com_id_estudiante, com_fecha, institucion, year)VALUES(?, ?, ?, ?, now(), ?, ?)";
        
        $parametros = [$codigo, mysqli_real_escape_string($conexion,$POST["foro"]), mysqli_real_escape_string($conexion,$POST["contenido"]), $_SESSION["id"], $config['conf_id_institucion'], $_SESSION["bd"]];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }
    
    /**
     * Este metodo me trae las respuesta de un comentario
     * @param mysqli $conexion
     * @param array $config
     * @param string $idComentario
     * 
     * @return mysqli_result $consulta
     */
    public static function consultarRespuestas(mysqli $conexion, array $config, string $idComentario){
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_actividad_foro_respuestas fore
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=fore.fore_id_estudiante AND uss.institucion=fore.institucion AND uss.year=fore.year
        WHERE fore.fore_id_comentario=? AND fore.institucion=? AND fore.year=?
        ORDER BY fore.fore_id ASC";

        $parametros = [$idComentario, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Este metodo me elimina las respuesta de un comentario
     * @param mysqli $conexion
     * @param array $config
     * @param string $idComentario
     */
    public static function eliminarRespuestaComentario(mysqli $conexion, array $config, string $idComentario){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_respuestas WHERE fore_id_comentario=? AND institucion=? AND year=?";

        $parametros = [$idComentario, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina una respuesta
     * @param mysqli $conexion
     * @param array $config
     * @param string $idRespuesta
     */
    public static function eliminarRespuesta(mysqli $conexion, array $config, string $idRespuesta){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_respuestas WHERE fore_id=? AND institucion=? AND year=?";

        $parametros = [$idRespuesta, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina todas las respuestas
     * @param mysqli $conexion
     * @param array $config
     */
    public static function eliminarTodasRespuestas(mysqli $conexion, array $config){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_respuestas WHERE institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me elimina las respuestas de un estudiante
     * @param mysqli $conexion
     * @param array $config
     * @param string $idEstudiante
     */
    public static function eliminarRespuestaEstudiante(mysqli $conexion, array $config, string $idEstudiante){
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_actividad_foro_respuestas WHERE fore_id_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Este metodo me guarda una respuesta
     * @param mysqli $conexion
     * @param array $config
     * @param array $POST
     */
    public static function guardarRespuesta(mysqli $conexion, array $config, array $POST){
        global $conexionPDO;
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_actividad_foro_respuestas');

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_actividad_foro_respuestas(fore_id, fore_id_estudiante, fore_id_comentario, fore_fecha, fore_respuesta, institucion, year)VALUES(?, ?', ?, now(), ?, ?, ?)";
        
        $parametros = [$codigo, $_SESSION["id"], $POST["comentario"], mysqli_real_escape_string($conexion,$POST["contenido"]), $config['conf_id_institucion'], $_SESSION["bd"]];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }
}