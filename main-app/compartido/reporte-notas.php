<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>Notas</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
   <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE NOTAS - PERIODO: <?=$_GET["per"];?></br>
</div>   
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <th>Cod</th>
        <th>Des</th>
        <th>Fecha</th>
        <th>Valor</th>
        <th>Indicador</th>
        <th>Estado</th>
        <th>#ET/#EC.</th>
  </tr>
  <?php
  //ESTUDIANTES ACTUALES
  $consultaNumEstudiantes=mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["grado"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
	$numEstudiantes = mysqli_num_rows($consultaNumEstudiantes);
  $cont=1;
  $consulta = mysqli_query($conexion, "SELECT * FROM academico_actividades WHERE act_id_carga='".$_GET["carga"]."' AND act_estado=1 AND act_periodo='".$_GET["per"]."'");
  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
  $consultaInd=mysqli_query($conexion, "SELECT * FROM academico_indicadores WHERE ind_id='".$resultado[4]."'");
	$ind = mysqli_fetch_array($consultaInd, MYSQLI_BOTH);
	if($resultado[6]==1) $estado = "REGISTRADA"; else $estado = "PENDIENTE";
  $consultaNumCalificados=mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_actividad='".$resultado[0]."'");
	$numCalificados = mysqli_num_rows($consultaNumCalificados);
	if($numEstudiantes!=$numCalificados) $bg = '#FCC'; else $bg = '#FFF';
  ?>
  <tr style="font-size:13px;">
      <td align="center"><?=$resultado[0];?></td>
      <td><?=$resultado[1];?></td>
      <td align="center"><?=$resultado[2];?></td>
      <td align="center"><?=$resultado[3];?>%</td>
      <td><?=$ind[1];?></td>
      <td><?=$estado;?></td> 
      <td style="text-align:center; background:<?=$bg;?>"><?=$numEstudiantes;?>/<?=$numCalificados;?></td>  
</tr>
  <?php
  $cont++;
  }//Fin mientras que
  ?>
  </table>
  </center>
	<div align="center" style="font-size:10px; margin-top:10px;">
                                        <img src="../files/images/sintia.png" height="50" width="100"><br>
                                        SINTIA -  SISTEMA INTEGRAL DE GESTI&Oacute;N INSTITUCIONAL - <?=date("l, d-M-Y");?>
                                    </div>
</body>
</html>


