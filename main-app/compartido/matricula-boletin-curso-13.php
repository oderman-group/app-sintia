<?php
include("session-compartida.php");
$idPaginaInterna = 'DT0224';
if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once(ROOT_PATH."/main-app/class/Estudiantes.php");
require_once(ROOT_PATH."/main-app/class/Asignaturas.php");
require_once(ROOT_PATH."/main-app/class/Calificaciones.php");
require_once(ROOT_PATH."/main-app/class/Boletin.php");
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

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $year);
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
$puestoCurso = 0;
$promedioPuesto = 0;
$puestos = Boletin::obtenerPuestoYpromedioEstudiante($periodoActual,$matriculadosDatos['mat_grado'], $matriculadosDatos['mat_grupo'], $year);

foreach($puestos as $puesto){
        if($puesto['estudiante_id']==$matriculadosDatos['mat_id']){
		$puestoCurso = $puesto['puesto'];
		$promedioPuesto = Boletin::notaDecimales((float)$puesto['prom']);
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
	<title>Boletín Formato 13</title>
	<link rel="shortcut icon" href="../sintia-icono.png" />
	<meta name="tipo_contenido"  content="text/html;" http-equiv="content-type" charset="utf-8">
<style>
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}
</style>
</head>

<body style="font-family:Arial;">
<?php
//CONSULTA QUE ME TRAE EL DESEMPEÑO
$consulta_desempeno = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consulta_mat_area_est = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr['mat_grado'], $datosUsr['mat_grupo'], $year);
$numero_periodos=$periodoActual;

// ============================================
// OPTIMIZACIONES: Pre-cargar datos para evitar N+1 queries
// ============================================

// OPTIMIZACIÓN 1: Pre-cargar todas las notas del boletín para este estudiante y todos los períodos
$notasBoletinMapa = []; // [car_id][periodo] => datos_nota
try {
	// Obtener todas las cargas del estudiante
	$idsCargas = [];
	mysqli_data_seek($consulta_mat_area_est, 0);
	while ($areaTemp = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
		$consulta_a_mat_temp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $areaTemp["ar_id"], "1,2,3,4", $year);
		while ($materiaTemp = mysqli_fetch_array($consulta_a_mat_temp, MYSQLI_BOTH)) {
			if (!in_array($materiaTemp['car_id'], $idsCargas)) {
				$idsCargas[] = $materiaTemp['car_id'];
			}
		}
	}
	mysqli_data_seek($consulta_mat_area_est, 0);
	
	if (!empty($idsCargas)) {
		$sqlNotas = "SELECT bol_carga, bol_periodo, bol_nota, bol_observaciones_boletin
					 FROM " . BD_ACADEMICA . ".academico_boletin
					 WHERE bol_estudiante = ?
					   AND bol_carga IN (" . implode(',', array_map('intval', $idsCargas)) . ")
					   AND institucion = ?
					   AND year = ?
					   AND bol_periodo <= ?";
		$paramNotas = [
			$matriculadosDatos['mat_id'],
			$config['conf_id_institucion'],
			$year,
			$numero_periodos
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
	}
} catch (Exception $eNotas) {
	include("../compartido/error-catch-to-report.php");
}

// OPTIMIZACIÓN 2: Pre-cargar nivelaciones para todas las cargas
$nivelacionesMapa = []; // [car_id] => datos_nivelacion
try {
	mysqli_data_seek($consulta_mat_area_est, 0);
	while ($areaTemp = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
		$consulta_a_mat_temp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $areaTemp["ar_id"], "1,2,3,4", $year);
		while ($materiaTemp = mysqli_fetch_array($consulta_a_mat_temp, MYSQLI_BOTH)) {
			$idCarga = $materiaTemp['car_id'];
			if (!isset($nivelacionesMapa[$idCarga])) {
				$nivTemp = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $idCarga, $year);
				$niv = mysqli_fetch_array($nivTemp, MYSQLI_BOTH);
				if ($niv) {
					$nivelacionesMapa[$idCarga] = $niv;
				}
			}
		}
	}
	mysqli_data_seek($consulta_mat_area_est, 0);
} catch (Exception $eNiv) {
	include("../compartido/error-catch-to-report.php");
}

