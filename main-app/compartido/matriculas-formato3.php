<?php
if(!isset($_GET["ref"]) or $_GET["ref"]=="" or !is_numeric($_GET["ref"]) or $_SERVER['HTTP_REFERER']==""){
	echo '<script type="text/javascript">window.close()</script>';
	exit();
}

include("../directivo/session.php");
include("../../config-general/config.php");
include("head.php");
?>
  </head>
  <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, Helvetica, sans-serif;">
  <?php
  $consulta = mysqli_query($conexion, "SELECT * FROM academico_matriculas 
  INNER JOIN academico_grados ON gra_id=mat_grado
  INNER JOIN academico_grupos ON gru_id=mat_grupo
  INNER JOIN ".$baseDatosServicios.".opciones_generales ON ogen_id=mat_genero
  WHERE mat_matricula='".$_GET["ref"]."'");
  $resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH);
  $consultaAcudiente1=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$resultado['mat_acudiente']."'");
  $acudiente1 = mysqli_fetch_array($consultaAcudiente1, MYSQLI_BOTH);
  $consultaAcudiente2=mysqli_query($conexion, "SELECT * FROM usuarios WHERE uss_id='".$resultado['mat_acudiente2']."'");
  $acudiente2 = mysqli_fetch_array($consultaAcudiente2, MYSQLI_BOTH);
  $consultaTipo=mysqli_query($conexion, "SELECT * FROM ".$baseDatosServicios.".opciones_generales WHERE ogen_id='".$resultado['mat_tipo']."'");
  $tipo = mysqli_fetch_array($consultaTipo, MYSQLI_BOTH);
  ?>
  
<table width="90%" cellpadding="5" cellspacing="0" border="0" align="center" style="font-size:10px;">
    <tr>
    	<td colspan="4" align="center"><img src="../files/images/logo/<?=$informacion_inst["info_logo"]?>" height="150"></td>
    </tr>
    
    <tr>
        <td align="center" colspan="4">
			<h2><?=$informacion_inst["info_nombre"]?></h2>
            REGISTRO DE MATRICULAS - AÑO <?=date("Y");?>
        </td>
    </tr>
    
    <tr>
    	<td align="right">Matricula:</td> 
        <td><b><?=$resultado['mat_numero_matricula'];?></b></td>
        <td align="right">Grado:</td> 
        <td><b><?=$resultado['gra_nombre'];?></b></td>
    </tr>
    
    <tr>
    	<td align="right">Folio:</td> 
        <td><b><?=$resultado['mat_folio'];?></b></td>
        <td align="right">Fecha:</td> 
        <td><b><?=$resultado[2];?></b></td>
    </tr> 
</table> 

<h4 align="center">DATOS PERSONALES</h4>   

<table width="90%" cellpadding="5" cellspacing="0" border="1" rules="groups" align="center" style="font-size:10px;">
    <tr>
    	<td>NOMBRE:</td> 
        <td><b><?=strtoupper($resultado['mat_nombres']." ".$resultado['mat_nombre2']);?></b></td>
        <td>APELLIDOS:</td> 
        <td><b><?=strtoupper($resultado[3]." ".$resultado[4]);?></b></td>
        <td>SEXO:</td> 
        <td><b><?=$resultado['ogen_nombre'];?></b></td>
    </tr>
    
    <tr>
    	<td colspan="2"><b><?=$resultado['mat_fecha_nacimiento'];?></b></td> 
        <td>&nbsp;</td>
        <td colspan="2"><b><?=$resultado['mat_lugar_nacimiento'];?></b></td>
        <td>&nbsp;</td> 
    </tr>
    
    <tr>
    	<td colspan="2">Fecha de nacimiento</td> 
        <td>&nbsp;</td>
        <td colspan="2">Edad:</td> 
        <td><b>12</b></td>
    </tr>
    
    <tr>
    	<td>NUIP:</td> 
        <td><b><?=$resultado[12];?></b></td>
        <td colspan="2"><b><?=$resultado['mat_lugar_expedicion'];?></b></td> 
        <td colspan="2">&nbsp;</td>
    </tr>
    
    <tr>
    	<td>&nbsp;</td>
        <td>&nbsp;</td> 
        <td colspan="2">Lugar de expedición</td> 
        <td colspan="2">&nbsp;</td>
    </tr> 
</table> 

<h4 align="center">DATOS FAMILIARES</h4>   

<table width="90%" cellpadding="5" cellspacing="0" border="1" rules="groups" align="center" style="font-size:10px;">
    <tr>
    	<td>NOMBRE DE LA MADRE:</td> 
        <td><b><?php if(isset($acudiente2['uss_nombre'])){ echo strtoupper($acudiente2['uss_nombre']);}?></b></td>
        <td>NOMBRE DEL PADRE:</td> 
        <td><b><?php if(isset($acudiente1['uss_nombre'])){ echo strtoupper($acudiente1['uss_nombre']);}?></b></td>
    </tr>
</table>

<h4 align="center">DATOS DEL ACUDIENTE</h4>   

