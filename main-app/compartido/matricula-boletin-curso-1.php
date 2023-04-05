<?php include("../directivo/session.php");
include("../class/Estudiantes.php");
    
$year=$agnoBD;
if(isset($_GET["year"])){
$year=$_GET["year"];
}
$BD=$_SESSION["inst"]."_".$year;

$modulo = 1;
if($_GET["periodo"]==""){
	$periodoActual = 1;
}else{
	$periodoActual = $_GET["periodo"];
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
if(is_numeric($_GET["id"])){$filtro .= " AND mat_id='".$_GET["id"]."'";}
if(is_numeric($_REQUEST["curso"])){$filtro .= " AND mat_grado='".$_REQUEST["curso"]."'";}
if(is_numeric($_REQUEST["grupo"])){$filtro .= " AND mat_grupo='".$_REQUEST["grupo"]."'";}

$matriculadosPorCurso = Estudiantes::estudiantesMatriculados($filtro, $BD);
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$contPeriodos=0;
$contadorIndicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr =Estudiantes::obtenerDatosEstudiantesParaBoletin($matriculadosDatos[0],$BD);
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
$consultaDesempeno1=mysqli_query($conexion, "SELECT notip_id, notip_nombre, notip_desde, notip_hasta FROM $BD.academico_notas_tipos WHERE notip_categoria=".$config["conf_notas_categoria"].";");	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst=mysqli_query($conexion, "SELECT ar_id, car_ih FROM $BD.academico_cargas ac
INNER JOIN $BD.academico_materias am ON am.mat_id=ac.car_materia
INNER JOIN $BD.academico_areas ar ON ar.ar_id= am.mat_area
WHERE  car_curso=".$datosUsr["mat_grado"]." AND car_grupo=".$datosUsr["mat_grupo"]." GROUP BY ar.ar_id ORDER BY ar.ar_posicion ASC;");
//$numeroPeriodos=$config["conf_periodos_maximos"];
$numeroPeriodos=2;
 ?>


<?php
$nombreInforme = "BOLETÍN DE CALIFICACIONES";
include("../compartido/head_informes.php") ?>

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
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$matriculadosDatos[0];?>&periodo=<?=$j?>" style="color:#000; text-decoration:underline;">2P</a></td>
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
$consultaNotdefArea=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre FROM $BD.academico_materias am
INNER JOIN $BD.academico_areas a ON a.ar_id=am.mat_area
INNER JOIN $BD.academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN $BD.academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
GROUP BY ar_id;");
//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consultaMat=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre,mat_nombre,mat_id FROM $BD.academico_materias am
INNER JOIN $BD.academico_areas a ON a.ar_id=am.mat_area
INNER JOIN $BD.academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN $BD.academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
GROUP BY mat_id
ORDER BY mat_id;");
//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consultaMatPer=mysqli_query($conexion, "SELECT bol_nota,bol_periodo,ar_nombre,mat_nombre,mat_id FROM $BD.academico_materias am
INNER JOIN $BD.academico_areas a ON a.ar_id=am.mat_area
INNER JOIN $BD.academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN $BD.academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
ORDER BY mat_id,bol_periodo
;");


//CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
$consultaMatIndicadores=mysqli_query($conexion, "SELECT mat_nombre,mat_area,mat_id,ind_nombre,ipc_periodo,(SUM(cal_nota)/COUNT(cal_nota))as nota FROM $BD.academico_materias am
INNER JOIN $BD.academico_areas a ON a.ar_id=am.mat_area
INNER JOIN $BD.academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN $BD.academico_indicadores_carga aic ON aic.ipc_carga=ac.car_id
INNER JOIN $BD.academico_indicadores ai ON aic.ipc_indicador=ai.ind_id
INNER JOIN $BD.academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND act_id_carga=car_id
INNER JOIN $BD.academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id
WHERE car_curso=".$datosUsr["mat_grado"]."  and car_grupo=".$datosUsr["mat_grupo"]." and mat_area=".$fila["ar_id"]." AND ipc_periodo in (".$condicion.") AND cal_id_estudiante='".$matriculadosDatos[0]."' and act_periodo=".$condicion2."
group by act_id_tipo, act_id_carga
order by mat_id,ipc_periodo,ind_id;");

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
		echo $totalPromedio;
				}
		
		?></td>
         <td align="center" style="font-weight:bold;"></td>
          <td align="center" style="font-weight:bold;"></td>
	</tr>
<?php

while($fila2=mysqli_fetch_array($consultaMat, MYSQLI_BOTH)){ 
$contadorPeriodos=0;
	  mysqli_data_seek($consultaMatPer,0);
	 //CONSULTAR NOTA POR PERIODO
while($fila3=mysqli_fetch_array($consultaMatPer, MYSQLI_BOTH)){
	
	 if($fila2["mat_id"]==$fila3["mat_id"]){
	  $contadorPeriodos++;
	  $notaPeriodo=round($fila3["bol_nota"],1);
	  if($notaPeriodo==1)	$notaPeriodo="1.0";	if($notaPeriodo==2)	$notaPeriodo="2.0";		if($notaPeriodo==3)	$notaPeriodo="3.0";	if($notaPeriodo==4)	$notaPeriodo="4.0";	if($notaPeriodo==5)	$notaPeriodo="5.0";
	  $notas[$contadorPeriodos] =$notaPeriodo;
	 }
	}
?>
 <tr bgcolor="#EAEAEA" style="font-size:12px;">
            <td style="font-size:12px; height:35px; font-weight:bold;background:#EAEAEA;">&raquo;<?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;background:#EAEAEA;"><?php echo $fila["car_ih"];?></td>
<?php for($l=1;$l<=$numeroPeriodos;$l++){ ?>
			<td class=""  align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;">
			<?php $consultaDesempenoNotaP=mysqli_query($conexion, "SELECT * FROM $BD.academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$notas[$l].">=notip_desde AND ".$notas[$l]."<=notip_hasta");
			$desempenoNotaP = mysqli_fetch_array($consultaDesempenoNotaP, MYSQLI_BOTH);
			if($datosUsr["mat_grado"]>11){
				$notaF = ceil($notas[$l]);
				switch($notaF){
					case 1: echo "D"; break;
					case 2: echo "I"; break;
					case 3: echo "A"; break;
					case 4: echo "S"; break;
					case 5: echo "E"; break;
				}
			}else{
				echo $notas[$l]."<br>".$desempenoNotaP[1];
			}
			
			$promedios[$l]=$promedios[$l]+$notas[$l];
			$contpromedios[$l]=$contpromedios[$l]+1;
			?></td>
        <?php }?>
      <?php 
	  $totalPromedio2=round( $fila2["suma"],1);
	   
	   if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
	   if($totalPromedio2<$rDesempeno["desbasdesde"]){$materiasPerdidas++;}
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
					echo $totalPromedio2;
				}
		
		?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php //DESEMPEÑO
		while($rDesempeno=mysqli_fetch_array($consultaDesempeno1, MYSQLI_BOTH)){
			if($totalPromedio2>=$rDesempeno["notip_desde"] && $totalPromedio2<=$rDesempeno["notip_hasta"]){
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
			}
			mysqli_data_seek($consultaDesempeno1,0);
		 ?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php if($r_ausencias[0]>0){ echo $r_ausencias[0]."/".$fila2["matmaxaus"];} else{ echo "0.0/".$fila2["matmaxaus"];}?></td>
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
				echo $notaIndicador;
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
					echo round(($promedios[$n]/$contpromedios[$n]),1);
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
$cndisiplina = mysqli_query($conexion, "SELECT * FROM ".$BD.".disiplina_nota WHERE dn_cod_estudiante=".$matriculadosDatos[0]." AND dn_periodo in(".$condicion.");");
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
$consultaDesempeno=mysqli_query($conexion, "SELECT * FROM $BD.academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$rndisiplina["dn_nota"].">=notip_desde AND ".$rndisiplina["dn_nota"]."<=notip_hasta");
$desempenoND = mysqli_fetch_array($consultaDesempeno, MYSQLI_BOTH);
?>
    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">
        <td><?=$rndisiplina["dn_periodo"]?></td>
        <td><?=$desempenoND[1]?></td>
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
if($periodoActual==4){
	if($materiasPerdidas>=$config["conf_num_materias_perder_agno"])
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr[4])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<3 and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr[4])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr[4])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
}
?>


<p align="center">

<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:12px;" align="center"><?=$msj;?></div>

</p>
<?php include("../compartido/footer-informes.php") ?>;				                   
<!-- 
<div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
                                    -->
 <div id="saltoPagina"></div>
                                    
<?php
 }// FIN DE TODOS LOS MATRICULADOS
?>

<script type="application/javascript">
print();
</script>                                    
                          
</body>
</html>
