<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once ROOT_PATH."/main-app/class/UsuariosPadre.php";
require_once ROOT_PATH."/main-app/class/Asignaturas.php";
require_once(ROOT_PATH."/main-app/class/App/Academico/boletin/Boletin.php");

class Boletin {

    public const BOLETIN_TIPO_NOTA_NORMAL                 = 1; // Calculada por el sistema de acuerdo a las notas en las actividades.
    public const BOLETIN_TIPO_NOTA_RECUPERACION_PERIODO   = 2; // La hace el docente o el directivo.
    public const BOLETIN_TIPO_NOTA_RECUPERACION_INDICADOR = 3; // La hace el docente por los indicadores.
    public const BOLETIN_TIPO_NOTA_DIRECTIVA              = 4; // La Coloca el directivo directamente.

    public const ESTADO_ABIERTO  = 'ABIERTO';
    public const ESTADO_GENERADO = 'GENERADO';

    public const PORCENTAJE_MINIMO_GENERAR_INFORME = 99;

    public const GENERAR_CON_PORCENTAJE_COMPLETO = 1;
    public const OMITIR_ESTUDIANTES_CON_PORCENTAJE_INCOMPLETO = 2;
    public const GENERAR_CON_CUALQUIER_PORCENTAJE = 3;


    /**
    * Devuelve una lista de tipos de notas basados en la categoría proporcionada y el año académico seleccionado.
    *
    * @param string $categoria La categoría de notas para la cual se desean listar los tipos.
    * @param string $yearBd (Opcional) El año académico para el cual se desean listar los tipos de notas. Si no se proporciona, se utiliza el año académico actual de la sesión.
    *
    * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene la información de los tipos de notas.
    *
    * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
    */
    public static function listarTipoDeNotas($categoria, string $yearBd    = ''){
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria=? AND institucion=? AND year=?";

        $parametros = [$categoria, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Agrega decimales a una nota si es necesario.
     *
     * @param string $nota La nota a la cual se le agregarán decimales.
     *
     * @return string La nota modificada con decimales.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $notaOriginal = "9";
     * $notaConDecimales = agregarDecimales($notaOriginal);
     * echo $notaConDecimales; // Salida: "9.00"
     * ```
     */
    public static function agregarDecimales($nota){
        
    
        if(strlen($nota) === 1 || $nota == 10){
            $nota = $nota.".00";
        }

        $explode = explode(".", $nota);
        $decimales = end($explode);
        if(!empty($decimales) && strlen($decimales) === 1){
            $nota = $nota."0";
        }

        return $nota;
    }

    /**
     * Obtiene los datos asociados a un tipo de notas basados en la categoría y la nota proporcionadas.
     *
     * @param string $categoria La categoría de notas para la cual se desea obtener información.
     * @param string $nota La nota para la cual se desea obtener información.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return array Un array asociativo que contiene los datos del tipo de notas, o un array vacío si no se encuentra ningún tipo de notas que coincida con la categoría y la nota proporcionadas.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $categoriaEjemplo = "categoria_ejemplo";
     * $notaEjemplo = "8";
     * $datosTipoNotas = obtenerDatosTipoDeNotas($categoriaEjemplo, $notaEjemplo, "2023");
     * print_r($datosTipoNotas);
     * ```
     */
    public static function obtenerDatosTipoDeNotas($categoria, $nota, string $yearBd    = ''){
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria=? AND ?>=notip_desde AND ?<=notip_hasta AND institucion=? AND year=?";

        $parametros = [$categoria, $nota, $nota, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }
        /**
     * Obtiene los datos asociados a un tipo de notas basados en la categoría y la nota proporcionadas dependeindo de una lista ya cargada.
     * este metho es para evitar la multiples consultas a la base de datos sino buscar la informacion a una consulta ya cargada
     * @param array  $listaDesemp La categoría de notas para la cual se desea obtener información.
     * @param string $nota La nota para la cual se desea obtener información.
     */
    public static function obtenerDatosTipoDeNotasCargadas($listaDesemp, $nota) {
        $encontrado = null;
        foreach ($listaDesemp as $item) {
            if ($nota >= $item['notip_desde'] && $nota <= $item['notip_hasta']) {
                $encontrado = $item;
                break;  // Detenemos la búsqueda una vez encontrado
            }
        }
        return $encontrado;
    }

    /**
     * Obtiene el puesto y el promedio de estudiantes en un grado y grupo específicos para un periodo académico determinado.
     *
     * @param int    $periodo El periodo académico para el cual se desea obtener el puesto y el promedio.
     * @param string $grado El grado del estudiante.
     * @param string $grupo El grupo al que pertenece el estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener el puesto y el promedio. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene información sobre el puesto y el promedio de los estudiantes.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $periodoEjemplo = 1;
     * $gradoEjemplo = "10";
     * $grupoEjemplo = "A";
     * $resultadosEstudiantes = obtenerPuestoYpromedioEstudiante($periodoEjemplo, $gradoEjemplo, $grupoEjemplo, "2023");
     * while ($estudiante = mysqli_fetch_assoc($resultadosEstudiantes)) {
     *     // Procesar información de cada estudiante
     *     echo "Matrícula: ".$estudiante['mat_id']." - Puesto: ".$estudiante['puesto']." - Promedio: ".$estudiante['prom']."<br>";
     * }
     * ```
     */
    /**
     * Obtiene el puesto y el promedio de estudiantes en un grado y grupo específicos para un periodo académico determinado.
     *
     * @param int    $periodo El periodo académico para el cual se desea obtener el puesto y el promedio.
     * @param string $grado El grado del estudiante.
     * @param string $grupo El grupo al que pertenece el estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener el puesto y el promedio. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene información sobre el puesto y el promedio de los estudiantes.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $periodoEjemplo = 1;
     * $gradoEjemplo = "10";
     * $grupoEjemplo = "A";
     * $resultadosEstudiantes = obtenerPuestoYpromedioEstudiante($periodoEjemplo, $gradoEjemplo, $grupoEjemplo, "2023");
     * while ($estudiante = mysqli_fetch_assoc($resultadosEstudiantes)) {
     *     // Procesar información de cada estudiante
     *     echo "Matrícula: ".$estudiante['mat_id']." - Puesto: ".$estudiante['puesto']." - Promedio: ".$estudiante['prom']."<br>";
     * }
     * ```
     */
    public static function obtenerPuestoYpromedioEstudianteOriginal(
        int    $periodo = 0,
        string $grado   = "",
        string $grupo   = "",
        string $yearBd  = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $consulta = mysqli_query($conexion, "SELECT mat_id as estudiante_id, bol_estudiante, bol_carga, mat_nombres, mat_grado, bol_periodo, AVG(bol_nota) as prom, ROW_NUMBER() OVER(ORDER BY prom desc) as puesto FROM ".BD_ACADEMICA.".academico_matriculas mat
            INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol_estudiante=mat.mat_id AND bol_periodo='".$periodo."' AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}
            WHERE  mat.mat_grado='".$grado."' AND mat.mat_grupo='".$grupo."' AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$year}
            AND (mat_estado_matricula=1 OR mat_estado_matricula=2)
            GROUP BY mat.mat_id 
            ORDER BY prom DESC");

            // Recorrer los resultados y almacenarlos en el array $resultado
            while ($row = mysqli_fetch_assoc($consulta)) {
                $resultado[] = $row;
            }

        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene el puesto el promedio de los estudiantes de un curso, grupo y periodo
     * basado en el valor de las asignaturas dentro de cada área.
     * 
     * @param int $periodo 
     * @param string $grado 
     * @param string $grupo 
     * @param string $yearBd 
     * 
     * @return array
     * 
     */
    public static function obtenerPuestoYpromedioEstudianteConPorcentaje(
    int    $periodo = 0,
    string $grado   = "",
    string $grupo   = "",
    string $yearBd  = ''
    )
    {
        global $conexion, $config;

        $resultado = []; // Este será el array de datos que devolveremos

        // Determinar el año a usar, priorizando $yearBd si no está vacío
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $institucion = $config['conf_id_institucion']; // Obtener el ID de la institución

        try {
            // La consulta SQL con CTEs
            $sql = "
            WITH NotasDefinitivasPorArea AS (
                -- Paso 1: Calcular la nota definitiva para cada área por estudiante y periodo.
                SELECT
                    ab.bol_estudiante AS estudiante_id,
                    ab.bol_periodo,
                    ab.bol_area AS area_id,
                    SUM(ab.bol_nota_equivalente) AS nota_definitiva_area
                FROM
                    ".BD_ACADEMICA.".academico_boletin AS ab
                INNER JOIN
                    ".BD_ACADEMICA.".academico_matriculas AS m
                    ON ab.bol_estudiante = m.mat_id
                WHERE
                    ab.bol_periodo = ?
                    AND ab.institucion = ?
                    AND ab.year = ?
                    AND m.mat_grado = ?
                    AND m.mat_grupo = ?
                    AND m.institucion = ?
                    AND m.year = ?
                    AND (m.mat_estado_matricula=1 OR m.mat_estado_matricula=2)
                GROUP BY
                    ab.bol_estudiante, ab.bol_periodo, ab.bol_area
            ),
            PromedioGeneralEstudiante AS (
                -- Paso 2: Calcular el promedio general del estudiante basado en la definitiva de las áreas.
                SELECT
                    ndpa.estudiante_id,
                    ndpa.bol_periodo,
                    AVG(ndpa.nota_definitiva_area) AS promedio_general
                FROM
                    NotasDefinitivasPorArea AS ndpa
                GROUP BY
                    ndpa.estudiante_id, ndpa.bol_periodo
            )
            -- Paso 3: Obtener el resultado final con el puesto del estudiante.
            SELECT
                m.mat_id AS estudiante_id,
                m.mat_nombres AS nombre_estudiante,
                pge.bol_periodo,
                ROUND(pge.promedio_general, 2) AS promedio_general,
                ROW_NUMBER() OVER(ORDER BY pge.promedio_general DESC) AS puesto
            FROM
                PromedioGeneralEstudiante AS pge
            INNER JOIN
                ".BD_ACADEMICA.".academico_matriculas AS m
                ON pge.estudiante_id = m.mat_id
                AND m.mat_grado = ?
                AND m.mat_grupo = ?
                AND m.institucion = ?
                AND m.year = ?
                AND (m.mat_estado_matricula=1 OR m.mat_estado_matricula=2)
            ORDER BY
                puesto DESC;
            ";

            // Preparar la sentencia
            if ($stmt = mysqli_prepare($conexion, $sql)) {
                // Vincular los parámetros
                // 's' para string, 'i' para integer
                // Hay 11 marcadores '?' en la consulta.
                // Los parámetros se repiten: periodo, institucion, year, grado, grupo, institucion, year (para la primera CTE)
                // Y luego: grado, grupo, institucion, year (para el JOIN final)
                mysqli_stmt_bind_param(
                    $stmt,
                    "isiisiisiii", // Tipos de los 11 parámetros: i=int, s=string
                    $periodo, $institucion, $year, $grado, $grupo, $institucion, $year, // Para la primera CTE
                    $grado, $grupo, $institucion, $year // Para el JOIN final
                );

                // Ejecutar la sentencia
                mysqli_stmt_execute($stmt);

                // Obtener los resultados
                $result_query = mysqli_stmt_get_result($stmt);

                // Recorrer los resultados y almacenarlos en el array $resultado
                while ($row = mysqli_fetch_assoc($result_query)) {
                    $resultado[] = $row;
                }

                // Cerrar la sentencia preparada
                mysqli_stmt_close($stmt);

            } else {
                // Manejo de errores si la preparación falla
                throw new Exception("Error al preparar la sentencia SQL: " . mysqli_error($conexion));
            }

        } catch (Exception $e) {
            Utilidades::writeLog('Se jodió esta vaina porque '.$e->getMessage());

            echo "Ocurrió un error mientras se obtenía el puesto y promedio del estudiante.";
            // Puedes loggear el error o manejarlo de otra forma
            //exit();
        }

        return $resultado;
    }

    /**
     * Se encarga de direccionar al método correcto de obtención de promedios
     * y puestos, basado en la configuración de la Institución
     * 
     * @param int $periodo
     * @param string $grado
     * @param string $grupo
     * @param string $yearBd
     * 
     * @return array
     */
    public static function obtenerPuestoYpromedioEstudiante(
    int    $periodo = 0,
    string $grado   = "",
    string $grupo   = "",
    string $yearBd  = ''
    ) {
        global $config;

        if($config['conf_agregar_porcentaje_asignaturas'] == 'SI' && !Asignaturas::hayAsignaturaSinValor()) {
            return self::obtenerPuestoYpromedioEstudianteConPorcentaje($periodo, $grado, $grupo, $yearBd);
        }

        return self::obtenerPuestoYpromedioEstudianteOriginal($periodo, $grado, $grupo, $yearBd);
    }

    /**
     * Obtiene las áreas asociadas a un estudiante en un grado y grupo específicos.
     *
     * @param string $grado El grado del estudiante.
     * @param string $grupo El grupo al que pertenece el estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener las áreas. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene información sobre las áreas asociadas al estudiante.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     */
    public static function obtenerAreasDelEstudiante(
        string    $grado      = "",
        string    $grupo      = "",
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT ar_id, car_ih FROM ".BD_ACADEMICA.".academico_cargas car
        INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car.car_materia AND am.institucion=car.institucion AND am.year=car.year
        INNER JOIN ".BD_ACADEMICA.".academico_areas ar ON ar.ar_id= am.mat_area AND ar.institucion=car.institucion AND ar.year=car.year
        WHERE  car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=? 
        GROUP BY ar.ar_id 
        ORDER BY ar.ar_posicion ASC";

        $parametros = [$grado, $grupo, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene datos asociados a un área específica para un estudiante y condiciones de periodo determinadas.
     *
     * @param string $estudiante La identificación del estudiante.
     * @param string $area La identificación del área para la cual se desean obtener datos.
     * @param string $condicion Una cadena que representa las condiciones del periodo académico (ej. "1,2,3").
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener datos. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene información sobre el promedio y otras estadísticas del área.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     */
    public static function obtenerDatosDelArea(
        string    $estudiante      = '',
        string    $area      = '',
        string    $condicion      = '',
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $resultado = mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre, ar_id,car_id,car_ih FROM ".BD_ACADEMICA.".academico_materias am
            INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_materia=am.mat_id AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol.bol_carga=car.car_id AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}
            WHERE bol_estudiante='" . $estudiante . "' and a.ar_id='" . $area . "' and bol_periodo in (" . $condicion . ") AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
            GROUP BY ar_id;");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene el promedio de notas de un estudiante para un periodo académico específico.
     *
     * @param int    $estudiante La identificación del estudiante.
     * @param int    $periodo El periodo académico para el cual se desea obtener el promedio.
     * @param string $BD (Opcional) El nombre de la base de datos específica a la que se va a realizar la consulta. Si no se proporciona, se utiliza la base de datos por defecto.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener el promedio. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene el promedio de notas del estudiante para el periodo académico especificado.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     */
    public static function obtenerPromedioPorTodosLosPeriodos(
        string $estudiante = '',
        int    $periodo    = 0,
        string $yearBd     = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT ROUND(AVG(bol_nota), 2) as promedio FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_periodo=? AND institucion=? AND year=?";
        $parametros = [$estudiante, $periodo, $config['conf_id_institucion'], $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene el promedio de notas de un estudiante para una carga académico específico.
     *
     * @param int    $estudiante La identificación del estudiante.
     * @param string $carga La carga académico para el cual se desea obtener el promedio.
     * @param string $BD (Opcional) El nombre de la base de datos específica a la que se va a realizar la consulta. Si no se proporciona, se utiliza la base de datos por defecto.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener el promedio. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene el promedio de notas del estudiante para el periodo académico especificado.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     */
    public static function obtenerPromedioPorTodasLasCargas(
        string $estudiante = '',
        string $carga = '',
        string $yearBd     = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT ROUND(AVG(bol_nota), 2) as def FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_carga=? AND institucion=? AND year=?";
        $parametros = [$estudiante, $carga, $config['conf_id_institucion'], $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Obtiene información sobre nivelaciones de un estudiante para una carga académica específica.
     *
     * @param string $carga La identificación de la carga académica.
     * @param string $estudiante La identificación del estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener información de nivelaciones. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene información sobre las nivelaciones del estudiante para la carga académica especificada.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     */
    public static function obtenerNivelaciones(
        $carga,
        $estudiante,
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_nivelaciones WHERE niv_id_asg=? AND niv_cod_estudiante=? AND institucion=? AND year=?";

        $parametros = [$carga, $estudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene las notas de disciplina de un estudiante para periodos académicos específicos.
     *
     * @param string $estudiante La identificación del estudiante.
     * @param string $condicion Una cadena que representa las condiciones de los periodos académicos (ej. "1,2,3").
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene las notas de disciplina del estudiante para los periodos académicos especificados.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $condicionEjemplo = "1,2,3";
     * $resultadosDisciplina = obtenerNotaDisciplina($estudianteEjemplo, $condicionEjemplo);
     * while ($notaDisciplina = mysqli_fetch_assoc($resultadosDisciplina)) {
     *     // Procesar información de cada nota de disciplina
     *     echo "ID de Nota de Disciplina: ".$notaDisciplina['dn_id']." - Fecha: ".$notaDisciplina['dn_fecha']." - Descripción: ".$notaDisciplina['dn_descripcion']."<br>";
     * }
     * ```
     */
    public static function obtenerNotaDisciplina(
        string $estudiante,
        string $condicion,
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $resultado = [];

        try {
            $resultado = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='" . $estudiante . "' AND institucion={$config['conf_id_institucion']} AND year={$year} AND dn_periodo in(" . $condicion . ");");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene información sobre recuperaciones de un estudiante para un indicador específico, en una carga académica y periodo determinados.
     *
     * @param string $estudiante La identificación del estudiante.
     * @param string $carga La identificación de la carga académica.
     * @param int    $periodo El periodo académico para el cual se desea obtener información de recuperación.
     * @param string $indicador El indicador específico para el cual se desean obtener datos de recuperación.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener información de recuperación. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene información sobre las recuperaciones del estudiante para el indicador, carga académica y periodo especificados.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $cargaEjemplo = "ID_CARGA";
     * $periodoEjemplo = 1;
     * $indicadorEjemplo = "INDICADOR";
     * $resultadosRecuperacion = obtenerRecuperacionPorIndicador($estudianteEjemplo, $cargaEjemplo, $periodoEjemplo, $indicadorEjemplo, "2023");
     * while ($recuperacion = mysqli_fetch_assoc($resultadosRecuperacion)) {
     *     // Procesar información de cada recuperación
     *     echo "ID de Recuperación: ".$recuperacion['rind_id']." - Fecha: ".$recuperacion['rind_fecha']." - Descripción: ".$recuperacion['rind_descripcion']."<br>";
     * }
     * ```
     */
    public static function obtenerRecuperacionPorIndicador(
        string    $estudiante      = '',
        string    $carga      = '',
        int    $periodo      = 0,
        string    $indicador      = '',
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_indicadores_recuperacion WHERE rind_estudiante=? AND rind_carga=? AND rind_periodo=? AND rind_indicador=? AND institucion=? AND year=?";
        $parametros = [$estudiante, $carga, $periodo, $indicador, $config['conf_id_institucion'], $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene las observaciones de un estudiante para una carga académica y periodo específicos.
     *
     * @param string $carga La identificación de la carga académica.
     * @param int    $periodo El periodo académico para el cual se desea obtener observaciones.
     * @param string $estudiante La identificación del estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener observaciones. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene las observaciones del estudiante para la carga académica y periodo especificados.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $cargaEjemplo = "ID_CARGA";
     * $periodoEjemplo = 1;
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $resultadosObservaciones = obtenerObservaciones($cargaEjemplo, $periodoEjemplo, $estudianteEjemplo, "2023");
     * while ($observacion = mysqli_fetch_assoc($resultadosObservaciones)) {
     *     // Procesar información de cada observación
     *     echo "ID de Observación: ".$observacion['bol_id']." - Fecha: ".$observacion['bol_fecha']." - Descripción: ".$observacion['bol_observacion']."<br>";
     * }
     * ```
     */
    public static function obtenerObservaciones(
        string    $carga      = '',
        int    $periodo      = 0,
        string    $estudiante      = '',
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_carga=? AND bol_periodo=? AND bol_estudiante=? AND institucion=? AND year=?";
        $parametros = [$carga, $periodo, $estudiante, $config['conf_id_institucion'], $year];
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Obtiene las notas de un estudiante para una carga académica y periodo específicos.
     *
     * @param string $carga La identificación de la carga académica.
     * @param int $periodo El periodo académico para el cual se desea obtener las notas.
     * @param string $estudiante La identificación del estudiante.
     * @param string $yearBd El año académico para el cual se desea obtener las notas.
     *
     * @return array Un arreglo que contiene las notas del estudiante para la carga académica y periodo especificados.
     */
    public static function obtenerNotasEstudiantilesPorCargaPeriodo(string $carga, int $periodo, string $estudiante, string $yearBd)
    {
        global $config;
        $resultado = [];

        $sql = "
        SELECT * FROM ".BD_ACADEMICA.".academico_boletin 
        WHERE bol_carga=? 
        AND bol_periodo=? 
        AND bol_estudiante=? 
        AND institucion=? 
        AND year=?
        ";

        $parametros = [$carga, $periodo, $estudiante, $config['conf_id_institucion'], $yearBd];
        $consulta   = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);

        return $resultado;
    }


    /**
     * Obtiene la nota definitiva y nombre de una materia para un estudiante en un área y periodos académicos específicos.
     *
     * @param string $estudiante La identificación del estudiante.
     * @param string $area La identificación del área.
     * @param string $condicion Una cadena que representa las condiciones de los periodos académicos (ej. "1,2,3").
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene la nota definitiva y nombre de la materia para el estudiante en el área y periodos especificados.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $areaEjemplo = "ID_AREA";
     * $condicionEjemplo = "1,2,3";
     * $resultadosDefinitiva = obtenerDefinitivaYnombrePorMateria($estudianteEjemplo, $areaEjemplo, $condicionEjemplo, "2023");
     * while ($definitiva = mysqli_fetch_assoc($resultadosDefinitiva)) {
     *     // Procesar información de cada materia y su nota definitiva
     *     echo "Materia: ".$definitiva['mat_nombre']." - Nota Definitiva: ".$definitiva['suma']."<br>";
     * }
     * ```
     */
    public static function obtenerDefinitivaYnombrePorMateria(
        string $estudiante = '',
        string $area       = '',
        string $condicion  = '',
        string $yearBd     = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $resultado = mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre,mat_nombre,mat_area,mat_valor,mat_id,car_id,car_docente,car_ih,car_director_grupo,mat_sumar_promedio, car_observaciones_boletin, mat_siglas FROM ".BD_ACADEMICA.".academico_materias am
            INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_materia=am.mat_id AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol.bol_carga=car.car_id AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}
            WHERE bol_estudiante='" . $estudiante . "' and a.ar_id='" . $area . "' and bol_periodo in (" . $condicion . ") AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
            GROUP BY mat_id
            ORDER BY mat_id;");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene las notas y periodos de una materia para un estudiante en un área y periodos académicos específicos.
     *
     * @param string $estudiante La identificación del estudiante.
     * @param string $area La identificación del área.
     * @param string $condicion Una cadena que representa las condiciones de los periodos académicos (ej. "1,2,3").
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene las notas y periodos de una materia para el estudiante en el área y periodos especificados.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $areaEjemplo = "ID_AREA";
     * $condicionEjemplo = "1,2,3";
     * $resultadosDefinitivaPorPeriodo = obtenerDefinitivaPorPeriodo($estudianteEjemplo, $areaEjemplo, $condicionEjemplo, "2023");
     * while ($definitivaPeriodo = mysqli_fetch_assoc($resultadosDefinitivaPorPeriodo)) {
     *     // Procesar información de cada materia, nota y periodo
     *     echo "Materia: ".$definitivaPeriodo['mat_nombre']." - Nota: ".$definitivaPeriodo['bol_nota']." - Periodo: ".$definitivaPeriodo['bol_periodo']."<br>";
     * }
     * ```
     */
    public static function obtenerDefinitivaPorPeriodo(
        string    $estudiante      = "",
        string    $area      = "",
        string    $condicion      = '',
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $resultado = mysqli_query($conexion, "SELECT bol_nota,bol_periodo,ar_nombre,mat_nombre,mat_id FROM ".BD_ACADEMICA.".academico_materias am
            INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_materia=am.mat_id AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_boletin bol ON bol.bol_carga=car.car_id AND bol.institucion={$config['conf_id_institucion']} AND bol.year={$year}
            WHERE bol_estudiante='" . $estudiante . "' and a.ar_id='" . $area . "' and bol_periodo in (" . $condicion . ") AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
            ORDER BY mat_id,bol_periodo
            ;");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene los indicadores y notas asociados a una materia y estudiante específicos, considerando un área, grados, grupos y condiciones de periodo académico.
     *
     * @param string $grado El identificador del grado.
     * @param string $grupo El identificador del grupo.
     * @param string $area El identificador del área.
     * @param string $condicion Una cadena que representa las condiciones de los periodos académicos para los indicadores (ej. "1,2,3").
     * @param string $estudiante La identificación del estudiante.
     * @param string $condicion2 Una cadena que representa las condiciones de los periodos académicos para las actividades (ej. "1,2,3").
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene los indicadores, notas y periodos asociados a una materia y estudiante específicos.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $gradoEjemplo = "GRADO_EJEMPLO";
     * $grupoEjemplo = "GRUPO_EJEMPLO";
     * $areaEjemplo = "AREA_EJEMPLO";
     * $condicionEjemplo = "1,2,3";
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $condicion2Ejemplo = "1,2,3";
     * $resultadosIndicadores = obtenerIndicadoresPorMateria($gradoEjemplo, $grupoEjemplo, $areaEjemplo, $condicionEjemplo, $estudianteEjemplo, $condicion2Ejemplo, "2023");
     * while ($indicador = mysqli_fetch_assoc($resultadosIndicadores)) {
     *     // Procesar información de cada indicador, nota y periodo
     *     echo "Materia: ".$indicador['mat_nombre']." - Indicador: ".$indicador['ind_nombre']." - Nota: ".$indicador['nota']." - Periodo: ".$indicador['ipc_periodo']."<br>";
     * }
     * ```
     */
    public static function obtenerIndicadoresPorMateria(
        string    $grado      = "",
        string    $grupo      = "",
        string    $area      = "",
        string    $condicion      = '',
        string    $estudiante      = "",
        string    $condicion2      = '',
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $resultado = mysqli_query($conexion, "SELECT mat_nombre,mat_area,mat_id,ind_nombre,ipc_periodo,
            ROUND(SUM(cal_nota*(act_valor/100)) / SUM(act_valor/100),2) as nota, ind_id, aii.aii_descripcion_indicador FROM ".BD_ACADEMICA.".academico_materias am
            INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_materia=am.mat_id AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga aic ON aic.ipc_carga=car.car_id AND aic.institucion={$config['conf_id_institucion']} AND aic.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON aic.ipc_indicador=ai.ind_id AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND act_id_carga=car_id AND act_estado=1 AND act_registrada=1 AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$year}
            LEFT JOIN ".BD_ACADEMICA.".academico_indicadores_inclusion aii ON aii.aii_id_indicador = ind_id AND aii.aii_id_estudiante = cal_id_estudiante AND aii.institucion={$config['conf_id_institucion']} AND aii.year={$year}
            WHERE car_curso='" . $grado . "'  and car_grupo='" . $grupo . "' and mat_area='" . $area . "' AND ipc_periodo in (" . $condicion . ") AND cal_id_estudiante='" . $estudiante . "' and act_periodo=" . $condicion2 . " AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
            group by act_id_tipo, act_id_carga
            order by mat_id,ipc_periodo,ind_id;");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene la cantidad total de ausencias de un estudiante en una materia y periodo académico específicos.
     *
     * @param string $grado El identificador del grado.
     * @param string $materia El identificador de la materia.
     * @param int $periodo El número de periodo académico.
     * @param string $estudiante La identificación del estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene la cantidad total de ausencias de un estudiante en una materia y periodo académico específicos.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $gradoEjemplo = "GRADO_EJEMPLO";
     * $materiaEjemplo = "MATERIA_EJEMPLO";
     * $periodoEjemplo = 1;
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $resultadosAusencias = obtenerDatosAusencias($gradoEjemplo, $materiaEjemplo, $periodoEjemplo, $estudianteEjemplo, "2023");
     * $ausencias = mysqli_fetch_assoc($resultadosAusencias);
     * echo "Total de Ausencias: " . $ausencias['sumAus'];
     * ```
     */
    public static function obtenerDatosAusencias(
        string    $grado      = "",
        string    $materia      = "",
        int    $periodo      = 0,
        string    $estudiante      = "",
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $resultado = mysqli_query($conexion, "SELECT sum(aus_ausencias) as sumAus FROM ".BD_ACADEMICA.".academico_ausencias aus
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_curso='".$grado."' AND car_materia='".$materia."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_clases cls ON cls.cls_id=aus.aus_id_clase AND cls.cls_id_carga=car_id AND cls.cls_periodo='".$periodo."' AND cls.institucion={$config['conf_id_institucion']} AND cls.year={$year}
            WHERE aus.aus_id_estudiante='".$estudiante."' AND aus.institucion={$config['conf_id_institucion']} AND aus.year={$year}");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene los indicadores de una materia específica para un periodo académico determinado.
     *
     * @param string $grado El identificador del grado.
     * @param string $grupo El identificador del grupo.
     * @param string $area El identificador del área.
     * @param int $periodo El número de periodo académico.
     * @param string $estudiante La identificación del estudiante.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene los indicadores de una materia específica para un periodo académico determinado.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $gradoEjemplo = "GRADO_EJEMPLO";
     * $grupoEjemplo = "GRUPO_EJEMPLO";
     * $areaEjemplo = "AREA_EJEMPLO";
     * $periodoEjemplo = 1;
     * $estudianteEjemplo = "ID_ESTUDIANTE";
     * $resultadosIndicadores = obtenerIndicadoresDeMateriaPorPeriodo($gradoEjemplo, $grupoEjemplo, $areaEjemplo, $periodoEjemplo, $estudianteEjemplo, "2023");
     * while ($row = mysqli_fetch_assoc($resultadosIndicadores)) {
     *     echo "Materia: " . $row['mat_nombre'] . ", Indicador: " . $row['ind_nombre'] . ", Nota: " . $row['nota'] . "\n";
     * }
     * ```
     */
    public static function obtenerIndicadoresDeMateriaPorPeriodo(
        string    $grado      = "",
        string    $grupo      = "",
        string    $area      = "",
        int    $periodo      = 0,
        string    $estudiante      = "",
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $resultado = mysqli_query($conexion, "SELECT mat_nombre,mat_area,mat_id,ind_nombre,ipc_periodo,
            ROUND(SUM(cal_nota*(act_valor/100)) / SUM(act_valor/100),2) as nota, ind_id, ipc_valor FROM ".BD_ACADEMICA.".academico_materias am
            INNER JOIN ".BD_ACADEMICA.".academico_areas a ON a.ar_id=am.mat_area AND a.institucion={$config['conf_id_institucion']} AND a.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car.car_materia=am.mat_id AND car.institucion={$config['conf_id_institucion']} AND car.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores_carga aic ON aic.ipc_carga=car.car_id AND aic.institucion={$config['conf_id_institucion']} AND aic.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_indicadores ai ON aic.ipc_indicador=ai.ind_id AND ai.institucion={$config['conf_id_institucion']} AND ai.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND act_id_carga=car_id AND act_estado=1 AND act_registrada=1 AND aa.institucion={$config['conf_id_institucion']} AND aa.year={$year}
            INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id AND aac.institucion={$config['conf_id_institucion']} AND aac.year={$year}
            WHERE car_curso='" . $grado . "'  and car_grupo='" . $grupo . "' and mat_area='" . $area . "' AND ipc_periodo= " . $periodo . " AND cal_id_estudiante='" . $estudiante . "' and act_periodo=" . $periodo . " AND am.institucion={$config['conf_id_institucion']} AND am.year={$year}
            group by act_id_tipo, act_id_carga
            order by mat_id,ipc_periodo,ind_id;");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene el puesto del estudiante en la institución para un periodo académico determinado.
     *
     * @param int $periodo El número de periodo académico.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene el puesto del estudiante en la institución para un periodo académico determinado.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $periodoEjemplo = 1;
     * $resultadosPuesto = obtenerPuestoEstudianteEnInstitucion($periodoEjemplo, "2023");
     * while ($row = mysqli_fetch_assoc($resultadosPuesto)) {
     *     echo "Estudiante: " . $row['mat_nombres'] . ", Puesto: " . $row['puesto'] . "\n";
     * }
     * ```
     */

    /**
     * Se encarga de la obtención del puesto del estudiante en la institución
     * basado en la configuración que la Institución tenga.
     *
     * @param int $periodo El número de periodo académico.
     * @param string $yearBd
     * 
     * @return array Un arreglo que contiene el puesto del estudiante en la institución.
     */
    public static function obtenerPuestoEstudianteEnInstitucion(
    int    $periodo = 0,
    string $yearBd  = ''
    ) {
        global $config;

        if($config['conf_agregar_porcentaje_asignaturas'] == 'SI' && !Asignaturas::hayAsignaturaSinValor()) {
            return self::obtenerPuestoEstudianteEnInstitucionConPorcentaje($periodo, $yearBd);
        }

        return self::obtenerPuestoEstudianteEnInstitucionOriginal($periodo, $yearBd);
    }

    /**
     * Obtiene el puesto del estudiante en la institución para un periodo académico determinado.
     *
     * @param int $periodo El número de periodo académico.
     * @param string $yearBd (Opcional) El año académico para el cual se desea obtener la información. Si no se proporciona, se utiliza el año académico actual de la sesión.
     *
     * @return mysqli_result Un conjunto de resultados (`mysqli_result`) que contiene el puesto del estudiante en la institución para un periodo académico determinado.
     *
     * @throws Exception Si hay algún problema durante la ejecución de la consulta SQL, se captura una excepción y se imprime un mensaje de error.
     *
     * @example
     * ```php
     * // Ejemplo de uso
     * $periodoEjemplo = 1;
     * $resultadosPuesto = obtenerPuestoEstudianteEnInstitucion($periodoEjemplo, "2023");
     * while ($row = mysqli_fetch_assoc($resultadosPuesto)) {
     *     echo "Estudiante: " . $row['mat_nombres'] . ", Puesto: " . $row['puesto'] . "\n";
     * }
     * ```
     */
    public static function obtenerPuestoEstudianteEnInstitucionOriginal(
        int    $periodo      = 0,
        string $yearBd    = ''
    )
    {
        global $conexion, $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $consulta = mysqli_query($conexion, "
            SELECT mat_id as estudiante_id,
            bol_estudiante,
            bol_carga, mat_nombres,
            mat_grado, bol_periodo,
            avg(bol_nota) as prom,
            ROW_NUMBER() OVER(ORDER BY prom desc) as puesto
            FROM ".BD_ACADEMICA.".academico_matriculas mat

            INNER JOIN ".BD_ACADEMICA.".academico_boletin bol 
            ON bol_estudiante=mat.mat_id AND bol_periodo='".$periodo."' 
            AND bol.institucion={$config['conf_id_institucion']} 
            AND bol.year={$year}

            WHERE  mat.mat_eliminado=0 
            AND mat.institucion={$config['conf_id_institucion']} 
            AND mat.year={$year} 
            AND mat.mat_estado_matricula IN (".MATRICULADO.", ".ASISTENTE.")
            GROUP BY mat.mat_id 
            ORDER BY prom DESC");

            // Recorrer los resultados y almacenarlos en el array $resultado
            while ($row = mysqli_fetch_assoc($consulta)) {
                $resultado[] = $row;
            }

        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Mediante una CTE se hace la consulta para obtener los puestos y promedios
     * estudiantes de la Institución.
     * 
     * @param int $periodo El número de periodo académico.
     * @param string $yearBd
     * 
     * @return array
     */
    public static function obtenerPuestoEstudianteEnInstitucionConPorcentaje(
        int    $periodo = 0,
        string $yearBd  = ''
    )
    {
        global $conexion, $config;
        $resultado = []; // This will be the array of data we return

        // Determine the year to use, prioritizing $yearBd if not empty
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $institucion = $config['conf_id_institucion']; // Get the institution ID

        try {
            // The SQL query with CTEs, simplified for institution-level average and rank
            $sql = "
            WITH NotasDefinitivasPorArea AS (
                -- Step 1: Calculate the final grade for each area per student and period.
                SELECT
                    ab.bol_estudiante AS estudiante_id,
                    ab.bol_periodo,
                    ab.bol_area AS area_id,
                    SUM(ab.bol_nota_equivalente) AS nota_definitiva_area
                FROM
                    ".BD_ACADEMICA.".academico_boletin AS ab
                INNER JOIN
                    ".BD_ACADEMICA.".academico_matriculas AS m
                    ON ab.bol_estudiante = m.mat_id
                WHERE
                    ab.bol_periodo = ?
                    AND ab.institucion = ?
                    AND ab.year = ?
                    -- Filters for m.mat_grado and m.mat_grupo are removed for institution-level calculation
                    AND m.institucion = ?
                    AND m.year = ?
                    AND (m.mat_estado_matricula=1 OR m.mat_estado_matricula=2)
                GROUP BY
                    ab.bol_estudiante, ab.bol_periodo, ab.bol_area
            ),
            PromedioGeneralEstudiante AS (
                -- Step 2: Calculate the student's general average based on the area's final grades.
                SELECT
                    ndpa.estudiante_id,
                    ndpa.bol_periodo,
                    AVG(ndpa.nota_definitiva_area) AS promedio_general
                FROM
                    NotasDefinitivasPorArea AS ndpa
                GROUP BY
                    ndpa.estudiante_id, ndpa.bol_periodo
            )
            -- Step 3: Get the final result with the student's rank,
            -- including only the required columns for institution-level average and rank.
            SELECT
                m.mat_id AS estudiante_id, -- Using mat_id as the primary identifier
                m.mat_nombres AS nombre_estudiante,
                pge.bol_periodo,
                ROUND(pge.promedio_general, 2) AS promedio_general,
                ROW_NUMBER() OVER(ORDER BY pge.promedio_general DESC) AS puesto
            FROM
                PromedioGeneralEstudiante AS pge
            INNER JOIN
                ".BD_ACADEMICA.".academico_matriculas AS m
                ON pge.estudiante_id = m.mat_id
                -- Filters for m.mat_grado and m.mat_grupo are removed for institution-level calculation
                AND m.institucion = ?
                AND m.year = ?
                AND m.mat_estado_matricula IN (".MATRICULADO.", ".ASISTENTE.") -- Use constants directly
            ORDER BY
                promedio_general DESC; -- Order by the calculated general average
            ";

            // Prepare the statement
            if ($stmt = mysqli_prepare($conexion, $sql)) {
                // Bind parameters
                // There are 7 '?' markers in the query:
                // 1-3: periodo, institucion, year (for the first CTE)
                // 4-5: institucion, year (for the first CTE)
                // 6-7: institucion, year (for the final JOIN to academico_matriculas)
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiiiiii", // Types of the 7 parameters: i=int
                    $periodo, $institucion, $year, // Parameters 1-3 for the first CTE
                    $institucion, $year, // Parameters 4-5 for the first CTE (repeated)
                    $institucion, $year // Parameters 6-7 for the final JOIN to academico_matriculas (repeated)
                );

                // Execute the statement
                mysqli_stmt_execute($stmt);

                // Get the results
                $result_query = mysqli_stmt_get_result($stmt);

                // Loop through the results and store them in the $resultado array
                while ($row = mysqli_fetch_assoc($result_query)) {
                    $resultado[] = $row;
                }

                // Close the prepared statement
                mysqli_stmt_close($stmt);

            } else {
                // Error handling if preparation fails
                throw new Exception("Error al preparar la sentencia SQL: " . mysqli_error($conexion));
            }

        } catch (Exception $e) {
            echo "Excepción capturada: ".$e->getMessage();
            // You can log the error or handle it differently
            exit();
        }

        return $resultado;
    }



    /**
     * Este metodo me elimina la nota del boletin por ID
     */
    public static function eliminarNotaBoletinID(
        array  $config,
        string $idBoletin,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_id=? AND institucion=? AND year=?";

        $parametros = [$idBoletin, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina la nota del boletin por ID
     */
    public static function eliminarNotasBoletinEstudiante(
        array  $config,
        string $idEstudiante,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me elimina la nota del boletin por ID
     */
    public static function eliminarNotasBoletinInstitucion(
        array  $config,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_boletin WHERE institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me trae la nota de un estudiante
     */
    public static function traerNotaBoletinEstudiante(
        array  $config, 
        string $idEstudiante,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me trae la nota de un estudiante en un periodo
     */
    public static function traerNotaBoletinPeriodo(
        array  $config,
        int    $periodo, 
        string $idEstudiante,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_periodo=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $periodo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me cuenta la nota de un estudiante en un periodo
     */
    public static function contarNotaBoletinPeriodo(
        array  $config,
        int    $periodo, 
        string $idEstudiante,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_periodo=? AND institucion=? AND year=? GROUP BY bol_carga";

        $parametros = [$idEstudiante, $periodo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $num = mysqli_num_rows($resultado);

        return $num;
    }

    /**
     * Este metodo me trae la nota en una carga de un estudiante en un periodo
     */
    public static function traerNotaBoletinCargaPeriodo(
        array  $config,
        int    $periodo, 
        string $idEstudiante,
        string $idCarga,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "
        SELECT * FROM ".BD_ACADEMICA.".academico_boletin bol 
        LEFT JOIN ".BD_ACADEMICA.".academico_notas_tipos ntp ON ntp.notip_categoria=? 
        AND bol_nota>=ntp.notip_desde 
        AND bol_nota<=ntp.notip_hasta 
        AND ntp.institucion=bol.institucion 
        AND ntp.year=bol.year
        WHERE bol_estudiante=? 
        AND bol_carga=? 
        AND bol_periodo=? 
        AND bol.institucion=? 
        AND bol.year=?
        ";

        $parametros = [$config["conf_notas_categoria"], $idEstudiante, $idCarga, $periodo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Retrieves the grades of a student for a specific academic load and period.
     *
     * @param array  $config       Configuration array containing institution settings.
     * @param int    $periodo      The academic period number.
     * @param string $idEstudiante The ID of the student.
     * @param string $idCarga      The ID of the academic load.
     * @param string $yearBd       (Optional) The academic year for which the information is desired. If not provided, the current session's academic year is used.
     *
     * @return array An associative array containing the student's grades for the specified academic load and period.
     */
    public static function obtenerNotasBoletin(
        array  $config,
        int    $periodo, 
        string $idEstudiante,
        string $idCarga,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "
        SELECT * FROM ".BD_ACADEMICA.".academico_boletin bol 
        WHERE bol_estudiante=? 
        AND bol_carga=? 
        AND bol_periodo=? 
        AND bol.institucion=? 
        AND bol.year=?
        ";

        $parametros = [$idEstudiante, $idCarga, $periodo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me trae la nota de un estudiante en una carga
     */
    public static function traerDefinitivaBoletinCarga(
        array  $config,
        string $idCarga, 
        string $idEstudiante,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT avg(bol_nota) AS promedio, MAX(bol_periodo) AS periodo FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_carga=? AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $idCarga, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me obtiene el promedio de diferentes cargas
     */
    public static function obtenerPromedioDiferentesCargas(
        array  $config,
        string $idEstudiante,
        string $cargas,
        string $yearBd = ""
    ){
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT avg(bol_nota) FROM ".BD_ACADEMICA.".academico_boletin WHERE bol_estudiante=? AND bol_carga IN(".$cargas.") AND institucion=? AND year=?";

        $parametros = [$idEstudiante, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
     * Este metodo me guarda la nota de un estudiante para el boletín
    **/
    public static function guardarNotaBoletin (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_boletin');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_boletin({$insert}) VALUES ({$signosPreguntas})";

        $resultado = BindSQL::prepararSQL($sql, $parametros);

    }

    /**
     * Este metodo me actualiza la nota de un estudiante en el boletin
    **/
    public static function actualizarNotaBoletin (
        array   $config,
        string  $idBoletin,
        array   $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdateConArray($update);

        $bolActualizaciones = !array_key_exists('bol_actualizaciones', $update) ? 'bol_actualizaciones=bol_actualizaciones+1,' : null;

        $sql = "UPDATE ".BD_ACADEMICA.".academico_boletin SET {$updateSql}, 
        {$bolActualizaciones}
        bol_ultima_actualizacion=now()
        WHERE bol_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idBoletin, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Updates the academic bulletin records based on the provided conditions.
     *
     * @param array  $config  Configuration array containing institution settings.
     * @param array  $update  Associative array of columns and their new values to be updated.
     * @param string $where   (Optional) SQL WHERE clause to specify which records to update. Defaults to 'bol_id=bol_id'.
     * @param string $yearBd  (Optional) The academic year for which the information is desired. If not provided, the current session's academic year is used.
     *
     * @return void
     */
    public static function actualizarBoletin (
        array   $config,
        array   $update,
        string  $where = 'bol_id=bol_id',
        string  $yearBd = ""
    ): void
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdateConArray($update);

        $bolActualizaciones = !array_key_exists('bol_actualizaciones', $update) ? 'bol_actualizaciones=bol_actualizaciones+1,' : null;

        $sql = "UPDATE ".BD_ACADEMICA.".academico_boletin SET {$updateSql}, 
        {$bolActualizaciones}
        bol_ultima_actualizacion=now()
        WHERE {$where} AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$config['conf_id_institucion'], $year]);

        BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me actualiza el registro de un estudiante en el boletin segun lacarga
    **/
    public static function actualizarBoletinCargaEstudiante (
        array   $config,
        string  $idCarga,
        string  $idEstudiante,
        array   $update,
        string  $yearBd = "",
        string  $filter = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdateConArray($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_boletin SET {$updateSql} WHERE bol_estudiante=? AND bol_carga=? AND institucion=? AND year=? $filter";

        $parametros = array_merge($updateValues, [$idEstudiante, $idCarga, $config['conf_id_institucion'], $year]);

        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este metodo me consulta los estudiantes y los organiza segun su puesto
    **/
    public static function consultarPuestosBoletin (
        array   $config,
        string  $idCurso,
        string  $idGrupo,
        int     $periodo,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT SUM(bol_nota) AS suma, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_nombre2 FROM ".BD_ACADEMICA.".academico_boletin bol
        INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_id=bol_estudiante AND mat.institucion=? AND mat.year=?
        INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_id=bol_carga AND car_curso=? AND car_grupo=? AND car.institucion=? AND car.year=?
        WHERE bol_periodo=? AND bol.institucion=? AND bol.year=? AND (mat_estado_matricula=1 OR mat_estado_matricula=2)
        GROUP BY bol_estudiante
        ORDER BY suma DESC";

        $parametros = [$config['conf_id_institucion'], $year, $idCurso, $idGrupo, $config['conf_id_institucion'], $year, $periodo, $config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    public static function datosBoletin(
        string  $grado,
        string  $grupo,
        array  $periodos,
        string  $year,
        string  $idEstudiante="",
        bool    $traerIndicadores=false,
    ) {
        global $conexion, $config;

        $year = empty($year) ?  $_SESSION["bd"] : $year;
        
        $andEstudiante="";

        if (!empty($idEstudiante)) {
            $andEstudiante = "AND   mat.mat_id  = '" . $idEstudiante."'";
        }
        $in_periodos2 = implode(', ', $periodos);
        $odenNombres    ='';
        switch ($config['conf_orden_nombre_estudiantes']) {
            case '1':
                $odenNombres = "mat_nombres,mat_nombre2,mat_primer_apellido,mat_segundo_apellido,";
                break;
            case '2':
                $odenNombres = "mat_primer_apellido,mat_segundo_apellido,mat_nombres,mat_nombre2,";
                break;
         }
        $sql = "
                 SELECT
                    mat.mat_id,
                    are.ar_id,                   
                    car.car_id,                    
                    gra.gra_nombre,
                    gra.gra_nivel,
                    gru.gru_nombre,
                  	ar_nombre,
                    mate.mat_id as id_materia,
                    mate.mat_nombre,                    
                    car.car_ih, 
					car.car_director_grupo,
                    mate.mat_valor,
                    per.gvp_valor as periodo_valor,
                    aus.aus_ausencias,
                    acum.*,
                    bol.*,
                    disi.*, 
                    docen.*,
                    mat.*,";
                    if($traerIndicadores){
                     $sql .="
                        ind.ind_id,
                        ind.ind_nombre,             
                        indv.*, 
                        indr.rind_id,
                        indr.rind_nota,  
                     ";
                    }
                    $sql .="                                    
                    niv.*,
                    car.car_docente
                    
                    
                    
                FROM " . BD_ACADEMICA . ".academico_matriculas mat

                LEFT JOIN " . BD_ACADEMICA . ".academico_cargas car 
                ON  car.institucion = mat.institucion 
                AND car.year        = mat.year
                AND car.car_grupo   = mat.mat_grupo
                AND car.car_curso   = mat.mat_grado

                LEFT JOIN " . BD_GENERAL . ".usuarios docen
				ON  docen.institucion  = mat.institucion
                AND docen.year         = mat.year
				AND docen.uss_id       = car.car_docente
            
                INNER JOIN " . BD_ACADEMICA . ".academico_materias mate 
                ON  mate.institucion = mat.institucion
                AND mate.year        = mat.year
                AND mate.mat_id      = car.car_materia

                INNER JOIN " . BD_ACADEMICA . ".academico_grados gra 
                ON  gra.institucion = mat.institucion 
                AND gra.year        = mat.year
                AND gra.gra_id      = mat.mat_grado
								
			    INNER JOIN " . BD_ACADEMICA . ".academico_grupos gru 
                ON  gru.institucion = mat.institucion 
                AND gru.year        = mat.year
                AND gru.gru_id      = mat.mat_grupo

                INNER JOIN " . BD_ACADEMICA . ".academico_areas are 
                ON  are.institucion = mat.institucion
                AND are.year        = mat.year
                AND are.ar_id       = mate.mat_area

                LEFT JOIN " . BD_ACADEMICA . ".academico_boletin bol
                ON    bol.institucion = mat.institucion 
                AND   bol.year        = mat.year
                AND   bol_estudiante  = mat.mat_id
                AND   bol_carga       = car.car_id
                AND   bol_periodo             IN ($in_periodos2)

                LEFT JOIN " . BD_ACADEMICA . ".academico_grados_periodos per
                ON    per.institucion = mat.institucion 
                AND   per.year        = mat.year
                AND   per.gvp_grado   = mat.mat_grado
                AND   per.gvp_periodo = bol.bol_periodo

                LEFT JOIN " . BD_ACADEMICA . ".academico_clases cls 
                ON  cls.institucion       = bol.institucion
                AND cls.year              = bol.year
                AND cls.cls_id_carga      = car.car_id
                AND cls.cls_periodo       = bol.bol_periodo
                AND cls.cls_registrada    = 1

                LEFT JOIN " . BD_ACADEMICA . ".academico_nivelaciones niv 
                ON  niv.institucion        = mat.institucion
                AND niv.year               = mat.year
                AND niv.niv_cod_estudiante = mat.mat_id
                AND niv.niv_id_asg         = car.car_id

                LEFT JOIN " . BD_ACADEMICA . ".academico_ausencias aus 
                ON  aus.institucion       = bol.institucion
                AND aus.year              = bol.year
                AND aus.aus_id_clase      = cls.cls_id
                AND aus.aus_id_estudiante = mat.mat_id


                LEFT JOIN " . BD_DISCIPLINA . ".disiplina_nota disi 
                ON  disi.institucion       = bol.institucion
                AND disi.year              = bol.year
                AND disi.dn_cod_estudiante = bol_estudiante
                AND disi.dn_periodo        = bol.bol_periodo
                AND disi.dn_id_carga       = car.car_id

                ";
                if($traerIndicadores){
                $sql .="
                    INNER JOIN " . BD_ACADEMICA . ".academico_indicadores_carga indc 
                    ON  indc.institucion  = mat.institucion
                    AND indc.year         = mat.year
                    AND indc.ipc_periodo  = bol.bol_periodo
                    AND indc.ipc_carga    = bol.bol_carga

                    INNER JOIN " . BD_ACADEMICA . ".academico_indicadores ind 
                    ON ind.institucion    = mat.institucion
                    AND ind.year          = mat.year
                    AND ind.ind_id        = indc.ipc_indicador                 

                    LEFT JOIN " . BD_ACADEMICA . ".academico_indicadores_recuperacion indr 
                    ON  indr.institucion        = mat.institucion
                    AND indr.year               = mat.year
                    AND indr.rind_estudiante     = mat.mat_id
                    AND indr.rind_carga          = car.car_id
                    AND indr.rind_nota          > indr.rind_nota_original
                    AND indr.rind_indicador      = indc.ipc_indicador
                    AND indr.rind_periodo        = indc.ipc_periodo


                    LEFT JOIN  " . BD_ACADEMICA . ".vista_valores_indicadores_periodos_estudiantes  AS indv
                    ON   indv.institucion       = mat.institucion
                    AND  indv.year              = mat.year
                    AND  indv.act_periodo       = indc.ipc_periodo
                    AND  indv.cal_id_estudiante = bol.bol_estudiante
                    AND  indv.act_id_tipo       = indc.ipc_indicador
                    AND  indv.act_id_carga      = car.car_id";
                }                

                $sql .="

                LEFT JOIN (
				   SELECT 
                    bol2.bol_estudiante as est,
                    bol2.bol_carga as carga_calculada, 
                    avg(bol2.bol_nota) AS promedio_acumulado, 
                    MAX(bol2.bol_periodo) AS periodo_calculado

					FROM " . BD_ACADEMICA . ".academico_boletin bol2

                    INNER JOIN " . BD_ACADEMICA . ".academico_matriculas mat1
					ON   mat1.mat_id      = bol2.bol_estudiante
					AND  mat1.institucion = bol2.institucion 
                    AND  mat1.year        = bol2.year
					
                    
                    WHERE bol2.institucion = ".$config['conf_id_institucion']."
				    AND bol2.year          =  $year 
				    AND   bol2.bol_periodo IN ($in_periodos2)
                    AND   mat1.mat_grado   = '$grado'
                    AND   mat1.mat_grupo   = '$grupo'

					GROUP BY bol2.bol_estudiante,bol2.bol_carga
					
                    ) AS acum
				ON   acum.est              = bol.bol_estudiante
                AND  acum.carga_calculada  = car.car_id                    


                WHERE mat.mat_grado            = ?
                AND   mat.mat_grupo            = ?
                AND   mat.institucion          = ?
                
                $andEstudiante
                AND   mat.year                 = ?
                AND   mat.mat_eliminado        = 0
                AND ( mat.mat_estado_matricula = " . MATRICULADO . " OR mat.mat_estado_matricula=" . ASISTENTE . ") 


                ORDER BY $odenNombres mat.mat_id,are.ar_posicion,car.car_id,bol_periodo";
                if($traerIndicadores){
                $sql .=",ind.ind_id";
                }
        $parametros = [$grado, $grupo, (int)$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
 * Obtiene los datos del boletín para estudiantes de media técnica con opción a incluir indicadores.
 *
 * @param array  $periodos         Array con los periodos que se deben consultar.
 * @param string $year             Año académico para el cual se realiza la consulta. Si está vacío, se toma el año de la sesión actual.
 * @param array  $estudinates      Array con los IDs de los estudiantes cuyos datos se van a consultar.
 * @param bool   $traerIndicadores Indica si deben incluirse los indicadores en la consulta. Por defecto, es `false`.
 *
 * @return array Retorna el resultado de la consulta como un array. 
 *               - Si `$traerIndicadores` es `true`, incluye los indicadores asociados a los estudiantes.
 *               - Si es `false`, devuelve solo la información básica de los cursos de media técnica.
 */
    public static function datosBoletinMediaTecnica(
        array  $periodos,
        string $year,
        array  $estudinates,
        bool   $traerIndicadores=false,
    ) {
        global $conexion, $config;

        $year = empty($year) ?  $_SESSION["bd"] : $year;
        
        // Validar que los arrays no estén vacíos
        if(empty($periodos) || empty($estudinates)){
            // Retornar un resultado vacío si no hay datos
            $sql = "SELECT * FROM " . BD_ACADEMICA . ".vista_matriculas_cursos_individual WHERE 1=0";
            $parametros = [];
            $resultado = BindSQL::prepararSQL($sql, $parametros);
            return $resultado;
        }
        
        // Construir placeholders para IN clauses
        $placeholders_periodos = implode(', ', array_fill(0, count($periodos), '?'));
        $placeholders_estudiantes = implode(', ', array_fill(0, count($estudinates), '?'));
        
        if(!$traerIndicadores){
            $sql ="SELECT * FROM  " . BD_ACADEMICA . ".vista_matriculas_cursos_individual 
								WHERE  matcur_id_institucion       = ?
								AND    matcur_years                = ?
								AND    matcur_id_matricula         IN ($placeholders_estudiantes)
								AND    bol_periodo                 IN ($placeholders_periodos)";
        }else{
            $sql = "
            SELECT * FROM  " . BD_ACADEMICA . ".vista_matriculas_cursos_individual_indicadores 
								WHERE  matcur_id_institucion       = ?
								AND    matcur_years                = ?
								AND    matcur_id_matricula         IN ($placeholders_estudiantes)
								AND    ipc_periodo           IN ($placeholders_periodos)";
        }
        
        // Construir parámetros: institución, year, estudiantes, periodos
        $parametros = [(int)$config['conf_id_institucion'], $year];
        $parametros = array_merge($parametros, $estudinates);
        $parametros = array_merge($parametros, $periodos);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    
    public static function datosBoletinPeriodos(
        string  $grado,
        string  $grupo,
        array  $periodos,
        string  $year,
        string  $idEstudiante=""
    ) {
        global $conexion, $config;

        $year = empty($year) ?  $_SESSION["bd"] : $year;
        
        $andEstudiante="";

        if (!empty($idEstudiante)) {
            $andEstudiante = "AND   mat.mat_id  = '" . $idEstudiante."'";
        }
         // Preparar los placeholders para la consulta
         $in_periodos  = implode(', ', array_fill(0, count($periodos), '?'));
         $in_periodos2 = implode(', ', $periodos);
         $odenNombres    ='';
         switch ($config['conf_orden_nombre_estudiantes']) {
            case '1':
                $odenNombres = "mat_nombres,mat_nombre2,mat_primer_apellido,mat_segundo_apellido,";
                break;
            case '2':
                $odenNombres = "mat_primer_apellido,mat_segundo_apellido,mat_nombres,mat_nombre2,";
                break;
         }
         $cancelados = "";
         if ($config['conf_mostrar_estudiantes_cancelados'] == SI) {
            $cancelados = "OR mat.mat_estado_matricula =  ".CANCELADO ." " ;
         }
        $sql = "
                 SELECT                   
					are.ar_id,
                    gra.gra_nombre,
                    gru.gru_nombre, 
                    car.car_id,
                    car.car_director_grupo,
	                are.ar_id,
	                are.ar_nombre,
	                mate.mat_id as id_materia,
	                mate.mat_nombre,
	                car_ih,
	                mate.mat_valor,
	                bol.bol_periodo,
	                bol.bol_nota,
                    bol.bol_tipo,
                    bol.bol_observaciones_boletin,
                    disi.dn_id,
                    disi.dn_periodo,
                    disi.dn_cod_estudiante,
	                disi.dn_observacion,
                    aus.aus_ausencias,
                    docen.*,
                    mat.*
                   
                    
                FROM " . BD_ACADEMICA . ".academico_matriculas mat

                LEFT JOIN " . BD_ACADEMICA . ".academico_cargas car 
                ON  car.institucion = mat.institucion 
                AND car.year        = mat.year
                AND car.car_grupo   = mat.mat_grupo
                AND car.car_curso   = mat.mat_grado

                LEFT JOIN " . BD_GENERAL . ".usuarios docen
				ON  docen.institucion  = car.institucion
                AND docen.year         = car.year
				AND docen.uss_id       = car.car_docente
            
                INNER JOIN " . BD_ACADEMICA . ".academico_materias mate 
                ON  mate.institucion = car.institucion
                AND mate.year        = car.year
                AND mate.mat_id      = car.car_materia

                INNER JOIN " . BD_ACADEMICA . ".academico_grados gra 
                ON  gra.institucion = mat.institucion 
                AND gra.year        = mat.year
                AND gra.gra_id      = mat.mat_grado
								
			    INNER JOIN " . BD_ACADEMICA . ".academico_grupos gru 
                ON  gru.institucion = mat.institucion 
                AND gru.year        = mat.year
                AND gru.gru_id      = mat.mat_grupo

                INNER JOIN " . BD_ACADEMICA . ".academico_areas are 
                ON  are.institucion = car.institucion
                AND are.year        = car.year
                AND are.ar_id       = mate.mat_area

                LEFT JOIN " . BD_ACADEMICA . ".academico_boletin bol
                ON    bol.institucion = mat.institucion 
                AND   bol.year        = mat.year
                AND   bol_estudiante  = mat.mat_id
                AND   bol_carga       = car.car_id
                AND   bol_periodo     IN ($in_periodos2)

                LEFT JOIN " . BD_ACADEMICA . ".academico_clases cls 
                ON  cls.institucion       = bol.institucion
                AND cls.year              = bol.year
                AND cls.cls_id_carga      = car.car_id
                AND cls.cls_periodo       = bol.bol_periodo
                AND cls.cls_registrada    = 1


                LEFT JOIN " . BD_ACADEMICA . ".academico_ausencias aus 
                ON  aus.institucion       = bol.institucion
                AND aus.year              = bol.year
                AND aus.aus_id_clase      = cls.cls_id
                AND aus.aus_id_estudiante = mat.mat_id
              

                LEFT JOIN " . BD_DISCIPLINA . ".disiplina_nota disi 
                ON  disi.institucion       = bol.institucion
                AND disi.year              = bol.year
                AND disi.dn_cod_estudiante = bol_estudiante
                AND disi.dn_periodo        = bol.bol_periodo
                AND disi.dn_id_carga       = car.car_id
                    


                WHERE mat.mat_grado            = ?
                AND   mat.mat_grupo            = ?
                AND   mat.institucion          = ?
                $andEstudiante
                AND   mat.year                 = ?
                AND   mat.mat_eliminado        = 0
                AND ( mat.mat_estado_matricula = " . MATRICULADO . " OR mat.mat_estado_matricula=" . ASISTENTE . " $cancelados )
                
               
                ORDER BY  $odenNombres mat.mat_id,are.ar_posicion,car.car_id,bol.bol_periodo
                ";
        $parametros = [$grado, $grupo, (int)$config['conf_id_institucion'], $year];

        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }
    public static function determinarRango($valor ,$data) {
        foreach ($data as $array) {
            if ($valor >= $array['notip_desde'] && $valor <= $array['notip_hasta']) {
                return $array; // Devuelve el array que contiene el valor en su rango
            }
        }
        return $array=["notip_nombre"  => "N/A",
                        "notip_imagen" => "bajo.png"]; //
    }

/**
 * Determina el color asociado a una nota según si está aprobada o no, basado en la configuración global.
 *
 * @param float $valorNota Valor de la nota que se evaluará.
 *
 * @return string Devuelve un string que representa el color asociado:
 *                - Si la nota es menor a la mínima aprobatoria, devuelve el color de pérdida.
 *                - Si la nota es igual o mayor a la mínima aprobatoria, devuelve el color de ganancia.
 */
    public static function colorNota(float $valorNota): string {
        global  $config;
        $color='';
        if ($valorNota < $config['conf_nota_minima_aprobar']) {
            $color = $config['conf_color_perdida'];
        } elseif ($valorNota >= $config['conf_nota_minima_aprobar']) {
            $color = $config['conf_color_ganada'];
        }
        return $color; //
    }

/**
 * Ajusta el valor de una nota al número de decimales configurado globalmente.
 *
 * @param float|null $valorNota Valor de la nota a ajustar. Si es null, se asigna un valor por defecto de 0.
 *
 * @return string Devuelve la nota ajustada y formateada como cadena, con el número de decimales definido en la configuración global.
 */
    public static function notaDecimales(float|null $valorNota){
        global  $config;
        Utilidades::valordefecto($valorNota,0);
        $nota = round($valorNota, $config['conf_decimales_notas']);
        $nota = number_format($nota, $config['conf_decimales_notas']);
        return $nota;
    }

/**
 * Formatea una nota numérica en función de los tipos de notas definidos y la configuración global.
 *
 * @param float|null $valorNota  Valor de la nota a formatear. Puede ser null.
 * @param array      $tiposNotas Array con los tipos de notas (rangos y descripciones) utilizados para determinar el desempeño.
 *
 * @return float|string Devuelve la nota formateada. 
 *                      - Si la configuración es cualitativa, retorna una descripción (`string`).
 *                      - Si la configuración es cuantitativa, retorna el valor de la nota (`float`).
 */
    public static function formatoNota(float|null $valorNota,array $tiposNotas=[]): float|string {
        global  $config;       
        $notaResultado=0;
        $nota = self::notaDecimales($valorNota);
        if(!empty($tiposNotas)){
            if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
                $desempeno = self::determinarRango($nota, $tiposNotas);
                $notaResultado=$desempeno['notip_nombre'];
            }else{
                $notaResultado=$nota;
            }
        }else{
            $notaResultado=$nota;
        }
        
        return $notaResultado; //
    }

     /**
     * Genera un mensaje sobre el estado académico del estudiante basado en el periodo, 
     * el número de materias perdidas, el nombre del estudiante y su género.
     *
     * @param int    $periodo         Número del periodo escolar actual.
     * @param int    $materiasPerdidas Número de materias perdidas por el estudiante (por defecto 0).
     * @param string $estudainte      Nombre del estudiante.
     * @param string $genero          Género del estudiante (por defecto '126' para femenino, masculino usa UsuariosPadre::GENERO_MASCULINO).
     *
     * @return string Mensaje en formato HTML que indica si el estudiante fue promovido, debe nivelar materias, o no fue promovido.
     */
    public static function mensajeFinalEstudainte($periodo,$materiasPerdidas,$estudainte,$genero,$promedioGeneral = 0):string {
        global  $config;
        Utilidades::valordefecto($materiasPerdidas,0);
        Utilidades::valordefecto($genero,'126');
        
        $prefijo = ['EL', 'O'];
        if ($genero != UsuariosPadre::GENERO_MASCULINO) {
            $prefijo = ['LA', 'A'];
        }
        $plural ='';
        if ($materiasPerdidas > 1) {
            $plural = "S";
        }

        $msj = "";
        if ($periodo >= $config["conf_periodos_maximos"]) {
            if ($materiasPerdidas >= $config["conf_num_materias_perder_agno"])
                $msj = "<center> $prefijo[0] ESTUDIANTE " . $estudainte . " NO FUE PROMOVID$prefijo[1] AL GRADO SIGUIENTE ($materiasPerdidas) MATERIA$plural PERDIDA$plural</center>";
            elseif ($materiasPerdidas < $config["conf_num_materias_perder_agno"] and $materiasPerdidas > 0){
                if(($config['conf_id_institucion'] == NUEVO_GANDY || $config['conf_id_institucion'] == INTEGRADO_POPULAR) && $materiasPerdidas = 1){
                    $promedioGeneral = self::notaDecimales($promedioGeneral);
                    if ($promedioGeneral  < $config['conf_nota_minima_aprobar']) {
                        $msj = "<center> $prefijo[0] ESTUDIANTE " . $estudainte . " DEBE NIVELAR LA$plural ($materiasPerdidas) MATERIA$plural PERDIDA$plural</center>";
                    }else{
                        $msj = "<center> $prefijo[0] ESTUDIANTE " . $estudainte . " FUE PROMOVID$prefijo[1] AL GRADO SIGUIENTE</center>"; 
                    }
                }else{
                    $msj = "<center> $prefijo[0] ESTUDIANTE " . $estudainte . " DEBE NIVELAR LA$plural ($materiasPerdidas) MATERIA$plural PERDIDA$plural</center>";
                }
                
            }else
                $msj = "<center> $prefijo[0] ESTUDIANTE " . $estudainte . " FUE PROMOVID$prefijo[1] AL GRADO SIGUIENTE</center>";
        }
        
        return $msj; //
    }
    
    /**
     * Calcula el promedio de un área considerando porcentajes de materias según configuración.
     * 
     * Este método centraliza el cálculo de promedios de área, considerando:
     * - Si conf_agregar_porcentaje_asignaturas = 'SI' y hay mat_valor: promedio ponderado
     * - Si no: promedio simple (todas las materias valen lo mismo)
     * 
     * @param array  $config          Configuración de la institución
     * @param string $estudianteId    ID del estudiante
     * @param string $areaId          ID del área
     * @param array  $periodos        Array de períodos [1,2,3] o [1,2,3,4]
     * @param string $grado           ID del grado
     * @param string $grupo           ID del grupo
     * @param string $yearBd          Año académico (opcional)
     * @return array Array con promedios por período y promedio acumulado: 
     *               ['periodos' => [1 => 4.5, 2 => 4.8], 'acumulado' => 4.65]
     */
    public static function calcularPromedioAreaCompleto(
        array  $config,
        string $estudianteId,
        string $areaId,
        array  $periodos,
        string $grado,
        string $grupo,
        string $yearBd = ''
    ): array {
        global $conexion;
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $resultado = ['periodos' => [], 'acumulado' => 0];
        
        // Obtener cargas del área
        $cargas = CargaAcademica::traerCargasAreasPorCursoGrupo($config, $grado, $grupo, $areaId, $year);
        
        $usarPorcentajes = ($config['conf_agregar_porcentaje_asignaturas'] == 'SI');
        $sumaPorPeriodo = [];
        $sumaPorcentajesPorPeriodo = [];
        $cantidadMateriasPorPeriodo = [];
        
        // Inicializar arrays por período
        foreach ($periodos as $periodo) {
            $sumaPorPeriodo[$periodo] = 0;
            $sumaPorcentajesPorPeriodo[$periodo] = 0;
            $cantidadMateriasPorPeriodo[$periodo] = 0;
        }
        
        // Almacenar cargas en array para poder iterar múltiples veces si es necesario
        $cargasArray = [];
        while ($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)) {
            $cargasArray[] = $carga;
        }
        
        // Recorrer cargas y calcular promedios
        foreach ($cargasArray as $carga) {
            $matValor = !empty($carga['mat_valor']) ? floatval($carga['mat_valor']) : 100;
            
            foreach ($periodos as $periodo) {
                $notaBoletin = self::traerNotaBoletinCargaPeriodo($config, $periodo, $estudianteId, $carga['car_id'], $year);
                $nota = !empty($notaBoletin['bol_nota']) ? floatval($notaBoletin['bol_nota']) : 0;
                
                if ($nota > 0) {
                    if ($usarPorcentajes && $matValor > 0) {
                        // Promedio ponderado: suma(nota * porcentaje/100)
                        $sumaPorPeriodo[$periodo] += $nota * ($matValor / 100);
                        $sumaPorcentajesPorPeriodo[$periodo] += ($matValor / 100);
                    } else {
                        // Promedio simple
                        $sumaPorPeriodo[$periodo] += $nota;
                        $cantidadMateriasPorPeriodo[$periodo]++;
                    }
                }
            }
        }
        
        // Calcular promedio por período
        foreach ($periodos as $periodo) {
            if ($usarPorcentajes && $sumaPorcentajesPorPeriodo[$periodo] > 0) {
                $resultado['periodos'][$periodo] = $sumaPorPeriodo[$periodo] / $sumaPorcentajesPorPeriodo[$periodo];
            } elseif ($cantidadMateriasPorPeriodo[$periodo] > 0) {
                $resultado['periodos'][$periodo] = $sumaPorPeriodo[$periodo] / $cantidadMateriasPorPeriodo[$periodo];
            } else {
                $resultado['periodos'][$periodo] = 0;
            }
        }
        
        // Calcular promedio acumulado (promedio de promedios por período)
        $sumaAcumulada = 0;
        $cantidadPeriodos = 0;
        foreach ($resultado['periodos'] as $promedioPeriodo) {
            if ($promedioPeriodo > 0) {
                $sumaAcumulada += $promedioPeriodo;
                $cantidadPeriodos++;
            }
        }
        
        if ($cantidadPeriodos > 0) {
            $resultado['acumulado'] = $sumaAcumulada / $cantidadPeriodos;
        }
        
        return $resultado;
    }
    
    /**
     * Calcula el promedio de una materia considerando períodos y porcentajes de período.
     * 
     * Este método centraliza el cálculo de promedios de materia, considerando:
     * - Notas por período
     * - Porcentajes de período según configuración del grado
     * - Nivelaciones si aplican
     * 
     * @param array  $config          Configuración de la institución
     * @param string $estudianteId   ID del estudiante
     * @param string $cargaId         ID de la carga académica
     * @param array  $periodos        Array de períodos [1,2,3] o [1,2,3,4]
     * @param string $gradoId         ID del grado (para obtener porcentajes de período)
     * @param string $yearBd          Año académico (opcional)
     * @param bool   $incluirNivelaciones Si incluir nivelaciones en el cálculo
     * @return array Array con promedios por período, definitiva y nivelación:
     *               ['periodos' => [1 => 4.5, 2 => 4.8], 'definitiva' => 4.65, 'nivelacion' => 0]
     */
    public static function calcularPromedioMateriaCompleto(
        array  $config,
        string $estudianteId,
        string $cargaId,
        array  $periodos,
        string $gradoId,
        string $yearBd = '',
        bool   $incluirNivelaciones = true
    ): array {
        global $conexion;
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $resultado = ['periodos' => [], 'definitiva' => 0, 'nivelacion' => 0];
        
        $sumaNotasPonderadas = 0;
        $sumaPorcentajes = 0;
        
        // Obtener notas por período
        foreach ($periodos as $periodo) {
            $notaBoletin = self::traerNotaBoletinCargaPeriodo($config, $periodo, $estudianteId, $cargaId, $year);
            $nota = !empty($notaBoletin['bol_nota']) ? floatval($notaBoletin['bol_nota']) : 0;
            $resultado['periodos'][$periodo] = $nota;
            
            if ($nota > 0) {
                // Obtener porcentaje del período
                $porcentajePeriodo = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $gradoId, $periodo);
                $porcentaje = !empty($porcentajePeriodo['gvp_valor']) ? floatval($porcentajePeriodo['gvp_valor']) : (100 / count($periodos));
                
                // Calcular definitiva ponderada: suma(nota * porcentaje_periodo/100)
                $sumaNotasPonderadas += $nota * ($porcentaje / 100);
                $sumaPorcentajes += ($porcentaje / 100);
            }
        }
        
        // Calcular definitiva
        if ($sumaPorcentajes > 0) {
            $resultado['definitiva'] = $sumaNotasPonderadas / $sumaPorcentajes;
        }
        
        // Verificar nivelación si aplica
        if ($incluirNivelaciones) {
            $nivelacion = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $estudianteId, $cargaId, $year);
            if ($nivelacion && mysqli_num_rows($nivelacion) > 0) {
                $datosNivelacion = mysqli_fetch_array($nivelacion, MYSQLI_BOTH);
                $notaNivelacion = !empty($datosNivelacion['niv_nota']) ? floatval($datosNivelacion['niv_nota']) : 0;
                if ($notaNivelacion > $resultado['definitiva']) {
                    $resultado['nivelacion'] = $notaNivelacion;
                    $resultado['definitiva'] = $notaNivelacion;
                }
            }
        }
        
        return $resultado;
    }
    
    /**
     * Calcula el promedio general de un estudiante considerando todas sus áreas.
     * 
     * Este método calcula el promedio general del estudiante basado en los promedios
     * de todas sus áreas, considerando la configuración de porcentajes.
     * 
     * @param array  $config          Configuración de la institución
     * @param string $estudianteId   ID del estudiante
     * @param array  $periodos        Array de períodos [1,2,3] o [1,2,3,4]
     * @param string $grado          ID del grado
     * @param string $grupo           ID del grupo
     * @param string $yearBd          Año académico (opcional)
     * @return array Array con promedios por período y promedio general:
     *               ['periodos' => [1 => 4.5, 2 => 4.8], 'general' => 4.65]
     */
    public static function calcularPromedioGeneralEstudiante(
        array  $config,
        string $estudianteId,
        array  $periodos,
        string $grado,
        string $grupo,
        string $yearBd = ''
    ): array {
        global $conexion;
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $resultado = ['periodos' => [], 'general' => 0];
        
        // Obtener todas las áreas del curso/grupo
        $areas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $grado, $grupo, $year);
        $areasUnicas = [];
        
        while ($area = mysqli_fetch_array($areas, MYSQLI_BOTH)) {
            if (!in_array($area['ar_id'], $areasUnicas)) {
                $areasUnicas[] = $area['ar_id'];
            }
        }
        
        $sumaPorPeriodo = [];
        $cantidadAreasPorPeriodo = [];
        
        // Inicializar arrays por período
        foreach ($periodos as $periodo) {
            $sumaPorPeriodo[$periodo] = 0;
            $cantidadAreasPorPeriodo[$periodo] = 0;
        }
        
        // Calcular promedio por área y acumular
        foreach ($areasUnicas as $areaId) {
            $promedioArea = self::calcularPromedioAreaCompleto($config, $estudianteId, $areaId, $periodos, $grado, $grupo, $year);
            
            foreach ($periodos as $periodo) {
                if (isset($promedioArea['periodos'][$periodo]) && $promedioArea['periodos'][$periodo] > 0) {
                    $sumaPorPeriodo[$periodo] += $promedioArea['periodos'][$periodo];
                    $cantidadAreasPorPeriodo[$periodo]++;
                }
            }
        }
        
        // Calcular promedio general por período
        foreach ($periodos as $periodo) {
            if ($cantidadAreasPorPeriodo[$periodo] > 0) {
                $resultado['periodos'][$periodo] = $sumaPorPeriodo[$periodo] / $cantidadAreasPorPeriodo[$periodo];
            } else {
                $resultado['periodos'][$periodo] = 0;
            }
        }
        
        // Calcular promedio general acumulado
        $sumaGeneral = 0;
        $cantidadPeriodos = 0;
        foreach ($resultado['periodos'] as $promedioPeriodo) {
            if ($promedioPeriodo > 0) {
                $sumaGeneral += $promedioPeriodo;
                $cantidadPeriodos++;
            }
        }
        
        if ($cantidadPeriodos > 0) {
            $resultado['general'] = $sumaGeneral / $cantidadPeriodos;
        }
        
        return $resultado;
    }
    
}

