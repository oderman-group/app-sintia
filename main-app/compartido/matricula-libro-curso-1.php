<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0227';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Plataforma.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
$Plataforma = new Plataforma;

$year=$_SESSION["bd"];
if(isset($_POST["year"])){
	$year=$_POST["year"];
}
if(isset($_GET["year"])){
	$year=base64_decode($_GET["year"]);
}

$periodoActual = 4;
if(isset($_POST["periodo"])){
	$periodoActual=$_POST["periodo"];
}
if(isset($_GET["periodo"])){
	$periodoActual=base64_decode($_GET["periodo"]);
}

switch($periodoActual){
	case 1:
		$periodoActuales = "Primero";
		break;
	case 2:
		$periodoActuales = "Segundo";
		break;
	case 3:
		$periodoActuales = "Tercero";
		break;
	case 4:
		$periodoActuales = "Final";
		break;
	case 5:
		$periodoActual = 4;
		$periodoActuales = "Final";
		break;
}

//CONSULTA ESTUDIANTES MATRICULADOS
$curso='';
if(isset($_POST["curso"])){
	$curso=$_POST["curso"];
}
if(isset($_GET["curso"])){
	$curso=base64_decode($_GET["curso"]);
}

$id='';
if(isset($_POST["id"])){
	$id=$_POST["id"];
}
if(isset($_GET["id"])){
	$id=base64_decode($_GET["id"]);
}

$filtro = '';
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".$curso."'";}
if(!empty($_REQUEST["id"])){$filtro .= " AND mat_id='".$id."'";}

$grupo="";
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".$_REQUEST["grupo"]."'"; $grupo=$_REQUEST["grupo"];}

// OPTIMIZACIÓN: Cachear tipos de notas una sola vez
$tiposNotasCache = [];
$consultaDesempeno = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
while($rDesempeno = mysqli_fetch_array($consultaDesempeno, MYSQLI_BOTH)){
	$tiposNotasCache[] = $rDesempeno;
}

// OPTIMIZACIÓN: Pre-cargar cache de desempeños para evitar búsquedas repetidas
$desempenosCache = [];
foreach($tiposNotasCache as $desempeno){
	// Crear cache para todos los valores posibles en el rango
	for ($i = $desempeno["notip_desde"]; $i <= $desempeno["notip_hasta"]; $i += 0.1) {
		$key = number_format((float)$i, 1, '.', '');
		if (!isset($desempenosCache[$key])) {
			$desempenosCache[$key] = $desempeno["notip_nombre"];
		}
	}
}

// Función auxiliar para obtener desempeño desde cache optimizado
function obtenerDesempeno($nota, $desempenosCache, $grado, $config){
	$notaRedondeada = number_format((float)$nota, 1, '.', '');
	
	if($grado > 11 && $config['conf_id_institucion'] != EOA_CIRUELOS && false){
		$notaFD = ceil($nota);
		switch($notaFD){
			case 1: return "BAJO";
			case 2: return "BAJO";
			case 3: return "BÁSICO";
			case 4: return "ALTO";
			case 5: return "SUPERIOR";
		}
	} else {
		return isset($desempenosCache[$notaRedondeada]) ? $desempenosCache[$notaRedondeada] : "";
	}
}

// Función auxiliar para formatear nota según configuración
function formatearNota($nota, $grado, $config){
	$nota = round($nota, 1);
	if($nota == 1) return "1.0";
	if($nota == 2) return "2.0";
	if($nota == 3) return "3.0";
	if($nota == 4) return "4.0";
	if($nota == 5) return "5.0";
	
	if($grado > 11 && $config['conf_id_institucion'] != EOA_CIRUELOS && false){
		$notaRedondeada = ceil($nota);
		switch($notaRedondeada){
			case 1: return "D";
			case 2: return "I";
			case 3: return "A";
			case 4: return "S";
			case 5: return "E";
		}
	}
	return (string)$nota;
}

