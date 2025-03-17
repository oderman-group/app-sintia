<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Administrativo_Usuarios_Por_Estudiantes extends BDT_Tablas {

    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios_por_estudiantes';

    public static $primaryKey = 'upe_id';
    
    public static $tableAs = 'upe';

}