<?php
if(!isset($_GET["ref"]) or $_GET["ref"]=="" or !is_numeric($_GET["ref"]) or $_SERVER['HTTP_REFERER']==""){
	echo '<script type="text/javascript">window.close()</script>';
	exit();
}
?>
<?php include("../../config-general/config.php");?>
<?php include("../directivo/session.php");?>
<?php include("../head.php");?>
  </head>
  <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, Helvetica, sans-serif;">
  <?php
  $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
  INNER JOIN academico_grados ON gra_id=mat_grado
  INNER JOIN academico_grupos ON gru_id=mat_grupo
  INNER JOIN opciones_generales ON ogen_id=mat_genero
  WHERE mat_matricula='".$_GET["ref"]."'");
  $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
  $consultaTipo=mysqli_query($conexion, "SELECT * FROM opciones_generales WHERE ogen_id='".$resultado['mat_tipo']."'");
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
    
    <tr>
        <td colspan="3">Valor Matricula:&nbsp;<b><?="$".number_format($resultado['gra_valor_matricula'],2,",",".");?></b></td>
        <td colspan="2">Valor Pensi&oacute;n:&nbsp;<b><?="$".number_format($resultado['gra_valor_pension'],2,",",".");?></b></td>
    </tr>
    
    
 </table>
 <?php
if($resultado['mat_tipo']==128){
		$msj = 'Su clave provisional es <b>sintia</b>. Por favor ingrese a la plataforma y en la opci&oacute;n mi perfil puede cambiar la contrase&ntilde;a.';
	}
else{	
	$msj = 'La contrase&ntilde;a que usted haya colocado. Si nunca ha ingresado al sistema o nunca la ha cambiado, por favor pida que se la restablezcan en la Instituci&oacute;n';
}	
?> 
  <span style="font-size:10px;">
 	Url: <b>http://plataformasintia.com/icolven/</b><br>
    Usuario: <b><?=$resultado[1];?></b><br>
    Contrase&ntilde;a: <b><?=$msj;?></b><br>
 </span> 
<br> 
<table width="80%" cellpadding="5" cellspacing="0" border="0" align="center" style="font-size:16px;">
 	 <tr align="center">
        <td>____________________________<br>Acudiente</td>
        <td>____________________________<br>Tesorer&iacute;a</td>
        <td colspan="2">____________________________<br>Secretar&iacute;a Acad&eacute;mica</td>
    </tr>
</table> 

<script>
	function imprimir(){
		imp = document.getElementById("imp");
		imp.style.display = 'none';
		window.print();
	}
</script>

<div align="center">
	<a href="" id="imp" onClick="imprimir()"><img src="../files/iconos/agt_print.png"></a>
</div>
  
</body>

</html>
