<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Usuarios.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");

$year=$_SESSION["bd"];
if(isset($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

// Configuración de visualización y tamaño del logo
$formularioEnviado = isset($_GET['config_aplicada']) && $_GET['config_aplicada'] == '1';

$mostrarLogoEncabezado = $formularioEnviado 
    ? (isset($_GET['mostrar_logo_encabezado']) ? (int)$_GET['mostrar_logo_encabezado'] : 0)
    : 1; // Por defecto visible
$mostrarFirmas = $formularioEnviado 
    ? (isset($_GET['mostrar_firmas']) ? (int)$_GET['mostrar_firmas'] : 0)
    : 1; // Por defecto visible

// Tamaño del logo (ancho en % y alto en px, 0 = auto)
$logoAncho = $formularioEnviado && isset($_GET['logo_ancho']) && is_numeric($_GET['logo_ancho'])
    ? (int)$_GET['logo_ancho']
    : ($_SESSION['idInstitucion'] == ICOLVEN ? 100 : 50); // Por defecto según institución
$logoAlto = $formularioEnviado && isset($_GET['logo_alto']) && is_numeric($_GET['logo_alto'])
    ? (int)$_GET['logo_alto']
    : 0; // Por defecto 0 (auto) - mantiene proporción si solo se especifica ancho

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
$filtro = '';
if (!empty($_GET["id"])) {
    $filtro .= " AND mat_id='" . base64_decode($_GET["id"]) . "'";
}
if (!empty($_REQUEST["curso"])) {
    $filtro .= " AND mat_grado='" . base64_decode($_REQUEST["curso"]) . "'";
}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$cont_periodos=0;
$contador_indicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);
$num_usr=mysqli_num_rows($usr);
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
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<title>Boletín Formato 9</title>
	<link rel="shortcut icon" href="../sintia-icono.png" />
<style>
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}

/* Estilos profesionales para el boletín */
body {
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	color: #333;
	background: #ffffff;
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
	padding: 10px;
	text-align: left;
	font-size: 11px;
}

.tabla-boletin thead th {
	background: #34495e;
	color: #FFF;
	font-weight: bold;
	height: 30px;
	font-size: 12px;
	letter-spacing: 0.3px;
	text-align: center;
}

.fila-par {
	background: #ffffff;
}

.fila-impar {
	background: #f8f9fa;
}

.dimension-nombre {
	font-weight: 600;
	font-size: 12px;
	color: #2c3e50;
	margin-bottom: 8px;
}

.indicador-item {
	font-size: 11px;
	color: #495057;
	margin: 4px 0;
	padding-left: 15px;
}

.observaciones-titulo {
	font-weight: 600;
	font-size: 11px;
	color: #34495e;
	text-align: center;
	margin: 10px 0 5px 0;
	letter-spacing: 0.3px;
}

