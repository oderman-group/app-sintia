<?php
if(!isset($_GET["carga"]) or !is_numeric($_GET["carga"])){
	if($_COOKIE["carga"]!="" and $_COOKIE["periodo"]!=""){
		$cargaConsultaActual = $_COOKIE["carga"];
		$periodoConsultaActual = $_COOKIE["periodo"];
	}else{
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
			exit();
	}
}else{
	$cargaConsultaActual = $_GET["carga"];
	$periodoConsultaActual = $_GET["periodo"];
}
$consultaCargaH=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_id='".$cargaConsultaActual."'");
$cargaHconsulta = mysqli_fetch_array($consultaCargaH, MYSQLI_BOTH);

if($cargaHconsulta['car_primer_acceso_docente']==""){
	mysqli_query($conexion, "UPDATE academico_cargas SET car_primer_acceso_docente=now() WHERE car_id='".$cargaConsultaActual."'");
	
}else{
	mysqli_query($conexion, "UPDATE academico_cargas SET car_ultimo_acceso_docente=now() WHERE car_id='".$cargaConsultaActual."'");
	
}
//A los directivos no se les consulta el docente ni tampoco el estado de la carga (Activa o Inactiva)
$consultaCargaActual = mysqli_query($conexion, "SELECT * FROM academico_cargas 
INNER JOIN academico_materias ON mat_id=car_materia
INNER JOIN academico_grados ON gra_id=car_curso
INNER JOIN academico_grupos ON gru_id=car_grupo
WHERE car_id='".$cargaConsultaActual."'");

$numCargaActual = mysqli_num_rows($consultaCargaActual);
$datosCargaActual = mysqli_fetch_array($consultaCargaActual, MYSQLI_BOTH);

$configCargasArray = array ("Automático","Manual"); 
$dgArray = array ("NO","SI"); 

if($numCargaActual==0)
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
	exit();		
}
?>