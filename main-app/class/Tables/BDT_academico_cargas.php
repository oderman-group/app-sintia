<?php
require_once 'BDT_tablas.php';

class BDT_AcademicoCargas extends BDT_Tablas {

    public const ESTADO_DIRECTIVO = 'DIRECTIVO'; //Signififca que la carga fue alterada por el usuario directivo
    public const ESTADO_SINTIA    = 'SINTIA'; //Signififca que la carga fue alterada por el sistema en su proceso normal


    public const GENERACION_MANUAL = 'MANUAL';
    public const GENERACION_AUTO   = 'AUTOMATICA';

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_cargas';

}