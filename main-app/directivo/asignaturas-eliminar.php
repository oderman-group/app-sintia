<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "DELETE FROM academico_materias WHERE mat_id=".$_GET["id"].";");
	if(mysql_errno()!=0){echo mysql_error(); exit();}
	echo '<script type="text/javascript">window.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
	exit();