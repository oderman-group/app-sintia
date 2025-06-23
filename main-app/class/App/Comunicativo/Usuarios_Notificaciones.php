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
            'upn_usuario'            => 'uss_id',
            'year'                   => Administrativo_Usuario_Usuario::$tableAs.'.year',
            'institucion'            => Administrativo_Usuario_Usuario::$tableAs.'.institucion'
        ]);

        $camposWhere = [
            'uss_tipo'     => TIPO_DIRECTIVO,
            'year'         => $anno,
            'institucion'  => $institucion,
        ];

        $camposSelect = 'uss_id, uss_usuario, uss_email, uss_nombre, uss_bloqueado, uss_estado, IFNULL(upn_id, 0) as upn_id';

        return self::SelectJoin($camposWhere, $camposSelect, [self::class]);

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
            'uss_id'      => 'upn_usuario',
            'year'        => self::$tableAs.'.year',
            'institucion' => self::$tableAs.'.institucion'
        ]);

        $camposWhere = [
            'upn_tipo_notificacion' => $tipoNotificacion,
            'year'                  => $anno,
            'institucion'           => $institucion,
        ];

        $camposSelect = 'upn_usuario, uss_email, uss_nombre';

        return self::SelectJoin($camposWhere, $camposSelect, [Administrativo_Usuario_Usuario::class]);

    }
}