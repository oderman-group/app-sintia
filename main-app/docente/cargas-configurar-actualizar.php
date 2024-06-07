<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0115';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

if(empty($_POST["indicadores"])) $_POST["indicadores"] = '0';
if(empty($_POST["calificaciones"])) $_POST["calificaciones"] = '0';
if(empty($_POST["fechaInforme"])) $_POST["fechaInforme"] = '2000-12-31';
if(empty($_POST["posicion"])) $_POST["posicion"] = '0';

$update = "car_valor_indicador=".$_POST["indicadores"].", car_configuracion=".$_POST["calificaciones"].", car_fecha_generar_informe_auto=".$_POST["fechaInforme"].", car_posicion_docente=".$_POST["posicion"]."";
CargaAcademica::actualizarCargaPorID($config, $cargaConsultaActual, $update);

//Se recalcula valores de los indicadores cuando es automatico
if($_POST["indicadores"] != $_POST["valorIndicadorActual"] && $_POST["indicadores"] == 0) {
		$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
		$porcentajePermitido = 100 - $sumaIndicadores[0];
		//El sistema reparte los porcentajes automáticamente y equitativamente.
		$valorIgualIndicador = ($porcentajePermitido/($sumaIndicadores[2]));

		//Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.
		Indicadores::actualizarValorIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $valorIgualIndicador);
}

//Se recalcula valores de las actividades cuando es automatico
if($_POST["calificaciones"] != $_POST["valorCalificacionActual"] && $_POST["calificaciones"] == 0) {
	Calificaciones::actualizarValorCalificacionesDeUnaCarga($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
}

$infoCargaActual = CargaAcademica::cargasDatosEnSesion(base64_decode($_GET["carga"]), $_SESSION["id"]);
$_SESSION["infoCargaActual"] = $infoCargaActual;

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cargas-configurar.php?carga='.$_GET["carga"].'&periodo='.$_GET["periodo"].'";</script>';
exit();