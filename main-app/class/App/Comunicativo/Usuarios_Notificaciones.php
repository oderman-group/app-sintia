<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';

class Comunicativo_Usuarios_Notificaciones extends BDT_Tablas implements BDT_JoinImplements{
    public static $schema = BD_GENERAL;

    public static $tableName = 'usuarios_notificaciones';

    public static $primaryKey = 'upn_id';

    public  static $tableAs = 'upn';

    use BDT_Join;

    CONST TIPO_NOTIFICACION_DESBLOQUEO_USUARIO = 1;
    CONST TIPO_NOTIFICACION_ADJUNTAR_DOCUMENTO_ESTUDIANTE_ACUDIENTE = 2;

    /**
     * Obtiene los usuarios directivos por tipo de notificacion y si tienen suscripción a este.
     * 
     * @param int $tipoNotificacion Tipo de notificación a consultar.
     * @param int $anno Año a consultar.
     * @param int $institucion ID de la institución a consultar.
     * @return array Lista de usuarios directivos con su información y estado de suscripción a las notificaciones.
     * 
     */
    public static function ObtenerUsuariosDirectivosxTipoNotificacionSuscripcion($tipoNotificacion,$anno,$institucion)
    {  

        self::foreignKey(self::LEFT, [
            'upn_tipo_notificacion'  => $tipoNotificacion,
            'upn_usuario'            => Administrativo_Usuario_Usuario::$tableAs.'.uss_id',
            'year'                   => Administrativo_Usuario_Usuario::$tableAs.'.year',
            'institucion'            => Administrativo_Usuario_Usuario::$tableAs.'.institucion'
        ]);

        $predicadoJoin = [
            Administrativo_Usuario_Usuario::$tableAs.'.uss_tipo'     => TIPO_DIRECTIVO,
            Administrativo_Usuario_Usuario::$tableAs.'.year'         => $anno,
            Administrativo_Usuario_Usuario::$tableAs.'.institucion'  => $institucion,
        ];

        $camposJoin = Administrativo_Usuario_Usuario::$tableAs.'.uss_id, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_usuario, '.
                        Administrativo_Usuario_Usuario::$tableAs . '.uss_email, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_nombre, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_bloqueado, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_estado, '.
                        'IFNULL('.self::$tableAs.'.upn_id, 0) upn_id';

        return self::SelectJoin($predicadoJoin, $camposJoin, Administrativo_Usuario_Usuario::class, [self::class]);

    }
}