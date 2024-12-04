<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

class Asignaturas {

    /**
     * Este metodo me trae los dotos de una asignatura
     * @param mysqli $conexion
     * @param array $config
     * @param string $idMateria
     * 
     * @return array $resultado
    **/
    public static function consultarDatosAsignatura (
        mysqli $conexion, 
        array $config,
        string $idMateria
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_materias WHERE mat_id=? AND institucion=? AND year=?";

        $parametros = [$idMateria, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me suma el valor de las materias de un area
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idGrupo
     * @param string $idArea
     * @param string $yearBd
     * 
     * @return array $resultado
    **/
    public static function sumarValorAsignaturasArea (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idGrupo,
        string $idArea,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT SUM(mat_valor) FROM ".BD_ACADEMICA.".academico_materias am
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_materia=am.mat_id AND car_curso=? AND car_grupo=? AND car.institucion=am.institucion AND car.year=am.year
        WHERE am.mat_area=? AND am.institucion=? AND am.year=?";

        $parametros = [$idCurso, $idGrupo, $idArea, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me consulta las asignaturas de un curso
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idGrupo
     * @param string $yearBd
     * 
     * @return mysqli_result $consulta
    **/
    public static function consultarAsignaturasCurso (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idGrupo,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT ar_id, ar_nombre, count(*) AS numMaterias, car_curso, car_grupo FROM ".BD_ACADEMICA.".academico_materias am
        INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id = am.mat_area AND a.institucion=am.institucion AND a.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car on car_materia = am.mat_id and car_curso=? AND car_grupo=? AND car.institucion=am.institucion AND car.year=am.year
        WHERE am.institucion=? AND am.year=?
        GROUP by am.mat_area
        ORDER BY a.ar_posicion";

        $parametros = [$idCurso, $idGrupo, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me consulta las asignaturas de una area
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idGrupo
     * @param string $idArea
     * @param string $yearBd
     * 
     * @return mysqli_result $consulta
    **/
    public static function consultarAsignaturasArea (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idGrupo,
        string $idArea,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id 
        FROM 
            ".BD_ACADEMICA.".academico_materias am, 
            ".BD_ACADEMICA.".academico_cargas car 
        WHERE 
            am.mat_area=? 
        AND am.mat_id=car_materia 
        AND car_curso=? 
        AND car_grupo=? 
        AND am.institucion=? 
        AND am.year=? 
        AND car.institucion=am.institucion 
        AND car.year=am.institucion
        ";

        $parametros = [$idArea, $idCurso, $idGrupo, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me consulta las asignaturas, Definitiva  e intensidad de una carga
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idCursoEst
     * @param string $idGrupo
     * @param string $idArea
     * @param string $yearBd
     * 
     * @return mysqli_result $consulta
    **/
    public static function consultarAsignaturaDefinitivaIntensidad (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idCursoEst,
        string $idGrupo,
        string $idArea,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id, am.mat_nombre, ipc.ipc_intensidad FROM ".BD_ACADEMICA.".academico_materias am, ".BD_ACADEMICA.".academico_cargas car, ".BD_ACADEMICA.".academico_intensidad_curso ipc WHERE am.mat_area=? AND am.mat_id=car_materia AND car_curso=? AND car_grupo=? AND ipc.ipc_curso=? AND ipc.ipc_materia=am.mat_id AND ipc.institucion=am.institucion AND ipc.year=am.year AND am.institucion=? AND am.year=? AND car.institucion=am.institucion AND car.year=am.year";

        $parametros = [$idArea, $idCurso, $idGrupo, $idCursoEst, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me consulta las asignaturas, el curso y el docente de una carga
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idMateria
     * @param string $idUsuario
     * @param string $yearBd
     * 
     * @return array $resultado
    **/
    public static function consultarAsignaturaCursoUsuario (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idMateria,
        string $idUsuario,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_id, mat_nombre, gra_codigo, gra_nombre, uss_id, uss_nombre FROM ".BD_ACADEMICA.".academico_materias am, ".BD_ACADEMICA.".academico_grados gra, ".BD_GENERAL.".usuarios uss WHERE am.mat_id=? AND gra_id=? AND uss_id=? AND am.institucion=? AND am.year=? AND gra.institucion=am.institucion AND gra.year=am.year AND uss.institucion=am.institucion AND uss.year=am.year";

        $parametros = [$idMateria, $idCurso, $idUsuario, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me busca una asignatura segun su nombre o siglas
     * @param mysqli $conexion
     * @param array $config
     * @param string $buscar
     * 
     * @return mysqli_result $consulta
    **/
    public static function buscadorAsignatura (
        mysqli $conexion, 
        array $config,
        string $buscar
    )
    {

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_materias WHERE mat_nombre LIKE '%".$buscar."%' OR mat_siglas LIKE '%".$buscar."%' AND institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae el nombre y docente de las materias de una carga
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idGrupo
     * @param string $yearBd
     * 
     * @return mysqli_result $consulta
    **/
    public static function consultarNombreAsignatura (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idGrupo,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_nombre, car_docente, car_director_grupo FROM ".BD_ACADEMICA.".academico_materias am
        INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id = am.mat_area AND a.institucion=am.institucion AND a.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car on car_materia = am.mat_id and car_curso=? AND car_grupo=? AND car.institucion=am.institucion AND car.year=am.year
        WHERE am.institucion=? AND am.year=?
        ORDER BY am.mat_id";

        $parametros = [$idCurso, $idGrupo, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae la nota de una materia
     * @param mysqli $conexion
     * @param array $config
     * @param string $idCurso
     * @param string $idGrupo
     * @param int    $periodo
     * @param string $idEstudiante
     * @param string $yearBd
     * 
     * @return mysqli_result $consulta
    **/
    public static function consultarNotaAsignatura (
        mysqli $conexion, 
        array $config,
        string $idCurso,
        string $idGrupo,
        int    $periodo,
        string $idEstudiante,
        string $yearBd    = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT bol_nota FROM ".BD_ACADEMICA.".academico_materias am
        INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id = am.mat_area AND a.institucion=am.institucion AND a.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_materia = am.mat_id AND car_curso=? AND car_grupo=? AND car.institucion=am.institucion AND car.year=am.year
        LEFT JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol_carga=car_id AND bol_periodo=? AND bol_estudiante=? AND bol.institucion=am.institucion AND bol.year=am.year
        WHERE am.institucion=? AND am.year=?
        ORDER BY am.mat_id";

        $parametros = [$idCurso, $idGrupo, $periodo, $idEstudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene los indicadores y notas asociados a una materia y estudiante específicos, considerando un área, grados, grupos y condiciones de periodo académico.
     *
     * @param string $grado El identificador del grado.
     * @param string $grupo El identificador del grupo.
     * @param string $materia El identificador del área.
     * @param int    $periodo
     * @param string $estudiante La identificación del estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene los indicadores, notas y periodos asociados a una materia y estudiante específicos.
     */
    public static function obtenerIndicadoresPorMateriaPeriodo(
        string    $grado,
        string    $grupo,
        string    $materia,
        int       $periodo,
        string    $estudiante,
        string    $yearBd    = ''
    )
    {
        global $config;

        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_nombre,mat_area,mat_id,ind_nombre,ipc_periodo,
        ROUND(SUM(cal_nota*(act_valor/100)) / SUM(act_valor/100),2) as nota, ind_id FROM ".BD_ACADEMICA.".academico_materias am
        INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion=am.institucion AND a.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_materia=am.mat_id AND car.institucion=am.institucion AND car.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga aic ON aic.ipc_carga=car.car_id AND aic.institucion=am.institucion AND aic.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON aic.ipc_indicador=ai.ind_id AND ai.institucion=am.institucion AND ai.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND act_id_carga=car_id AND act_estado=1 AND act_registrada=1 AND aa.institucion=am.institucion AND aa.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id AND aac.institucion=am.institucion AND aac.year=am.year
        WHERE car_curso=? and car_grupo=? and mat_id=? AND ipc_periodo=? AND cal_id_estudiante=? and act_periodo=? AND am.institucion=? AND am.year=?
        GROUP BY act_id_tipo, act_id_carga
        ORDER BY mat_id, ipc_periodo, ind_id";

        $parametros = [$grado, $grupo, $materia, $periodo, $estudiante, $periodo, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trae la todas las materias de la institucion
     * @param mysqli $conexion
     * @param array $config
     * @param string $filtro
     * 
     * @return mysqli_result $consulta
    **/
    public static function consultarTodasAsignaturas (
        mysqli $conexion, 
        array $config, 
        string $filtro = ""
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_materias am
        INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id=am.mat_area AND ar.institucion=am.institucion AND ar.year=am.year
        WHERE am.institucion=? AND am.year=? {$filtro}
        ORDER BY am.mat_nombre";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me cuenta todas las materias de un area
     * @param mysqli $conexion
     * @param array     $config
     * @param string    $idArea
     * 
     * @return array $resultado
    **/
    public static function contarAsignaturasArea (
        mysqli $conexion, 
        array $config, 
        string $idArea
    )
    {
        $sql = "SELECT COUNT(mat_id) FROM ".BD_ACADEMICA.".academico_materias WHERE mat_area=? AND institucion=? AND year=?";

        $parametros = [$idArea, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me guarda una asignatura
     * @param mysqli    $conexion
     * @param PDO       $conexionPDO
     * @param array     $config
     * @param array     $POST
     * 
     * @return string $codigo
    **/
    public static function guardarAsignatura (
        mysqli $conexion,
        PDO    $conexionPDO, 
        array  $config, 
        array  $POST
    )
    {
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_materias');
    
        if(empty($POST["siglasM"])) {$POST["siglasM"] = substr($POST["nombreM"], 0, 3);}
        if(empty($POST["porcenAsigna"])) {$POST["porcenAsigna"] = '';}
        $POST["sumarPromedio"] = !empty($POST["sumarPromedio"]) ? $POST["sumarPromedio"] : SI;
        $codigoAsignatura = "ASG".strtotime("now");

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_materias(mat_id, mat_codigo, mat_nombre, mat_siglas, mat_area, mat_oficial, mat_valor, mat_sumar_promedio, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $codigoAsignatura, $POST["nombreM"], strtoupper($POST["siglasM"]), $POST["areaM"], 1, $POST["porcenAsigna"], $POST["sumarPromedio"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
     * Este metodo me actualiza una asignatura
     * @param mysqli    $conexion
     * @param array     $config
     * @param array     $POST
    **/
    public static function actualizarAsignatura (
        mysqli $conexion,
        array  $config, 
        array  $POST
    )
    {
        if(empty($POST["porcenAsigna"])) {$POST["porcenAsigna"] = '';}
        $POST["sumarPromedio"] = !empty($POST["sumarPromedio"]) ? $POST["sumarPromedio"] : SI;

        $sql = "UPDATE ".BD_ACADEMICA.".academico_materias SET mat_codigo=?, mat_nombre=?, mat_siglas=?, mat_area=?, mat_oficial=?, mat_valor=?, mat_sumar_promedio=? WHERE mat_id=? AND institucion=? AND year=?";

        $parametros = [$POST["codigoM"], $POST["nombreM"], $POST["siglasM"], $POST["areaM"], 1, $POST["porcenAsigna"], $POST["sumarPromedio"], $POST["idM"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina una asignatura
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idMateria
    **/
    public static function eliminarAsignatura (
        mysqli $conexion,
        array  $config, 
        string $idMateria
    )
    {
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_materias WHERE mat_id=? AND institucion=? AND year=?";

        $parametros = [$idMateria, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina todas las asignaturas de una institucion
     * @param string $idInstitucion
     * @param string $yearBd
    **/
    public static function eliminarTodasAsignaturas (
        string  $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_materias WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
        /**
     * Lista  las materias relacionadas a los cursos y grupos selecionados tenienedo en cuenta las cargas.
     * 
     * @param array $cursos - un array que tiene los cursos selecionados
     * @param array $grupos - un array que tiene los grupos selecionados
     *
     * @return array - Un conjunto de resultados (`array`)
     *
     */
    public static function listarMateriasCursosGrups($cursos, $grupos)
    {
        global $config;
        // Preparar los placeholders para la consulta
        $in_cursos = implode(', ', array_fill(0, count($cursos), '?'));
        $in_grupos = implode(', ', array_fill(0, count($grupos), '?'));
        $sql = "SELECT DISTINCT car_materia,
                mat_id,
                mat_nombre,
                mat_siglas 
                FROM " . BD_ACADEMICA . ".academico_cargas car
               INNER JOIN " . BD_ACADEMICA . ".academico_materias mate ON
               (
                    mate.institucion=car.institucion
                    AND mate.year=car.year
                    AND mate.mat_id=car_materia
                )
                WHERE car.institucion=? 
                AND car.year=?
                AND car_curso IN($in_cursos)
                AND car_grupo IN($in_grupos)
                AND car_activa=1
                ORDER BY mat_nombre";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"], $cursos, $grupos];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = BindSQL::resultadoArray($resultado);
        return $resultado;
    }

    /**
     * Lista  las materias relacionadas a los cursos y grupos selecionados tenienedo en cuenta las cargas.
     * 
     * @param string $idMateria - un String de el id de la materia.
     *
     * @return array - Un conjunto de resultados (`array`)
     *
     */
    public static function consultarMateria($idMateria)
    {
        global $config;
        $sql = "SELECT 
                mat_id,
                mat_nombre,
                mat_siglas 
                FROM " . BD_ACADEMICA . ".academico_materias mate
                 WHERE mate.institucion=? 
                AND mate.year=?
                AND mat_id =?
                LIMIT 1";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"], $idMateria];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = BindSQL::resultadoArray($resultado)[0];
        return $resultado;
    }
}