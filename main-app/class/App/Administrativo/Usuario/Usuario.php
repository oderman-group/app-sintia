<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';
class Administrativo_Usuario_Usuario extends BDT_Tablas implements BDT_JoinImplements{
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios';

    public static $primaryKey = 'uss_id';
    
    public static $tableAs = 'uss';

    use BDT_Join;

}