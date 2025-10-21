<?php
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

class CargaAcademicaOptimizada extends CargaAcademica {
    
    /**
     * Versión optimizada del método listarCargas
     * Elimina subqueries pesadas y las carga bajo demanda
     */
    public static function listarCargasOptimizado(
        mysqli $conexion, 
        array $config, 
        string $filtroMT = "", 
        string $filtro = "", 
        string $order = "car_id", 
        string $limit = "LIMIT 0, 100",  // Paginación por defecto
        string $valueIlike = "",
        array  $filtro2 = array(),
        array $selectConsulta=[]  
    ){
        $stringSelect = "car.car_id, car.car_periodo, car.car_curso, car.car_grupo, car.car_ih, 
                        car.car_permiso2, car.car_indicador_automatico, car.car_maximos_indicadores,
                        car.car_docente, car.car_maximas_calificaciones, car.car_director_grupo, 
                        car.car_activa, car.id_nuevo AS id_nuevo_carga,
                        am.mat_id, am.mat_nombre, am.mat_valor,
                        gra.gra_id, gra.gra_nombre, gra.gra_tipo,
                        gru.gru_nombre,
                        uss.uss_id, uss.uss_nombre, uss.uss_nombre2, uss.uss_apellido1, uss.uss_apellido2";
        
        if (!empty($selectConsulta)) {
            $stringSelect = implode(", ", $selectConsulta);
        }
        
        // Filtro de búsqueda
        if(!empty($valueIlike)){
            $busqueda = $valueIlike;
            $filtro .= " AND (
                 car.car_id LIKE '%" . $busqueda . "%' 
                OR uss.uss_nombre LIKE '%".$busqueda."%' 
                OR uss.uss_nombre2 LIKE '%".$busqueda."%' 
                OR uss.uss_apellido1 LIKE '%".$busqueda."%' 
                OR uss.uss_apellido2 LIKE '%".$busqueda."%' 
                OR gra.gra_nombre LIKE '%" . $busqueda . "%' 
                OR am.mat_nombre LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre), ' ',TRIM(uss.uss_apellido1), ' ', TRIM(uss.uss_apellido2)) LIKE '%".$busqueda."%'
            )";
        }
        
        // Filtro de periodos
        if(!empty($filtro2)){           
            if(!empty($filtro2['periodoSeleccionados'])){
                $arrayPeriodos = $filtro2['periodoSeleccionados'];
                $periodos = implode(", ", $arrayPeriodos);
                $filtro .= " AND car.car_periodo IN ({$periodos})";
            }
        }
        
        try {
            // Consulta optimizada SIN las subqueries pesadas
            $sql = "SELECT 
                    $stringSelect
                    
                    FROM ".BD_ACADEMICA.".academico_cargas car
        
                    INNER JOIN ".BD_ACADEMICA.".academico_grados gra 
                    ON  gra.gra_id = car.car_curso 
                    AND gra.institucion = car.institucion 
                    AND gra.year = car.year                    
                    {$filtroMT}

                    LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru 
                    ON  gru.gru_id = car.car_grupo 
                    AND gru.institucion = car.institucion 
                    AND gru.year = car.year
                    
                    LEFT JOIN ".BD_ACADEMICA.".academico_materias am 
                    ON  am.mat_id = car.car_materia 
                    AND am.institucion = car.institucion 
                    AND am.year = car.year

                    LEFT JOIN ".BD_GENERAL.".usuarios uss 
                    ON uss.uss_id = car.car_docente 
                    AND uss.institucion = car.institucion 
                    AND uss.year = car.year
                    
                    WHERE car.institucion = ? 
                    AND car.year = ? 
                    AND car.car_activa = 1
                    {$filtro}
                    
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
     * Contar total de cargas para la paginación
     */
    public static function contarTotalCargas(
        mysqli $conexion, 
        array $config, 
        string $filtro = ""
    ){
        try {
            $sql = "SELECT COUNT(DISTINCT car.car_id) as total
                    FROM ".BD_ACADEMICA.".academico_cargas car
                    
                    INNER JOIN ".BD_ACADEMICA.".academico_grados gra 
                    ON  gra.gra_id = car.car_curso 
                    AND gra.institucion = car.institucion 
                    AND gra.year = car.year

                    LEFT JOIN ".BD_ACADEMICA.".academico_grupos gru 
                    ON  gru.gru_id = car.car_grupo 
                    AND gru.institucion = car.institucion 
                    AND gru.year = car.year
                    
                    LEFT JOIN ".BD_ACADEMICA.".academico_materias am 
                    ON  am.mat_id = car.car_materia 
                    AND am.institucion = car.institucion 
                    AND am.year = car.year

                    LEFT JOIN ".BD_GENERAL.".usuarios uss 
                    ON uss.uss_id = car.car_docente 
                    AND uss.institucion = car.institucion 
                    AND uss.year = car.year
                    
                    WHERE car.institucion = ? 
                    AND car.year = ? 
                    AND car.car_activa = 1
                    {$filtro}";
    
            $parametros = [$config['conf_id_institucion'], $_SESSION["bd"]];
            
            $consulta = BindSQL::prepararSQL($sql, $parametros);
            $resultado = mysqli_fetch_assoc($consulta);
            
            return intval($resultado['total'] ?? 0);
            
        } catch (Exception $e) {
            include("../compartido/error-catch-to-report.php");
            return 0;
        }
    }
    
    /**
     * Obtener datos adicionales de una carga específica (lazy loading)
     * Se llama solo cuando se expande la fila o cuando el usuario hace clic en "Ver notas"
     */
    public static function obtenerDatosAdicionalesCarga($config, $cargaId, $periodo) {
        global $conexionPDO;
        
        $datos = [
            'estudiantes_totales' => 0,
            'estudiantes_sin_nota' => 0,
            'actividades_totales' => 0,
            'actividades_registradas' => 0,
            'estudiantes_mt' => 0
        ];
        
        try {
            // Contar estudiantes totales en el curso/grupo
            $sqlEstudiantes = "SELECT COUNT(*) as total
                              FROM ".BD_ACADEMICA.".academico_matriculas mat
                              INNER JOIN ".BD_ACADEMICA.".academico_cargas car 
                                ON car.car_curso = mat.mat_grado
                                AND car.car_grupo = mat.mat_grupo
                                AND car.institucion = mat.institucion
                                AND car.year = mat.year
                              WHERE car.car_id = :carga_id
                              AND mat.mat_eliminado = 0
                              AND (mat.mat_estado_matricula = 1 OR mat.mat_estado_matricula = 2)
                              AND mat.institucion = :institucion
                              AND mat.year = :year";
            
            $stmt = $conexionPDO->prepare($sqlEstudiantes);
            $stmt->bindParam(':carga_id', $cargaId, PDO::PARAM_STR);
            $stmt->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $datos['estudiantes_totales'] = intval($result['total'] ?? 0);
            
            // Suma de actividades (declaradas y registradas)
            $sqlActividades = "SELECT 
                                COALESCE(SUM(act_valor), 0) as total,
                                COALESCE(SUM(CASE WHEN act_registrada = 1 THEN act_valor ELSE 0 END), 0) as registradas
                              FROM ".BD_ACADEMICA.".academico_actividades
                              WHERE act_id_carga = :carga_id
                              AND act_periodo = :periodo
                              AND act_estado = 1
                              AND institucion = :institucion
                              AND year = :year";
            
            $stmt2 = $conexionPDO->prepare($sqlActividades);
            $stmt2->bindParam(':carga_id', $cargaId, PDO::PARAM_STR);
            $stmt2->bindParam(':periodo', $periodo, PDO::PARAM_INT);
            $stmt2->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt2->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            $stmt2->execute();
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            $datos['actividades_totales'] = floatval($result2['total'] ?? 0);
            $datos['actividades_registradas'] = floatval($result2['registradas'] ?? 0);
            
            // Contar estudiantes sin nota (opcional, puede ser costoso)
            $sqlSinNota = "SELECT COUNT(DISTINCT mat.mat_id) as sin_nota
                          FROM ".BD_ACADEMICA.".academico_matriculas mat
                          INNER JOIN ".BD_ACADEMICA.".academico_cargas car 
                            ON car.car_curso = mat.mat_grado
                            AND car.car_grupo = mat.mat_grupo
                            AND car.institucion = mat.institucion
                            AND car.year = mat.year
                          INNER JOIN ".BD_ACADEMICA.".academico_actividades act
                            ON act.act_id_carga = car.car_id
                            AND act.act_periodo = :periodo
                            AND act.act_estado = 1
                            AND act.institucion = mat.institucion
                            AND act.year = mat.year
                          LEFT JOIN ".BD_ACADEMICA.".academico_calificaciones cal
                            ON cal.cal_id_estudiante = mat.mat_id
                            AND cal.cal_id_actividad = act.act_id
                            AND cal.institucion = mat.institucion
                            AND cal.year = mat.year
                          WHERE car.car_id = :carga_id
                          AND mat.mat_eliminado = 0
                          AND (mat.mat_estado_matricula = 1 OR mat.mat_estado_matricula = 2)
                          AND cal.cal_id IS NULL
                          AND mat.institucion = :institucion
                          AND mat.year = :year";
            
            $stmt3 = $conexionPDO->prepare($sqlSinNota);
            $stmt3->bindParam(':carga_id', $cargaId, PDO::PARAM_STR);
            $stmt3->bindParam(':periodo', $periodo, PDO::PARAM_INT);
            $stmt3->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $stmt3->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);
            $stmt3->execute();
            $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            $datos['estudiantes_sin_nota'] = intval($result3['sin_nota'] ?? 0);
            
        } catch (Exception $e) {
            error_log("Error en obtenerDatosAdicionalesCarga: " . $e->getMessage());
        }
        
        return $datos;
    }
}
?>


