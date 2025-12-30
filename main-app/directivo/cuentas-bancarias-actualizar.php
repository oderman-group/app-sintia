<?php
	include("session.php");
	$idPaginaInterna = 'DT0280';
	require_once(ROOT_PATH."/main-app/class/Movimientos.php");
	require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");

	Modulos::validarAccesoDirectoPaginas();

	if(!Modulos::validarSubRol([$idPaginaInterna])){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(empty($_POST["cba_nombre"]) || empty($_POST["cba_tipo"]) || empty($_POST["cba_metodo_pago_asociado"])){
		echo '<script type="text/javascript">window.location.href="cuentas-bancarias-editar.php?error=ER_DT_4&id='.base64_encode($_POST["cba_id"]).'";</script>';
		exit();
	}

	Movimientos::actualizarCuentaBancaria($conexion, $config, $_POST);

	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias.php?success=SC_DT_2&id='.base64_encode($_POST["cba_id"]).'";</script>';
	exit();