// Función auxiliar para procesar materias de un área (OPTIMIZADA)
function procesarMateriasArea($consultaAMat, $consultaAMatPer, $matriculadosDatos, $datosUsr, $config, $year, $conexion, $desempenosCache, &$materiasPerdidas, $periodoActual, $ausenciasMapa = [], $nivelacionesMapa = []){
	$materias = [];
	if(!empty($consultaAMat)){
		// OPTIMIZACIÓN: Pre-cargar todas las notas por período en un mapa
		$notasPeriodosMapa = [];
		mysqli_data_seek($consultaAMatPer, 0);
		while($fila3 = mysqli_fetch_array($consultaAMatPer, MYSQLI_BOTH)){
			$keyNota = $fila3["mat_id"] . '_' . $fila3["bol_periodo"];
			if (!isset($notasPeriodosMapa[$keyNota])) {
				$notasPeriodosMapa[$keyNota] = [];
			}
			$notaBoletin = !empty($fila3["bol_nota"]) ? $fila3["bol_nota"] : 0;
			$notaPeriodo = round($notaBoletin, 1);
			$notasPeriodosMapa[$keyNota][] = formatearNota($notaPeriodo, $datosUsr["mat_grado"], $config);
		}
		
		while($fila2 = mysqli_fetch_array($consultaAMat, MYSQLI_BOTH)){
			$notasPeriodos = [];
			// OPTIMIZACIÓN: Obtener notas del mapa pre-cargado
			for($j = 1; $j <= $periodoActual; $j++){
				$keyNota = $fila2["mat_id"] . '_' . $j;
				if(isset($notasPeriodosMapa[$keyNota])){
					$notasPeriodos = array_merge($notasPeriodos, $notasPeriodosMapa[$keyNota]);
				}
			}
			
			$totalPromedio2 = round($fila2["suma"], 1);
			$totalPromedio2Formatted = formatearNota($totalPromedio2, $datosUsr["mat_grado"], $config);
			
			// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
			// Sumar ausencias de todos los períodos hasta el actual (igual que el código original)
			$sumAusencias = 0;
			for($j = 1; $j <= $periodoActual; $j++){
				// Clave: estudiante_materia_periodo (igual que en el mapa)
				$keyAusencias = $datosUsr['mat_id'] . '_' . $fila2['mat_id'] . '_' . $j;
				if(isset($ausenciasMapa[$keyAusencias])){
					$sumAusencias += $ausenciasMapa[$keyAusencias];
				}
			}
			
			// OPTIMIZACIÓN: Obtener nivelaciones del mapa pre-cargado
			$msj = '';
			$notaOriginal = round($fila2["suma"], 1);
			if($notaOriginal < $config[5]){
				$keyNivelacion = $matriculadosDatos['mat_id'] . '_' . $fila2['car_id'];
				if(isset($nivelacionesMapa[$keyNivelacion])){
					$nivelacion = $nivelacionesMapa[$keyNivelacion];
					if($nivelacion['niv_definitiva'] < $config[5]){
						$materiasPerdidas++;
					} else {
						$totalPromedio2Formatted = formatearNota($nivelacion['niv_definitiva'], $datosUsr["mat_grado"], $config);
						$msj = 'Niv';
					}
				}
			}
			
			$materias[] = [
				'mat_nombre' => $fila2["mat_nombre"],
				'car_ih' => $fila2["car_ih"],
				'definitiva' => $totalPromedio2Formatted,
				'desempeno' => obtenerDesempeno($notaOriginal, $desempenosCache, $datosUsr["mat_grado"], $config),
				'ausencias' => !empty($fila2["matmaxaus"]) ? $fila2["matmaxaus"] : 0,
				'rAusencias' => $sumAusencias,
				'msj' => $msj
			];
		}
	}
	return $materias;
}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
$contadorEstudiantes = 0;

// OPTIMIZACIÓN: Pre-cargar todos los estudiantes en un array
$listaEstudiantes = [];
$idsEstudiantes = [];
while($est = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
	$listaEstudiantes[] = $est;
	$idsEstudiantes[] = $est['mat_id'];
}

