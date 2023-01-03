<?php include("session.php"); ?>
<?php include("../modelo/conexion.php"); ?>
<?php
	mysqli_query($conexion, "UPDATE usuarios SET uss_usuario=(SELECT mat_documento FROM academico_matriculas WHERE mat_id_usuario=uss_id AND mat_documento!='') WHERE uss_tipo=4");
	echo '<script type="text/javascript">window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>';
	exit();