<?php
//include("../modell/conexion.php");
include("informacion.php");
$consulta = mysql_query("SELECT * FROM facultades",$conexion);
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
            LISTADO DE FACULTADES<br>
        </td>
	</tr>   
</table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <tr style="font-weight:bold; font-size:10px" align="center">
        <td>No</th>
        <td>C&oacute;digo</th>
        <td>Nombre</th>
        <td>Disponible</th> 
    </tr>
<?php
$con = 1;
while($resultado = mysql_fetch_array($consulta)){
?>
    <tr style="font-size:8px;">
        <td align="center"><?=$con;?></td>
        <td align="center"><?=$resultado[0];?></td>
        <td><?=$resultado[1];?></td>
        <td align="center"><?=$resultado[4];?></td>
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