.observaciones-texto {
	font-size: 10px;
	color: #6c757d;
	font-style: italic;
	margin: 5px 10px;
	line-height: 1.4;
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

.tabla-convivencia {
	border: 1px solid #dee2e6;
	border-collapse: collapse;
	width: 100%;
	margin-top: 15px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.tabla-convivencia thead th {
	background: #34495e;
	color: #FFF;
	font-weight: bold;
	padding: 12px;
	font-size: 12px;
	letter-spacing: 0.3px;
}

.tabla-convivencia tbody td {
	border: 1px solid #dee2e6;
	padding: 10px;
	font-size: 11px;
}

.firmas-container {
	margin-top: 30px;
	padding: 20px 0;
	border-top: 2px solid #dee2e6;
}

.firma-nombre {
	font-weight: 600;
	font-size: 11px;
	color: #2c3e50;
	margin-top: 5px;
}

.firma-cargo {
	font-size: 10px;
	color: #6c757d;
	margin-top: 3px;
}
</style>
</head>

<body>

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
            Mostrar logo del encabezado
        </label>
        <div style="margin: 15px 0;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tamaño del Logo:</label>
            <label style="display: block; margin-bottom: 5px;">
                Ancho (%): <input type="number" name="logo_ancho" value="<?= $logoAncho ?>" min="1" max="100" style="width: 60px;">
            </label>
            <label style="display: block; margin-bottom: 5px;">
                Alto (px, 0=auto): <input type="number" name="logo_alto" value="<?= $logoAlto ?>" min="0" style="width: 60px;">
            </label>
        </div>
        <label style="display: block; margin-bottom: 10px;">
            <input type="checkbox" name="mostrar_firmas" value="1" <?= $mostrarFirmas ? 'checked' : '' ?>>
            Mostrar firmas del pie de página
        </label>
        <button type="submit" style="background: #34495e; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Aplicar Configuración</button>
    </form>
</div>
<style>
    @media print {
        .config-boletin-form { display: none !important; }
    }
</style>

<?php if($mostrarLogoEncabezado): ?>
<div align="center" style="margin-bottom:25px;">
	<img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="<?=$logoAncho?>%" <?= $logoAlto > 0 ? 'height="'.$logoAlto.'"' : '' ?>><br>
</div>
<?php endif; ?> 

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" class="info-header" style="font-size:12px; margin-bottom: 20px;">
    <tr>
    	<td style="padding: 12px 15px;">C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td style="padding: 12px 15px;">Nombre: <b><?=$nombre?></b></td>   
    </tr>
    
    <tr class="info-content">
    	<td style="padding: 10px 15px;">Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
        <td style="padding: 10px 15px;">Periodo: <b><?=strtoupper($periodoActuales);?></b></td>    
    </tr>
</table>
<table width="100%" class="tabla-boletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
	<tr class="header-boletin">
		<td width="5%" align="center">No.</td>
		<td width="90%" align="center">DIMENSIONES</td>
		<td width="5%" align="center">I.H</td>
	</tr>
	
	<?php
	$cargasConsulta = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
	
	// ============================================
	// OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
	// ============================================
	
	// OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y periodo
	$notasBoletinMapa = []; // [carga] => datos_nota
	try {
		$sqlNotas = "SELECT bol_carga, bol_observaciones_boletin
					 FROM " . BD_ACADEMICA . ".academico_boletin
					 WHERE bol_estudiante = ?
					   AND institucion = ?
					   AND year = ?
					   AND bol_periodo = ?";
		$paramNotas = [
			$datosUsr['mat_id'],
			$config['conf_id_institucion'],
			$year,
			$periodoActual
		];
		$resNotas = BindSQL::prepararSQL($sqlNotas, $paramNotas);
		while ($rowNota = mysqli_fetch_array($resNotas, MYSQLI_BOTH)) {
			$idCarga = $rowNota['bol_carga'];
			$notasBoletinMapa[$idCarga] = $rowNota;
		}
	} catch (Exception $eNotas) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 2: Pre-cargar todos los indicadores para todas las cargas
	$indicadoresMapa = []; // [car_id] => array de indicadores
	try {
		mysqli_data_seek($cargasConsulta, 0);
		while ($filaTemp = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)) {
			$idCarga = $filaTemp['car_id'];
			if (!isset($indicadoresMapa[$idCarga])) {
				$indicadoresMapa[$idCarga] = [];
				$indicadoresTemp = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $idCarga, $periodoActual, $year);
				while ($indTemp = mysqli_fetch_array($indicadoresTemp, MYSQLI_BOTH)) {
					$indicadoresMapa[$idCarga][] = $indTemp;
				}
			}
		}
		mysqli_data_seek($cargasConsulta, 0);
	} catch (Exception $eInd) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 3: Cachear datos de usuarios (director y rector)
	$directorGrupo = null;
	$rector = null;
	$idDirector = null; // Se establecerá en el bucle de cargas
	
	$i=1;
	while($cargas = mysqli_fetch_array($cargasConsulta, MYSQLI_BOTH)){
		//DIRECTOR DE GRUPO
		if($cargas["car_director_grupo"]==1){
			$idDirector=$cargas["car_docente"];
			// OPTIMIZACIÓN: Cargar director solo una vez
			if($directorGrupo === null && !empty($idDirector)){
				$directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
			}
		}
		// OPTIMIZACIÓN: Obtener indicadores del mapa pre-cargado
		$indicadores = $indicadoresMapa[$cargas['car_id']] ?? [];
		
		// OPTIMIZACIÓN: Obtener observación del mapa pre-cargado
		$observacion = $notasBoletinMapa[$cargas['car_id']] ?? null;
		if($observacion === null){
			// Fallback: consulta individual si no está en el mapa
			$obsTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $datosUsr['mat_id'], $cargas['car_id'], $year);
			if($obsTemp){
				$observacion = mysqli_fetch_array($obsTemp, MYSQLI_BOTH);
			} else {
				$observacion = ['bol_observaciones_boletin' => ''];
			}
		}
		
		$claseFila = ($i % 2 == 0) ? 'fila-par' : 'fila-impar';
	?>
	<tr class="<?=$claseFila;?>">
		<td width="5%" align="center" style="font-weight: 600; color: #495057;"><?=$i;?></td>
		<td width="90%">
			<div class="dimension-nombre"><?=$cargas['mat_nombre'];?></div>
			<?php
			// OPTIMIZACIÓN: Usar array pre-cargado en lugar de mysqli_result
			if(!empty($indicadores)){
				foreach($indicadores as $ind){
					echo '<div class="indicador-item">• '.$ind['ind_nombre'].'</div>';
				}
			}
			?>
			<hr style="border: none; border-top: 1px solid #dee2e6; margin: 10px 0;">
			<div class="observaciones-titulo">OBSERVACIONES</div>
			<div class="observaciones-texto">
				<?=!empty($observacion['bol_observaciones_boletin']) ? htmlspecialchars($observacion['bol_observaciones_boletin']) : "Sin observaciones";?>
			</div>
		</td>
		<td width="5%" align="center" style="font-weight: 600; color: #495057;"><?=$cargas['car_ih'];?></td>
	</tr>
	<?php $i++;}?>
