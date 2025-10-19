<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Seguridad_Historial_Acciones extends BDT_Tablas {
    public static $schema = BD_ADMIN;

    public static $tableName = 'seguridad_historial_acciones';

    public static $primaryKey = 'hil_id';

}