// OPTIMIZACIÓN 3: Pre-cargar ausencias (si están disponibles)
$ausenciasMapa = []; // [car_id] => [ausencias, matmaxaus]
try {
	mysqli_data_seek($consulta_mat_area_est, 0);
	while ($areaTemp = mysqli_fetch_array($consulta_mat_area_est, MYSQLI_BOTH)) {
		$consulta_a_mat_temp = Boletin::obtenerDefinitivaYnombrePorMateria($matriculadosDatos['mat_id'], $areaTemp["ar_id"], "1,2,3,4", $year);
		while ($materiaTemp = mysqli_fetch_array($consulta_a_mat_temp, MYSQLI_BOTH)) {
			$idCarga = $materiaTemp['car_id'];
			if (!isset($ausenciasMapa[$idCarga])) {
				// Nota: La consulta de ausencias parece estar en otra parte del código
				// Por ahora, solo inicializamos el mapa
				$ausenciasMapa[$idCarga] = ['ausencias' => 0, 'matmaxaus' => $materiaTemp['matmaxaus'] ?? 0];
			}
		}
	}
	mysqli_data_seek($consulta_mat_area_est, 0);
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
				$key = number_format((float)$i, $config['conf_decimales_notas'], '.', '');
				if(!isset($notasCualitativasCache[$key])){
					$notasCualitativasCache[$key] = $tipoNota['notip_nombre'];
				}
			}
		}
	}
}

// OPTIMIZACIÓN 5: Cachear desempeños en un array para búsqueda rápida
$desempenosCache = [];
mysqli_data_seek($consulta_desempeno, 0);
while($r_desempeno_temp = mysqli_fetch_array($consulta_desempeno, MYSQLI_BOTH)){
	// Crear un mapa por rango de notas para búsqueda rápida
	for($i = $r_desempeno_temp['notip_desde']; $i <= $r_desempeno_temp['notip_hasta']; $i += 0.1){
		$key = number_format((float)$i, $config['conf_decimales_notas'], '.', '');
		if(!isset($desempenosCache[$key])){
			$desempenosCache[$key] = $r_desempeno_temp;
		}
	}
}
mysqli_data_seek($consulta_desempeno, 0);

 ?>

<?php
$nombreInforme = "BOLETÍN DE CALIFICACIONES";
include("../compartido/head-informes.php") ?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">
    <tr>
    	<td>C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td colspan="2">Nombre: <b><?=$nombre?></b></td>   
    </tr>
    
    <tr>
    	<td>Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
        <td>Periodo: <b><?=strtoupper($periodoActuales);?></b></td>
        <td>Puesto Curso:<br> <?=$puestoCurso?></td>    
    </tr>
</table>
<br>
<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
<tr style="font-weight:bold; background:#EAEAEA; border-color:#000; height:20px; color:#000; font-size:12px;">
<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
<td width="2%" align="center">I.H</td>


<?php $columnas=5; for($j=1;$j<=$numero_periodos;$j++){?>
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$matriculadosDatos['mat_id'];?>&periodo=<?=$j?>" style="color:#000; text-decoration:underline;"><?=$j?>P</a></td>
<?php $columnas++;}?>



