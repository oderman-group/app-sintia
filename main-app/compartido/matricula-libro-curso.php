<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
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
$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_REQUEST["curso"]."' AND mat_eliminado=0 AND mat_estado_matricula=1 ORDER BY mat_grupo, mat_primer_apellido LIMIT 0,5");
while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){
//contador materias
$contPeriodos=0;
$contadorIndicadores=0;
$materiasPerdidas=0;
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr=mysqli_query($conexion, "SELECT * FROM academico_matriculas am
INNER JOIN academico_grupos ON mat_grupo=gru_id
INNER JOIN academico_grados ON mat_grado=gra_id WHERE mat_id=".$matriculadosDatos[0]);
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
$consultaDesempeno=mysqli_query($conexion, "SELECT notip_id, notip_nombre, notip_desde, notip_hasta FROM academico_notas_tipos WHERE notip_categoria=".$config["conf_notas_categoria"].";");	
//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE
$consultaMatAreaEst=mysqli_query($conexion, "SELECT ar_id, car_ih FROM academico_cargas ac
INNER JOIN academico_materias am ON am.mat_id=ac.car_materia
INNER JOIN academico_areas ar ON ar.ar_id= am.mat_area
WHERE  car_curso=".$datosUsr["mat_grado"]." AND car_grupo=".$datosUsr["mat_grupo"]." GROUP BY ar.ar_id ORDER BY ar.ar_posicion ASC;");
//$numeroPeriodos=$config["conf_periodos_maximos"];
$numeroPeriodos=$config["conf_periodo"];
 ?>

<div align="center" style="margin-bottom:20px; font-weight:bold;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br><br>
    <!--<?=$informacion_inst["info_nombre"]?><br>-->
    REGISTRO DE VALORACIÓN<br>
</div> 

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:10px;">
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
<table width="100%" align="left">
<tr style="border:solid; font-weight:bold; color:#000; font-size:10px;">
    <td width="20%" align="center">AREAS/ ASIGNATURAS</td>
    <td width="2%" align="center">I.H</td>
    <td width="4%" align="center">DEF</td>
    <td width="8%" align="center">DESEMPE&Ntilde;O</td>   
    <td width="2%" align="center">AUS</td>
    <td width="15%" align="center">OBSERVACIONES</td>
</tr> 

    <tr>
    	<td class="area" colspan="<?=$columnas;?>" style="font-size:10px; font-weight:bold;"></td>
    </tr>

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
WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
GROUP BY ar_id;");
//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA
$consultaAMat=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre,mat_nombre,mat_id,car_id FROM academico_materias am
INNER JOIN academico_areas a ON a.ar_id=am.mat_area
INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
GROUP BY mat_id
ORDER BY mat_id;");
//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO
$consultaAMatPer=mysqli_query($conexion, "SELECT bol_nota,bol_periodo,ar_nombre,mat_nombre,mat_id FROM academico_materias am
INNER JOIN academico_areas a ON a.ar_id=am.mat_area
INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id
INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id
WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")
ORDER BY mat_id,bol_periodo
;");


$resultadoNotArea=mysqli_fetch_array($consultaNotdefArea, MYSQLI_BOTH);
$numfilasNotArea=mysqli_num_rows($consultaNotdefArea);
$totalPromedio=round( $resultadoNotArea["suma"],1);


if($totalPromedio==1)	$totalPromedio="1.0";	if($totalPromedio==2)	$totalPromedio="2.0";		if($totalPromedio==3)	$totalPromedio="3.0";	if($totalPromedio==4)	$totalPromedio="4.0";	if($totalPromedio==5)	$totalPromedio="5.0";
	if($numfilasNotArea>0){
			?>
  <tr style="font-size:10px;">
            <td style="font-size:10px; font-weight:bold;"><?php echo $resultadoNotArea["ar_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:10px;"></td>
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

while($fila2=mysqli_fetch_array($consultaAMat, MYSQLI_BOTH)){ 
	$contadorPeriodos=0;
	mysqli_data_seek($consultaAMatPer,0);
	//CONSULTAR NOTA POR PERIODO
	while($fila3=mysqli_fetch_array($consultaAMatPer, MYSQLI_BOTH)){
		if($fila2["mat_id"]==$fila3["mat_id"]){
			$contadorPeriodos++;
			$notaPeriodo=round($fila3["bol_nota"],1);
			if($notaPeriodo==1)$notaPeriodo="1.0";	if($notaPeriodo==2)$notaPeriodo="2.0";	if($notaPeriodo==3)$notaPeriodo="3.0";	if($notaPeriodo==4)$notaPeriodo="4.0";	if($notaPeriodo==5)$notaPeriodo="5.0";
			$notas[$contadorPeriodos] =$notaPeriodo;
		}
	}//FIN FILA3
?>
 <tr style="font-size:10px;">
            <td style="font-size:10px;"><?php echo $fila2["mat_nombre"];?></td> 
            <td align="center" style="font-weight:bold; font-size:10px;"><?php echo $fila["car_ih"];?></td>
<?php for($l=1;$l<=$numeroPeriodos;$l++){ 
	$consultaNotaEstudiante=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_carga='".$fila2['car_id']."' AND bol_estudiante='".$matriculadosDatos[0]."' AND bol_periodo='".$l."'");
	$notaDelEstudiante = mysqli_fetch_array($consultaNotaEstudiante, MYSQLI_BOTH);
?>

			<?php 
			if($notaDelEstudiante['bol_nota']!=""){
				$consultaDesempenoNotaP=mysqli_query($conexion, "SELECT * FROM academico_notas_tipos WHERE notip_categoria='".$config[22]."' AND ".$notaDelEstudiante['bol_nota'].">=notip_desde AND ".$notaDelEstudiante['bol_nota']."<=notip_hasta");
				$desempenoNotaP = mysqli_fetch_array($consultaDesempenoNotaP, MYSQLI_BOTH);
				if($datosUsr["mat_grado"]>11){
					$notaF = ceil($notaDelEstudiante['bol_nota']);
					switch($notaF){
						case 1: echo "D"; break;
						case 2: echo "I"; break;
						case 3: echo "A"; break;
						case 4: echo "S"; break;
						case 5: echo "E"; break;
					}
				}else{
					 $notaDelEstudiante['bol_nota']."<br>".$desempenoNotaP[1];
					//echo $notas[$l]."<br>".$desempenoNotaP[1];
				}
				$promedios[$l]=$promedios[$l]+$notaDelEstudiante['bol_nota'];
				$contpromedios[$l]=$contpromedios[$l]+1;
			}else{}
			?>

        <?php }?>
      <?php 
	  $totalPromedio2=round( $fila2["suma"],1);
	   
	   if($totalPromedio2==1)	$totalPromedio2="1.0";	if($totalPromedio2==2)	$totalPromedio2="2.0";		if($totalPromedio2==3)	$totalPromedio2="3.0";	if($totalPromedio2==4)	$totalPromedio2="4.0";	if($totalPromedio2==5)	$totalPromedio2="5.0";
	   //if($totalPromedio2<$rDesempeno["desbasdesde"]){$materiasPerdidas++;}
	    $msj='';
	   if($totalPromedio2<$config[5]){
			$consultaNivelaciones=mysqli_query($conexion, "SELECT * FROM  academico_nivelaciones WHERE niv_id_asg='".$fila2['car_id']."' AND niv_cod_estudiante='".$matriculadosDatos[0]."'");
		   $nivelaciones = mysqli_fetch_array($consultaNivelaciones, MYSQLI_BOTH);
		   if($nivelaciones[3]<$config[5]){
				$materiasPerdidas++;
			}else{
				$totalPromedio2 = $nivelaciones[3];
				$msj='Niv';
			}
		   
		   }
	   ?>
       
        <td align="center" style="font-weight:bold; "><?php 
		
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
        <td align="center" style="font-weight:bold;"><?php //DESEMPEÑO
		while($rDesempeno=mysqli_fetch_array($consultaDesempeno, MYSQLI_BOTH)){
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
			mysqli_data_seek($consultaDesempeno,0);
		 ?></td>
        <td align="center" style="font-weight:bold; "><?php if($rAusencias[0]>0){ echo $rAusencias[0]."/".$fila2["matmaxaus"];} else{ echo "0.0/".$fila2["matmaxaus"];}?></td>
        
        <td align="center">_______________________________________</td>
	
	</tr>
<?php
}//while fin materias
?>  
<?php }}//while fin areas?>	 

    
</table>

<p>&nbsp;</p>


</div>


<p>&nbsp;</p>


<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" style="text-align:center; font-size:10px;">
	<tr>
		<td align="center">_________________________________<br>Victor Cabrera<br>Rector(a)</td>
		<td align="center">_________________________________<br>Cristhell Orozco<br>Secretaria Académica</td>
    </tr>
</table> 






</div>  
<?php 
if($periodoActual==4){
	if($materiasPerdidas>=$config["conf_num_materias_perder_agno"])
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr[3]." ".$datosUsr[4]." ".$datosUsr["mat_nombres"])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<$config["conf_num_materias_perder_agno"] and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr[3]." ".$datosUsr[4]." ".$datosUsr["mat_nombres"])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datosUsr[3]." ".$datosUsr[4]." ".$datosUsr["mat_nombres"])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
}
?>


<p align="center">

<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:10px;" align="center"><?=$msj;?></div>

</p>					                   

 <div id="saltoPagina"></div>
                                    
<?php
 }// FIN DE TODOS LOS MATRICULADOS
?>
<script type="application/javascript">
//print();
</script>                                    
                          
</body>
</html>