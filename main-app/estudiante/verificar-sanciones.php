<?php
$reportesD = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM disciplina_reportes WHERE dr_estudiante='".$_SESSION["id"]."' AND dr_aprobacion_estudiante=0"));
if($reportesD>0){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=215";</script>';
	exit();		
}
?>