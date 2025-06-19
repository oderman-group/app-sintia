<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';

class Academico_Matriculas_Adjuntos extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_matriculas_adjuntos';
    public  static $tableAs   = 'ama';
    public static $primaryKey = 'ama_id';

    use BDT_Join;

    /**
     * Obtiene los documentos adjuntos de un estudiante por año y por institucion
     *
     * @param int $idEstudiante - ID del estudiante
     * @param int $anno - Año académico
     * @param string $institucion - Código de la institución
     * @return array
     */
    public static function ObtenerDocumentosxEstudiante($idEstudiante,$anno,$idInstitucion)
    {  
        Administrativo_Usuario_Usuario::foreignKey(Administrativo_Usuario_Usuario::INNER, [
            'uss_id'      => self::$tableAs.'.ama_id_responsable',
            'year'        => self::$tableAs.'.year',
            'institucion' => self::$tableAs.'.institucion'
        ]);

        $camposWhere = [
            self::$tableAs.'.ama_id_estudiante' => $idEstudiante,
            self::$tableAs.'.year'                  => $anno,
            self::$tableAs.'.institucion'           => $idInstitucion,
        ];

        $camposSelect = self::$tableAs.'.ama_id,' .
                        self::$tableAs.'.ama_fecha_registro,' .
                        self::$tableAs.'.ama_id_estudiante,' .
                        self::$tableAs.'.ama_titulo,' .
                        self::$tableAs.'.ama_descripcion,' .
                        'IF('.Administrativo_Usuario_Usuario::$tableAs .'.uss_tipo = 1, "Proceso interno", "Evidencia") categoria,' .
                        self::$tableAs.'.ama_documento,' .
                        self::$tableAs.'.ama_visible,' .
                        self::$tableAs.'.ama_id_responsable,' .
                        self::$tableAs.'.institucion,' .
                        self::$tableAs.'.year,' .
                        Administrativo_Usuario_Usuario::$tableAs . '.uss_usuario, '.
                        Administrativo_Usuario_Usuario::$tableAs . '.uss_tipo, '.
                        Administrativo_Usuario_Usuario::$tableAs.'.uss_nombre';

        $orderBy = self::$tableAs.'.ama_fecha_registro DESC';

        return self::SelectJoin($camposWhere, $camposSelect, self::class, [Administrativo_Usuario_Usuario::class], '', $orderBy);

    }

}