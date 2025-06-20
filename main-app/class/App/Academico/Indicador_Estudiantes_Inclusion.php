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
            'ogen_id'      => Matricula::$tableAs.'.mat_genero'
        ]);

        Carga::foreignKey(Carga::INNER, [
            'car_curso'   => Matricula::$tableAs.'.mat_grado',
            'car_grupo'   => Matricula::$tableAs.'.mat_grupo',
            'year'        => Matricula::$tableAs.'.year',
            'institucion' => Matricula::$tableAs.'.institucion'
        ]);

        Indicador_carga::foreignKey(Indicador_carga::INNER, [
            'ipc_carga'   => Carga::$tableAs.'.car_id',
            'year'        => Carga::$tableAs.'.year',
            'institucion' => Carga::$tableAs.'.institucion'
        ]);

        Indicador::foreignKey(Indicador::INNER, [
            'ind_id'      => Indicador_carga::$tableAs.'.ipc_indicador',
            'year'        => Indicador_carga::$tableAs.'.year',
            'institucion' => Indicador_carga::$tableAs.'.institucion'
        ]);

        self::foreignKey(self::LEFT, [
            'aii_id_indicador'  => Indicador::$tableAs.'.ind_id',
            'aii_id_estudiante' => Matricula::$tableAs.'.mat_id',
            'year'              => Indicador::$tableAs.'.year',
            'institucion'       => Indicador::$tableAs.'.institucion'
        ]);


        $camposWhere = [
            Matricula::$tableAs.'.mat_estado_matricula' => MATRICULADO,
            Indicador_carga::$tableAs.'.id_nuevo'       => $idIndicadorNuevo,
            Matricula::$tableAs.'.mat_inclusion'         => Matricula::ESTUDIANTE_INCLUSION,
        ];

        $camposSelect = Matricula::$tableAs.'.mat_id,' .
                        Matricula::$tableAs.'.mat_documento,' .
                        Matricula::$tableAs.'.mat_nombres,' .
                        Matricula::$tableAs.'.mat_nombre2,' .
                        Matricula::$tableAs.'.mat_primer_apellido,' .
                        Matricula::$tableAs.'.mat_segundo_apellido,' .
                        BDT_OpcionesGenerales::$tableAs.'.ogen_nombre genero,' .
                        'IFNULL('.self::$tableAs.'.aii_id,0) aii_id,'.
                        self::$tableAs.'.aii_fecha,' .
                        self::$tableAs.'.aii_descripcion_indicador indicador_inclusion' ;

        $orderBy = Matricula::$tableAs.'.mat_nombres';

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