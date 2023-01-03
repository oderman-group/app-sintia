<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");
?>
<head>
	<title>Estudiantes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
    <?=$informacion_inst["info_nombre"]?><br>
    INFORME DE ESTUDIANTES</br>
</div>   
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <th>Matr&iacute;cula</th>
        <th>Documento</th>
        <th>Estudiante</th>
        <th>Grado</th>
        <th>Grupo</th>
        <th>Telefono</th>
        <th>Celular</th>
        <th>Acudiente</th>
  </tr>
  <?php
  if(isset($_GET["grado"])) $adicional = "mat_grado='".$_GET["grado"]."' AND "; else $adicional = "";
  $cont=1;
  $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE ".$adicional." (mat_estado_matricula=1 OR mat_estado_matricula=2) AND mat_eliminado=0 ORDER BY mat_primer_apellido");
  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
  $consultaAcudiente=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$resultado[26]."'");
	$acudiente = mysqli_fetch_array($consultaAcudiente, MYSQLI_BOTH);
  $consultaGrados=mysqli_query($conexion, "SELECT * FROM academico_grados, academico_grupos WHERE gra_id='".$resultado[6]."' AND gru_id='".$resultado[7]."'");
	$grados = mysqli_fetch_array($consultaGrados, MYSQLI_BOTH);	
  ?>
  <tr style="font-size:13px;">
      <td><?=$resultado[1];?></td>
      <td><?=$resultado[12];?></td>
      <td><?=strtoupper($resultado[3]." ".$resultado[4]." ".$resultado[5]);?></td>
      <td><?=$grados["gra_nombre"];?></td>
      <td><?=$grados["gru_nombre"];?></td>
      <td><?=$resultado["mat_telefono"];?></td>
      <td><?=$resultado["mat_celular"];?></td>
     <td><?=strtoupper($acudiente[4]);?></td> 
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


