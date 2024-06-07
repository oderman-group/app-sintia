<?php 
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0193';

if(!Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}
include("../compartido/historial-acciones-guardar.php");

$year=$_SESSION["bd"];
if(!empty($_REQUEST["year"])){
$year=$_REQUEST["year"];
}

$folio=1;
if(!empty($_REQUEST["numFolio"])){$folio=$_REQUEST["numFolio"];}

$filtro = '';
if(!empty($_REQUEST["tipoEstudiantes"]) && $_REQUEST["tipoEstudiantes"]!=0){$filtro .= " AND mat_estado_matricula='".$_REQUEST["tipoEstudiantes"]."'";}

	$consulta = Estudiantes::listarMatriculasFolio($config, $filtro, $year);
	while($datos = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
		$update = "mat_folio=".$folio."";
		Estudiantes::actualizarMatriculasPorId($config, $datos['mat_id'], $update);

		$folio ++ ;
	}

	include("../compartido/guardar-historial-acciones.php");
	echo '<script type="text/javascript">window.location.href="estudiantes.php?success=SC_DT_12";</script>';
	exit();
