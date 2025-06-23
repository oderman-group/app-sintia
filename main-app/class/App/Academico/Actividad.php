<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Actividad extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_actividades';
    public  static $tableAs   = 'act';

    use BDT_Join;

}
