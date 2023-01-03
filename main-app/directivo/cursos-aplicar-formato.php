<?php
	include("session.php");
	include("../modelo/conexion.php");
	
	mysqli_query($conexion, "UPDATE academico_grados SET gra_formato_boletin=1");
	
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();