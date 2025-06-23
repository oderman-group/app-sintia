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
            'uss_id'      => 'ama_id_responsable',
            'year'        => self::$tableAs.'.year',
            'institucion' => self::$tableAs.'.institucion'
        ]);

        $camposWhere = [
            'ama_id_estudiante' => $idEstudiante,
            'year'              => $anno,
            'institucion'       => $idInstitucion,
        ];

        $camposSelect = 'ama_id, ama_fecha_registro, ama_id_estudiante, ama_titulo, ama_descripcion, IF(uss_tipo = 1, "Proceso interno", "Evidencia") categoria, ama_documento, ama_visible, ama_id_responsable,' . self::$tableAs.'.institucion,' . self::$tableAs.'.year, uss_usuario, uss_tipo, uss_nombre';

        $orderBy = 'ama_fecha_registro DESC';

        return self::SelectJoin($camposWhere, $camposSelect, [Administrativo_Usuario_Usuario::class], '', $orderBy);

    }

}