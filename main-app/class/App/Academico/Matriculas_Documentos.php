<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Matriculas_Documentos extends BDT_Tablas implements BDT_JoinImplements
{
    public static $schema = BD_ACADEMICA;

    public static $tableName = 'academico_matriculas_documentos';

    public static $primaryKey = 'matd_id';

    public static $tableAs = 'matd';

    use BDT_Join;
}
