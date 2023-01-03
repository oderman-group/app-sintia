<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<head>
	<title>Informes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/logoodermanp.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE CALIFICACIONES - ACTIVIDAD: <?=$_GET["idActividad"];?></br>
</div>   
   <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <th class="center">C&oacute;digo</th>
                                        <th class="center">Nombre</th>
                                        <th class="center">Nota</th>
                                        <th class="center">Observaciones</th>
  </tr>
  <?php
									 $con = 1;
									 $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_GET["grado"]."' AND mat_grupo='".$_GET["grupo"]."' AND (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
									 while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
										 //LAS CALIFICACIONES A MODIFICAR Y LAS OBSERVACIONES
										 $notasConsulta = mysqli_query($conexion, "SELECT * FROM academico_calificaciones WHERE cal_id_estudiante=".$resultado[0]." AND cal_id_actividad=".$_GET["idActividad"]);
										 $notasResultado = mysqli_fetch_array($notasConsulta, MYSQLI_BOTH);
									 ?>
  <tr style="font-size:13px;">
      <td class="center"><?=$resultado[0];?></td>
                                        <td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
                                        <td class="center" style="font-size: 13px; text-align: center; color:<?php if($notasResultado[3]<$config[5] and $notasResultado[3]!="")echo $config[6]; elseif($notasResultado[3]>=$config[5]) echo $config[7]; else echo "black";?>"><?=$notasResultado[3];?></td>
                                        <td class="center"><?=$notasResultado[4];?></td>
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


