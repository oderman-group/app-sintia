<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0128';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
$porcentajePermitido = 100 - $sumaIndicadores[0];
$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
$porcentajeRestante = ($porcentajeRestante + $_POST["valorIndicador"]);

$update = "
	ind_nombre=".mysqli_real_escape_string($conexion,$_POST["contenido"])."
";
Indicadores::actualizarIndicador($config, $_POST["idInd"], $update);

//Si vamos a relacionar los indicadores con los SABERES
if($datosCargaActual['car_saberes_indicador']==1){
	$update="
		ipc_evaluacion=" . $_POST["saberes"] . "
	";
	Indicadores::actualizarRelacionIndicadorCargas($config, $_POST["idR"], $update);
}

//Si los valores de los indicadores son de forma manual
if($datosCargaActual['car_valor_indicador']==1){
	if(!is_numeric($_POST["valor"])){$_POST["valor"]=1;}
	//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.
	if($_POST["valor"]>$porcentajeRestante and $porcentajeRestante>0){$_POST["valor"] = $porcentajeRestante;}

	$update="
		ipc_valor=" . $_POST["valor"] . "
	";
	Indicadores::actualizarRelacionIndicadorCargas($config, $_POST["idR"], $update);
}else{
		//El sistema reparte los porcentajes automáticamente y equitativamente.
		$valorIgualIndicador = ($porcentajePermitido/($sumaIndicadores[2]));

		//Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.
		Indicadores::actualizarValorIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $valorIgualIndicador);
}

//Si los valores de las calificaciones son de forma automática.
if($datosCargaActual['car_configuracion']==0){
	Calificaciones::actualizarValorCalificacionesDeUnaCarga($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="indicadores.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'&carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'";</script>';
exit();