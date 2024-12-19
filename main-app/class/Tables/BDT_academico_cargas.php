<?php
require_once 'BDT_tablas.php';

class BDT_AcademicoCargas extends BDT_Tablas {

    public const ESTADO_DIRECTIVO = 'DIRECTIVO';
    public const ESTADO_SINTIA    = 'SINTIA';

    public const GENERACION_MANUAL = 'MANUAL';
    public const GENERACION_AUTO   = 'AUTOMATICA';

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_cargas';

}