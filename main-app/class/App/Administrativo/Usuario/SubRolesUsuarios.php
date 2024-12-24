<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH."/main-app/class/Conexion.php";

class Administrativo_Usuario_SubRolesUsuarios extends BDT_Tablas {

    public static $schema = BD_ADMIN;

    public static $tableName = 'sub_roles_usuarios';

    public static $primaryKey = 'spu_id';

}