<?php
	include("session.php");
	require_once(ROOT_PATH."/main-app/class/Grados.php");

	Modulos::validarAccesoDirectoPaginas();
	$idPaginaInterna = 'DT0211';
	
	if(!Modulos::validarSubRol([$idPaginaInterna])){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
		exit();
	}
	include("../compartido/historial-acciones-guardar.php");
	
	$update = "gra_valor_pension=0";
	Grados::actualizarTodosCursos($config, $update);
	
	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();