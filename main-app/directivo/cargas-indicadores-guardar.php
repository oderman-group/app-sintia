<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0098';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include("verificar-carga.php");

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);

$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

if ($sumaIndicadores[2] >= $datosCargaActual['car_maximos_indicadores']) {
    include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
    echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=209";</script>';
    exit();
}


$codigoAI = Utilidades::getNextIdSequence($conexionPDO, BD_ACADEMICA, 'academico_indicadores');
try{
    mysqli_query($conexion, "INSERT INTO ".BD_ACADEMICA.".academico_indicadores(ind_id, ind_nombre, ind_obligatorio, ind_publico, institucion, year) VALUES('".$codigoAI."', '" . mysqli_real_escape_string($conexion,$_POST["contenido"]) . "', '" . $_POST["creado"] . "', 0, {$config['conf_id_institucion']}, {$_SESSION["bd"]})");
} catch (Exception $e) {
    include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
}

$codigo=Utilidades::generateCode("IPC");
//Si decide poner los valores porcentuales de los indicadores de forma manual
if ($datosCargaActual['car_valor_indicador'] == 1) {
    if ($porcentajeRestante <= 0) {
        include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
        echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=210&restante=' . $porcentajeRestante . '";</script>';
        exit();
    }
    if (!is_numeric($_POST["valor"])) {
        $_POST["valor"] = 1;
    }
    //Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.
    if ($_POST["valor"] > $porcentajeRestante and $porcentajeRestante > 0) {
        $_POST["valor"] = $porcentajeRestante;
    }
    Indicadores::guardarIndicadorCarga($conexion, $conexionPDO, $config, $cargaConsultaActual, $codigoAI, $periodoConsultaActual, $_POST, NULL, 1);
}
//El sistema reparte los porcentajes automáticamente y equitativamente.
else {
    $valorIgualIndicador = ($porcentajePermitido / ($sumaIndicadores[2] + 1));
    Indicadores::guardarIndicadorCarga($conexion, $conexionPDO, $config, $cargaConsultaActual, $codigoAI, $periodoConsultaActual, $_POST, NULL, 1);
    //Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.
    Indicadores::actualizarValorIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $valorIgualIndicador);
}

//Si las calificaciones son de forma automática.
if ($datosCargaActual['car_configuracion'] == 0) {
    //Repetimos la consulta de los indicadores porque los valores fueron actualizados
    $indicadoresConsultaActualizado = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
    //Actualizamos todas las actividades por cada indicador
    while ($indicadoresDatos = mysqli_fetch_array($indicadoresConsultaActualizado, MYSQLI_BOTH)) {
        $consultaNumActividades = Actividades::consultaActividadesCargaIndicador($config, $indicadoresDatos['ipc_indicador'], $cargaConsultaActual, $periodoConsultaActual);
        $actividadesNum = mysqli_num_rows($consultaNumActividades);
        //Si hay actividades relacionadas al indicador, actualizamos su valor.
        if ($actividadesNum > 0) {
            $valorIgualActividad = ($indicadoresDatos['ipc_valor'] / $actividadesNum);
            Actividades::actualizarValorActividadesIndicador($config, $valorIgualActividad, $indicadoresDatos['ipc_indicador'], $cargaConsultaActual, $periodoConsultaActual);
        }
    }
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cargas-indicadores.php?carga=' . base64_encode($cargaConsultaActual) . '&periodo=' . base64_encode($periodoConsultaActual) . '&docente=' . $_GET["docente"] . '";</script>';
exit();