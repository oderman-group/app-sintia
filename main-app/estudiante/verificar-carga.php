<?php
$carga="";
if(!empty($_GET["carga"])){ $carga=base64_decode($_GET["carga"]);}
$periodo="";
if(!empty($_GET["periodo"])){ $periodo=base64_decode($_GET["periodo"]);}

if(!isset($_GET["carga"]) or !isset($_GET["periodo"]) or !is_numeric($periodo)){
	if(!empty($_COOKIE["cargaE"]) and !empty($_COOKIE["periodoE"])){
		$cargaConsultaActual = $_COOKIE["cargaE"];
		$periodoConsultaActual = $_COOKIE["periodoE"];
	}else{
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
			exit();
	}
}else{
	$cargaConsultaActual = $carga;
	$periodoConsultaActual = $periodo;
}
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$cargaHconsulta = CargaAcademica::accesoCargasEstudiante($conexion, $config, $cargaConsultaActual, $datosEstudianteActual['mat_id']);
$cargaHnum = mysqli_num_rows($cargaHconsulta);
if($cargaHnum==0){
	CargaAcademica::guardarAccesoCargasEstudiante($conexion, $conexionPDO, $config, $cargaConsultaActual, $datosEstudianteActual['mat_id']);
}else{
	CargaAcademica::actualizarAccesoCargasEstudiante($conexion, $config, $cargaConsultaActual, $datosEstudianteActual['mat_id']);
}

$consultaCargaActual = mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_cargas car 
INNER JOIN ".BD_ACADEMICA.".academico_materias am ON am.mat_id=car_materia AND am.institucion={$config['conf_id_institucion']} AND am.year={$_SESSION["bd"]}
INNER JOIN ".BD_GENERAL.".usuarios uss ON uss_id=car_docente AND uss.institucion={$config['conf_id_institucion']} AND uss.year={$_SESSION["bd"]}
LEFT JOIN ".$baseDatosServicios.".mediatecnica_matriculas_cursos ON matcur_id_matricula='".$datosEstudianteActual['mat_id']."' AND matcur_estado='".ACTIVO."'
WHERE car_id='".$cargaConsultaActual."' AND car.institucion={$config['conf_id_institucion']} AND car.year={$_SESSION["bd"]} AND (car_curso='".$datosEstudianteActual['mat_grado']."' OR car_curso=matcur_id_curso) AND (car_grupo='".$datosEstudianteActual['mat_grupo']."' OR car_grupo=matcur_id_grupo) AND car_activa=1");

$numCargaActual = mysqli_num_rows($consultaCargaActual);
$datosCargaActual = mysqli_fetch_array($consultaCargaActual, MYSQLI_BOTH);
if($numCargaActual==0)
{
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100";</script>';
	exit();		
}else{
	//Verificar si el estudiante está matriculado en cursos de extensión o complementarios
	if($datosCargaActual['car_curso_extension']==1){
		$cursoExt = CargaAcademica::validarCursosComplementario($conexion, $config, $datosEstudianteActual['mat_id'], $datosCargaActual['car_id']);
		if($cursoExtensionNum==0){
			echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=100&cursoext=1";</script>';
			exit();	
		}
	}
}

if($config['conf_activar_encuesta']==1){
	$respuesta = mysqli_num_rows(mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".general_encuestas 
	WHERE genc_estudiante='".$datosEstudianteActual['mat_id']."' AND genc_institucion={$config['conf_id_institucion']} AND genc_year={$_SESSION["bd"]}"));
	if($respuesta==0 and $datosEstudianteActual['mat_grado']!=11){
		echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=214";</script>';
		exit();	
	}
}
?>
