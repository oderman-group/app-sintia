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
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
    
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
if($periodoActual==4) $periodoActuales = "Final";?>
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
$cont_periodos=0;
$contador_indicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$num_usr=mysqli_num_rows($usr);
$datosUsr=mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);	
if($num_usr==0)
{
?>
	<script type="text/javascript">
		window.close();
	</script>
<?php
	exit();
}

$contador_periodos=0;

$contp = 1;
// Configuración de visualización de elementos
$formularioEnviado = isset($_GET['config_aplicada']) && $_GET['config_aplicada'] == '1';

$mostrarLogoEncabezado = $formularioEnviado 
    ? (isset($_GET['mostrar_logo_encabezado']) ? (int)$_GET['mostrar_logo_encabezado'] : 0)
    : 1; // Por defecto visible
$mostrarFirmas = $formularioEnviado 
    ? (isset($_GET['mostrar_firmas']) ? (int)$_GET['mostrar_firmas'] : 0)
    : 1; // Por defecto visible
$mostrarLogoPlataforma = $formularioEnviado 
    ? (isset($_GET['mostrar_logo_plataforma']) ? (int)$_GET['mostrar_logo_plataforma'] : 0)
    : 1; // Por defecto visible
$mostrarAreas = $formularioEnviado 
    ? (isset($_GET['mostrar_areas']) ? (int)$_GET['mostrar_areas'] : 0)
    : 1; // Por defecto visible
$mostrarIndicadores = $formularioEnviado 
    ? (isset($_GET['mostrar_indicadores']) ? (int)$_GET['mostrar_indicadores'] : 0)
    : 1; // Por defecto visible
$mostrarIH = $formularioEnviado 
    ? (isset($_GET['mostrar_ih']) ? (int)$_GET['mostrar_ih'] : 0)
    : 1; // Por defecto visible
$mostrarAUS = $formularioEnviado 
    ? (isset($_GET['mostrar_aus']) ? (int)$_GET['mostrar_aus'] : 0)
    : 1; // Por defecto visible
$mostrarPuestoCurso = $formularioEnviado 
    ? (isset($_GET['mostrar_puesto_curso']) ? (int)$_GET['mostrar_puesto_curso'] : 0)
    : 1; // Por defecto visible (si es 0, mostrar línea para escribir manualmente)

$puestoCurso = 0;
$promedioPuesto = 0;
$puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual,$matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);

foreach($puestos as $puesto){
	if($puesto['estudiante_id']==$matriculadosDatos['mat_id']){
		$puestoCurso = $puesto['puesto'];
		$promedioPuesto = round($puesto['prom'],2);
	}
	$contp ++;
}
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<title>Boletín Formato 6</title>
	<link rel="shortcut icon" href="../sintia-icono.png" />
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
<style>
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}

/* Estilos profesionales para el boletín */
body {
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	color: #333;
}

.header-boletin {
	background: #34495e;
	color: #FFF;
	font-weight: bold;
	height: 35px;
	font-size: 13px;
	letter-spacing: 0.5px;
}

