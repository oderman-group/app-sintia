<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Grado extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_grados';
    public  static $tableAs   = 'gra';
    public  $tableAs2;

    use BDT_Join;
    public function __construct($alias = 'gra')
    {
        $this->tableAs = $alias;
    }
}
