<?php
include("session.php");
require_once("../class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

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
$ihArray = isset($_POST["ih"]) && is_array($_POST["ih"]) ? $_POST["ih"] : null;
$dgArray = isset($_POST["dg"]) && is_array($_POST["dg"]) ? $_POST["dg"] : null;

while ($contCurso < $numCurso) {

    $numGrupo = (count($_POST["grupo"]));
    $contGrupo = 0;
    while ($contGrupo < $numGrupo) {

        $numAsignatura = (count($_POST["asignatura"]));
        $contAsignatura = 0;
        while ($contAsignatura < $numAsignatura) {

            $indiceCombinacion = $contCurso * ($numGrupo * $numAsignatura) + $contGrupo * $numAsignatura + $contAsignatura;
            $ih = ($ihArray !== null && isset($ihArray[$indiceCombinacion])) ? max(1, (int) $ihArray[$indiceCombinacion]) : max(1, (int) (isset($_POST["ih"]) ? $_POST["ih"] : 1));
            $dg = ($dgArray !== null && isset($dgArray[$indiceCombinacion])) ? (int) $dgArray[$indiceCombinacion] : (int) (isset($_POST["dg"]) ? $_POST["dg"] : 0);

            $existeCarga = CargaAcademica::validarExistenciaCarga($_POST["docente"], $_POST["curso"][$contCurso], $_POST["grupo"][$contGrupo], $_POST["asignatura"][$contAsignatura]);

            if(!$existeCarga) {
                $tematicaValor = isset($_POST["tematica"]) && $_POST["tematica"] !== '' ? $_POST["tematica"] : 0;
                $observacionesBoletinValor = isset($_POST["observacionesBoletin"]) && $_POST["observacionesBoletin"] !== '' ? $_POST["observacionesBoletin"] : 0;
                $indicadoresDirectivoValor = isset($_POST["indicadoresDirectivo"]) && $_POST["indicadoresDirectivo"] !== '' ? $_POST["indicadoresDirectivo"] : 0;
                $idInsercion = CargaAcademica::guardarCarga($conexionPDO, "car_docente, car_curso, car_grupo, car_materia, car_periodo, car_activa, car_permiso1, car_director_grupo, car_ih, car_fecha_creada, car_responsable, car_maximos_indicadores, car_maximas_calificaciones, car_configuracion, car_valor_indicador, car_indicador_automatico, car_observaciones_boletin, car_tematica, car_posicion_docente, car_indicadores_directivo, institucion, year, car_id", [$_POST["docente"], $_POST["curso"][$contCurso], $_POST["grupo"][$contGrupo],$_POST["asignatura"][$contAsignatura], $_POST["periodo"], 1, 1, $dg, $ih, date("Y-m-d H:i:s"), $_SESSION["id"], $_POST["maxIndicadores"], $_POST["maxActividades"], $_POST["valorActividades"], $_POST["valorIndicadores"], $_POST["indicadorAutomatico"], $observacionesBoletinValor, $tematicaValor, 1, $indicadoresDirectivoValor, $config['conf_id_institucion'], $_SESSION["bd"]]);
                $cargasCreadas ++;
                
                // Si se solicita aplicar indicadores obligatorios existentes
                if (!empty($_POST["aplicarIndicadores"]) && $_POST["aplicarIndicadores"] == "1" && !empty($_POST["indicadoresSeleccionados"]) && is_array($_POST["indicadoresSeleccionados"])) {
                    $periodosAplicar = !empty($_POST["periodosAplicar"]) && is_array($_POST["periodosAplicar"]) ? $_POST["periodosAplicar"] : [$_POST["periodo"]];
                    
                    foreach ($_POST["indicadoresSeleccionados"] as $idIndicador) {
                        // Obtener el valor del indicador
                        $datosIndicador = Indicadores::traerIndicadoresDatos($idIndicador);
                        if (!empty($datosIndicador)) {
                            $resultadoAsignacion = Indicadores::asignarIndicadorACargas(
                                $conexion,
                                $conexionPDO,
                                $config,
                                $idIndicador,
                                [$idInsercion],
                                $periodosAplicar,
                                (float)$datosIndicador['ind_valor']
                            );
                        }
                    }
                }
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