.tabla-boletin {
	border: 1px solid #dee2e6;
	border-collapse: collapse;
	width: 100%;
	margin-top: 15px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.tabla-boletin th, .tabla-boletin td {
	border: 1px solid #dee2e6;
	padding: 8px;
	text-align: center;
	font-size: 11px;
}

.tabla-boletin thead th {
	background: #34495e;
	color: #FFF;
	font-weight: bold;
	height: 30px;
	font-size: 12px;
	letter-spacing: 0.3px;
}

.area-row {
	background: #e9ecef;
	font-weight: bold;
	font-size: 12px;
	text-align: left;
}

.area-row td:first-child {
	text-align: left !important;
	padding-left: 15px !important;
}

.materia-row {
	background: #FFFFFF;
	font-size: 11px;
}

.materia-row:nth-child(even) {
	background: #f8f9fa;
}

.materia-row td:first-child {
	text-align: left !important;
	padding-left: 15px !important;
}

.indicador-row {
	background: #fdfdfd;
	font-size: 10px;
	text-align: left;
}

.indicador-row td:first-child {
	text-align: left !important;
	padding-left: 30px !important;
}

.promedio-row {
	background: #e9ecef;
	font-weight: bold;
	font-size: 12px;
	border-top: 2px solid #2c3e50;
}

.nota-destacada {
	font-weight: 600;
	font-size: 12px;
}

.info-header {
	background: #2c3e50;
	color: #ffffff;
	padding: 12px 15px;
	font-weight: 600;
	letter-spacing: 0.3px;
}

.info-content {
	background: #ffffff;
	border: 1px solid #dee2e6;
	padding: 10px 15px;
	color: #495057;
	font-weight: 500;
}

.tabla-comportamiento {
	border: 1px solid #dee2e6;
	border-collapse: collapse;
	width: 100%;
	margin-top: 15px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.tabla-comportamiento thead th {
	background: #34495e;
	color: #FFF;
	font-weight: bold;
	padding: 12px;
	font-size: 12px;
	letter-spacing: 0.3px;
}

.tabla-comportamiento tbody td {
	border: 1px solid #dee2e6;
	padding: 10px;
	font-size: 11px;
}
</style>
</head>

<body>
<?php
//CONSULTA QUE ME TRAE EL DESEMPEÑO
$consulta_desempeno = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consulta_mat_area_est = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
$numero_periodos=$periodoActual;

// ============================================
// OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
// ============================================

// OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y todos los períodos
$notasBoletinMapa = []; // [carga][periodo] => datos_nota
try {
	$sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota, bol_observaciones_boletin
				 FROM " . BD_ACADEMICA . ".academico_boletin
				 WHERE bol_estudiante = ?
				   AND institucion = ?
				   AND year = ?
				   AND bol_periodo <= ?";
	$paramNotas = [
		$matriculadosDatos['mat_id'],
		$config['conf_id_institucion'],
		$year,
		$periodoActual
	];
	$resNotas = BindSQL::prepararSQL($sqlNotas, $paramNotas);
	while ($rowNota = mysqli_fetch_array($resNotas, MYSQLI_BOTH)) {
		$idCarga = $rowNota['bol_carga'];
		$per = (int)$rowNota['bol_periodo'];
		if (!isset($notasBoletinMapa[$idCarga])) {
			$notasBoletinMapa[$idCarga] = [];
		}
		$notasBoletinMapa[$idCarga][$per] = $rowNota;
	}
} catch (Exception $eNotas) {
	include("../compartido/error-catch-to-report.php");
}

// OPTIMIZACIÓN 2: Pre-cargar todas las nivelaciones para este estudiante
$nivelacionesMapa = []; // [materia_id] => datos_nivelacion
try {
	// Primero obtener todas las materias del estudiante
	$idsMaterias = [];
	mysqli_data_seek($consulta_mat_area_est, 0);
	while ($filaTemp = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
		$consulta_a_mat_temp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $filaTemp["ar_id"], "1,2,3,4", $year);
		while ($fila2Temp = mysqli_fetch_array($consulta_a_mat_temp, MYSQLI_BOTH)) {
			if (!in_array($fila2Temp['mat_id'], $idsMaterias)) {
				$idsMaterias[] = $fila2Temp['mat_id'];
			}
		}
	}
	mysqli_data_seek($consulta_mat_area_est, 0);
	
	if (!empty($idsMaterias)) {
		foreach ($idsMaterias as $idMateria) {
			$nivTemp = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $idMateria, $year);
			$niv = mysqli_fetch_array($nivTemp, MYSQLI_BOTH);
			if ($niv) {
				$nivelacionesMapa[$idMateria] = $niv;
			}
		}
	}
} catch (Exception $eNiv) {
	include("../compartido/error-catch-to-report.php");
}

