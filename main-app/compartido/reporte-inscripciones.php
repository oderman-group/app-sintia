<?php
//include("../modell/conexion.php");
include("informacion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM inscripciones");
?>
<head>
	<title>REPORTES - WolfSystem</title>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial;">
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr height="40">
        <td align="center" colspan="12" style="font-size:16px;">
            <img src="../files-sgpa/images/<?=$infoI[5];?>" width="<?=$infoI[6];?>" height="<?=$infoI[7];?>"><br>
			<?=$infoI[1];?><br />
            LISTADO DE INSCRIPCIONES<br>
        </td>
	</tr>   
</table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <tr style="font-weight:bold; font-size:10px" align="center">
        <td>No</th>
        <th>Fecha Inscripci&oacute;n</th>
                                <th>Estudiante</th>
								<th>Programa</th>
         
    </tr>
<?php
$con = 1;
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
$consultaA1 = mysqli_query($conexion, "SELECT * FROM programas WHERE programas.id='".$resultado[5]."'");
									$resultadoA1 = mysqli_fetch_array($consultaA1, MYSQLI_BOTH);
									$consultaA2 = mysqli_query($conexion, "SELECT * FROM estados_inscripciones, estados_programa, estados_estudiante WHERE estados_inscripciones.id='".$resultado[6]."' AND estados_programa.id='".$resultado[7]."' AND estados_estudiante.id='".$resultado[8]."'");
									$resultadoA2 = mysqli_fetch_array($consultaA2, MYSQLI_BOTH);
?>
    <tr style="font-size:8px;">
        <td align="center"><?=$con;?></td>
        <td><?=$resultado[1];?><br><span class="label label-<?=$estadoI;?>"><?=$resultadoA2[1];?></span></td>
                                <td><?=$resultado[2]."<br>".$resultado[10]."<br>".$resultado[12]." ".$resultado[13]." ".$resultado[14]."<br>".$resultado[21]."<br>".$resultado[22];?><br><span class="label label-success"><?=$resultadoA2[7];?></span></td>
								<td><?=$resultadoA1[1];?><br>Semestre: <?=$resultado[25];?><br><span class="label label-info"><?=$resultadoA2[4];?></span></td>
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