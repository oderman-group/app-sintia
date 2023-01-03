<?php
if($_GET["periodo"]==""){
	$periodoActual = 1;
}else{
	$periodoActual = $_GET["periodo"];
}
include("../../config-general/config.php");
//include("verificar_periodo.php");
//include("verificar_asignacion.php");
//include("../modell/conexion.php");
//include("../institucion.php");?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<?php
//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================
$usr=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_id=".$_GET["id"]);

$num_usr=mysqli_num_rows($usr);
$datos_usr=mysqli_fetch_array($usr, MYSQLI_BOTH);
if($num_usr==0)
{
?>
	<script type="text/javascript">
		window.close();
	</script>
<?php
	exit();
}
//=============================== MATERIAS DEL ESTUDIANTE =================
$mat=mysqli_query($conexion, "SELECT * FROM academico_cargas WHERE car_curso=".$datos_usr[6]." AND car_grupo='".$datos_usr[7]."' ORDER BY car_materia");

$num_mat=mysqli_num_rows($mat);
if($num_mat==0)
{
	header("Location:cargas.php");
	exit();
}
if($asg[2]<6)
	$tipo="EDUCACION BASICA PRIMARIA";
elseif($asg[2]>=6 and $asg[2]<=9)
	$tipo="EDUCACION BASICA SECUNDARIA";
elseif($asg[2]>9 and $asg[2]<=11)
	$tipo="EDUCACION MEDIA";
?>
<title>Informe Acad&eacute;mico</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">

<body style="font-family:Arial;">


<?php
if($periodoActual==1) $periodoActuales = "Primero";
if($periodoActual==2) $periodoActuales = "Segundo";
if($periodoActual==3) $periodoActuales = "Tercero";
if($periodoActual==4) $periodoActuales = "Final";
//if($periodoActual==5) $periodoActuales = "Final";
?>

<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/oe.png" height="150" width="150"><br>
    INSTITUTO COLOMO VENEZOLANO<br>
    INFORME ACAD&Eacute;MICO</br>
</div>  

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:11px;">
    <tr>
    	<td>C&oacute;digo: <b><?=$datos_usr[1];?></b></td>
        <td>Nombre: <b><?=strtoupper($datos_usr[3]." ".$datos_usr[4]." ".$datos_usr[5]);?></b></td>   
    </tr>
</table>


<br>
<table width="100%" cellspacing="0" cellpadding="0" rules="none" border="0" align="left">
<tr style="font-weight:bold; background:#ECECEC; height:10px; color:#000; font-size:10px;">
<td width="20%" align="center">AREAS/ ASIGNATURAS</td>
<td width="2%" align="center">I.H</td>
<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$_GET["id"];?>&periodo=1" style="color:#0000FF; text-decoration:underline;">1P</a></td>
<td width="4%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$_GET["id"];?>&periodo=2" style="color:#0000FF; text-decoration:underline;">2P</a></td>
<td width="4%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$_GET["id"];?>&periodo=3" style="color:#0000FF; text-decoration:underline;">3P</a></td>
<td width="4%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$_GET["id"];?>&periodo=4" style="color:#0000FF; text-decoration:underline;">4P</a></td>
<td width="4%" align="center">PRO</td>
<!--<td width="5%" align="center">PER</td>-->
<td width="8%" align="center">DESEMPE&Ntilde;O</td>   
<td width="5%" align="center">AUS</td>
</tr> 
<?php
$cont=1;
$contador = 0;  //para que solo halla un while en area
$totalDefini=0;
$materiasPerdidas=0;
$materia=mysqli_query($conexion, "SELECT  ar_id, ar_nombre, mat_nombre, mat_id, mat_area, car_id, car_materia, car_periodo FROM academico_areas, academico_materias, academico_cargas WHERE (ar_id=mat_area AND mat_id=car_materia) AND (car_curso=".$datos_usr[6]." AND car_grupo=".$datos_usr[7].") AND ar_id=mat_area GROUP BY ar_id ORDER BY ar_posicion");