// OPTIMIZACIÓN 3: Pre-cargar todas las ausencias para este estudiante
$ausenciasMapa = []; // [carga] => suma_ausencias
try {
	// Obtener todas las cargas del estudiante
	$idsCargas = [];
	mysqli_data_seek($consulta_mat_area_est, 0);
	while ($filaTemp = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
		$consulta_a_mat_temp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $filaTemp["ar_id"], "1,2,3,4", $year);
		while ($fila2Temp = mysqli_fetch_array($consulta_a_mat_temp, MYSQLI_BOTH)) {
			if (!in_array($fila2Temp['car_id'], $idsCargas)) {
				$idsCargas[] = $fila2Temp['car_id'];
			}
		}
	}
	mysqli_data_seek($consulta_mat_area_est, 0);
	
	if (!empty($idsCargas)) {
		$idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
		$inCargas = implode(',', array_map('intval', $idsCargas));
		$institucion = (int)$config['conf_id_institucion'];
		$yearEsc = mysqli_real_escape_string($conexion, $year);
		$periodoEsc = (int)$periodoActual;
		
		$sqlAusencias = "SELECT cls.cls_id_carga, SUM(aus.aus_ausencias) as total_ausencias
						 FROM " . BD_ACADEMICA . ".academico_clases cls
						 INNER JOIN " . BD_ACADEMICA . ".academico_ausencias aus 
						     ON aus.aus_id_clase = cls.cls_id 
						     AND aus.aus_id_estudiante = '{$idEstudianteEsc}'
						     AND aus.institucion = cls.institucion 
						     AND aus.year = cls.year
						 WHERE cls.cls_id_carga IN ({$inCargas})
						   AND cls.cls_periodo <= {$periodoEsc}
						   AND cls.institucion = {$institucion}
						   AND cls.year = '{$yearEsc}'
						 GROUP BY cls.cls_id_carga";
		
		$consultaAusencias = mysqli_query($conexion, $sqlAusencias);
		if($consultaAusencias){
			while($rowAus = mysqli_fetch_array($consultaAusencias, MYSQLI_BOTH)){
				$ausenciasMapa[$rowAus['cls_id_carga']] = (float)$rowAus['total_ausencias'];
			}
		}
	}
} catch (Exception $eAus) {
	include("../compartido/error-catch-to-report.php");
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
		while($tipoNota = mysqli_fetch_array($consultaNotasTipo, MYSQLI_BOTH)){
			// Pre-cargar cache para todos los valores posibles (de 0.1 en 0.1)
			for($i = $tipoNota['notip_desde']; $i <= $tipoNota['notip_hasta']; $i += 0.1){
				$key = number_format((float)$i, 1, '.', '');
				if(!isset($notasCualitativasCache[$key])){
					$notasCualitativasCache[$key] = $tipoNota['notip_nombre'];
				}
			}
		}
	}
}

// OPTIMIZACIÓN 5: Pre-cargar desempeños en cache
$desempenosCache = [];
mysqli_data_seek($consulta_desempeno, 0);
while($r_desempeno = mysqli_fetch_array($consulta_desempeno, MYSQLI_BOTH)){
	// Crear cache para todos los valores en el rango
	for($i = $r_desempeno['notip_desde']; $i <= $r_desempeno['notip_hasta']; $i += 0.1){
		$key = number_format((float)$i, 1, '.', '');
		if(!isset($desempenosCache[$key])){
			$desempenosCache[$key] = $r_desempeno;
		}
	}
}
mysqli_data_seek($consulta_desempeno, 0);
 ?>

<?php
$nombreInforme = "BOLETÍN DE CALIFICACIONES";
if($mostrarLogoEncabezado){
    include("../compartido/head-informes.php");
}
?>

<!-- Formulario de configuración -->
<div class="config-boletin-form" style="position: fixed; top: 10px; right: 10px; background: white; padding: 15px; border: 2px solid #34495e; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 1000; max-width: 300px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <h4 style="margin-top: 0; color: #34495e;">⚙️ Configuración del Boletín</h4>
    <form method="GET" id="configBoletinForm">
        <?php
        // Mantener todos los parámetros GET existentes
        if(!empty($_GET["id"])) echo '<input type="hidden" name="id" value="'.htmlspecialchars($_GET["id"]).'">';
        if(!empty($_GET["periodo"])) echo '<input type="hidden" name="periodo" value="'.htmlspecialchars($_GET["periodo"]).'">';
        if(!empty($_REQUEST["curso"])) echo '<input type="hidden" name="curso" value="'.htmlspecialchars($_REQUEST["curso"]).'">';
        if(!empty($_REQUEST["grupo"])) echo '<input type="hidden" name="grupo" value="'.htmlspecialchars($_REQUEST["grupo"]).'">';
        if(!empty($_GET["year"])) echo '<input type="hidden" name="year" value="'.htmlspecialchars($_GET["year"]).'">';
        echo '<input type="hidden" name="config_aplicada" value="1">';
        ?>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_logo_encabezado" value="1" <?= $mostrarLogoEncabezado ? 'checked' : '' ?>>
            Mostrar encabezado completo
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_areas" value="1" <?= $mostrarAreas ? 'checked' : '' ?>>
            Mostrar filas de áreas
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_indicadores" value="1" <?= $mostrarIndicadores ? 'checked' : '' ?>>
            Mostrar filas de indicadores
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_ih" value="1" <?= $mostrarIH ? 'checked' : '' ?>>
            Mostrar columna I.H (Intensidad Horaria)
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_aus" value="1" <?= $mostrarAUS ? 'checked' : '' ?>>
            Mostrar columna AUS (Ausencias)
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_puesto_curso" value="1" <?= $mostrarPuestoCurso ? 'checked' : '' ?>>
            Mostrar puesto de curso (si no, línea para escribir)
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_firmas" value="1" <?= $mostrarFirmas ? 'checked' : '' ?>>
            Mostrar firmas del pie de página
        </label>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_logo_plataforma" value="1" <?= $mostrarLogoPlataforma ? 'checked' : '' ?>>
            Mostrar logo y leyenda de SINTIA
        </label>
        <button type="submit" style="background: #34495e; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Aplicar Configuración</button>
    </form>
