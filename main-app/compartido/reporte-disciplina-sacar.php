<?php
session_start();
include("../../config-general/config.php");
include("../../config-general/consulta-usuario-actual.php");?>
<?php include("../compartido/head.php");?>

<?php
$consultaDatos=mysqli_query($conexion, "SELECT * FROM academico_grados
LEFT JOIN academico_grupos ON gru_id='".$_POST["grupo"]."'
WHERE gra_id='".$_POST["grado"]."'");
$datos = mysqli_fetch_array($consultaDatos, MYSQLI_BOTH);
?>

<head>
	<title>Estudiantes</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="shortcut icon" href="../files/images/ico.png">
</head>
<body style="font-family:Arial;">
<div align="center" style="margin-bottom:20px;">
    <?=$informacion_inst["info_nombre"]?><br>
    REPORTES DISCIPLINARIOS</br>
	<?=strtoupper($datos['gra_nombre']." ".$datos['gru_nombre']);?><br>
	DE <?=$_POST["desde"];?> HASTA <?=$_POST["hasta"];?>
</div>   
  <table bgcolor="#FFFFFF" width="80%" cellspacing="5" cellpadding="5" rules="all" border="<?php echo $config[13] ?>" style="border:solid; border-color:<?php echo $config[11] ?>;" align="center">
  <tr style="font-weight:bold; font-size:12px; height:30px; background:<?php echo $config[12] ?>;">
        <th>#</th>
		<th>Fecha</th>
		<th>Estudiante</th>
		<th>Curso</th>
		<th>Categoría</th>
		<th>Cod</th>
	  	<th>Observaciones</th>
		<th>Usuario</th>
		<th title="Firma y aprobación del estudiante">F.E</th>
		<th title="Firma y aprobación del acudiente">F.A</th>
  </tr>
  <?php
  $cont=1;
  $filtro = '';
  if($_POST["est"]!=""){$filtro .= " AND dr_estudiante='".$_POST["est"]."'";}
  if($_POST["falta"]!=""){$filtro .= " AND dr_falta='".$_POST["falta"]."'";}
  if($_POST["usuario"]!=""){$filtro .= " AND dr_usuario='".$_POST["usuario"]."'";}

  $filtroMat = '';
  if($_POST["grado"]!=""){$filtro .= " AND mat_grado='".$_POST["grado"]."'";}
  if($_POST["grupo"]!=""){$filtro .= " AND mat_grupo='".$_POST["grupo"]."'";}			 

  //if($datosUsuarioActual[3]!=5){$filtro .= " AND dr_usuario='".$_SESSION["id"]."'";}
													
  $consulta = mysqli_query($conexion, "SELECT * FROM disciplina_reportes
  INNER JOIN disciplina_faltas ON dfal_id=dr_falta
  INNER JOIN disciplina_categorias ON dcat_id=dfal_id_categoria
  INNER JOIN academico_matriculas ON mat_id_usuario=dr_estudiante $filtroMat
  LEFT JOIN academico_grados ON gra_id=mat_grado
  LEFT JOIN academico_grupos ON gru_id=mat_grupo
  INNER JOIN usuarios ON uss_id=dr_usuario
  WHERE dr_fecha>='".$_POST["desde"]."' AND dr_fecha<='".$_POST["hasta"]."' $filtro
  ");
  while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
  ?>
  <tr style="font-size:13px;">
    <td><?=$cont;?></td>
	<td><?=$resultado['dr_fecha'];?></td>
	<td><?=strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']." ".$resultado['mat_nombres']);?></td>
	<td><?=$resultado['gra_nombre']." ".$resultado['gru_nombre'];?></td>
	<td><?=$resultado['dcat_nombre'];?></td>
	<td><?=$resultado['dfal_codigo'];?></td>
    <td><?=$resultado['dr_observaciones'];?></td>
	<td><?=$resultado['uss_nombre'];?></td>
														<td>
															<?php if($resultado['dr_aprobacion_estudiante']==0){ echo "-"; }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_estudiante_fecha'];?>">OK</i>
															<?php }?>
														</td>
														<td>
															<?php if($resultado['dr_aprobacion_acudiente']==0){ echo "-"; }else{?>
																<i class="fa fa-check-circle" title="<?=$resultado['dr_aprobacion_acudiente_fecha'];?>">OK</i>
															<?php }?>
														</td>
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