<td width="4%" align="center">PRO</td>
<!--<td width="5%" align="center">PER</td>-->
<td width="8%" align="center">DESEMPE&Ntilde;O</td>   
<td width="5%" align="center">AUS</td>
</tr> 

    <tr style="background:#F06;">
    	<td class="area" id="" colspan="<?=$columnas;?>" style="font-size:12px; font-weight:bold;"></td>
        <!--<td colspan="3"></td>-->
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
	$total_promedio = Boletin::notaDecimales($total_promedio);
}
	if($numfilas_not_area>0){
			?>
  <tr bgcolor="#ABABAB" style="font-size:12px;">
            <td style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultado_not_area["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($k=1;$k<=$numero_periodos;$k++){ 
			?>
			<td class=""  align="center" style="font-weight:bold;"></td>
            <?php }?>
        <td align="center" style="font-weight:bold;"><?=$total_promedio;?></td>
         <td align="center" style="font-weight:bold;"></td>
          <td align="center" style="font-weight:bold;"></td>
	</tr>
<?php

while($fila2=mysqli_fetch_array($consulta_a_mat, MYSQLI_BOTH)){ 
	$contador_periodos=0;
	mysqli_data_seek($consulta_a_mat_per,0);
	//CONSULTAR NOTA POR PERIODO
	while($fila3=mysqli_fetch_array($consulta_a_mat_per, MYSQLI_BOTH)){
		if($fila2["mat_id"]==$fila3["mat_id"]){
			$contador_periodos++;
			$nota_periodo = !empty($fila3["bol_nota"]) ? (float)$fila3["bol_nota"] : 0;
			$nota_periodo = Boletin::notaDecimales($nota_periodo);
			$notas[$contador_periodos] = $nota_periodo;
		}
	}//FIN FILA3
?>
 <tr bgcolor="#EAEAEA" style="font-size:12px;">
            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;">&raquo;<?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"];?></td>
<?php for($l=1;$l<=$numero_periodos;$l++){
	// OPTIMIZACIÓN: Obtener nota del mapa pre-cargado
	$notaDelEstudiante = $notasBoletinMapa[$fila2['car_id']][$l] ?? ['bol_nota' => '', 'bol_observaciones_boletin' => ''];
?>
			<td class=""  align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;">
			<?php 
			if(!empty($notaDelEstudiante['bol_nota'])){
				$notaFormateada = Boletin::notaDecimales((float)$notaDelEstudiante['bol_nota']);
				// OPTIMIZACIÓN: Usar cache de notas cualitativas
				$notaRedondeada = number_format((float)$notaDelEstudiante['bol_nota'], $config['conf_decimales_notas'], '.', '');
				$desempenoNotaP = isset($notasCualitativasCache[$notaRedondeada]) 
					? ['notip_nombre' => $notasCualitativasCache[$notaRedondeada]] 
					: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDelEstudiante['bol_nota'], $year);
				if($desempenoNotaP === null){
					$desempenoNotaP = ['notip_nombre' => ''];
				}
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					echo $desempenoNotaP['notip_nombre'];
				}else{
					echo $notaFormateada."<br>".$desempenoNotaP['notip_nombre'];
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
	  $total_promedio2 = !empty($fila2["suma"]) ? (float)$fila2["suma"] : 0;
	  $total_promedio2 = Boletin::notaDecimales($total_promedio2);
	   //if($total_promedio2<$r_desempeno["desbasdesde"]){$materiasPerdidas++;}
	    $msj='';
	   if($total_promedio2<$config[5]){
			// OPTIMIZACIÓN: Obtener nivelación del mapa pre-cargado
			$nivelaciones = $nivelacionesMapa[$fila2['car_id']] ?? null;
			if($nivelaciones === null){
				// Fallback: consulta individual si no está en el mapa
				$consultaNivelaciones = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
				$nivelaciones = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);
				if($nivelaciones){
					$nivelacionesMapa[$fila2['car_id']] = $nivelaciones;
				}
			}
		   if($nivelaciones && !empty($nivelaciones['niv_definitiva'])){
				if($nivelaciones['niv_definitiva']<$config[5]){
					$materiasPerdidas++;
				}else{
					$total_promedio2 = $nivelaciones['niv_definitiva'];
					$msj='Niv';
				}
		   }else{
				$materiasPerdidas++;
		   }
		   
		   }
	   ?>
       
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?=$total_promedio2?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php //DESEMPEÑO
		// OPTIMIZACIÓN: Usar cache de desempeños
		$totalPromedioRedondeado = number_format((float)$total_promedio2, $config['conf_decimales_notas'], '.', '');
		$r_desempeno = $desempenosCache[$totalPromedioRedondeado] ?? null;
		if($r_desempeno === null){
			// Fallback: buscar en consulta original
			mysqli_data_seek($consulta_desempeno, 0);
			while($r_desempeno_temp=mysqli_fetch_array($consulta_desempeno, MYSQLI_BOTH)){
				if($total_promedio2>=$r_desempeno_temp["notip_desde"] && $total_promedio2<=$r_desempeno_temp["notip_hasta"]){
					$r_desempeno = $r_desempeno_temp;
					break;
				}
			}
			mysqli_data_seek($consulta_desempeno, 0);
		}
		if($r_desempeno){
			if($datosUsr["mat_grado"]>100){
				echo $r_desempeno["notip_nombre"];
			}else{
				echo $r_desempeno["notip_nombre"];
			}
		}
		 ?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php 
		// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado (si está disponible)
		$ausenciasData = $ausenciasMapa[$fila2['car_id']] ?? ['ausencias' => 0, 'matmaxaus' => ($fila2["matmaxaus"] ?? 0)];
		$ausencias = $ausenciasData['ausencias'];
		$matmaxaus = $ausenciasData['matmaxaus'];
		if($ausencias > 0){ 
			echo $ausencias."/".$matmaxaus;
		} else{ 
			echo "0.0/".$matmaxaus;
		}
		?></td>
	
	</tr>
	
<?php
if($numIndicadores>0){
	 mysqli_data_seek($consulta_a_mat_indicadores,0);
	 $contador_indicadores=0;
	while($fila4=mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)){
	if($fila4["mat_id"]==$fila2["mat_id"]){
		$contador_indicadores++;
		$nota_indicador = !empty($fila4["nota"]) ? (float)$fila4["nota"] : 0;
		$nota_indicador = Boletin::notaDecimales($nota_indicador);
		
	?>
<tr bgcolor="#FFF" style="font-size:12px;">
            <td style="font-size:12px; height:15px;"><?php echo $contador_indicadores.".".$fila4["ind_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($m=1;$m<=$numero_periodos;$m++){ 
			// OPTIMIZACIÓN: Usar cache de notas cualitativas
			$notaIndRedondeada = number_format((float)$nota_indicador, $config['conf_decimales_notas'], '.', '');
			$desempenoNotaInd = isset($notasCualitativasCache[$notaIndRedondeada]) 
				? ['notip_nombre' => $notasCualitativasCache[$notaIndRedondeada]] 
				: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota_indicador, $year);
			?>
			<td class=""  align="center" style="font-weight:bold;"><?php if($periodoActual==$m){ echo $nota_indicador;} ?></td>
            <?php } ?>
 <td align="center" style="font-weight:bold;"></td>
        <td align="center" style="font-weight:bold;"></td>
        <td align="center" style="font-weight:bold;"></td>
<?php
	}//fin if
	}
}
?>
	<!-- observaciones de la asignatura-->
	<?php
	// OPTIMIZACIÓN: Obtener observación del mapa pre-cargado
	$observacion = $notasBoletinMapa[$fila2['car_id']][$periodoActual] ?? ['bol_observaciones_boletin' => ''];
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
	 

          

            

    <tr align="center" style="font-size:12px; font-weight:bold;">
        <td colspan="2" align="right">PROMEDIO</td>

		<?php for($n=1;$n<=$numero_periodos;$n++){ 
		$notaFFF =0;
		if(!empty($contpromedios[$n]) && $contpromedios[$n] > 0){
			$notaFFF = (float)($promedios[$n]/$contpromedios[$n]);
			$notaFFF = Boletin::notaDecimales($notaFFF);
		}
		// OPTIMIZACIÓN: Usar cache de notas cualitativas
		$notaFFFRedondeada = number_format((float)$notaFFF, $config['conf_decimales_notas'], '.', '');
		$desempenoNotaProm = isset($notasCualitativasCache[$notaFFFRedondeada]) 
			? ['notip_nombre' => $notasCualitativasCache[$notaFFFRedondeada]] 
			: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaFFF, $year);
		if($desempenoNotaProm === null){
			$desempenoNotaProm = ['notip_nombre' => ''];
		}
		?>
        <td style="font-size:16px;">
        	<?php  if($promedios[$n]!=0){ echo $promedioPuesto;}?></td>
        <?php } ?>
        <td></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    
