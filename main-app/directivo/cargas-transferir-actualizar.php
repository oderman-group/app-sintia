<?php
	include("session.php");
	require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

	Modulos::validarAccesoDirectoPaginas();
	$idPaginaInterna = 'DT0171';

	if(!Modulos::validarSubRol([$idPaginaInterna])){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}
	include("../compartido/historial-acciones-guardar.php");

	foreach ($_POST["cargas"] as $idCarga) {
		$update = "car_docente=" . $_POST["para"] . "";
		CargaAcademica::actualizarCargaPorID($config, $idCarga, $update);
	}

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="cargas.php?success='.base64_encode('SC_DT_13').'";</script>';
	exit();