// OPTIMIZACIÓN: Pre-cargar todas las ausencias de todos los estudiantes en una sola consulta
require_once(ROOT_PATH."/main-app/class/Ausencias.php");
$ausenciasMapa = [];
if(!empty($idsEstudiantes) && $periodoActual > 0){
	$idsEstudiantesEsc = array_map(function($id) use ($conexion) {
		return "'" . mysqli_real_escape_string($conexion, $id) . "'";
	}, $idsEstudiantes);
	$inEstudiantes = implode(',', $idsEstudiantesEsc);
	$yearEsc = mysqli_real_escape_string($conexion, $year);
	$institucion = (int)$config['conf_id_institucion'];
	
	// CORRECCIÓN: La consulta debe agrupar por estudiante, materia (mat_id) y período
	// para coincidir con cómo se llama el método original (que también filtra por grado)
	// Incluimos el grado del estudiante desde la tabla de matrículas
	$sqlAusencias = "SELECT 
						aus.aus_id_estudiante,
						car.car_materia as mat_id,
						cls.cls_periodo,
						mat.mat_grado as gra_id,
						SUM(aus.aus_ausencias) as sumAus
					FROM " . BD_ACADEMICA . ".academico_ausencias aus
					INNER JOIN " . BD_ACADEMICA . ".academico_clases cls 
						ON cls.cls_id = aus.aus_id_clase 
						AND cls.cls_periodo <= " . (int)$periodoActual . "
						AND cls.institucion = aus.institucion 
						AND cls.year = aus.year
					INNER JOIN " . BD_ACADEMICA . ".academico_cargas car 
						ON car.car_id = cls.cls_id_carga 
						AND car.institucion = aus.institucion 
						AND car.year = aus.year
					INNER JOIN " . BD_ACADEMICA . ".academico_matriculas mat
						ON mat.mat_id = aus.aus_id_estudiante
						AND mat.institucion = aus.institucion
						AND mat.year = aus.year
					WHERE aus.aus_id_estudiante IN ({$inEstudiantes})
					AND aus.institucion = {$institucion}
					AND aus.year = '{$yearEsc}'
					AND car.car_curso = mat.mat_grado
					GROUP BY aus.aus_id_estudiante, car.car_materia, cls.cls_periodo, mat.mat_grado";
	
	$consultaAusencias = mysqli_query($conexion, $sqlAusencias);
	if($consultaAusencias){
		while($aus = mysqli_fetch_array($consultaAusencias, MYSQLI_BOTH)){
			// Clave: estudiante_materia_periodo (igual que el método original)
			$key = $aus['aus_id_estudiante'] . '_' . $aus['mat_id'] . '_' . $aus['cls_periodo'];
			$ausenciasMapa[$key] = (float)($aus['sumAus'] ?? 0);
		}
	}
}

// OPTIMIZACIÓN: Pre-cargar todas las nivelaciones de todos los estudiantes en una sola consulta
$nivelacionesMapa = [];
if(!empty($idsEstudiantes)){
	$idsEstudiantesEsc = array_map(function($id) use ($conexion) {
		return "'" . mysqli_real_escape_string($conexion, $id) . "'";
	}, $idsEstudiantes);
	$inEstudiantes = implode(',', $idsEstudiantesEsc);
	$yearEsc = mysqli_real_escape_string($conexion, $year);
	$institucion = (int)$config['conf_id_institucion'];
	
	$sqlNivelaciones = "SELECT 
							niv_cod_estudiante,
							niv_id_asg,
							niv_definitiva
						FROM " . BD_ACADEMICA . ".academico_nivelaciones 
						WHERE niv_cod_estudiante IN ({$inEstudiantes})
						AND institucion = {$institucion}
						AND year = '{$yearEsc}'";
	
	$consultaNivelaciones = mysqli_query($conexion, $sqlNivelaciones);
	if($consultaNivelaciones){
		while($niv = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH)){
			$key = $niv['niv_cod_estudiante'] . '_' . $niv['niv_id_asg'];
			$nivelacionesMapa[$key] = [
				'niv_definitiva' => (float)($niv['niv_definitiva'] ?? 0)
			];
		}
	}
}

