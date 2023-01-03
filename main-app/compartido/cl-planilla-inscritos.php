<?php
//include("../modell/conexion.php");
include("informacion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM cl_estudiantes_cursos WHERE estcur_id_curso='".$_GET["cursoId"]."' AND estcur_id_estado=2");
?>
<head>
	<title>PLANILLA DE ASISTENCIA - WolfSyetem</title>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial;">
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr height="40">
        <td align="center" colspan="12" style="font-size:16px;">
            <?=$infoI[1];?><br />
            ESTUDIANTES INSCRITOS EN CURSOS LIBRES<br>
        </td>
	</tr>
    <tr height="30" style="font-size:11px;">
        <td colspan="6" align="left">
            <b>CURSO:</b> <?php echo strtoupper($_GET["cursoNombre"]);?> <br />      
        </td>
        <td colspan="6" align="left">     
            <b>FECHA:</b> <?php echo date("d/M/Y");?>
        </td>
    </tr>   
</table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <tr style="font-weight:bold; font-size:10px" align="center">
        <td>No</th>
        <td>C&oacute;digo</th>
        <td>Nombre</th>
        <td>No. Documento</th>  
    </tr>
<?php
$con = 1;
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	$consultaAdicional = mysqli_query($conexion, "SELECT * FROM cl_estudiantes WHERE est_usuario='".$resultado[1]."'");
	$resultadoAdicional = mysqli_fetch_array($consultaAdicional, MYSQLI_BOTH);
?>
    <tr style="font-size:10px;">
        <td align="center"><?=$con;?></td>
        <td align="center"><?=$resultadoAdicional[1];?></td>
        <td><?=strtoupper($resultadoAdicional[4]);?></td>
        <td align="center"><?=$resultadoAdicional[3];?></td>
    </tr>
<?php
	$con++;
}
?>
</table>
<p style="font-size:6px;">
    <img src="../files-sgpa/images/wolflsystem.png" height="50" align="absmiddle">
    SISTEMA GESTOR DE PROCESOS ACADEMICOS - SGPA
</p>
</body>
</html>
