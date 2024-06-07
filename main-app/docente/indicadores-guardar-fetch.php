<?php
include("session.php");
$input = json_decode(file_get_contents("php://input"), true);
Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DC0127';
include(ROOT_PATH . "/main-app/compartido/historial-acciones-guardar.php");
include(ROOT_PATH . "/main-app/compartido/sintia-funciones.php");
include("verificar-carga.php");
include("verificar-periodos-diferentes.php");
require_once(ROOT_PATH . "/main-app/class/Utilidades.php");
require_once(ROOT_PATH . "/main-app/class/Indicadores.php");
require_once(ROOT_PATH . "/main-app/class/Calificaciones.php");


$valores = json_decode($input["valor"], true);


$infoCompartir = 0;
foreach ($valores["indicadores"] as $clave => $descripcion) {
	$sumaIndicadores = Indicadores::consultarSumaIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
	$porcentajePermitido = 100 - $sumaIndicadores[0];
	$porcentajeRestante = ($porcentajePermitido - $sumaIndicadores[1]);
	if ($sumaIndicadores[2] >= $datosCargaActual['car_maximos_indicadores']) {
		include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=209";</script>';
		exit();
	}
	$idRegistro = Indicadores::guardarIndicador($conexionPDO, "ind_nombre, ind_obligatorio, ind_publico, institucion, year, ind_id", [mysqli_real_escape_string($conexion, $descripcion["descripcion"]), 0, $infoCompartir, $config['conf_id_institucion'], $_SESSION["bd"]]);
	//Si decide poner los valores porcentuales de los indicadores de forma manual
	if ($datosCargaActual['car_valor_indicador'] == 1) {
		if ($porcentajeRestante <= 0) {
			include(ROOT_PATH . "/main-app/compartido/guardar-historial-acciones.php");
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
		Indicadores::guardarIndicadorCarga($conexion, $conexionPDO, $config, $cargaConsultaActual, $idRegistro, $periodoConsultaActual, $_POST, NULL, 1);
	} else {
		//El sistema reparte los porcentajes automáticamente y equitativamente.
		$valorIgualIndicador = ($porcentajePermitido / ($sumaIndicadores[2] + 1));
		Indicadores::guardarIndicadorCarga($conexion, $conexionPDO, $config, $cargaConsultaActual, $idRegistro, $periodoConsultaActual, $_POST, NULL, 1);
		//Actualiza todos valores de la misma carga y periodo; incluyendo el que acaba de crear.
		Indicadores::actualizarValorIndicadores($conexion, $config, $cargaConsultaActual, $periodoConsultaActual, $valorIgualIndicador);
	}
	//Si las calificaciones son de forma automática.
	if ($datosCargaActual['car_configuracion'] == 0) {
		Calificaciones::actualizarValorCalificacionesDeUnaCarga($conexion, $config, $cargaConsultaActual, $periodoConsultaActual);
	}
}
