<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Comunicativo_Tipos_Notificaciones extends BDT_Tablas {
    public static $schema = BD_GENERAL;

    public static $tableName = 'tipos_notificaciones';

    public static $primaryKey = 'tnf_id';

    public  static $tableAs = 'tnf';

}