<?php
	include("session.php");

	Modulos::validarAccesoDirectoPaginas();
	$idPaginaInterna = 'DC0095';

	include("verificar-carga.php");
	include("verificar-periodos-diferentes.php");

	include("../compartido/historial-acciones-guardar.php");

	//COMPROBAMOS QUE TODOS LOS CAMPOS NECESARIOS ESTEN LLENOS
	if(empty($_POST["nombre"])){
		include("../compartido/guardar-historial-acciones.php");
		echo '<script type="text/javascript">window.location.href="unidades-agregar.php?error=ER_DT_4";</script>';
		exit();
	}
	require_once(ROOT_PATH."/main-app/class/Unidades.php");

	Unidades::guardarUnidades($conexion, $conexionPDO, $config, $cargaConsultaActual, $periodoConsultaActual, $_POST);
	
	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="clases.php?carga='.base64_encode($cargaConsultaActual).'&periodo='.base64_encode($periodoConsultaActual).'&tab=2";</script>';
	exit();