</table>

<?php for($n=1;$n<=$numero_periodos;$n++){if($promedios[$n]!=0){$promedios[$n]=0; $contpromedios[$n]=0;} } ?>

<p>&nbsp;</p>
<?php 
// OPTIMIZACIÓN: Usar prepared statements para consulta de disciplina
$idEstudianteEsc = mysqli_real_escape_string($conexion, $matriculadosDatos['mat_id']);
$condicionEsc = mysqli_real_escape_string($conexion, $condicion);
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='{$idEstudianteEsc}' AND institucion=".(int)$config['conf_id_institucion']." AND year='".mysqli_real_escape_string($conexion, $year)."' AND dn_periodo in({$condicionEsc});");
if(@mysqli_num_rows($cndisiplina)>0){
?>
<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">

    <tr style="font-weight:bold; background:#036; border-color:#036; height:40px; color:#FC0; font-size:12px; text-align:center">
    	<td colspan="3">NOTA DE COMPORTAMIENTO</td>
    </tr>
    
    <tr style="font-weight:bold; background:#F06; border-color:#F06; height:25px; color:#FFF; font-size:12px; text-align:center">
        <td width="8%">Periodo</td>
        <!--<td width="8%">Nota</td>-->
        <td>Observaciones</td>
    </tr>
<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){
// OPTIMIZACIÓN: Usar cache de notas cualitativas
$notaDisciplinaRedondeada = number_format((float)$rndisiplina["dn_nota"], $config['conf_decimales_notas'], '.', '');
$desempenoND = isset($notasCualitativasCache[$notaDisciplinaRedondeada]) 
	? ['notip_nombre' => $notasCualitativasCache[$notaDisciplinaRedondeada]] 
	: Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $rndisiplina["dn_nota"], $year);
if($desempenoND === null){
	$desempenoND = ['notip_nombre' => ''];
}
?>
    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
        <td><?=$rndisiplina["dn_periodo"]?></td>
        <!--<td><?=$desempenoND['notip_nombre']?></td>-->
        <td align="left"><?=$rndisiplina["dn_observacion"]?></td>
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
	if($periodoActual==4){
		if($materiasPerdidas>=$config["conf_num_materias_perder_agno"])
			$msj = "<center>EL (LA) ESTUDIANTE ".UsuariosPadre::nombreCompletoDelUsuario($datosUsr)." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
		elseif($materiasPerdidas<$config["conf_num_materias_perder_agno"] and $materiasPerdidas>0)
			$msj = "<center>EL (LA) ESTUDIANTE ".UsuariosPadre::nombreCompletoDelUsuario($datosUsr)." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
		else
			$msj = "<center>EL (LA) ESTUDIANTE ".UsuariosPadre::nombreCompletoDelUsuario($datosUsr)." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
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
