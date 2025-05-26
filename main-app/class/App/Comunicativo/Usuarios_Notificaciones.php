<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Comunicativo_Usuarios_Notificaciones extends BDT_Tablas {
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios_notificaciones';

    public static $primaryKey = 'upn_id';

    CONST TIPO_NOTIFICACION_DESBLOQUEO_USUARIO = 1;

    CONST SQL_DATOS_DIRECTIVOS_NOTIFICACION_DESBLOQUEO = 'SELECT u.uss_id, u.uss_email, u.uss_nombre FROM {$schema}.{$tableName} AS un
    INNER JOIN {$schema}.usuarios u ON u.uss_id=un.upn_usuario
    WHERE un.upn_tipo_notificacion = ' . self::TIPO_NOTIFICACION_DESBLOQUEO_USUARIO;

}