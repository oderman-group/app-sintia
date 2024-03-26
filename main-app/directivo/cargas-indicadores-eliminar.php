<?php 
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0087';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include("verificar-carga.php");

$idR = "";
if (!empty($_GET['idR'])) {
    $idR = base64_decode($_GET['idR']);
}

$actividadesRelacionadasConsulta = Actividades::consultaActividadesCargaIndicador($config, base64_decode($_GET["idIndicador"]), base64_decode($_GET["carga"]), base64_decode($_GET["periodo"]));
while ($actividadesRelacionadasDatos = mysqli_fetch_array($actividadesRelacionadasConsulta, MYSQLI_BOTH)) {
	Actividades::eliminarActividadDirectivo($config, $actividadesRelacionadasDatos['act_id'], $cargaConsultaActual, $periodoConsultaActual);
}

Indicadores::eliminarIndicador($conexion, $config, $idR);

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

//Si decide poner los valores porcentuales de los indicadores de forma manual
if ($datosCargaActual['car_valor_indicador'] == 1) {
}
//El sistema reparte los porcentajes automáticamente y equitativamente.
else {
	$valorIgualIndicador = 0;
	if(!empty($sumaIndicadores[2])){ $valorIgualIndicador = ($porcentajePermitido / ($sumaIndicadores[2])); }
	//Actualiza todos valores de la misma carga y periodo.
	Indicadores::actualizarValorIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $valorIgualIndicador);

	//Si decide que los valores de las calificaciones son de forma automática.
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
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cargas-indicadores.php?carga=' . $_GET["carga"] . '&docente=' . $_GET["docente"] . '";</script>';
exit();