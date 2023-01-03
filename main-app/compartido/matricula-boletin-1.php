<?php include("../directivo/session.php");?>
<?php include("../../config-general/config.php");?>
<?php
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
//contador materias
$contPeriodos=0;
$contadorIndicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr=mysqli_query($conexion, "SELECT * FROM academico_matriculas am
INNER JOIN academico_grupos ON mat_grupo=gru_id
INNER JOIN academico_grados ON mat_grado=gra_id WHERE mat_id=".$_GET["id"]);
$numUsr=mysqli_num_rows($usr);
$datosUsr=mysqli_fetch_array($usr, MYSQLI_BOTH);
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
</head>

<body style="font-family:Arial;">
<?php
//CONSULTA QUE ME TRAE EL DESEMPEÑO
$consultaDesempeno=mysqli_query($conexion, "SELECT notip_id, notip_nombre, notip_desde, notip_hasta FROM academico_notas_tipos WHERE notip_categoria=".$config["conf_notas_categoria"].";");	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst=mysqli_query($conexion, "SELECT ar_id, car_ih FROM academico_cargas ac
INNER JOIN academico_materias am ON am.mat_id=ac.car_materia
INNER JOIN academico_areas ar ON ar.ar_id= am.mat_area
WHERE  car_curso=".$datosUsr["mat_grado"]." AND car_grupo=".$datosUsr["mat_grupo"]." GROUP BY ar.ar_id ORDER BY ar.ar_posicion ASC;");
//$numeroPeriodos=$config["conf_periodos_maximos"];
$numeroPeriodos=2;
 ?>

<div align="center" style="margin-bottom:20px;">
    <!--<img src="enca.png"><br>-->
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="200"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    BOLET&Iacute;N DE CALIFICACIONES<br>
</div> 

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">
    <tr>
    	<td>C&oacute;digo: <b><?=$datosUsr["mat_matricula"];?></b></td>
        <td>Nombre: <b><?=strtoupper($datosUsr[3]." ".$datosUsr[4]." ".$datosUsr["mat_nombres"]);?></b></td>   
    </tr>
    
    <tr>
    	<td>Grado: <b><?=$datosUsr["gra_nombre"]." ".$datosUsr["gru_nombre"];?></b></td>
        <td>Periodo: <b><?=strtoupper($periodoActuales);?></b></td>    
    </tr>
</table>


<br>
<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">
<tr style="font-weight:bold; background:#EAEAEA; border-color:#000; height:20px; color:#000; font-size:12px;">
<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
<td width="2%" align="center">I.H</td>
<!--
<?php for($j=1;$j<=$numeroPeriodos;$j++){ ?>
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$_GET["id"];?>&periodo=<?=$j?>" style="color:#000; text-decoration:underline;">2P</a></td>
<?php }?>
-->
<td width="3%" align="center" style="font-size:10px;">NOTAS<br>ASIGNATURA</td>
<td width="3%" align="center" style="font-size:10px;">NOTAS<br>LOGROS</td>

<td width="4%" align="center">PRO</td>
<!--<td width="5%" align="center">PER</td>-->
<td width="8%" align="center">DESEMPE&Ntilde;O</td>   
<td width="5%" align="center">AUS</td>
</tr> 

    <tr style="background:#F06;">
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
$consultaNotdefArea=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre FROM academico_materias am
INNER JOIN academico_areas a ON a.ar_id=am.mat_area
INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$_GET["id"]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
GROUP BY ar_id;");
//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consultaMat=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre,mat_nombre,mat_id FROM academico_materias am
INNER JOIN academico_areas a ON a.ar_id=am.mat_area
INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$_GET["id"]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
GROUP BY mat_id
ORDER BY mat_id;");
//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consultaMatPer=mysqli_query($conexion, "SELECT bol_nota,bol_periodo,ar_nombre,mat_nombre,mat_id FROM academico_materias am
INNER JOIN academico_areas a ON a.ar_id=am.mat_area
INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$_GET["id"]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
ORDER BY mat_id,bol_periodo
;");


//CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA
$consultaMatIndicadores=mysqli_query($conexion, "SELECT mat_nombre,mat_area,mat_id,ind_nombre,ipc_periodo,(SUM(cal_nota)/COUNT(cal_nota))as nota FROM academico_materias am
INNER JOIN academico_areas a ON a.ar_id=am.mat_area
INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN academico_indicadores_carga aic ON aic.ipc_carga=ac.car_id
INNER JOIN academico_indicadores ai ON aic.ipc_indicador=ai.ind_id
INNER JOIN academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND act_id_carga=car_id
INNER JOIN academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id
WHERE car_curso=".$datosUsr["mat_grado"]."  and car_grupo=".$datosUsr["mat_grupo"]." and mat_area=".$fila["ar_id"]." AND ipc_periodo in (".$condicion.") AND cal_id_estudiante='".$_GET["id"]."' and act_periodo=".$condicion2."
group by act_id_tipo, act_id_carga
order by mat_id,ipc_periodo,ind_id;");

$numIndicadores=mysqli_num_rows($consultaMatIndicadores);

$resultadoNotArea=mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
$numfilasNotArea=mysqli_num_rows($consultaNotdefArea);
$totalPromedio=round( $resultadoNotArea["suma"],1);


if($totalPromedio==1)	$totalPromedio="1.0";	if($totalPromedio==2)	$totalPromedio="2.0";		if($totalPromedio==3)	$totalPromedio="3.0";	if($totalPromedio==4)	$totalPromedio="4.0";	if($totalPromedio==5)	$totalPromedio="5.0";
	if($numfilasNotArea>0){
			?>
  <tr bgcolor="#ABABAB" style="font-size:12px;">
            <td style="font-size:12px; height:25px; font-weight:bold;"><?php echo $resultadoNotArea["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"></td>
            <?php for($k=1;$k<=$numeroPeriodos;$k++){ 
			?>
			<td class=""  align="center" style="font-weight:bold;"></td>
            <?php }?>
        <td align="center" style="font-weight:bold;"><?php echo $totalPromedio;?></td>
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
            <td style="font-size:12px; height:35px; font-weight:bold; color:#000;">&raquo;<?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:12px;"><?php echo $fila["car_ih"];?></td>
<?php for($l=1;$l<=$numeroPeriodos;$l++){ ?>
			

			<td class=""  align="center" style="font-weight:bold; background:#EAEAEA; font-size:16px;"><?php $desempenoNotaP = mysqli_fetch_array(mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$notas[$l].">=notip_desde AND ".$notas[$l]."<=notip_hasta"));
			 echo $notas[$l]."<br>".$desempenoNotaP[1];
			
			
			$promedios[$l]=$promedios[$l]+$notas[$l];
			$contpromedios[$l]=$contpromedios[$l]+1;
			?></td>
            
        <?php }?>
      <?php 
	  $totalPromedio2=round( $fila2["suma"],1);
	   
	   if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
	   if($totalPromedio2<$r_desempeno["desbasdesde"]){$materiasPerdidas++;}
	   ?>
       
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?=$totalPromedio2;?></td>
        <td align="center" style="font-weight:bold; background:#EAEAEA;"><?php //DESEMPEÑO
		while($rDesempeno=mysqli_fetch_array($consultaDesempeno, MYSQLI_BOTH)){
			if($totalPromedio2>=$rDesempeno["notip_desde"] && $totalPromedio2<=$rDesempeno["notip_hasta"]){
				echo $rDesempeno["notip_nombre"];
				}
			}
			mysqli_data_seek($consultaDesempeno,0);
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
			<td class=""  align="center" style="font-weight:bold;"><?php if($periodoActual==$m){echo $notaIndicador;} ?></td>
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
        <td style="font-size:16px;"><?php if($promedios[$n]!=0){echo round(($promedios[$n]/$contpromedios[$n]),1);}?></td>
        <?php } ?>
        <td></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    
</table>

<p>&nbsp;</p>
<?php 
$cndisiplina = mysqli_query($conexion, "SELECT * FROM disiplina_nota WHERE dn_cod_estudiante='".$_GET["id"]."' AND dn_periodo in(".$condicion.");");
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
$consultaDesempenoND=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$rndisiplina["dn_nota"].">=notip_desde AND ".$rndisiplina["dn_nota"]."<=notip_hasta");
$desempenoND = mysqli_fetch_array($consultaDesempenoND, MYSQLI_BOTH);
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
    
    <?php if($num_observaciones>0){?>COMPORTAMIENTO:<?php }?> <b><u><?=strtoupper($r_diciplina[3]);?></u></b><br>
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
 
<div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
                          
</body>
</html>
