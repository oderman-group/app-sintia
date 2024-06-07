<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0144';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

$idR = "";
if (!empty($_GET['idR'])) {
    $idR = base64_decode($_GET['idR']);
}

$actividadesRelacionadasConsulta = Actividades::traerActividadesCargaIndicador($config, base64_decode($_GET["idIndicador"]), $cargaConsultaActual, $periodoConsultaActual);

while($actividadesRelacionadasDatos = mysqli_fetch_array($actividadesRelacionadasConsulta, MYSQLI_BOTH)){
    Actividades::eliminarActividadCalificacionesIndicador($config, $cargaConsultaActual, $actividadesRelacionadasDatos['act_id'], $periodoConsultaActual);
}

Indicadores::eliminarIndicador($conexion, $config, $idR);

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);

//Si decide poner los valores porcentuales de los indicadores de forma manual
if($datosCargaActual['car_valor_indicador']==1){}else{
//El sistema reparte los porcentajes automáticamente y equitativamente.
    $valorIgualIndicador = ($porcentajePermitido/($sumaIndicadores[2]));
    //Actualiza todos valores de la misma carga y periodo.
    Indicadores::actualizarValorIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $valorIgualIndicador);
}

//Si los valores de las calificaciones son de forma automática.
if($datosCargaActual['car_configuracion']==0){
	Calificaciones::actualizarValorCalificacionesDeUnaCarga($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="indicadores.php?error=ER_DT_3";</script>';
exit();