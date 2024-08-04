<?php
require_once(ROOT_PATH."/main-app/class/Conexion.php");

class Instituciones {

    /**
     * Retrieves data of a specific institution from the database.
     *
     * @param int $institutionId The unique identifier of the institution.
     *
     * @return mysqli_result|bool Returns a mysqli_result object containing the institution's data if found,
     *                           or false if no data is found or an error occurred.
     *
     * @throws Exception If the database connection fails.
     */
    public static function getDataInstitution($institutionId) {
        $conexionBaseDatosServicios = Conexion::newConnection('MYSQL');

        $institucionConsulta = mysqli_query($conexionBaseDatosServicios, "SELECT * FROM ".BD_ADMIN.".instituciones 
        WHERE ins_estado = 1 AND ins_id='".$institutionId."' AND ins_enviroment='".ENVIROMENT."'");

        return $institucionConsulta;
    }

}