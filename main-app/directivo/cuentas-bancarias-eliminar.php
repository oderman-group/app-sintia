<?php
	include("session.php");
	$idPaginaInterna = 'DT0281';
	require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
	require_once(ROOT_PATH."/main-app/class/Movimientos.php");

	Modulos::validarAccesoDirectoPaginas();

	if(!Modulos::validarSubRol([$idPaginaInterna])){
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}

	$id = '';
	if (!empty($_GET['id'])) {
		$id = base64_decode($_GET['id']);
	}

	if (empty($id)) {
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="cuentas-bancarias.php?error=ER_DT_4";</script>';
		exit();
	}

	// Validar si la cuenta bancaria está en uso
	require_once(ROOT_PATH."/main-app/class/Movimientos.php");
	if (Movimientos::validarCuentaBancariaEnUso($conexion, $config, $id)) {
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">alert("No se puede eliminar la cuenta bancaria porque está asociada a uno o más pagos."); window.location.href="cuentas-bancarias.php?error=ER_DT_CREATE&msj=No+se+puede+eliminar+la+cuenta+bancaria+porque+está+asociada+a+uno+o+más+pagos.";</script>';
		exit();
	}

	// En lugar de eliminar, desactivamos la cuenta
	require_once(ROOT_PATH."/main-app/class/Conexion.php");
	$conexionPDO = Conexion::newConnection('PDO');
	$conexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	try {
		$sql = "UPDATE ".BD_FINANCIERA.".finanzas_cuentas_bancarias SET cba_activa=0 WHERE cba_id=? AND institucion=? AND year=?";
		$stmt = $conexionPDO->prepare($sql);
		$stmt->bindParam(1, $id, PDO::PARAM_STR);
		$stmt->bindParam(2, $config['conf_id_institucion'], PDO::PARAM_INT);
		$stmt->bindParam(3, $_SESSION["bd"], PDO::PARAM_INT);
		$stmt->execute();
	} catch (Exception $e) {
		include(ROOT_PATH."/main-app/compartido/error-catch-to-report.php");
	}

	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias.php?success=SC_DT_3&id='.base64_encode($id).'";</script>';
	exit();