$ii = 1;
while($fila_mat=mysqli_fetch_array($materia, MYSQLI_BOTH)){
if($ii%2==0)$bgC = '#FFF'; else $bgC = '#E0E0E0';
?>
    <tr style="background:#F3F3F3;">
    	<td class="area" id="<?=$fila_mat[0]?>" colspan="9" style="font-size:10px; font-weight:bold;"><?php if(strtoupper(substr($fila_mat[1],0,4))=='ESPA') echo "ESPA&Ntilde;OL"; else echo strtoupper($fila_mat[1]);?></td>
    </tr>
<?php 
	$consulta = mysqli_query($conexion, "SELECT  mat_nombre, mat_area, mat_id, car_id FROM academico_areas, academico_materias, academico_cargas WHERE (ar_id=mat_area AND mat_id=car_materia)AND(car_curso=".$datos_usr[6]." AND car_grupo=".$datos_usr[7].") and ar_id=mat_area and mat_area=".$fila_mat[4]);
	
	while($fila = mysqli_fetch_array($consulta, MYSQLI_BOTH)){	

		$periodo=$fila_mat[7]-1; //asperiodo
		$datos=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_carga=".$fila[3]." AND bol_estudiante=".$_GET["id"]." AND  bol_periodo=".$periodoActual);//asmat
		
		$dato=mysqli_fetch_array($datos, MYSQLI_BOTH);
		$numero=mysqli_num_rows($datos);
		if($numero>0)$acumu=$acumu+1;
?>
        <!-- Aca ira un while con los indiracores, dentro de los cuales debera ir otro while con las notas de los indicadores-->
        <tr bgcolor="<?=$bgC;?>" style="font-size:8px;">
            <td style="font-size:8px; height:15px; font-weight:bold;">&raquo; <?php if(strtoupper(substr($fila[0],0,4))=='ESPA') echo "ESPA&Ntilde;OL"; else echo strtoupper($fila[0]);?> </td> 
            <td align="center" style="font-weight:bold; font-size:8px;"><?php echo $ih[3];?></td>
<?php
		//notas de los periodos anteriores hasta el actual
		$defini = 0;
		$prom=0;
		$per=1;
		while($per<=4){
			
			//============================ NOTA DE CADA UNO DE LOS PERIODOS  POR SEPARADO =====================================================
			$notas=mysqli_query($conexion, "SELECT * FROM academico_boletin WHERE bol_carga=".$fila[3]." AND bol_estudiante=".$_GET["id"]." AND bol_periodo=".$per);
			$nota=mysqli_fetch_array($notas, MYSQLI_BOTH);
			//============================ FIN DE LAS NOTAS POR SEPARADO ======================================================================	
?>
			<td class="<?=$per;?>"  align="center" style="font-weight:bold; border:groove;"><?php  echo $nota[4]; $defini = $defini + $nota[4]; $prome = $nota[4];?></td>
<?php 
			$vectorT[$fila[1]][$per] = $prome;
			
			if($per==1){
				$prom1 = $prom1+$prome;
			}
			if($per==2){
				$prom2 = $prom2+$prome;
				$promedioArea2 = $promedioArea2 + $prome;
			}
			if($per==3){
				$prom3 = $prom3+$prome;
				$promedioArea3 = $promedioArea3 + $prome;
			}
			if($per==4){
				$prom4 = $prom4+$prome;
				$promedioArea4 = $promedioAre4 + $prome;
			}
			$per++;
		}//FIN MIENTRAS QUE DE PERIODOS (1-4)
		$defini = ($defini/$periodoActual);
		$defini = round($defini,1);
		$nivelaciones = mysqli_query($conexion, "SELECT * FROM academico_nivelaciones WHERE niv_id_asg=".$fila_mat[5]." AND niv_cod_estudiante=".$_GET["id"]);
		$numNivelaciones = mysqli_num_rows($nivelaciones);
		$notasNivelaciones = mysqli_fetch_array($nivelaciones, MYSQLI_BOTH);
		if($numNivelaciones>0){
			if($notasNivelaciones[3]>$defini){
				$defini = $notasNivelaciones[3];
				$msjH = '<br><span style="font-size:9px; color:red;">Nivelada</span>';
			}	
		}else{$msjH = '';}
		if($defini<3){$materiasPerdidas++;}	
		$totalDefini = $totalDefini+$defini;
		if($defini==1)	$defini="1.0";	if($defini==2)	$defini="2.0";		if($defini==3)	$defini="3.0";	if($defini==4)	$defini="4.0";	if($defini==5)	$defini="5.0";
		$defini = $defini/4;
?>
    	<td align="center" style="font-weight:bold; font-size:11px; border:groove;"><?php echo $defini." ".$msjH;?></td>	
<?php 
		$definitiva=substr($definitiva,0,3);
		$final=$dato[4];
?>
		<td align="center" style="font-weight:bold; font-size:11px; border:groove;">
<?php
			if($periodoActual==4){
			if($defini>=$cde[1] and $defini<=$cde[2]) echo "SUPERIOR"; elseif($defini>=$cde[3] and $defini<=$cde[4]) echo "ALTO"; elseif($defini>=3 and $defini<=$cde[6]) echo "B&Aacute;SICO"; elseif($defini>=$cde[7] and $defini<=$cde[8]) echo "<span style='background:#FFFF00;'>BAJO</span>"; else echo "-";
			}else{
				if($final>=$cde[1] and $final<=$cde[2]) echo "SUPERIOR"; elseif($final>=$cde[3] and $final<=$cde[4]) echo "ALTO"; elseif($final>=3 and $final<=$cde[6]) echo "B&Aacute;SICO"; elseif($final>=$cde[7] and $final<=$cde[8]) echo "BAJO"; else echo "-";
			}
?>
    	</td>
    	<td align="center"><?php if($aus[0]>0){ echo $aus[0]."/".$fila_mat[3];} else{ echo "0.0/".$fila_mat[3];};?></td>
	</tr>
<?php
  	$consulta_2 =  mysqli_query($conexion, "SELECT ind_id, ind_nombre, ipc_valor, ipc_periodo FROM academico_indicadores, academico_indicadores_carga WHERE ind_id=ipc_indicador AND ipc_periodo=".$periodoActual." AND ipc_carga=".$fila[3]);
	$num = mysqli_num_rows($consulta);
	if ($num>0) // si tiene indicadores 
	{
		$ind = 1;
		while ($indicador = mysqli_fetch_array($consulta_2, MYSQLI_BOTH)){ //While indicador
?>  
	 <tr style="font-size:8px; text-align:justify; background:<?=$bgC;?>;"> 
         <td align="justify"><?php echo $ind.". ".$indicador[1];?></td>
         <td></td>
         <td class="a"></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
     </tr>  
<?php 
		$ind ++;
		$a = $_GET['id'];
		$reg = mysqli_query($conexion, "SELECT * FROM academico_calificaciones, academico_actividades WHERE cal_id_actividad in(SELECT act_id FROM academico_actividades WHERE act_id_carga=".$fila[3]." and act_id_tipo=".$indicador[0]." and act_periodo=".$periodoActual.") and cal_id_estudiante=".$_GET['id']." and cal_id_actividad=act_id");
		$num = mysqli_num_rows($reg);
    	$contador = 0;
		while ($nota = mysqli_fetch_array($reg, MYSQLI_BOTH)){ //While de notas
		?>
           <tr bgcolor="#E0E0E0" class="nota" bgcolor="AliceBlue" style="font-size:8px;"> <!-- Para ls notas pero en este caso no se mostrara, con esto se procesara y se utilizara el prev() -->
                <td colspan="9"></td>                  
           </tr> 
<?php
		  //Todo este codigo es para sacar el resultado de cada indicador
		   $array[$contador] = $nota[1];
		   $contador++;
		} //End while notas 
		if ($num>0)
		{
		 	$acumulador = 0;
   			$capacidad_array = count($array);
			for ($i=0;$i<$capacidad_array;$i++)
			{
			 $acumulador = $acumulador + $array[$i];
			}
			unset($array); //Destruye el array para borrar datos tenidos
			$sacadaDef = $acumulador/$capacidad_array;
			if($sacadaDef==1) $sacadaDef="1.0";
			$sacadaDef = round($sacadaDef,1);
?> 
              <script>
			  
			  $('.newClass').removeClass();
			  
			  $('.nota').first().attr('class', 'newClass');
			  
			  $('.nota').removeClass();
			  
			  var espacios = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			  //$('.newClass').prev().children().next().next().next().next().html("");
			  </script>
<?php
if($periodoActual==1){?>
	<script>
    $('.newClass').prev().children().next().next().html(espacios+<?php echo $sacadaDef;?>);
    $('.newClass').prev().children().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().next().next().html("");
    </script>
<?php }
if($periodoActual==2){?>
	<script>
    $('.newClass').prev().children().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().html(espacios+<?php echo $sacadaDef;?>);
    $('.newClass').prev().children().next().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().next().next().html("");
    </script>
<?php
}
if($periodoActual==3){?>
	<script>
    $('.newClass').prev().children().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().html(espacios+<?php echo $sacadaDef;?>);
    $('.newClass').prev().children().next().next().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().next().next().html("");
    </script>
<?php
}
if($periodoActual==4){?>
	<script>
    $('.newClass').prev().children().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().html(espacios+"");
    $('.newClass').prev().children().next().next().next().next().next().html(espacios+<?php echo $sacadaDef;?>);
    $('.newClass').prev().children().next().next().next().next().next().next().html("");
    </script>
<?php
}
?> 
              <?php
		   } //if php --> Si hay notas
	     } //indicadores
    	} //if php --> si hay indicadores
	  }	//materias  
	  ?>
			
<?php
$ii++;
}	//Area	
?>

    <tr align="center" style="font-size:10px; font-weight:bold;">
        <td colspan="2" align="right">PROMEDIO</td>
        <td><?php if(1<=$periodoActual) echo round($prom1/$num_mat,1); else echo "-";?></td>
        <td><?php if(2<=$periodoActual) echo round($prom2/$num_mat,1); else echo "-";?></td>
        <td><?php if(3<=$periodoActual) echo round($prom3/$num_mat,1); else echo "-";?></td>
        <td><?php if(4<=$periodoActual) echo round($prom4/$num_mat,1); else echo "-";?></td>
        <td><?=round($totalDefini/$num_mat,1);?></td>
        <td colspan="2">&nbsp;</td>
    </tr>
    
