<?php
//include("../modell/conexion.php");
include("informacion.php");
$consulta =mysqli_query($conexion, "SELECT * FROM carga_academica, usuarios, aulas, grupos WHERE usuarios.id=carga_academica.id_docente AND aulas.id=carga_academica.id_aula AND grupos.id=carga_academica.id_grupo");
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
            LISTADO DE CARGAS ACAD&Eacute;MICAS<br>
        </td>
	</tr>   
</table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <tr style="font-weight:bold; font-size:10px" align="center">
        <td>No</th>
        <th>Cod</th>
        <th>Docente</th>
        <th>Grupo</th>
        <th>Programa</th>
        <th>Asignatura</th>
        <th>Aula</th>
        <th>Activa</th>
        <th>Terminada</th>
        <th>#Est</th>
    </tr>
<?php
$con = 1;
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
	$consultaAdicional = mysqli_query($conexion, "SELECT * FROM asignaturas, programas WHERE programas.id='".$resultado[24]."' AND asignaturas.id='".$resultado[23]."'");
	$resultadoAdicional = mysqli_fetch_array($consultaAdicional, MYSQLI_BOTH);
	//==========================================================
	$consultaAdicional2 = mysqli_query($conexion, "SELECT * FROM matriculas WHERE id_grupo='".$resultado[2]."'");
	$numAdicional2 = mysqli_num_rows($consultaAdicional2);
?>
    <tr style="font-size:8px;">
        <td align="center"><?=$con;?></td>
        <td align="center"><?=$resultado[0];?></td>
		<td><?=$resultado[11]." ".$resultado[12];?></td>
		<td align="center"><?=$resultado[2];?></td>
        <td><?=$resultadoAdicional[6];?></td>
        <td><?=$resultadoAdicional[1];?></td>
        <td><?=$resultado[19];?></td>
        <td align="center"><?=$resultado[4];?></td>
        <td align="center"><?=$resultado[5];?></td>
        <td align="center"><?=$numAdicional2;?></td>
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

