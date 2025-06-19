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

        $camposWhere = [
            Administrativo_Usuario_Usuario::$tableAs.'.uss_tipo'     => TIPO_DIRECTIVO,
            Administrativo_Usuario_Usuario::$tableAs.'.year'         => $anno,
            Administrativo_Usuario_Usuario::$tableAs.'.institucion'  => $institucion,
        ];

        $camposSelect = Administrativo_Usuario_Usuario::$tableAs.'.uss_id, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_usuario, '.
                        Administrativo_Usuario_Usuario::$tableAs . '.uss_email, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_nombre, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_bloqueado, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_estado, '.
                        'IFNULL('.self::$tableAs.'.upn_id, 0) upn_id';

        return self::SelectJoin($camposWhere, $camposSelect, Administrativo_Usuario_Usuario::class, [self::class]);

    }

    /**
     * Obtiene los usuarios suscritos a un tipo de notificación específico.
     * 
     * @param int $tipoNotificacion Tipo de notificación a consultar.
     * @param int $anno Año a consultar.
     * @param int $institucion ID de la institución a consultar.
     * @return array Lista de usuarios suscritos con su información.
     * 
     */
    public static function ObtenerUsuariosSuscritosxTipoNotificacion($tipoNotificacion,$anno,$institucion)
    {  
        Administrativo_Usuario_Usuario::foreignKey(Administrativo_Usuario_Usuario::INNER, [
            'uss_id'      => self::$tableAs.'.upn_usuario',
            'year'        => self::$tableAs.'.year',
            'institucion' => self::$tableAs.'.institucion'
        ]);

        $camposWhere = [
            self::$tableAs.'.upn_tipo_notificacion' => $tipoNotificacion,
            self::$tableAs.'.year'                  => $anno,
            self::$tableAs.'.institucion'           => $institucion,
        ];

        $camposSelect = self::$tableAs.'.upn_usuario,' .
                        Administrativo_Usuario_Usuario::$tableAs . '.uss_email, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_nombre';

        return self::SelectJoin($camposWhere, $camposSelect, self::class, [Administrativo_Usuario_Usuario::class]);

    }
}