<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class Ausencias {

    /**
     * Esta función ejecuta una consulta preparada para seleccionar los registros de ausencias
     * de un estudiante en una clase determinada de la base de datos.
     *
     * @param array  $config       Configuración de la aplicación.
     * @param string $idClase      Identificador de la clase de la que se desean obtener las ausencias.
     * @param string $idEstudiante Identificador del estudiante del que se desean obtener las ausencias.
     * @param string $yearBd       Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
     * @return mixed|array|null    Retorna un array asociativo con los datos de ausencias si se encuentra alguna coincidencia, o NULL si no se encuentra ninguna.
    **/
    public static function traerAusenciasClaseEstudiante (
        array   $config,
        string  $idClase,  
        string  $idEstudiante,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_ausencias WHERE aus_id_clase=? AND aus_id_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idClase, $idEstudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este método ejecuta una consulta SQL para obtener la suma total de ausencias de un estudiante
     * en una carga académica determinada, considerando un período específico.
     *
     * @param array   $config       Configuración de la aplicación.
     * @param string  $idCurso      Identificador del curso al que pertenece la carga académica.
     * @param string  $idMateria    Identificador de la materia de la carga académica.
     * @param int     $periodo      Número de período o sesión en la carga académica.
     * @param string  $idEstudiante Identificador del estudiante del que se desean obtener las ausencias.
     * @param string  $yearBd       Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
     * @return mixed|array|null     Retorna un array asociativo con la suma total de ausencias si se encuentra alguna coincidencia, o NULL si no se encuentra ninguna.
    **/
    public static function sumarAusenciasCarga (
        array   $config,
        string  $idCurso,  
        string  $idMateria,
        int     $periodo,
        string  $idEstudiante,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT sum(aus_ausencias) as sumAus FROM ".BD_ACADEMICA.".academico_ausencias aus
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_curso=? AND car_materia=? AND car.institucion=aus.institucion AND car.year=aus.year
        INNER JOIN ".BD_ACADEMICA.".academico_clases cls ON cls.cls_id=aus.aus_id_clase AND cls.cls_id_carga=car_id AND cls.cls_periodo=? AND cls.institucion=aus.institucion AND cls.year=aus.year
        WHERE aus.aus_id_estudiante=? AND aus.institucion=? AND aus.year=?";

        $parametros = [$idCurso, $idMateria, $periodo, $idEstudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
    * Este método ejecuta una consulta SQL para insertar una nueva ausencia en la tabla academico_ausencias
    * utilizando los parámetros proporcionados.
    *
    * @param PDO     $conexionPDO   Objeto de conexión PDO.
    * @param string  $insert        Lista de campos para insertar separados por comas.
    * @param array   $parametros    Array de valores de parámetros para la consulta preparada.
    **/
    public static function guardarAusencia (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_ausencias');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_ausencias({$insert}) VALUES ({$signosPreguntas})";
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Este método ejecuta una consulta SQL para actualizar una ausencia en la tabla academico_ausencias
    * utilizando los parámetros proporcionados.
    *
    * @param array   $config       Configuración general del sistema.
    * @param string  $idAusencia   Identificador único de la ausencia a actualizar.
    * @param string  $update       Lista de campos a actualizar con sus nuevos valores en formato de SQL UPDATE.
    * @param string  $yearBd       Año académico para el que se realiza la actualización (opcional).
    **/
    public static function actualizarAusencia (
        array   $config,
        string  $idAusencia,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_ausencias SET {$updateSql} WHERE aus_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idAusencia, $config['conf_id_institucion'], $year]);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Este método ejecuta una consulta SQL para eliminar una ausencia de la tabla academico_ausencias
    * utilizando el identificador de la ausencia y la configuración proporcionada.
    *
    * @param array   $config       Configuración general del sistema.
    * @param string  $idAusencia   Identificador único de la ausencia a eliminar.
    * @param string  $yearBd       Año académico para el que se realiza la eliminación (opcional).
    **/
    public static function eliminarAusenciasID (
        array   $config,
        string  $idAusencia,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_ausencias WHERE aus_id=? AND institucion=? AND year=?";

        $parametros = [$idAusencia, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Este método ejecuta una consulta SQL para eliminar todas las ausencias de un estudiante
    * en la tabla academico_ausencias utilizando el identificador del estudiante y la configuración proporcionada.
    *
    * @param array   $config       Configuración general del sistema.
    * @param string  $idEstudiante Identificador único del estudiante cuyas ausencias se desean eliminar.
    * @param string  $yearBd       Año académico para el que se realiza la eliminación (opcional).
    **/
    public static function eliminarAusenciasEstudiantes (
        array   $config,
        string  $idEstudiante,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_ausencias WHERE aus_id_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Este método ejecuta una consulta SQL para eliminar todas las ausencias asociadas a una clase
    * en la tabla academico_ausencias utilizando el identificador de la clase y la configuración proporcionada.
    *
    * @param array   $config       Configuración general del sistema.
    * @param string  $idClase      Identificador único de la clase cuyas ausencias se desean eliminar.
    * @param string  $yearBd       Año académico para el que se realiza la eliminación (opcional).
    **/
    public static function eliminarAusenciasClases (
        array   $config,
        string  $idClase,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_ausencias WHERE aus_id_clase=? AND institucion=? AND year=?";

        $parametros = [$idClase, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Este método ejecuta una consulta SQL para eliminar todas las ausencias asociadas a una institución
    * en la tabla academico_ausencias utilizando la configuración proporcionada.
    *
    * @param array   $config       Configuración general del sistema.
    * @param string  $yearBd       Año académico para el que se realiza la eliminación (opcional).
    * @return void
    **/
    public static function eliminarAusenciasInstitucion (
        array   $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_ausencias WHERE institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Este método ejecuta una consulta SQL para contar el número de estudiantes ausentes en una clase
    * determinada, considerando la configuración y los datos de carga proporcionados.
    *
    * @param array   $config            Configuración general del sistema.
    * @param array   $datosCargaActual  Datos de la carga académica actual.
    * @param string  $idClase           Identificador de la clase.
    * @param string  $yearBd            Año académico para el que se realiza la consulta (opcional).
    * @return mixed  El resultado de la consulta, que representa el número de estudiantes ausentes.
    **/
    public static function consultaNumEstudiantesAusencias(
        array  $config,
        array  $datosCargaActual, 
        string $idClase, 
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        if($datosCargaActual['gra_tipo'] == GRADO_INDIVIDUAL) {
            $sql = "SELECT count(*) FROM ".BD_ACADEMICA.".academico_ausencias aus
            INNER JOIN ".BD_ADMIN.".mediatecnica_matriculas_cursos ON matcur_id_curso=? AND matcur_id_grupo=? AND matcur_estado='".ACTIVO."' AND matcur_id_institucion=aus.institucion AND matcur_years=aus.year AND matcur_id_matricula=aus.aus_id_estudiante
            INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 AND mat_id=matcur_id_matricula AND mat.institucion=aus.institucion AND mat.year=aus.year
            WHERE aus.aus_id_clase=? AND aus.institucion=? AND aus.year=?";

            $parametros = [$datosCargaActual['car_curso'], $datosCargaActual['car_grupo'], $idClase, $config['conf_id_institucion'], $year];
        } else {
            $sql = "SELECT count(*) FROM ".BD_ACADEMICA.".academico_ausencias aus
            INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_grado=? AND mat_grupo=? AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 AND mat_id=aus.aus_id_estudiante AND mat.institucion=aus.institucion AND mat.year=aus.year
            WHERE aus.aus_id_clase=? AND aus.institucion=? AND aus.year=?";

            $parametros = [$datosCargaActual['car_curso'], $datosCargaActual['car_grupo'], $idClase, $config['conf_id_institucion'], $year];
        }

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
}