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
require_once(ROOT_PATH."/main-app/class/Indicadores.php");
require_once(ROOT_PATH."/main-app/class/Grados.php");
require_once(ROOT_PATH."/main-app/class/CargaAcademica.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");

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

if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";
//CONSULTA ESTUDIANTES MATRICULADOS
$filtro = '';
if(!empty($_GET["id"])){$filtro .= " AND mat_id='".base64_decode($_GET["id"])."'";}
if(!empty($_REQUEST["curso"])){$filtro .= " AND mat_grado='".base64_decode($_REQUEST["curso"])."'";}
if(!empty($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".base64_decode($_REQUEST["grupo"])."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
Utilidades::validarInfoBoletin($matriculadosPorCurso);
$numMatriculados = mysqli_num_rows($matriculadosPorCurso);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
	//contadores
	$contador_periodos = 0;
	$contador_indicadores = 0;
	$materiasPerdidas = 0;
	if($matriculadosDatos['mat_id']==""){?>
		<script type="text/javascript">window.close();</script>
	<?php
		//exit();
	}
$contp = 1;
$puestoCurso = 0;	
$puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual,$matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);
foreach($puestos as $puesto){
	if($puesto['estudiante_id']==$matriculadosDatos['mat_id']){$puestoCurso = $contp;}
	$contp ++;
}
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos['mat_id'],$year);
$datosUsr = mysqli_fetch_array($usr, MYSQLI_BOTH);
$nombre = Estudiantes::NombreCompletoDelEstudiante($datosUsr);
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
	<style>
    	#saltoPagina{PAGE-BREAK-AFTER: always;}
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
</head>

<body style="font-family:Arial; font-size:9px;">

<div>
	
	<!--<div align="center" style="margin-bottom: 10px;"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" width="350"></div>-->
	
	<div align="center" style="margin-bottom: 10px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="200"><br>
    <!-- <?=$informacion_inst["info_nombre"]?><br>
    BOLETÍN DE CALIFICACIONES<br> --></div>
    
	<div style="width:100%">
        <table width="100%" cellspacing="5" cellpadding="5" border="1" rules="all">
            <tr>
                <td>C&oacute;digo:<br> <?=strpos($datosUsr["mat_documento"], '.') !== true && is_numeric($datosUsr["mat_documento"]) ? number_format($datosUsr["mat_documento"],0,",",".") : $datosUsr["mat_documento"];?></td>
                <td>Nombre:<br> <?=$nombre?></td>
                <td>Grado:<br> <?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></td>
                <td>Puesto Curso:<br> <?=$puestoCurso;?></td>
            </tr>
            
            <tr>
                <td>Jornada:<br> Mañana</td>
                <td>Sede:<br> <?=$informacion_inst["info_nombre"]?></td>
                <td colspan="2">Periodo:<br> <b><?=$periodoActual." (".$year.")";?></b></td>
               <!-- <td>Puesto Colegio:<br> &nbsp;</td>   -->
            </tr>
        </table>
        <p>&nbsp;</p>
    </div>
</div>

