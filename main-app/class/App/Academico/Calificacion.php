<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Academico_Calificacion extends BDT_Tablas implements BDT_JoinImplements {
    public static $schema = BD_ACADEMICA;

    public static $tableName = 'academico_calificaciones';

    public static $primaryKey = 'cal_id';

    public  static $tableAs   = 'cal';

    use BDT_Join;

    public static function contarRegistrosEnCalificaciones(array $predicado = []) {
        return self::numRows($predicado);
    }
}