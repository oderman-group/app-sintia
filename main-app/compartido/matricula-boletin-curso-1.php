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
</style>
</head>

<body style="font-family:Arial;">
<?php
//CONSULTA QUE ME TRAE EL DESEMPEÑO
$consultaDesempeno1 = Boletin::listarTipoDeNotas($config["conf_notas_categoria"], $year);	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
$numeroPeriodos=2;

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

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px; border:solid; 
  border-color:<?=$Plataforma->colorUno;?>; ">
	<tr style="font-weight:bold; height:30px; background:<?=$Plataforma->colorUno;?>; color:#FFF;">
    	<td>C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td>Nombre: <b><?=$nombre?></b></td>   
    </tr>
    
    <tr>
    	<td>Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
        <td>Periodo: <b><?=strtoupper($periodoActuales);?></b></td>    
    </tr>
</table>
<br>
<table width="100%" id="tblBoletin"  style="border:solid;border-color:<?=$Plataforma->colorUno;?>;" pacing="0" cellpadding="0" rules="all" border="1" align="left">
<tr style="font-weight:bold; height:20px;background:<?=$Plataforma->colorUno;?>; color:#FFF; font-size:12px;">
<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
<td width="2%" align="center">I.H</td>

<!--
<?php for($j=1;$j<=$numeroPeriodos;$j++){ ?>
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$matriculadosDatos['mat_id'];?>&periodo=<?=$j?>" style="color:#000; text-decoration:underline;">2P</a></td>
<?php }?>
-->

<td width="3%" align="center" style="font-size:10px;">NOTAS<br>ASIGNATURA</td>
<td width="3%" align="center" style="font-size:10px;">NOTAS<br>LOGROS</td>

<td width="4%" align="center">PRO</td>
<!--<td width="5%" align="center">PER</td>-->
<td width="8%" align="center">DESEMPE&Ntilde;O</td>   
<td width="5%" align="center">AUS</td>
</tr> 

<tr style="border-color:<?=$Plataforma->colorUno;?>;">
    	<td class="area" id="" colspan="6" style="font-size:12px; font-weight:bold;"></td>
        <td colspan="3"></td>
    </tr>
        <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->
        <?php while($fila = mysqli_fetch_array($consultaMatAreaEst, MYSQLI_BOTH)){
		
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
$consultaNotdefArea = Boletin::obtenerDatosDelArea($matriculadosDatos['mat_id'], $fila["ar_id"], $condicion, $year);

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
  <tr   style="border:solid;border-color:<?=$Plataforma->colorUno;?>;font-size:12px;">
            <td style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultadoNotArea["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($k=1;$k<=$numeroPeriodos;$k++){ 
			?>
			<td class=""  align="center" style="font-weight:bold;"></td>
            <?php }?>
        <td align="center" style="font-weight:bold;"><?php 
		
		if($datosUsr["mat_grado"]>11){
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
?>
 <tr bgcolor="#EAEAEA" style="font-size:12px;">
            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;">&raquo;<?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"];?></td>
<?php for($l=1;$l<=$numeroPeriodos;$l++){ ?>
			<td class=""  align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;">
			<?php 
			if($datosUsr["mat_grado"]>11){
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
      <?php 
	  $totalPromedio2=round( $fila2["suma"],1);
	   
	   if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
	   if($totalPromedio2<$config['conf_nota_minima_aprobar']){$materiasPerdidas++;}
	   ?>
       
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php 
		
					if($datosUsr["mat_grado"]>11){
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
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php //DESEMPEÑO
		// OPTIMIZACIÓN: Usar cache de desempeños
		$rDesempeno = Boletin::obtenerDatosTipoDeNotasCargadas($desempenosCache, $totalPromedio2);
		if($rDesempeno){
			if($datosUsr["mat_grado"]>11){
				$notaFD = ceil($totalPromedio2);
				switch($notaFD){
					case 1: echo "BAJO"; break;
					case 2: echo "BAJO"; break;
					case 3: echo "B&Aacute;SICO"; break;
					case 4: echo "ALTO"; break;
					case 5: echo "SUPERIOR"; break;
				}
			}else{
				echo $rDesempeno["notip_nombre"];
			}
		}

		// OPTIMIZACIÓN: Obtener ausencias del mapa pre-cargado
		$sumAusencias = 0;
		for($j = 1; $j <= $periodoActual; $j++){
			$keyAusencias = $matriculadosDatos['mat_id'] . '_' . $fila2['mat_id'] . '_' . $j;
			if(isset($ausenciasMapa[$keyAusencias])){
				$sumAusencias += $ausenciasMapa[$keyAusencias];
			}
		}
		 ?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php if($sumAusencias>0){ echo $sumAusencias;} else{ echo "0.0";}?></td>
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
<tr bgcolor="#FFF" style="font-size:12px;">
            <td style="font-size:12px; height:15px;"><?php echo $contadorIndicadores.". ".$fila4["ind_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($m=1;$m<=$numeroPeriodos;$m++){ ?>
			<td class=""  align="center" style="font-weight:bold;"><?php if($periodoActual==$m){
				if($datosUsr["mat_grado"]>11){
				$notaFII = ceil($notaIndicador);
				switch($notaFII){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
			}else{
				$notaIndicadorFinal=$notaIndicador;
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
					// OPTIMIZACIÓN: Usar cache de notas cualitativas
					$notaRedondeada = number_format((float)$notaIndicador, 1, '.', '');
					$notaIndicadorFinal = isset($notasCualitativasCache[$notaRedondeada]) 
						? $notasCualitativasCache[$notaRedondeada] 
						: "";
				}
				echo $notaIndicadorFinal;
			}
				
				} ?></td>
            <?php } ?>
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
	 

          

            

    <tr align="center" style="font-size:12px; font-weight:bold;">
        <td colspan="2" align="right">PROMEDIO</td>

		<?php for($n=1;$n<=$numeroPeriodos;$n++){ ?>
        <td style="font-size:16px;"><?php 
		if($promedios[$n]!=0){
			if($datosUsr["mat_grado"]>11){
				$notaFF = ceil(round(($promedios[$n]/$contpromedios[$n]),1));
				switch($notaFF){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
					}
				}else{
					$promedioTotal= round(($promedios[$n]/$contpromedios[$n]),1);
					$promedioTotalFinal=$promedioTotal;
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						// OPTIMIZACIÓN: Usar cache de notas cualitativas
						$notaRedondeada = number_format((float)$promedioTotal, 1, '.', '');
						$promedioTotalFinal = isset($notasCualitativasCache[$notaRedondeada]) 
							? $notasCualitativasCache[$notaRedondeada] 
							: "";
					}
					echo $promedioTotalFinal;
				}
			
			}?></td>
        <?php } ?>
        <td></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    
</table>

<?php for($n=1;$n<=$numeroPeriodos;$n++){if($promedios[$n]!=0){$promedios[$n]=0; $contpromedios[$n]=0;} } ?>

<p>&nbsp;</p>
<?php 
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$matriculadosDatos['mat_id']."' AND institucion={$config['conf_id_institucion']} AND year={$year} AND dn_periodo in(".$condicion.");");
if(@mysqli_num_rows($cndisiplina)>0){
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
	if($materiasPerdidas>=$config["conf_num_materias_perder_agno"])
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_segundo_apellido'])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<3 and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_segundo_apellido'])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_segundo_apellido'])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
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