</div>
<style>
    @media print {
        .config-boletin-form { display: none !important; }
    }
</style>

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" class="info-header" style="font-size:12px; margin-bottom: 15px;">
    <tr>
    	<td style="padding: 12px 15px;">C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td colspan="2" style="padding: 12px 15px;">Nombre: <b><?=$nombre?></b></td>   
    </tr>
    
    <tr class="info-content">
    	<td style="padding: 10px 15px;">Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
        <td style="padding: 10px 15px;">Periodo: <b><?=strtoupper($periodoActuales);?></b></td>
        <td style="padding: 10px 15px;">Puesto Curso: <?php if($mostrarPuestoCurso): ?><b><?=$puestoCurso?></b><?php else: ?>________________<?php endif; ?></td>    
    </tr>
</table>
<br>
<table width="100%" id="tblBoletin" class="tabla-boletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
<tr class="header-boletin">
<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
<?php if($mostrarIH): ?><td width="2%" align="center">I.H</td><?php endif; ?>


<?php 
// Calcular número de columnas base: AREAS/ASIGNATURAS + periodos + PRO + DESEMPEÑO
$columnas = 3 + $numero_periodos; // AREAS/ASIGNATURAS + periodos + PRO + DESEMPEÑO
if($mostrarIH) $columnas++; // + I.H si está visible
if($mostrarAUS) $columnas++; // + AUS si está visible
for($j=1;$j<=$numero_periodos;$j++){?>
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=base64_encode($matriculadosDatos['mat_id']);?>&periodo=<?=base64_encode($j)?>" style="color:#000; text-decoration:underline;"><?=$j?>P</a></td>
<?php $columnas++;}?>



