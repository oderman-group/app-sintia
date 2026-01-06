<?php
	include("session.php");
	$idPaginaInterna = 'DT0279';
	require_once(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
	require_once(ROOT_PATH."/main-app/class/Movimientos.php");

	Modulos::validarAccesoDirectoPaginas();

	if(!Modulos::validarSubRol([$idPaginaInterna])){
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(empty($_POST["cba_nombre"]) || empty($_POST["cba_tipo"]) || empty($_POST["cba_metodo_pago_asociado"])){
		require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="cuentas-bancarias-agregar.php?error=ER_DT_4";</script>';
		exit();
	}

	$codigo=Movimientos::guardarCuentaBancaria($conexion, $config, $_POST);
	
	require_once(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cuentas-bancarias.php?success=SC_DT_1&id='.base64_encode($codigo).'";</script>';
	exit();


