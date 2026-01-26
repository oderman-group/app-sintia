<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0170';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
if (trim($_POST["nombre"]) == "" or trim($_POST["valor"]) == "") {
	echo "<span style='font-family:Arial; color:red;'>Debe llenar todos los campos.</samp>";
	exit();
}

// Verificar si el indicador está en uso
$verificacionUso = Indicadores::verificarIndicadorEnUso($config, $_POST["idI"]);
$enUso = $verificacionUso['enUso'];

// Obtener datos actuales del indicador
$datosActuales = Indicadores::traerIndicadoresDatos($_POST["idI"]);
if (empty($datosActuales)) {
	include("../compartido/guardar-historial-acciones.php");
	echo "<span style='font-family:Arial; color:red;'>El indicador no existe.</samp>";
	exit();
}

// Si está en uso, verificar que no se hayan modificado nombre ni valor
if ($enUso) {
	$nombreOriginal = trim($datosActuales['ind_nombre']);
	$nombreEnviado = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : '';
	$valorOriginal = (float)$datosActuales['ind_valor'];
	$valorEnviado = isset($_POST["valor"]) ? (float)$_POST["valor"] : 0;
	
	// Normalizar valores para comparación (eliminar espacios extra, normalizar decimales)
	$nombreOriginalNormalizado = trim($nombreOriginal);
	$nombreEnviadoNormalizado = trim($nombreEnviado);
	$nombreCambiado = $nombreOriginalNormalizado !== $nombreEnviadoNormalizado;
	
	// Comparar valores numéricos con tolerancia para floats
	$diferenciaValor = abs($valorOriginal - $valorEnviado);
	$valorCambiado = $diferenciaValor > 0.0001;
	
	// Log para debug (puede eliminarse después)
	if ($nombreCambiado || $valorCambiado) {
		Utilidades::writeLog('Validación indicador en uso - Nombre original: ' . $nombreOriginal . ', Nombre enviado: ' . $nombreEnviado . ', Valor original: ' . $valorOriginal . ', Valor enviado: ' . $valorEnviado);
	}
	
	if ($nombreCambiado || $valorCambiado) {
		include("../compartido/guardar-historial-acciones.php");
		$mensajeError = "Este indicador está en uso y no se pueden modificar el nombre ni el valor. Solo se pueden asignar nuevas cargas.\n\n" . $verificacionUso['mensaje'];
		echo '<script type="text/javascript">alert("' . addslashes($mensajeError) . '"); window.location.href="cargas-indicadores-obligatorios-editar.php?id=' . base64_encode($_POST["idI"]) . '";</script>';
		exit();
	}
} else {
	// Solo si NO está en uso, validar y actualizar nombre y valor
	$ind = Indicadores::consultarValorIndicadoresObligatorios($_POST["idI"]);
	$sumaActual = !empty($ind[0]) ? (float)$ind[0] : 0;
	$valorNuevo = (float)$_POST["valor"];

	if (($sumaActual + $valorNuevo) > 100) {
		echo "<span style='font-family:Arial; color:red;'>Los valores de los indicadores no deben superar el 100%.</samp>";
		exit();
	}

	$update = [
		'ind_nombre' => $_POST["nombre"], 
		'ind_valor'  => $_POST["valor"]
	];
	Indicadores::actualizarIndicador($config, $_POST["idI"], $update);
}

// Procesar asignaciones de cargas si se enviaron períodos
$mensajeAsignacion = "";
if (!empty($_POST["periodos"]) && is_array($_POST["periodos"]) && count($_POST["periodos"]) > 0) {
	// Determinar cargas a asignar
	$cargas = [];
	if (!empty($_POST["asignacionTipo"]) && $_POST["asignacionTipo"] == "especificas" && !empty($_POST["cargas"]) && is_array($_POST["cargas"]) && count($_POST["cargas"]) > 0) {
		$cargas = $_POST["cargas"];
	}
	// Si es "todas", el array queda vacío y el método asignarIndicadorACargas obtendrá todas las cargas activas con car_indicadores_directivo=1

	// Asignar indicador a cargas y períodos
	// Si está en uso, usar el valor original; si no, usar el valor enviado
	$valorParaAsignar = $enUso ? (float)$datosActuales['ind_valor'] : (float)$_POST["valor"];
	
	$resultadoAsignacion = Indicadores::asignarIndicadorACargas(
		$conexion,
		$conexionPDO,
		$config,
		$_POST["idI"],
		$cargas,
		$_POST["periodos"],
		$valorParaAsignar
	);

	// Construir mensaje de resultado
	if ($enUso) {
		// Si está en uso, solo se pueden asignar nuevas cargas, no se actualiza nombre/valor
		if ($resultadoAsignacion['asignadas'] > 0) {
			$mensajeAsignacion = "Se asignó el indicador a {$resultadoAsignacion['asignadas']} nueva(s) relación(es) de carga/período. (El nombre y valor no se modificaron porque el indicador está en uso)";
		} elseif ($resultadoAsignacion['duplicadas'] > 0 || (isset($resultadoAsignacion['omitidas']) && $resultadoAsignacion['omitidas'] > 0)) {
			// Si hay duplicadas u omitidas, informar
			$mensajeAsignacion = "No se asignaron nuevas cargas. El indicador está en uso, por lo que no se pueden modificar el nombre ni el valor.";
		} else {
			// Si no se seleccionaron períodos o no hay cargas disponibles
			$mensajeAsignacion = "";
		}
	} else {
		// Si NO está en uso, se actualizó nombre/valor y se pueden asignar cargas
		if ($resultadoAsignacion['asignadas'] > 0) {
			$mensajeAsignacion = "Indicador actualizado y asignado a {$resultadoAsignacion['asignadas']} relación(es) de carga/período.";
		} else {
			$mensajeAsignacion = "Indicador actualizado correctamente.";
		}
	}
	
	if ($resultadoAsignacion['duplicadas'] > 0) {
		$mensajeAsignacion .= ($mensajeAsignacion ? " " : "") . "Se omitieron {$resultadoAsignacion['duplicadas']} asignación(es) duplicada(s).";
	}
	if (isset($resultadoAsignacion['omitidas']) && $resultadoAsignacion['omitidas'] > 0) {
		$mensajeAsignacion .= ($mensajeAsignacion ? " " : "") . "Se omitieron {$resultadoAsignacion['omitidas']} asignación(es) porque el docente ya tiene indicadores creados en ese período.";
	}
	if (!empty($resultadoAsignacion['errores'])) {
		$mensajeAsignacion .= ($mensajeAsignacion ? " " : "") . "Errores: " . implode(", ", $resultadoAsignacion['errores']);
	}
	
	// Si no hubo asignaciones ni duplicadas ni omitidas ni errores, puede ser que no haya cargas disponibles
	if ($resultadoAsignacion['asignadas'] == 0 && $resultadoAsignacion['duplicadas'] == 0 && (empty($resultadoAsignacion['omitidas']) || $resultadoAsignacion['omitidas'] == 0) && empty($resultadoAsignacion['errores']) && !$enUso) {
		$mensajeAsignacion = "No se pudieron asignar cargas. Verifique que existan cargas académicas con la configuración 'Indicadores definidos por directivo' activada.";
	}
}

include("../compartido/guardar-historial-acciones.php");

if (!empty($mensajeAsignacion)) {
	echo '<script type="text/javascript">alert("' . addslashes($mensajeAsignacion) . '"); window.location.href="cargas-indicadores-obligatorios.php";</script>';
} else {
	echo '<script type="text/javascript">window.location.href="cargas-indicadores-obligatorios.php";</script>';
}
exit();