<?php 
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/UsuariosPadre.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
require_once(ROOT_PATH."/main-app/class/Clases.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");

$year=$_SESSION["bd"];
if(!empty($_GET["year"])){
$year=base64_decode($_GET["year"]);
}

$modulo = 1;
if(empty($_GET["periodo"])){
	$periodoActual = 1;
}else{
	$periodoActual = base64_decode($_GET["periodo"]);
}

if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(!empty($_GET["id"])){$filtro .= " AND mat_id='".base64_decode($_GET["id"])."'";}
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".base64_decode($_REQUEST["curso"])."'";}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro,$year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
	//contadores
	$contador_periodos = 0;
	$contador_indicadores = 0;
	$materiasPerdidas = 0;
	if($matriculadosDatos['mat_id']==""){?>
		<script type="text/javascript">window.close();</script>
	<?php
		exit();
	}
// Configuración de visualización del puesto del curso
$formularioEnviado = isset($_GET['config_aplicada']) && $_GET['config_aplicada'] == '1';
$mostrarPuestoCurso = $formularioEnviado 
    ? (isset($_GET['mostrar_puesto_curso']) ? (int)$_GET['mostrar_puesto_curso'] : 0)
    : 1; // Por defecto visible

$contp = 1;
$puestoCurso = 0;
$numMatriculados = 0;
if($mostrarPuestoCurso){
    $puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual,$matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
    $numMatriculados = count($puestos);
    
    foreach($puestos as $puesto){
        if($puesto['estudiante_id']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
        $contp ++;
    }
}
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
	<title>Boletín Formato 7</title>
	<link rel="shortcut icon" href="../sintia-icono.png" />
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<style>
    	#saltoPagina{PAGE-BREAK-AFTER: always;}
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>

<body style="font-family:Arial; font-size:9px;">

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
            <input type="checkbox" name="mostrar_puesto_curso" value="1" <?= $mostrarPuestoCurso ? 'checked' : '' ?>>
            Mostrar puesto del curso
        </label>
        <button type="submit" style="background: #34495e; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Aplicar Configuración</button>
    </form>
</div>
<style>
    @media print {
        .config-boletin-form { display: none !important; }
    }
</style>

<div>
    <div style="float:left; width:50%"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="80"></div>
    <div style="float:right; width:50%">
        <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
            <tr>
                <td>C&oacute;digo:<br> <?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>
                <td>Nombre:<br> <?=$nombre?></td>
                <td>Grado:<br> <?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></td>
                <td>Puesto Curso:<br> <?php if($mostrarPuestoCurso): ?><?=$puestoCurso." de ".$numMatriculados;?><?php else: ?>&nbsp;<?php endif; ?></td>   
            </tr>
            
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?=$informacion_inst["info_nombre"]?></td>
                <td>Periodo:<br> <b><?=$periodoActual." (".$year.")";?></b></td>
                <td>Fecha Impresión:<br> <?=date("d/m/Y H:i:s");?></td>
            </tr>
        </table>
        <p>&nbsp;</p>
    </div>
</div>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center;">
            <td width="20%" rowspan="2">ASIGNATURAS</td>
            <td width="2%" rowspan="2">I.H.</td>
            
            <?php  for($j=1;$j<=$periodoActual;$j++){ ?>
                <td width="3%" colspan="3"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$datosUsr['mat_id'];?>&periodo=<?=$j?>" style="color:#000; text-decoration:none;">Periodo <?=$j?></a></td>
            <?php }?>
            <td width="3%" colspan="3">Final</td>
        </tr> 
        
        <tr style="font-weight:bold; text-align:center;">
            <?php  for($j=1;$j<=$periodoActual;$j++){ ?>
                <td width="3%">Fallas</td>
                <td width="3%">Nota</td>
                <td width="3%">Nivel</td>
            <?php }?>
            <td width="3%">Nota</td>
            <td width="3%">Nivel</td>
            <td width="3%">Hab</td>
        </tr>
        
    </thead>
    
    <?php
	$contador=1;
    $conCargas = CargaAcademica::traerCargasMateriasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $year);
	
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
			$datosUsr['mat_id'],
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
	
	// OPTIMIZACIÓN 2: Pre-cargar todas las ausencias para este estudiante y todos los períodos
	$ausenciasMapa = []; // [carga][periodo] => suma_ausencias
	try {
		// Obtener todas las cargas del estudiante
		$idsCargas = [];
		mysqli_data_seek($conCargas, 0);
		while ($filaTemp = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
			if (!in_array($filaTemp['car_id'], $idsCargas)) {
				$idsCargas[] = $filaTemp['car_id'];
			}
		}
		mysqli_data_seek($conCargas, 0);
		
		if (!empty($idsCargas)) {
			$idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
			$inCargas = implode(',', array_map('intval', $idsCargas));
			$institucion = (int)$config['conf_id_institucion'];
			$yearEsc = mysqli_real_escape_string($conexion, $year);
			$periodoEsc = (int)$periodoActual;
			
			$sqlAusencias = "SELECT cls.cls_id_carga, cls.cls_periodo, SUM(aus.aus_ausencias) as total_ausencias
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
							 GROUP BY cls.cls_id_carga, cls.cls_periodo";
			
			$consultaAusencias = mysqli_query($conexion, $sqlAusencias);
			if($consultaAusencias){
				while($rowAus = mysqli_fetch_array($consultaAusencias, MYSQLI_BOTH)){
					$idCarga = $rowAus['cls_id_carga'];
					$per = (int)$rowAus['cls_periodo'];
					if (!isset($ausenciasMapa[$idCarga])) {
						$ausenciasMapa[$idCarga] = [];
					}
					$ausenciasMapa[$idCarga][$per] = (float)$rowAus['total_ausencias'];
				}
			}
		}
	} catch (Exception $eAus) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 3: Pre-cargar todas las nivelaciones para este estudiante
	$nivelacionesMapa = []; // [car_id] => datos_nivelacion
	try {
		mysqli_data_seek($conCargas, 0);
		while ($filaTemp = mysqli_fetch_array($conCargas, MYSQLI_BOTH)) {
			$idCarga = $filaTemp['car_id'];
			if (!isset($nivelacionesMapa[$idCarga])) {
				$nivTemp = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $datosUsr['mat_id'], $idCarga, $year);
				$niv = mysqli_fetch_array($nivTemp, MYSQLI_BOTH);
				if ($niv) {
					$nivelacionesMapa[$idCarga] = $niv;
				}
			}
		}
		mysqli_data_seek($conCargas, 0);
	} catch (Exception $eNiv) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 4: Pre-cargar promedios por período y ausencias totales
	$promediosPeriodosMapa = []; // [periodo] => promedio
	$ausenciasTotalesMapa = []; // [periodo] => suma_ausencias
	try {
		$idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
		$gradoEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_grado']);
		$grupoEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_grupo']);
		$institucion = (int)$config['conf_id_institucion'];
		$yearEsc = mysqli_real_escape_string($conexion, $year);
		
		for($j=1; $j<=$periodoActual; $j++){
			// Promedio por período
			$sqlPromedio = "SELECT ROUND(AVG(bol_nota),2) as promedio 
							FROM ".BD_ACADEMICA.".academico_boletin bol
							INNER JOIN ".BD_ACADEMICA.".academico_cargas car 
								ON car_id=bol_carga 
								AND car_curso='{$gradoEsc}' 
								AND car_grupo='{$grupoEsc}' 
								AND car.institucion={$institucion} 
								AND car.year='{$yearEsc}'
							INNER JOIN ".BD_ACADEMICA.".academico_materias mat 
								ON mat_id=car_materia 
								AND mat_sumar_promedio='SI' 
								AND mat.institucion={$institucion} 
								AND mat.year='{$yearEsc}'
							WHERE bol_estudiante='{$idEstudianteEsc}' 
								AND bol_periodo='{$j}' 
								AND bol.institucion={$institucion} 
								AND bol.year='{$yearEsc}'";
			$consultaPromedio = mysqli_query($conexion, $sqlPromedio);
			if($consultaPromedio){
				$rowProm = mysqli_fetch_array($consultaPromedio, MYSQLI_BOTH);
				$promediosPeriodosMapa[$j] = !empty($rowProm['promedio']) ? (float)$rowProm['promedio'] : 0;
			}
			
			// Ausencias totales por período
			$sqlAusTotal = "SELECT sum(aus_ausencias) as total 
							FROM ".BD_ACADEMICA.".academico_clases cls 
							INNER JOIN ".BD_ACADEMICA.".academico_ausencias aus 
								ON aus.aus_id_clase=cls.cls_id 
								AND aus.aus_id_estudiante='{$idEstudianteEsc}' 
								AND aus.institucion={$institucion} 
								AND aus.year='{$yearEsc}'
							WHERE cls.cls_periodo='{$j}' 
								AND cls.institucion={$institucion} 
								AND cls.year='{$yearEsc}'";
			$consultaAusTotal = mysqli_query($conexion, $sqlAusTotal);
			if($consultaAusTotal){
				$rowAusTotal = mysqli_fetch_array($consultaAusTotal, MYSQLI_BOTH);
				$ausenciasTotalesMapa[$j] = !empty($rowAusTotal['total']) ? (float)$rowAusTotal['total'] : 0;
			}
		}
	} catch (Exception $eProm) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 5: Pre-cargar cache de notas cualitativas
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
	
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
		if($contador%2==1){$fondoFila = '#EAEAEA';}else{$fondoFila = '#FFF';}
	?>
    <tbody>
        <tr style="background:<?=$fondoFila;?>">
            <td><?=$datosCargas['mat_nombre'];?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td> 
            <?php 
			$promedioMateria = 0;
			for($j=1;$j<=$periodoActual;$j++){
				// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
				$datosBoletin = $notasBoletinMapa[$datosCargas['car_id']][$j] ?? null;
				if($datosBoletin === null){
					// Fallback: consulta individual si no está en el mapa
					$resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $j, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
					if($resTemp){
						$datosBoletin = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
					} else {
						$datosBoletin = ['bol_nota' => 0];
					}
				}
		
				// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
				$datosAusencias = [0 => ($ausenciasMapa[$datosCargas['car_id']][$j] ?? 0)];
				
				$notaPeriodo = !empty($datosBoletin['bol_nota']) ? (float)$datosBoletin['bol_nota'] : 0;
				$promedioMateria += $notaPeriodo;
				
				// OPTIMIZACIÓN: Calcular desempeño usando cache de notas cualitativas
				$desempenoNotaPeriodo = '';
				if($notaPeriodo > 0){
					$notaRedondeada = number_format($notaPeriodo, $config['conf_decimales_notas'], '.', '');
					$desempenoNotaPeriodo = isset($notasCualitativasCache[$notaRedondeada]) 
						? $notasCualitativasCache[$notaRedondeada] 
						: '';
					if(empty($desempenoNotaPeriodo)){
						$estiloNotaPeriodo = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaPeriodo, $year);
						$desempenoNotaPeriodo = !empty($estiloNotaPeriodo['notip_nombre']) ? $estiloNotaPeriodo['notip_nombre'] : '';
					}
				}
            ?>
                <td align="center"><?php 
                if (!empty($datosAusencias[0]) && $datosAusencias[0]>0) {
                    echo round($datosAusencias[0],0);
                } 
                ?></td>
                <td align="center"><?=$notaPeriodo > 0 ? Boletin::notaDecimales($notaPeriodo) : '';?></td>
                <td align="center"><?=$desempenoNotaPeriodo;?></td>
            <?php 
			}
			$promedioMateria = ($j-1) > 0 ? ($promedioMateria/($j-1)) : 0;
			$promedioMateriaFinal = $promedioMateria;
			// OPTIMIZACIÓN: Obtener nivelación del mapa pre-cargado
			$nivelacion = $nivelacionesMapa[$datosCargas['car_id']] ?? null;
			if($nivelacion === null){
				// Fallback: consulta individual si no está en el mapa
				$consultaNivelacion = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
				$nivelacion = mysqli_fetch_array($consultaNivelacion, MYSQLI_BOTH);
				if($nivelacion){
					$nivelacionesMapa[$datosCargas['car_id']] = $nivelacion;
				}
			}
			
			// SI PERDIÓ LA MATERIA A FIN DE AÑO
			if($promedioMateria<$config["conf_nota_minima_aprobar"]){
				if(!empty($nivelacion['niv_definitiva']) && $nivelacion['niv_definitiva']>=$config["conf_nota_minima_aprobar"]){
					$promedioMateriaFinal = $nivelacion['niv_definitiva'];
				}else{
					$materiasPerdidas++;
				}	
			}

			// Formatear promedio de la materia con decimales configurados
			$promedioMateriaFinalFormateado = Boletin::notaDecimales($promedioMateriaFinal);
			
			// OPTIMIZACIÓN: Usar cache de notas cualitativas
			$notaMateriaRedondeada = number_format($promedioMateriaFinal, $config['conf_decimales_notas'], '.', '');
			$promediosMateriaEstiloNota = isset($notasCualitativasCache[$notaMateriaRedondeada]) 
				? ['notip_nombre' => $notasCualitativasCache[$notaMateriaRedondeada]] 
				: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateriaFinal, $year);
			if($promediosMateriaEstiloNota === null){
				$promediosMateriaEstiloNota = ['notip_nombre' => ''];
			}
			?>
            <td align="center"><?=$promedioMateriaFinalFormateado;?></td>
            <td align="center"><?=!empty($promediosMateriaEstiloNota['notip_nombre']) ? $promediosMateriaEstiloNota['notip_nombre'] : '';?></td>
            <td align="center">&nbsp;</td>
        </tr>
    </tbody>
    <?php 
		$contador++;
	}
	?>
    <tfoot>
    	<tr style="font-weight:bold; text-align:center;">
        	<td style="text-align:left;">PROMEDIO/TOTAL</td>
            <td>-</td> 
            <?php 
            $promedioFinal = 0;
            for($j=1;$j<=$periodoActual;$j++){
				// OPTIMIZACIÓN: Obtener promedio del mapa pre-cargado
				$promedioPeriodo = $promediosPeriodosMapa[$j] ?? 0;
				$promedioPeriodoFormateado = Boletin::notaDecimales($promedioPeriodo);
				
				// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
				$sumaAusencias = [0 => ($ausenciasTotalesMapa[$j] ?? 0)];
				
				// OPTIMIZACIÓN: Usar cache de notas cualitativas
				$promedioRedondeado = number_format($promedioPeriodo, $config['conf_decimales_notas'], '.', '');
				$promediosEstiloNota = isset($notasCualitativasCache[$promedioRedondeado]) 
					? ['notip_nombre' => $notasCualitativasCache[$promedioRedondeado]] 
					: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioPeriodo, $year);
				if($promediosEstiloNota === null){
					$promediosEstiloNota = ['notip_nombre' => ''];
				}
            ?>
                <td><?php //echo $sumaAusencias[0];?></td>
                <td><?=$promedioPeriodoFormateado;?></td>
                <td><?=!empty($promediosEstiloNota['notip_nombre']) ? $promediosEstiloNota['notip_nombre'] : '';?></td>
            <?php 
                $promedioFinal += $promedioPeriodo;
            }

            $promedioFinal = $periodoActual > 0 ? ($promedioFinal/$periodoActual) : 0;
			// Formatear promedio final con decimales configurados
			$promedioFinalFormateado = Boletin::notaDecimales($promedioFinal);
			
			// OPTIMIZACIÓN: Usar cache de notas cualitativas
			$promedioFinalRedondeado = number_format($promedioFinal, $config['conf_decimales_notas'], '.', '');
			$promedioFinalEstiloNota = isset($notasCualitativasCache[$promedioFinalRedondeado]) 
				? ['notip_nombre' => $notasCualitativasCache[$promedioFinalRedondeado]] 
				: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioFinal, $year);
			if($promedioFinalEstiloNota === null){
				$promedioFinalEstiloNota = ['notip_nombre' => ''];
			}
            ?>
            <td><?=$promedioFinalFormateado;?></td>
            <td><?=!empty($promedioFinalEstiloNota['notip_nombre']) ? $promedioFinalEstiloNota['notip_nombre'] : '';?></td>
            <td>-</td>
        </tr>
    </tfoot>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>	
