<?php
if(!isset($_GET["carga"]) or !isset($_GET["periodo"]) or !is_numeric($_GET["carga"]) or !is_numeric($_GET["periodo"])){
	if($_COOKIE["cargaE"]!="" and $_COOKIE["periodoE"]!=""){
		$cargaConsultaActual = $_COOKIE["cargaE"];
		$periodoConsultaActual = $_COOKIE["periodoE"];
	}else{
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
			exit();
	}
}else{
	$cargaConsultaActual = $_GET["carga"];
	$periodoConsultaActual = $_GET["periodo"];
}

$cargaHconsulta = mysqli_query($conexion, "SELECT * FROM academico_cargas_acceso 
WHERE carpa_id_carga='".$cargaConsultaActual."' AND carpa_id_estudiante='".$datosEstudianteActual['mat_id']."'");
$cargaHnum = mysqli_num_rows($cargaHconsulta);
if($cargaHnum==0){
	mysqli_query($conexion, "INSERT INTO academico_cargas_acceso(carpa_id_carga, carpa_id_estudiante, carpa_primer_acceso, carpa_ultimo_acceso, carpa_cantidad)
	VALUES('".$cargaConsultaActual."', '".$datosEstudianteActual['mat_id']."', now(), now(), 1)
	");
}else{
	mysqli_query($conexion, "UPDATE academico_cargas_acceso SET carpa_ultimo_acceso=now(), carpa_cantidad=carpa_cantidad+1
	WHERE carpa_id_carga='".$cargaConsultaActual."' AND carpa_id_estudiante='".$datosEstudianteActual['mat_id']."'");
}

$consultaCargaActual = mysqli_query($conexion, "SELECT * FROM academico_cargas 
INNER JOIN academico_materias ON mat_id=car_materia
INNER JOIN usuarios ON uss_id=car_docente
WHERE car_id='".$cargaConsultaActual."' AND car_curso='".$datosEstudianteActual[6]."' AND car_grupo='".$datosEstudianteActual[7]."' AND car_activa=1");

$numCargaActual = mysqli_num_rows($consultaCargaActual);
$datosCargaActual = mysqli_fetch_array($consultaCargaActual, MYSQLI_BOTH);
if($numCargaActual==0)
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
	exit();		
}else{
	//Verificar si el estudiante está matriculado en cursos de extensión o complementarios
	if($datosCargaActual['car_curso_extension']==1){
		$cursoExtensionNum = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM academico_cargas_estudiantes WHERE carpest_carga='".$datosCargaActual['car_id']."' AND carpest_estudiante='".$datosEstudianteActual['mat_id']."' AND carpest_estado=1"));
		if($cursoExtensionNum==0){
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100&cursoext=1";</script>';
			exit();	
		}
	}
}

if($config['conf_activar_encuesta']==1){
	$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM general_encuestas 
	WHERE genc_estudiante='".$datosEstudianteActual['mat_id']."'"));
	if($respuesta==0 and $datosEstudianteActual[6]!=11){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=214";</script>';
		exit();	
	}
}
?>
