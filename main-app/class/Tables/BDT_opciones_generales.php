<?php
require_once 'BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class BDT_OpcionesGenerales extends BDT_Tablas implements BDT_JoinImplements{

    public static $tableName = 'opciones_generales';
    public static $primaryKey = 'ogen_id';
    public static $schema = BD_ADMIN;
    public static $tableAs = 'ogen';

    use BDT_Join;

}