</table>

<?php
//print_r($vectorT);
?>
</div> 
<br>
<div align="center">
<table width="100%" cellspacing="0" cellpadding="0"  border="1" style="text-align:center; font-size:10px; background:#FFFFCC;">
  <tr style="text-transform:uppercase;">
    <td style="font-weight:bold;" align="right">ESCALA NACIONAL</td><td>Desempe&ntilde;o Superior</td><td>Desempe&ntilde;o Alto</td><td>Desempe&ntilde;o B&aacute;sico</td><td>Desempe&ntilde;o Bajo</td>
  </tr>
  
  <tr>
  	<td style="font-weight:bold;" align="right">RANGO INSTITUCIONAL</td><td><?=$cde[1];?> - <?=$cde[2];?></td><td><?=$cde[3];?> - <?=$cde[4];?></td><td><?=$cde[5];?> - <?=$cde[6];?></td><td><?=$cde[7];?> - <?=$cde[8];?></td>  
  </tr>

</table>

</div>  



<p align="center">
<?php 
if($periodoActual==4){
	if($materiasPerdidas>=3)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datos_usr[4])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";
	elseif($materiasPerdidas<3 and $materiasPerdidas>0)
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datos_usr[4])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";
	else
		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datos_usr[4])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	
?>
	<!--Materias Perdidas = --><?php //echo $materiasPerdidas;?>