<table width="90%" cellpadding="5" cellspacing="0" border="1" rules="groups" align="center" style="font-size:10px;">
    <tr>
    	<td>NOMBRES Y APELLIDOS:</td> 
        <td><b><?php if(isset($acudiente1['uss_nombre'])){ echo strtoupper($acudiente1['uss_nombre']." ".$acudiente1['uss_nombre2']." ".$acudiente1['uss_apellido1']." ".$acudiente1['uss_apellido2']);}?></b></td>
        <td>DNI:</td> 
        <td><b><?php if(isset($acudiente1['uss_usuario'])){ echo strtoupper($acudiente1['uss_usuario']);}?></b></td>
        <td>EDAD: </td> 
        <td>ESTRATO</td> 
        <td>&nbsp;</td>
    </tr>
    
    <tr>
    	<td>PARENTESCO:</td> 
        <td colspan="2">&nbsp;</td>
        <td>PROFESIÓN:</td> 
        <td><b><?php if(isset($acudiente1['uss_ocupacion'])){ echo strtoupper($acudiente1['uss_ocupacion']);}?></b></td>
        <td>CELULAR: <b><?php if(isset($acudiente1['uss_celular'])){ echo strtoupper($acudiente1['uss_celular']);}?></b></td> 
        <td>TELÉFONO: <b><?php if(isset($acudiente1['uss_telefono'])){ echo strtoupper($acudiente1['uss_telefono']);}?></b></td> 
    </tr>
</table>

<h6 align="center">ACUDIENTE 2</h6>   

<table width="90%" cellpadding="5" cellspacing="0" border="1" rules="groups" align="center" style="font-size:10px;">
    <tr>
    	<td>NOMBRES Y APELLIDOS:</td> 
        <td><b><?php if(isset($acudiente2['uss_nombre'])){ echo strtoupper($acudiente2['uss_nombre']);}?></b></td>
        <td>DNI:</td> 
        <td><b><?php if(isset($acudiente2['uss_usuario'])){ echo strtoupper($acudiente2['uss_usuario']);}?></b></td>
        <td>EDAD: </td> 
        <td>ESTRATO</td> 
        <td>&nbsp;</td>
    </tr>
    
    <tr>
    	<td>PARENTESCO:</td> 
        <td colspan="2">&nbsp;</td>
        <td>PROFESIÓN:</td> 
        <td><b><?php if(isset($acudiente2['uss_ocupacion'])){ echo strtoupper($acudiente2['uss_ocupacion']);}?></b></td>
        <td>CELULAR: <b><?php if(isset($acudiente2['uss_celular'])){ echo strtoupper($acudiente2['uss_celular']);}?></b></td> 
        <td>TELÉFONO: <b><?php if(isset($acudiente2['uss_telefono'])){ echo strtoupper($acudiente2['uss_telefono']);}?></b></td> 
    </tr>
    
    <tr>
    	<td>DIRECCIÓN:</td> 
        <td colspan="2"><b><?php if(isset($acudiente2['uss_direccion'])){ echo strtoupper($acudiente2['uss_direccion']);}?></b></td>
        <td>BARRIO:</td> 
        <td colspan="3">&nbsp;</td> 
    </tr>
    
    <tr>
    	<td>CORREO ELECTRÓNICO:</td> 
        <td colspan="2"><b><?php if(isset($acudiente2['uss_email'])){ echo strtoupper($acudiente2['uss_email']);}?></b></td>
        <td>CORREO ELECTRÓNICO:</td> 
        <td colspan="3">&nbsp;</td> 
    </tr>
</table>

<h4 align="center">DATOS ESCOLARES</h4>   

<table width="90%" cellpadding="5" cellspacing="0" border="1" rules="groups" align="center" style="font-size:10px;">
    <tr>
    	<td>INSTITUCIÓN DE PROCEDENCIA:</td> 
        <td colspan="2"><b><?php if(isset($resultado['mat_institucion_procedencia'])){ echo strtoupper($resultado['mat_institucion_procedencia']);}?></b></td>
    </tr>
    
    <tr style="font-weight:bold;">
    	<td>GRADO</td> 
        <td>AÑO</td>
        <td>INSTITUCIÓN</td> 
    </tr>
</table>

<h6 align="center">C O M P R O M I S O S &nbsp; F A M I L I A R E S</h6>
<p align="center" style="font-size:10px;">Nos comprometemos a cumplir con lo estipulado en el Proyecto Educativo Institucional y el Manual de Convivencia de la Institución.</p>

<table width="90%" cellpadding="5" cellspacing="0" border="0" rules="groups" align="center" style="font-size:10px; margin-top:10px;">
    <tr align="center">
    	<td>__________________________________<br>FIRMA DEL ESTUDIANTE</td> 
        <td>__________________________________<br>FIRMA DEL PADRE O ACUDIENTE</td>
        <td>__________________________________<br>FIRMA DEL PADRE O ACUDIENTE</td>
    </tr>  
</table>

<table width="90%" cellpadding="5" cellspacing="0" border="0" rules="groups" align="center" style="font-size:10px; margin-top:10px;">
    <tr align="center">
    	<td>__________________________________<br>RECTOR(A)</td> 
        <td>__________________________________<br>SECRETARIO(A)</td>
    </tr>  
</table>
 
  
</body>

</html>
