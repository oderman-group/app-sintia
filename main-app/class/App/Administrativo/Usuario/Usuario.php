<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';
class Administrativo_Usuario_Usuario extends BDT_Tablas implements BDT_JoinImplements{
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios';

    public static $primaryKey = 'uss_id';
    
    public static $tableAs = 'uss';

    use BDT_Join;

    public static function bloquearUsuarios(array $usuarios, $bloquear = true){
        
        foreach ($usuarios as $user) {
            $users[] = parent::formatValor($user);
        };
        $in_usuarios = implode(', ', $users);

        $predicado =
        [
            self::OTHER_PREDICATE   => "uss_id IN ($in_usuarios)",
            "institucion"           => $_SESSION["idInstitucion"]
        ];

        $datos =
        [
            "uss_bloqueado"   => $bloquear?1:0,
        ];
        $sql = parent::Update($datos,$predicado);
        return $sql;
    }

}