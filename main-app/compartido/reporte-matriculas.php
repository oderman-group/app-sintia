<?php
//include("../modell/conexion.php");
include("informacion.php");
$consulta = mysqli_query($conexion, "SELECT * FROM matriculas, inscripciones WHERE inscripciones.id=matriculas.id_estudiante");
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
            LISTADO DE MATRICULAS<br>
        </td>
	</tr>   
</table>
<p>&nbsp;</p>  
<table bgcolor="#FFFFFF" width="100%" cellspacing="0" cellpadding="0" rules="all" border="1" align="center">
    <tr style="font-weight:bold; font-size:10px" align="center">
        <td>No</th>
                                <th>Estudiante</th>
                                <th>Matr&iacute;cula</th>
                                <th>Definitiva</th>
                                <th>Estado</th>
    </tr>
<?php
$con = 1;
while($resultado = mysqli_fetch_array($consulta, MYSQLI_BOTH)){
$consultaA1 = mysqli_query($conexion, "SELECT * FROM programas WHERE programas.id='".$resultado[11]."'");
									$resultadoA1 = mysqli_fetch_array($consultaA1, MYSQLI_BOTH);
									//===========================
									$consultaAdicional = mysqli_query($conexion, "SELECT * FROM grupos, asignaturas, programas WHERE grupos.id='".$resultado[3]."' AND programas.id=grupos.id_programa AND asignaturas.id=grupos.id_asignatura");
									$resultadoAdicional = mysqli_fetch_array($consultaAdicional, MYSQLI_BOTH);
									//============================================================
									$consultaAdicional2 = mysqli_query($conexion, "SELECT * FROM carga_academica, usuarios WHERE carga_academica.id_grupo='".$resultadoAdicional[0]."' AND usuarios.id=carga_academica.id_docente");
									$resultadoAdicional2 = mysqli_fetch_array($consultaAdicional2, MYSQLI_BOTH);
									//============================================================
									$consultaAdicional3 = mysqli_query($conexion, "SELECT * FROM estados_matriculas WHERE id='".$resultado[4]."'");
									$resultadoAdicional3 = mysqli_fetch_array($consultaAdicional3, MYSQLI_BOTH);
?>
    <tr style="font-size:8px;">
        <td align="center"><?=$con;?></td>
                                <td><?=$resultado[8]."<br>".$resultado[18]." ".$resultado[19]." ".$resultado[20]."<br>".$resultado[28]."<br>".$resultado[32]."<br><b>".$resultadoA1[1]."</b>";?></td>
                                <td><?="<b>Fecha:</b> ".$resultado[1]."<br><b>Grupo:</b> ".$resultado[3]."<br><b>Curso:</b> ".$resultadoAdicional[4]."<br><b>Programa:</b> ".$resultadoAdicional[9]."<br><b>Docente:</b> ".$resultadoAdicional2[11]." ".$resultadoAdicional2[12];?></td>
                                <td align="center"><u><?=$resultado[5];?></u></td>
                                <td align="center"><u><?=$resultadoAdicional3[1];?></u></td>
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
