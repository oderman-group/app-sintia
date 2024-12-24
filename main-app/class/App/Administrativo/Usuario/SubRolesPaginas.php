<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH."/main-app/class/Conexion.php";

class Administrativo_Usuario_SubRolesPaginas extends BDT_Tablas {

    public static $schema = BD_ADMIN;

    public static $tableName = 'sub_roles_paginas';

    public static $primaryKey = 'spp_id';

    public static function getPagesFromListingRoles(string $roles): null|PDOStatement 
    {
        $conexionPDO = Conexion::newConnection('PDO');

        $sql = "SELECT * 
        FROM " . self::$schema . "." . self::$tableName . "
        WHERE 
            spp_id_rol IN ($roles)";

        try {
            $stmt = $conexionPDO->prepare($sql);
            $stmt->execute();

            return $stmt;
        } catch (PDOException  $e) {
            echo "Excepción capturada: ". $e->getMessage();
            return null;
        }

    }

}