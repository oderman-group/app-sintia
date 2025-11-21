<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0230';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/Grupos.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/servicios/GradoServicios.php");
$year = $_SESSION["bd"];
if(!empty($_REQUEST["agno"])){
	$year = $_REQUEST["agno"];
}

// Obtener curso, grupo y estudiante (desde POST o GET)
$cursoV = '';
$grupoV = '';
$estudianteV = '';
if (!empty($_GET["curso"])) {
	$cursoV = base64_decode($_GET['curso']);
	$grupoV = base64_decode($_GET['grupo']);
	$estudianteV = !empty($_GET["estudiante"]) ? base64_decode($_GET['estudiante']) : '';
}elseif(!empty($_POST["curso"])) {
	$cursoV = $_POST['curso'];
	$grupoV = $_POST['grupo'];
	$estudianteV = !empty($_POST["estudiante"]) ? $_POST['estudiante'] : '';
}

$consultaCurso = null;
$curso = null;
$consultaGrupo = null;
$grupo = null;

if (!empty($cursoV)) {
	$consultaCurso = Grados::obtenerDatosGrados($cursoV);
	$curso = mysqli_fetch_array($consultaCurso, MYSQLI_BOTH);
}

if (!empty($grupoV)) {
	$consultaGrupo = Grupos::obtenerDatosGrupos($grupoV);
	$grupo = mysqli_fetch_array($consultaGrupo, MYSQLI_BOTH);
}

// Obtener porcentajes de periodos para el curso
$porcentajesPeriodos = [];
if (!empty($cursoV)) {
	$sqlPorcentajes = "SELECT gvp_periodo, gvp_valor FROM " . BD_ACADEMICA . ".academico_grados_periodos 
	                   WHERE gvp_grado = ? AND institucion = ? AND year = ?";
	$parametrosPorcentajes = [$cursoV, $config['conf_id_institucion'], $year];
	$resultadoPorcentajes = BindSQL::prepararSQL($sqlPorcentajes, $parametrosPorcentajes);
	if ($resultadoPorcentajes) {
		while ($porc = mysqli_fetch_array($resultadoPorcentajes, MYSQLI_BOTH)) {
			$porcentajesPeriodos[$porc['gvp_periodo']] = (float)$porc['gvp_valor'] / 100; // Convertir a decimal
		}
	}
}

// Si no hay porcentajes configurados, usar valores por defecto
if (empty($porcentajesPeriodos)) {
	for ($p = 1; $p <= $config[19]; $p++) {
		$porcentajesPeriodos[$p] = 1 / $config[19]; // Distribución equitativa
	}
}
?>
<head>
	<title>SINTIA | Consolidado Final</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
	<link href="../../config-general/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="../../config-general/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	
	<style type="text/css">
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			background: #f5f7fa;
			margin: 0;
			padding: 20px;
		}
		
		.explicacion-columnas {
			background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
			padding: 20px;
			border-radius: 12px;
			border: 2px solid #667eea;
			margin-bottom: 20px;
			font-size: 12px;
		}
		
		.explicacion-columnas h4 {
			color: #92400e;
			font-weight: 700;
			margin-top: 0;
			margin-bottom: 15px;
		}
		
		.explicacion-columnas ul {
			margin-left: 20px;
			line-height: 1.8;
		}
		
		.explicacion-columnas li {
			margin-bottom: 10px;
		}
		
		/* ==================== ESTILOS PARA IMPRESIÓN ==================== */
		@media print {
			.no-print {
				display: none !important;
			}
			
			body {
				background: white;
				padding: 0;
			}
		}
	</style>
</head>
<body style="font-family:Arial;">