<td width="4%" align="center">PRO</td>
<!--<td width="5%" align="center">PER</td>-->
<td width="8%" align="center">DESEMPE&Ntilde;O</td>   
<?php if($mostrarAUS): ?><td width="5%" align="center">AUS</td><?php endif; ?>
</tr> 

    <tr class="area-row">
    	<td class="area" id="" colspan="<?=$columnas;?>" style="font-size:12px; font-weight:bold;"></td>
    </tr>
        <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->
        <?php while($fila = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)){
		
		if($periodoActual==1){
			$condicion="1";
			$condicion2="1";
			}
		if($periodoActual==2){
			$condicion="1,2";
			$condicion2="2";
		}
		if($periodoActual==3){
			$condicion="1,2,3";
			$condicion2="3";
		}
		if($periodoActual==4){
			$condicion="1,2,3,4";
			$condicion2="4";
		}
		
//CONSULTA QUE ME TRAE EL NOMBRE Y EL PROMEDIO DEL AREA
$consulta_notdef_area = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consulta_a_mat = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consulta_a_mat_per = Boletin::obtenerDefinitivaPorPeriodo($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

//CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
$consulta_a_mat_indicadores = Boletin::obtenerIndicadoresPorMateria($datosUsr["mat_grado"], $datosUsr["mat_grupo"], $fila["ar_id"], $condicion, $matriculadosDatos['mat_id'], $condicion2, $year);

$numIndicadores=mysqli_num_rows($consulta_a_mat_indicadores);

$resultado_not_area=mysqli_fetch_array($consulta_notdef_area, MYSQLI_BOTH);
$numfilas_not_area=mysqli_num_rows($consulta_notdef_area);
$total_promedio=0;
if(!empty($resultado_not_area['suma'])){
	$total_promedio = (float)$resultado_not_area["suma"];
}

// Formatear promedio del área con decimales configurados
$total_promedioFormateado = Boletin::notaDecimales($total_promedio);

if($numfilas_not_area>0){
			?>
  <?php if($mostrarAreas): ?>
  <tr class="area-row">
            <td align="left" style="font-size:12px; height:25px; font-weight:bold; padding-left: 15px;"><?php echo $resultado_not_area["ar_nombre"];?></td> 
            <?php if($mostrarIH): ?><td align="center" style="font-weight:bold; font-size:12px;"></td><?php endif; ?>
            <?php for($k=1;$k<=$numero_periodos;$k++){ 
			?>
			<td class=""  align="center" style="font-weight:bold;"></td>
            <?php }?>
        <td align="center" class="nota-destacada" style="font-weight:bold;"><?php 
		// OPTIMIZACIÓN: Usar cache de desempeños
		$notaRedondeada = number_format($total_promedio, $config['conf_decimales_notas'], '.', '');
		$desempenoNotaPromArea = isset($desempenosCache[$notaRedondeada]) 
			? $desempenosCache[$notaRedondeada] 
			: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $total_promedio, $year);
		
		if($datosUsr["mat_grado"]>11){
			echo $desempenoNotaPromArea['notip_nombre'] ?? '';
		}else{
			$totalPromedioFinal = $total_promedioFormateado;
			if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
				// OPTIMIZACIÓN: Usar cache de notas cualitativas
				$totalPromedioFinal = isset($notasCualitativasCache[$notaRedondeada]) 
					? $notasCualitativasCache[$notaRedondeada] 
					: "";
			}
			echo $totalPromedioFinal;
		}
		
		?></td>
         <td align="center" style="font-weight:bold;"></td>
          <?php if($mostrarAUS): ?><td align="center" style="font-weight:bold;"></td><?php endif; ?>
	</tr>
  <?php endif; ?>
<?php

while($fila2=mysqli_fetch_array($consulta_a_mat, MYSQLI_BOTH)){ 
	$contador_periodos=0;
	mysqli_data_seek($consulta_a_mat_per,0);
	//CONSULTAR NOTA POR PERIODO
	while($fila3=mysqli_fetch_array($consulta_a_mat_per, MYSQLI_BOTH)){
		if($fila2["mat_id"]==$fila3["mat_id"]){
			$contador_periodos++;
			$nota_periodo=round($fila3["bol_nota"],1);
			if($nota_periodo==1)$nota_periodo="1.0";	if($nota_periodo==2)$nota_periodo="2.0";	if($nota_periodo==3)$nota_periodo="3.0";	if($nota_periodo==4)$nota_periodo="4.0";	if($nota_periodo==5)$nota_periodo="5.0";
			$notas[$contador_periodos] =$nota_periodo;
		}
	}//FIN FILA3
?>
 <tr class="materia-row">
            <td align="left" style="font-size:12px; height:35px; font-weight:bold; padding-left: 15px;">&raquo; <?php echo $fila2["mat_nombre"];?></td> 
            <?php if($mostrarIH): ?><td align="center" style="font-weight:bold; font-size:12px;"><?php echo $fila["car_ih"];?></td><?php endif; ?>
<?php 
for($l=1;$l<=$numero_periodos;$l++){
	// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
	$notaDelEstudiante = $notasBoletinMapa[$fila2['car_id']][$l] ?? null;
	if($notaDelEstudiante === null){
		// Fallback: consulta individual si no está en el mapa
		$resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $l, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
		if($resTemp){
			$notaDelEstudiante = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
		} else {
			$notaDelEstudiante = ['bol_nota' => ''];
		}
	}
?>
			<td class="nota-destacada" align="center" style="font-weight:bold; font-size:16px;">
			<?php 
			if(!empty($notaDelEstudiante['bol_nota'])){
				// Formatear nota con decimales configurados
				$notaFormateada = Boletin::notaDecimales((float)$notaDelEstudiante['bol_nota']);
				
				if($datosUsr["mat_grado"]>11){
					// OPTIMIZACIÓN: Usar cache de desempeños
					$notaFRedondeada = number_format((float)$notaDelEstudiante['bol_nota'], $config['conf_decimales_notas'], '.', '');
					$desempenoNotaP = isset($desempenosCache[$notaFRedondeada]) 
						? $desempenosCache[$notaFRedondeada] 
						: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDelEstudiante['bol_nota'], $year);
					echo $desempenoNotaP['notip_nombre'] ?? '';
				}else{
					// OPTIMIZACIÓN: Usar cache de desempeños
					$notaRedondeada = number_format((float)$notaDelEstudiante['bol_nota'], $config['conf_decimales_notas'], '.', '');
					$desempenoNotaP = isset($desempenosCache[$notaRedondeada]) 
						? $desempenosCache[$notaRedondeada] 
						: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDelEstudiante['bol_nota'], $year);
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						// OPTIMIZACIÓN: Usar cache de notas cualitativas
						echo isset($notasCualitativasCache[$notaRedondeada]) 
							? $notasCualitativasCache[$notaRedondeada] 
							: ($desempenoNotaP['notip_nombre'] ?? '');
					}else{
						echo $notaFormateada."<br>".($desempenoNotaP['notip_nombre'] ?? '');
					}
				}

				if (!isset($promedios[$l])) {
					$promedios[$l] = 0;
				}
				if (!isset($contpromedios[$l])) {
					$contpromedios[$l] = 0;
				}
				if ($fila2["mat_sumar_promedio"] == SI) {
					if (isset($notaDelEstudiante['bol_nota'])) {
						$promedios[$l] += $notaDelEstudiante['bol_nota'];
					}
					$contpromedios[$l]++;
				}
			}else{
					echo "-";
			}
			?>
            </td>
        <?php
		
		
	}
	 ?>
      <?php 
	  // Formatear promedio de la materia con decimales configurados
	  $total_promedio2 = (float)$fila2["suma"];
	  $total_promedio2Formateado = Boletin::notaDecimales($total_promedio2);
	   
	    $msj='';
	   if($total_promedio2<$config[5]){
			// OPTIMIZACIÓN: Obtener nivelación del mapa pre-cargado
			$nivelaciones = $nivelacionesMapa[$fila2['mat_id']] ?? null;
			if($nivelaciones === null){
				// Fallback: consulta individual si no está en el mapa
				$consultaNivelaciones = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $fila2['mat_id'], $year);
				$nivelaciones = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);
				if($nivelaciones){
					$nivelacionesMapa[$fila2['mat_id']] = $nivelaciones;
				}
			}

			if(!empty($nivelaciones['niv_definitiva'])){
				if($nivelaciones['niv_definitiva']<$config[5]){
					$materiasPerdidas++;
				}else{
					$total_promedio2 = $nivelaciones['niv_definitiva'];
					$msj='Niv';
				}
			}
		}
	   ?>
       
        <td align="center" class="nota-destacada" style="font-weight:bold;"><?php 
		// OPTIMIZACIÓN: Usar cache de desempeños
		$notaXasigRedondeada = number_format($total_promedio2, $config['conf_decimales_notas'], '.', '');
		$desempenoNotaXasig = isset($desempenosCache[$notaXasigRedondeada]) 
			? $desempenosCache[$notaXasigRedondeada] 
			: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $total_promedio2, $year);
	
		if($datosUsr["mat_grado"]>11){
			echo $desempenoNotaXasig['notip_nombre'] ?? '';
		}else{
			$totalPromedio2Final = $total_promedio2Formateado;
			if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
				// OPTIMIZACIÓN: Usar cache de notas cualitativas
				$totalPromedio2Final = isset($notasCualitativasCache[$notaXasigRedondeada]) 
					? $notasCualitativasCache[$notaXasigRedondeada] 
					: "";
			}
			echo $totalPromedio2Final;
		}
		
		?></td>
        <td align="center" class="nota-destacada" style="font-weight:bold;"><?php //DESEMPEÑO
		// OPTIMIZACIÓN: Usar cache de desempeños
		$notaPromRedondeada = number_format($total_promedio2, $config['conf_decimales_notas'], '.', '');
		$r_desempeno = isset($desempenosCache[$notaPromRedondeada]) 
			? $desempenosCache[$notaPromRedondeada] 
			: null;
		
		if($r_desempeno){
			echo $r_desempeno["notip_nombre"] ?? '';
		}
		$matmaxaus='';
		if(!empty($fila2["matmaxaus"])){ $matmaxaus=$fila2["matmaxaus"];}
		 ?></td>
        <?php if($mostrarAUS): ?><td align="center" class="nota-destacada" style="font-weight:bold;"><?php 
		// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
		$r_ausencias = [0 => ($ausenciasMapa[$fila2['car_id']] ?? 0)];
		if(!empty($r_ausencias[0]) && $r_ausencias[0]>0){ echo $r_ausencias[0]."/".$matmaxaus;} else{ echo "0.0/".$matmaxaus;}?></td><?php endif; ?>
	
	</tr>
	
