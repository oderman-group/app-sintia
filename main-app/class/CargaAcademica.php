<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
class CargaAcademica {

    /**
     * Valida la existencia de una carga académica para un docente en un curso, grupo y asignatura específicos.
     *
     * @param string $docente El identificador del docente.
     * @param string $curso El nivel o curso académico.
     * @param string $grupo El grupo al que pertenece la carga académica.
     * @param string $asignatura El identificador de la asignatura o materia.
     *
     * @return bool Devuelve `true` si existe una carga académica que cumple con los parámetros proporcionados, de lo contrario, devuelve `false`.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $docenteEjemplo = "ID_DOCENTE";
     * $cursoEjemplo = "10";
     * $grupoEjemplo = "A";
     * $asignaturaEjemplo = "ID_ASIGNATURA";
     * $existeCarga = validarExistenciaCarga($docenteEjemplo, $cursoEjemplo, $grupoEjemplo, $asignaturaEjemplo);
     * if ($existeCarga) {
     *     echo "La carga académica existe para el docente en el curso, grupo y asignatura especificados.\n";
     * } else {
     *     echo "No existe carga académica para el docente en el curso, grupo y asignatura especificados.\n";
     * }
     * ```
     */
    public static function validarExistenciaCarga($docente, $curso, $grupo, $asignatura)
    {

        global $conexion, $config;
        $result = false;

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas WHERE car_docente=? AND car_curso=? AND car_grupo=? AND car_materia=? AND institucion=? AND year=?";

        $parametros = [$docente, $curso, $grupo, $asignatura, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($consulta);
        if($num > 0) {
            $result = true;
        }

        return $result;

    }

    /**
     * Este función consulta los datos de la carga académica actual y
     * los almacena en sesion
     * 
     * @param string $carga
     * @param string $sesion
     * 
     * @return array
     */
    public static function cargasDatosEnSesion(string $carga, string $sesion): array 
    {
        global $conexion, $filtroMT, $config;

        $infoCargaActual = [];
        $sql = "SELECT car.*, am.*, gra.*, gru.*, car.id_nuevo AS id_nuevo_carga FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year {$filtroMT}
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        WHERE car_id=? AND car_docente=? AND car_activa=1 AND car.institucion=? AND car.year=?";

        $parametros = [$carga, $sesion, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
		$datosCargaActual = mysqli_fetch_array($resultado, MYSQLI_BOTH);
		$infoCargaActual = [
			'datosCargaActual'  => $datosCargaActual
		];

        return $infoCargaActual;
    }

    /**
     * Validar Permiso para Acceso a Períodos Diferentes
     *
     * Esta función se utiliza para determinar si un usuario tiene permiso para acceder
     * a un período de carga diferente en una aplicación o sistema. Verifica si el usuario
     * tiene los derechos necesarios para acceder a un período diferente en función de ciertas
     * condiciones.
     *
     * @param Array $datosCargaActual Un array de datos que contiene información sobre la carga actual.
     * @param Int $periodoConsultaActual El período al que el usuario intenta acceder.
     *
     * @return bool Devuelve `true` si el usuario tiene permiso para acceder al período especificado,
     *              y `false` en caso contrario.
     */
    public static function validarPermisoPeriodosDiferentes(Array $datosCargaActual, Int $periodoConsultaActual): bool 
    {

        if(
            $periodoConsultaActual <= $datosCargaActual['gra_periodos'] 
            && ($periodoConsultaActual == $datosCargaActual['car_periodo'] || $datosCargaActual['car_permiso2'] == PERMISO_EDICION_PERIODOS_DIFERENTES)
        ) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Validar Acción para Agregar Calificaciones
     *
     * Esta función se utiliza para validar si se puede realizar la acción de agregar calificaciones
     * en un sistema o aplicación. Comprueba si se cumplen ciertas condiciones, como la configuración
     * de calificaciones, el valor de calificación a agregar y el permiso para acceder a períodos diferentes.
     *
     * @param Array $datosCargaActual Un array de datos que contiene información sobre la carga actual.
     * @param Array $valores Un array que contiene valores relacionados con la acción de agregar calificaciones.
     * @param Int $periodoConsultaActual El período al que el usuario intenta acceder.
     * @param Float $porcentajeRestante El porcentaje restante de calificaciones disponibles.
     *
     * @return bool Devuelve `true` si se permite la acción de agregar calificaciones,
     *              y `false` en caso contrario.
     */
    public static function validarAccionAgregarCalificaciones(
        Array $datosCargaActual, 
        Array $valores, 
        Int $periodoConsultaActual,
        Float $porcentajeRestante
    ): bool {

        if(
            (
                (
                    $datosCargaActual['car_configuracion'] == CONFIG_AUTOMATICO_CALIFICACIONES 
                    && $valores[1] < $datosCargaActual['car_maximas_calificaciones'] 
                )
                || 
                ( $datosCargaActual['car_configuracion'] == CONFIG_MANUAL_CALIFICACIONES 
                && $valores[1] < $datosCargaActual['car_maximas_calificaciones'] 
                && $porcentajeRestante > 0 )
            )

            && self::validarPermisoPeriodosDiferentes($datosCargaActual, $periodoConsultaActual)
        ) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Obtiene datos relacionados con una carga académica a partir de su ID.
     *
     * Esta función realiza una consulta a la base de datos para obtener información relacionada con una carga académica, como el grado, grupo, materia y docente asociados.
     *
     * @param string $idCarga - El ID de la carga académica que se desea consultar.
     *
     * @return array - Un array asociativo con los datos relacionados o un array vacío si no se encuentran datos.
     */
    public static function datosRelacionadosCarga($idCarga)
    {

        global $conexion, $config;
        $result = [];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
        LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        LEFT JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car_id=? AND car.institucion=? AND car.year=?";

        $parametros = [$idCarga, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $result = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $result;

    }

    /**
     * Verifica el acceso de un estudiante a una carga académica.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $idCarga Identificador de la carga académica.
     * @param string $idEstudiante Identificador del estudiante.
     *
     * @return mysqli_result Devuelve el objeto de la consulta preparada o false en caso de error.
     */
    public static function accesoCargasEstudiante(
        mysqli $conexion, 
        array $config, 
        string $idCarga, 
        string $idEstudiante
    ) {
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_cargas_acceso WHERE carpa_id_carga=? AND carpa_id_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Guardar el acceso de un estudiante a una carga académica.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param PDO $conexionPDO Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $idCarga Identificador de la carga académica.
     * @param string $idEstudiante Identificador del estudiante.
     *
     */
    public static function guardarAccesoCargasEstudiante(
        mysqli $conexion, 
        PDO $conexionPDO, 
        array $config, 
        string $idCarga, 
        string $idEstudiante
    ){
        $idInsercion = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_cargas_acceso');

        $sql = "INSERT INTO " . BD_ACADEMICA . ".academico_cargas_acceso (carpa_id, carpa_id_carga, carpa_id_estudiante, carpa_primer_acceso, carpa_ultimo_acceso, carpa_cantidad, institucion, year) VALUES (?, ?, ?, NOW(), NOW(), 1, ?, ?)";

        $parametros = [$idInsercion, $idCarga, $idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Actualizar el acceso de un estudiante a una carga académica.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $idCarga Identificador de la carga académica.
     * @param string $idEstudiante Identificador del estudiante.
     *
     */
    public static function actualizarAccesoCargasEstudiante(
        mysqli $conexion, 
        array $config, 
        string $idCarga, 
        string $idEstudiante
    ){
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_cargas_acceso SET carpa_ultimo_acceso=NOW(), carpa_cantidad=carpa_cantidad+1 WHERE carpa_id_carga=? AND carpa_id_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Traer horarios de una carga académica.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $idCarga Identificador de la carga académica.
     *
     */
    public static function traerHorariosCargas(
        mysqli $conexion, 
        array $config, 
        string $idCarga
    ){
        $sql = "SELECT * FROM " . BD_ACADEMICA . ".academico_horarios WHERE hor_id_carga=? AND hor_estado=1 AND institucion=? AND year=?";

        $parametros = [base64_decode($idCarga), $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    /**
     * Guardar horarios de una carga académica.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $dia Identificador de la carga académica.
     * @param array $POST Configuraciones de la aplicación.
     *
     */
    public static function guardarHorariosCargas(
        mysqli $conexion, 
        PDO $conexionPDO, 
        array $config, 
        string $dia, 
        array $POST
    ){
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_horarios');

        $sql = "INSERT INTO " . BD_ACADEMICA . ".academico_horarios(hor_id, hor_id_carga, hor_dia, hor_desde, hor_hasta, institucion, year) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $POST["idH"], $dia, $POST["inicioH"], $POST["finH"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        return $codigo;
    }
    
    /**
     * Traer datos de un horario.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $idHorario Identificador del horario.
     *
     * @return mysqli_result|false Devuelve el resultado de la consulta o false en caso de error.
     */
    public static function traerDatosHorarios(
        mysqli $conexion, 
        array $config, 
        string $idHorario
    ){
        $sql = "SELECT hor_id_carga, hor_dia, hor_desde, hor_hasta FROM " . BD_ACADEMICA . ".academico_horarios WHERE hor_id=? AND institucion=? AND year=?";

        $parametros = [base64_decode($idHorario), $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        return $resultado;
    }
    
    /**
     * Actualizar horarios de una carga académica.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param array $POST Configuraciones de la aplicación.
     *
     */
    public static function actualizarHorariosCargas(
        mysqli $conexion, 
        array $config, 
        array $POST
    ){
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_horarios SET hor_dia=?, hor_desde=?, hor_hasta=? WHERE hor_id=? AND institucion=? AND year=?";

        $parametros = [$POST["diaH"], $POST["inicioH"], $POST["finH"], $POST["idH"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Eliminar un horario.
     *
     * @param mysqli $conexion Objeto de conexión a la base de datos.
     * @param array $config Configuraciones de la aplicación.
     * @param string $idHorario Identificador del horario.
     *
     */
    public static function eliminarHorarios(
        mysqli $conexion, 
        array $config, 
        string $idHorario
    ){
        $sql = "UPDATE " . BD_ACADEMICA . ".academico_horarios SET hor_estado=0 WHERE hor_id=? AND institucion=? AND year=?";

        $parametros = [base64_decode($idHorario), $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Listar todas las cargas.
     *
     * @param mysqli $conexion
     * @param array $config
     * @param string $filtroMT
     * @param string $filtro
     * @param string $order
     * @param string $limit
     * @param string $valueIlike - Valor String que se utilizara para biuscar por cualuqier parametro definido (puede ser nulo).
     *
     */
    public static function listarCargas(
        mysqli $conexion, 
        array $config, 
        string $filtroMT = "", 
        string $filtro = "", 
        string $order = "car_id", 
        string $limit = "LIMIT 0, 2000",
        string $valueIlike = "",
        array  $filtro2 = array()  

    ){
        if(!empty($valueIlike)){
            $busqueda=$valueIlike;
            $filtro .= " AND (
                 car_id LIKE '%" . $busqueda . "%' 
                OR uss_nombre LIKE '%".$busqueda."%' 
                OR uss_nombre2 LIKE '%".$busqueda."%' 
                OR uss_apellido1 LIKE '%".$busqueda."%' 
                OR uss_apellido2 LIKE '%".$busqueda."%' 
                OR gra_nombre LIKE '%" . $busqueda . "%' 
                OR mat_nombre LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss_nombre), ' ',TRIM(uss_apellido1), ' ', TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1), TRIM(uss_apellido2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), ' ', TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(uss_nombre), TRIM(uss_apellido1)) LIKE '%".$busqueda."%'
            )";
        }
        if(!empty($filtro2)){
            if(!empty($filtro2['periodoSeleccionados'])){
                $arrayPeriodos=$filtro2['periodoSeleccionados'];
                $periodos = implode(", ", $arrayPeriodos);
                $filtro .= " AND car_periodo IN ({$periodos})";
            }
        }
        try {
            $sql="SELECT car.*, am.*, gra.*, gru.*, uss.*, car.id_nuevo AS id_nuevo_carga FROM ".BD_ACADEMICA.".academico_cargas car
            INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year {$filtroMT}
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
            LEFT JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
            WHERE car.institucion=? AND car.year=? {$filtro}
            ORDER BY {$order}
            {$limit};";
    
            $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
            
            $consulta = BindSQL::prepararSQL($sql, $parametros);
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }
        return $consulta;
    }
    
    /**
     * Este metodo verifica si el estudiante está matriculado en cursos de extensión o complementarios
     * @param mysqli $conexion
     * @param array $config
     * @param string $idEstudiante
     * @param string $idCarga
     * 
     * @return int $num
     */
    public static function validarCursosComplementario(mysqli $conexion, array $config, string $idEstudiante, string $idCarga){
        $num=0;
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas_estudiantes WHERE carpest_carga=? AND carpest_estudiante=? AND carpest_estado=1 AND institucion=? AND year=?";

        $parametros = [$idCarga, $idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $num = mysqli_num_rows($resultado);

        return $num;
    }

    /**
     * Este metodo me elimina un tipo de nota
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idTipoNota
    **/
    public static function eliminarTiposNotas (
        mysqli  $conexion, 
        array   $config, 
        string  $idTipoNota
    )
    {
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_id=? AND institucion=? AND year=?";

        $parametros = [$idTipoNota, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina todos los tipos de nota de una categoria
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idCategoria
    **/
    public static function eliminarTiposNotasCategoria (
        mysqli  $conexion, 
        array   $config, 
        string  $idCategoria
    )
    {
        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria=? AND institucion=? AND year=?";

        $parametros = [$idCategoria, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me guarda un tipo de nota
     * @param mysqli    $conexion
     * @param PDO       $conexionPDO
     * @param array     $config
     * @param array     $POST
    **/
    public static function guardarTipoNota (
        mysqli $conexion,
        PDO    $conexionPDO, 
        array  $config, 
        array  $POST
    )
    {
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_notas_tipos');

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_notas_tipos (notip_id, notip_nombre, notip_desde, notip_hasta,notip_categoria, institucion, year)VALUES(?, ?, ?, ?, ?, ?, ?)";

        $parametros = [$codigo, $POST["nombreCN"], $POST["ndesdeCN"], $POST["nhastaCN"], $POST["idCN"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me actualiza un tipo de nota
     * @param mysqli    $conexion
     * @param array     $config
     * @param array     $POST
    **/
    public static function actualizarTipoNota (
        mysqli $conexion,
        array  $config, 
        array  $POST
    )
    {

        $sql = "UPDATE ".BD_ACADEMICA.".academico_notas_tipos SET notip_nombre=?, notip_desde=?, notip_hasta=? WHERE notip_id=? AND institucion=? AND year=?";

        $parametros = [$POST["nombreCN"], $POST["ndesdeCN"], $POST["nhastaCN"], $POST["idN"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me trae los datos de un tipo de nota
     * @param mysqli    $conexion
     * @param array     $config
     * @param string    $idTipoNota
     * 
     * @return array    $resultado
    **/
    public static function traerDatosNotasTipo (
        mysqli  $conexion, 
        array   $config,  
        string  $idTipoNota
    )
    {
        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_id=? AND institucion=? AND year=?";

        $parametros = [$idTipoNota, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me elimina todos los tipos de nota
     * @param string $idInstitucion
     * @param string $yearBd
    **/
    public static function eliminarTodosTiposNota (
        string  $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Trae los datos relacionados de una carga académica para un estudiante.
     *
     * Este método realiza una consulta SQL para obtener los datos relacionados de una carga académica
     * para un estudiante específico. Utiliza las tablas academico_cargas, academico_materias,
     * academico_matriculas, usuarios y academico_grados.
     *
     * @param int     $idInstitucion  Identificador de la institución cuyas cargas se eliminarán.
     * @param string  $idEstudiante   ID del estudiante.
     * @param string  $idCarga        ID de la carga académica.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return array|null             Los datos relacionados de la carga académica para el estudiante, o null si no se encontraron.
     */
    public static function traerDatosRelacionadosCargaEstudiante (
        int     $idInstitucion,
        string  $idEstudiante,
        string  $idCarga,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias AS mate ON mate.mat_id=car_materia AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_matriculas AS matri ON matri.mat_id=? AND matri.institucion=car.institucion AND matri.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat_acudiente AND uss.institucion=car.institucion AND uss.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados AS gra ON gra.gra_id=matri.mat_grado AND gra.institucion=car.institucion AND gra.year=car.year
        WHERE car_id=? AND car.institucion=? AND car.year=?";

        $parametros = [$idEstudiante, $idCarga, $idInstitucion, $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Trae la información de una carga académica y su materia asociada mediante su ID.
     *
     * @param array   $config     Configuración del sistema.
     * @param string  $idCarga    Identificador de la carga académica.
     * @param string  $yearBd     Año académico (opcional).
     *
     * @return array|null         Los resultados de la consulta como un array asociativo o null si no se encuentra la carga.
     */
    public static function traerCargaMateriaPorID (
        array   $config,
        string  $idCarga,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
		INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car.car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_responsable AND uss.institucion=car.institucion AND uss.year=car.year
		WHERE car_id=? AND car.institucion=? AND car.year=?";

        $parametros = [$idCarga, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Trae latodas lacargas de un docente.
     *
     * @param array   $config     Configuración del sistema.
     * @param string  $idDocente  Identificador del docente.
     * @param string  $yearBd     Año académico (opcional).
     */
    public static function traerCargasDocentes (
        array   $config,
        string  $idDocente,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
		INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car.car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
		WHERE  car_docente=? AND car.institucion=? AND car.year=?
        ORDER BY CAST(car_posicion_docente AS SIGNED), car_curso, car_grupo, am.mat_nombre";

        $parametros = [$idDocente, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae las cargas académicas y las materias correspondientes para un curso y grupo específicos.
     *
     * Este método realiza una consulta SQL para obtener las cargas académicas y las materias asociadas
     * a un curso y grupo específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idGrupo        ID del grupo.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function traerCargasMateriasPorCursoGrupo (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $yearBd = "",
        string  $filtroOr = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car.institucion=? AND car.year=? AND (car_curso=? AND car_grupo=? {$filtroOr})
        ORDER BY mat_id";

        $parametros = [$config['conf_id_institucion'], $year, $idCurso, $idGrupo];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae las cargas académicas y las materias correspondientes para un curso, grupo y materia específicos.
     *
     * Este método realiza una consulta SQL para obtener las cargas académicas y las materias asociadas
     * a un curso y grupo específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idGrupo        ID del grupo.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function traerCargasMateriasPorCursoGrupoMateria (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $idMateria,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car.institucion=? AND car.year=? AND car_curso=? AND car_grupo=? AND car_materia=?";

        $parametros = [$config['conf_id_institucion'], $year, $idCurso, $idGrupo, $idMateria];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Trae las cargas académicas y las materias correspondientes para un curso y grupo específicos.
     *
     * Este método realiza una consulta SQL para obtener las cargas académicas y las materias asociadas
     * a un curso y grupo específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idGrupo        ID del grupo.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function traerCargasAreasPorCursoGrupo (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $idArea,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.mat_area=?  AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=?";

        $parametros = [$idArea, $idCurso, $idGrupo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae las areas académicas y las materias correspondientes para un curso y grupo específicos.
     *
     * Este método realiza una consulta SQL para obtener las areas académicas y las materias asociadas
     * a un curso y grupo específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idGrupo        ID del grupo.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function traerCargasMateriasAreaPorCursoGrupo (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id= mate.mat_area AND ar.institucion=car.institucion AND ar.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=?
        GROUP BY ar.ar_id 
        ORDER BY ar.ar_posicion ASC";

        $parametros = [$idCurso, $idGrupo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * calcula el promedio de un areas académicas para un curso y grupo específicos.
     *
     * Este método realiza una consulta SQL para obtener las areas académicas y las materias asociadas
     * a un curso y grupo específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idGrupo        ID del grupo.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function calcularPromedioAreaPorCursoGrupo (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $idArea,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.mat_area=? AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id= am.mat_area AND ar.institucion=car.institucion AND ar.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=?
        GROUP BY ar.ar_id 
        ORDER BY ar.ar_posicion ASC";

        $parametros = [$idArea, $idCurso, $idGrupo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae las cargas académicas y las materias correspondientes para un curso y materia específicos.
     *
     * Este método realiza una consulta SQL para obtener las cargas académicas y las materias asociadas
     * a un curso y materia específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idMateria      ID del materia.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function traerCargasMateriasPorCursoMateria (
        array   $config,
        string  $idCurso,
        string  $idMateria,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.institucion=car.institucion AND mate.year=car.year
        WHERE car_curso=? AND car_materia=? AND car.institucion=? AND car.year=?";

        $parametros = [$idCurso, $idMateria, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae los indicadores de una cargas académicas correspondientes para un curso y grupo específicos.
     *
     * Este método realiza una consulta SQL para obtener los indicadores de una cargas académicas asociadas
     * a un curso y grupo específicos. Utiliza las tablas academico_cargas y academico_materias.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idGrupo        ID del grupo.
     * @param int     $periodo       
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return mysqli_result
     */
    public static function traerIndicadoresCargasPorCursoGrupo (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        int     $periodo,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON ai.ind_carga=car_id AND ai.ind_periodo=? AND ai.ind_tematica=1 AND ai.institucion=car.institucion AND ai.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=?";

        $parametros = [$periodo, $idCurso, $idGrupo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Trae la carga académica para un director de grupo en un curso específico.
     *
     * @param array   $config     Configuración del sistema.
     * @param string  $idCurso    Identificador del curso.
     * @param string  $yearBd     Año académico (opcional).
     *
     * @return array|null         Los resultados de la consulta como un array asociativo o null si no se encuentra la carga.
     */
    public static function traerCargaDirectorGrupo (
        array   $config,
        string  $idCurso,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas WHERE car_curso=? AND car_director_grupo=1 AND car.institucion=? AND car.year=?";

        $parametros = [$idCurso, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta preparada para insertar un nuevo registro de carga en la tabla 'academico_cargas'.
    *
    * @param PDO    $conexionPDO  Conexión PDO a la base de datos.
    * @param string $insert       Lista de campos separados por coma para la inserción.
    * @param array  $parametros   Array de parámetros para la consulta preparada.
    * @return string              Código único generado para el nuevo registro de carga.
    **/
    public static function guardarCarga (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_cargas');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_cargas ({$insert}) VALUES ({$signosPreguntas})";

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $codigo;
    }

    /**
     * Actualiza las cargas académicas por ID específicos.
     *
     * Este método ejecuta una consulta SQL para actualizar una carga académica
     * específica en la base de datos. Utiliza la tabla academico_cargas.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCarga        ID de la carga.
     * @param string  $update         Cadena con las actualizaciones a realizar en formato SQL.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return void                   No devuelve ningún valor.
     */
    public static function actualizarCargaPorID (
        array   $config,
        string  $idCarga,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_cargas SET {$updateSql} WHERE car_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idCarga, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Actualiza las cargas académicas por curso y materia específicos.
     *
     * Este método ejecuta una consulta SQL para actualizar las cargas académicas de un curso
     * y materia específicos en la base de datos. Utiliza la tabla academico_cargas.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCurso        ID del curso.
     * @param string  $idMateria      ID de la materia.
     * @param string  $update         Cadena con las actualizaciones a realizar en formato SQL.
     * @param string  $yearBd         Año para la base de datos (opcional).
     * @return void                   No devuelve ningún valor.
     */
    public static function actualizarCargaPorCursoMateria (
        array   $config,
        string  $idCurso,
        string  $idMateria,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_cargas SET {$updateSql} WHERE car_curso=? AND car_materia=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idCurso, $idMateria, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }
    
    /**
     * Consulta la posición de una carga académica en relación con un docente.
     *
     * @param array   $config     Configuración del sistema.
     * @param string  $docente    Identificador del docente.
     * @param string  $idCarga    Identificador de la carga académica.
     * @param int     $posicion   Posición inicial para la consulta.
     * @param mixed   $filtroMT   Filtro adicional para la consulta (opcional).
     * @param string  $yearBd     Año académico (opcional).
     *
     * @return array|null         Los resultados de la consulta como un array asociativo o null si no se encuentra ninguna carga.
     */
    public static function consultarPosicionCarga (
        array   $config,
        string  $docente,
        string  $idCarga,
        int     $posicion,
        $filtroMT = "",
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id, car_posicion_docente FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year {$filtroMT}
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        WHERE car_id<>? AND car_posicion_docente>=? AND car_docente=?  AND car.institucion=? AND car.year=?
        ORDER BY CAST(car_posicion_docente AS SIGNED)";

        $parametros = [$idCarga, $posicion, $docente, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Elimina las cargas académicas por ID específicos.
     *
     * Este método ejecuta una consulta SQL para eliminar una carga académica
     * específica en la base de datos. Utiliza la tabla academico_cargas.
     *
     * @param array   $config         Configuración de la aplicación.
     * @param string  $idCarga        ID de la carga.
     * @param string  $yearBd         Año para la base de datos (opcional).
     */
    public static function eliminarCargaPorID (
        array   $config,
        string  $idCarga,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_cargas WHERE car_id=? AND institucion=? AND year=?";

        $parametros = [$idCarga, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar todos los registros de cargas
    * pertenecientes a una institución para un año específico de la base de datos.
    *
    * @param int    $idInstitucion Identificador de la institución cuyas cargas se eliminarán.
    * @param string $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    * @return void
    **/
    public static function eliminarCargasInstitucion (
        int     $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_cargas WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Consulta los indicadores perdidos por un estudiante en un período y curso específicos.
     *
     * @param array   $config        Configuración del sistema.
     * @param string  $idEstudiante Identificador del estudiante.
     * @param int     $periodo      Número del período académico.
     * @param string  $idCurso      Identificador del curso.
     * @param string  $idGrupo      Identificador del grupo.
     * @param string  $periodos     Lista de períodos académicos.
     * @param string  $yearBd       Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaIndicadoresPerdidos (
        array   $config,
        string  $idEstudiante,
        int     $periodo,
        string  $idCurso,
        string  $idGrupo,
        string  $periodos,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT am.mat_id, am.mat_nombre, ar.ar_id, ar.ar_nombre, car.car_id, ind.ind_nombre, aic.ipc_periodo, ROUND(SUM(aac.cal_nota * (aa.act_valor / 100)) / SUM(aa.act_valor / 100), 2) AS nota, ROUND(rind_nota, 2) AS rind_nota, ind.ind_id
        FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id = car.car_materia AND am.institucion = car.institucion  AND am.year = car.year
        INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id = am.mat_area AND ar.institucion = car.institucion  AND ar.year = car.year
        INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol.bol_carga = car.car_id AND bol.institucion = car.institucion  AND bol.year = car.year
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga aic ON aic.ipc_carga = car.car_id AND aic.institucion = car.institucion  AND aic.year = car.year
        INNER JOIN ".BD_ACADEMICA.".academico_indicadores ind ON ind.ind_id = aic.ipc_indicador AND ind.institucion = car.institucion  AND ind.year = car.year
        INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id_tipo = aic.ipc_indicador AND aa.act_id_carga = car.car_id AND aa.act_estado = 1 AND aa.act_registrada = 1 AND aa.institucion = car.institucion  AND aa.year = car.year
        INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad = aa.act_id AND aac.institucion = car.institucion  AND aac.year = car.year
        LEFT JOIN ".BD_ACADEMICA.".academico_indicadores_recuperacion rec ON rind_estudiante=? AND rind_carga=car.car_id AND rind_periodo=? AND rind_indicador=ind_id AND rec.institucion=car.institucion AND rec.year=car.year
        WHERE car.car_curso=? AND car.car_grupo=? AND car.institucion=?  AND car.year=? AND bol.bol_estudiante=? AND bol.bol_periodo IN (" . $periodos . ") AND aac.cal_id_estudiante=? AND aa.act_periodo=?
        GROUP BY ar.ar_id, am.mat_id, ind.ind_id
        HAVING nota < ? AND (rind_nota IS NULL OR (rind_nota < nota AND rind_nota < ?))
        ORDER BY ar.ar_posicion ASC";

        $parametros = [$idEstudiante, $periodo, $idCurso, $idGrupo, $config['conf_id_institucion'], $year, $idEstudiante, $idEstudiante, $periodo, $config['conf_nota_minima_aprobar'], $config['conf_nota_minima_aprobar']];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Consulta las cargas repetidas de una institución.
     *
     * @param array   $config        Configuración del sistema.
     * @param string  $yearBd       Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaCargasRepetidas (
        array   $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT GROUP_CONCAT( car_id SEPARATOR ', ')as car_id, uss_nombre, gra_nombre, gru_nombre, mat_nombre, COUNT(*) as duplicados
        FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        WHERE car.institucion=? AND car.year=?
        GROUP BY car_docente, car_curso, car_grupo, car_materia
        HAVING COUNT(*) > 1 
        ORDER BY car_id ASC";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Consulta los datos relacionados con las cargas académicas de un curso y grupo específicos.
     *
     * @param array   $config     Configuración del sistema.
     * @param string  $idCurso    Identificador del curso.
     * @param string  $idGrupo    Identificador del grupo.
     * @param string  $filtroOR   Filtro adicional opcional en formato SQL para la cláusula WHERE (por defecto, vacío).
     * @param string  $yearBd     Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function datosRelacionadosCargaPorCursoGrupo (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $filtroOR = "",
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=? {$filtroOR}";

        $parametros = [$idCurso, $idGrupo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Consulta las materias de un estudiante en un periodo específico.
     *
     * @param array   $config         Configuración del sistema.
     * @param int     $periodo        Número del periodo.
     * @param string  $idEstudiante   Identificador del estudiante.
     * @param string  $idCurso        Identificador del curso.
     * @param string  $idGrupo        Identificador del grupo.
     * @param string  $idArea         Identificador del área.
     * @param string  $yearBd         Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaMaterias (
        array   $config,
        int     $periodo,
        string  $idEstudiante,
        string  $idCurso,
        string  $idGrupo,
        string  $idArea,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id, car_ih, car_materia, car_docente, car_director_grupo,
        mat_nombre, mat_area, mat_valor,
        ar_nombre, ar_posicion
        bol_estudiante, bol_periodo, bol_nota,
        bol_nota * (mat_valor/100) AS notaArea
        FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id = car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id = am.mat_area AND a.institucion=car.institucion AND a.year=car.year
        LEFT JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol_carga=car_id AND bol_periodo =? AND bol_estudiante = ? AND bol.institucion=car.institucion AND bol.year=car.year
        WHERE car_curso = ? AND car_grupo = ? AND car.institucion=? AND car.year=? AND am.mat_area = ?";

        $parametros = [$periodo, $idEstudiante, $idCurso, $idGrupo, $config['conf_id_institucion'], $year, $idArea];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Consulta las materias agrupadas por su area de un estudiante en un periodo específico.
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $idCurso        Identificador del curso.
     * @param string  $idGrupo        Identificador del grupo.
     * @param string  $yearBd         Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaMateriasAreas (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_valor, mat_nombre, mat_area
        FROM ".BD_ACADEMICA.".academico_cargas ac
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON mat_id=car_materia AND am.institucion=ac.institucion AND am.year=ac.year
        WHERE ac.institucion=? AND ac.year=? AND car_curso=? AND car_grupo=?
        AND mat_area IN (
            SELECT mat_area
            FROM ".BD_ACADEMICA.".academico_materias
            WHERE institucion=ac.institucion AND year=ac.year
            GROUP BY mat_area
            HAVING COUNT(DISTINCT mat_id) > 1
        )
        ORDER BY mat_area, mat_valor";

        $parametros = [$config['conf_id_institucion'], $year, $idCurso, $idGrupo];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Consulta las areas de un estudiante en un periodo específico.
     *
     * @param array   $config         Configuración del sistema.
     * @param int     $periodo        Número del periodo.
     * @param string  $idEstudiante   Identificador del estudiante.
     * @param string  $idArea         Identificador del área.
     * @param string  $yearBd         Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaAreasPeriodos (
        array   $config,
        int     $periodo,
        string  $idEstudiante,
        string  $idArea,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_valor,
        bol_estudiante, bol_periodo, bol_nota,
        SUM(bol_nota * (mat_valor/100)) AS notaArea
        FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id = car_materia AND mat_sumar_promedio='SI' AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol_carga=car_id AND bol_periodo=? AND bol_estudiante = ? AND bol.institucion=car.institucion AND bol.year=car.year
        WHERE am.mat_area = ? AND car.institucion=? AND car.year=?
        GROUP BY am.mat_area";

        $parametros = [$periodo, $idEstudiante, $idArea, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Consulta el progreso de los docentes
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $idDocente   Identificador del docente.
     * @param string  $yearBd         Año académico (opcional).
     */
    public static function consultaProgresoDocentes (
        array   $config,
        string  $idDocente,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT
        (SELECT count(car_id) FROM ".BD_ACADEMICA.".academico_cargas WHERE car_docente=? AND car_periodo=? AND institucion=? AND year=?),
        (SELECT sum(act_valor) FROM ".BD_ACADEMICA.".academico_actividades aa INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_id=aa.act_id_carga AND car_periodo=? AND car_docente=? AND car.institucion=? AND car.year=? WHERE aa.act_estado=1 AND aa.act_periodo=? AND aa.institucion=? AND aa.year=?),
        (SELECT sum(act_valor) FROM ".BD_ACADEMICA.".academico_actividades aa INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_id=aa.act_id_carga AND car_periodo=? AND car_docente=? AND car.institucion=? AND car.year=? WHERE aa.act_estado=1 AND aa.act_periodo=? AND aa.act_registrada=1 AND aa.institucion=? AND aa.year=?)";

        $parametros = [$idDocente, $config['conf_periodo'], $config['conf_id_institucion'], $year, $config['conf_periodo'], $idDocente, $config['conf_id_institucion'], $year, $config['conf_periodo'], $config['conf_id_institucion'], $year, $config['conf_periodo'], $idDocente, $config['conf_id_institucion'], $year, $config['conf_periodo'], $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * cuenta cuantas cargas tiene una materia
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $idMateria   Identificador de la materia.
     * @param string  $yearBd         Año académico (opcional).
     */
    public static function contarCargasMaterias (
        array   $config,
        string  $idMateria,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT COUNT(car_id) FROM ".BD_ACADEMICA.".academico_cargas WHERE car_materia=? AND institucion=? AND year=?";

        $parametros = [$idMateria, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * cuenta cuantas cargas tiene un docente
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $idDocente      Identificador del docente
     * @param string  $yearBd         Año académico (opcional).
     */
    public static function contarCargasDocente (
        array   $config,
        string  $idDocente,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT COUNT(car_id) FROM ".BD_ACADEMICA.".academico_cargas WHERE car_docente=? AND institucion=? AND year=?";

        $parametros = [$idDocente, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * cuenta cuantas cargas tiene un curso
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $idCurso        Identificador del curso
     * @param string  $yearBd         Año académico (opcional).
     */
    public static function contarCargasGrado (
        array   $config,
        string  $idCurso,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT COUNT(car_id) FROM ".BD_ACADEMICA.".academico_cargas WHERE car_curso=? AND institucion=? AND year=?";

        $parametros = [$idCurso, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * cuenta cuantas cargas tiene una institucion
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $yearBd         Año académico (opcional).
     */
    public static function contarCargas (
        array   $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT COUNT(car_id) FROM ".BD_ACADEMICA.".academico_cargas WHERE car_curso=? AND institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
        
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * me consulta las cargas relacionadas a un curso y un docente
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $idDocente   Identificador de la materia.
     * @param string  $yearBd         Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaCargasRelacionadas (
        array   $config,
        string  $idDocente,
        string  $idCurso,
        string  $yearBd = "",
        string  $filtroMT = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year {$filtroMT}
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=car_grupo AND gru.institucion=car.institucion AND gru.year=car.year
        WHERE car.institucion=? AND car.year=? AND (car_docente=? OR car_curso=?)
        ORDER BY car_docente";

        $parametros = [$config['conf_id_institucion'], $year, $idDocente, $idCurso];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Me consulta las cargas que tienen intensidad Horaria
     *
     * @param array   $config         Configuración del sistema.
     * @param string  $yearBd         Año académico (opcional).
     */
    public static function traerCargasConIntensidad (
        array   $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_cargas WHERE car_ih!='' AND institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me trar las cargas que el estudiante lleva perdidas hasta el momento
     * @param array   $config           Configuración del sistema.
     * @param string  $idEstudiante     Identificador del grupo.
     * @param string  $idCurso          Identificador del curso.
     * @param string  $idGrupo          Identificador del grupo.
     * @param string  $filtroOR         Filtro adicional opcional en formato SQL para la cláusula WHERE (por defecto, vacío).
     * @param string  $yearBd           Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaInformeParcialPerdidas (
        array   $config,
        string  $idEstudiante,
        string  $idCurso,
        string  $idGrupo,
        string  $filtroOR = "",
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id, mat_id, mat_nombre, mat_sumar_promedio, SUM(aa.act_valor) AS porcentaje, ROUND(SUM(ac.cal_nota * (aa.act_valor / 100)) / SUM(aa.act_valor / 100), 2) AS nota, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2 FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON act_id_carga=car_id AND act_registrada=1 AND act_estado=1 AND act_periodo=? AND aa.institucion=car.institucion AND aa.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_calificaciones ac ON cal_id_actividad=act_id AND cal_id_estudiante=? AND ac.institucion=car.institucion AND ac.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=? {$filtroOR}
        GROUP BY car_id
        HAVING nota<? AND porcentaje > 0";

        $parametros = [$config['conf_periodo'], $idEstudiante, $idCurso, $idGrupo, $config['conf_id_institucion'], $year, $config['conf_nota_minima_aprobar']];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /** Este metodo me muestra el progreso del estudiante en todas sus materias
     * @param array   $config           Configuración del sistema.
     * @param string  $idEstudiante     Identificador del grupo.
     * @param string  $idCurso          Identificador del curso.
     * @param string  $idGrupo          Identificador del grupo.
     * @param string  $filtroOR         Filtro adicional opcional en formato SQL para la cláusula WHERE (por defecto, vacío).
     * @param string  $yearBd           Año académico (opcional).
     *
     * @return mysqli_result
     */
    public static function consultaInformeParcialTodas (
        array   $config,
        string  $idEstudiante,
        string  $idCurso,
        string  $idGrupo,
        string  $filtroOR = "",
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id, mat_id, mat_nombre, mat_sumar_promedio, SUM(aa.act_valor) AS porcentaje, ROUND(SUM(ac.cal_nota * (aa.act_valor / 100)) / SUM(aa.act_valor / 100), 2) AS nota, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2 FROM ".BD_ACADEMICA.".academico_cargas car 
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=car_curso AND gra.institucion=car.institucion AND gra.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON act_id_carga=car_id AND act_registrada=1 AND act_estado=1 AND act_periodo=? AND aa.institucion=car.institucion AND aa.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_calificaciones ac ON cal_id_actividad=act_id AND cal_id_estudiante=? AND ac.institucion=car.institucion AND ac.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        WHERE car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=? {$filtroOR}
        GROUP BY car_id
        HAVING porcentaje > 0";

        $parametros = [$config['conf_periodo'], $idEstudiante, $idCurso, $idGrupo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    public static function consultaInformeSabanas (
        string  $idEstudiante,
        int     $periodo,
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        string  $yearBd = "",
        string  $filtroOr = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT car_id, bol_nota, notip_nombre FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias mate ON mate.mat_id=car_materia AND mate.institucion=car.institucion AND mate.year=car.year
        INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion=car.institucion AND uss.year=car.year
        LEFT JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol_estudiante=? AND bol_carga=car_id AND bol_periodo=? AND bol.institucion=car.institucion AND bol.year=car.year
        LEFT JOIN ".BD_ACADEMICA.".academico_notas_tipos ntp ON ntp.notip_categoria=? AND bol_nota>=ntp.notip_desde AND bol_nota<=ntp.notip_hasta AND ntp.institucion=bol.institucion AND ntp.year=bol.year
        WHERE car.institucion=? AND car.year=? AND (car_curso=? AND car_grupo=? {$filtroOr})
        ORDER BY mat_id";

        $parametros = [$idEstudiante, $periodo, $config['conf_notas_categoria'], $config['conf_id_institucion'], $year, $idCurso, $idGrupo];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
}