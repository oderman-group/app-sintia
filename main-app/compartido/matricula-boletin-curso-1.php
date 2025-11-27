<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Ausencias.php");

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$modulo = 1;
if(empty($_GET["periodo"])){
	$periodoActual = 1;
}else{
	$periodoActual = base64_decode($_GET["periodo"]);
}
//$periodoActual=2;
if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==$config['conf_periodos_maximos']) $periodoActuales = "Final";?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(!empty($_GET["id"])){$filtro .= " AND mat_id='".base64_decode($_GET["id"])."'";}
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".base64_decode($_REQUEST["curso"])."'";}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$contPeriodos=0;
$contadorIndicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$numUsr=mysqli_num_rows($usr);
$datosUsr=mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);	
if($numUsr==0)
{
?>
	<script type="text/javascript">
		window.close();
	</script>
<?php
	exit();
}



$contadorPeriodos=0;
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<link rel="shortcut icon" href="<?=$Plataforma->logo;?>">
<style>
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}

/* Estilos profesionales para el boletín */
.boletin-profesional {
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.convencion-card {
	background: #f8f9fa;
	border: 1px solid #dee2e6;
	border-left: 4px solid #2c3e50;
	padding: 15px;
	margin-bottom: 20px;
	font-size: 11px;
	border-radius: 4px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.convencion-card strong {
	color: #2c3e50;
	font-weight: 600;
}

.convencion-diferencia {
	background: #fff9e6;
	border-left: 3px solid #856404;
	padding: 8px;
	margin-top: 5px;
	color: #856404;
}

.header-estudiante {
	background: #2c3e50;
	color: #ffffff;
	border: none;
}

.header-estudiante td {
	padding: 12px 15px;
	font-weight: 600;
	letter-spacing: 0.3px;
}

.info-estudiante {
	background: #ffffff;
	border: 1px solid #dee2e6;
}

.info-estudiante td {
	padding: 10px 15px;
	color: #495057;
	font-weight: 500;
}

.tabla-boletin {
	border: 1px solid #dee2e6;
	border-collapse: collapse;
	box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.tabla-boletin thead tr {
	background: #34495e;
	color: #ffffff;
	height: 40px;
	font-weight: 600;
	letter-spacing: 0.5px;
	text-transform: uppercase;
	font-size: 11px;
}

.tabla-boletin thead td {
	padding: 12px 8px;
	text-align: center;
	border: 1px solid #2c3e50;
}

.tabla-boletin tbody tr {
	border-bottom: 1px solid #e9ecef;
}

.tabla-boletin tbody tr.area-row {
	background: #e9ecef;
	font-weight: 600;
	color: #2c3e50;
}

.tabla-boletin tbody tr.area-row td {
	padding: 10px 12px;
	border: 1px solid #dee2e6;
}

.tabla-boletin tbody tr.materia-row {
	background: #ffffff;
}

.tabla-boletin tbody tr.materia-row:hover {
	background: #f8f9fa;
}

.tabla-boletin tbody tr.materia-row td {
	padding: 12px 8px;
	border: 1px solid #e9ecef;
	color: #495057;
	font-size: 12px;
}

.tabla-boletin tbody tr.indicador-row {
	background: #ffffff;
}

.tabla-boletin tbody tr.indicador-row td {
	padding: 8px 12px;
	border: 1px solid #e9ecef;
	color: #6c757d;
	font-size: 11px;
	font-style: italic;
}

.tabla-boletin tbody tr.promedio-row {
	background: #f8f9fa;
	border-top: 2px solid #2c3e50;
	font-weight: 600;
}

.tabla-boletin tbody tr.promedio-row td {
	padding: 12px 8px;
	border: 1px solid #dee2e6;
	color: #2c3e50;
	font-size: 13px;
}

.nota-destacada {
	font-weight: 600;
	color: #2c3e50;
}

.desempeno-superior { color: #28a745; font-weight: 600; }
.desempeno-alto { color: #17a2b8; font-weight: 600; }
.desempeno-basico { color: #ffc107; font-weight: 600; }
.desempeno-bajo { color: #dc3545; font-weight: 600; }
</style>
</head>

<body style="font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;" class="boletin-profesional">
<?php
//CONSULTA QUE ME TRAE EL DESEMPEÑO
$consultaDesempeno1 = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
$numeroPeriodos=2;

// OPTIMIZACIÓN: Cachear valores de configuración
$notaMinimaAprobar = $config['conf_nota_minima_aprobar'] ?? 3.0;
$numMateriasPerderAno = $config["conf_num_materias_perder_agno"] ?? 3;

// OPTIMIZACIÓN: Cachear desempeños en un array para evitar múltiples iteraciones
$desempenosCache = [];
mysqli_data_seek($consultaDesempeno1, 0);
while($rDesempeno = mysqli_fetch_array($consultaDesempeno1, MYSQLI_BOTH)){
	$desempenosCache[] = $rDesempeno;
}

// OPTIMIZACIÓN: Pre-cargar cache de notas cualitativas
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

// OPTIMIZACIÓN: Pre-cargar todas las ausencias del estudiante para todas las materias y períodos
$ausenciasMapa = [];
if($periodoActual > 0){
	$idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
	$graIdEsc = mysqli_real_escape_string($conexion, $datosUsr['gra_id']);
	$yearEsc = mysqli_real_escape_string($conexion, $year);
	$institucion = (int)$config['conf_id_institucion'];
	
	// Obtener todas las materias del estudiante primero para optimizar la consulta
	$sqlMaterias = "SELECT DISTINCT car.car_materia as mat_id
					FROM " . BD_ACADEMICA . ".academico_cargas car
					INNER JOIN " . BD_ACADEMICA . ".academico_clases cls 
						ON cls.cls_id_carga = car.car_id 
						AND cls.cls_periodo <= " . (int)$periodoActual . "
						AND cls.institucion = car.institucion 
						AND cls.year = car.year
					WHERE car.car_curso = '" . $graIdEsc . "'
					AND car.institucion = {$institucion}
					AND car.year = '{$yearEsc}'";
	
	$consultaMaterias = mysqli_query($conexion, $sqlMaterias);
	$idsMaterias = [];
	if($consultaMaterias){
		while($mat = mysqli_fetch_array($consultaMaterias, MYSQLI_BOTH)){
			$idsMaterias[] = "'" . mysqli_real_escape_string($conexion, $mat['mat_id']) . "'";
		}
	}
	
	if(!empty($idsMaterias)){
		$inMaterias = implode(',', $idsMaterias);
		
		$sqlAusencias = "SELECT 
							aus.aus_id_estudiante,
							car.car_materia as mat_id,
							cls.cls_periodo,
							SUM(aus.aus_ausencias) as sumAus
						FROM " . BD_ACADEMICA . ".academico_ausencias aus
						INNER JOIN " . BD_ACADEMICA . ".academico_clases cls 
							ON cls.cls_id = aus.aus_id_clase 
							AND cls.cls_periodo <= " . (int)$periodoActual . "
							AND cls.institucion = aus.institucion 
							AND cls.year = aus.year
						INNER JOIN " . BD_ACADEMICA . ".academico_cargas car 
							ON car.car_id = cls.cls_id_carga 
							AND car.car_curso = '" . $graIdEsc . "'
							AND car.car_materia IN ({$inMaterias})
							AND car.institucion = aus.institucion 
							AND car.year = aus.year
						WHERE aus.aus_id_estudiante = '{$idEstudianteEsc}'
						AND aus.institucion = {$institucion}
						AND aus.year = '{$yearEsc}'
						GROUP BY aus.aus_id_estudiante, car.car_materia, cls.cls_periodo";
		
		$consultaAusencias = mysqli_query($conexion, $sqlAusencias);
		if($consultaAusencias){
			while($aus = mysqli_fetch_array($consultaAusencias, MYSQLI_BOTH)){
				// Clave: estudiante_materia_periodo
				$key = $aus['aus_id_estudiante'] . '_' . $aus['mat_id'] . '_' . $aus['cls_periodo'];
				$ausenciasMapa[$key] = (float)($aus['sumAus'] ?? 0);
			}
		}
	}
}
 ?>


<?php
$nombreInforme = "BOLETÍN DE CALIFICACIONES";
include("head-informes.php");
?>

<!-- Leyenda de Columnas - Card antes de la tabla -->
<div class="convencion-card">
    <strong style="font-size:12px; letter-spacing:0.5px;">CONVENCIÓN DE COLUMNAS</strong><br>
    <table style="width:100%; margin-top:8px; font-size:10px; border-collapse:separate; border-spacing:0;">
        <tr>
            <td style="padding:6px 8px; border-bottom:1px solid #e9ecef;"><strong>AREAS/ASIGNATURAS:</strong> <span style="color:#6c757d;">Nombre del área académica o asignatura</span></td>
            <td style="padding:6px 8px; border-bottom:1px solid #e9ecef;"><strong>I.H:</strong> <span style="color:#6c757d;">Intensidad Horaria (horas semanales)</span></td>
        </tr>
        <tr>
            <td style="padding:6px 8px; border-bottom:1px solid #e9ecef;"><strong>NOTAS ASIGNATURA:</strong> <span style="color:#6c757d;">Promedio definitivo calculado a partir de las notas registradas en el boletín por períodos académicos</span></td>
            <td style="padding:6px 8px; border-bottom:1px solid #e9ecef;"><strong>NOTAS LOGROS:</strong> <span style="color:#6c757d;">Promedio de los logros/indicadores de desempeño evaluados</span></td>
        </tr>
        <tr>
            <td colspan="2" class="convencion-diferencia" style="padding:8px; margin-top:5px;">
                <strong>DIFERENCIA:</strong> La "NOTA ASIGNATURA" puede diferir de "NOTAS LOGROS" porque la primera es el promedio de las notas definitivas por período registradas en el boletín, mientras que la segunda es el promedio directo de los logros/indicadores evaluados. Ambas son válidas según el sistema de evaluación institucional.
            </td>
        </tr>
        <tr>
            <td style="padding:6px 8px;"><strong>PRO:</strong> <span style="color:#6c757d;">Promedio general del estudiante (solo materias que suman al promedio)</span></td>
            <td style="padding:6px 8px;"><strong>DESEMPEÑO:</strong> <span style="color:#6c757d;">Nivel de desempeño según la escala institucional (Superior, Alto, Básico, Bajo)</span></td>
        </tr>
        <tr>
            <td style="padding:6px 8px;"><strong>AUS:</strong> <span style="color:#6c757d;">Total de ausencias acumuladas en la asignatura hasta el período actual</span></td>
            <td style="padding:6px 8px;"></td>
        </tr>
    </table>
</div>

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" class="header-estudiante" style="margin-bottom:15px;">
	<tr>
    	<td style="width:50%;">C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td style="width:50%;">Nombre: <b><?=$nombre?></b></td>   
    </tr>
    
    <tr class="info-estudiante">
    	<td>Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
        <td>Periodo: <b><?=strtoupper($periodoActuales);?></b></td>    
    </tr>
</table>
<br>
<table width="100%" id="tblBoletin" class="tabla-boletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
<thead>
<tr>
<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
<td width="2%" align="center">I.H</td>
<td width="3%" align="center" style="font-size:10px;">NOTAS<br>ASIGNATURA</td>
<td width="3%" align="center" style="font-size:10px;">NOTAS<br>LOGROS</td>
<td width="4%" align="center">PRO</td>
<td width="8%" align="center">DESEMPE&Ntilde;O</td>   
<td width="5%" align="center">AUS</td>
</tr>
</thead>
<tbody>
<tr class="area-row">
    	<td colspan="7"></td>
    </tr>
        <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->
        <?php while($fila = mysqli_fetch_array($consultaMatAreaEst, MYSQLI_BOTH)){
		
		$condicionArray = [];
		for($p = 1; $p <= $periodoActual; $p++){
			$condicionArray[] = $p;
		}
		$condicion = implode(",", $condicionArray);
		$condicion2 = (string)$periodoActual;
		
//CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA (usando método centralizado)
$promedioAreaCompleto = Boletin::calcularPromedioAreaCompleto($config, $matriculadosDatos['mat_id'], $fila["ar_id"], $condicionArray, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
// OPTIMIZACIÓN: Usar prepared statements para obtener nombre del área
$consultaAreaNombre = null;
$sqlArea = "SELECT ar_nombre, ? AS suma FROM ".BD_ACADEMICA.".academico_areas WHERE ar_id=? AND institucion=? AND year=? LIMIT 1";
$stmtArea = mysqli_prepare($conexion, $sqlArea);
if ($stmtArea) {
	$acumulado = $promedioAreaCompleto['acumulado'];
	$arId = $fila["ar_id"];
	$institucion = (int)$config['conf_id_institucion'];
	mysqli_stmt_bind_param($stmtArea, "dsis", $acumulado, $arId, $institucion, $year);
	mysqli_stmt_execute($stmtArea);
	$consultaAreaNombre = mysqli_stmt_get_result($stmtArea);
	mysqli_stmt_close($stmtArea);
}
$consultaNotdefArea = $consultaAreaNombre;

//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consultaMat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consultaMatPer = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

// OPTIMIZACIÓN: Pre-cargar notas por período en un mapa
$notasPeriodosMapa = [];
mysqli_data_seek($consultaMatPer, 0);
while($fila3 = mysqli_fetch_array($consultaMatPer, MYSQLI_BOTH)){
	$keyNota = $fila3["mat_id"] . '_' . $fila3["bol_periodo"];
	if (!isset($notasPeriodosMapa[$keyNota])) {
		$notasPeriodosMapa[$keyNota] = [];
	}
	$notaBoletin = !empty($fila3["bol_nota"]) ? $fila3["bol_nota"] : 0;
	$notaPeriodo = round($notaBoletin, $config['conf_decimales_notas']);
	if($notaPeriodo==1)	$notaPeriodo="1.0";	if($notaPeriodo==2)	$notaPeriodo="2.0";		if($notaPeriodo==3)	$notaPeriodo="3.0";	if($notaPeriodo==4)	$notaPeriodo="4.0";	if($notaPeriodo==5)	$notaPeriodo="5.0";
	$notasPeriodosMapa[$keyNota][] = $notaPeriodo;
}

//CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
$consultaMatIndicadores = Boletin::obtenerIndicadoresPorMateria($datosUsr["mat_grado"], $datosUsr["mat_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);

$numIndicadores=mysqli_num_rows($consultaMatIndicadores);

$resultadoNotArea=mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
$numfilasNotArea=mysqli_num_rows($consultaNotdefArea);
$totalPromedio=0;
if(!empty($resultadoNotArea['suma'])){
	$totalPromedio=round( $resultadoNotArea["suma"],1);
}

if($totalPromedio==1)	$totalPromedio="1.0";	if($totalPromedio==2)	$totalPromedio="2.0";		if($totalPromedio==3)	$totalPromedio="3.0";	if($totalPromedio==4)	$totalPromedio="4.0";	if($totalPromedio==5)	$totalPromedio="5.0";
	if($numfilasNotArea>0){
			?>
  <tr class="area-row">
            <td><?php echo $resultadoNotArea["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <td align="center" style="font-weight:bold;"><?php 
		
		if(isset($datosUsr["gra_nivel"]) && $datosUsr["gra_nivel"] == PREESCOLAR){
				$notaFA = ceil($totalPromedio);
				switch($notaFA){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				}else{
					$totalPromedioFinal=$totalPromedio;
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						// OPTIMIZACIÓN: Usar cache de notas cualitativas
						$notaRedondeada = number_format((float)$totalPromedio, 1, '.', '');
						$totalPromedioFinal = isset($notasCualitativasCache[$notaRedondeada]) 
							? $notasCualitativasCache[$notaRedondeada] 
							: "";
					}
		echo $totalPromedioFinal;
				}
		
		?></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
	</tr>
<?php

while($fila2=mysqli_fetch_array($consultaMat, MYSQLI_BOTH)){ 
$contadorPeriodos=0;
	// OPTIMIZACIÓN: Obtener notas del mapa pre-cargado
	$notas = [];
	for($j = 1; $j <= $numeroPeriodos; $j++){
		$keyNota = $fila2["mat_id"] . '_' . $j;
		if(isset($notasPeriodosMapa[$keyNota]) && !empty($notasPeriodosMapa[$keyNota])){
			$contadorPeriodos++;
			$notas[$j] = $notasPeriodosMapa[$keyNota][0]; // Tomar la primera nota del período
		}
	}
	
	// Calcular promedio de logros/indicadores para esta materia
	$promedioLogros = 0;
	$contadorLogros = 0;
	if($numIndicadores > 0){
		mysqli_data_seek($consultaMatIndicadores, 0);
		while($filaIndicador = mysqli_fetch_array($consultaMatIndicadores, MYSQLI_BOTH)){
			if($filaIndicador["mat_id"] == $fila2["mat_id"]){
				$notaLogro = !empty($filaIndicador["nota"]) ? (float)$filaIndicador["nota"] : 0;
				if($notaLogro > 0){
					$promedioLogros += $notaLogro;
					$contadorLogros++;
				}
			}
		}
	}
	
	// Calcular promedio final de logros
	$promedioLogrosFinal = 0;
	if($contadorLogros > 0){
		$promedioLogrosFinal = round($promedioLogros / $contadorLogros, 1);
		if($promedioLogrosFinal==1) $promedioLogrosFinal="1.0";
		if($promedioLogrosFinal==2) $promedioLogrosFinal="2.0";
		if($promedioLogrosFinal==3) $promedioLogrosFinal="3.0";
		if($promedioLogrosFinal==4) $promedioLogrosFinal="4.0";
		if($promedioLogrosFinal==5) $promedioLogrosFinal="5.0";
	}
?>
 <tr class="materia-row">
            <td style="font-weight:600;">&raquo; <?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:600;"><?php echo $fila["car_ih"];?></td>
<!-- Columnas de períodos comentadas - no se muestran en el header
<?php for($l=1;$l<=$numeroPeriodos;$l++){ ?>
			<td class=""  align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;">
			<?php 
			if(isset($datosUsr["gra_nivel"]) && $datosUsr["gra_nivel"] == PREESCOLAR){
				if(isset($notas[$l]) && $notas[$l] !== null && $notas[$l] !== ''){
					$notaF = ceil((float)$notas[$l]);
					switch($notaF){
						case 1: echo "D"; break;
						case 2: echo "I"; break;
						case 3: echo "A"; break;
						case 4: echo "S"; break;
						case 5: echo "E"; break;
						default: echo ""; break;
					}
				}
			}else{
				if(isset($notas[$l])){
					// OPTIMIZACIÓN: Usar cache de notas cualitativas y desempeños
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$notaRedondeada = number_format((float)$notas[$l], 1, '.', '');
						$notaCualitativa = isset($notasCualitativasCache[$notaRedondeada]) 
							? $notasCualitativasCache[$notaRedondeada] 
							: "";
						echo $notaCualitativa;
					}else{
						$desempenoNotaP = Boletin::obtenerDatosTipoDeNotasCargadas($desempenosCache, $notas[$l]);
						echo $notas[$l]."<br>".($desempenoNotaP['notip_nombre'] ?? '');
					}
				}
			}

			if (!isset($promedios[$l])) {
				$promedios[$l] = 0;
			}
			if (!isset($contpromedios[$l])) {
				$contpromedios[$l] = 0;
			}
			if ($fila2["mat_sumar_promedio"] == SI) {
				if (isset($notas[$l])) {
					$promedios[$l] += $notas[$l];
				}
				$contpromedios[$l]++;
			}
			?></td>
        <?php }?>
-->
      <?php 
	  $totalPromedio2=round( $fila2["suma"],1);
	   
	   if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
	   // OPTIMIZACIÓN: Usar valor cacheado
	   if($totalPromedio2<$notaMinimaAprobar){$materiasPerdidas++;}
	   ?>
       
        <td align="center" class="nota-destacada"><?php 
		
					if(isset($datosUsr["gra_nivel"]) && $datosUsr["gra_nivel"] == PREESCOLAR){
				$notaFI = ceil($totalPromedio2);
				switch($notaFI){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				}else{
					$totalPromedio2Final=$totalPromedio2;
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						// OPTIMIZACIÓN: Usar cache de notas cualitativas
						$notaRedondeada = number_format((float)$totalPromedio2, 1, '.', '');
						$totalPromedio2Final = isset($notasCualitativasCache[$notaRedondeada]) 
							? $notasCualitativasCache[$notaRedondeada] 
							: "";
					}
					echo $totalPromedio2Final;
				}
		
		?></td>
        <td align="center" class="nota-destacada"><?php //NOTAS LOGROS
		// Mostrar promedio de logros/indicadores
		if($contadorLogros > 0){
			if(isset($datosUsr["gra_nivel"]) && $datosUsr["gra_nivel"] == PREESCOLAR){
				$notaLogrosPreescolar = ceil((float)$promedioLogrosFinal);
				switch($notaLogrosPreescolar){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
					default: echo ""; break;
				}
			}else{
				$promedioLogrosFinalMostrar = $promedioLogrosFinal;
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					// OPTIMIZACIÓN: Usar cache de notas cualitativas
					$notaRedondeada = number_format((float)$promedioLogrosFinal, 1, '.', '');
					$promedioLogrosFinalMostrar = isset($notasCualitativasCache[$notaRedondeada]) 
						? $notasCualitativasCache[$notaRedondeada] 
						: "";
				}
				echo $promedioLogrosFinalMostrar;
			}
		} else {
			echo "-";
		}
		?></td>
        <td align="center" class="nota-destacada"><?php //PRO - Promedio (vacío para materias individuales)
		// Esta columna se usa solo en la fila de PROMEDIO general
		?></td>
        <td align="center"><?php //DESEMPEÑO
		// OPTIMIZACIÓN: Usar cache de desempeños
		$rDesempeno = Boletin::obtenerDatosTipoDeNotasCargadas($desempenosCache, $totalPromedio2);
		$claseDesempeno = '';
		if($rDesempeno){
			if(isset($datosUsr["gra_nivel"]) && $datosUsr["gra_nivel"] == PREESCOLAR){
				$notaFD = ceil($totalPromedio2);
				switch($notaFD){
					case 1: $textoDesempeno = "BAJO"; $claseDesempeno = 'desempeno-bajo'; break;
					case 2: $textoDesempeno = "BAJO"; $claseDesempeno = 'desempeno-bajo'; break;
					case 3: $textoDesempeno = "B&Aacute;SICO"; $claseDesempeno = 'desempeno-basico'; break;
					case 4: $textoDesempeno = "ALTO"; $claseDesempeno = 'desempeno-alto'; break;
					case 5: $textoDesempeno = "SUPERIOR"; $claseDesempeno = 'desempeno-superior'; break;
					default: $textoDesempeno = ""; break;
				}
				echo '<span class="'.$claseDesempeno.'">'.$textoDesempeno.'</span>';
			}else{
				$nombreDesempeno = $rDesempeno["notip_nombre"];
				// Determinar clase según el nombre del desempeño
				if(stripos($nombreDesempeno, 'superior') !== false) $claseDesempeno = 'desempeno-superior';
				elseif(stripos($nombreDesempeno, 'alto') !== false) $claseDesempeno = 'desempeno-alto';
				elseif(stripos($nombreDesempeno, 'básico') !== false || stripos($nombreDesempeno, 'basico') !== false) $claseDesempeno = 'desempeno-basico';
				elseif(stripos($nombreDesempeno, 'bajo') !== false) $claseDesempeno = 'desempeno-bajo';
				echo '<span class="'.$claseDesempeno.'">'.$nombreDesempeno.'</span>';
			}
		}
		?></td>
        <td align="center"><?php //AUS
		// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
		$sumAusencias = 0;
		for($j = 1; $j <= $periodoActual; $j++){
			$keyAusencias = $matriculadosDatos['mat_id'] . '_' . $fila2['mat_id'] . '_' . $j;
			if(isset($ausenciasMapa[$keyAusencias])){
				$sumAusencias += $ausenciasMapa[$keyAusencias];
			}
		}
		if($sumAusencias>0){ echo $sumAusencias;} else{ echo "0.0";}
		?></td>
	</tr>
<?php
if($numIndicadores>0){
	 mysqli_data_seek($consultaMatIndicadores,0);
	 $contadorIndicadores=0;
	while($fila4=mysqli_fetch_array($consultaMatIndicadores, MYSQLI_BOTH)){
	if($fila4["mat_id"]==$fila2["mat_id"]){
		$contadorIndicadores++;
		$notaIndicador=round($fila4["nota"],1);
		 if($notaIndicador==1)	$notaIndicador="1.0";	if($notaIndicador==2)	$notaIndicador="2.0";		if($notaIndicador==3)	$notaIndicador="3.0";	if($notaIndicador==4)	$notaIndicador="4.0";	if($notaIndicador==5)	$notaIndicador="5.0";
	?>
<tr class="indicador-row">
            <td><?php echo $contadorIndicadores.". ".$fila4["ind_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
            <td align="center" style="font-weight:bold;"></td>
<?php
	}//fin if
	}
}
}//while fin materias
?>  
<?php }}//while fin areas?>
	 

          

            

    <tr class="promedio-row" align="center">
        <td colspan="2" align="right">PROMEDIO</td>
        <td style="font-size:16px; font-weight:bold;"><?php 
		// Calcular promedio de NOTAS ASIGNATURA
		$promedioAsignaturas = 0;
		$contadorAsignaturas = 0;
		// Este cálculo se hace basado en las materias que suman al promedio
		// Por ahora mostramos vacío o podemos calcularlo si es necesario
		?></td>
        <td style="font-size:16px; font-weight:bold;"><?php 
		// Calcular promedio de NOTAS LOGROS
		// Por ahora mostramos vacío
		?></td>
        <td style="font-size:16px; font-weight:bold;"><?php 
		// Calcular PRO (promedio general)
		$promedioGeneral = 0;
		$contadorMaterias = 0;
		// Este cálculo se hace basado en las materias que suman al promedio
		// Por ahora mostramos vacío o podemos calcularlo si es necesario
		?></td>
        <td></td>
        <td></td>
    </tr>
</tbody>
</table>

<?php for($n=1;$n<=$numeroPeriodos;$n++){if($promedios[$n]!=0){$promedios[$n]=0; $contpromedios[$n]=0;} } ?>

<p>&nbsp;</p>
<?php 
// OPTIMIZACIÓN: Usar prepared statements para consulta de disciplina
// Nota: Para IN() con valores dinámicos, necesitamos escapar los valores manualmente
$cndisiplina = null;
$idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
$condicionEsc = mysqli_real_escape_string($conexion, $condicion);
$institucion = (int)$config['conf_id_institucion'];
$yearEsc = mysqli_real_escape_string($conexion, $year);
$sqlDisciplina = "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='{$idEstudianteEsc}' AND institucion={$institucion} AND year='{$yearEsc}' AND dn_periodo IN ({$condicionEsc})";
$cndisiplina = mysqli_query($conexion, $sqlDisciplina);
if($cndisiplina && mysqli_num_rows($cndisiplina)>0){
?>
<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

    <tr style="font-weight:bold; background:#036; border-color:#036; height:40px; color:#FC0; font-size:12px; text-align:center">
    	<td colspan="3">NOTA DE COMPORTAMIENTO</td>
    </tr>
    
    <tr style="font-weight:bold; background:#F06; border-color:#F06; height:25px; color:#FFF; font-size:12px; text-align:center">
        <td width="8%">Periodo</td>
        <td width="8%">Nota</td>
        <td>Observaciones</td>
    </tr>
<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
// OPTIMIZACIÓN: Usar cache de desempeños
$desempenoND = Boletin::obtenerDatosTipoDeNotasCargadas($desempenosCache, $rndisiplina["dn_nota"]);
?>
    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
        <td><?=$rndisiplina["dn_periodo"]?></td>
        <td><?=$desempenoND['notip_nombre'] ?? ""?></td>
        <td><?=$rndisiplina["dn_observacion"]?></td>
    </tr>
<?php }?>
</table>

<?php }?>
<!--<hr align="center" width="100%">-->
<div align="center">
<table width="100%" cellspacing="0" cellpadding="0"  border="0" style="text-align:center; font-size:12px;">
  <tr>
    <td style="font-weight:bold;" align="left">
    
    <!-- <?php if($num_observaciones>0){?>COMPORTAMIENTO:<?php }?> <b><u><?=strtoupper($r_diciplina[3]);?></u></b><br> -->
    	<?php
	?>
    </td>
  </tr>
</table>
<?php
//print_r($vectorT);
?>
</div>
<!--
<div>
<table width="100%" cellspacing="0" cellpadding="0"  border="0" style="text-align:center; font-size:12px;">
  <tr>
    <td style="font-weight:bold;" align="left">
    OBSERVACIONES:_____________________________________________________________________________________________________________<br><br>
    ____________________________________________________________________________________________________________________________<br><br>
    ____________________________________________________________________________________________________________________________<br>
    </td>
  </tr>
</table>

</div>
-->

<p>&nbsp;</p>
<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
	<tr>
		<td align="center">_________________________________<br><!--<?=strtoupper("");?><br>-->Rector(a)</td>
		<td align="center">_________________________________<br><!--<?=strtoupper("");?><br>-->Director(a) de grupo</td>
    </tr>
</table>  

<!--
<br>
<div align="center">
<table width="100%" cellspacing="0" cellpadding="0"  border="1" style="text-align:center; font-size:8px; background:#FFFFCC;">
  <tr style="text-transform:uppercase;">
    <td style="font-weight:bold;" align="right">ESCALA NACIONAL</td><td>Desempe&ntilde;o Superior</td><td>Desempe&ntilde;o Alto</td><td>Desempe&ntilde;o B&aacute;sico</td><td>Desempe&ntilde;o Bajo</td>
  </tr>
  
  <tr>
  	<td style="font-weight:bold;" align="right">RANGO INSTITUCIONAL</td>
  	<td>NO HAY</td><td>NO HAY</td><td>NO HAY</td><td>NO HAY</td>  
  </tr>

</table>
-->




</div>  
<?php 
$msj = "";
if($periodoActual==4){
	// OPTIMIZACIÓN: Usar valor cacheado
	$nombreEstudiante = !empty($nombre) ? $nombre : 'SIN NOMBRE';
	if($materiasPerdidas>=$numMateriasPerderAno)
		$msj = "<center>EL (LA) ESTUDIANTE ".$nombreEstudiante." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<3 and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".$nombreEstudiante." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".$nombreEstudiante." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
}
?>


<p align="center">

<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:12px;" align="center"><?=$msj;?></div>

</p>
<?php include("../compartido/footer-informes.php") ?>				                   
<!-- 
<div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
                                    -->
 <div id="saltoPagina"></div>
                                    
<?php
 }// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

<script type="application/javascript">
print();
</script>                                    
                          
</body>
</html>
