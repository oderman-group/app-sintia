<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Notas_tipo extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema = BD_ACADEMICA;
    public static $tableName = 'academico_notas_tipos';
    public static $tableAs = 'notip';

    use BDT_Join;

    public static function listarTipoDeNotas($categoria, string $yearBd    = ''){
        $campos     = "*"; 
        $predicado =
        [
            "notip_categoria"       => $categoria,
            "institucion"           => $_SESSION["idInstitucion"],
            "year"                  => $yearBd
        ];
        $sql = parent::Select($predicado,$campos);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
