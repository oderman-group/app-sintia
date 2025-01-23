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

    public static function getCursosEstudiante($estudiantes, string $yearBd    = ''){
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
}
