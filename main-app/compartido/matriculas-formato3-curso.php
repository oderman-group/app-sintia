<?php
if($_SERVER['HTTP_REFERER']==""){
	echo '<script type="text/javascript">window.close()</script>';
	exit();
}
?>
<?php include("../directivo/session.php");?>
<?php include("../../config-general/config.php");?>
<?php include("head.php");?>
<style>
#saltoPagina
{
	PAGE-BREAK-AFTER: always;
}
</style>
  </head>
  <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, Helvetica, sans-serif;">
  <?php
  $curso = mysqli_query($conexion, "SELECT * FROM academico_matriculas WHERE mat_grado='".$_REQUEST["curso"]."'");
  while($c = mysqli_fetch_array($curso, MYSQLI_BOTH)){
	  
  $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
  INNER JOIN academico_grados ON gra_id=mat_grado
  INNER JOIN academico_grupos ON gru_id=mat_grupo
  INNER JOIN $baseDatosServicios.opciones_generales ON ogen_id=mat_genero
  WHERE mat_id='".$c[0]."'");
  $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
  $consultaTipo=mysqli_query($conexion, "SELECT * FROM $baseDatosServicios.opciones_generales WHERE ogen_id='".$resultado['mat_tipo']."'");
  $tipo = mysqli_fetch_array($consultaTipo, MYSQLI_BOTH);
  ?>
<table width="80%" cellpadding="5" cellspacing="0" border="0" align="center" style="font-size:15px;">
 	<tr>
    	<td colspan="4" align="center">
        	<img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150" width="250"><br>
            <?=$informacion_inst["info_nombre"]?><br>
            FORMATO DE MATRICULAS
        </td>
    </tr>
</table>    


 <table width="100%" cellpadding="3" cellspacing="0" border="1" rules="groups" align="center" style="font-size:12px;">
    
    <tr>
    	<td>Fecha Matricula:&nbsp;<b><?=$resultado[2];?></b></td>
        <td>C&oacute;digo Estudiante:&nbsp;<b><?=$resultado[1];?></b></td>
        <td>No. Documento:&nbsp;<b><?=$resultado[12];?></b></td>
        <td>No. Matrícula:&nbsp;<b><?=$resultado['mat_numero_matricula'];?></b></td>
        <td>Folio:&nbsp;<b><?=$resultado['mat_folio'];?></b></td>
    </tr>
    
    <tr>
        <td>Apellidos:&nbsp;<b><?=strtoupper($resultado[3]." ".$resultado[4]);?></b></td>
        <td>Nombres:&nbsp;<b><?=strtoupper($resultado[5]);?></b></td>
        <td>Curso:&nbsp;<b><?=$resultado['gra_nombre'];?></b></td>
        <td colspan="2">Grupo:&nbsp;<b><?=$resultado['gru_nombre'];?></b></td>
    </tr>
    
    <tr>
        <td colspan="3">Tipo Estudiante:&nbsp;<b><?=$tipo[1];?></b></td>
        <td>Fecha Nacimiento:&nbsp;<b><?=$resultado['mat_fecha_nacimiento'];?></b></td>
        <td>Genero:&nbsp;<b><?=$resultado['ogen_nombre'];?></b></td>
    </tr>
    
     <tr>
        <td>Direcci&oacute;n:&nbsp;<b><?=$resultado['mat_direccion'];?></b></td>
        <td>Barrio:&nbsp;<b><?=$resultado['mat_barrio'];?></b></td>
        <td>Tel&eacute;fono:&nbsp;<b><?=$resultado['mat_telefono'];?></b></td>
        <td colspan="2">Celular:&nbsp;<b><?=$resultado['mat_celular'];?></b></td>
    </tr>

    
    
 </table>

<p>&nbsp;</p> 
<table width="80%" cellpadding="5" cellspacing="0" border="0" align="center" style="font-size:16px;">
 	 <tr align="center">
        <td>____________________________<br>Estudiante</td>
        <td>____________________________<br>Acudiente</td>
        <td>____________________________<br>Rectoría</td>
        <td>____________________________<br>Secretar&iacute;a Acad&eacute;mica</td>
    </tr>
</table>

<div id="saltoPagina"></div> 
<?php }?>

<script type="application/javascript">
print();
</script> 
  
</body>

</html>