<?php
if($mostrarIndicadores && $numIndicadores>0){
	 mysqli_data_seek($consulta_a_mat_indicadores,0);
	 $contador_indicadores=0;
	while($fila4=mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)){
	if($fila4["mat_id"]==$fila2["mat_id"]){
		$contador_indicadores++;
		// Formatear nota del indicador con decimales configurados
		$nota_indicador = (float)$fila4["nota"];
		$nota_indicadorFormateada = Boletin::notaDecimales($nota_indicador);
		 
		// OPTIMIZACIÓN: Usar cache de desempeños
		$notaIndRedondeada = number_format($nota_indicador, $config['conf_decimales_notas'], '.', '');
		$desempenoNotaInd = isset($desempenosCache[$notaIndRedondeada]) 
			? $desempenosCache[$notaIndRedondeada] 
			: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota_indicador, $year);
	?>
<tr class="indicador-row">
            <td align="left" style="font-size:12px; height:15px; padding-left: 30px;"><?php echo $contador_indicadores.".".$fila4["ind_nombre"];?></td> 
            <?php if($mostrarIH): ?><td align="center" style="font-weight:bold; font-size:12px;"></td><?php endif; ?>
            <?php for($m=1;$m<=$numero_periodos;$m++){ 
			?>
			<td class="nota-destacada" align="center" style="font-weight:bold;"><?php if($periodoActual==$m){
				if($datosUsr["mat_grado"]>11){
					echo $desempenoNotaInd['notip_nombre'] ?? '';
				}else{
					$notaIndicadorFinal = $nota_indicadorFormateada;
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						// OPTIMIZACIÓN: Usar cache de notas cualitativas
						$notaIndicadorFinal = isset($notasCualitativasCache[$notaIndRedondeada]) 
							? $notasCualitativasCache[$notaIndRedondeada] 
							: "";
					}
					echo $notaIndicadorFinal;
				}
			} ?></td>
            <?php } ?>
 <td align="center" style="font-weight:bold;"></td>
        <td align="center" style="font-weight:bold;"></td>
        <?php if($mostrarAUS): ?><td align="center" style="font-weight:bold;"></td><?php endif; ?>
