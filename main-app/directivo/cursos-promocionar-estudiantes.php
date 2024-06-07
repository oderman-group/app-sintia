<?php
	include("session.php");
	require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
	require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
	require_once(ROOT_PATH."/main-app/class/Grados.php");
    require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
	require_once(ROOT_PATH."/main-app/class/Boletin.php");

	$filtro = " AND car_curso='".$_POST["desde"]."'";
	$numEstudiantesPromocionados = 0;
	foreach ($_POST["estudiantes"] as $idEstudiantes) {
		$cambiarEstado = !empty($_POST["estado".$idEstudiantes]) ? ", mat_estado_matricula=1" : "";
		$grupo = (!empty($_POST["grupoPara"]) && $_POST["grupoPara"] != 0)  ? $_POST["grupoPara"] : $_POST["grupo".$idEstudiantes];

		$update = "mat_grado=".$_POST["para"].", mat_grupo=".$grupo.", mat_promocionado=1 {$cambiarEstado}";
		Estudiantes::actualizarMatriculasPorId($config, $idEstudiantes, $update);

		if (!empty($_POST['relacionCargas']) || $_POST['relacionCargas'] == 1) {
			$filtro .= (!empty($_POST["grupoDesde"]) && $_POST["grupoDesde"] != 0) ? " AND car_grupo='".$_POST["grupoDesde"]."'" : "";
			$consultaCargas = CargaAcademica::listarCargas($conexion, $config, "", $filtro,"mat_id, car_grupo");
			while($datosCarga = mysqli_fetch_array($consultaCargas, MYSQLI_BOTH)){
				
				$update = "bol_carga=".$_POST["carga".$datosCarga['car_id']]."";
				Boletin::actualizarBoletinCargaEstudiante($config, $datosCarga['car_id'], $idEstudiantes, $update);
				
				Calificaciones::transferirNivelacion($conexion, $config, $_POST["carga".$datosCarga['car_id']], $datosCarga['car_id'], $idEstudiantes);
			}
		}
		$numEstudiantesPromocionados++;
	}

	$consultaGradoActual=Grados::obtenerDatosGrados($_POST["desde"]);
	$gradoActual = mysqli_fetch_array($consultaGradoActual, MYSQLI_BOTH);

	$consultaGrado=Grados::obtenerDatosGrados($_POST["para"]);
	$gradoSiguiente = mysqli_fetch_array($consultaGrado, MYSQLI_BOTH);

	echo '<script type="text/javascript">window.location.href="cursos.php?success=SC_DT_7&curso='.base64_encode($gradoActual['gra_nombre']).'&siguiente='.base64_encode($gradoSiguiente['gra_nombre']).'&numEstudiantesPromocionados='.base64_encode($numEstudiantesPromocionados).'";</script>';
	exit();