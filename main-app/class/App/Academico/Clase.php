<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Clase extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_clases';
    public  static $tableAs   = 'cls';

    use BDT_Join;

}
