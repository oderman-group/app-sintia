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

while($puesto = mysqli_fetch_array($puestos, MYSQLI_BOTH)){
	if($puesto['bol_estudiante']==$matriculadosDatos['mat_id']){
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
$consulta_mat_area_est = CargaAcademica::traerCargasMateriasAreaPorCursoGrupo($config, $datosUsr["mat_grado"], $datosUsr["mat_grupo"], $year);
$numero_periodos=$periodoActual;
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
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=base64_encode($matriculadosDatos['mat_id']);?>&periodo=<?=base64_encode($j)?>" style="color:#000; text-decoration:underline;"><?=$j?>P</a></td>
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
	$total_promedio=round( $resultado_not_area["suma"],1);
}

if($total_promedio==1)	$total_promedio="1.0";	if($total_promedio==2)	$total_promedio="2.0";		if($total_promedio==3)	$total_promedio="3.0";	if($total_promedio==4)	$total_promedio="4.0";	if($total_promedio==5)	$total_promedio="5.0";
	if($numfilas_not_area>0){
			?>
  <tr bgcolor="#ABABAB" style="font-size:12px;">
            <td style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultado_not_area["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($k=1;$k<=$numero_periodos;$k++){ 
			?>
			<td class=""  align="center" style="font-weight:bold;"></td>
            <?php }?>
        <td align="center" style="font-weight:bold;"><?php 
		$desempenoNotaPromArea = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $total_promedio, $year);
		
		if($datosUsr["mat_grado"]>11){
				$notaFA = ceil($total_promedio);
			/*
				switch($notaFA){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				*/
			echo $desempenoNotaPromArea['notip_nombre'];
				}else{
				$totalPromedioFinal=$total_promedio;
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
				$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $total_promedio, $year);
				$totalPromedioFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
				}
		echo $totalPromedioFinal;
				}
		
		?></td>
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
			$nota_periodo=round($fila3["bol_nota"],1);
			if($nota_periodo==1)$nota_periodo="1.0";	if($nota_periodo==2)$nota_periodo="2.0";	if($nota_periodo==3)$nota_periodo="3.0";	if($nota_periodo==4)$nota_periodo="4.0";	if($nota_periodo==5)$nota_periodo="5.0";
			$notas[$contador_periodos] =$nota_periodo;
		}
	}//FIN FILA3
?>
 <tr bgcolor="#EAEAEA" style="font-size:12px;">
            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;">&raquo;<?php echo $fila2["car_id"]." - ".$fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"];?></td>
<?php 
for($l=1;$l<=$numero_periodos;$l++){
	$notaDelEstudiante = Boletin::traerNotaBoletinCargaPeriodo($config, $l, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
?>
			<td class=""  align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;">
			<?php 
			if($notaDelEstudiante['bol_nota']!=""){
				if($datosUsr["mat_grado"]>11){
					$notaF = ceil($notaDelEstudiante['bol_nota']);
					/*
					switch($notaF){
						case 1: echo "D"; break;
						case 2: echo "I"; break;
						case 3: echo "A"; break;
						case 4: echo "S"; break;
						case 5: echo "E"; break;
					}
					*/
					echo $desempenoNotaP[1];
				}else{
					$desempenoNotaP = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaDelEstudiante['bol_nota'], $year);
					if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						echo $desempenoNotaP['notip_nombre'];
					}else{
						echo $notaDelEstudiante['bol_nota']."<br>".$desempenoNotaP['notip_nombre'];
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
	  $total_promedio2=round( $fila2["suma"],1);
	   
	   if($total_promedio2==1)	$total_promedio2="1.0";	if($total_promedio2==2)	$total_promedio2="2.0";		if($total_promedio2==3)	$total_promedio2="3.0";	if($total_promedio2==4)	$total_promedio2="4.0";	if($total_promedio2==5)	$total_promedio2="5.0";
	    $msj='';
	   if($total_promedio2<$config[5]){
			$consultaNivelaciones = Calificaciones::nivelacionEstudianteCarga($conexion, $config, $matriculadosDatos['mat_id'], $fila2['mat_id'], $year);
			$nivelaciones = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);

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
       
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php 
		$desempenoNotaXasig = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $total_promedio2, $year);
	
					if($datosUsr["mat_grado"]>11){
				$notaFI = ceil($total_promedio2);
						/*
				switch($notaFI){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				*/
						echo $desempenoNotaXasig['notip_nombre'];
						
				}else{
						$totalPromedio2Final=$total_promedio2;
						if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
						$estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $total_promedio2, $year);
						$totalPromedio2Final= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
						}
				echo $totalPromedio2Final;
				}
		
		?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php //DESEMPEÑO
		while($r_desempeno=mysqli_fetch_array($consulta_desempeno, MYSQLI_BOTH)){
			if($total_promedio2>=$r_desempeno["notip_desde"] && $total_promedio2<=$r_desempeno["notip_hasta"]){
				if($datosUsr["mat_grado"]>11){
					/*
					$notaFD = ceil($total_promedio2);
				switch($notaFD){
					case 1: echo "BAJO"; break;
					case 2: echo "BAJO"; break;
					case 3: echo "B&Aacute;SICO"; break;
					case 4: echo "ALTO"; break;
					case 5: echo "SUPERIOR"; break;					
				}
				*/
				echo $r_desempeno["notip_nombre"];

				}else{
					
						echo $r_desempeno["notip_nombre"];
					}
				}
			}
			mysqli_data_seek($consulta_desempeno,0);
			$matmaxaus='';
			if(!empty($fila2["matmaxaus"])){ $matmaxaus=$fila2["matmaxaus"];}
		 ?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php if(!empty($r_ausencias[0]) && $r_ausencias[0]>0){ echo $r_ausencias[0]."/".$matmaxaus;} else{ echo "0.0/".$matmaxaus;}?></td>
	
	</tr>
	