<?php
}
?>

<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:11px;" align="center"><?=$msj;?></div>

</p>					                   
 
<?php
$periodo1 = round($prom1/$num_mat,1);
$periodo2 = round($prom2/$num_mat,1);
$periodo3 = round($prom3/$num_mat,1);
$periodo4 = round($prom4/$num_mat,1);

if($periodoActual==1){
	$title = "'1 Periodo'";
	$data =  $periodo1;
}
if($periodoActual==2){
	$title = "'1 Periodo', '2 Periodo'";
	$data =  $periodo1 .",". $periodo2;
}
if($periodoActual==3){
	$title = "'1 Periodo', '2 Periodo', '3 Periodo'";
	$data =  $periodo1 .",". $periodo2 .",". $periodo3;
}
if($periodoActual==4){
	$title = "'1 Periodo', '2 Periodo', '3 Periodo', '4 Periodo'";
	$data =  $periodo1 .",". $periodo2 .",". $periodo3 .",". $periodo4;
}
?> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column',
                margin: [ 50, 50, 100, 80]
            },
            title: {
                text: '<?=strtoupper($datos_usr[4]);?> - Promedio por periodos'
            },
            xAxis: {
                categories: [
                    <?php echo $title;?>
                ],
                labels: {
                    rotation: -45,
                    align: 'right',
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: '<?=$cursoN[1]." ".$grupoN[1];?>'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        'Promedio: '+ Highcharts.numberFormat(this.y, 1) +
                        ' ';
                }
            },
            series: [{
                name: 'Population',
                data: [<?php echo $data;?>],
                dataLabels: {
                    enabled: true,
                    rotation: -90,
                    color: '#FFFFFF',
                    align: 'right',
                    x: 4,
                    y: 10,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            }]
        });
    });
    
});
</script>
   <script src="highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="min-width: 200px; width:700px; height: 300px; margin: 0 auto;"></div>
                          
</body>
</html>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>