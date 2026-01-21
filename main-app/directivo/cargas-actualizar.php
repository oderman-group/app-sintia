<?php
include("session.php");
require_once(ROOT_PATH."/main-app/class/Sysjobs.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Utilidades.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Tables/BDT_academico_cargas.php");

Modulos::validarAccesoDirectoPaginas();
$idPaginaInterna = 'DT0167';

if (!Modulos::validarSubRol([$idPaginaInterna])) {
	echo '<script type="text/javascript">window.location.href="page-info.php?idmsg=301";</script>';
	exit();
}

include("../compartido/historial-acciones-guardar.php");

	$existeCarga = false;

	if (
		$_POST["docente"] != $_POST["docenteActual"] || 
		$_POST["curso"] != $_POST["cursoActual"] || 
		$_POST["grupo"] != $_POST["grupoActual"] || 
		$_POST["asignatura"] != $_POST["asignaturaActual"]
	) {
		$existeCarga = CargaAcademica::validarExistenciaCarga($_POST["docente"], $_POST["curso"], $_POST["grupo"], $_POST["asignatura"]);
	}

	if (!$existeCarga) {

		if ($_POST["periodo"] != $_POST["periodoActual"]) {
			$parametros = [
				"carga" 	=> $_POST["idR"],
				"periodo" 	=> $_POST["periodo"],
				"grado" 	=> $_POST["curso"],
				"grupo"		=> $_POST["grupo"]
			];

			$parametrosBuscar = [
				"tipo" 			=> JOBS_TIPO_GENERAR_INFORMES,
				"responsable" 	=> $_POST["docente"],
				"parametros" 	=> json_encode($parametros),
				"agno"			=> $config['conf_agno'],
				"estado"		=> JOBS_ESTADO_FINALIZADO
			];

			$buscarJobs = SysJobs::consultar($parametrosBuscar);

			if (mysqli_num_rows($buscarJobs) > 0) {
				$jobsEncontrado = mysqli_fetch_array($buscarJobs, MYSQLI_BOTH);

				try {
					mysqli_query($conexion, "DELETE FROM ".$baseDatosServicios.".sys_jobs WHERE job_id='".$jobsEncontrado["job_id"]."'");
				} catch (Exception $e) {
					include("../compartido/error-catch-to-report.php");
				}
			}

		}

		$update = [
			'car_docente'                => $_POST["docente"], 
			'car_curso'                  => $_POST["curso"], 
			'car_grupo'                  => $_POST["grupo"], 
			'car_materia'                => $_POST["asignatura"], 
			'car_periodo'                => $_POST["periodo"], 
			'car_director_grupo'         => $_POST["dg"], 
			'car_ih'                     => $_POST["ih"], 
			'car_activa'                 => $_POST["estado"], 
			'car_maximos_indicadores'    => $_POST["maxIndicadores"], 
			'car_maximas_calificaciones' => $_POST["maxActividades"], 
			'car_configuracion'          => $_POST["valorActividades"], 
			'car_valor_indicador'        => $_POST["valorIndicadores"], 
			'car_permiso1'               => $_POST["permiso1"],
			'car_indicador_automatico'   => $_POST["indicadorAutomatico"],
			'car_observaciones_boletin'  => $_POST["observacionesBoletin"],
			'car_tematica'               => $_POST["tematica"], 
		];

		$predicado = [
			'car_id'      => $_POST["idR"],
			'institucion' => $config['conf_id_institucion'],
			'year'        => $_SESSION["bd"],
		];

		$campos = "car_periodo, car_estado, car_historico";

		$datosCargaActualConsulta = BDT_AcademicoCargas::select($predicado, $campos, BD_ACADEMICA);
		$datosCargaActual         = $datosCargaActualConsulta->fetchAll();

		if ($_POST["periodo"] != $datosCargaActual[0]['car_periodo']) {
			$update['car_estado'] = BDT_AcademicoCargas::ESTADO_DIRECTIVO;

			$carHistoricoArray = [];
			$carHistoricoCampo = $datosCargaActual[0]['car_historico'];

			if (!empty($carHistoricoCampo)) {
				$carHistoricoArray = json_decode($carHistoricoCampo, true);
			}

			$carHistoricoArray[$_POST["idR"].':'.time()] = [
				'car_periodo_anterior' => (int) $datosCargaActual[0]['car_periodo'],
				'car_estado_anterior'  => $datosCargaActual[0]['car_estado'],
			];

			$update['car_historico'] = json_encode($carHistoricoArray);

			$updateBoletin = [
				'bol_estado' => Boletin::ESTADO_ABIERTO,
			];

			$where = "bol_carga='{$_POST["idR"]}' AND bol_periodo={$_POST["periodo"]}";

			Boletin::actualizarBoletin($config, $updateBoletin, $where);
		}

		CargaAcademica::actualizarCargaPorID($config, $_POST["idR"], $update);

		Grados::eliminarIntensidadMateriaCurso($conexion, $config, $_POST["curso"], $_POST["asignatura"]);
		
		Grados::guardarIntensidadMateriaCurso($conexion, $conexionPDO, $config, $_POST["curso"], $_POST["asignatura"], $_POST["ih"]);

		$mensaje = 'success=SC_DT_2';
	} else {
		$mensaje = 'error=ER_DT_20';
	}

include("../compartido/guardar-historial-acciones.php");
echo '<script type="text/javascript">window.location.href="cargas-editar.php?idR='.base64_encode($_POST["idR"]).'&'.$mensaje.'&id='.base64_encode($_POST["idR"]).'";</script>';
exit();