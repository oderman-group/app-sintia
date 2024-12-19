<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Administrativo_Usuario_Usuario_Bloqueado extends BDT_Tablas {

    public const USUARIO_INDIVIDUAL = "USUARIO_INDIVIDUAL";
    public const USUARIO_GRUPAL     = "USUARIO_GRUPAL";
    public const MATRICULA          = "MATRICULA";

    public static $schema = BD_ADMIN;

    public static $tableName = 'usuarios_bloqueados';

    public static $primaryKey = 'usblo_id';

}