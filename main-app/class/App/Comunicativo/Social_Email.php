<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Comunicativo_Social_Email extends BDT_Tablas {
    public static $schema = BD_ADMIN;

    public static $tableName = 'social_emails';

    public static $primaryKey = 'ema_id';

}