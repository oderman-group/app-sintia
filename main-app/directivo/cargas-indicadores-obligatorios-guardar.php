<?php
include("session.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0185';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
		exit();
	}

	// Validar períodos
	if (empty($_POST["periodos"]) || !is_array($_POST["periodos"])) {
		include("../compartido/guardar-historial-acciones.php");
		echo "<span style='font-family:Arial; color:red;'>Debe seleccionar al menos un período.</samp>";
		exit();
	}
	
	$ind = Indicadores::consultarValorIndicadoresObligatorios();
	$sumaActual = !empty($ind[0]) ? (float)$ind[0] : 0;
	$valorNuevo = (float)$_POST["valor"];

	if (($sumaActual + $valorNuevo) > 100) {
		echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
		exit();
	}
	
	// Crear el indicador obligatorio
	$codigo = Indicadores::guardarIndicador($conexionPDO, "ind_nombre, ind_valor, ind_obligatorio, institucion, year, ind_id", [mysqli_real_escape_string($conexion,$_POST["nombre"]),$_POST["valor"],1, $config['conf_id_institucion'], $_SESSION["bd"]]);
	
	// Determinar cargas a asignar
	$cargas = [];
	if (!empty($_POST["asignacionTipo"]) && $_POST["asignacionTipo"] == "especificas" && !empty($_POST["cargas"]) && is_array($_POST["cargas"]) && count($_POST["cargas"]) > 0) {
		$cargas = $_POST["cargas"];
	}
	// Si es "todas", el array queda vacío y el método asignarIndicadorACargas obtendrá todas las cargas activas con car_indicadores_directivo=1

	// Asignar indicador a cargas y períodos
	$resultadoAsignacion = Indicadores::asignarIndicadorACargas(
		$conexion,
		$conexionPDO,
		$config,
		$codigo,
		$cargas,
		$_POST["periodos"],
		(float)$_POST["valor"]
	);

	// Mostrar mensaje de resultado
	$mensaje = "";
	if ($resultadoAsignacion['asignadas'] > 0) {
		$mensaje = "Indicador creado y asignado a {$resultadoAsignacion['asignadas']} relación(es) de carga/período.";
	}
	if ($resultadoAsignacion['duplicadas'] > 0) {
		$mensaje .= ($mensaje ? " " : "") . "Se omitieron {$resultadoAsignacion['duplicadas']} asignación(es) duplicada(s).";
	}
	if (!empty($resultadoAsignacion['errores'])) {
		$mensaje .= ($mensaje ? " " : "") . "Errores: " . implode(", ", $resultadoAsignacion['errores']);
	}
	
	// Si no hubo asignaciones ni duplicadas ni errores, puede ser que no haya cargas disponibles
	if ($resultadoAsignacion['asignadas'] == 0 && $resultadoAsignacion['duplicadas'] == 0 && empty($resultadoAsignacion['errores'])) {
		$mensaje = "Indicador creado, pero no se pudieron asignar cargas. Verifique que existan cargas académicas con la configuración 'Indicadores definidos por directivo' activada.";
	}

	include("../compartido/guardar-historial-acciones.php");
	
	if (!empty($mensaje)) {
		echo '<script type="text/javascript">alert("' . addslashes($mensaje) . '"); window.location.href="cargas-indicadores-obligatorios.php";</script>';
	} else {
		echo '<script type="text/javascript">window.location.href="cargas-indicadores-obligatorios.php";</script>';
	}
	exit();