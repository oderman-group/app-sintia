<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Matricula extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema = BD_ACADEMICA;
    public static $tableName = 'academico_matriculas';
    public static $tableAs = 'matri';

    use BDT_Join;
/**
 * Obtiene los cursos de un conjunto de estudiantes para un año específico.
 *
 * Esta función recupera los datos de los cursos de los estudiantes especificados
 * en el parámetro `$estudiantes` para el año indicado en `$yearBd`.
 *
 * @param array  $estudiantes  Arreglo de IDs de estudiantes para los cuales se desean obtener los cursos.
 * @param string $yearBd       Año académico para filtrar los cursos. Si se deja vacío, se usará el año por defecto.
 *
 * @return array  Retorna un arreglo asociativo con los datos de los cursos de los estudiantes,
 *                incluyendo los campos: `mat_id`, `mat_grado`, `mat_grupo`.
 *
 * @throws Exception Si ocurre un error en la consulta a la base de datos.
 */
    public static function getCursosEstudiante(array $estudiantes, string $yearBd    = ''):array{
        $campos     = "mat_id,mat_grado,mat_grupo"; 
        $in_estudiantes = implode(', ', $estudiantes);
        $predicado =
        [
           
            "institucion"           => $_SESSION["idInstitucion"],
            "year"                  => $yearBd,
            self::OTHER_PREDICATE   => "mat_id IN ({$in_estudiantes})"
        ];
        $sql = parent::Select($predicado,$campos);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarEsdutiantes($curso, $grupo){
        $campos     = "mat_id,mat_grado,mat_grupo,mat_nombres,mat_nombre2,mat_primer_apellido,mat_segundo_apellido,mat_documento"; 
        $predicado =
        [
           
            "institucion"           => $_SESSION["idInstitucion"],
            "year"                  => $_SESSION["bd"],
            "mat_grado"             => $curso,
            "mat_grupo"             => $grupo
        ];
        $sql = parent::Select($predicado,$campos);
        $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as &$row){
            $nombreCompleto         = Estudiantes::NombreCompletoDelEstudiante($row);
            $row['nombre_completo'] = $nombreCompleto;
        }
        return $result;
    }
}
