<?php
if(!isset($_REQUEST["ref"]) or $_REQUEST["ref"]=="" or $_SERVER['HTTP_REFERER']==""){
    echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=220";</script>';
	exit();	
}

include("session-compartida.php");
$idPaginaInterna = 'DT0249';

if($datosUsuarioActual['uss_tipo'] == TIPO_DIRECTIVO && !Modulos::validarSubRol([$idPaginaInterna])){
	echo '<script type="text/javascript">window.location.href="../directivo/page-info.php?idmsg=301";</script>';
	exit();
}
include(ROOT_PATH."/main-app/compartido/historial-acciones-guardar.php");
require_once("../class/Estudiantes.php");
include("head.php");
?>
  </head>
  <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, Helvetica, sans-serif;">
<?php
    $year=$_SESSION["bd"];
    if(!empty($_POST["year"])){
        $year=$_POST["year"];
    }

    $ref="";
    if(!empty($_GET["ref"])){
        $ref=base64_decode($_GET["ref"]);
    }
    if(!empty($_POST["ref"])){
        $ref=$_POST["ref"];
    }
    $resultado = Estudiantes::obtenerDatosEstudiante($ref, $year);
    $acudiente1 = UsuariosPadre::sesionUsuario($resultado['mat_acudiente']);
    $acudiente2 = !empty($resultado['mat_acudiente2']) ? UsuariosPadre::sesionUsuario($resultado['mat_acudiente2']) : array();
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
        <td><b><?=$resultado['mat_fecha'];?></b></td>
    </tr> 
</table> 

<h4 align="center">DATOS PERSONALES</h4>   

<table width="90%" cellpadding="5" cellspacing="0" border="1" rules="groups" align="center" style="font-size:10px;">
    <tr>
    	<td>NOMBRE:</td> 
        <td><b><?=strtoupper($resultado['mat_nombres']." ".$resultado['mat_nombre2']);?></b></td>
        <td>APELLIDOS:</td> 
        <td><b><?=strtoupper($resultado['mat_primer_apellido']." ".$resultado['mat_segundo_apellido']);?></b></td>
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
        <td><b><?=$resultado['mat_documento'];?></b></td>
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
<?php
include(ROOT_PATH."/main-app/compartido/guardar-historial-acciones.php");
?>
</body>

</html>
