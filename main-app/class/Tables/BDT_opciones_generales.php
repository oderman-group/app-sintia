<?php
require_once 'BDT_tablas.php';

class BDT_OpcionesGenerales extends BDT_Tablas implements BDT_JoinImplements{

    public static $tableName = 'opciones_generales';
    public static $primaryKey = 'ogen_id';
    public static $schema = BD_ADMIN;
    public static $tableAs = 'ogen';

    use BDT_Join;

}