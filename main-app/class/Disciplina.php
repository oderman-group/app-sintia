<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/app-sintia/config-general/constantes.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/Tables/BDT_disciplina.php");

class Disciplina
{

    /**
     * Esta función lista la nota de comportamieno de los estudiantes según varios parámetros.
     *
     * @param string $filtroAdicional - Filtros adicionales para la consulta SQL.
     * @param string $filtroLimite - Límite de resultados para la consulta SQL.
     * @param string $valueIlike - Valor String que se utilizara para biuscar por cualuqier parametro definido (puede ser nulo).
     * @param array $selectConsulta - valores de los select que se van a nececitar para las consultas
     * 
     * @return mysqli_result - Un array con los resultados de la consulta.
     */
    public static function listarComportamiento(
        string  $filtroAdicional = '',
        string  $filtroLimite    = 'LIMIT 0, 20',
        string  $valueIlike      = null,
        array   $selectConsulta  = []
    ) {
        global $config;
        $conexionPDO = Conexion::newConnection('PDO');

        $stringSelect = "*";
        if (!empty($selectConsulta)) {
            $stringSelect = implode(", ", $selectConsulta);
        };

        if (!empty($valueIlike)) {
            $busqueda = $valueIlike;
            $filtroAdicional .= " AND (
                mat.mat_id LIKE '%" . $busqueda . "%' 
                OR mat.mat_nombres LIKE '%" . $busqueda . "%' 
                OR mat.mat_nombre2 LIKE '%" . $busqueda . "%' 
                OR mat.mat_primer_apellido LIKE '%" . $busqueda . "%' 
                OR mat.mat_segundo_apellido LIKE '%" . $busqueda . "%' 
                OR mat.mat_documento LIKE '%" . $busqueda . "%' 
                OR mat.mat_email LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_segundo_apellido), ' ', TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), TRIM(mat.mat_segundo_apellido), TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), ' ', TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_nombres), ' ', TRIM(mat.mat_primer_apellido)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_nombres), '', TRIM(mat.mat_primer_apellido)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_primer_apellido), '', TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_nombres), ' ', TRIM(mat.mat_nombre2)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_nombres), ' ', TRIM(mat.mat_segundo_apellido)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_nombre2), ' ', TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_segundo_apellido), ' ', TRIM(mat.mat_nombres)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_segundo_apellido), ' ', TRIM(mat.mat_nombre2)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_segundo_apellido), ' ', TRIM(mat.mat_primer_apellido)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(mat.mat_nombre2), ' ', TRIM(mat.mat_segundo_apellido)) LIKE '%" . $busqueda . "%'
                OR gra.gra_nombre LIKE '%" . $busqueda . "%'
                OR mate.mat_nombre LIKE '%" . $busqueda . "%'
                OR uss.uss_usuario LIKE '%" . $busqueda . "%'
                OR uss.uss_nombre LIKE '%" . $busqueda . "%' 
                OR uss.uss_nombre2 LIKE '%" . $busqueda . "%' 
                OR uss.uss_apellido1 LIKE '%" . $busqueda . "%' 
                OR uss.uss_apellido2 LIKE '%" . $busqueda . "%' 
                OR uss.uss_documento LIKE '%" . $busqueda . "%' 
                OR CONCAT(TRIM(uss.uss_apellido1), ' ', TRIM(uss.uss_apellido2), ' ', TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido1), TRIM(uss.uss_apellido2), TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido1), ' ', TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido1), TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre), ' ', TRIM(uss.uss_apellido1)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre), '', TRIM(uss.uss_apellido1)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido1), '', TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre), ' ', TRIM(uss.uss_nombre2)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre), ' ', TRIM(uss.uss_apellido2)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre2), ' ', TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido2), ' ', TRIM(uss.uss_nombre)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido2), ' ', TRIM(uss.uss_nombre2)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_apellido2), ' ', TRIM(uss.uss_apellido1)) LIKE '%" . $busqueda . "%'
                OR CONCAT(TRIM(uss.uss_nombre2), ' ', TRIM(uss.uss_apellido2)) LIKE '%" . $busqueda . "%'
            )";
        }
        try {
            $sql = "SELECT 
                    $stringSelect 
                    FROM " . BD_DISCIPLINA . "." . BDT_disciplina::$tableName . " dn

                    LEFT JOIN " . BD_ACADEMICA . ".academico_matriculas mat 
                    ON mat.mat_id           = dn.dn_cod_estudiante 
                    AND mat.institucion     = dn.institucion 
                    AND mat.year            = dn.year

                    LEFT JOIN " . BD_ACADEMICA . ".academico_cargas car 
                    ON car_id               = dn.dn_id_carga 
                    AND car.institucion     = dn.institucion 
                    AND car.year            = dn.year
                    
                    LEFT JOIN " . BD_GENERAL . ".usuarios uss 
                    ON uss_id               = car.car_docente 
                    AND uss.institucion     = dn.institucion 
                    AND uss.year            = dn.year

                    LEFT JOIN " . BD_ACADEMICA . ".academico_grados gra 
                    ON gra_id               = car.car_curso 
                    AND gra.institucion     = dn.institucion 
                    AND gra.year            = dn.year

                    LEFT JOIN " . BD_ACADEMICA . ".academico_grupos gru 
                    ON gru.gru_id           = car.car_grupo 
                    AND gru.institucion     = dn.institucion 
                    AND gru.year            = dn.year

                    LEFT JOIN " . BD_ACADEMICA . ".academico_materias mate 
                    ON mate.mat_id          = car.car_materia 
                    AND mate.institucion    = dn.institucion 
                    AND mate.year           = dn.year
                    
                    WHERE dn.institucion  = :institucion 
                    AND dn.year           = :year
                    AND (dn.dn_observacion IS NOT NULL OR dn.dn_nota IS NOT NULL OR dn.dn_observacion != '' OR dn.dn_nota != '')
                    
                    {$filtroAdicional}
                    
                    ORDER BY 
                        dn_periodo DESC, 
                        mat.mat_grado ASC, 
                        mat.mat_grupo ASC, 
                        mat.mat_primer_apellido ASC, 
                        mat.mat_segundo_apellido ASC, 
                        mat.mat_nombres ASC

                    {$filtroLimite}";
            $pdo = $conexionPDO->prepare($sql);

            $pdo->bindParam(':institucion', $config['conf_id_institucion'], PDO::PARAM_INT);
            $pdo->bindParam(':year', $_SESSION["bd"], PDO::PARAM_INT);

            if ($pdo) {
                $pdo->execute();
                return $pdo;
            } else {
                throw new Exception("Error al preparar la consulta.");
            }
        } catch (Exception $e) {
            echo "Excepción catpurada: " . $e->getMessage();
            exit();
        }
    }

    /**
     * Este metodo actualiza el periodo en una observacion de comportamiento
     * 
     * @param array     $config 
     * @param int       $idNuevo
     * @param int       $periodo
     **/
    public static function actualizarPeriodoComportamiento(
        array   $config,
        int     $idNuevo,
        int     $periodo
    ) {

        $datos = [
            'dn_periodo'    => $periodo
        ];

        $predicado = [
            "id_nuevo"      =>  $idNuevo,
            "institucion"   =>  $config['conf_id_institucion'],
            "year"          =>  $_SESSION["bd"]
        ];

        BDT_disciplina::update($datos, $predicado, BD_DISCIPLINA);
    }

    /**
     * Este metodo elimina la observacion y la nota de un comportamiento
     * 
     * @param array     $config 
     * @param int       $idNuevo
     **/
    public static function eliminarComportamiento(
        array   $config,
        int     $idNuevo
    ) {

        $datos = [
            'dn_observacion'    =>  NULL,
            'dn_nota'           =>  NULL
        ];

        $predicado = [
            "id_nuevo"          =>  $idNuevo,
            "institucion"       =>  $config['conf_id_institucion'],
            "year"              =>  $_SESSION["bd"]
        ];

        BDT_disciplina::update($datos, $predicado, BD_DISCIPLINA);
    }
}