</table>
	<p>&nbsp;</p>
<?php 
// OPTIMIZACIÓN: Usar prepared statements para la consulta de disciplina
$idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
$periodoEsc = (int)$periodoActual;
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota 
WHERE dn_cod_estudiante='{$idEstudianteEsc}' AND dn_periodo<={$periodoEsc} AND institucion=".(int)$config['conf_id_institucion']." AND year=".(int)$year."
GROUP BY dn_cod_estudiante, dn_periodo
ORDER BY dn_id
");
if(@mysqli_num_rows($cndisiplina)>0){
?>
<table width="100%" id="tblBoletin" class="tabla-convivencia" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <thead>
        <tr>
            <th colspan="2">OBSERVACIONES DE CONVIVENCIA</th>
        </tr>
        <tr>
            <th width="15%" style="text-align:center;">Periodo</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
?>
        <tr>
            <td align="center" style="font-weight: 600; color: #495057;"><?=$rndisiplina["dn_periodo"]?></td>
            <td align="left" style="padding-left: 15px; color: #6c757d;"><?=htmlspecialchars($rndisiplina["dn_observacion"])?></td>
        </tr>
<?php }?>
    </tbody>
</table>
<?php }?>

<?php if($mostrarFirmas): ?>
<div class="firmas-container">
	<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center;">
		<tr>
			<td width="50%" align="center" style="padding: 20px;">
				<?php
					// OPTIMIZACIÓN: Usar director cacheado (ya se cargó arriba)
					if($directorGrupo === null && !empty($idDirector)){
						$directorGrupo = Usuarios::obtenerDatosUsuario($idDirector);
					}
					if($directorGrupo){
						$nombreDirectorGrupo = UsuariosPadre::nombreCompletoDelUsuario($directorGrupo);
						if(!empty($directorGrupo["uss_firma"])){
							echo '<img src="../files/fotos/'.$directorGrupo["uss_firma"].'" width="120" style="margin-bottom: 10px;"><br>';
						}else{
							echo '<div style="height: 80px; margin-bottom: 10px;"></div>';
						}
						echo '<div style="border-top: 2px solid #2c3e50; width: 200px; margin: 0 auto; padding-top: 5px;"></div>
							<div class="firma-nombre">'.htmlspecialchars($nombreDirectorGrupo).'</div>
							<div class="firma-cargo">Director(a) de grupo</div>';
					}else{
						echo '<div style="height: 80px; margin-bottom: 10px;"></div>
							<div style="border-top: 2px solid #2c3e50; width: 200px; margin: 0 auto; padding-top: 5px;"></div>
							<div class="firma-nombre"></div>
							<div class="firma-cargo">Director(a) de grupo</div>';
					}
				?>
			</td>
			<td width="50%" align="center" style="padding: 20px;">
				<?php
					// OPTIMIZACIÓN: Cargar rector solo una vez
					if($rector === null && !empty($informacion_inst["info_rector"])){
						$rector = Usuarios::obtenerDatosUsuario($informacion_inst["info_rector"]);
					}
					if($rector){
						$nombreRector = UsuariosPadre::nombreCompletoDelUsuario($rector);
						if(!empty($rector["uss_firma"])){
							echo '<img src="../files/fotos/'.$rector["uss_firma"].'" width="120" style="margin-bottom: 10px;"><br>';
						}else{
							echo '<div style="height: 80px; margin-bottom: 10px;"></div>';
						}
						echo '<div style="border-top: 2px solid #2c3e50; width: 200px; margin: 0 auto; padding-top: 5px;"></div>
							<div class="firma-nombre">'.htmlspecialchars($nombreRector).'</div>
							<div class="firma-cargo">Rector(a)</div>';
					}else{
						echo '<div style="height: 80px; margin-bottom: 10px;"></div>
							<div style="border-top: 2px solid #2c3e50; width: 200px; margin: 0 auto; padding-top: 5px;"></div>
							<div class="firma-nombre"></div>
							<div class="firma-cargo">Rector(a)</div>';
					}
				?>
			</td>
		</tr>
	</table>
</div>
<?php endif; ?>
		
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
