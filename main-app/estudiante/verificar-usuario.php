<?php
if($datosUsuarioActual[3]==4){
	$usuarioEstudianteConsultaActual = $_SESSION["id"];
}else{
	if(is_numeric($_GET["usrEstud"])){
		$usuarioEstudianteConsultaActual = $_GET["usrEstud"];
	}else{
		//Redireccionamos
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=300";</script>';
		exit();
	}
}

//ESTUDIANTE ACTUAL
$consultaEstudianteActual = mysqli_query($conexion, "SELECT * FROM academico_matriculas
LEFT JOIN usuarios ON uss_id=mat_acudiente
INNER JOIN academico_grados ON gra_id=mat_grado
WHERE mat_id_usuario='".$usuarioEstudianteConsultaActual."'");

$numEstudianteActual = mysqli_num_rows($consultaEstudianteActual);
$datosEstudianteActual = mysqli_fetch_array($consultaEstudianteActual, MYSQLI_BOTH);
?>