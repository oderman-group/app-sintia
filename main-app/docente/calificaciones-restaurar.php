<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0132';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

$idIndicador = "";
if (!empty($_GET['idIndicador'])) {
	$idIndicador = base64_decode($_GET['idIndicador']);
}

$indicadoresDatos = Indicadores::consultaIndicadorPeriodo($conexion, $config, $idIndicador, $cargaConsultaActual, $periodoConsultaActual);

//Restauramos la actividad
Actividades::restaurarActividadCalificaciones($config, $cargaConsultaActual, $periodoConsultaActual, base64_decode($_GET["idR"]));

//Si los valores de las calificaciones son de forma automática, recalculamos porcentajes
if($datosCargaActual['car_configuracion']==0){
	//Actualizamos el valor de todas las actividades del indicador
	Calificaciones::actualizarValorCalificacionesDeUnIndicador($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $indicadoresDatos);	
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");

// Construir URL correctamente sin duplicar parámetros
$urlReferer = $_SERVER['HTTP_REFERER'];
$separador = (strpos($urlReferer, '?') !== false) ? '&' : '?';
$urlFinal = $urlReferer . $separador . 'tab=1&error=ER_DT_4_RES';

echo '<script type="text/javascript">window.location.href="'.$urlFinal.'";</script>';
exit();

