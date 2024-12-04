<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Administrativo_Usuario_Usuario extends BDT_Tablas {
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios';

    public static $primaryKey = 'uss_id';

}