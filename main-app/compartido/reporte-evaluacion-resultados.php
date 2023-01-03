<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<?php
$consultaDatosGenerales=mysqli_query($conexion, "SELECT * FROM general_evaluacion_asignar 
INNER JOIN general_evaluaciones ON evag_id=epag_id_evaluacion
INNER JOIN usuarios ON uss_id=epag_usuario
INNER JOIN academico_grados ON gra_id=epag_curso
INNER JOIN academico_grupos ON gru_id=epag_grupo
WHERE epag_id='".$_GET["a"]."'");
$datosGenerales = mysqli_fetch_array($consultaDatosGenerales, MYSQLI_BOTH);
?>
<head>
	<title>SINTIA | Resultado de evaluaciones</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
   <!-- <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>-->
    <?=$informacion_inst["info_nombre"]?><br>
    RESULTADO DE EVALUACIONES</br>
</div> 

<table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>; color:#FFF;">
  	<td>Evaluación: <?=$datosGenerales['evag_nombre'];?></td>
    <td>Docente: <?=$datosGenerales['uss_nombre'];?></td>
    <td>Curso: <?=$datosGenerales['gra_nombre']." ".$datosGenerales['gru_nombre'];?></td>
  </tr>
  </table>
  
<p>&nbsp;</p>  

  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
    <th>#</th>
    <th>#P</th>
    <th>Pregunta</th>
    <th>Respuesta</th>
    <!--<th>Estudiante</th>-->
  </tr>
<?php
$consulta = mysqli_query($conexion, "SELECT * FROM general_resultados
INNER JOIN general_preguntas ON pregg_id=resg_id_pregunta
INNER JOIN academico_matriculas ON mat_id=resg_id_estudiante
WHERE resg_id_asignacion='".$_GET["a"]."'");
$consultaNumPregunta=mysqli_query($conexion, "SELECT * FROM general_preguntas WHERE pregg_id_evaluacion='".$datosGenerales['epag_id_evaluacion']."'");
$preguntasNum = mysqli_num_rows($consultaNumPregunta);
$e=0;
$i=0;
$c=0;
$num=0; 
$fondo[1] = '#FFC';
$fondo[2] = '#FC9';
$fondo[3] = '#CFC';
$fondo[4] = '#FCC';
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
  $consultaRespuesta=mysqli_query($conexion, "SELECT * FROM general_respuestas WHERE resg_id='".$resultado['resg_id_respuesta']."'");
	$respuesta = mysqli_fetch_array($consultaRespuesta, MYSQLI_BOTH);
	//ESTUDIANTE
	if($e!=$resultado['resg_id_estudiante']){$e=$resultado['resg_id_estudiante']; $i++; $c=1; $num++;} if($i==5) $i=1;
?>
  <tr style="font-size:13px; background:<?=$fondo[$i];?>;">
      <?php if($c==1){?><td rowspan="<?=$preguntasNum;?>" style="text-align:center; font-weight:bold;"><?=$num;?></td><?php }?>
      <td><?=$c;?></td>
      <td><?=$resultado['pregg_descripcion'];?></td>
      <td><?=$respuesta[1];?></td>
     <!-- <?php if($c==1){?><td rowspan="<?=$preguntasNum;?>" style="font-weight:bold;"><?=strtoupper($resultado['mat_nombres']." ".$resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']);?></td><?php }?>-->
	</tr>
<?php
	$c++;
}
?>
  </table>
<p>&nbsp;</p>
<h2 align="center">RESUMEN ESTADISTICO</h2> 
<table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" align="center" style="color:#FCC">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:#003; color:#FFF;">
  	<td>Evaluación: <?=$datosGenerales['evag_nombre'];?></td>
    <td>Docente: <?=$datosGenerales['uss_nombre'];?></td>
    <td>Curso: <?=$datosGenerales['gra_nombre']." ".$datosGenerales['gru_nombre'];?></td>
  </tr>
  </table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" align="center">
<?php
//CANTIDAD DE OPCIONES DE RESPUESTAS POR PREGUNTA
$preguntas = mysqli_query($conexion, "SELECT * FROM general_preguntas WHERE pregg_id_evaluacion='".$datosGenerales['epag_id_evaluacion']."'");
while($preg = mysqli_fetch_array($preguntas, MYSQLI_BOTH)){
?>
	<tr style="font-weight:bold; font-size:12px; height:30px; background:#003; color:#FFF;">
    	<td colspan="2"><?=$preg[1];?></td>
    </tr>
    <tr style="font-weight:bold; font-size:12px; height:30px;">
    	<td>Respuesta</td>
        <td>Cant.</td>
    </tr>
<?php	
	$rpp = mysqli_query($conexion, "SELECT resg_id_respuesta, count(resg_id_respuesta) as cant FROM general_resultados WHERE resg_id_pregunta='".$preg[0]."' AND resg_id_asignacion='".$_GET["a"]."' group by resg_id_respuesta");
	while($rppD = mysqli_fetch_array($rpp, MYSQLI_BOTH)){
    $consultaRespuesta=mysqli_query($conexion, "SELECT * FROM general_respuestas WHERE resg_id='".$rppD['resg_id_respuesta']."'");
		$respuesta = mysqli_fetch_array($consultaRespuesta, MYSQLI_BOTH);
		$total = $total + $rppD['cant'];
?>
	<tr>
    	<td><?=$respuesta[1];?></td>
        <td><?=$rppD['cant'];?></td>
    </tr>
<?php		
	}
?>
	<tr style="font-weight:bold;">
    	<td align="right">TOTAL RESPUESTAS</td>
        <td><?=$total;?></td>
    </tr>
<?php
	$total = 0;	
}
?>
</table>

  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
      <img src="../files/images/sintia.png" height="50" width="100"><br>
      SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
     </div>
</body>
</html>


