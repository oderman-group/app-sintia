<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0129';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

$consultaNumTema = Indicadores::consultaTematica($cargaConsultaActual, $periodoConsultaActual);
$numTema = mysqli_num_rows($consultaNumTema);



if($numTema>0){

	$update = "ind_nombre=".$_POST["contenido"]."";
	Indicadores::actualizarIndicadorCargaPeriodo($config, $cargaConsultaActual, $periodoConsultaActual, $update);
}else{
	$codigo = Indicadores::guardarIndicador($conexionPDO, "ind_nombre, ind_obligatorio, ind_periodo, ind_carga, ind_fecha_creacion, ind_tematica, institucion, year, ind_id", [mysqli_real_escape_string($conexion,$_POST["contenido"]), 0, $periodoConsultaActual, $cargaConsultaActual, date("Y-m-d H:i:s"), 1, $config['conf_id_institucion'], $_SESSION["bd"]]);
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="clases.php?success=SC_GN_3&carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'&tab=4";</script>';
exit();