<?php if (empty($cursoV) || empty($grupoV)) { ?>
	<div style="text-align: center; padding: 50px; font-family: Arial;">
		<h2>Error: Parámetros Incompletos</h2>
		<p>No se especificó el curso y grupo para generar el informe.</p>
		<button onclick="window.close()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Cerrar</button>
	</div>
<?php } else { ?>
	<!-- Botón de impresión -->
	<div class="no-print" style="text-align: center; padding: 20px;">
		<button onclick="window.print();" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; padding: 12px 25px; font-weight: 600; font-size: 14px; cursor: pointer;">
			<i class="fa fa-print"></i> Imprimir Consolidado
		</button>
	</div>
	
	<!-- Explicación de columnas -->
	<div class="explicacion-columnas no-print">
		<h4><i class="fa fa-calculator"></i> Explicación de las Columnas DEF y PROM</h4>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Columnas DEF (Definitivas por Materia):</strong></p>
		<ul>
			<li><strong style="color: #92400e;">Primera DEF</strong> (fondo amarillo): Calcula la definitiva usando los porcentajes configurados para cada período. Si un estudiante no tiene nota en algún período, ese período cuenta como cero en el cálculo.</li>
			<li><strong style="color: #0369a1;">Segunda DEF</strong> (fondo azul claro): Calcula la definitiva usando los porcentajes configurados solo para los períodos que tienen nota registrada. Esta columna solo muestra valores cuando hay al menos un período con nota mayor a cero.</li>
		</ul>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Columnas PROM (Promedio General del Estudiante):</strong></p>
		<ul>
			<li><strong style="color: #d97706;">Primera PROM</strong>: Promedio general del estudiante calculado con base en la <strong>primera DEF</strong> de todas sus materias. Divide la suma de todas las primeras definitivas entre el número total de materias.</li>
			<li><strong style="color: #0369a1;">Segunda PROM</strong> (fondo azul claro): Promedio general del estudiante calculado con base en la <strong>segunda DEF</strong> de todas sus materias. Solo considera las materias que tienen definitiva mayor a cero. Divide la suma de las segundas definitivas entre el número de materias con definitiva mayor a cero.</li>
		</ul>
		<p style="margin-bottom: 15px;"><strong style="color: #92400e;">Fila de Promedios (Footer):</strong></p>
		<ul>
			<li><strong>Promedio por Período:</strong> Muestra el promedio de todos los estudiantes en cada período de cada materia. Se calcula sumando las notas de todos los estudiantes en ese período y dividiendo entre el número de estudiantes que tienen nota.</li>
			<li><strong>Promedio de Primera DEF:</strong> Muestra el promedio de todas las primeras definitivas de todos los estudiantes en cada materia.</li>
			<li><strong>Promedio de Segunda DEF:</strong> Muestra el promedio de todas las segundas definitivas de todos los estudiantes en cada materia. Solo considera estudiantes con definitiva mayor a cero.</li>
			<li><strong>Promedio de Primera PROM:</strong> Muestra el promedio de todas las primeras PROM de todos los estudiantes.</li>
			<li><strong>Promedio de Segunda PROM:</strong> Muestra el promedio de todas las segundas PROM de todos los estudiantes. Solo considera estudiantes con segunda PROM mayor a cero.</li>
		</ul>
	</div>
	
	<?php
	$nombreInforme = "CONSOLIDADO FINAL " .$year."<br>" . "CURSO: " .Utilidades::getToString($curso['gra_nombre']). "<br>" . "GRUPO: ".Utilidades::getToString($grupo['gru_nombre']);
	if (!empty($estudianteV)) {
		$filtroTemp = " AND mat_grado='" . $cursoV . "' AND mat_grupo='" . $grupoV . "' AND mat_id='" . $estudianteV . "'";
		$cursoActualTemp = GradoServicios::consultarCurso($cursoV);
		$consultaTemp = Estudiantes::listarEstudiantesEnGrados($filtroTemp, "", $cursoActualTemp, "", $year);
		if ($consultaTemp !== null && $consultaTemp !== false) {
			$estudianteSeleccionado = mysqli_fetch_array($consultaTemp, MYSQLI_BOTH);
			if ($estudianteSeleccionado) {
				$nombreInforme .= "<br>ESTUDIANTE: " . Estudiantes::NombreCompletoDelEstudiante($estudianteSeleccionado);
			}
		}
	}
	include("../compartido/head-informes.php");
	
	// ============================================
	// OPTIMIZACIONES: PRE-CARGAR DATOS
	// ============================================
	
	// OPTIMIZACIÓN 1: Cachear valores de configuración
	$notaMinimaAprobar = $config[5] ?? 3.0;
	$colorPerdida = $config[6] ?? '#dc3545';
	$colorGanada = $config[7] ?? '#28a745';
	$numeroPeriodos = $config[19] ?? 4;
	
	// OPTIMIZACIÓN 2: Cachear consulta de cargas (se usa 4 veces)
	$cargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $cursoV, $grupoV, $year);
	$numCargasPorCurso = mysqli_num_rows($cargas);
	$materias = [];
	$idsCargas = [];
	mysqli_data_seek($cargas, 0);
	while($carga = mysqli_fetch_array($cargas, MYSQLI_BOTH)){
		$materias[$carga['car_id']] = $carga;
		$idsCargas[] = $carga['car_id'];
	}
	
	// OPTIMIZACIÓN 3: Pre-cargar todas las notas del boletín en un mapa
	// [estudiante][carga][periodo] => datos_nota
	$notasBoletinMapa = [];
	if(!empty($idsCargas)){
		$idsCargasEsc = array_map(function($id) use ($conexion) {
			return "'" . mysqli_real_escape_string($conexion, $id) . "'";
		}, $idsCargas);
		$inCargas = implode(',', $idsCargasEsc);
		$institucion = (int)$config['conf_id_institucion'];
		$yearEsc = mysqli_real_escape_string($conexion, $year);
		
		$sqlNotas = "SELECT bol_estudiante, bol_carga, bol_periodo, bol_nota
					 FROM " . BD_ACADEMICA . ".academico_boletin
					 WHERE bol_carga IN ({$inCargas})
					 AND institucion = {$institucion}
					 AND year = '{$yearEsc}'";
		
		$consultaNotas = mysqli_query($conexion, $sqlNotas);
		if($consultaNotas){
			while($nota = mysqli_fetch_array($consultaNotas, MYSQLI_BOTH)){
				$idEst = $nota['bol_estudiante'];
				$idCarga = $nota['bol_carga'];
				$periodo = (int)$nota['bol_periodo'];
				if(!isset($notasBoletinMapa[$idEst])){
					$notasBoletinMapa[$idEst] = [];
				}
				if(!isset($notasBoletinMapa[$idEst][$idCarga])){
					$notasBoletinMapa[$idEst][$idCarga] = [];
				}
				$notasBoletinMapa[$idEst][$idCarga][$periodo] = $nota;
			}
		}
	}
	
	// OPTIMIZACIÓN 4: Pre-cargar cache de notas cualitativas
	$notasCualitativasCache = [];
	if ($config['conf_forma_mostrar_notas'] == CUALITATIVA) {
		$consultaNotasTipo = mysqli_query($conexion, 
			"SELECT notip_desde, notip_hasta, notip_nombre 
			 FROM ".BD_ACADEMICA.".academico_notas_tipos 
			 WHERE notip_categoria='".mysqli_real_escape_string($conexion, $config['conf_notas_categoria'])."' 
			 AND institucion=".(int)$config['conf_id_institucion']." 
			 AND year='".mysqli_real_escape_string($conexion, $year)."'
			 ORDER BY notip_desde ASC");
		
		if($consultaNotasTipo){
			while ($notaTipo = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)) {
				for ($i = $notaTipo['notip_desde']; $i <= $notaTipo['notip_hasta']; $i += 0.1) {
					$key = number_format((float)$i, 1, '.', '');
					if (!isset($notasCualitativasCache[$key])) {
						$notasCualitativasCache[$key] = $notaTipo['notip_nombre'];
					}
				}
			}
		}
	}
	?>

	<table width="100%" cellspacing="5" cellpadding="5" rules="all" 
	  style="
	  border:solid; 
	  border-color:<?=$Plataforma->colorUno;?>; 
	  font-size:11px;
	  ">

	<tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
		<th rowspan="2" style="font-size:9px;">Mat</th>
		<th rowspan="2" style="font-size:9px;">Estudiante</th>
		<?php
		// OPTIMIZACIÓN: Usar cargas cacheadas
		foreach($materias as $carga){
		?>
			<th style="font-size:9px; text-align:center; border:groove;" colspan="<?=$numeroPeriodos+2;?>" width="5%"><?php if(!empty($carga['mat_nombre'])){echo $carga['mat_nombre'];}?></th>
		<?php
		}
		?>
		<th rowspan="2" style="text-align:center;">PROM</th>
		<th rowspan="2" style="text-align:center; background:#e0f2fe;">PROM</th>
	</tr>
	
	<tr>
		<?php
		// OPTIMIZACIÓN: Usar cargas cacheadas
		foreach($materias as $carga){
			$p = 1;
			//PERIODOS DE CADA MATERIA
			while($p<=$numeroPeriodos){
				echo '<th style="text-align:center;">'.$p.'</th>';
				$p++;
			}
			//DEFINITIVA DE CADA MATERIA - DOS COLUMNAS
			echo '<th style="text-align:center; background:#fffbeb; color:#92400e; font-weight:700;">DEF</th>';
			echo '<th style="text-align:center; background:#e0f2fe; color:#0369a1; font-weight:700;">DEF</th>';
		}
		?>
	</tr>
	
	<?php
	$filtroAdicional = "";
	if(!empty($cursoV) and !empty($grupoV)){
		$filtroAdicional= "AND mat_grado='".$cursoV."' AND mat_grupo='".$grupoV."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2)";
	}
	
	// Si hay un estudiante seleccionado, agregar filtro
	if (!empty($estudianteV)) {
		$filtroAdicional .= " AND mat_id='" . $estudianteV . "'";
	}
	
	$cursoActual=GradoServicios::consultarCurso($cursoV);
	$consulta =Estudiantes::listarEstudiantesEnGrados($filtroAdicional,"",$cursoActual,"",$year);
	
	// Arrays para acumular promedios
	$promediosPorPeriodo = []; // [carga_id][periodo] => [suma, contador]
	$promediosDefinitivas = []; // [carga_id] => [suma_def1, suma_def2, contador_def1, contador_def2]
	$promediosGenerales = ['prom1' => 0, 'prom2' => 0, 'contador_prom1' => 0, 'contador_prom2' => 0];
	
	// Inicializar arrays
	foreach ($materias as $carga) {
		$promediosDefinitivas[$carga['car_id']] = ['def1' => 0, 'def2' => 0, 'contador_def1' => 0, 'contador_def2' => 0];
		for ($p = 1; $p <= $numeroPeriodos; $p++) {
			$promediosPorPeriodo[$carga['car_id']][$p] = ['suma' => 0, 'contador' => 0];
		}
	}
	
	while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
		$defPorEstudiante = 0;
		$defPorEstudianteConNotas = 0;
		$materiasConNota = 0;
	?>
		<tr style="border-color:<?=$Plataforma->colorDos;?>;">
			<td style="font-size:9px;"><?=$resultado['mat_matricula'];?></td>
			<td style="font-size:9px;"><?=Estudiantes::NombreCompletoDelEstudiante($resultado);?></td>
			<?php
			// OPTIMIZACIÓN: Usar cargas cacheadas
			foreach($materias as $carga){
				$p = 1;
				$defPorMateria = 0;
				$defPorMateriaConNotas = 0;
				$periodosConNota = 0;
				$sumaPorcentajesConNota = 0;
				
				//PERIODOS DE CADA MATERIA
				while($p<=$numeroPeriodos){
					// Inicializar color por defecto
					$color = '#000000';
					
					// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
					$boletin = $notasBoletinMapa[$resultado['mat_id']][$carga['car_id']][$p] ?? null;
					
					if(!empty($boletin['bol_nota']) and $boletin['bol_nota']<$notaMinimaAprobar and $boletin['bol_nota']!="")$color = $colorPerdida; 
					elseif(!empty($boletin['bol_nota']) and $boletin['bol_nota']>=$notaMinimaAprobar) $color = $colorGanada;
					
					$notaBoletinFinal="";
					$title='';
					if(!empty($boletin['bol_nota'])){
						$notaBoletinFinal=$boletin['bol_nota'];
						if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
							$title='title="Nota Cuantitativa: '.$boletin['bol_nota'].'"';
							// OPTIMIZACIÓN: Usar cache de notas cualitativas
							$notaRedondeada = number_format((float)$boletin['bol_nota'], 1, '.', '');
							$notaBoletinFinal = isset($notasCualitativasCache[$notaRedondeada]) 
								? $notasCualitativasCache[$notaRedondeada] 
								: "";
						}
						
						// Primera definitiva: suma ponderada usando porcentajes configurados
						$porcentaje = isset($porcentajesPeriodos[$p]) ? $porcentajesPeriodos[$p] : (1 / $numeroPeriodos);
						$defPorMateria += ($boletin['bol_nota'] * $porcentaje);
						
						// Segunda definitiva: solo periodos con nota
						$defPorMateriaConNotas += ($boletin['bol_nota'] * $porcentaje);
						$sumaPorcentajesConNota += $porcentaje;
						$periodosConNota++;
						
						// Acumular para promedio por período
						$promediosPorPeriodo[$carga['car_id']][$p]['suma'] += $boletin['bol_nota'];
						$promediosPorPeriodo[$carga['car_id']][$p]['contador']++;
					}
					//DEFINITIVA DE CADA PERIODO
				?>	
					<td style="text-align:center; color:<?=$color;?>" <?=$title;?>><?=$notaBoletinFinal?></td>
				<?php
					$p++;
				}
				
				// Primera definitiva (basada en total de periodos con porcentajes)
				$defPorMateria = round($defPorMateria, 2);
				
				// Segunda definitiva (basada solo en periodos con nota)
				$defPorMateriaConNotasCalculada = 0;
				if ($sumaPorcentajesConNota > 0) {
					$defPorMateriaConNotasCalculada = round($defPorMateriaConNotas / $sumaPorcentajesConNota, 2);
				}
				
				//DEFINITIVA DE CADA MATERIA
				// Inicializar color por defecto
				$color = '#000000';
				if($defPorMateria<$notaMinimaAprobar and $defPorMateria!="")$color = $colorPerdida; 
				elseif($defPorMateria>=$notaMinimaAprobar) $color = $colorGanada;
				
				$colorConNotas = '#000000';
				if($defPorMateriaConNotasCalculada<$notaMinimaAprobar and $defPorMateriaConNotasCalculada!="")$colorConNotas = $colorPerdida; 
				elseif($defPorMateriaConNotasCalculada>=$notaMinimaAprobar) $colorConNotas = $colorGanada;
				
				$defPorMateriaFinal=$defPorMateria;
				$defPorMateriaConNotasFinal=$defPorMateriaConNotasCalculada;
				$title='';
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					$title='title="Nota Cuantitativa: '.$defPorMateria.'"';
					// OPTIMIZACIÓN: Usar cache de notas cualitativas
					$notaRedondeada = number_format((float)$defPorMateria, 1, '.', '');
					$defPorMateriaFinal = isset($notasCualitativasCache[$notaRedondeada]) 
						? $notasCualitativasCache[$notaRedondeada] 
						: "";
					
					$titleConNotas='title="Nota Cuantitativa: '.$defPorMateriaConNotasCalculada.'"';
					$notaRedondeadaConNotas = number_format((float)$defPorMateriaConNotasCalculada, 1, '.', '');
					$defPorMateriaConNotasFinal = isset($notasCualitativasCache[$notaRedondeadaConNotas]) 
						? $notasCualitativasCache[$notaRedondeadaConNotas] 
						: "";
				}
			?>
				<!-- Primera DEF -->
				<td style="text-align:center; background:#fffbeb; color:<?=$color;?>; text-decoration:underline; font-weight:700;" <?=$title;?>><?=$defPorMateriaFinal;?></td>
				<!-- Segunda DEF -->
				<td style="text-align:center; background:#e0f2fe; color:<?=$colorConNotas;?>; text-decoration:underline; font-weight:700;" <?=isset($titleConNotas) ? $titleConNotas : '';?>><?=$defPorMateriaConNotasCalculada > 0 ? $defPorMateriaConNotasFinal : '-';?></td>
			<?php
				//DEFINITIVA POR CADA ESTUDIANTE (basada en primera DEF)
				$defPorEstudiante += $defPorMateria;
				
				//DEFINITIVA POR CADA ESTUDIANTE (basada en segunda DEF)
				if ($defPorMateriaConNotasCalculada > 0) {
					$defPorEstudianteConNotas += $defPorMateriaConNotasCalculada;
					$materiasConNota++;
				}
				
				// Acumular definitivas para promedio
				$promediosDefinitivas[$carga['car_id']]['def1'] += $defPorMateria;
				$promediosDefinitivas[$carga['car_id']]['contador_def1']++;
				
				if ($defPorMateriaConNotasCalculada > 0) {
					$promediosDefinitivas[$carga['car_id']]['def2'] += $defPorMateriaConNotasCalculada;
					$promediosDefinitivas[$carga['car_id']]['contador_def2']++;
				}
			}
			
			// Promedio basado en primera DEF
			if($numCargasPorCurso > 0){
				$defPorEstudiante = round($defPorEstudiante/$numCargasPorCurso,2);
			}
			
			// Promedio basado en segunda DEF
			$defPorEstudianteConNotasCalculada = 0;
			if ($materiasConNota > 0) {
				$defPorEstudianteConNotasCalculada = round($defPorEstudianteConNotas / $materiasConNota, 2);
			}
			
			// Inicializar color por defecto
			$color = '#000000';
			if($defPorEstudiante<$notaMinimaAprobar and $defPorEstudiante!="")$color = $colorPerdida; 
			elseif($defPorEstudiante>=$notaMinimaAprobar) $color = $colorGanada;
			
			$colorConNotas = '#000000';
			if($defPorEstudianteConNotasCalculada<$notaMinimaAprobar and $defPorEstudianteConNotasCalculada!="")$colorConNotas = $colorPerdida; 
			elseif($defPorEstudianteConNotasCalculada>=$notaMinimaAprobar) $colorConNotas = $colorGanada;
			
			// Formatear PROM según configuración de notas
			$defPorEstudianteFinal = $defPorEstudiante;
			$defPorEstudianteConNotasFinal = $defPorEstudianteConNotasCalculada;
			$titleProm1 = '';
			$titleProm2 = '';
			
			if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
				$titleProm1 = 'title="Nota Cuantitativa: '.$defPorEstudiante.'"';
				$notaRedondeadaProm1 = number_format((float)$defPorEstudiante, 1, '.', '');
				$defPorEstudianteFinal = isset($notasCualitativasCache[$notaRedondeadaProm1]) 
					? $notasCualitativasCache[$notaRedondeadaProm1] 
					: "";
				
				if ($defPorEstudianteConNotasCalculada > 0) {
					$titleProm2 = 'title="Nota Cuantitativa: '.$defPorEstudianteConNotasCalculada.'"';
					$notaRedondeadaProm2 = number_format((float)$defPorEstudianteConNotasCalculada, 1, '.', '');
					$defPorEstudianteConNotasFinal = isset($notasCualitativasCache[$notaRedondeadaProm2]) 
						? $notasCualitativasCache[$notaRedondeadaProm2] 
						: "";
				}
			} else {
				// Formato cuantitativo: usar notaDecimales para respetar decimales configurados
				$defPorEstudianteFinal = Boletin::notaDecimales($defPorEstudiante);
				if ($defPorEstudianteConNotasCalculada > 0) {
					$defPorEstudianteConNotasFinal = Boletin::notaDecimales($defPorEstudianteConNotasCalculada);
				}
			}
			
			// Acumular promedios generales (usar valores numéricos para cálculos)
			$promediosGenerales['prom1'] += $defPorEstudiante;
			$promediosGenerales['contador_prom1']++;
			if ($defPorEstudianteConNotasCalculada > 0) {
				$promediosGenerales['prom2'] += $defPorEstudianteConNotasCalculada;
				$promediosGenerales['contador_prom2']++;
			}
			?>
			<!-- Primera PROM -->
			<td style="text-align:center; width:40px; font-weight:bold; color:<?=$color;?>" <?=$titleProm1;?>><?=$defPorEstudianteFinal;?></td>
			<!-- Segunda PROM -->
			<td style="text-align:center; width:40px; font-weight:bold; background:#e0f2fe; color:<?=$colorConNotas;?>" <?=$titleProm2;?>><?=$defPorEstudianteConNotasCalculada > 0 ? $defPorEstudianteConNotasFinal : '-';?></td>
		</tr>
	<?php } ?>
	
	<!-- Footer con promedios -->
	<tfoot>
		<tr style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); font-weight: 700;">
			<td colspan="2" style="text-align: center; border: 2px solid #667eea;">
				<strong>PROMEDIO</strong>
			</td>
			<?php 
			// OPTIMIZACIÓN: Usar cargas cacheadas
			foreach($materias as $carga) {
				for ($p = 1; $p <= $numeroPeriodos; $p++) {
					$promedioPeriodo = 0;
					if ($promediosPorPeriodo[$carga['car_id']][$p]['contador'] > 0) {
						$promedioPeriodo = round($promediosPorPeriodo[$carga['car_id']][$p]['suma'] / $promediosPorPeriodo[$carga['car_id']][$p]['contador'], 2);
					}
					
					$colorPromedio = '#000000';
					if ($promedioPeriodo > 0) {
						if ($promedioPeriodo < $notaMinimaAprobar) {
							$colorPromedio = $colorPerdida;
						} elseif ($promedioPeriodo >= $notaMinimaAprobar) {
							$colorPromedio = $colorGanada;
						}
					}
				// Formatear promedio por período según configuración
				$promedioPeriodoFinal = $promedioPeriodo;
				$titlePromedioPeriodo = '';
				if ($promedioPeriodo > 0) {
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$titlePromedioPeriodo = 'title="Nota Cuantitativa: '.$promedioPeriodo.'"';
						$notaRedondeadaPromedio = number_format((float)$promedioPeriodo, 1, '.', '');
						$promedioPeriodoFinal = isset($notasCualitativasCache[$notaRedondeadaPromedio]) 
							? $notasCualitativasCache[$notaRedondeadaPromedio] 
							: "";
					} else {
						$promedioPeriodoFinal = Boletin::notaDecimales($promedioPeriodo);
					}
				}
				?>
					<td style="text-align: center; color: <?= $colorPromedio; ?>; font-weight: 700; border: 1px solid #e2e8f0;" <?=$titlePromedioPeriodo;?>>
						<?= $promedioPeriodo > 0 ? $promedioPeriodoFinal : '-'; ?>
					</td>
				<?php } ?>
				
				<?php 
				// Promedio de primera definitiva
				$promedioDef1 = 0;
				if ($promediosDefinitivas[$carga['car_id']]['contador_def1'] > 0) {
					$promedioDef1 = round($promediosDefinitivas[$carga['car_id']]['def1'] / $promediosDefinitivas[$carga['car_id']]['contador_def1'], 2);
				}
				
				$colorDef1 = '#000000';
				if ($promedioDef1 > 0) {
					if ($promedioDef1 < $notaMinimaAprobar) {
						$colorDef1 = $colorPerdida;
					} elseif ($promedioDef1 >= $notaMinimaAprobar) {
						$colorDef1 = $colorGanada;
					}
				}
				
				// Formatear promedio de primera definitiva
				$promedioDef1Final = $promedioDef1;
				$titleDef1 = '';
				if ($promedioDef1 > 0) {
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$titleDef1 = 'title="Nota Cuantitativa: '.$promedioDef1.'"';
						$notaRedondeadaDef1 = number_format((float)$promedioDef1, 1, '.', '');
						$promedioDef1Final = isset($notasCualitativasCache[$notaRedondeadaDef1]) 
							? $notasCualitativasCache[$notaRedondeadaDef1] 
							: "";
					} else {
						$promedioDef1Final = Boletin::notaDecimales($promedioDef1);
					}
				}
				
				// Promedio de segunda definitiva
				$promedioDef2 = 0;
				if ($promediosDefinitivas[$carga['car_id']]['contador_def2'] > 0) {
					$promedioDef2 = round($promediosDefinitivas[$carga['car_id']]['def2'] / $promediosDefinitivas[$carga['car_id']]['contador_def2'], 2);
				}
				
				$colorDef2 = '#000000';
				if ($promedioDef2 > 0) {
					if ($promedioDef2 < $notaMinimaAprobar) {
						$colorDef2 = $colorPerdida;
					} elseif ($promedioDef2 >= $notaMinimaAprobar) {
						$colorDef2 = $colorGanada;
					}
				}
				
				// Formatear promedio de segunda definitiva
				$promedioDef2Final = $promedioDef2;
				$titleDef2 = '';
				if ($promedioDef2 > 0) {
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$titleDef2 = 'title="Nota Cuantitativa: '.$promedioDef2.'"';
						$notaRedondeadaDef2 = number_format((float)$promedioDef2, 1, '.', '');
						$promedioDef2Final = isset($notasCualitativasCache[$notaRedondeadaDef2]) 
							? $notasCualitativasCache[$notaRedondeadaDef2] 
							: "";
					} else {
						$promedioDef2Final = Boletin::notaDecimales($promedioDef2);
					}
				}
				?>
				<td style="text-align: center; background:#fffbeb; color: <?= $colorDef1; ?>; font-weight: 700; border: 1px solid #e2e8f0;" <?=$titleDef1;?>>
					<?= $promedioDef1 > 0 ? $promedioDef1Final : '-'; ?>
				</td>
				<td style="text-align: center; background:#e0f2fe; color: <?= $colorDef2; ?>; font-weight: 700; border: 1px solid #e2e8f0;" <?=$titleDef2;?>>
					<?= $promedioDef2 > 0 ? $promedioDef2Final : '-'; ?>
				</td>
			<?php } ?>
			
			<?php 
			// Promedio de primera columna PROM
			$promedioProm1 = 0;
			if ($promediosGenerales['contador_prom1'] > 0) {
				$promedioProm1 = round($promediosGenerales['prom1'] / $promediosGenerales['contador_prom1'], 2);
			}
			
			$colorProm1 = '#000000';
			if ($promedioProm1 > 0) {
				if ($promedioProm1 < $notaMinimaAprobar) {
					$colorProm1 = $colorPerdida;
				} elseif ($promedioProm1 >= $notaMinimaAprobar) {
					$colorProm1 = $colorGanada;
				}
			}
			
			// Formatear promedio de primera PROM
			$promedioProm1Final = $promedioProm1;
			$titleProm1 = '';
			if ($promedioProm1 > 0) {
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					$titleProm1 = 'title="Nota Cuantitativa: '.$promedioProm1.'"';
					$notaRedondeadaProm1 = number_format((float)$promedioProm1, 1, '.', '');
					$promedioProm1Final = isset($notasCualitativasCache[$notaRedondeadaProm1]) 
						? $notasCualitativasCache[$notaRedondeadaProm1] 
						: "";
				} else {
					$promedioProm1Final = Boletin::notaDecimales($promedioProm1);
				}
			}
			
			// Promedio de segunda columna PROM
			$promedioProm2 = 0;
			if ($promediosGenerales['contador_prom2'] > 0) {
				$promedioProm2 = round($promediosGenerales['prom2'] / $promediosGenerales['contador_prom2'], 2);
			}
			
			$colorProm2 = '#000000';
			if ($promedioProm2 > 0) {
				if ($promedioProm2 < $notaMinimaAprobar) {
					$colorProm2 = $colorPerdida;
				} elseif ($promedioProm2 >= $notaMinimaAprobar) {
					$colorProm2 = $colorGanada;
				}
			}
			
			// Formatear promedio de segunda PROM
			$promedioProm2Final = $promedioProm2;
			$titleProm2 = '';
			if ($promedioProm2 > 0) {
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					$titleProm2 = 'title="Nota Cuantitativa: '.$promedioProm2.'"';
					$notaRedondeadaProm2 = number_format((float)$promedioProm2, 1, '.', '');
					$promedioProm2Final = isset($notasCualitativasCache[$notaRedondeadaProm2]) 
						? $notasCualitativasCache[$notaRedondeadaProm2] 
						: "";
				} else {
					$promedioProm2Final = Boletin::notaDecimales($promedioProm2);
				}
			}
			?>
			<td style="text-align: center; font-weight: 700; border: 2px solid #f59e0b; color: <?= $colorProm1; ?>;" <?=$titleProm1;?>>
				<?= $promedioProm1 > 0 ? $promedioProm1Final : '-'; ?>
			</td>
			<td style="text-align: center; font-weight: 700; border: 2px solid #0369a1; background:#e0f2fe; color: <?= $colorProm2; ?>;" <?=$titleProm2;?>>
				<?= $promedioProm2 > 0 ? $promedioProm2Final : '-'; ?>
			</td>
		</tr>
	</tfoot>
  </table>
  <?php include("../compartido/footer-informes.php");
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php"); ?>
<?php } ?>
</body>
</html>
