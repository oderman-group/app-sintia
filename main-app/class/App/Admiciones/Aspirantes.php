<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Aspirantes extends BDT_Tablas implements BDT_JoinImplements
{
    public static $schema = BD_ADMISIONES;

    public static $tableName = 'aspirantes';

    public static $primaryKey = 'asp_id';

    public static $tableAs = 'asp';

    use BDT_Join;
}