<p>&nbsp;</p>
<p>&nbsp;</p>	

<table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
	<tr>
        <td width="40%">
            ________________________________________________________________<br>
            <?php if(!empty($datosUsr['uss_nombre'])) echo strtoupper($datosUsr['uss_nombre']);?><br>
            DIRECTOR DE CURSO
        </td>
        <td width="20%">
        	<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
            	<?php
				$contador=1;
                $estilosNota = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);
				while($eN = mysqli_fetch_array($estilosNota, MYSQLI_BOTH)){
					if($contador%2==1){$fondoFila = '#EAEAEA';}else{$fondoFila = '#FFF';}
				?>
                <tr style="background:<?=$fondoFila;?>">
                	<td><?=$eN['notip_nombre'];?></td>
                    <td align="center"><?=$eN['notip_desde']." - ".$eN['notip_hasta'];?></td>
                </tr>
                <?php $contador++;}?>
            </table>
        </td>
		
		<?php
		$msjPromocion = '';
		if($periodoActual==$config['conf_periodos_maximos']){
			if($materiasPerdidas==0){$msjPromocion = 'PROMOVIDO';}
			else{$msjPromocion = 'NO PROMOVIDO';}	
		}
		
		?>
        <td width="60%">
        	<p style="font-weight:bold;">Observaciones: <b><?=$msjPromocion;?></b></p>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________
        </td>
    </tr>
</table>

<?php
// Solo mostrar la segunda hoja si hay datos para mostrar
$conCargas = CargaAcademica::traerIndicadoresCargasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $periodoActual, $year);
$numIndicadores = mysqli_num_rows($conCargas);

if($numIndicadores > 0){
	// Solo agregar salto de página si hay contenido para mostrar
?>
<div id="saltoPagina"></div>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center;">
            <td width="30%">Asignaturas</td>
            <td width="70%">Contenidos Evaluados</td>
        </tr>     
    </thead>
    
    <?php
	while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
	?>
    <tbody>
        <tr style="color:#585858;">
            <td><?=$datosCargas['mat_nombre'];?><br>
            <span style="color:#C1C1C1;"><?=UsuariosPadre::nombreCompletoDelUsuario($datosCargas);?></span></td>
            <td><?=$datosCargas['ind_nombre'];?></td> 
        </tr>
    </tbody>
    <?php 
	}
	?>
</table>
<?php 
}
?>

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