<?php
if($numIndicadores>0){
	 mysqli_data_seek($consulta_a_mat_indicadores,0);
	 $contador_indicadores=0;
	while($fila4=mysqli_fetch_array($consulta_a_mat_indicadores, MYSQLI_BOTH)){
	if($fila4["mat_id"]==$fila2["mat_id"]){
		$contador_indicadores++;
		$nota_indicador=round($fila4["nota"],1);
		 if($nota_indicador==1)	$nota_indicador="1.0";	if($nota_indicador==2)	$nota_indicador="2.0";		if($nota_indicador==3)	$nota_indicador="3.0";	if($nota_indicador==4)	$nota_indicador="4.0";	if($nota_indicador==5)	$nota_indicador="5.0";
		 
		$desempenoNotaInd = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota_indicador, $year);
	?>
<tr bgcolor="#FFF" style="font-size:12px;">
            <td style="font-size:12px; height:15px;"><?php echo $contador_indicadores.".".$fila4["ind_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($m=1;$m<=$numero_periodos;$m++){ 
			?>
			<td class=""  align="center" style="font-weight:bold;"><?php if($periodoActual==$m){
				if($datosUsr["mat_grado"]>11){
				$notaFII = ceil($nota_indicador);
					/*
				switch($notaFII){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
				*/
					echo $desempenoNotaInd['notip_nombre'];
			}else{
				$notaIndicadorFinal=$nota_indicador;
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
				  $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $nota_indicador, $year);
				  $notaIndicadorFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
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
?>
	<!-- observaciones de la asignatura-->
	<?php
	$observacion = Boletin::traerNotaBoletinCargaPeriodo($config, $periodoActual, $matriculadosDatos['mat_id'], $fila2['car_id'], $year);
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
		if(!empty($contpromedios[$n])){
			$notaFFF = round(($promedios[$n]/$contpromedios[$n]),1);
		}
		
		$desempenoNotaProm = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $notaFFF, $year);
		?>
        <td style="font-size:16px;">
        	<?php 
		if($promedios[$n]!=0){
			if($datosUsr["mat_grado"]>11){
				$notaFF = ceil(round(($promedios[$n]/$contpromedios[$n]),1));
				echo $desempenoNotaProm['notip_nombre'];
			}else{
				$promedioTotal= round(($promedios[$n]/$contpromedios[$n]),1);
				$promedioTotalFinal=$promedioTotal;
				if($config['conf_forma_mostrar_notas'] == CUALITATIVA){
				  $estiloNota = Boletin::obtenerDatosTipoDeNotas($config['conf_notas_categoria'], $promedioTotal, $year);
				  $promedioTotalFinal= !empty($estiloNota['notip_nombre']) ? $estiloNota['notip_nombre'] : "";
				}
				echo $promedioTotalFinal;
			}
			
			}?></td>
        <?php } ?>
        <td></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    
</table>

<?php for($n=1;$n<=$numero_periodos;$n++){if($promedios[$n]!=0){$promedios[$n]=0; $contpromedios[$n]=0;} } ?>

<p>&nbsp;</p>
<?php 
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".BD_DISCIPLINA.".disiplina_nota WHERE dn_cod_estudiante='".$matriculadosDatos['mat_id']."' AND institucion={$config['conf_id_institucion']} AND year={$year} AND dn_periodo in(".$condicion.");");
if(mysqli_num_rows($cndisiplina)>0){
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
// $consultaDesempenoND=mysqli_query($conexion, "SELECT * FROM ".BD_ACADEMICA.".academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$rndisiplina["dn_nota"].">=notip_desde AND ".$rndisiplina["dn_nota"]."<=notip_hasta AND institucion={$config['conf_id_institucion']} AND year={$year}");
// $desempenoND = mysqli_fetch_array($consultaDesempenoND, MYSQLI_BOTH);
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
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_primer_apellido']." ".$datosUsr['mat_segundo_apellido']." ".$datosUsr["mat_nombres"])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<$config["conf_num_materias_perder_agno"] and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_primer_apellido']." ".$datosUsr['mat_segundo_apellido']." ".$datosUsr["mat_nombres"])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr['mat_primer_apellido']." ".$datosUsr['mat_segundo_apellido']." ".$datosUsr["mat_nombres"])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
}
?>


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
