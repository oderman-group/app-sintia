<?php
	include("session.php");
	require_once(ROOT_PATH."/main-app/class/Grados.php");
	
	$update = "gra_formato_boletin=1";
	Grados::actualizarTodosCursos($config, $update);
	
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();