foreach($listaEstudiantes as $matriculadosDatos){
	$contadorEstudiantes++;
	$materiasPerdidas = 0;
	
	//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
	$usr = Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'], $year);
	$numUsr = mysqli_num_rows($usr);

	if ($numUsr == 0) {
		$url = UsuariosPadre::verificarTipoUsuario($datosUsuarioActual['uss_tipo'], 'page-info.php?idmsg=306');
		echo '<script type="text/javascript">window.location.href="' . $url . '";</script>';
		exit();
	}
	
	$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
	$idGrado = $datosUsr["mat_grado"];
	$idGrupo = $datosUsr["mat_grupo"];
	$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);

	// OPTIMIZACIÓN: Cargar áreas una sola vez
	$consultaMatAreaEst = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $idGrado, $idGrupo, $year);
	$numeroPeriodos = $config["conf_periodo"];
	$ultimoPeriodoAreas = $config['conf_periodos_maximos'];
	$numfilasNotArea = 0;
	
	$nombreInforme = "REGISTRO DE VALORACIÓN";
	
	// Preparar datos de áreas y materias
	$areasData = [];
	if(!empty($consultaMatAreaEst)){
		while($fila = mysqli_fetch_array($consultaMatAreaEst, MYSQLI_BOTH)){
			$condicion = "1,2,3,4";
			
			$consultaNotdefArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
			$consultaAMat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
			$consultaAMatPer = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
			
			$resultadoNotArea = mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
			$numfilasNotArea = mysqli_num_rows($consultaNotdefArea);
			
			if($numfilasNotArea > 0){
				$totalPromedio = 0;
				if(!empty($resultadoNotArea["suma"])){
					$totalPromedio = round($resultadoNotArea["suma"], 1);
				}
				
				if (!empty($resultadoNotArea['periodo']) && $resultadoNotArea['periodo'] < $config['conf_periodos_maximos']){
					$ultimoPeriodoAreas = $resultadoNotArea['periodo'];
				}
				
				$materias = procesarMateriasArea($consultaAMat, $consultaAMatPer, $matriculadosDatos, $datosUsr, $config, $year, $conexion, $desempenosCache, $materiasPerdidas, $periodoActual, $ausenciasMapa, $nivelacionesMapa);
				
				$areasData[] = [
					'ar_nombre' => $resultadoNotArea["ar_nombre"],
					'total_promedio' => formatearNota($totalPromedio, $datosUsr["mat_grado"], $config),
					'materias' => $materias
				];
			}
		}
	}
	
	// MEDIA TECNICA
	if (array_key_exists(10, $_SESSION["modulos"]) && $matriculadosDatos["mat_tipo_matricula"] == GRADO_INDIVIDUAL){
		require_once(ROOT_PATH . "/main-app/class/servicios/MediaTecnicaServicios.php");
		$consultaEstudianteActualMT = MediaTecnicaServicios::existeEstudianteMT($config, $year, $matriculadosDatos['mat_id']);
		while($datosEstudianteActualMT = mysqli_fetch_array($consultaEstudianteActualMT, MYSQLI_BOTH)){
			if(!empty($datosEstudianteActualMT)){
				$consultaMatAreaEstMT = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosEstudianteActualMT["matcur_id_curso"], $datosEstudianteActualMT["matcur_id_grupo"], $year);
				
				while($fila = mysqli_fetch_array($consultaMatAreaEstMT, MYSQLI_BOTH)){
					$condicion = "1,2,3,4";
					
					$consultaNotdefArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
					$consultaAMat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
					$consultaAMatPer = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);
					
					$resultadoNotArea = mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
					$numfilasNotAreaMT = mysqli_num_rows($consultaNotdefArea);
					
					if($numfilasNotAreaMT > 0){
						$totalPromedio = 0;
						if(!empty($resultadoNotArea["suma"])){
							$totalPromedio = round($resultadoNotArea["suma"], 1);
						}
						
						$materias = procesarMateriasArea($consultaAMat, $consultaAMatPer, $matriculadosDatos, $datosUsr, $config, $year, $conexion, $desempenosCache, $materiasPerdidas, $periodoActual, $ausenciasMapa, $nivelacionesMapa);
						
						$areasData[] = [
							'ar_nombre' => $resultadoNotArea["ar_nombre"],
							'total_promedio' => formatearNota($totalPromedio, $datosUsr["mat_grado"], $config),
							'materias' => $materias
						];
					}
				}
			}
		}
		if(!empty($consultaEstudianteActualMT)){
			$consultaEstudianteActualMT->free();
		}
	}
	
	// Generar mensaje de promoción
	$msj = "";
	if($periodoActual == 4 && $numfilasNotArea > 0){
		if($materiasPerdidas >= $config["conf_num_materias_perder_agno"]){
			$msj = "EL (LA) ESTUDIANTE ".$nombre." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
		} elseif($materiasPerdidas < $config["conf_num_materias_perder_agno"] && $materiasPerdidas > 0){
			$msj = "EL (LA) ESTUDIANTE ".$nombre." DEBE NIVELAR LAS MATERIAS PERDIDAS";
		} else {
			$msj = "EL (LA) ESTUDIANTE ".$nombre." FUE PROMOVIDO(A) AL GRADO SIGUIENTE";
		}

		if ($matriculadosDatos['mat_estado_matricula'] == CANCELADO && $ultimoPeriodoAreas < $config["conf_periodos_maximos"]) {
			$msj = "EL(LA) ESTUDIANTE FUE RETIRADO SIN FINALIZAR AÑO LECTIVO.";
		}
	}
	
	// Preparar datos para firma
	$rector = UsuariosPadre::sesionUsuario($informacion_inst["info_rector"], "", $config['conf_id_institucion'], $year);
	$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
	$secretario = UsuariosPadre::sesionUsuario($informacion_inst["info_secretaria_academica"], "", $config['conf_id_institucion'], $year);
	$nombreSecretario = UsuariosPadre::nombreCompletoDelUsuario($secretario);
	
	if($contadorEstudiantes > 1){
		echo '<div id="saltoPagina"></div>';
	}
	?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>SINTIA - <?=$nombreInforme?></title>
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			font-size: 11px;
			line-height: 1.5;
			color: #333;
			background: #f5f5f5;
			padding: 20px;
		}
		
		.report-container {
			max-width: 100%;
			margin: 0 auto;
			background: #fff;
			padding: 25px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			border-radius: 8px;
		}
		
		.header-report {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 25px;
			padding: 20px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border-radius: 8px;
			color: #fff;
		}
		
		.logo-container {
			flex: 0 0 auto;
		}
		
		.logo-container img {
			max-height: 100px;
			max-width: 200px;
			object-fit: contain;
		}
		
		.report-title-section {
			flex: 1;
			text-align: center;
			padding: 0 20px;
		}
		
		.report-title-section h1 {
			font-size: 20px;
			font-weight: 700;
			margin-bottom: 5px;
			color: #fff;
		}
		
		.report-title-section .periodo {
			font-size: 14px;
			opacity: 0.95;
		}
		
		.report-title-section .fecha {
			font-size: 11px;
			opacity: 0.85;
			margin-top: 5px;
		}
		
		.student-info-section {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			padding: 15px 20px;
			border-radius: 6px;
			margin-bottom: 20px;
			color: #fff;
		}
		
		.student-info-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 10px;
			font-size: 11px;
		}
		
		.student-info-item {
			display: flex;
			align-items: center;
		}
		
		.student-info-label {
			font-weight: 600;
			margin-right: 8px;
			opacity: 0.9;
		}
		
		.student-info-value {
			font-weight: 700;
		}
		
		.table-container {
			width: 100%;
			overflow-x: auto;
			margin-bottom: 20px;
		}
		
		table.informe-table {
			width: 100%;
			border-collapse: collapse;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
			border-radius: 6px;
			overflow: hidden;
		}
		
		table.informe-table thead {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		}
		
		table.informe-table thead th {
			padding: 12px 8px;
			text-align: center;
			font-weight: 700;
			color: #fff;
			font-size: 11px;
			border: 1px solid rgba(255,255,255,0.2);
		}
		
		table.informe-table tbody tr {
			border-bottom: 1px solid #e0e0e0;
			transition: background-color 0.2s;
		}
		
		table.informe-table tbody tr:nth-child(even) {
			background-color: #f8f9fa;
		}
		
		table.informe-table tbody tr:hover {
			background-color: #e8f4f8;
		}
		
		table.informe-table tbody tr.area-row {
			background-color: #e9ecef;
			font-weight: 700;
		}
		
		table.informe-table tbody td {
			padding: 10px 8px;
			border: 1px solid #e0e0e0;
			font-size: 11px;
		}
		
		table.informe-table tbody td.area-name {
			font-weight: 700;
			background-color: #e9ecef;
		}
		
		.promocion-mensaje {
			padding: 15px 20px;
			margin: 20px 0;
			background: #fff3cd;
			border-left: 4px solid #ffc107;
			border-radius: 4px;
			font-weight: 700;
			font-style: italic;
			font-size: 11px;
		}
		
		.signatures-section {
			display: flex;
			justify-content: space-around;
			align-items: flex-start;
			margin-top: 40px;
			padding-top: 20px;
			border-top: 2px solid #ddd;
		}
		
		.signature-block {
			text-align: center;
			flex: 1;
			max-width: 300px;
		}
		
		.signature-block img {
			max-width: 200px;
			max-height: 100px;
			margin-bottom: 10px;
		}
		
		.signature-line {
			border-top: 1px solid #000;
			margin: 20px auto;
			width: 200px;
		}
		
		.signature-name {
			font-weight: 600;
			margin-top: 10px;
			font-size: 11px;
		}
		
		.signature-role {
			font-size: 10px;
			color: #666;
			margin-top: 5px;
		}
		
		.botones-accion {
			text-align: center;
			margin-bottom: 20px;
			padding: 15px;
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		}
		
		.btn-accion {
			display: inline-block;
			padding: 10px 20px;
			margin: 0 5px;
			background: #6017dc;
			color: #fff;
			text-decoration: none;
			border-radius: 5px;
			font-weight: 600;
			transition: all 0.3s;
			border: none;
			cursor: pointer;
			font-size: 13px;
		}
		
		.btn-accion:hover {
			background: #4a12b3;
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.2);
		}
		
		.btn-accion.secondary {
			background: #6c757d;
		}
		
		.btn-accion.secondary:hover {
			background: #545b62;
		}
		
		#saltoPagina {
			page-break-after: always;
		}
		
		@media print {
			body {
				background: #fff;
				padding: 0;
			}
			
			.report-container {
				box-shadow: none;
				padding: 15px;
			}
			
			.botones-accion {
				display: none;
			}
			
			.header-report {
				background: #667eea !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			
			table.informe-table thead {
				background: #667eea !important;
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
			
			@page {
				size: landscape;
				margin: 1cm;
			}
			
			#saltoPagina {
				page-break-after: always;
			}
		}
	</style>
</head>

<body>
	<div class="report-container">
		<?php if($config['conf_mostrar_encabezado_informes'] == 1): ?>
			<?php include("../compartido/head-informes.php"); ?>
		<?php else: ?>
			<?php 
			// Determinar logo a mostrar
			$logoHTML = '';
			$logoPath = "../files/images/logo/" . $informacion_inst["info_logo"];
			
			// Verificar si el logo de la institución existe
			if(!empty($informacion_inst["info_logo"]) && file_exists(ROOT_PATH . "/main-app/files/images/logo/" . $informacion_inst["info_logo"])){
				// Usar el logo de la institución
				$logoHTML = '<img src="' . htmlspecialchars($logoPath ?? '', ENT_QUOTES, 'UTF-8') . '" alt="Logo" style="max-height: 100px; max-width: 200px; object-fit: contain;">';
			} else {
				// Logo SVG por defecto de SINTIA
				$logoHTML = '<svg width="180" height="60" viewBox="0 0 180 60" xmlns="http://www.w3.org/2000/svg" style="max-height: 100px; max-width: 200px;">
					<defs>
						<linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
							<stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
							<stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
						</linearGradient>
					</defs>
					<rect width="180" height="60" rx="8" fill="url(#grad1)"/>
					<text x="90" y="38" font-family="Arial, sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">SINTIA</text>
				</svg>';
			}
			?>
			<div class="header-report">
				<div class="logo-container">
					<?=$logoHTML?>
				</div>
				<div class="report-title-section">
					<h1><?=$nombreInforme?></h1>
					<div class="periodo">PERÍODO: <?=strtoupper($periodoActuales)?></div>
					<div class="fecha">Fecha: <?=date("d/m/Y")?></div>
				</div>
				<div class="logo-container"></div>
			</div>
		<?php endif; ?>
		
		<div class="student-info-section">
			<div class="student-info-grid">
				<div class="student-info-item">
					<span class="student-info-label">Código:</span>
					<span class="student-info-value"><?=htmlspecialchars($datosUsr["mat_matricula"] ?? '', ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="student-info-item">
					<span class="student-info-label">Nombre:</span>
					<span class="student-info-value"><?=htmlspecialchars($nombre ?? '', ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="student-info-item">
					<span class="student-info-label">Matrícula:</span>
					<span class="student-info-value"><?=htmlspecialchars($datosUsr["mat_numero_matricula"] ?? '', ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="student-info-item">
					<span class="student-info-label">Grado:</span>
					<span class="student-info-value"><?=htmlspecialchars(($matriculadosDatos["gra_nombre"] ?? '') . " " . ($matriculadosDatos["gru_nombre"] ?? ''), ENT_QUOTES, 'UTF-8');?></span>
				</div>
				<div class="student-info-item">
					<span class="student-info-label">Período:</span>
					<span class="student-info-value"><?=strtoupper($periodoActuales)?></span>
				</div>
				<div class="student-info-item">
					<span class="student-info-label">Folio:</span>
					<span class="student-info-value"><?=htmlspecialchars(isset($datosUsr["mat_folio"]) ? $datosUsr["mat_folio"] : '', ENT_QUOTES, 'UTF-8');?></span>
				</div>
			</div>
		</div>
		
		<div class="botones-accion no-print">
			<button class="btn-accion" onclick="window.print()">
				<i class="fa fa-print"></i> Imprimir
			</button>
			<button class="btn-accion secondary" onclick="window.close()">
				<i class="fa fa-times"></i> Cerrar
			</button>
		</div>
		
		<div class="table-container">
			<table class="informe-table">
				<thead>
					<tr>
						<th width="30%">ÁREAS/ASIGNATURAS</th>
						<th width="5%">I.H</th>
						<th width="8%">DEF</th>
						<th width="15%">DESEMPEÑO</th>
						<th width="8%">AUS</th>
						<th width="34%">OBSERVACIONES</th>
					</tr>
				</thead>
				<tbody>
					<?php if(empty($areasData)): ?>
						<tr>
							<td colspan="6" style="text-align: center; padding: 20px; color: #888;">
								No hay calificaciones registradas para este período
							</td>
						</tr>
					<?php else: ?>
						<?php foreach($areasData as $area): ?>
							<tr class="area-row">
								<td class="area-name"><?=htmlspecialchars($area['ar_nombre'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
								<td align="center"></td>
								<td align="center" style="font-weight: bold;"><?=htmlspecialchars($area['total_promedio'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
								<td align="center"></td>
								<td align="center"></td>
								<td></td>
							</tr>
							<?php foreach($area['materias'] as $materia): ?>
								<tr>
									<td style="padding-left: 20px;"><?=htmlspecialchars($materia['mat_nombre'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
									<td align="center" style="font-weight: bold;"><?=htmlspecialchars($materia['car_ih'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
									<td align="center" style="font-weight: bold;"><?=htmlspecialchars($materia['definitiva'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
									<td align="center" style="font-weight: bold;"><?=htmlspecialchars($materia['desempeno'] ?? '', ENT_QUOTES, 'UTF-8');?></td>
									<td align="center" style="font-weight: bold;">
										<?php
										$ausDisplay = ($materia['rAusencias'] > 0) ? $materia['rAusencias'] : "0.0";
										$ausencias = $materia['ausencias'] ?? '';
										echo htmlspecialchars($ausDisplay."/".$ausencias, ENT_QUOTES, 'UTF-8');
										?>
									</td>
									<td align="center">_______________________________________</td>
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<?php if(!empty($msj)): ?>
			<div class="promocion-mensaje">
				<?=htmlspecialchars($msj ?? '', ENT_QUOTES, 'UTF-8');?>
			</div>
		<?php endif; ?>
		
		<div class="signatures-section">
			<div class="signature-block">
				<?php if(!empty($rector["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $rector['uss_firma'])): ?>
					<img src="../files/fotos/<?=$rector["uss_firma"]?>" alt="Firma Rector" onerror="this.style.display='none'">
				<?php else: ?>
					<div style="height: 80px;"></div>
				<?php endif; ?>
				<div class="signature-line"></div>
				<div class="signature-name"><?=htmlspecialchars($nombreRector ?? '', ENT_QUOTES, 'UTF-8');?></div>
				<div class="signature-role">Rector(a)</div>
			</div>
			<div class="signature-block">
				<?php if(!empty($secretario["uss_firma"]) && file_exists(ROOT_PATH.'/main-app/files/fotos/' . $secretario['uss_firma'])): ?>
					<img src="../files/fotos/<?=$secretario["uss_firma"]?>" alt="Firma Secretario" onerror="this.style.display='none'">
				<?php else: ?>
					<div style="height: 80px;"></div>
				<?php endif; ?>
				<div class="signature-line"></div>
				<div class="signature-name"><?=htmlspecialchars($nombreSecretario ?? '', ENT_QUOTES, 'UTF-8');?></div>
				<div class="signature-role">Secretario(a) Académico</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		// Atajo de teclado para imprimir
		document.addEventListener('keydown', function(e) {
			if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
				e.preventDefault();
				window.print();
			}
		});
	</script>
</body>
</html>
<?php
	} // FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>