<?php
	}//fin if
	}
}
?>
	<!-- observaciones de la asignatura-->
	<?php
	// OPTIMIZACIÓN: Obtener observación del mapa pre-cargado
	$observacion = $notasBoletinMapa[$fila2['car_id']][$periodoActual] ?? null;
	if($observacion === null){
		// Fallback: consulta individual si no está en el mapa
		$obsTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
		if($obsTemp){
			$observacion = mysqli_fetch_array($obsTemp, MYSQLI_BOTH);
		} else {
			$observacion = [];
		}
	}
	if(!empty($observacion['bol_observaciones_boletin'])){
	?>
	<tr>
		<td colspan="7">
			<h5 align="center">Observaciones</h5>
			<p style="margin-left: 5px; font-size: 11px; margin-top: -10px; margin-bottom: 5px; font-style: italic;">
				<?=$observacion['bol_observaciones_boletin'];?>
			</p>
		</td>
	</tr>
	<?php }?>
	
<?php	
}//while fin materias
?>  
<?php }}//while fin areas?>
	 

          

            

    <tr class="promedio-row" align="center" style="font-size:12px; font-weight:bold;">
        <td colspan="<?= ($mostrarIH ? 2 : 1) ?>" align="right">PROMEDIO</td>

		<?php for($n=1;$n<=$numero_periodos;$n++){ 
		$notaFFF = 0;
		if(!empty($contpromedios[$n])){
			$notaFFF = ($promedios[$n]/$contpromedios[$n]);
		}
		
		// Formatear promedio con decimales configurados
		$notaFFFFormateada = Boletin::notaDecimales($notaFFF);
		
		// OPTIMIZACIÓN: Usar cache de desempeños
		$notaFFFRedondeada = number_format($notaFFF, $config['conf_decimales_notas'], '.', '');
		$desempenoNotaProm = isset($desempenosCache[$notaFFFRedondeada]) 
			? $desempenosCache[$notaFFFRedondeada] 
			: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaFFF, $year);
		?>
        <td class="nota-destacada" style="font-size:16px;">
        	<?php 
		if($promedios[$n]!=0){
			if($datosUsr["mat_grado"]>11){
				echo $desempenoNotaProm['notip_nombre'] ?? '';
			}else{
				$promedioTotalFinal = $notaFFFFormateada;
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					// OPTIMIZACIÓN: Usar cache de notas cualitativas
					$promedioTotalFinal = isset($notasCualitativasCache[$notaFFFRedondeada]) 
						? $notasCualitativasCache[$notaFFFRedondeada] 
						: "";
				}
				echo $promedioTotalFinal;
			}
		}?></td>
        <?php } ?>
        <td></td>
        <td>&nbsp;</td>
        <?php if($mostrarAUS): ?><td>&nbsp;</td><?php endif; ?>
    </tr>
    
