<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

$consultaEstudiante = Estudiantes::obtenerListadoDeEstudiantes(" AND mat_id='" . $_POST["estudiante"] . "'");
$estudiante = mysqli_fetch_array($consultaEstudiante, MYSQLI_BOTH);

	$cargasConsulta = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $estudiante["mat_grado"], $estudiante["mat_grupo"]);
	$contador=0;
	while ($cargasDatos = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)) {
		$cargasConsultaNuevo = CargaAcademica::traerCargasMateriasPorCursoGrupoMateria($config, $_POST["cursoNuevo"], $_POST["grupoNuevo"], $cargasDatos["car_materia"]);		
        if(!is_null($cargasConsultaNuevo)){
			$update = "bol_carga=".$cargasConsultaNuevo["car_id"]."";
			Boletin::actualizarBoletinCargaEstudiante($config, $cargasDatos['car_id'], $_POST["estudiante"], $update);
			$contador++;
		}		
	}
	
	$update = "mat_grado=" . $_POST["cursoNuevo"] . ", mat_grupo=" . $_POST["grupoNuevo"] . "";
	Estudiantes::actualizarMatriculasPorId($config, $_POST["estudiante"], $update);
	
	include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
	$msj="Se actualizaron (".$contador.") cargas para el estudiante ".Estudiantes::NombreCompletoDelEstudiante($estudiante);
	echo '<script type="text/javascript">window.location.href="estudiantes.php?success=SC_DT_4&summary='.base64_encode($msj).'&id='.base64_encode($_POST["estudiante"]).'";</script>';
	exit();