<table width="100%" cellspacing="5" cellpadding="5" rules="all" border="1">
    <thead>
        <tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
            <td width="20%" rowspan="2">AREAS / ASIGNATURAS</td>
            <td width="2%" rowspan="2">I.H.</td>
            
            <?php  
			for($j=1;$j<=$periodoActual;$j++){
				// OPTIMIZACIÓN: Obtener porcentaje del mapa pre-cargado
				$porcentajeGrado = $porcentajesPeriodoMapa[$j] ?? 25;
			?>
                <td width="3%" colspan="2"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$datosUsr['mat_id'];?>&periodo=<?=$j?>" style="color:#000; text-decoration:none;">Periodo <?=$j."<br>(".$porcentajeGrado."%)"?></a></td>
            <?php }?>
            <td width="3%" colspan="2">Acumulado</td>
        </tr> 
        
        <tr style="font-weight:bold; text-align:center; background-color: #74cc82;">
            <?php  for($j=1;$j<=$periodoActual;$j++){ ?>

                <td width="3%">Nota</td>
                <td width="3%">Desempeño</td>
            <?php }?>
            <td width="3%">Nota</td>
            <td width="3%">Desempeño</td>

        </tr>
        
    </thead>
    
    <?php
	$materiasPerdidas = 0;
	$colspan = 2 + (2 * $periodoActual);
    $conAreas = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $year);
	
	// ============================================
	// OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
	// ============================================
	
	// OPTIMIZACIÓN 1: Pre-cargar porcentajes por período
	$porcentajesPeriodoMapa = []; // [periodo] => porcentaje
	for($j=1; $j<=$periodoActual; $j++){
		$periodosCursos = Grados::traerPorcentajePorPeriodosGrados($conexion, $config, $datosUsr['gra_id'], $j);
		$porcentajesPeriodoMapa[$j] = !empty($periodosCursos['gvp_valor']) ? (float)$periodosCursos['gvp_valor'] : 25;
	}
	
	// OPTIMIZACIÓN 2: Pre-cargar todas las notas del boletín para este estudiante y todos los períodos
	$notasBoletinMapa = []; // [carga][periodo] => datos_nota
	try {
		$sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota, bol_observaciones_boletin, notip_nombre
					 FROM " . BD_ACADEMICA . ".academico_boletin bol
					 LEFT JOIN " . BD_ACADEMICA . ".academico_notas_tipos notip 
					     ON notip.notip_categoria = '".mysqli_real_escape_string($conexion, $config['conf_notas_categoria'])."'
					     AND bol.bol_nota >= notip.notip_desde 
					     AND bol.bol_nota <= notip.notip_hasta
					     AND notip.institucion = bol.institucion
					     AND notip.year = bol.year
					 WHERE bol.bol_estudiante = ?
					   AND bol.institucion = ?
					   AND bol.year = ?
					   AND bol.bol_periodo <= ?";
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
	
	// OPTIMIZACIÓN 3: Pre-cargar todas las ausencias para este estudiante y todos los períodos
	$ausenciasMapa = []; // [carga][periodo] => suma_ausencias
	try {
		// Obtener todas las cargas del estudiante
		$idsCargas = [];
		mysqli_data_seek($conAreas, 0);
		while ($areaTemp = mysqli_fetch_array($conAreas, MYSQLI_BOTH)) {
			$conCargasTemp = CargaAcademica::traerCargasAreasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $areaTemp['ar_id'], $year);
			while ($cargaTemp = mysqli_fetch_array($conCargasTemp, MYSQLI_BOTH)) {
				if (!in_array($cargaTemp['car_id'], $idsCargas)) {
					$idsCargas[] = $cargaTemp['car_id'];
				}
			}
		}
		mysqli_data_seek($conAreas, 0);
		
		if (!empty($idsCargas)) {
			$idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
			$inCargas = implode(',', array_map('intval', $idsCargas));
			$institucion = (int)$config['conf_id_institucion'];
			$yearEsc = mysqli_real_escape_string($conexion, $year);
			$periodoEsc = (int)$periodoActual;
			
			for($j=1; $j<=$periodoEsc; $j++){
				$sqlAusencias = "SELECT cls.cls_id_carga, SUM(aus.aus_ausencias) as total_ausencias
								 FROM " . BD_ACADEMICA . ".academico_clases cls
								 INNER JOIN " . BD_ACADEMICA . ".academico_ausencias aus 
								     ON aus.aus_id_clase = cls.cls_id 
								     AND aus.aus_id_estudiante = '{$idEstudianteEsc}'
								     AND aus.institucion = cls.institucion 
								     AND aus.year = cls.year
								 WHERE cls.cls_id_carga IN ({$inCargas})
								   AND cls.cls_periodo = {$j}
								   AND cls.institucion = {$institucion}
								   AND cls.year = '{$yearEsc}'
								 GROUP BY cls.cls_id_carga";
				
				$consultaAusencias = mysqli_query($conexion, $sqlAusencias);
				if($consultaAusencias){
					while($rowAus = mysqli_fetch_array($consultaAusencias, MYSQLI_BOTH)){
						$idCarga = $rowAus['cls_id_carga'];
						if (!isset($ausenciasMapa[$idCarga])) {
							$ausenciasMapa[$idCarga] = [];
						}
						$ausenciasMapa[$idCarga][$j] = (float)$rowAus['total_ausencias'];
					}
				}
			}
		}
	} catch (Exception $eAus) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 4: Pre-cargar indicadores y notas de indicadores para todas las cargas
	$indicadoresMapa = []; // [car_id] => array de indicadores
	$notasIndicadoresMapa = []; // [car_id][indicador] => nota
	try {
		mysqli_data_seek($conAreas, 0);
		while ($areaTemp = mysqli_fetch_array($conAreas, MYSQLI_BOTH)) {
			$conCargasTemp = CargaAcademica::traerCargasAreasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $areaTemp['ar_id'], $year);
			while ($cargaTemp = mysqli_fetch_array($conCargasTemp, MYSQLI_BOTH)) {
				$idCarga = $cargaTemp['car_id'];
				if (!isset($indicadoresMapa[$idCarga])) {
					$indicadoresMapa[$idCarga] = [];
					$indicadoresTemp = Indicadores::traerCargaIndicadorPorPeriodo($conexion, $config, $idCarga, $periodoActual, $year);
					while ($indTemp = mysqli_fetch_array($indicadoresTemp, MYSQLI_BOTH)) {
						$indicadoresMapa[$idCarga][] = $indTemp;
						// Pre-cargar nota del indicador
						$notaIndTemp = Calificaciones::consultaNotaIndicadores($config, $indTemp['ipc_indicador'], $idCarga, $datosUsr['mat_id'], $periodoActual, $year);
						if (!isset($notasIndicadoresMapa[$idCarga])) {
							$notasIndicadoresMapa[$idCarga] = [];
						}
						$notasIndicadoresMapa[$idCarga][$indTemp['ipc_indicador']] = $notaIndTemp;
					}
				}
			}
		}
		mysqli_data_seek($conAreas, 0);
	} catch (Exception $eInd) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 5: Pre-cargar promedios por período
	$promediosPeriodosMapa = []; // [periodo] => promedio
	try {
		$idEstudianteEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_id']);
		$gradoEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_grado']);
		$grupoEsc = mysqli_real_escape_string($conexion, $datosUsr['mat_grupo']);
		$institucion = (int)$config['conf_id_institucion'];
		$yearEsc = mysqli_real_escape_string($conexion, $year);
		
		for($j=1; $j<=$periodoActual; $j++){
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
		}
	} catch (Exception $eProm) {
		include("../compartido/error-catch-to-report.php");
	}
	
	// OPTIMIZACIÓN 6: Pre-cargar cache de notas cualitativas
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
	
	while($datosAreas = mysqli_fetch_array($conAreas, MYSQLI_BOTH)){
	?>
    <tbody>
        <!-- AREAS -->
		<tr style="background: lightgray; color:black; height: 30px; font-weight: bold; font-size: 14px;">
            <td colspan="<?=$colspan;?>"><?=strtoupper($datosAreas['ar_nombre']);?></td> 
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>

        </tr>
		
		<?php
		$contador=1;
		$conCargas = CargaAcademica::traerCargasAreasPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $datosAreas['ar_id'], $year);
		while($datosCargas = mysqli_fetch_array($conCargas, MYSQLI_BOTH)){
		?>
		<!-- ASIGNATURAS -->
		<tr style="background:#fff; height: 25px; font-weight: bold;">
            <td><?=strtoupper($datosCargas['mat_nombre']);?></td>
            <td align="center"><?=$datosCargas['car_ih'];?></td> 
            <?php 
			$promedioMateria = 0;
			$sumaPorcentaje = 0;
			for($j=1;$j<=$periodoActual;$j++){
				// OPTIMIZACIÓN: Obtener porcentaje del mapa pre-cargado
				$porcentajeGrado = $porcentajesPeriodoMapa[$j] ?? 25;
				$decimal = $porcentajeGrado/100;
				
				// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
				$datosBoletin = $notasBoletinMapa[$datosCargas['car_id']][$j] ?? null;
				if($datosBoletin === null){
					// Fallback: consulta individual si no está en el mapa
					$resTemp = Boletin::traerNotaBoletinCargaPeriodo($config, $j, $datosUsr['mat_id'], $datosCargas['car_id'], $year);
					if($resTemp){
						$datosBoletin = mysqli_fetch_array($resTemp, MYSQLI_BOTH);
					} else {
						$datosBoletin = ['bol_nota' => 0, 'notip_nombre' => ''];
					}
				}
				
				// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado (aunque no se usan en este archivo, las pre-cargamos por si acaso)
				$datosAusencias = [0 => ($ausenciasMapa[$datosCargas['car_id']][$j] ?? 0)];
				
				$promedioMateria += !empty($datosBoletin['bol_nota']) ? (float)$datosBoletin['bol_nota']*$decimal : 0;
				$sumaPorcentaje += $decimal;
				$colorFondoNota = '';
				if(!empty($datosBoletin['bol_nota']) && $datosBoletin['bol_nota']<$config["conf_nota_minima_aprobar"]){$colorFondoNota = 'tomato';}
            ?>

                <td align="center" style="background-color: <?=$colorFondoNota;?>;"><?=!empty($datosBoletin['bol_nota']) ? $datosBoletin['bol_nota'] : '';?></td>
                <td align="center"><?=!empty($datosBoletin['notip_nombre']) ? $datosBoletin['notip_nombre'] : '';?></td>
            <?php 
			}
			$promedioMateria = $sumaPorcentaje > 0 ? ($promedioMateria / $sumaPorcentaje) : 0;
			$promedioMateria = round((float)$promedioMateria, $config['conf_decimales_notas']);
			
			$colorFondoPromedioM = '';
			if($promedioMateria!="" && $promedioMateria<$config["conf_nota_minima_aprobar"]){$colorFondoPromedioM = 'tomato'; $materiasPerdidas++;}
			
			// OPTIMIZACIÓN: Usar cache de notas cualitativas
			$notaMateriaRedondeada = number_format((float)$promedioMateria, 1, '.', '');
			$promediosMateriaEstiloNota = isset($notasCualitativasCache[$notaMateriaRedondeada]) 
				? ['notip_nombre' => $notasCualitativasCache[$notaMateriaRedondeada]] 
				: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioMateria, $year);
			if($promediosMateriaEstiloNota === null){
				$promediosMateriaEstiloNota = ['notip_nombre' => ''];
			}
			?>
            <td align="center" style="background-color: <?=$colorFondoPromedioM;?>"><?=$promedioMateria;?></td>
            <td align="center"><?=$promediosMateriaEstiloNota['notip_nombre'];?></td>

        </tr>
		
		
		<?php
		// OPTIMIZACIÓN: Obtener indicadores del mapa pre-cargado
		$indicadores = $indicadoresMapa[$datosCargas['car_id']] ?? [];
		foreach($indicadores as $ind){
			// OPTIMIZACIÓN: Obtener nota del indicador del mapa pre-cargado
			$calificacionesIndicadores = $notasIndicadoresMapa[$datosCargas['car_id']][$ind['ipc_indicador']] ?? [0 => 0];
		?>
		<!-- INDICADORES -->
		<tr>
            <td><?=$ind['ipc_indicador'].") ".$ind['ind_nombre'];?></td>
            <td align="center"><?=$ind['ipc_valor']."%";?></td> 
            <?php 
			$promedioMateria = 0;
			for($j=1;$j<=$periodoActual;$j++){

				$notaIndicadorFinal="&nbsp;";
				if($j==$periodoActual){
					$notaIndicadorFinal = !empty($calificacionesIndicadores[0]) ? $calificacionesIndicadores[0] : "&nbsp;";
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						// OPTIMIZACIÓN: Usar cache de notas cualitativas
						$notaIndRedondeada = number_format((float)$calificacionesIndicadores[0], 1, '.', '');
						$notaIndicadorFinal = isset($notasCualitativasCache[$notaIndRedondeada]) 
							? $notasCualitativasCache[$notaIndRedondeada] 
							: "";
					}
				}
            ?>
                <td align="center">&nbsp;</td>
                <td align="center"><?=$notaIndicadorFinal;?></td>

            <?php 
			}
			?>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>

        </tr>
		<?php }?>
		
		<?php }?>
		
		
    </tbody>
    <?php 
		$contador++;
	}
	?>

	<tfoot>
    	<tr style="font-weight:bold; text-align:center; font-size:13px;">
        	<td style="text-align:left;">PROMEDIO/TOTAL</td>
            <td>-</td> 

            <?php 
            for($j=1;$j<=$periodoActual;$j++){
				// OPTIMIZACIÓN: Obtener promedio del mapa pre-cargado
				$promediosPeriodos = ['promedio' => ($promediosPeriodosMapa[$j] ?? 0)];
				
				// OPTIMIZACIÓN: Usar cache de notas cualitativas
				$promedioRedondeado = number_format((float)$promediosPeriodos['promedio'], 1, '.', '');
				$promediosEstiloNota = isset($notasCualitativasCache[$promedioRedondeado]) 
					? ['notip_nombre' => $notasCualitativasCache[$promedioRedondeado]] 
					: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promediosPeriodos['promedio'], $year);
				if($promediosEstiloNota === null){
					$promediosEstiloNota = ['notip_nombre' => ''];
				}
            ?>
                <td><?=$promediosPeriodos['promedio'];?></td>
                <td><?=!empty($promediosEstiloNota['notip_nombre']) ? $promediosEstiloNota['notip_nombre'] : '';?></td>
            <?php }?>

            <td>-</td>
            <td>-</td>
        </tr>
    </tfoot>

</table>
<p>&nbsp;</p>	

<?php
$estadoAgno = '';
if($periodoActual==$datosUsr['gra_periodos']){
	if($materiasPerdidas==0){$estadoAgno = 'PROMOVIDO';}
	elseif($materiasPerdidas>0 and $materiasPerdidas<$config["conf_num_materias_perder_agno"]){$estadoAgno = 'DEBE NIVELAR';}
	elseif($materiasPerdidas>=$config["conf_num_materias_perder_agno"]){$estadoAgno = 'NO FUE PROMOVIDO';}
}
?>
	
<table width="100%" cellspacing="5" cellpadding="5" rules="none" border="0">
	<tr>
        <td width="40%">
            ________________________________________________________________<br>
            DIRECTOR DE GRADO
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
        <td width="60%">
        	<p style="font-weight:bold;">Observaciones: <?=$estadoAgno;?></p>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________<br><br>
            ______________________________________________________________________
        </td>
    </tr>
</table>

<div id="saltoPagina"></div>
                                   
<?php
}// FIN DE TODOS LOS MATRICULADOS
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>

<!--
<script type="application/javascript">
print();
</script>   
-->                                 
                          
</body>
</html>
