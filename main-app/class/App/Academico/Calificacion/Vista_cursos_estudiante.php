<?php
require_once ROOT_PATH . '/main-app/class/Tables/BDT_Join.php';
require_once ROOT_PATH . '/main-app/class/Tables/BDT_tablas.php';

class Vista_cursos_estudiante extends BDT_Tablas
{

    public static $schema = BD_ACADEMICA;
    public static $tableName = 'vista_cursos_estudiante';
    public  static $tableAs = 'vce';


    public static function listarCursosEstudiates(
        array $idEstudiantes = [],
        string $year = null,
    ) {
        $year = empty($year) ? $_SESSION["bd"] : $year;
        $campos = "*";
        $predicado =
            [
                "institucion" => $_SESSION["idInstitucion"],
                "year" => $year,
                "gra_tipo" => GRADO_GRUPAL
            ];

        if (!empty($idEstudiantes)) {
            foreach ($idEstudiantes as &$estudiante) {
                $estudiante = "'".$estudiante."'";
            }
            ;
            $in_estudiantes = implode(', ', $idEstudiantes);
            $predicado['mat_id IN'] = '(' . $in_estudiantes . ')';
        }
        $order = "ORDER BY mat_id,curso,year";
        $sql = parent::Select(predicado: $predicado, campos: $campos,sqlfooter:$order);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        return self::agruparDatos($result);
    }


    public static function agruparDatos(array $datos)
    {
        global $config;

        $conteoEstudiante = 0;
        $mat_id = "";
        $estudiantes = [];

        foreach ($datos as $registro) {
            $curso = "";
            Utilidades::valordefecto($registro["mat_id"]);

            $curso_grupo = $registro["curso"] . '-' . $registro["grupo"]. '-' . $registro["year"];
           // por estudiante
            if ($mat_id != $registro["mat_id"]) {
                $contarCurso = 0;
                $estudiantes[$registro["mat_id"]] = [
                    "mat_id" => $registro["mat_id"]
                 ];
                 $mat_id = $registro["mat_id"];
            }
            // Datos de los cursos
            if ($curso != $mat_id . '-' . $curso_grupo) {
                $contarCurso++;
                $estudiantes[$registro["mat_id"]]["cursos"][$curso_grupo] = [
                    "curso"      => $registro['curso'],
                    "grupo"      => $registro['grupo'],
                    "year"       => $registro['year'],
                    "nro"        => $contarCurso,
                    "gra_nombre" => $registro['gra_nombre'],
                    "gru_nombre" => $registro['gru_nombre'],
                ];
                $curso = $mat_id . '-' . $curso_grupo;
            }
        }

       

        return $estudiantes;
    }


}