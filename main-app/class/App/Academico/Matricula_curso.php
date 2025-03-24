<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Matricula_curso extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema = BD_ADMIN;
    public static $tableName = 'mediatecnica_matriculas_cursos';
    public  static $tableAs = 'mdt';

    use BDT_Join;

}
