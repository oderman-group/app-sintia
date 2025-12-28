<?php
	include("session.php");
	$idPaginaInterna = 'DT0299';
	require_once(ROOT_PATH."/main-app/class/Movimientos.php");
	require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

	Modulos::validarAccesoDirectoPaginas();

	if(!Modulos::validarSubRol([$idPaginaInterna])){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}

	$id = '';
	if (!empty($_GET['id'])) {
		$id = base64_decode($_GET['id']);
	}

	try {
		Movimientos::eliminarImpuestos($conexion, $config, $id);
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="impuestos.php?success=SC_DT_3";</script>';
		exit();
	} catch (Exception $e) {
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		$mensajeError = urlencode("No se puede eliminar el impuesto porque está asociado a uno o más registros de transacciones.");
		echo '<script type="text/javascript">alert("' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '"); window.location.href="impuestos.php?error=ER_DT_CREATE&msj=' . $mensajeError . '";</script>';
		exit();
	}