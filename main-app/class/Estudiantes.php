<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH."/main-app/class/servicios/MediaTecnicaServicios.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/BindSQL.php");

class Estudiantes {

    /**
     * Esta función lista estudiantes según varios parámetros.
     *
     * @param int $eliminados - Indica si se deben incluir estudiantes eliminados (0 o 1).
     * @param string $filtroAdicional - Filtros adicionales para la consulta SQL.
     * @param string $filtroLimite - Límite de resultados para la consulta SQL.
     * @param mixed $cursoActual - Información sobre el curso actual (puede ser nulo).
     * @param string $valueIlike - Valor String que se utilizara para biuscar por cualuqier parametro definido (puede ser nulo).
     *
     * @return mysqli_result - Un array con los resultados de la consulta.
     */
    public static function listarEstudiantes(
        int    $eliminados      = 0, 
        string $filtroAdicional = '', 
        string $filtroLimite    = 'LIMIT 0, 2000',
        $cursoActual=null,
        $valueIlike=null
    )
    {
        global $config, $arregloModulos;
        $tipoGrado = $cursoActual ? $cursoActual["gra_tipo"] : GRADO_GRUPAL;
        $resultado = [];
        if(!empty($valueIlike)){
            $busqueda=$valueIlike;
            $filtroAdicional .= " AND (
                mat_id LIKE '%".$busqueda."%' 
                OR mat_nombres LIKE '%".$busqueda."%' 
                OR mat_nombre2 LIKE '%".$busqueda."%' 
                OR mat_primer_apellido LIKE '%".$busqueda."%' 
                OR mat_segundo_apellido LIKE '%".$busqueda."%' 
                OR mat_documento LIKE '%".$busqueda."%' 
                OR mat_email LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_segundo_apellido), TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_primer_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_primer_apellido), TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), '', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_primer_apellido), '', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombres), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombres)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_nombre2)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_segundo_apellido), ' ', TRIM(mat_primer_apellido)) LIKE '%".$busqueda."%'
                OR CONCAT(TRIM(mat_nombre2), ' ', TRIM(mat_segundo_apellido)) LIKE '%".$busqueda."%'
            )";
        }
        try {
            if( $tipoGrado == GRADO_GRUPAL || !array_key_exists(10, $arregloModulos) ){
                $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
                LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
                LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
                LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
                LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
                LEFT JOIN ".BD_ADMIN.".localidad_ciudades ON ciu_id=mat.mat_lugar_nacimiento
                WHERE mat.mat_eliminado IN (0, '".$eliminados."') AND mat.institucion=? AND mat.year=?
                {$filtroAdicional}
                ORDER BY mat.mat_grado, mat.mat_grupo, mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres
                {$filtroLimite}";
        
                $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
                
                $resultado = BindSQL::prepararSQL($sql, $parametros);
            }else{
                $parametros = [
                    'matcur_id_curso'=>$cursoActual["gra_id"],
                    'matcur_id_institucion'=>$config['conf_id_institucion'],
                    'limite'=>$filtroLimite,
                    'arreglo'=>false
                ];
                $resultado = MediaTecnicaServicios::listarEstudiantes($parametros);
                }
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Función para listar estudiantes en grados.
     *
     * @param string $filtroAdicional Filtro adicional para la consulta SQL.
     * @param string $filtroLimite Filtro de límite para la consulta SQL.
     * @param mixed $cursoActual Curso actual (puede ser nulo).
     * @param int $grupoActual Grupo actual (predeterminado: 1).
     * @param string $yearBd Año de la base de datos (predeterminado: vacío, se toma de la sesión si no se proporciona).
     *
     * @return mysqli_result Arreglo con los resultados de la consulta.
     */
    public static function listarEstudiantesEnGrados(
        string $filtroAdicional = '', 
        string $filtroLimite    = 'LIMIT 0, 2000',
        $cursoActual=null,
        $grupoActual=1,
        string $yearBd    = ''
    )
    {
        global $config;
        $tipoGrado=$cursoActual?$cursoActual["gra_tipo"]:GRADO_GRUPAL;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            if($tipoGrado==GRADO_GRUPAL){
                $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
                LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
                INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
                INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
                LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
                WHERE mat.mat_eliminado = 0 AND mat.institucion=? AND mat.year=?
                {$filtroAdicional}
                ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres
                {$filtroLimite}";
        
                $parametros = [$config['conf_id_institucion'], $year];
                
                $resultado = BindSQL::prepararSQL($sql, $parametros);
            }else{
                $parametros = [
                    'matcur_id_curso'=>$cursoActual["gra_id"],
                    'matcur_id_grupo'=>$grupoActual,
                    'matcur_id_institucion'=>$config['conf_id_institucion'],
                    'limite'=>$filtroLimite,
                    'arreglo'=>false
                ];
                $resultado = MediaTecnicaServicios::listarEstudiantes($parametros,$year);
            }
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Función para listar estudiantes con notas faltantes.
     *
     * @param string $carga      ID de la carga académica.
     * @param string $periodo    Período académico.
     * @param string $tipoGrado  Tipo de grado (opcional, por defecto es GRADO_GRUPAL).
     *
     * @return mysqli_result     Resultado de la consulta con la información de los estudiantes.
     */
    public static function listarEstudiantesNotasFaltantes(
        string $carga, 
        string $periodo,
        string $tipoGrado=GRADO_GRUPAL,
    )
    {
        global $config;
        $resultado = [];

        if($tipoGrado==GRADO_GRUPAL){
            $sqlString= "SELECT mat.*, sum(act_valor) as acumulado FROM ".BD_ACADEMICA.".academico_matriculas mat
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_id=? AND car.institucion=mat.institucion AND car.year=mat.year
            INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_estudiante=mat.mat_id AND aac.institucion=mat.institucion AND aac.year=mat.year 
            LEFT JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad and aa.act_id_carga=car_id and aa.act_periodo=? and aa.act_registrada=1 and aa.act_estado=1 AND aa.institucion=mat.institucion AND aa.year=mat.year
            WHERE mat.mat_eliminado=0 AND (mat.mat_estado_matricula=1 OR mat.mat_estado_matricula=2) AND mat.mat_grado=car_curso AND mat.mat_grupo=car_grupo AND mat.institucion=? AND mat.year=?
            GROUP BY mat.mat_id
            HAVING acumulado < ".PORCENTAJE_MINIMO_GENERAR_INFORME." OR acumulado IS NULL
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres";
        }else{
            $sqlString= "SELECT mat.*, sum(act_valor) as acumulado FROM ".BD_ADMIN.".mediatecnica_matriculas_cursos
            INNER JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat_id=matcur_id_matricula AND mat.mat_eliminado=0 AND (mat.mat_estado_matricula=1 OR mat.mat_estado_matricula=2) AND mat.institucion=matcur_id_institucion AND mat.year=matcur_years
            INNER JOIN ".BD_ACADEMICA.".academico_cargas car ON car_id=? AND car.institucion=matcur_id_institucion AND car.year=matcur_years
            INNER JOIN ".BD_ACADEMICA.".academico_calificaciones aac ON aac.cal_id_estudiante=mat.mat_id AND aac.institucion=matcur_id_institucion AND aac.year=matcur_years 
            LEFT JOIN ".BD_ACADEMICA.".academico_actividades aa ON aa.act_id=aac.cal_id_actividad and aa.act_id_carga=car_id and aa.act_periodo=? and aa.act_registrada=1 and aa.act_estado=1 AND aa.institucion=matcur_id_institucion AND aa.year=matcur_years
            WHERE matcur_id_curso=car_curso AND matcur_id_grupo=car_grupo AND matcur_estado='".ACTIVO."' AND matcur_id_institucion=? AND matcur_years=?
            GROUP BY mat.mat_id
            HAVING acumulado < ".PORCENTAJE_MINIMO_GENERAR_INFORME." OR acumulado IS NULL
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres";
        }
    
        $parametros = [$carga, $periodo, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sqlString, $parametros);

        return $resultado;
    }

    /**
     * Obtiene la lista de estudiantes para los docentes con los filtros proporcionados.
     *
     * @param string $filtroDocentes Filtro adicional para docentes.
     * @param string $filtroLimite Filtro adicional para limitar resultados.
     *
     * @return mysqli_result|false Resultado de la consulta o false si hay un error.
     */
    public static function listarEstudiantesParaDocentes(string $filtroDocentes = '',string $filtroLimite = '')
    {
        global $config;
        $resultado = [];

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
            LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
            WHERE mat.mat_eliminado=0 AND mat.institucion=? AND mat.year=? 
            AND (mat.mat_estado_matricula=1 OR mat.mat_estado_matricula=2)
            {$filtroDocentes}
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres
            {$filtroLimite}";
    
            $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
            
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene los datos de un estudiante según su identificación, número de matrícula o documento.
     *
     * @param int|string $estudiante Identificación, número de matrícula o documento del estudiante.
     * @param string $yearBd Año de la base de datos (opcional).
     *
     * @return array|false Arreglo asociativo con los datos del estudiante o false si no se encuentra.
     */
    public static function obtenerDatosEstudiante($estudiante = 0, $yearBd    = '')
    {

        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $doctSinPuntos = strpos($estudiante, '.') == true ? str_replace('.', '', $estudiante) : $estudiante;
        $doctConPuntos = strpos($estudiante, '.') !== true && is_numeric($estudiante) ? str_replace('.', '', $estudiante) : $estudiante;

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
            LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
            WHERE (mat.mat_id=? || (mat.mat_documento =? OR mat.mat_documento =?) || mat.mat_matricula=?) AND mat.mat_eliminado=0 AND mat.institucion=? AND mat.year=?";
    
            $parametros = [$estudiante, $doctSinPuntos, $doctConPuntos, $estudiante, $config['conf_id_institucion'], $year];
            $consulta = BindSQL::prepararSQL($sql, $parametros);
            $num = mysqli_num_rows($consulta);
            if($num == 0){
                echo "Estás intentando obtener datos de un estudiante que no existe: ".$estudiante."<br>";
            }
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;

    }

    /**
     * Este metodo me forma el nombre completo de un estudiante
     * @param array $estudiante
     * 
     * @return string $nombre
     */
    public static function NombreCompletoDelEstudiante(array $estudiante){
        
        $nombre=$estudiante['mat_nombres'];
        if(!empty($estudiante['mat_nombre2'])){
            $nombre.=" ".$estudiante['mat_nombre2'];
        }
        if(!empty($estudiante['mat_segundo_apellido'])){
            $nombre=$estudiante['mat_segundo_apellido']." ".$nombre;
        }
        if(!empty($estudiante['mat_primer_apellido'])){
            $nombre=$estudiante['mat_primer_apellido']." ".$nombre;
        }
        return strtoupper($nombre);
    }

    /**
     * Este metodo me lista los acudidos de un usuario acuiente
     * @param string $acudiente
     * 
     * @return mysqli_result $resultado
     */
    public static function listarEstudiantesParaAcudientes($acudiente)
    {
        global $config;
        $resultado = [];

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
            LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
            INNER JOIN ".BD_GENERAL.".usuarios_por_estudiantes upe ON upe.upe_id_estudiante=mat.mat_id AND upe.upe_id_usuario=? AND upe.institucion=mat.institucion AND upe.year=mat.year
            WHERE mat.mat_eliminado=0 AND mat.institucion=? AND mat.year=?
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres";
    
            $parametros = [$acudiente, $config['conf_id_institucion'], $_SESSION["bd"]];
            
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Este metodo me lista los estudiante para un estudiante
     * @param string $filtroEstudiante
     * @param array $cursoActual
     * @param string $grupoActual
     * 
     * @return mysqli_result $resultado
     */
    public static function listarEstudiantesParaEstudiantes(string $filtroEstudiantes = '', $cursoActual=null, $grupoActual=1)
    {
        global $config;
        $resultado = [];
        $tipoGrado=$cursoActual?$cursoActual["gra_tipo"]:GRADO_GRUPAL;
        try {
             if($tipoGrado==GRADO_GRUPAL){
                $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
                LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
                LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
                LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
                LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
                WHERE mat.mat_eliminado=0 AND mat.institucion=? AND mat.year=?
                AND (mat.mat_estado_matricula=1 OR mat.mat_estado_matricula=2)
                {$filtroEstudiantes}
                ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres";
        
                $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
                
                $resultado = BindSQL::prepararSQL($sql, $parametros);
            } else{
                $parametros = [
                      'matcur_id_curso'=>$cursoActual["gra_id"],
                      'matcur_id_grupo'=>$grupoActual,
                      'matcur_id_institucion'=>$config['conf_id_institucion'],
                      'and'=>'AND (mat_estado_matricula=1 OR mat_estado_matricula=2)',
                      'arreglo'=>false
                  ];
                  $resultado = MediaTecnicaServicios::listarEstudiantes($parametros);
                  
              }
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Obtiene los datos de un estudiante según su ID de usuario.
     *
     * @param int $estudianteIdUsuario ID de usuario del estudiante.
     *
     * @return array Arreglo asociativo con los datos del estudiante o un arreglo vacío si no se encuentra.
     */
    public static function obtenerDatosEstudiantePorIdUsuario($estudianteIdUsuario = 0)
    {

        global $config;
        $resultado = [];

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
            LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
            WHERE mat.mat_id_usuario=? AND mat.mat_eliminado=0 AND mat.institucion=? AND mat.year=?";
    
            $parametros = [$estudianteIdUsuario, $config['conf_id_institucion'], $_SESSION["bd"]];
            
            $consulta = BindSQL::prepararSQL($sql, $parametros);
            $num = mysqli_num_rows($consulta);
            if($num == 0){
                return $resultado;
            }
            $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;

    }

    /**
     * Este metodo me valida la existencia de un estudiante
     * @param string $estudiante
     * @param string $yearBd
     * 
     * @return int $num
     */
    public static function validarExistenciaEstudiante(
        $estudiante = 0,
        $BD    = '',
        string $yearBd    = ''
    ){

        global $config;
        $num = 0;
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $doctSinPuntos = strpos($estudiante, '.') == true ? str_replace('.', '', $estudiante) : $estudiante;
        $doctConPuntos = strpos($estudiante, '.') !== true && is_numeric($estudiante) ? str_replace('.', '', $estudiante) : $estudiante;

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas
            WHERE (mat_id=? || (mat_documento =? OR mat_documento =?)) AND mat_eliminado=0 AND institucion=? AND year=?";
    
            $parametros = [$estudiante, $doctSinPuntos, $doctConPuntos, $config['conf_id_institucion'], $year];
            
            $consulta = BindSQL::prepararSQL($sql, $parametros);
            $num = mysqli_num_rows($consulta);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $num;

    }

    /**
     * Este metodo me lista los estudiante para la planillas
     * @param int $eliminados
     * @param string $filtroAdicional
     * @param string $yearBd
     * 
     * @return mysqli_result $resultado
     */
    public static function listarEstudiantesParaPlanillas(
        int    $eliminados      = 0, 
        string $filtroAdicional = '', 
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
            LEFT JOIN ".BD_ADMIN.".opciones_generales ON ogen_id=mat.mat_genero
            LEFT JOIN ".BD_ADMIN.".localidad_ciudades ON ciu_id=mat.mat_lugar_nacimiento
            WHERE mat.mat_eliminado IN (0, '".$eliminados."') AND mat.institucion=? AND mat.year=?
            {$filtroAdicional}
            ORDER BY mat.mat_grado, mat.mat_grupo, mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres";
    
            $parametros = [$config['conf_id_institucion'], $year];
            
            $resultado = BindSQL::prepararSQL($sql, $parametros);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Este metodo me ordena el nombre de un estudiante
     * @param array $estudiante
     * @param int $orden
     * 
     * @return string $nombre
     */
    public static function NombreCompletoDelEstudianteParaInformes(array $estudiante, $orden){
        
        $nombre=$estudiante['mat_nombres'];
        if(!empty($estudiante['mat_nombre2'])){
            $nombre.=" ".$estudiante['mat_nombre2'];
        }
        if(!empty($estudiante['mat_primer_apellido'])){
            $nombre.=" ".$estudiante['mat_primer_apellido'];
        }
        if(!empty($estudiante['mat_segundo_apellido'])){
            $nombre.=" ".$estudiante['mat_segundo_apellido'];
        }
        
        if($orden==2){
            $nombre=$estudiante['mat_nombres'];
            if(!empty($estudiante['mat_nombre2'])){
                $nombre.=" ".$estudiante['mat_nombre2'];
            }
            if(!empty($estudiante['mat_segundo_apellido'])){
                $nombre=$estudiante['mat_segundo_apellido']." ".$nombre;
            }
            if(!empty($estudiante['mat_primer_apellido'])){
                $nombre=$estudiante['mat_primer_apellido']." ".$nombre;
            }
        }
        return strtoupper($nombre);
    }

    /**
     * Este metodo me actualiza el estado de un estudiante
     * @param string $idEstudiante
     * @param int $estadoMatricula
     */
    public static function ActualizarEstadoMatricula($idEstudiante, $estadoMatricula)
    {
        global $config;

        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET mat_estado_matricula=? WHERE mat_id=? AND institucion=? AND year=?";

        $parametros = [$estadoMatricula, $idEstudiante, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
     * Este método registra las matrículas retiradas o restauradas
     * @param string $idEstudiante
     * @param string $motivo
     * @param array $config
     * @param mysqli $conexion
     */
    public static function retirarRestaurarEstudiante($idEstudiante, $motivo, $config, $conexion, $conexionPDO,$finalizarTransacion=true)
    {
        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas_retiradas');

        $sql = "INSERT INTO " . BD_ACADEMICA . ".academico_matriculas_retiradas (matret_id, matret_estudiante, matret_fecha, matret_motivo, matret_responsable, institucion, year) VALUES (?, ?, NOW(), ?, ?, ?, ?)";

        $parametros = [$codigo, $idEstudiante, $motivo, $_SESSION["id"], $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros,$finalizarTransacion);
    }

    /**
     * Este metodo me trae todos los estudiantes matriculados
     * @param string $filtro
     * @param string $yearBD
     * 
     * @return mysqli_result $resultado
     */
    public static function estudiantesMatriculados(
        string    $filtro      = '',
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];
        $filtroCancelados = $config['conf_mostrar_estudiantes_cancelados'] == NO ? "AND mat.mat_estado_matricula IN (".MATRICULADO.", ".ASISTENTE.")" : " AND mat.mat_estado_matricula IN (".MATRICULADO.", ".ASISTENTE.", ".CANCELADO.")";

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat 
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON mat.mat_grupo=gru.gru_id AND gru.institucion=mat.institucion AND gru.year=mat.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON mat.mat_grado=gra_id AND gra.institucion=mat.institucion AND gra.year=mat.year 
        WHERE mat.mat_eliminado=0 {$filtroCancelados} AND mat.institucion=? AND mat.year=? {$filtro} 
        GROUP BY mat.mat_id
        ORDER BY mat.mat_grupo, mat.mat_primer_apellido";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * este metodo me trae los datos de un estudiante para usar en boletines
     * @param string $estudiante
     * @param string $yearBD
     * 
     * @return mysqli_result $resultado
     */
    public static function obtenerDatosEstudiantesParaBoletin(
        string $estudiante      = "",
        string $yearBd    = ''
    )
    {
        global $config;
        $resultado = [];
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas am
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON am.mat_grupo=gru.gru_id AND gru.institucion=am.institucion AND gru.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON am.mat_grado=gra_id AND gra.institucion=am.institucion AND gra.year=am.year 
        WHERE am.mat_id=? AND am.institucion=? AND am.year=?";

        $parametros = [$estudiante, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
     * Este metodo me valida si un documento esta repetido
     * @param string $documento
     * @param string $idEstudiante
     * 
     * @return int $num
     */
    public static function validarRepeticionDocumento($documento, $idEstudiante)
    {

        global $conexion, $config;
        $num = 0;

        try {
            $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas
            WHERE mat_id!=? AND mat_documento=? AND mat_eliminado=0 AND institucion=? AND year=?";
    
            $parametros = [$idEstudiante, $documento, $config['conf_id_institucion'], $_SESSION["bd"]];
            
            $consulta = BindSQL::prepararSQL($sql, $parametros);
            $num = mysqli_num_rows($consulta);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $num;

    }
    
    /**
     * Este metodo me lista los estudiante MT de un docente
     * @param array $datosCargaActual
     * 
     * @return mysqli_result $resultado
     */
    public static function listarEstudiantesParaDocentesMT(array $datosCargaActual = [])
    {
        global $conexion, $baseDatosServicios, $config;
        $resultado = [];

        try {
            $resultado = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".mediatecnica_matriculas_cursos
            LEFT JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_eliminado=0 AND (mat.mat_estado_matricula=1 OR mat.mat_estado_matricula=2) AND mat.mat_id=matcur_id_matricula AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=matcur_id_curso AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=matcur_id_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".$baseDatosServicios.".opciones_generales ON ogen_id=mat.mat_genero
            WHERE matcur_id_curso='".$datosCargaActual['car_curso']."' AND matcur_id_grupo='".$datosCargaActual['car_grupo']."' AND matcur_estado='".ACTIVO."' AND matcur_id_institucion='".$config['conf_id_institucion']."'
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres;
            ");
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $resultado;
    }

    /**
     * Este metodo me cuenta los estudiante de una carga
     * @param array $datosCargaActual
     * 
     * @return int $cantidad
     */
    public static function contarEstudiantesParaDocentesMT(array $datosCargaActual = [])
    {
        global $conexion, $baseDatosServicios, $config;
        $cantidad = 0;

        try {
            $consulta = mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".mediatecnica_matriculas_cursos
            LEFT JOIN ".BD_ACADEMICA.".academico_matriculas mat ON mat.mat_eliminado=0 AND (mat.mat_estado_matricula=1 OR mat.mat_estado_matricula=2) AND mat.mat_grupo='".$datosCargaActual['car_grupo']."' AND mat.mat_id=matcur_id_matricula AND mat.institucion={$config['conf_id_institucion']} AND mat.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=matcur_id_curso AND gra.institucion={$config['conf_id_institucion']} AND gra.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=matcur_id_grupo AND gru.institucion={$config['conf_id_institucion']} AND gru.year={$_SESSION["bd"]}
            LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
            LEFT JOIN ".$baseDatosServicios.".opciones_generales ON ogen_id=mat.mat_genero
            WHERE matcur_id_curso='".$datosCargaActual['car_curso']."' AND matcur_id_grupo='".$datosCargaActual['car_grupo']."' AND matcur_estado='".ACTIVO."' AND matcur_id_institucion='".$config['conf_id_institucion']."'
            ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres;
            ");
            $cantidad = mysqli_num_rows($consulta);
        } catch (Exception $e) {
            echo "Excepción catpurada: ".$e->getMessage();
            exit();
        }

        return $cantidad;
    }

    /**
     * Este metodo me escoge la consulta segun el tipo de curso
     * 
     * @param array $datosCargaActual
     * 
     * @return mysqli_result $consulta
     */
    public static function escogerConsultaParaListarEstudiantesParaDocentes(array $datosCargaActual = [])
    {
        $filtroDocentesParaListarEstudiantes = " AND mat_grado='".$datosCargaActual['car_curso']."' AND mat_grupo='".$datosCargaActual['car_grupo']."'";

        if($datosCargaActual['gra_tipo'] == GRADO_INDIVIDUAL) {
            $consulta = Estudiantes::listarEstudiantesParaDocentesMT($datosCargaActual);
        } else {
            $consulta = Estudiantes::listarEstudiantesParaDocentes($filtroDocentesParaListarEstudiantes);
        }

        return $consulta;
    }

    /**
     * Este metodo me lista todos los estudiantes
     * 
     * @param string $where
     * 
     * @return mysqli_result $consulta
     */
    public static function reporteEstadoEstudiantes($where="")
    {

        global $config;

        $sql = "SELECT mat_matricula, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_inclusion, mat_extranjero, mat_documento, uss_usuario, uss_email, uss_celular, uss_telefono, gru_nombre, gra_nombre, og.ogen_nombre as Tipo_est, mat_id,
        IF(mat_acudiente is null,'No',uss_nombre) as nom_acudiente,
        IF(mat_foto is null,'No','Si') as foto, 
        og2.ogen_nombre as genero, og3.ogen_nombre as religion, og4.ogen_nombre as estrato, og5.ogen_nombre as tipoDoc,
        CASE mat_estado_matricula 
            WHEN 1 THEN 'Matriculado' 
            WHEN 2 THEN 'Asistente' 
            WHEN 3 THEN 'Cancelado' 
            WHEN 4 THEN 'No matriculado'
            WHEN 5 THEN 'En inscripción' 
        END AS estado
        FROM ".BD_ACADEMICA.".academico_matriculas am 
        LEFT JOIN ".BD_ACADEMICA.".academico_grupos ag ON am.mat_grupo=ag.gru_id AND ag.institucion=am.institucion AND ag.year=am.year
        LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra.gra_id=am.mat_grado AND gra.institucion=am.institucion AND gra.year=am.year
        LEFT JOIN ".BD_ADMIN.".opciones_generales og ON og.ogen_id=am.mat_tipo
        LEFT JOIN ".BD_ADMIN.".opciones_generales og2 ON og2.ogen_id=am.mat_genero
        LEFT JOIN ".BD_ADMIN.".opciones_generales og3 ON og3.ogen_id=am.mat_religion
        LEFT JOIN ".BD_ADMIN.".opciones_generales og4 ON og4.ogen_id=am.mat_estrato
        LEFT JOIN ".BD_ADMIN.".opciones_generales og5 ON og5.ogen_id=am.mat_tipo_documento
        LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss.institucion=am.institucion AND uss.year=am.year AND (uss.uss_id=am.mat_acudiente or am.mat_acudiente is null)
        WHERE am.institucion=? AND am.year=? {$where}
        GROUP BY mat_id
        ORDER BY mat_primer_apellido,mat_estado_matricula;";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        return $consulta;

    }

    /**
     * Esta función permite insertar los datos de los estudiantes en
     * las matrículas
     * 
     * @param $conexionPDO ConexionPDO
     * @param $POST Array
     * @param $fechaNacimiento String
     * @param $procedencia String
     * @param $pasosMatricula String
     */
    public static function insertarEstudiantes($conexionPDO, $POST, $idEstudianteU, $result_numMat = '', $procedencia = '', $idAcudiente = '')
    {
        global $config, $conexion;
        $codigoMAT = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas');

        $tipoD = isset($POST["tipoD"]) ? $POST["tipoD"] : "";
        $nDoc = isset($POST["nDoc"]) ? $POST["nDoc"] : "";
        $religion = isset($POST["religion"]) ? $POST["religion"] : "";
        $email = isset($POST["email"]) ? strtolower($POST["email"]) : "";
        $direccion = isset($POST["direccion"]) ? $POST["direccion"] : "";
        $barrio = isset($POST["barrio"]) ? $POST["barrio"] : "";
        $telefono = isset($POST["telefono"]) ? $POST["telefono"] : "";
        $celular = isset($POST["celular"]) ? $POST["celular"] : "";
        $estrato = isset($POST["estrato"]) ? $POST["estrato"] : "";
        $genero = isset($POST["genero"]) ? $POST["genero"] : "";
        $apellido1 = isset($POST["apellido1"]) ? mysqli_real_escape_string($conexion,$POST["apellido1"]) : "";
        $apellido2 = isset($POST["apellido2"]) ? mysqli_real_escape_string($conexion,$POST["apellido2"]) : "";
        $nombres = isset($POST["nombres"]) ? mysqli_real_escape_string($conexion,$POST["nombres"]) : "";
        $grado = isset($POST["grado"]) ? $POST["grado"] : "";
        $grupo = isset($POST["grupo"]) ? $POST["grupo"] : "";
        $tipoEst = isset($POST["tipoEst"]) ? $POST["tipoEst"] : "";
        $lugarD = isset($POST["lugarD"]) ? $POST["lugarD"] : "";
        $matestM = isset($POST["matestM"]) ? $POST["matestM"] : "";
        $folio = isset($POST["folio"]) ? $POST["folio"] : "";
        $codTesoreria = isset($POST["codTesoreria"]) ? $POST["codTesoreria"] : "";
        $va_matricula = isset($POST["va_matricula"]) ? $POST["va_matricula"] : "";
        $inclusion = isset($POST["inclusion"]) ? $POST["inclusion"] : "";
        $extran = isset($POST["extran"]) ? $POST["extran"] : "";
        $tipoSangre = isset($POST["tipoSangre"]) ? $POST["tipoSangre"] : "";
        $eps = isset($POST["eps"]) ? $POST["eps"] : "";
        $celular2 = isset($POST["celular2"]) ? $POST["celular2"] : "";
        $ciudadR = isset($POST["ciudadR"]) ? $POST["ciudadR"] : "";
        $nombre2 = isset($POST["nombre2"]) ? mysqli_real_escape_string($conexion,$POST["nombre2"]) : "";
        $fNac = isset($POST["fNac"]) ? $POST["fNac"] : "";
        $tipoMatricula = isset($_POST["tipoMatricula"]) ? $POST["tipoMatricula"] : "";

        try{

            $consulta = "INSERT INTO ".BD_ACADEMICA.".academico_matriculas(
                mat_id, mat_matricula, mat_fecha, mat_tipo_documento, 
                mat_documento, mat_religion, mat_email, 
                mat_direccion, mat_barrio, mat_telefono, 
                mat_celular, mat_estrato, mat_genero, 
                mat_fecha_nacimiento, mat_primer_apellido, mat_segundo_apellido, 
                mat_nombres, mat_grado, mat_grupo, 
                mat_tipo, mat_lugar_nacimiento, mat_lugar_expedicion, 
                mat_acudiente, mat_estado_matricula, mat_id_usuario, 
                mat_folio, mat_codigo_tesoreria, mat_valor_matricula, 
                mat_inclusion, mat_extranjero, mat_tipo_sangre, 
                mat_eps, mat_celular2, mat_ciudad_residencia, 
                mat_nombre2, mat_estado_agno, mat_tipo_matricula, institucion, year)
                VALUES(
                :codigo, ".$result_numMat.", now(), :tipoD,
                :nDoc, :religion, :email,
                :direccion, :barrio, :telefono,
                :celular, :estrato, :genero, 
                :fNac, :apellido1, :apellido2, 
                :nombres, :grado, :grupo,
                :tipoEst, '".$procedencia."', :lugarD,
                '".$idAcudiente."', :matestM, '".$idEstudianteU."', 
                :folio, :codTesoreria, :va_matricula, 
                :inclusion, :extran, :tipoSangre, 
                :eps, :celular2, :ciudadR, 
                :nombre2, 3, :tipoMatricula, :idInstitucion, :year
                )";

            $stmt = $conexionPDO->prepare($consulta);

             // Asociar los valores a los marcadores de posición
            $stmt->bindParam(':codigo', $codigoMAT, PDO::PARAM_STR);
            $stmt->bindParam(':tipoD', $tipoD, PDO::PARAM_INT);

            $stmt->bindParam(':nDoc', $nDoc, PDO::PARAM_STR);
            $stmt->bindParam(':religion', $religion);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $stmt->bindParam(':barrio', $barrio, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);

            $stmt->bindParam(':celular', $celular, PDO::PARAM_STR);
            $stmt->bindParam(':estrato', $estrato);
            $stmt->bindParam(':genero', $genero);

            $stmt->bindParam(':fNac', $fNac, PDO::PARAM_STR);
            $stmt->bindParam(':apellido1', $apellido1, PDO::PARAM_STR);
            $stmt->bindParam(':apellido2', $apellido2, PDO::PARAM_STR);

            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
            $stmt->bindParam(':grupo', $grupo);

            $stmt->bindParam(':tipoEst', $tipoEst);
            $stmt->bindParam(':lugarD', $lugarD, PDO::PARAM_STR);

            $stmt->bindParam(':matestM', $matestM);

            $stmt->bindParam(':folio', $folio,PDO::PARAM_STR);
            $stmt->bindParam(':codTesoreria', $codTesoreria, PDO::PARAM_STR);
            $stmt->bindParam(':va_matricula', $va_matricula, PDO::PARAM_STR);

            $stmt->bindParam(':inclusion', $inclusion);
            $stmt->bindParam(':extran', $extran);
            $stmt->bindParam(':tipoSangre', $tipoSangre, PDO::PARAM_STR);

            $stmt->bindParam(':eps', $eps, PDO::PARAM_STR);
            $stmt->bindParam(':celular2', $celular2, PDO::PARAM_STR);
            $stmt->bindParam(':ciudadR', $ciudadR, PDO::PARAM_STR);

            $stmt->bindParam(':nombre2', $nombre2, PDO::PARAM_STR);
            $stmt->bindParam(':tipoMatricula', $tipoMatricula, PDO::PARAM_STR);
            $stmt->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);

            if ($stmt) {
                $stmt->execute();
                return $codigoMAT;
            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }	

    }


    /**
     * Esta función permite actualizar los datos de los estudiantes
     * 
     * @param $conexionPDO ConexionPDO
     * @param $POST Array
     * @param $fechaNacimiento String
     * @param $procedencia String
     * @param $pasosMatricula String
     */
    public static function actualizarEstudiantes($conexionPDO, $POST, $fechaNacimiento = '', $procedencia = '', $pasosMatricula = '')
    {
        global $config, $conexion;

        $tipoD = isset($POST["tipoD"]) ? $POST["tipoD"] : "";
        $nDoc = isset($POST["nDoc"]) ? $POST["nDoc"] : "";
        $religion = isset($POST["religion"]) ? $POST["religion"] : "";
        $email = isset($POST["email"]) ? strtolower($POST["email"]) : "";
        $direccion = isset($POST["direccion"]) ? $POST["direccion"] : "";
        $barrio = isset($POST["barrio"]) ? $POST["barrio"] : "";
        $telefono = isset($POST["telefono"]) ? $POST["telefono"] : "";
        $celular = isset($POST["celular"]) ? $POST["celular"] : "";
        $estrato = isset($POST["estrato"]) ? $POST["estrato"] : "";
        $genero = isset($POST["genero"]) ? $POST["genero"] : "";
        $apellido1 = isset($POST["apellido1"]) ? mysqli_real_escape_string($conexion,$POST["apellido1"]) : "";
        $apellido2 = isset($POST["apellido2"]) ? mysqli_real_escape_string($conexion,$POST["apellido2"]) : "";
        $nombres = isset($POST["nombres"]) ? mysqli_real_escape_string($conexion,$POST["nombres"]) : "";
        $grado = isset($POST["grado"]) ? $POST["grado"] : "";
        $grupo = isset($POST["grupo"]) ? $POST["grupo"] : "";
        $tipoEst = isset($POST["tipoEst"]) ? $POST["tipoEst"] : "";
        $lugarD = isset($POST["lugarD"]) ? $POST["lugarD"] : "";
        $matestM = isset($POST["matestM"]) ? $POST["matestM"] : "";
        $matricula = isset($POST["matricula"]) ? $POST["matricula"] : "";
        $folio = isset($POST["folio"]) ? $POST["folio"] : "";
        $codTesoreria = isset($POST["codTesoreria"]) ? $POST["codTesoreria"] : "";
        $va_matricula = isset($POST["va_matricula"]) ? $POST["va_matricula"] : "";
        $inclusion = isset($POST["inclusion"]) ? $POST["inclusion"] : "";
        $extran = isset($POST["extran"]) ? $POST["extran"] : "";
        $NumMatricula = isset($POST["NumMatricula"]) ? $POST["NumMatricula"] : "";
        $estadoAgno = isset($POST["estadoAgno"]) ? $POST["estadoAgno"] : "";
        $tipoSangre = isset($POST["tipoSangre"]) ? $POST["tipoSangre"] : "";
        $eps = isset($POST["eps"]) ? $POST["eps"] : "";
        $celular2 = isset($POST["celular2"]) ? $POST["celular2"] : "";
        $ciudadR = isset($POST["ciudadR"]) ? $POST["ciudadR"] : "";
        $nombre2 = isset($POST["nombre2"]) ? mysqli_real_escape_string($conexion,$POST["nombre2"]) : "";
        $id = isset($POST["id"]) ? $POST["id"] : "";
        $tipoMatricula = isset($POST["tipoMatricula"]) ? $_POST["tipoMatricula"] : GRADO_GRUPAL;

        try{
            
            $consulta = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET 
            mat_tipo_documento    = :tipoD, 
            mat_documento         = :nDoc, 
            mat_religion          = :religion, 
            mat_email             = :email, 
            mat_direccion         = :direccion, 
            mat_barrio            = :barrio, 
            mat_telefono          = :telefono, 
            mat_celular           = :celular, 
            mat_estrato           = :estrato, 
            mat_genero            = :genero,
            mat_primer_apellido   = :apellido1, 
            mat_segundo_apellido  = :apellido2, 
            mat_nombres           = :nombres, 
            mat_grado             = :grado, 
            mat_grupo             = :grupo, 
            mat_tipo              = :tipoEst,
            mat_lugar_expedicion  = :lugarD,
            mat_estado_matricula  = :matestM, 
            mat_matricula         = :matricula, 
            mat_folio             = :folio, 
            mat_codigo_tesoreria  = :codTesoreria, 
            mat_valor_matricula   = :va_matricula, 
            mat_inclusion         = :inclusion, 
            mat_extranjero        = :extran, 
            mat_fecha             = NOW(), 
            mat_numero_matricula  = :NumMatricula, 
            mat_estado_agno       = :estadoAgno,
            mat_tipo_sangre       = :tipoSangre, 
            mat_eps               = :eps, 
            mat_celular2          = :celular2, 
            mat_ciudad_residencia = :ciudadR,
            mat_lugar_nacimiento  = :procedencia,
            $pasosMatricula
            $fechaNacimiento
            mat_nombre2           = :nombre2,
            mat_tipo_matricula    = :tipoMatricula

            WHERE mat_id = :id AND institucion= :idInstitucion AND year= :year";

            $stmt = $conexionPDO->prepare($consulta);

             // Asociar los valores a los marcadores de posición
            $stmt->bindParam(':tipoD', $tipoD, PDO::PARAM_INT);
            $stmt->bindParam(':nDoc', $nDoc, PDO::PARAM_STR);
            $stmt->bindParam(':religion', $religion);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
            $stmt->bindParam(':barrio', $barrio, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindParam(':celular', $celular, PDO::PARAM_STR);
            $stmt->bindParam(':estrato', $estrato);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':apellido1', $apellido1, PDO::PARAM_STR);
            $stmt->bindParam(':apellido2', $apellido2, PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $nombres, PDO::PARAM_STR);
            $stmt->bindParam(':grado', $grado, PDO::PARAM_STR);
            $stmt->bindParam(':grupo', $grupo);
            $stmt->bindParam(':tipoEst', $tipoEst);
            $stmt->bindParam(':lugarD', $lugarD, PDO::PARAM_STR);
            $stmt->bindParam(':matestM', $matestM);
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':folio', $folio,PDO::PARAM_STR);
            $stmt->bindParam(':codTesoreria', $codTesoreria, PDO::PARAM_STR);
            $stmt->bindParam(':va_matricula', $va_matricula, PDO::PARAM_STR);
            $stmt->bindParam(':inclusion', $inclusion);
            $stmt->bindParam(':extran', $extran);
            $stmt->bindParam(':NumMatricula', $NumMatricula);
            $stmt->bindParam(':estadoAgno', $estadoAgno, PDO::PARAM_INT);
            $stmt->bindParam(':tipoSangre', $tipoSangre, PDO::PARAM_STR);
            $stmt->bindParam(':eps', $eps, PDO::PARAM_STR);
            $stmt->bindParam(':celular2', $celular2, PDO::PARAM_STR);
            $stmt->bindParam(':ciudadR', $ciudadR, PDO::PARAM_STR);
            $stmt->bindParam(':procedencia', $procedencia);
            $stmt->bindParam(':nombre2', $nombre2, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->bindParam(':tipoMatricula', $tipoMatricula, PDO::PARAM_STR);
            $stmt->bindParam(':idInstitucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_STR);

            if ($stmt) {
                $stmt->execute();

                return $stmt;
            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
        }	

    }

    /**
     * Cuenta el número de estudiantes disponibles para un grupo de docentes, opcionalmente aplicando un filtro.
     *
     * @param string $filtroDocentes (Opcional) - Un filtro para limitar la cuenta de estudiantes a un grupo específico de docentes.
     *
     * @return int - El número de estudiantes disponibles para los docentes después de aplicar el filtro (o el número total de estudiantes si no se proporciona un filtro).
     */
    public static function contarEstudiantesParaDocentes(string $filtroDocentes = '')
    {
        $consulta = self::listarEstudiantesParaDocentes($filtroDocentes);
        $num = mysqli_num_rows($consulta);
        return $num;
    }

    /**
     * Obtiene un listado de estudiantes matriculados en base a un predicado opcional.
     *
     * Esta función realiza una consulta a la base de datos para obtener un listado de estudiantes matriculados.
     *
     * @param string $predicado (Opcional) Una cadena que puede contener condiciones SQL adicionales para filtrar los resultados. Por ejemplo, "AND estado = 'activo'".
     *
     * @return mysqli_result|false Devuelve un objeto `mysqli_result` que contiene el resultado de la consulta si la consulta se realiza con éxito. Devuelve `false` si se produce un error.
     */
    public static function obtenerListadoDeEstudiantes($predicado="")
    {

        global $conexion, $config;

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas WHERE mat_id=mat_id AND institucion=? AND year=? {$predicado}";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
        
        $consulta = BindSQL::prepararSQL($sql, $parametros);

        return $consulta;

    }

    /**
     * Obtiene los datos de estudiante retirado.
     * @param mysqli $conexion
     * @param array $config
     * @param string $id
     * 
     * @return array $resultado
     */
    public static function traerDatosEstudiantesretirados(mysqli $conexion, array $config, string $id)
    {
        $sql = "SELECT MAX(tabla_retiradas.id_nuevo), mat_id, mat_estado_matricula, mat_documento, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_nombre2, matret_motivo, matret_fecha, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_usuario FROM " . BD_ACADEMICA . ".academico_matriculas mat
        LEFT JOIN (SELECT * FROM " . BD_ACADEMICA . ".academico_matriculas_retiradas matret WHERE matret.institucion=? AND matret.year=? ORDER BY matret.id_nuevo DESC) AS tabla_retiradas ON tabla_retiradas.matret_estudiante=mat.mat_id
        LEFT JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=matret_responsable AND uss.institucion=? AND uss.year=?
        WHERE mat_id=? AND mat.institucion=? AND mat.year=?";

        $parametros = [$config['conf_id_institucion'], $_SESSION["bd"], $config['conf_id_institucion'], $_SESSION["bd"], $id, $config['conf_id_institucion'], $_SESSION["bd"]];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Obtener la fila de resultados como un array asociativo
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        // Devolver el resultado
        return $resultado;
    }

    /**
     * Me lista los datos de un estudiante retirado.
     * @param mysqli $conexion
     * @param array $config
     * @param string $id
     * 
     * @return mysqli_result $consulta
     */
    public static function listarDatosEstudiantesretirados(
        mysqli $conexion, 
        array $config, 
        string $id, 
        string $yearBd = ''
    )
    {
        $year = !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_id, mat_estado_matricula, mat_documento, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_nombre2, matret_motivo, matret_fecha, uss_nombre, uss_nombre2, uss_apellido1, uss_apellido2, uss_usuario FROM " . BD_ACADEMICA . ".academico_matriculas mat
        INNER JOIN (SELECT * FROM " . BD_ACADEMICA . ".academico_matriculas_retiradas matret WHERE matret.institucion=? AND matret.year=?) AS tabla_retiradas ON tabla_retiradas.matret_estudiante=mat.mat_id
        INNER JOIN " . BD_GENERAL . ".usuarios uss ON uss_id=matret_responsable AND uss.institucion=? AND uss.year=?
        WHERE mat_id=? AND mat.institucion=? AND mat.year=?
        ORDER BY tabla_retiradas.id_nuevo DESC";

        $parametros = [$config['conf_id_institucion'], $year, $config['conf_id_institucion'], $year, $id, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        // Devolver el resultado
        return $resultado;
    }

    /**
    * Este método devuelve una lista de matrículas según un filtro opcional para la institución y año indicados.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $filtro      Filtro adicional para la consulta SQL (opcional).
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta SQL que devuelve las matrículas.
    */
    public static function listarMatriculasFolio(
        array   $config, 
        string  $filtro = "", 
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
        WHERE mat_eliminado=0 AND mat.institucion=? AND mat.year=? {$filtro}
        ORDER BY gra_vocal, mat_grupo, mat_primer_apellido, mat_segundo_apellido, mat_nombres";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método realiza una consulta SQL para obtener la información de las matrículas
    * de los aspirantes cuyo estado de matrícula es igual a en inscripciones
    * para la institución y año especificados en la configuración.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta, que contiene las matrículas de los aspirantes.
    */
    public static function listarMatriculasAspirantes(
        array   $config, 
        string  $filtro = "", 
        string  $limite = "", 
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
        INNER JOIN ".BD_ADMISIONES.".aspirantes ON asp_id=mat.mat_solicitud_inscripcion
        LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=asp_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
        WHERE mat.mat_estado_matricula=5 AND mat.institucion=? AND mat.year=? {$filtro}
        ORDER BY mat.mat_primer_apellido  {$limite}";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método realiza una consulta SQL para obtener las matrículas que están asociadas a observaciones disciplinarias,
    * incluyendo detalles como las notas de disciplina y el grado al que pertenecen.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $filtro      Filtro adicional para restringir los resultados de la consulta (opcional).
    * @param string  $orden       Orden de los resultados de la consulta (opcional). Por defecto, ordenado por apellidos.
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta, que contiene las matrículas con observaciones disciplinarias asociadas.
    */
    public static function listarMatriculasObservador(
        array   $config, 
        string  $filtro = "", 
        string  $orden = "mat_primer_apellido, mat_segundo_apellido", 
        string  $yearBd = "" 
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat
        INNER JOIN ".BD_DISCIPLINA.".disiplina_nota dn ON dn_cod_estudiante=mat_id AND dn.institucion=mat.institucion AND dn.year=mat.year
        LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
        WHERE  mat_eliminado=0  AND mat.institucion=? AND mat.year=? {$filtro}
        ORDER BY {$orden}";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método realiza una consulta SQL para obtener las matrículas que están asociadas a reportes disciplinarios,
    * incluyendo detalles como el tipo de falta y la fecha del reporte.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $filtro      Filtro adicional para restringir los resultados de la consulta (opcional).
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta, que contiene las matrículas con reportes disciplinarios asociados.
    */
    public static function listarMatriculasReportes(
        array   $config, 
        string  $filtro = "", 
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT mat_matricula,mat_primer_apellido,mat_segundo_apellido,mat_nombres,gru_nombre,gra_nombre,ogen_nombre,dr_fecha, dr_estudiante, dr_falta,
        CASE dr_tipo WHEN 1 THEN 'Leve' WHEN 2 THEN 'Grave' WHEN 3 THEN 'Gravísima' END as tipo_falta
        FROM ".BD_ACADEMICA.".academico_matriculas am 
        INNER JOIN ".BD_ACADEMICA.".academico_grupos ag ON am.mat_grupo=ag.gru_id AND ag.institucion=am.institucion AND ag.year=am.year
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra.gra_id=am.mat_grado AND gra.institucion=am.institucion AND gra.year=am.year
        INNER JOIN ".BD_ADMIN.".opciones_generales og ON og.ogen_id=am.mat_tipo
        INNER JOIN ".BD_DISCIPLINA.".disciplina_reportes dr ON dr.dr_estudiante=am.mat_id AND dr.institucion=am.institucion AND dr.year=am.year 
        WHERE am.institucion=? AND am.year=? {$filtro}
        ORDER BY mat_primer_apellido";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método realiza una consulta SQL para obtener los pasos de matrícula de los estudiantes,
    * aplicando filtros y ordenamiento según los parámetros especificados.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $filtro      Filtro adicional para aplicar en la consulta SQL (opcional).
    * @param string  $orden       Campo de ordenamiento para aplicar en la consulta SQL (opcional).
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta, que contiene los pasos de matrícula de los estudiantes.
    */
    public static function listarPasosMatricula(
        array   $config, 
        string  $filtro = "", 
        string  $orden = "mat_primer_apellido, mat_segundo_apellido", 
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat 
        LEFT JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
        WHERE mat_eliminado=0 AND mat.institucion=? AND mat.year=? {$filtro}
        ORDER BY {$orden}";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método realiza una consulta SQL para obtener las matrículas que no tienen un usuario asociado,
    * aplicando filtros según la configuración proporcionada.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta, que contiene las matrículas sin usuario asociado.
    */
    public static function listarMatriculaSinUsuario(
        array   $config, 
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT * FROM ".BD_ACADEMICA.".academico_matriculas mat 
        LEFT JOIN ".BD_GENERAL.".usuarios uss ON uss_id=mat.mat_id_usuario AND uss.institucion=mat.institucion AND uss.year=mat.year 
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat.mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year 
        INNER JOIN ".BD_ACADEMICA.".academico_grupos gru ON gru.gru_id=mat.mat_grupo AND gru.institucion=mat.institucion AND gru.year=mat.year
        WHERE mat.mat_eliminado=0 AND mat.institucion=? AND mat.year=?
        ORDER BY mat.mat_primer_apellido, mat.mat_segundo_apellido, mat.mat_nombres";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método realiza una consulta SQL para encontrar las matrículas que tienen el mismo número de documento
    * de identidad y están asociadas a diferentes grados en la misma institución y año.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $yearBd      Año de la base de datos a utilizar (opcional). Si no se proporciona, se utiliza el año de la sesión.
    * @return mixed  El resultado de la consulta, que contiene las matrículas duplicadas según el número de documento.
    */
    public static function listarMatriculasRepetidas(
        array   $config, 
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT 
        GROUP_CONCAT( mat_id SEPARATOR ', ') as mat_id, 
        GROUP_CONCAT( mat_matricula SEPARATOR ', ') as mat_matricula, 
        GROUP_CONCAT( gra_nombre SEPARATOR ', ') as gra_nombre, 
        mat_documento, mat_estado_matricula, mat_primer_apellido, mat_segundo_apellido, mat_nombres, mat_nombre2, COUNT(*) as duplicados 
        FROM ".BD_ACADEMICA.".academico_matriculas mat 
        INNER JOIN ".BD_ACADEMICA.".academico_grados gra ON gra_id=mat_grado AND gra.institucion=mat.institucion AND gra.year=mat.year
        WHERE mat_eliminado=0 AND mat.institucion=? AND mat.year=?
        GROUP BY mat_documento
        HAVING COUNT(*) > 1 
        ORDER BY mat_id ASC";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        return $resultado;
    }

    /**
    * Este método ejecuta una consulta SQL para obtener el año de nacimiento de un usuario
    * específico utilizando su ID de usuario y la configuración proporcionada.
    *
    * @param array   $config      Configuración general del sistema.
    * @param string  $idUsuario   Identificador del usuario del cual se desea obtener el año de nacimiento.
    * @param string  $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    * @return mixed  El resultado de la consulta, que representa el año de nacimiento del usuario.
    */
    public static function traerYearNacimiento(
        array   $config, 
        string  $idUsuarios,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "SELECT YEAR(mat_fecha_nacimiento) FROM ".BD_ACADEMICA.".academico_matriculas WHERE mat_id_usuario=? AND institucion=? AND year=?";

        $parametros = [$idUsuarios, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
        $resultado = mysqli_fetch_array($resultado, MYSQLI_BOTH);

        return $resultado;
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar un registro de matriculas
    *
    * @param array  $config         Configuración del sistema.
    * @param int    $idMatricula    Identificador del registro a eliminar.
    * @param string $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function eliminarMatricula (
        array   $config,
        int     $idMatricula,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_matriculas WHERE mat_id=? AND institucion=? AND year=?";

        $parametros = [$idMatricula, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar todos los registros de matriculas
    * pertenecientes a una institución para un año específico de la base de datos.
    *
    * @param int    $idInstitucion Identificador de la institución cuyas matriculas se eliminarán.
    * @param string $yearBd        Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function eliminarTodasMatriculas (
        int     $idInstitucion,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "DELETE FROM ".BD_ACADEMICA.".academico_matriculas WHERE institucion=? AND year=?";

        $parametros = [$idInstitucion, $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para insertar un nuevo registro de matricula en la tabla 'academico_matriculas'.
    *
    * @param PDO    $conexionPDO  Conexión PDO a la base de datos.
    * @param string $insert       Lista de campos separados por coma para la inserción.
    * @param array  $parametros   Array de parámetros para la consulta preparada.
    **/
    public static function guardarMatricula (
        PDO     $conexionPDO,
        string  $insert,
        array   $parametros
    )
    {
        $campos = explode(',', $insert);
        $numCampos = count($campos);
        $signosPreguntas = str_repeat('?,', $numCampos);
        $signosPreguntas = rtrim($signosPreguntas, ',');

        $codigo = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_matriculas');
        $parametros[] = $codigo;

        $sql = "INSERT INTO ".BD_ACADEMICA.".academico_matriculas ({$insert}) VALUES ({$signosPreguntas})";
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para actualizar un registro de matriculas en la tabla 'academico_matriculas' por el id unico.
    *
    * @param array  $config         Configuración del sistema.
    * @param string $idMatricula    Identificador de matricula a actualizar.
    * @param string $update         Lista de campos y valores a actualizar en formato de cadena.
    * @param string $yearBd         Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function actualizarMatriculasPorId (
        array   $config,
        string  $idMatricula,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET {$updateSql} WHERE mat_id=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idMatricula, $config['conf_id_institucion'], $year]);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para actualizar un registro de matriculas en la tabla 'academico_matriculas' por el id de su usuario.
    *
    * @param array  $config     Configuración del sistema.
    * @param string $idUsuario  Identificador del id del usuario a actualizar.
    * @param string $update     Lista de campos y valores a actualizar en formato de cadena.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function actualizarMatriculasPorIdUsuario (
        array   $config,
        string  $idUsuario,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET {$updateSql} WHERE mat_id_usuario=? AND institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$idUsuario, $config['conf_id_institucion'], $year]);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para actualizar todos los registros de matriculas en la tabla 'academico_matriculas' de una institución.
    *
    * @param array  $config         Configuración del sistema.
    * @param string $update         Lista de campos y valores a actualizar en formato de cadena.
    * @param string $yearBd         Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function actualizarMatriculasInstitucion (
        array   $config,
        string  $update,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        [$updateSql, $updateValues] = BindSQL::prepararUpdate($update);

        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET {$updateSql} WHERE institucion=? AND year=?";

        $parametros = array_merge($updateValues, [$config['conf_id_institucion'], $year]);
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar la matriculas inactivas en la tabla 'academico_matriculas'.
    *
    * @param mysqli $conexion   
    * @param array  $config     Configuración del sistema.
    * @param string $yearBd     Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function eliminarMatriculasInactivas (
        mysqli  $conexion,
        array   $config,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET mat_eliminado=1 WHERE mat_estado_matricula!=1 AND institucion=? AND year=?";

        $parametros = [$config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);

        $columnasAfectadas = mysqli_affected_rows($conexion);

        return $columnasAfectadas;
    }

    /**
    * Esta función ejecuta una consulta preparada para eliminar el id del acudiente en la tabla 'academico_matriculas'.
    *
    * @param array  $config         Configuración del sistema.
    * @param string $idAcudiente    Identificador del id del acudiente a eliminar.
    * @param string $yearBd         Año de la base de datos (opcional). Si no se proporciona, se utiliza el valor de sesión.
    **/
    public static function eliminarMatriculasAcudiente (
        array   $config,
        string  $idAcudiente,
        string  $yearBd = ""
    )
    {
        $year= !empty($yearBd) ? $yearBd : $_SESSION["bd"];

        $sql = "UPDATE ".BD_ACADEMICA.".academico_matriculas SET mat_acudiente=NULL WHERE mat_acudiente=? AND institucion=? AND year=?";

        $parametros = [$idAcudiente, $config['conf_id_institucion'], $year];
        
        $resultado = BindSQL::prepararSQL($sql, $parametros);
    }

}