</table>

<?php for($n=1;$n<=$numero_periodos;$n++){if($promedios[$n]!=0){$promedios[$n]=0; $contpromedios[$n]=0;} } ?>

<p>&nbsp;</p>
<?php 
// OPTIMIZACIÓN: Usar prepared statements para la consulta de disciplina
$idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
$condicionEsc = mysqli_real_escape_string($conexion, $condicion);
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$idEstudianteEsc."' AND institucion=".(int)$config['conf_id_institucion']." AND year=".(int)$year." AND dn_periodo IN(".$condicionEsc.")");
if(mysqli_num_rows($cndisiplina)>0){
?>
<table width="100%" id="tblBoletin" class="tabla-comportamiento" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

    <tr>
    	<th colspan="3" style="text-align:center">NOTA DE COMPORTAMIENTO</th>
    </tr>
    
    <tr>
        <th width="8%">Periodo</th>
        <th>Observaciones</th>
    </tr>
<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
// $consultaDesempenoND=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$rndisiplina["dn_nota"].">=notip_desde AND ".$rndisiplina["dn_nota"]."<=notip_hasta AND institucion={$config['conf_id_institucion']} AND year={$year}");
// $desempenoND = mysqli_fetch_array($consultaDesempenoND, MYSQLI_BOTH);
?>
    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
        <td style="text-align:center;"><?=$rndisiplina["dn_periodo"]?></td>
        <td align="left" style="padding-left: 15px;"><?=$rndisiplina["dn_observacion"]?></td>
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

<?php if($mostrarFirmas): ?>
<p>&nbsp;</p>
<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
	<tr>
		<td align="center">_________________________________<br><!--<?=strtoupper("");?><br>-->Rector(a)</td>
		<td align="center">_________________________________<br><!--<?=strtoupper("");?><br>-->Director(a) de grupo</td>
    </tr>
</table> 
<?php endif; ?>

<?php if($mostrarLogoPlataforma): ?>
<?php include("../compartido/footer-informes.php") ?>
<?php endif; ?>

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
if($periodoActual==4){
	if($materiasPerdidas>=$config["conf_num_materias_perder_agno"])
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_primer_apellido']." ".$datosUsr['mat_segundo_apellido']." ".$datosUsr["mat_nombres"])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<$config["conf_num_materias_perder_agno"] and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_primer_apellido']." ".$datosUsr['mat_segundo_apellido']." ".$datosUsr["mat_nombres"])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_primer_apellido']." ".$datosUsr['mat_segundo_apellido']." ".$datosUsr["mat_nombres"])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
}
?>
				                   
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
