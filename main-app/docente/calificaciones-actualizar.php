<?php
include("session.php");
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0114';
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

include(ROOT_PATH."/main-app/compartido/sintia-funciones.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Actividades.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");

$indicadoresDatosC = Indicadores::consultaIndicadorPeriodo($conexion, $config, $_POST['indicador'], $cargaConsultaActual, $periodoConsultaActual);

$valores = Actividades::consultarPorcentajeActividadesIndicador($config, $cargaConsultaActual, $_POST["indicador"], $periodoConsultaActual);

$porcentajeRestante = $indicadoresDatosC['ipc_valor'] - $valores[0];
$porcentajeRestante = ($porcentajeRestante + $_POST["valorCalificacion"]);

$fecha = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["fecha"])));

//Si las calificaciones son de forma automática.
if($datosCargaActual['car_configuracion']==0){
	Actividades::actualizarActividadesCalificacionAutomatica($config, mysqli_real_escape_string($conexion,$_POST["contenido"]), $fecha, $_POST["evidencia"], $_POST["indicador"], $_POST["idR"]);

	//Actualizamos los valores de todas las actividades de la carga
	Calificaciones::actualizarValorCalificacionesDeUnaCarga($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);

}else{
//Si las calificaciones son de forma manual.
	if($porcentajeRestante<=0){
		include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=212&restante='.$porcentajeRestante.'";</script>';
		exit();
	}

	if(!is_numeric($_POST["valor"])){$_POST["valor"]=1;}
	//Si el valor es mayor al adecuado lo ajustamos al porcentaje restante; Siempre que este último sea mayor a 0.
	if($_POST["valor"]>$porcentajeRestante and $porcentajeRestante>0){$_POST["valor"] = $porcentajeRestante;}

	Actividades::actualizarActividadesCalificacionManual($config, mysqli_real_escape_string($conexion,$_POST["contenido"]), $fecha, $_POST["valor"], $_POST["indicador"], $_POST["idR"]);
}

include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="calificaciones.php?success=SC_DT_2&id='.base64_encode($_POST["idR"]).'";</script>';
exit();