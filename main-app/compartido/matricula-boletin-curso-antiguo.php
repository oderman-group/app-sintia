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

$matriculadosPorCurso = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_REQUEST["curso"]."' AND mat_eliminado=0 AND mat_estado_matricula!=3 ORDER BY mat_grupo, mat_primer_apellido");

while($matriculadosDatos = mysqli_fetch_array($matriculadosPorCurso, MYSQLI_BOTH)){

//contador materias

$cont_periodos=0;

$contador_indicadores=0;

$materiasPerdidas=0;

//======================= DATOS DEL ESTUDIANTE MATRICULADO =========================

$usr=mysqli_query($conexion, "SELECT * FROM academico_matriculas am

INNER JOIN academico_grupos ON mat_grupo=gru_id

INNER JOIN academico_grados ON mat_grado=gra_id WHERE mat_id=".$matriculadosDatos[0]);

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

$consulta_desempeno=mysqli_query($conexion, "SELECT notip_id, notip_nombre, notip_desde, notip_hasta FROM academico_notas_tipos WHERE notip_categoria=".$config["conf_notas_categoria"].";");	

//CONSULTA QUE ME TRAE LAS areas DEL ESTUDIANTE

$consulta_mat_area_est=mysqli_query($conexion, "SELECT ar_id, car_ih FROM academico_cargas ac

INNER JOIN academico_materias am ON am.mat_id=ac.car_materia

INNER JOIN academico_areas ar ON ar.ar_id= am.mat_area

WHERE  car_curso=".$datos_usr["mat_grado"]." AND car_grupo=".$datos_usr["mat_grupo"]." GROUP BY ar.ar_id ORDER BY ar.ar_posicion ASC;");

$numero_periodos=$config["conf_periodos_maximos"];

 ?>



<div align="center" style="margin-bottom:20px;">

    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="800"><br>

    <!--<?=$informacion_inst["info_nombre"]?><br>-->

    BOLET&Iacute;N DE CALIFICACIONES<br>

</div> 



<table width="100%" cellspacing="0" cellpadding="0" border="0" align="left" style="font-size:12px;">

    <tr>

    	<td>C&oacute;digo: <b><?=$datos_usr["mat_matricula"];?></b></td>

        <td>Nombre: <b><?=strtoupper($datos_usr[3]." ".$datos_usr[4]." ".$datos_usr["mat_nombres"]);?></b></td>   

    </tr>

    

    <tr>

    	<td>Grado: <b><?=$datos_usr["gra_nombre"]." ".$datos_usr["gru_nombre"];?></b></td>

        <td>Periodo: <b><?=$periodoActuales;?></b></td>    

    </tr>

</table>

<br>

<table width="100%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="left">

<tr style="font-weight:bold; background:#F06; border-color:#F06; height:20px; color:#FFF; font-size:12px;">

<td width="20%" align="center">AREAS/ ASIGNATURAS</td>

<td width="2%" align="center">I.H</td>

<?php for($j=1;$j<=$numero_periodos;$j++){ ?>

<td width="3%" align="center"><a href="<?=$_SERVER['PHP_SELF'];?>?id=<?=$matriculadosDatos[0];?>&periodo=<?=$j?>" style="color:#FFF; text-decoration:underline;"><?=$j?>P</a></td>

<?php }?>

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

		

//CONSULTA QUE ME EL NOMBRE Y EL PROMEDIO DEL AREA

$consulta_notdef_area=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre FROM academico_materias am

INNER JOIN academico_areas a ON a.ar_id=am.mat_area

INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id

INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id

WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")

GROUP BY ar_id;");

//CONSULTA QUE ME TRAE LA DEFINITIVA POR MATERIA Y NOMBRE DE LA MATERIA

$consulta_a_mat=mysqli_query($conexion, "SELECT (SUM(bol_nota)/COUNT(bol_nota)) as suma,ar_nombre,mat_nombre,mat_id FROM academico_materias am

INNER JOIN academico_areas a ON a.ar_id=am.mat_area

INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id

INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id

WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")

GROUP BY mat_id

ORDER BY mat_id;");

//CONSULTA QUE ME TRAE LAS DEFINITIVAS POR PERIODO

$consulta_a_mat_per=mysqli_query($conexion, "SELECT bol_nota,bol_periodo,ar_nombre,mat_nombre,mat_id FROM academico_materias am

INNER JOIN academico_areas a ON a.ar_id=am.mat_area

INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id

INNER JOIN academico_boletin ab ON ab.bol_carga=ac.car_id

WHERE bol_estudiante='".$matriculadosDatos[0]."' and a.ar_id=".$fila["ar_id"]." and bol_periodo in (".$condicion.")

ORDER BY mat_id,bol_periodo

;");





//CONSULTA QUE ME TRAE LOS INDICADORES DE CADA MATERIA

$consulta_a_mat_indicadores=mysqli_query($conexion, "SELECT mat_nombre,mat_area,mat_id,ind_nombre,ipc_periodo,(SUM(cal_nota)/COUNT(cal_nota))as nota FROM academico_materias am

INNER JOIN academico_areas a ON a.ar_id=am.mat_area

INNER JOIN academico_cargas ac ON ac.car_materia=am.mat_id

INNER JOIN academico_indicadores_carga aic ON aic.ipc_carga=ac.car_id

INNER JOIN academico_indicadores ai ON aic.ipc_indicador=ai.ind_id

INNER JOIN academico_actividades aa ON aa.act_id_tipo=aic.ipc_indicador AND act_id_carga=car_id

INNER JOIN academico_calificaciones aac ON aac.cal_id_actividad=aa.act_id

WHERE car_curso=".$datos_usr["mat_grado"]."  and car_grupo=".$datos_usr["mat_grupo"]." and mat_area=".$fila["ar_id"]." AND ipc_periodo in (".$condicion.") AND cal_id_estudiante='".$matriculadosDatos[0]."' and act_periodo=".$condicion2."

group by act_id_tipo, act_id_carga

order by mat_id,ipc_periodo,ind_id;");



$numIndicadores=mysqli_num_rows($consulta_a_mat_indicadores);



$resultado_not_area=mysqli_fetch_array($consulta_notdef_area, MYSQLI_BOTH);

$numfilas_not_area=mysqli_num_rows($consulta_notdef_area);

$total_promedio=round( $resultado_not_area["suma"],1);





if($total_promedio==1)	$total_promedio="1.0";	if($total_promedio==2)	$total_promedio="2.0";		if($total_promedio==3)	$total_promedio="3.0";	if($total_promedio==4)	$total_promedio="4.0";	if($total_promedio==5)	$total_promedio="5.0";

	if($numfilas_not_area>0){

			?>

  <tr bgcolor="#FFFFFF" style="font-size:12px;">

            <td style="font-size:12px; height:15px; font-weight:bold;"><?php echo $resultado_not_area["ar_nombre"];?></td> 

            <td align="center" style="font-weight:bold; font-size:12px;"></td>

            <?php for($k=1;$k<=$numero_periodos;$k++){ 

			?>

			<td class=""  align="center" style="font-weight:bold;"></td>

            <?php }?>

        <td align="center" style="font-weight:bold;"><?php echo $total_promedio;?></td>

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

	  if($nota_periodo==1)	$nota_periodo="1.0";	if($nota_periodo==2)	$nota_periodo="2.0";		if($nota_periodo==3)	$nota_periodo="3.0";	if($nota_periodo==4)	$nota_periodo="4.0";	if($nota_periodo==5)	$nota_periodo="5.0";

	  $notas[$contador_periodos] =$nota_periodo;

	 }

	}

?>

 <tr bgcolor="#FFF" style="font-size:12px;">

            <td style="font-size:12px; height:15px; font-weight:bold;background:#FFF;">&raquo;<?php echo $fila2["mat_nombre"];?></td> 

            <td align="center" style="font-weight:bold; font-size:12px;background:#FFF;"><?php echo $fila["car_ih"];?></td>

<?php for($l=1;$l<=$numero_periodos;$l++){ ?>

			<td class=""  align="center" style="font-weight:bold; background:#FFF;"><?php echo $notas[$l];

			$promedios[$l]=$promedios[$l]+$notas[$l];

			$contpromedios[$l]=$contpromedios[$l]+1;

			?></td>

        <?php }?>

      <?php 

	  $total_promedio2=round( $fila2["suma"],1);

	   

	   if($total_promedio2==1)	$total_promedio2="1.0";	if($total_promedio2==2)	$total_promedio2="2.0";		if($total_promedio2==3)	$total_promedio2="3.0";	if($total_promedio2==4)	$total_promedio2="4.0";	if($total_promedio2==5)	$total_promedio2="5.0";

	   if($total_promedio2<$r_desempeno["desbasdesde"]){$materiasPerdidas++;}

	   ?>

       

        <td align="center" style="font-weight:bold; background:#FFF;"><?=$total_promedio2;?></td>

        <td align="center" style="font-weight:bold; background:#FFF;"><?php //DESEMPEÑO

		while($r_desempeno=mysqli_fetch_array($consulta_desempeno, MYSQLI_BOTH)){

			if($total_promedio2>=$r_desempeno["notip_desde"] && $total_promedio2<=$r_desempeno["notip_hasta"]){

				echo $r_desempeno["notip_nombre"];

				}

			}

			mysqli_data_seek($consulta_desempeno,0);

		 ?></td>

        <td align="center" style="font-weight:bold; background:#FFF;"><?php if($r_ausencias[0]>0){ echo $r_ausencias[0]."/".$fila2["matmaxaus"];} else{ echo "0.0/".$fila2["matmaxaus"];}?></td>

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

	?>

<tr bgcolor="#FFF" style="font-size:12px;">

            <td style="font-size:12px; height:15px;"><?php echo $contador_indicadores.". ".$fila4["ind_nombre"];?></td> 

            <td align="center" style="font-weight:bold; font-size:12px;"></td>

            <?php for($m=1;$m<=$numero_periodos;$m++){ ?>

			<td class=""  align="center" style="font-weight:bold;"><?php 

			if($periodoActual==$m){echo $nota_indicador;} ?></td>

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

        <?php for($n=1;$n<=$numero_periodos;$n++){ ?>

        <td><?php if($promedios[$n]!=0){echo round(($promedios[$n]/$contpromedios[$n]),1);}?></td>

        <?php 
			unset($promedios);
			unset($contpromedios);
			} 
		?>

        <td></td>

        <td colspan="2">&nbsp;</td>

    </tr>

    

</table>



<p>&nbsp;</p>

<?php 

$cndisiplina = mysqli_query($conexion, "SELECT * FROM disiplina_nota WHERE dn_cod_estudiante='".$matriculadosDatos[0]."' AND dn_periodo in(".$condicion.");");

if(@mysqli_num_rows($cndisiplina)>0){

?>

<table width="50%" id="tblBoletin" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">



    <tr style="font-weight:bold; background:#036; border-color:#036; height:40px; color:#FC0; font-size:12px; text-align:center">

    	<td colspan="3">NOTAS DISCIPLINARIAS</td>

    </tr>

    

    <tr style="font-weight:bold; background:#F06; border-color:#F06; height:25px; color:#FFF; font-size:12px; text-align:center">

        <td width="8%">Periodo</td>

        <td width="8%">Nota</td>

        <td>Observaciones</td>

    </tr>

<?php while($rndisiplina=mysqli_fetch_array($cndisiplina, MYSQLI_BOTH)){?>

    <tr align="center" style="font-weight:bold; font-size:12px; height:20px;">

        <td><?=$rndisiplina["dn_periodo"]?></td>

        <td><?=$rndisiplina["dn_nota"]?></td>

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

		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datos_usr[4])." NO FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";

	elseif($materiasPerdidas<3 and $materiasPerdidas>0)

		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datos_usr[4])." DEBE NIVELAR LAS MATERIAS PERDIDAS</center>";

	else

		$msj = "<center>EL (LA) ESTUDIANTE ".strtoupper($datos_usr[4])." FUE PROMOVIDO(A) AL GRADO SIGUIENTE</center>";	

}

?>





<p align="center">



<div style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-style:italic; font-size:12px;" align="center"><?=$msj;?></div>



</p>					                   

 

<div align="center" style="font-size:10px; margin-top:10px;">

                                        <img src="../files/images/sintia.png" height="50" width="100"><br>

                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>

                                    </div>

 <div id="saltoPagina"></div>

                                    

<?php

 }// FIN DE TODOS LOS MATRICULADOS


?>

<script type="application/javascript">

print();

</script>                                    

                          

</body>

</html>

