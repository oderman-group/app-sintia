<?php
//include("../modell/conexion.php");
include("informacion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM grupos, programas, asignaturas WHERE asignaturas.id=grupos.id_asignatura AND programas.id=grupos.id_programa");
?>
<head>
	<title>REPORTES - WolfSyetem</title>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial;">
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr height="40">
        <td align="center" colspan="12" style="font-size:16px;">
            <img src="../files-sgpa/images/<?=$infoI[5];?>" width="<?=$infoI[6];?>" height="<?=$infoI[7];?>"><br>
			<?=$infoI[1];?><br />
            LISTADO DE GRUPOS<br>
        </td>
	</tr>   
</table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <tr style="font-weight:bold; font-size:8px" align="center">
        <td>No</th>
        <th>Cod</th>
        <th>Programa</th>
        <th>Asignatura</th>
        <th>Estudiantes</th>
    </tr>
<?php
$con = 1;
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	$consultaAdicional = mysqli_query($conexion, "SELECT * FROM matriculas WHERE id_grupo='".$resultado[0]."'");
	$numAdicional = mysqli_num_rows($consultaAdicional);
?>
    <tr style="font-size:10px;">
        <td align="center"><?=$con;?></td>
        <td align="center"><?=$resultado[0];?></td>
		<td><?=$resultado[4];?></td>
		<td><?=$resultado[12];?></td>
        <td align="center"><?=$numAdicional;?></td>
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
