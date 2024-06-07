<?php
include("session.php");
require_once("../class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0183';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

$cargasNoCreadas = 0;
$cargasCreadas = 0;
$numCurso = (count($_POST["curso"]));
$contCurso = 0;
while ($contCurso < $numCurso) {

    $numGrupo = (count($_POST["grupo"]));
    $contGrupo = 0;
    while ($contGrupo < $numGrupo) {

        $numAsignatura = (count($_POST["asignatura"]));
        $contAsignatura = 0;
        while ($contAsignatura < $numAsignatura) {

            $existeCarga = CargaAcademica::validarExistenciaCarga($_POST["docente"], $_POST["curso"][$contCurso], $_POST["grupo"][$contGrupo], $_POST["asignatura"][$contAsignatura]);

            if(!$existeCarga) {
                $idInsercion = CargaAcademica::guardarCarga($conexionPDO, "car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, car_maximos_indicadores, car_maximas_calificaciones, car_configuracion, car_valor_indicador, car_permiso2, car_indicador_automatico, car_observaciones_boletin, car_posicion_docente, institucion, year, car_id", [$_POST["docente"], $_POST["curso"][$contCurso], $_POST["grupo"][$contGrupo],$_POST["asignatura"][$contAsignatura], $_POST["periodo"], 1, 1, $_POST["dg"], $_POST["ih"], date("Y-m-d H:i:s"), $_SESSION["id"], $_POST["maxIndicadores"], $_POST["maxActividades"], $_POST["valorActividades"], $_POST["valorIndicadores"], $_POST["permiso2"], $_POST["indicadorAutomatico"], 0, 1, $config['conf_id_institucion'], $_SESSION["bd"]]);
                $cargasCreadas ++;
            } else {
                $cargasNoCreadas ++;
            }
            $contAsignatura++;
        }
        $contGrupo++;
    }
    $contCurso++;
}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cargas.php?docente='.base64_encode($_POST["docente"]).'&id='.base64_encode($idInsercion).'&success=SC_DT_6&creadas='.base64_encode($cargasCreadas).'&noCreadas='.base64_encode($cargasNoCreadas).'";</script>';
exit();