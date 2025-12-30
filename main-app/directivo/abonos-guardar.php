<?php
	include("session.php");
	$idPaginaInterna = 'DT0266';
	require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
	require_once(ROOT_PATH."/main-app/class/Movimientos.php");

	Modulos::validarAccesoDirectoPaginas();

	if(!Modulos::validarSubRol([$idPaginaInterna])){
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}

	try {
		$codigo=Movimientos::guardarAbonos($conexion, $config, $_POST, $_FILES);
		
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="abonos.php?success=SC_DT_1&id='.base64_encode($codigo).'";</script>';
		exit();
	} catch (Exception $e) {
		// Capturar errores y redirigir con mensaje amigable
		$mensajeError = $e->getMessage();
		
		// Mensajes de error amigables para el usuario
		$mensajesAmigables = [
			"Debes ingresar al menos un valor de abono a una factura." => "Por favor, ingresa al menos un valor de abono a una factura antes de guardar.",
			"La fecha del documento no puede ser futura." => "La fecha del documento no puede ser futura. Por favor, selecciona una fecha válida.",
			"La fecha del documento no puede ser mayor a un año en el pasado." => "La fecha del documento no puede ser mayor a un año en el pasado. Por favor, selecciona una fecha válida.",
		];
		
		// Buscar mensaje amigable por coincidencia parcial (para errores con valores dinámicos)
		$mensajeFinal = $mensajesAmigables[$mensajeError] ?? null;
		
		// Si no hay coincidencia exacta, buscar por coincidencia parcial
		if ($mensajeFinal === null) {
			if (strpos($mensajeError, "excede el saldo pendiente") !== false) {
				$mensajeFinal = "El valor del abono excede el saldo pendiente de la factura. Por favor, verifica el monto e intenta nuevamente.";
			} elseif (strpos($mensajeError, "Error al validar factura") !== false) {
				$mensajeFinal = "Error al validar la factura. Por favor, verifica los datos e intenta nuevamente.";
			} else {
				$mensajeFinal = "Ocurrió un error al guardar el abono. Por favor, verifica los datos e intenta nuevamente.";
			}
		}
		
		// Registrar el error técnico para debugging (sin mostrarlo al usuario)
		// Solo registrar si no es un error de validación esperado
		if (strpos($mensajeError, "Debes ingresar") === false && 
		    strpos($mensajeError, "fecha del documento") === false &&
		    strpos($mensajeError, "excede el saldo") === false) {
			require_once(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
		}
		
		// Redirigir con mensaje de error amigable
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="abonos-agregar.php?error=ER_DT_CREATE&msj='.urlencode($mensajeFinal).'";</script>';
		exit();
	}