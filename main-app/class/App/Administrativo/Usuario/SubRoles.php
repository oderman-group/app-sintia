<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH."/main-app/class/Conexion.php";
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/SubRolesUsuarios.php';
require_once ROOT_PATH.'/main-app/class/App/Administrativo/Usuario/SubRolesPaginas.php';

class Administrativo_Usuario_SubRoles extends BDT_Tablas {

    public static $schema = BD_ADMIN;

    public static $tableName = 'sub_roles';

    public static $primaryKey = 'subr_id';

    public static function getInfoRolesFromUser(string $idUsuario, int $idInstitucion): array
    {

        $predicado = [
            'spu_id_usuario'  => $idUsuario,
            'spu_institucion' => $idInstitucion
        ];

        $consultaSubRolesUsuario = Administrativo_Usuario_SubRolesUsuarios::Select(
            $predicado,
            'spu_id_sub_rol',
            Administrativo_Usuario_SubRolesUsuarios::$schema
        );

        $datosSubRolesUsuario = [];
        $valoresPaginas       = [];

        if (Administrativo_Usuario_SubRolesUsuarios::numRows($predicado) > 0) {
            $datosSubRolesUsuario = $consultaSubRolesUsuario->fetchAll(PDO::FETCH_ASSOC);
            $datosSubRolesUsuario = array_column($datosSubRolesUsuario, 'spu_id_sub_rol');
            $valoresCadena        = implode(',', $datosSubRolesUsuario);

            //Consulta de paginas habilitadas para los subroles del usuario.
            $consultaPaginaSubRoles = Administrativo_Usuario_SubRolesPaginas::getPagesFromListingRoles($valoresCadena);

            $subRolesPaginas = $consultaPaginaSubRoles->fetchAll(PDO::FETCH_ASSOC);
            $valoresPaginas  = array_column($subRolesPaginas, 'spp_id_pagina');
        }

        return [
            'datos_sub_roles_usuario' => $datosSubRolesUsuario,
            'valores_paginas'         => $valoresPaginas
        ];

    }

}