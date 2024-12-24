<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Administrativo_General_Solicitud extends BDT_Tablas {
    public static $schema = BD_GENERAL;

    public static $tableName = 'general_solicitudes';

    public static $primaryKey = 'soli_id';

    public const SOLICITUD_PENDIENTE = 1;
    public const SOLICITUD_ACEPTADA  = 3;
    public const SOLICITUD_RECHAZADA  = 4;

}