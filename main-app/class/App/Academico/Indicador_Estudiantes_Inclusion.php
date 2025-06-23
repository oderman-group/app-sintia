<?php

require_once ROOT_PATH.'/main-app/class/Tables/BDT_opciones_generales.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_JoinImplements.php';
require_once ROOT_PATH.'/main-app/class/Tables/BDT_Join.php';
require_once ROOT_PATH.'/main-app/class/App/Academico/Carga.php';
require_once ROOT_PATH.'/main-app/class/App/Academico/Indicador_carga.php';
require_once ROOT_PATH.'/main-app/class/App/Academico/Indicador.php';
require_once ROOT_PATH.'/main-app/class/App/Academico/Matricula.php';


class Academico_Indicadores_Estudiantes_Inclusion extends BDT_Tablas implements BDT_JoinImplements
{

    public static $schema    = BD_ACADEMICA;
    public static $tableName = 'academico_indicadores_inclusion';
    public  static $tableAs   = 'aii';
    public static $primaryKey = 'aii_id';

    use BDT_Join;

    /**
     * Obtiene los estudiantes de inclusion de la carga asignada al indicador.
     * @param int $idIndicadorNuevo El ID del indicador nuevo.
     * 
     */
    public static function obtenerEstudiantesInclusionxIdIndicador($idIndicadorNuevo)
    {         

        BDT_OpcionesGenerales::foreignKey(BDT_OpcionesGenerales::INNER, [
            'ogen_id'      => 'mat_genero'
        ]);

        Carga::foreignKey(Carga::INNER, [
            'car_curso'   => 'mat_grado',
            'car_grupo'   => 'mat_grupo',
            'year'        => Matricula::$tableAs.'.year',
            'institucion' => Matricula::$tableAs.'.institucion'
        ]);

        Indicador_carga::foreignKey(Indicador_carga::INNER, [
            'ipc_carga'   => 'car_id',
            'id_nuevo'    => $idIndicadorNuevo,
            'year'        => Carga::$tableAs.'.year',
            'institucion' => Carga::$tableAs.'.institucion'
        ]);

        Indicador::foreignKey(Indicador::INNER, [
            'ind_id'      => 'ipc_indicador',
            'year'        => Indicador_carga::$tableAs.'.year',
            'institucion' => Indicador_carga::$tableAs.'.institucion'
        ]);

        self::foreignKey(self::LEFT, [
            'aii_id_indicador'  => 'ind_id',
            'aii_id_estudiante' => 'mat_id',
            'year'              => Indicador::$tableAs.'.year',
            'institucion'       => Indicador::$tableAs.'.institucion'
        ]);


        $camposWhere = [
            'mat_estado_matricula'  => MATRICULADO,
            'mat_inclusion'         => Matricula::ESTUDIANTE_INCLUSION,
            'year'                  => $_SESSION['bd'],
            'institucion'           => $_SESSION['idInstitucion'],
        ];

        $camposSelect = 'mat_id, mat_documento, mat_nombres, mat_nombre2, mat_primer_apellido, mat_segundo_apellido, ogen_nombre genero, IFNULL(aii_id,0) AS aii_id, aii_fecha, aii_descripcion_indicador AS indicador_inclusion' ;

        $orderBy = 'mat_nombres';

        $clasesJoin = [
            BDT_OpcionesGenerales::class,
            Carga::class,
            Indicador_carga::class,
            Indicador::class,
            self::class
        ];

        return Matricula::SelectJoin($camposWhere, $camposSelect, $clasesJoin, '', $orderBy);

    }

}