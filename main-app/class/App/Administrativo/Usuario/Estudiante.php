<?php
require_once ROOT_PATH.'/main-app/class/Tables/BDT_tablas.php';
require_once ROOT_PATH.'/main-app/class/App/Academico/Calificacion.php';
require_once ROOT_PATH.'/main-app/class/Boletin.php';

class Administrativo_Usuario_Estudiante extends BDT_Tablas{
    public static $schema = BD_ACADEMICA;

    public static $tableName = 'academico_matriculas';

    public static $primaryKey = 'mat_id';

    public array $estudiante = [];

    public function __construct(array $estudiante) {
        $this->estudiante = $estudiante;
    }

    public function tieneRegistrosAcademicos(): bool {
        return (bool) $this->tieneRegistrosEnBoletin() || $this->tieneRegistrosEnCalificaciones();
    }

    public function tieneRegistrosEnBoletin(): bool {
        $resultado = Boletin::traerNotaBoletinEstudiante(
            [
                'conf_id_institucion' => $_SESSION["idInstitucion"]
            ],
            $this->estudiante['mat_id'],
            $_SESSION["bd"]
        );

        return !empty($resultado);
    }

    public function tieneRegistrosEnCalificaciones(): bool {
        $predicado = [
            'cal_id_estudiante' => $this->estudiante['mat_id'],
            'institucion'       => $_SESSION["idInstitucion"],
            'year'              => $_SESSION['bd']
        ];

        return Academico_Calificacion::contarRegistrosEnCalificaciones($predicado) > 0;
    }
}