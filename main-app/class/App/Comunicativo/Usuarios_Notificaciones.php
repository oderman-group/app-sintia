<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Comunicativo_Usuarios_Notificaciones extends BDT_Tablas {
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios_notificaciones';

    public static $primaryKey = 'upn_id';

    public  static $tableAs = 'upn';

    CONST TIPO_NOTIFICACION_DESBLOQUEO_USUARIO = 1;

    public static function ObtenerUsuariosDirectivosSuscripcion($year,$institucion)
    {  
        $sql = 'SELECT 
                us.uss_id,us.uss_usuario,us.uss_email,us.uss_nombre, us.uss_bloqueado,us.uss_estado, IFNULL(un.upn_id, 0) upn_id
                FROM mobiliar_general.usuarios us
                LEFT JOIN mobiliar_general.usuarios_notificaciones un ON 
                    un.upn_tipo_notificacion = 1 AND 
                    un.upn_usuario = us.uss_id AND 
                    un.institucion = us.institucion AND 
                    un.`year` = us.`year`
                WHERE us.uss_tipo = 5 AND us.institucion = '.$institucion.' AND us.`year` = '.$year.';
            ';

        return self::ejecutarSQL($sql);

    }

}